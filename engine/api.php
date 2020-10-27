<?php
if(!defined("INCLUDED")) die("Access forbidden.");
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/pre_loader.php";
header('Content-Type: text/plain');

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/constants.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/vdf.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/class/Core.class.php";
$Core = new AmperEngine();

if(isset($_COOKIE["session_id"])) $_SESSION["token"] = $_COOKIE["session_id"];
if(!isset($_COOKIE["session_id"])) $_SESSION["token"] = NULL;

if(!isset($_COOKIE["locale"]) || !file_exists(format($_SERVER['DOCUMENT_ROOT']."/translations/locale_%s.json",["en"]))) {
    setcookie("locale", "en", time()+60*60*24*120,'/');
    $_COOKIE["locale"]="en";
}
$Core->LanguageMap = json_decode(file_get_contents(format($_SERVER['DOCUMENT_ROOT']."/translations/locale_%s.json",array($_COOKIE["locale"]))),true);
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

function PrintLogs()
{
    global $Core;
    var_dump($Core->getCache()->get("logs"));
}

function ReCaptcha_IsBot()
{
    global $Core;
    $score = recaptcha_check_token($Core->config->api->recaptcha, $_SERVER["HTTP_X_RECAPTCHA_VALIDATION"])->score;
    return $score < 0.4;
}

function ThrowAPIError($err, $param = NULL)
{
    global $Core;
    $Error = $Core->errors->find('code', $err);
    http_response_code($Error->http);

    if(in_array("text/keyvalues", explode(",", $_SERVER["HTTP_ACCEPT"])) || ($_GET["__type"] ?? NULL) == "text/keyvalues")
    {
        die(render("api", [
            'content' => vdf_encode(["Response" => ['result'=>'ERROR', 'error'=>[
                'code' => $Error->code,
                'title' => $Error->title,
                'content' => $Error->content
            ]]], true),
            'param' => $param
        ]));
        }else{
        die(render("api", [
            'content' => json_encode(['result'=>'ERROR', 'error'=>[
                'code' => $Error->code,
                'title' => $Error->title,
                'content' => $Error->content
            ]]),
            'param' => $param
        ]));
    }
}

function ThrowResult($content = [], $json = true, $kv = null)
{
    if(!isset($json)) $kv = true;
    if(!isset($kv)) $kv = $json;
    if(!is_array($content)) throw new TypeError("API result needs to be an array.");

    ob_start();
    if(in_array("text/keyvalues", explode(",", $_SERVER["HTTP_ACCEPT"])) || ($_GET["__type"] ?? NULL) == "text/keyvalues")
    {
        if($kv === true)
            echo render('api',[
                "content" => vdf_encode(["Response" => array_merge(["result"=>"SUCCESS"], $content)], true)
            ]);
        else
            echo render('api',[
                "content" => vdf_encode($content, true)
            ]);
    }else{
        if($json === true)
            echo render('api',[
                "content" => json_encode(array_merge(["result"=>"SUCCESS"], $content))
            ]);
        else
            echo render('api',[
                "content" => json_encode($content)
            ]);
    }

    $size = ob_get_length();
    header("Content-Encoding: none");
    header("Content-Length: {$size}");
    header("Connection: close");

    ob_end_flush();
    ob_flush();
    flush();
}

function ThrowCustomAPIError($code, $err = NULL)
{
  if(is_nan($code)) throw new TypeError("HTTP code needs to be an integer.");
  http_response_code($code);

  if(in_array("text/keyvalues", explode(",", $_SERVER["HTTP_ACCEPT"])))
  {
    die(render("api", [
			'content' => vdf_encode(["Response" => ['result'=>'ERROR', 'error'=>[
				'code' => $code,
				'title' => $err ?? HTTPCodeMessage($code)
			]]], true)
		]));
  }else{
    die(render("api", [
			'content' => json_encode(['result'=>'ERROR', 'error'=>[
				'code' => $code,
				'title' => $err ?? HTTPCodeMessage($code)
			]])
		]));
  }
}

function HTTPCodeMessage($code)
{
    switch ($code) {
        case 100: return 'Continue'; break;
        case 101: return 'Switching Protocols'; break;
        case 200: return 'OK'; break;
        case 201: return 'Created'; break;
        case 202: return 'Accepted'; break;
        case 203: return 'Non-Authoritative Information'; break;
        case 204: return 'No Content'; break;
        case 205: return 'Reset Content'; break;
        case 206: return 'Partial Content'; break;
        case 300: return 'Multiple Choices'; break;
        case 301: return 'Moved Permanently'; break;
        case 302: return 'Moved Temporarily'; break;
        case 303: return 'See Other'; break;
        case 304: return 'Not Modified'; break;
        case 305: return 'Use Proxy'; break;
        case 400: return 'Bad Request'; break;
        case 401: return 'Unauthorized'; break;
        case 402: return 'Payment Required'; break;
        case 403: return 'Forbidden'; break;
        case 404: return 'Not Found'; break;
        case 405: return 'Method Not Allowed'; break;
        case 406: return 'Not Acceptable'; break;
        case 407: return 'Proxy Authentication Required'; break;
        case 408: return 'Request Time-out'; break;
        case 409: return 'Conflict'; break;
        case 410: return 'Gone'; break;
        case 411: return 'Length Required'; break;
        case 412: return 'Precondition Failed'; break;
        case 413: return 'Request Entity Too Large'; break;
        case 414: return 'Request-URI Too Large'; break;
        case 415: return 'Unsupported Media Type'; break;
        case 500: return 'Internal Server Error'; break;
        case 501: return 'Not Implemented'; break;
        case 502: return 'Bad Gateway'; break;
        case 503: return 'Service Unavailable'; break;
        case 504: return 'Gateway Time-out'; break;
        case 505: return 'HTTP Version not supported'; break;
        default: exit('Unknown http status code "' . htmlentities($code) . '"'); break;
    }
    return $text;
}

function CheckPermission_CanRead()
{
  global $Core;
  if(!isset($Core->User))
    return false;

  if(isset($Core->Server)) {
    if(!(
      isset($Core->Key) &&
      isset($Core->Server) &&
      $Core->Key->special & APISPECIAL_SERVER_KEY
    )) return false;
  }
  return true;
}

function CheckPermission_CanWrite()
{
  global $Core;
  if(!isset($Core->User))
    return false;

  if(isset($Core->Server)) {
    if(!(
      isset($Core->Key) &&
      isset($Core->Server) &&
      $Core->Key->special & APISPECIAL_SERVER_KEY &&
      $Core->Key->special & APISPECIAL_SERVER_KEY_WRITE
    )) return false;
  }
  return true;
}

function CSRFCheck()
{
  $headers = getallheaders();
  $headers = array_change_key_case($headers);
  if($_SESSION["X-CSRF"] != ($headers["x-csrf-validation"] ?? NULL))
  {
    ThrowAPIError(ERROR_CSRF);
    die();
  }
}

$CTYPE = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $_REQ = $_GET;
    break;
  case 'POST':
    if($CTYPE == 'application/json'){
      $_REQ = json_decode(file_get_contents("php://input"),true);
    }else if($CTYPE == 'text/keyvalues'){
      $_REQ = vdf_decode(file_get_contents("php://input"),true);
      $_REQ = $_REQ[array_keys($_REQ)[0]];
    }else{
      $_REQ = $_POST;
    }
    break;
  default:
    if($CTYPE == 'text/keyvalues')
    {
      $_REQ = vdf_decode(file_get_contents("php://input"),true);
      $_REQ = $_REQ[array_keys($_REQ)[0]];
    } else
      $_REQ = json_decode(file_get_contents("php://input"),true);
    break;
}

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/authorize.php";
?>
