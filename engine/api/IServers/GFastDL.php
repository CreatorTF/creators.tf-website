<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET'){

  if($_REQ["file"][0] == '/') $_REQ["file"] = substr($_REQ["file"], 1);

  $param = check($_REQ,['file', 'proxy']);
  if(isset($param))
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

  $Path = $_SERVER["DOCUMENT_ROOT"]."/cdn/fastdl/3emgpu6nUKWTa8jzJq8eV4zapRfrB8Fk/".$_REQ["file"];

  if(file_exists($Path))
    Header("Location: ".$Core->config->website->fastdl."/".$_REQ["file"]);
  else Header("Location: ".$_REQ["proxy"]."/".$_REQ["file"]);
}else{
    http_response_code(404);
}
?>
