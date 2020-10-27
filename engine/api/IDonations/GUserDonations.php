<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
    $param = check($_REQ, ["steamid"]);
    if(isset($param)) ThrowAPIError(5, $param);

    if(!is_numeric($_REQ["steamid"])) ThrowAPIError(5, "steamid");

    $User = $Core->users->find("steamid", $_REQ["steamid"]);
    if(!isset($User)) ThrowAPIError(404);

    if(
        !(isset($Core->User) && $Core->User->id == $User->id)
        // TODO: Make another check based on user's preferences.
    ) ThrowAPIError(403);

    $_OFFSET = (+($_REQ["offset"] ?? 0));
    if(!is_numeric($_OFFSET)) ThrowAPIError(ERROR_UNEXPECTED);

    if($_OFFSET < 0) $_OFFSET = 0;
    $_MONTH = +date("n");
    $_YEAR = +date("Y");

    $_MONTH -= $_OFFSET;
    while($_MONTH <= 0)
    {
        $_MONTH += 12;
        $_YEAR -= 1;
    }

    $Count = $Core->dbh->getRow(
        " SELECT sum(tf_pledges.cents_amount) as total
        FROM tf_pledges
        INNER JOIN tf_users ON JSON_CONTAINS(tf_users.connections, CONCAT('{\"patreon\": {\"id\":\"',tf_pledges.charger_id,'\"}}'))
        WHERE MONTH(tf_pledges.charge_time) = %d AND YEAR(tf_pledges.charge_time) = %d AND tf_users.steamid = %s", [$_MONTH, $_YEAR, $User->steamid]
    ) ["total"];

    $Count = (+$Count);

    ThrowResult([
        'steamid' => $_REQ["steamid"],
        'amount' => $Count,
        "time" => [
            "year" => $_YEAR,
            "month" => (+$_MONTH)
        ],
    ]);
} else if($method == "POST"){
    
} else {
    http_response_code(404);
}
?>
