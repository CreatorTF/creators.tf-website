<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "report"){
  if($_GET["type"] == "error")
  {
    preg_match("/([^a-zA-Z0-9\+\/\=]+)/", $_GET["raw"], $a);
    if(count($a) > 0)
    {
      $Core->error = ERROR_UNEXPECTED;
    }else{
      $Link = substr(md5($_GET["raw"].time()), 0, 12);
      $Core->db->query(format("INSERT INTO tf_short (link, redirect, expires) VALUES ('%s', '%s', '%s')",[
        $Link,
        "/report/view?raw=".urlencode($_GET["raw"]),
        date("Y-m-d H:i:s", time() + 7 * 24 * 60 * 60)
      ]));

      $Core->db->query("DELETE FROM tf_short WHERE expires IS NOT NULL AND expires < NOW()");

      $msg = format("https://creators.tf/s/%s", [$Link]);
      discord_webhook_send($Core->config->webhooks->DISCORD_ERROR_REPORT, $msg);

      header("Location: /");
    }
  }else if($_GET["type"] == "view"){
    $_DATA["page_name"] = "View Report";
    $_REPORT = json_decode(base64_decode($_GET["raw"]), true);
    if(isset($_REPORT))
    {
      $Content = render("page", [
        "title" => "View Report",
        "content" => addslashes(join("", array_map(function($k, $v){return format("<h3>%s</h3><p>%s</p>", [$k, htmlspecialchars(urldecode($v))]);}, array_keys($_REPORT), $_REPORT)))
      ]);
    }else{
      $Core->error = ERROR_UNEXPECTED;
    }
  }
}
?>
