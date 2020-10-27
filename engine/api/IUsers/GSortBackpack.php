<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST')
{
  if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

  $param = check($_REQ, ["type"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!in_array($_REQ["type"], [
    BPSORT_QUALITY,
    BPSORT_TYPE,
    BPSORT_CLASS,
    BPSORT_SLOT,
    BPSORT_DATE
  ]))
    ThrowAPIError(5, $param);

  $Core->User->sortBackpack($_REQ["type"]);
  ThrowResult();
}
?>
