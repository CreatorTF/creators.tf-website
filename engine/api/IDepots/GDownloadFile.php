<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
  $param = check($_REQ, ["depid", "file"]);
  if(isset($param))
    ThrowAPIError(5, $param);

  if(!is_numeric($_REQ["depid"]))
    ThrowAPIError(5, "depid");

  $_READ = isset($Core->Server) &&
          ($Core->Key->special & APISPECIAL_SERVER_KEY);
  $_WRITE = isset($Core->Server) &&
          ($Core->Key->special & APISPECIAL_SERVER_KEY) &&
          ($Core->Key->special & APISPECIAL_SERVER_KEY_WRITE);

  $_POSSIBLE_ACCESS = ["NULL"];
  if($_READ) array_push($_POSSIBLE_ACCESS, "READ");
  if($_WRITE) array_push($_POSSIBLE_ACCESS, "WRITE");

  $Depot = $Core->depots->find("id", $_REQ["depid"]);
  if(!isset($Depot))
    ThrowAPIError(404);

  $Manifest = $Depot->getManifest();

  $_VERDICT = false;
  $Path = $Depot->getBaseDir()."/content/".$_REQ["file"];

  if(!file_exists($Path))
    die(http_response_code(404));

  foreach ($Manifest->Groups as $Group) {
    if(in_array($Group->Access ?? "NULL", $_POSSIBLE_ACCESS))
    {
      foreach ($Group->Files as $Pattern) {
        $P = str_replace('\\', '/', $Pattern);
        $S = str_replace('\\', '/', $_REQ["file"]);
        if(fnmatch($P, $S))
        {
          $_VERDICT = true;
          break;
        }
      }
    }
  }

  if($_VERDICT) {
    header("Cache-Control: public");
    header("Content-Type: ".mime_content_type($Path));
    header("Content-Length:".filesize($Path));
    header("Content-Disposition: attachment; filename=".basename($Path));
    readfile($Path);
    die();
  } else http_response_code(403);
}else{
    http_response_code(404);
}
?>
