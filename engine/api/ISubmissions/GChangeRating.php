<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == "POST"){
  CSRFCheck();

  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ,['id', 'rate']);
  if(isset($param)) {
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
  }

  if(!is_numeric($_REQ["id"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'id');

  if(!in_array($_REQ["rate"], ["true", "false"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'rate');

  $Sub = $Core->submissions->find("id", $_REQ["id"]);
  if(!isset($Sub))
    ThrowAPIError(404);

  if(!in_array($Sub->status, [1,2,3]))
    ThrowAPIError(404);
    
  $Sub->setRateState($Core->User->steamid, $_REQ["rate"] == "true");

  ThrowResult();
}
?>
