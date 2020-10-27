<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET'){
  $param = check($_REQ,['id']);
  if(isset($param))
      ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
  if(!is_numeric($_REQ["id"]))
    ThrowAPIError(ERROR_API_INVALIDPARAM, 'id');

  $S = $Core->db->getRow(format("SELECT *, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(cache_ts)) as seconds FROM `tf_servers` WHERE is_cached = 1 AND id = %s", [$_REQ["id"]]));
  if(!isset($S))
    ThrowAPIError(ERROR_NOT_FOUND);

  $a = explode(",",$S["cache"]);
  $Data = [];
  foreach ($a as $b) {
    $k = explode("=", $b)[0];
    $v = explode("=", $b)[1];
    $Data[$k] = $v;
  }

  ThrowResult([
    "server" => [
      "id" => (+$S["id"]),
      "is_down" => $S["seconds"] > 100,
      "ip" => $S["ip"],
      "port" => $S["port"],
      "region" => $S["region"],
      "map" => $Data["m"] ?? "",
      "online" => (+$Data["o"]),
      "maxplayers" => (+$Data["mp"]),
      "hostname" => addslashes($Data["h"]),
      "passworded" => ($Data["p"] ?? 0) == 1,
      "since_heartbeat" => (+$S["seconds"])
    ]
  ]);

}else{
    http_response_code(404);
}
?>
