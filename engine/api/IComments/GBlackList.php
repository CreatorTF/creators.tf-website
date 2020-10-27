<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST')
{
  $param = check($_REQ, ["id", "state"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!isset($Core->User))
    ThrowAPIError(403);

  $Comment = $Core->comments->find("id", $_REQ["id"]);
  if(!isset($Comment))
    ThrowAPIError(404);

  if($Comment->author == $Core->User->id)
    ThrowCustomAPIError(403, "You can't blacklist your own comment.");

  $Comment->blacklist($Core->User, $_REQ["state"] === "true");
  ThrowResult();
} else {
  http_response_code(404);
}
?>
