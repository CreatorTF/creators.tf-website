<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET'){
  $param = check($_REQ,['provider']);
  if(isset($param))
      ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
  if(!is_numeric($_REQ["provider"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'provider');

  $S = $Core->dbh->getAllRows(
    " SELECT *, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(cache_ts)) as seconds FROM tf_servers
      WHERE is_cached = 1 AND owner = %d",
    [
      $_REQ["provider"]
    ]
  );

  if(count($S) == 0)
    ThrowAPIError(ERROR_NOT_FOUND);

  $Result = [];

  foreach ($S as $Server) {
    $a = explode(",",$Server["cache"]);
    $Data = [];
    foreach ($a as $b) {
      $k = explode("=", $b)[0];
      $v = explode("=", $b)[1];
      $Data[$k] = $v;
    }

    array_push($Result, [
      "id" => (+$Server["id"]),
      "is_down" => $Server["seconds"] > 100,
      "ip" => $Server["ip"],
      "port" => $Server["port"],
      "region" => $Server["region"],
      "map" => $Data["m"] ?? "",
      "online" => (+$Data["o"]),
      "maxplayers" => (+$Data["mp"]),
      "hostname" => addslashes($Data["h"]),
      "passworded" => ($Data["p"] ?? 0) == 1,
      "since_heartbeat" => (+$Server["seconds"])
    ]);
  }

  ThrowResult([
    "servers" => $Result
  ]);
}else{
    http_response_code(404);
}
?>
