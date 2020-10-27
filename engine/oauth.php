<?php
if(!defined("INCLUDED")) die("Access forbidden.");
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/pre_loader.php";

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/constants.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/class/Core.class.php";
$Core = new AmperEngine();

if(isset($_COOKIE["session_id"])) $_SESSION["token"] = $_COOKIE["session_id"];
if(!isset($_COOKIE["session_id"])) $_SESSION["token"] = NULL;

if(!isset($_COOKIE["locale"]) || !file_exists(format($_SERVER['DOCUMENT_ROOT']."/translations/locale_%s.json",array($_COOKIE["locale"])))) {
    setcookie("locale", "en", time()+60*60*24*120,'/');
    $_COOKIE["locale"]="en";
}
$Core->LanguageMap = json_decode(file_get_contents(format($_SERVER['DOCUMENT_ROOT']."/translations/locale_%s.json",["en"])),true);
$Core->Language = $_COOKIE["locale"];

require_once $_SERVER['DOCUMENT_ROOT']."/config.php";
if(file_exists( $_SERVER['DOCUMENT_ROOT']."/dev.config.php"))
{
  require_once $_SERVER['DOCUMENT_ROOT']."/dev.config.php";
}
$Core->config = json_decode(json_encode($Core->config),false);

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/db_framework.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/tp_framework.php";

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/economy.php";
$Core->Economy = $_ECONOMY;

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/authorize.php";
?>
