<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == "POST"){
  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ,['id', 'param', 'status']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  if(!is_numeric($_REQ["id"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'id');

  if(!in_array($_REQ["param"], ["status", "update"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'param');


  $Sub = $Core->submissions->find("id", $_REQ["id"]);
  if(!isset($Sub))
    ThrowAPIError(404);

  if($_REQ["param"] == "status")
  {
    if(!in_array($_REQ["status"], [0,1,2,3,4,5]))
      ThrowAPIError(ERROR_API_INVALIDPARAM, 'status');

    if(
      ($Core->User->hasPermission(ADMINFLAG_SUBMISSIONS_MODERATOR) && in_array($_REQ["status"], [0,1,5])) ||
      ($Core->User->hasPermission(ADMINFLAG_SUBMISSIONS) && in_array($_REQ["status"], [0,1,2,3,4,5]))
    ) {
        $Sub->setStatus($_REQ["status"]);
    } else ThrowAPIError(403);

  }else if($_REQ["param"] == "update")
  {
    if(!$Core->User->hasPermission(ADMINFLAG_SUBMISSIONS) && !in_array($Core->User->steamid, $Sub->authors))
      ThrowAPIError(403);

    if(!in_array($_REQ["status"], [0,1]))
      ThrowAPIError(ERROR_API_INVALIDPARAM, 'status');
    $Sub->setUpdateState($_REQ["status"]);
  }

  ThrowResult();
}
?>
