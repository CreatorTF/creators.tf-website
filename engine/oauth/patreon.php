<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/pre_loader.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/oauth.php";

require_once $_SERVER['DOCUMENT_ROOT'].'/engine/oauth/patreon/API.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/engine/oauth/patreon/OAuth.php';
if(!isset($Core->User))
{
	header('location: /engine/oauth/steam.php');
	die();
}

if($_GET["action"] == "logout"){
		$Core->User->DisconnectIntegration("patreon");
    Header("Location: /donate");
    die();
}
//SELECT * FROM `tf_users` WHERE JSON_CONTAINS(`connections`, '{"patreon":{"id":"bioxdevcompany@gmail.com"}}');
use Patreon\API;
use Patreon\OAuth;

$client_id = 'ZYp9KMrXcyrEN5-XDVWvZqLVGjxL3yQXFosmxPH2gE73XOz1LC4XLI3X2Y_Q3n58';
$client_secret = 'qMTy0-9dqQlfvytb7lo_IkTFhz6NJm1FxG5sX9udbTJ_ofHWHFIdbwGY75wCZjMB';

$redirect_uri = "http://".$_SERVER['HTTP_HOST']."/engine/oauth/patreon.php";

$href = 'https://www.patreon.com/oauth2/authorize?response_type=code&client_id='
. $client_id . '&redirect_uri=' . urlencode($redirect_uri);
$state = array();
$state_parameters = '&state=' . urlencode( base64_encode( json_encode( $state ) ) );
$href .= $state_parameters;
$scope_parameters = '&scope=identity%20identity'.urlencode("[email]");
$href .= $scope_parameters;
if ( isset($_GET["code"]) && $_GET['code'] != '') {

	$oauth_client = new OAuth($client_id, $client_secret);

	$tokens = $oauth_client->get_tokens($_GET['code'], $redirect_uri);

	if(!isset($tokens["access_token"]))
	{
		header('location: /engine/oauth/patreon.php');
		die();
	}

	$access_token = $tokens['access_token'];
	$refresh_token = $tokens['refresh_token'];
	$api_client = new API($access_token);
	$api_client->api_return_format = 'object';
	$patron_response = $api_client->fetch_campaigns();

	$PatreonUser = $api_client->fetch_user();

	$Core->User->ConnectIntegration("patreon",[
		"id" => $PatreonUser->data->attributes->email,
		"name" => $PatreonUser->data->attributes->full_name,
		"avatar" => $PatreonUser->data->attributes->image_url
	]);

	echo render('pages/connection', [
		"name" => "<i class='mdi mdi-patreon'></i> ".$PatreonUser->data->attributes->full_name ?? '1',
		"avatar" => $PatreonUser->data->attributes->image_url ?? '1',
		"text" => "Patreon Profile",
		"connect_title" => "Patreon Connected",
		"url" => "/donate",
		"connect_summary" => "You have successfully connected Patreon account to the site. Now you can support us through this platform and get many different bonuses."
	]);
}else{
	header("Location: $href");
}
