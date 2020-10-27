<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "profile")
{
  $Profile = $Core->users->findOR("steamid", $_GET["profile"], "alias", $_GET["profile"]);

  if(isset($Profile)){
    $_DATA['page_name'] = $Profile->name;
    $_DATA["og"]["image"] = $Profile->avatar;
    $SCUT = explode(" ",strip_tags($Profile->motd));
    $SCUT = array_slice($SCUT, 0,15);
    $Content = render(
      "users/profile",
      [
        "avatar" => $Profile->avatar,
        "status" => "offline",
        "summary" => $Profile->motd,
        "username" => $Profile->name,
        "steamid" => $Profile->steamid,
        'alias' => isset($Profile->alias)?$Profile->alias:$Profile->steamid,
        'special' => $Profile->toDOMSpecial(),

        // TODO: Uncomment profile comments once we ship profile update.

        /*"comments" => render("prefabs/comments", [
          "target" => "user",
          "id" => $Profile->id,
          "count" => 6
        ], [
          "LOGIN" => isset($Core->User)
        ])*/
        "comments" => null,

        "ip" => $Profile->isOnServer() ? $Profile->getServer()->ip : NULL,
        "port" => $Profile->isOnServer() ? $Profile->getServer()->port : NULL,
        "server_name" => $Profile->isOnServer() ? $Profile->getServer()->cache : NULL
      ], [
        "OWNER" => isset($Core->User)?($Profile->id == $Core->User->id):false,
        "GUEST" => isset($Core->User)?($Profile->id != $Core->User->id):true,
        "LOGGED" => isset($Core->User),
        'FAKE' => !$Profile->real,
        'REAL' => $Profile->real,

        'RICH_PRESENCE' => $Profile->isOnServer()
      ]
    );
  }else{
    $Core->error = ERROR_NOT_FOUND;
  }
}
?>
