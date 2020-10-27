<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == "POST") {
  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ,['id']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  $Sub = $Core->submissions->find("id", $_REQ["id"]);
  if(!isset($Sub))
    ThrowAPIError(404);

  if(!in_array($Core->User->steamid, $Sub->authors) && !$Core->User->hasPermission(ADMINFLAG_SUBMISSIONS))
    ThrowAPIError(403);

  if(isset($_REQ["access_link"]))
  {
    $Sub->setFilesLink($_REQ["access_link"]);
  }

  if(isset($_REQ["type"]))
  {
    if(!in_array($_REQ["type"], $Core->config->submissions->types))
      ThrowCustomAPIError(422, "Invalid type parameter");
    $Sub->setTags([["name" => "Type", "value" => [$_REQ["type"]]]], true);
  }

  if(isset($_REQ["Theme"]) || isset($_REQ["Class"]) || isset($_REQ["GameMode"]))
  {
    $tags = [];
    foreach ([
      "Theme" => explode(",",$_REQ["Theme"]),
      "Class" => explode(",",$_REQ["Class"]),
      "GameMode" => explode(",",$_REQ["GameMode"])
    ] as $k => $v) {
      if($k == "Theme" && $v[0] != "" && $Sub->getType() != NULL) array_push($tags, ["name" => "Theme", "value" => array_values(array_intersect($Core->config->submissions->themes, $v))]);
      if($k == "Class" && $v[0] != "" && in_array($Sub->getType(), ["Cosmetic", "Unusual Effect", "War Paint", "Taunt", "Weapon"])) array_push($tags, ["name" => "Class", "value" => array_values(array_intersect($Core->config->submissions->classes, $v))]);
      if($k == "GameMode" && $v[0] != "" && in_array($Sub->getType(), ["Map"])) array_push($tags, ["name" => "Game Mode", "value" => array_values(array_intersect($Core->config->submissions->gamemodes, $v))]);
    }
    $Sub->setTags($tags, false);
  }

  ThrowResult();
} else if ($method == "DELETE")
{
  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ,['id']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  $Sub = $Core->submissions->find("id", $_REQ["id"]);
  if(!isset($Sub))
    ThrowAPIError(404);

  if(!in_array($Core->User->steamid, $Sub->authors) && !$Core->User->hasPermission(ADMINFLAG_SUBMISSIONS))
    ThrowAPIError(403);

  $Sub->trash();

  ThrowResult();
}
?>
