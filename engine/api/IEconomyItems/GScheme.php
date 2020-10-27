<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
    if(isset($param)) ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

    require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/economy.php";
    if(isset($_GET["field"])) $_ECONOMY = $_ECONOMY[$_GET["field"]] ?? [];

    ThrowResult(["Economy" => $_ECONOMY], false);
}
?>
