<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST')
{
  if(!(
    isset($Core->Server) &&
    isset($Core->Key) &&
    $Core->Key->special & APISPECIAL_SERVER_KEY
  )) ThrowAPIError(403);

  if(!isset($Core->User))
  {
    $Key = explode(" ", $_SERVER["HTTP_ACCESS"]);
    if(isset($Key[3]))
      $Core->User = $Core->users->create($Key[3]);
  }

  if(!isset($Core->User))
    ThrowAPIError(403);

  $param = check($_REQ, ["server"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!is_numeric($_REQ["server"]))
    ThrowAPIError(5, $param);

  $Core->User->bump($_REQ["server"]);

  ThrowResult();
}
?>
