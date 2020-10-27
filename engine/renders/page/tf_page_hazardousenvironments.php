<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("RENDER_ONLY_CONTENT", true);
switch ($_GET["surname"])
{
    default:
        $Content = render("pages/landing/hev/hev_day1");
        break;

    case 'features':
        $Content = render("pages/landing/hev/hev_day2");
        break;

    case 'weapons':
        $Content = render("pages/landing/hev/hev_day3");
        break;

    case 'release':
        $Content = render("pages/landing/hev/hev_day4");
        break;
}

?>
