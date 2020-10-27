<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];
if($method == 'POST')
{
  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ,['item_id', 'slot_id']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM,$param);

  if(!is_numeric($_REQ["slot_id"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM,"slot_id");

  if(!is_numeric($_REQ["item_id"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM,"item_id");

  if($_REQ["slot_id"] >= $Core->User->getMaxBackpackSlots())
    ThrowAPIError(403);

  $Item = $Core->items->findAND("id", $_REQ["item_id"], "steamid", $Core->User->steamid);
  if(isset($Item))
  {
    $Item2 = $Core->items->findAND("steamid", $Core->User->steamid, 'slot', $_REQ["slot_id"]);
    if(isset($Item2))
      $Item2->setSlot($Item->slot);

    $Item->setSlot($_REQ["slot_id"]);

    ThrowResult();
  }
}
?>
