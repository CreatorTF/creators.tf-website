<?php
define("INCLUDED", true);
define("ITEM_SOUND_DRAG_DROP_DEFAULT", "{CDN}/assets/sounds/ui/pickup/item_default_drop.wav");
define("ITEM_SOUND_DRAG_PICKUP_DEFAULT", "{CDN}/assets/sounds/ui/pickup/item_default_pickup.wav");

require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
  $param = check($_REQ,['limit', 'profile']);
  if(isset($param)) {
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
  }

  $Profile = $Core->users->findOR("steamid", $_REQ["profile"], "alias", $_REQ["profile"]);
  if(!isset($Profile))
    ThrowAPIError(ERROR_NOT_FOUND);

  // Backwards compatibility
  if(!$Profile->items_slotted)
  {
    $Profile->sortBackpack(BPSORT_TYPE);
    $Profile->setBackpackPages($Core->config->economy->default_pages);
  }

  $_CURSOR = $_GET["cursor"] ?? 0;
  $_CURSOR = (+$_CURSOR);

  $_LIMIT = (+$_GET["limit"]);

  if(!is_numeric($_CURSOR) || !is_numeric($_LIMIT))
    ThrowAPIError(ERROR_UNEXPECTED);

  $_ITEMS = $Core->dbh->getAllRows(
    "SELECT * FROM tf_pack WHERE steamid = %s AND id > %d LIMIT %d",
    [
      $Profile->steamid,
      $_CURSOR,
      $_LIMIT
    ]
  );

  $LAST_ID = end($_ITEMS)["id"];
  $LAST_ID = (+$LAST_ID);

  $Count = $Core->dbh->getRow(
    "SELECT count(id) as count FROM tf_pack WHERE steamid = %s AND id > %d",
    [
      $Profile->steamid,
      $LAST_ID
    ]
  )["count"];
  $Count = (+$Count);

  $Items = [];
  $Overflow = [];
  $MaxSlots = $Profile->getMaxBackpackSlots();
  foreach ($_ITEMS as $ITEM) {
    if($ITEM["slot"] < $MaxSlots)
    {
        if(isset($Core->Economy["Items"][$ITEM["defid"]]))
            array_push($Items, new Item($ITEM, $Core));
    }
    else
      array_push($Overflow, new Item($ITEM, $Core));
  }

  ThrowResult([
    "cursor" => ($Count > 0) ? $LAST_ID : NULL,
    "items" => array_map(function($i){
      global $Core;
      global $Profile;
      return [
        "id" => $i->id,
        "slot" => $i->slot,
        "name" => $i->name,
        "attributes" => $Core->items->parseAttributes($i->attributes, $i),
        "description" => $i->def->description ?? NULL,
        "image" => $i->image,
        "sounds" => [
            "drag_pickup" => $i->def->sounds["drag_pickup"] ?? ITEM_SOUND_DRAG_PICKUP_DEFAULT,
            "drag_drop" => $i->def->sounds["drag_drop"] ?? ITEM_SOUND_DRAG_DROP_DEFAULT
        ],
        "html" => $i->toDOM([],["CONTEXT" => ($Core->User->id ?? NULL) == $Profile->id && $Profile->getMaxBackpackSlots() > $i->slot])
      ];
    }, $Items),
    "overflow" => array_map(function($i){
      global $Core;
      global $Profile;
      return [
        "id" => $i->id,
        "slot" => $i->slot,
        "name" => $i->name,
        "attributes" => $Core->items->parseAttributes($i->attributes, $i),
        "description" => $i->def->description,
        "image" => $i->image,
        "html" => $i->toDOM([],["CONTEXT" => ($Core->User->id ?? NULL) == $Profile->id && $Profile->getMaxBackpackSlots() > $i->slot])
      ];
    }, $Overflow)
  ]);
}
?>
