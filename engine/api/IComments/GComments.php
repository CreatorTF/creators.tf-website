<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
  $param = check($_REQ, ["target", "id", "count"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!in_array($_REQ["target"], ["submission", "user","post"]))
    ThrowAPIError(5);

  $_COUNT = (+$_REQ["count"]);
  if($_COUNT == 0)
    ThrowAPIError(5, "count");

  $_OFFSET = (+($_REQ["offset"] ?? 0)) * $_COUNT;

  if($_REQ["target"] == "submission")
    $Target = "[S:".$_REQ["id"]."]";
  if($_REQ["target"] == "user")
    $Target = "[U:".$_REQ["id"]."]";
  if($_REQ["target"] == "post")
    $Target = "[P:".$_REQ["id"]."]";

  $_TOTAL_COUNT = $Core->dbh->getRow("SELECT count(*) as count FROM tf_comments WHERE targetid = %s", [$Target])["count"];

  $_COMMENTS = $Core->dbh->getAllRows("SELECT * FROM tf_comments WHERE targetid = %s ORDER BY created DESC LIMIT %d OFFSET %d", [$Target, $_COUNT, $_OFFSET]);
  $_COMMENTS = array_map(function($a){global $Core; return new Comment($a, $Core); }, $_COMMENTS);

  $_RESULT = [
    "total_count" => $_TOTAL_COUNT,
    "per_page" => $_COUNT
  ];

  if(isset($_REQ["ajax"]))
  {
    $_RESULT["outer_html"] = join(array_map(function($a){
      global $Core;
      return $a->toDOM([],[
        "CAN_FLAG" => isset($Core->User) && $a->author != $Core->User->id,
        "CAN_DELETE" => isset($Core->User) && ($a->author == $Core->User->id || $Core->User->hasPermission(ADMINFLAG_COMMENTS)),
        "VISIBLE" => !in_array($Core->User->id, $a->blacklist),
        "BLACKLISTED" => in_array($Core->User->id, $a->blacklist)
      ]);}, $_COMMENTS ), "");
  }

  ThrowResult($_RESULT);

} else if($method == 'POST')
{
  $param = check($_REQ, ["target", "id"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!isset($Core->User))
    ThrowAPIError(403);

  if(!in_array($_REQ["target"], ["submission", "user", "post"]))
    ThrowAPIError(5);

  if(ReCaptcha_IsBot())
    ThrowCustomAPIError(403, "Bot check failed. Try again later or contact us if you think that's an error.");

  if(mb_strlen($_REQ["content"]) <= 0)
    ThrowCustomAPIError(423, "Content of a comment should not be empty.");

  if(mb_strlen($_REQ["content"]) > $Core->config->social->comments_max_length)
    ThrowCustomAPIError(423, "The length of the comment should not exceed ".$Core->config->social->comments_max_length." characters.");

  $Target = null;
  if($_REQ["target"] == "submission")
    $Target = $Core->submissions->find("id", $_REQ["id"]);
  if($_REQ["target"] == "user")
    $Target = $Core->users->find("id", $_REQ["id"]);
  if($_REQ["target"] == "post")
    $Target = $Core->posts->find("id", $_REQ["id"]);

  if(!isset($Target))
    ThrowAPIError(404);

  $Target->comment($Core->User, $_REQ["content"]);
  ThrowResult();
} else if($method == 'DELETE')
{
  $param = check($_REQ, ["id"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!isset($Core->User))
    ThrowAPIError(403);

  $Comment = $Core->comments->find("id", $_REQ["id"]);
  if(!isset($Comment))
    ThrowAPIError(404);

  if($Core->User->id != $Comment->author && !$Core->User->hasPermission(ADMINFLAG_COMMENTS))
    ThrowAPIError(403);

  $Comment->delete();
  ThrowResult();
} else {
  http_response_code(404);
}
?>
