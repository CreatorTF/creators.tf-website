<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
  $param = check($_REQ, ["depid"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!is_numeric($_REQ["depid"]))
    ThrowAPIError(5, "depid");

  $_READ = isset($Core->Server) &&
          ($Core->Key->special & APISPECIAL_SERVER_KEY);
  $_WRITE = isset($Core->Server) &&
          ($Core->Key->special & APISPECIAL_SERVER_KEY) &&
          ($Core->Key->special & APISPECIAL_SERVER_KEY_WRITE);

  $Tags = explode(",", strtolower($_REQ["tags"]));
  $Depot = $Core->depots->find("id", $_REQ["depid"]);

  if(!isset($Depot))
    ThrowAPIError(404);

  if(isset($_REQ["global_hash"]))
  {
    ThrowResult(["hash" => $Depot->getHash()]);
  }else{
    $Manifest = $Depot->getManifest();

    $_RETURN = [];

    foreach ($Manifest->Groups as $Group) {
      $Files = [];
      if(isset($Group->Contains))
        if(!in_array($Group->Contains, $Tags)) continue;

      if($Group->Access == "READ" && !$_READ) continue;
      if($Group->Access == "WRITE" && !$_WRITE) continue;

      foreach ($Group->Files as $Pattern) {
        $Files = array_merge($Files, array_map(function($a) {
          global $Depot;
          global $Group;

          return [
            explode($Depot->getBaseDir()."/content/".$Group->Directory->remote."/", $a) [1],
            hash_file('md5', $a)
          ];
        }, rglob($Depot->getBaseDir()."/content/".$Pattern)));
      }
      array_push($_RETURN, [
        "directory" => [
          "local" => $Group->Directory->local,
          "remote" => $Group->Directory->remote
        ],
        "files" => $Files
      ]);
    }
    ThrowResult(["groups" => $_RETURN, "hash" => $Depot->getHash()]);
  }
}else{
    http_response_code(404);
}
?>
