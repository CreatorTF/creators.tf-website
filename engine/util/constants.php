<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("AMPER_EPOCH", 1546290000000);

/**
* Purpose: Admin Bits
*/
define("ADMINFLAG_ROOT", (1<<0));
define("ADMINFLAG_SUBMISSIONS", (1<<1));
define("ADMINFLAG_SUBMISSIONS_MODERATOR", (1<<2));
define("ADMINFLAG_POSTS", (1<<3));
define("ADMINFLAG_COMMENTS", (1<<3));

/**
* Purpose: Special Permissions for API requests
*/
define('APISPECIAL_SERVER_KEY', (1<<0));
define('APISPECIAL_SERVER_KEY_WRITE', (1<<1));
define('APISPECIAL_ADMIN_PROMO_DISTRIBUTE', (1<<2));

/**
* Purpose: Error Codes
*/
define("ERROR_NULL", 0);
define("ERROR_NOT_FOUND", 404);
define("ERROR_NO_PERMISSION", 403);
define("ERROR_TEAPOT", 418);
define("ERROR_LOGIN_INVALID", 1);
define("ERROR_UNEXPECTED", 2);
define("ERROR_REGISTER_DUPLICATE", 3);
define("ERROR_API_INVALIDKEY", 4);
define("ERROR_API_INVALIDPARAM", 5);
define("ERROR_BAN_OVERALL", 10);
define("ERROR_BAN_WEBSITE", 11);
define("ERROR_BAN_BLOG", 12);
define("ERROR_CSRF", 13);
define("ERROR_SERVER_UNREACHABLE", 25);
define("ERROR_PRIVATE_PROFILE", 26);
define("ERROR_PRIVATE_INVENTORY", 27);

/**
* Purpose: User Special
*/
define("USERSPECIAL_VERIFIED", 1);
define("USERSPECIAL_BLOG", 2);
define("USERSPECIAL_BOT", 3);

/**
* Purpose: Backpack sort types.
*/
define("BPSORT_QUALITY", 0);
define("BPSORT_TYPE", 1);
define("BPSORT_CLASS", 2);
define("BPSORT_SLOT", 0);
define("BPSORT_DATE", 0);

/**
* Purpose: Default Strange Parts.
*/
define("SP_WEAPON", 1);
define("SP_COSMETIC", 2);

/**
* FUNCTIONS
*/

function clamp($num, $min, $max)
{
    return min(max($num, $min), $max);
}

function enable_errors()
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

function md5_dir($dir)
{
    if (!is_dir($dir))
        return false;

    $filemd5s = [];
    $d = dir($dir);

    while (false !== ($entry = $d->read()))
    {
        if ($entry != '.' && $entry != '..')
        {
            if (is_dir($dir.'/'.$entry)) $filemd5s[] = MD5_DIR($dir.'/'.$entry);
            else $filemd5s[] = md5_file($dir.'/'.$entry);
        }
    }
    $d->close();
    return md5(implode('', $filemd5s));
}

function ref_array($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0)
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}

function params_build_url($url, $base)
{
    return strtok($url, '?')."?".join("&", array_map(function($a, $b){
        if(is_array($a))
        {
            $c = [];
            foreach ($a as $d) {
                array_push($c, $b."[]=".$d);
            }
            return join("&", $c);
        } else return $b."=".$a;
    }, $base, array_keys($base)));
}

function get_contents_url($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}


/**
* Purpose: Sends a Discord Webhook request to a specific URL with message.
*/
function discord_webhook_send($url, $message)
{
    $json_data = array ('content'=>$message);
    $make_json = json_encode($json_data);
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec( $ch );
}

/**
* Purpose: Sends GET request to a url and returns the content.
*/
function recaptcha_check_token($key, $token)
{
  global $_REQ;
  $a = get_contents_url("https://www.google.com/recaptcha/api/siteverify?secret=".urlencode($key)."&response=".urlencode($token));
  $a = (object) json_decode($a);
  if($a->success == true)
  {
    return $a;
  } else {
    return (object) ["success" => true, "score" => 0.0];
  }
}

/**
* Purpose: Escapes all major chars that may break some requests.
*/
function escape($str, $quotes = true)
{
    $str = str_replace("&","&amp;", $str);
    $str = str_replace("%","&#37;", $str);
    return $str;
}

/**
* Purpose: Returns Class name without namespace.
*/
function get_class_name($object)
{
    $classname = get_class($object);
    if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
    return $pos;
}

/**
* Purpose: Formats the string with the given parameters.
*/
function format($string, $args, $letter = 's'){
    foreach($args as $k=>$v){
        $string = preg_replace('/%'.$letter.'/', $v, $string, 1);
    }
    return $string;
}

function check($arr, $keys, $small = false)
{
    if($small)
    {
        $arr = array_change_key_case($arr, CASE_LOWER);
    }
    foreach($keys as $key)
    {
        if(!isset($arr[$key])) return $key;
        if($arr[$key] == "") return $key;
    }
    return NULL;
}

function array_ksearch($array,$key,$value)
{
    foreach ($array as $k => $v) {
        $v = (array) $v;
        if(($v[$key] ?? NULL) == $value) return $k;
    }
    return null;
}

function array_remove($array,$value)
{
    if (($key = array_search($value, $array)) !== false) {
        array_splice($array,(int)$key,1);
    }
    return $array;
}

function str_lreplace($search, $replace, $subject)
{
    return preg_replace('~(.*)' . preg_quote($search, '~') . '~', '$1' . $replace, $subject, 1);
}

function array_merge_recursive_ex(array $array1, array $array2)
{
    $merged = $array1;

    foreach ($array2 as $key => & $value) {
        if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
            $merged[$key] = array_merge_recursive_ex($merged[$key], $value);
        } else if (is_numeric($key)) {
             if (!in_array($value, $merged)) {
                $merged[] = $value;
             }
        } else {
            $merged[$key] = $value;
        }
    }

    return $merged;
}

function apply_cookie($token, $domain, $expires = 60 * 60 * 24 * 31, $secure = false)
{
    header("Set-Cookie: session_id=$token; Expires=".date(DATE_RFC822, time() + $expires)."; ".($secure ? "Secure; " : "")."HttpOnly; SameSite=lax; Domain=$domain; Path=/");
}
?>
