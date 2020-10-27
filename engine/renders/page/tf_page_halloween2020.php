<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("RENDER_ONLY_CONTENT", true);
if($_GET["surname"] == "theredfiles")
{
    $Content = render("pages/landing/halloween2020_teaser");
} else {
    $Content = render("pages/landing/halloween2020");
}

?>
