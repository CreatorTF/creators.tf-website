<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("RENDER_ONLY_CONTENT", true);

$iNow = time();
$iThen = strtotime("2017-10-20");
$iDiff = $iNow - $iThen;

$hDays = sprintf('%04d', round($iDiff / (60 * 60 * 24)));

$Content = render('pages/landing/promo',
[
    'timer:1' => $hDays[0],
    'timer:2' => $hDays[1],
    'timer:3' => $hDays[2],
    'timer:4' => $hDays[3]
]);
?>
