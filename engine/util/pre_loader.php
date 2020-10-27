<?php
if(!defined("INCLUDED")) die("Access forbidden.");

function request_issecure()
{
	if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
		return true;
	}
	return false;
}

if (!function_exists('getallheaders')) {
	function getallheaders() {
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

session_name("sessid");
session_set_cookie_params(NULL, '/; SameSite=Lax', $_SERVER['HTTP_HOST'], request_issecure(), true);
session_start();

define("ENVIRONMENT_LOCAL", 0);
define("ENVIRONMENT_REMOTE", 1);
define("ENVIRONMENT_PRODUCTION", 2);
define("ENVIRONMENT_BETA", 3);

$ENVIRONMENT = ENVIRONMENT_LOCAL;

define("PATTERN_PRODUCTION", "creators.tf");
define("PATTERN_BETA", "beta.creators.tf");
define("PATTERN_REMOTE", "staging.creators.tf");

if(fnmatch(PATTERN_PRODUCTION, $_SERVER['HTTP_HOST']))
{
  $ENVIRONMENT = ENVIRONMENT_PRODUCTION;
}else if(fnmatch(PATTERN_BETA, $_SERVER['HTTP_HOST']))
{
  $ENVIRONMENT = ENVIRONMENT_BETA;
}else if(fnmatch(PATTERN_REMOTE, $_SERVER['HTTP_HOST']))
{
  $ENVIRONMENT = ENVIRONMENT_REMOTE;
}else $ENVIRONMENT = ENVIRONMENT_LOCAL;

$GLOBALS["ENVIRONMENT"] = $ENVIRONMENT;

$v = NULL;
if(file_exists('.git/HEAD'))
{
	if(file_exists(".git/".trim(substr(file_get_contents('.git/HEAD'), 4))))
	{
		$v = trim(substr(file_get_contents(".git/".trim(substr(file_get_contents('.git/HEAD'), 4))),0,6));
	}
}

if(!isset($v))
{
	// Generating random version index to prevent errors
	if(!isset($_SESSION["temp_v"]))
	{
		$_SESSION["temp_v"] = rand(100000,999999);
	}
	$v = $_SESSION["temp_v"];
}
define("VERSION", $v);

if(isset($_GET["log_errors"]) /*|| $ENVIRONMENT == ENVIRONMENT_LOCAL*/)
{
	define("DEBUG",1);
}else{
	define("DEBUG",0);
}

if(DEBUG == 1) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}else{
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
}

if(!isset($_SESSION["X-CSRF"]))
{
  $_SESSION["X-CSRF"] = bin2hex(random_bytes(32));
}

$CSP_Domains = [
	"https://*.kxcdn.com",
	"https://*.creators.tf",
	"https://*.bootstrapcdn.com",
	"https://*.syndication.twimg.com",
	"https://*.twitter.com",
	"https://*.yandex.ru",
	"https://*.googleapis.com",
	"https://*.jsdelivr.net",
	"https://*.cloudflare.com",
	"https://*.ytimg.com",
	"https://*.akamaihd.net",
	"https://*.youtube.com",
	"https://*.google.com",
	"https://*.gstatic.com",
	"https://*.cloudflareinsights.com",
	"https://*.facebook.net",
	"https://*.facebook.com"
];

$CSP_Rules = [
	"default-src 'self'",
	"script-src 'unsafe-inline' 'self'",
	"img-src 'self' * blob: data: https://*.patreonusercontent.com https://*.imgur.com",
	"style-src 'self' 'unsafe-inline' https://*.materialdesignicons.com",
	"font-src 'self' https://*.gstatic.com https://*.materialdesignicons.com",
	"connect-src *",
	"frame-src 'self' https://googleads.g.doubleclick.net/"
];

foreach ($CSP_Rules as $k => $v)
{
	$CSP_Rules[$k].=" ".join(" ", $CSP_Domains);
}

$CSP = "Content-Security-Policy: ".join("; ", $CSP_Rules);

header($CSP);
header("X-XSS-Protection: 1");
header("X-Frame-Options: deny");
header("X-Content-Type-Options: nosniff");
header("Strict-Transport-Security: max-age=15552000; preload");


$_HEADERS = getallheaders();
$_HEADERS = array_change_key_case($_HEADERS, CASE_UPPER);
?>
