<?php
if(!defined("INCLUDED")) die("Access forbidden.");

require_once $_SERVER['DOCUMENT_ROOT']."/engine/util/tp_framework.php";
if(file_exists($_SERVER['DOCUMENT_ROOT']."/WEB_DISABLED"))
{
    die(render('pages/disabled'));
}

ob_start();

$_DATA = [
    'page_name' => '#Navigation_Main',
    'og' => [
        'type' => 'website',
        'url' => $_SERVER['REQUEST_URI'],
        'image' => 'https://creators.tf/cdn/assets/images/creators_logo_square.png',
        'description' => 'Become part of the Creators.TF project and help us deliver content to the community.'
    ]
];

$Content = NULL;
$_GET["page"] = $_GET["page"] ?? NULL;

if($_GET["page"] == "my")
{
    if(isset($Core->User))
    {
      if($_GET["my"] == "inv") header('Location: /profiles/'.$Core->User->alias."/inventory");

    }else{
        $Core->error = ERROR_NO_PERMISSION;
    }
}

// $Core->users->__debug_ShowCache();
// $Core->getCache()->flush();

require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/campaigns.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/blog.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/economy.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/static.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/short.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/profile.php";
require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/workshop.php";


if(defined("RENDER_INCLUDE_FILE"))
{
    require_once RENDER_INCLUDE_FILE;
}

if(defined("RENDER_ONLY_CONTENT"))
{
    echo $Content;

} else {
    if($Core->error != ERROR_NULL)
    {

        $Error = $Core->errors->find("code", $Core->error);
        $_DATA['page_name'] = $Error->title;
        $Content = render(
            "error",
            [
                "title" => $Error->title,
                "content" => $Error->content,
                "code" => $Error->code,
                "steamid" => $Core->User->steamid ?? NULL
            ]
        );
  }
  echo render(
    "main",
    [
      'headers' => format(
        '<title>#Website_Title %s %s</title>
        <script>window.csrfvalidation = "%s"</script>',
        [
          $Core->config->website->separator,
          $_DATA['page_name'],
          $_SESSION["X-CSRF"]
        ]
      ),
      'og:title' => $_DATA['page_name'],
      'og:type' => $_DATA["og"]["type"],
      'og:url' => $_DATA["og"]["url"],
      'og:image' => $_DATA["og"]["image"],
      'og:description' => htmlentities($_DATA["og"]["description"]),
      "recaptcha_api" => $Core->config->api->recaptcha_public,
      "content" => $Content,
      'uri' => $_SERVER['REQUEST_URI'],
      'username' => $Core->User->name ?? NULL,
      'username_f' => addslashes(htmlentities($Core->User->name ?? NULL)),
      'avatar' => $Core->User->avatar ?? NULL,
      'alias' => $Core->User->alias ?? ($Core->User->steamid ?? NULL),
      'steamid' => $Core->User->steamid ?? NULL,
      'monthly' => $Core->users->getPlayersThisMonth()
    ],
    [
      "NOLOGIN" => !isset($Core->User),
      "LOGIN" => isset($Core->User)
    ]
  );
}

$_OUTPUT = ob_get_contents();
ob_end_clean();

echo $_OUTPUT;
?>
