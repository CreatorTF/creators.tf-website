<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("PATREON_MONTHLY_GOAL", 750);

$_MONTH = +date("n");
$_YEAR = +date("Y");

$Current = $Core->db->getRow("SELECT sum(cents_amount) as cents FROM tf_pledges WHERE MONTH(tf_pledges.charge_time) = ".$_MONTH." AND YEAR(tf_pledges.charge_time) = ".$_YEAR)["cents"];

if(isset($Core->User->connections->patreon->id))
{
    $Person = $Core->db->getRow("SELECT sum(cents_amount) as cents FROM tf_pledges WHERE charger_id = '".$Core->User->connections->patreon->id."'")["cents"];
}

$Content = render('pages/donate', [
    "patreon.name" => $Core->User->connections->patreon->name ?? NULL,
    "patreon.lifetime" => ($Person ?? 0) / 100,
    "patreon.avatar" => $Core->User->connections->patreon->avatar ?? NULL,
    "goal.current" => $Current / 100,
    "goal.goal" => PATREON_MONTHLY_GOAL,
    "goal.percent" => floor((($Current / 100) / PATREON_MONTHLY_GOAL) * 100),
    "goal.percent.capped" => min(floor((($Current / 100) / PATREON_MONTHLY_GOAL) * 100), 100)
],
[
    "PATREON_CONNECTED" => isset($Core->User->connections->patreon->id),
    "PATREON_UNCONNECTED" => !isset($Core->User->connections->patreon->id)
]);
?>
