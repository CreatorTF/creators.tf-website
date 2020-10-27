<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
  $param = check($_REQ,['items', 'profile']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  $Profile = $Core->users->findOR("steamid", $_REQ["profile"], "alias", $_REQ["profile"]);
  if(!isset($Profile))
    ThrowAPIError(ERROR_NOT_FOUND);

  $IDS = explode(",", $_REQ["items"]);
  foreach ($IDS as $k => $v) {
    $IDS[$k] = (+$v);

    if(!is_numeric($IDS[$k]))
      ThrowAPIError(ERROR_UNEXPECTED);
  }

  $Clause = implode(',', array_fill(0, count($IDS), '%s'));
  $_ITEMS = $Core->dbh->getAllRows(
    "SELECT * FROM tf_pack WHERE steamid = %s AND id IN ($Clause)",
    array_merge([$Profile->steamid], $IDS)
  );

  $Items = [];
  foreach ($_ITEMS as $ITEM) {
    array_push($Items, new Item($ITEM, $Core));
  }

  ThrowResult([
    "items" => array_map(function($i){
      global $Profile;
      global $Core;
      return [
        "id" => $i->id,
        "image" => $i->image,
        "html" => $i->toDOM([],[
            "CONTEXT" => ($Core->User->id ?? NULL) == $Profile->id && $Profile->getMaxBackpackSlots() > $i->slot
        ])
      ];
    }, $Items)
  ]);
}else if($method == "DELETE")
{
  $param = check($_REQ,['items']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $IDS = array_values($_REQ["items"]);
  foreach ($IDS as $k => $v) {
    $IDS[$k] = (+$v);

    if(!is_numeric($IDS[$k]))
      ThrowAPIError(ERROR_UNEXPECTED);
  }
  $Clause = implode(',', array_fill(0, count($IDS), '%s'));

  $_ITEMS = $Core->dbh->getAllRows(
    "SELECT * FROM tf_pack WHERE steamid = %s AND id IN ($Clause)",
    array_merge([$Core->User->steamid], $IDS)
  );

  $Core->dbh->query(
    "DELETE FROM tf_pack WHERE steamid = %s AND id IN ($Clause)",
    array_merge([$Core->User->steamid], $IDS)
  );
  $Overflow = $Core->User->getOverflowItems();
  $OverflowResult = [];

  foreach ($_ITEMS as $k => $v) {
    if(!isset($Overflow[$k])) break;
    $v = new Item($v, $Core);
    $Overflow[$k]->setSlot($v->slot);
    array_push($OverflowResult, ["id" => $Overflow[$k]->id, "slot" => $v->slot]);
  }

  ThrowResult(["overflows" => $OverflowResult]);

  $Core->User->_Server_CleanInventoryCache();
}else if($method == "PATCH")
{
  $param = check($_REQ,['items']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $IDS = array_values($_REQ["items"]);
  foreach ($IDS as $k => $v) {
    $IDS[$k] = (+$v);

    if(!is_numeric($IDS[$k]))
      ThrowAPIError(ERROR_UNEXPECTED);
  }

  $ScrapCount = 0;
  $MaxSlots = $Core->User->getMaxBackpackSlots();

  $Clause = implode(',', array_fill(0, count($IDS), '%s'));
  $_ITEMS = $Core->dbh->getAllRows(
    "SELECT * FROM tf_pack WHERE steamid = %s AND id IN ($Clause)",
    array_merge([$Core->User->steamid], $IDS)
  );

  $Items = [];
  foreach ($_ITEMS as $ITEM) {
    $Item = new Item($ITEM, $Core);
    if(($Item->scrap ?? 0) == 0 || $Item->slot >= $MaxSlots)
      ThrowAPIError(ERROR_NO_PERMISSION);

    $ScrapCount += $Item->scrap;
  }

  $Core->User->chargeCurrency(-$ScrapCount);

  $Core->dbh->query(
    "DELETE FROM tf_pack WHERE steamid = %s AND id IN ($Clause)",
    array_merge([$Core->User->steamid], $IDS)
  );

  $Overflow = $Core->User->getOverflowItems();
  $OverflowResult = [];

  foreach ($_ITEMS as $k => $v) {
    if(!isset($Overflow[$k])) break;
    $v = new Item($v, $Core);
    $Overflow[$k]->setSlot($v->slot);
    array_push($OverflowResult, ["id" => $Overflow[$k]->id, "slot" => $v->slot]);
  }

  ThrowResult(['value' => $ScrapCount, "overflows" => $OverflowResult]);

  $Core->User->_Server_CleanInventoryCache();
}
?>
