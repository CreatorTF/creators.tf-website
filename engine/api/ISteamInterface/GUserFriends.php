<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
  if(!CheckPermission_CanRead())
    ThrowAPIError(403);

  try{
    $a = file_get_contents("https://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=".$Core->config->api->steam."&steamid=".$Core->User->steamid."&relationship=friend");
    if($a != null)
    {
      $a = json_decode($a, true);
      $a = $a["friendslist"]["friends"];
      $a = array_map(function($b){return $b["steamid"];}, $a);

      ThrowResult(["friends" => $a]);
    }else{
      ThrowCustomAPIError(403, "Steam Friends list of this profile is hidden.");
    }
  }catch(Exception $e){}
}
?>
