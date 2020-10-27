<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/pre_loader.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/constants.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/class/Core.class.php";

$Core = new AmperEngine();

if(isset($_COOKIE["session_id"])) $_SESSION["token"] = $_COOKIE["session_id"];
if(!isset($_COOKIE["session_id"])) $_SESSION["token"] = NULL;

$_COOKIE["locale"] = "en";
$Core->LanguageMap = json_decode(file_get_contents(format($_SERVER['DOCUMENT_ROOT']."/translations/locale_%s.json",["en"])),true);
$Core->Language = $_COOKIE["locale"];

require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
if(file_exists( $_SERVER['DOCUMENT_ROOT']."/dev.config.php"))
{
    require_once $_SERVER['DOCUMENT_ROOT']."/dev.config.php";
}
$Core->config = json_decode(json_encode($Core->config), false);

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/economy.php";
$Core->Economy = $_ECONOMY;

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/db_framework.php";

$Core->config->renderOnlyContent = false;
$Core->content = NULL;
$Core->error = NULL;

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/authorize.php";

if(!file_exists(format($_SERVER['DOCUMENT_ROOT']."/templates/%s/main.tpl",array($Core->config->website->template)))){
    $Core->config->website->template = "default";
}
$template = $Core->config->website->template;

require_once $_SERVER['DOCUMENT_ROOT']."/engine/renderer.php";


mysqli_close($SQL);
?>
