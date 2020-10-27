<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == "POST"){
  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ,['id']);
  if(isset($param)) {
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
  }

  if(!is_numeric($_REQ["id"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'id');

  $Sub = $Core->submissions->find("workshop_id", $_REQ["id"]);
  if(isset($Sub))
  {
    if(!in_array($Core->User->steamid, $Sub->authors) && !$Core->User->hasPermission(ADMINFLAG_SUBMISSIONS))
      ThrowCustomAPIError(403, "You need to be listed as author of this submission to be able to import it.");
  }

  $Sub = new SteamWorkshopCrawler($_REQ["id"], $Core);
  if($Sub->error)
    ThrowAPIError(ERROR_NOT_FOUND);

  if($Sub->getGame()["id"] != 440)
    ThrowAPIError(ERROR_NOT_FOUND);

  if($Sub->getName() == ("" || NULL))
    ThrowAPIError(ERROR_NOT_FOUND);

  if($Sub->isCollection())
    ThrowAPIError(ERROR_NOT_FOUND);

  if(!in_array($Core->User->steamid, $Sub->getAuthorsSteamID()) && !$Core->User->hasPermission(ADMINFLAG_SUBMISSIONS))
    ThrowCustomAPIError(403, "You need to be listed as author of this submission to be able to import it.");

  $Entry = $Core->submissions->find("workshop_id", $_REQ["id"]);
  $iId;

  $iStatus = 0;
  $sSubTags = $Sub->getTags();

  $sTags = [];

  /*
  * Item Types are defined by submission tags.
  **/

  /*
  * = MAP =
  * Type "Map" => If submission has "Game Mode" tag.
  * We also apply "Game Mode" tag.
  **/
  if(($a = array_ksearch($sSubTags, "name", "Game Mode")) !== NULL)
  {
    array_push($sTags, ["name" => "Type", "value" => ["Map"]]);
    array_push($sTags, ["name" => "Game Mode", "value" => $sSubTags[$a]["value"]]);
  }
  /*
  * = COSMETIC =
  * Type "Cosmetic" => If submission has "Item Slot" tag that is set to either "Headgear" or "Misc".
  * We also apply "Class" tag.
  **/
  else if(
      ($a = array_ksearch($sSubTags, "name", "Item Slot")) !== NULL &&
      (
        in_array("Headgear", $sSubTags[$a]["value"]) ||
        in_array("Misc", $sSubTags[$a]["value"])
      )
    )
  {
    array_push($sTags, ["name" => "Type", "value" => ["Cosmetic"]]);
    if(($a = array_ksearch($sSubTags, "name", "Class")) !== NULL)
      array_push($sTags, ["name" => "Class", "value" => $sSubTags[$a]["value"]]);
  }
  /*
  * = TAUNT =
  * Type "Taunt" => If submission has "Item Slot" tag that is set to "Taunt".
  * We also apply "Class" tag.
  **/
  else if(
      ($a = array_ksearch($sSubTags, "name", "Item Slot")) !== NULL &&
      in_array("Taunt", $sSubTags[$a]["value"])
    )
  {
    array_push($sTags, ["name" => "Type", "value" => ["Taunt"]]);
    if(($a = array_ksearch($sSubTags, "name", "Class")) !== NULL)
      array_push($sTags, ["name" => "Class", "value" => $sSubTags[$a]["value"]]);
  }
  /*
  * = WEAPON =
  * Type "Weapon" => If submission has "Item Slot" tag that is set to "Weapon".
  * We also apply "Class" tag.
  **/
  else if(
      ($a = array_ksearch($sSubTags, "name", "Item Slot")) !== NULL &&
      in_array("Weapon", $sSubTags[$a]["value"])
    )
  {
    array_push($sTags, ["name" => "Type", "value" => ["Weapon"]]);
    if(($a = array_ksearch($sSubTags, "name", "Class")) !== NULL)
      array_push($sTags, ["name" => "Class", "value" => $sSubTags[$a]["value"]]);
  }
  /*
  * = WAR PAINT =
  * Type "War Paint" => If submission has "Other" tag that is set to "War Paint".
  * We also apply "Class" tag.
  **/
  else if(
      ($a = array_ksearch($sSubTags, "name", "Other")) !== NULL &&
      in_array("War Paint", $sSubTags[$a]["value"])
    )
  {
    array_push($sTags, ["name" => "Type", "value" => ["War Paint"]]);
    if(($a = array_ksearch($sSubTags, "name", "Class")) !== NULL)
      array_push($sTags, ["name" => "Class", "value" => $sSubTags[$a]["value"]]);
  }
  /*
  * = UNUSUAL EFFECT =
  * Type "Unusual Effect" => If submission has "Other" tag that is set to "Unusual Effect".
  * We also apply "Class" tag.
  **/
  else if(
      ($a = array_ksearch($sSubTags, "name", "Other")) !== NULL &&
      in_array("Unusual Effect", $sSubTags[$a]["value"])
    )
  {
      array_push($sTags, ["name" => "Type", "value" => ["Unusual Effect"]]);
    if(($a = array_ksearch($sSubTags, "name", "Class")) !== NULL)
      array_push($sTags, ["name" => "Class", "value" => $sSubTags[$a]["value"]]);
  }

  if(($a = array_ksearch($sSubTags, "name", "Other")) !== NULL) {
    $b = [];
    foreach ($Core->config->submissions->themes as $v) {
      if(in_array($v, $sSubTags[$a]["value"])) array_push($b, $v);
    }
    if(count($b) > 0) array_push($sTags, ["name" => "Theme", "value" => $b]);
  }

  if(!isset($Entry))
  {
    if(($a = array_ksearch($sSubTags, "name", "Other")) !== NULL && in_array("Certified Compatible", $sSubTags[$a]["value"])) {
      $iStatus = 2;
    }
    if(
      $Sub->getRating() >= $Core->config->submissions->min_stars &&
      $Sub->getNumRating() >= $Core->config->submissions->min_rating
    ) {
      $iStatus = 1;
    }
    $Core->submissions->import($Sub, $sTags, $iStatus, $Core->User->id);


    $iId = $Core->db->getRow("select id from tf_submissions where workshop_id = '".$Sub->id."'")["id"];

    if($iStatus == 0)
    {
      discord_webhook_send(
        $Core->config->webhooks->DISCORD_SUBMISSION_MODERATION,
        "New Submission: **".$Sub->getName()."**\n".$Sub->getImages()[0]."\n```".substr(strip_tags($Sub->getDesc()), 0, 500)."```https://".$_SERVER['HTTP_HOST']."/submission/".$iId
      );
    }
  } else {
    $Entry->reimport($Sub, $sTags);
    $iId = $Entry->id;
  }

  ThrowResult(["submission_id"=>$iId]);
}
?>
