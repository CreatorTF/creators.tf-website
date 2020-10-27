<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "campaign")
{
    require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/campaign/tf_campaign_halloween.php";
}
?>
