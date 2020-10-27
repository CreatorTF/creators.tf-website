<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/pre_loader.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/oauth.php";
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/oauth/steam/openid.php';

if($ENVIRONMENT == ENVIRONMENT_LOCAL)
{
  if(isset($_GET["token"]))
  {
    $Core->login($_GET["token"]);
    die();
  }
}

if(isset($_GET["action"]) && $_GET["action"] == "logout") {
  $Core->logout();
  header("Location: /");
} else {
  try {
    $openid = new LightOpenID($_SERVER['HTTP_HOST']);
    if(!$openid->mode)
    {
      if(!isset($_SESSION["session_id"]))
      {
        $openid->identity = 'http://steamcommunity.com/openid/?l=english';
        header('Location: ' . $openid->authUrl());
      } else {
        Header("Location: /");
      }
    } else if($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
      if($openid->validate())
      {
        $id = $openid->identity;
        $steamid = explode("/", $id)[5];

        $User = $Core->users->find('steamid',$steamid);
        $_TOKEN = null;
        if(!isset($User))
        {
          $User = $Core->users->create($steamid, 2);
        } else {
          $User->import();
        }
        $_TOKEN = $User->token;

        $Core->login($_TOKEN);
        if(isset($_GET["redirect"]))
        {
          header("Location: ".$_GET["redirect"]);
        }else{
          header("Location: /");
        }
      } else {
        echo "User is not logged in.\n";
      }
    }
  }
  catch(ErrorException $e)
  {
    echo $e->getMessage();
  }

}
