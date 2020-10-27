<?php
define("INCLUDED", true);

define("PARTICLE_ATTRIBUTE_NAME", "attach particle effect");
define("PARTICLE_ATTRIBUTE_VALUE", 4);

define("SELFMADE_ATTRIBUTE_NAME", "selfmade description");
define("SELFMADE_ATTRIBUTE_VALUE", 1);

require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST')
{
    $param = check($_REQ,['item_id', 'submission_id', 'key']);
    if(isset($param))
        ThrowAPIError(ERROR_API_INVALIDPARAM,$param);

    $Key = $Core->apikeys->find("apikey", $_REQ["key"]);
    if(!isset($Key) || !$Key->hasPermission(APISPECIAL_ADMIN_PROMO_DISTRIBUTE))
        ThrowCustomAPIError(403, "Invalid API key");

    $Submission = $Core->submissions->find("id", $_REQ["submission_id"]);

    if(!isset($Submission))
        ThrowAPIError(404);

    $Item = $Core->items->getItemConfigByDefIndex($_REQ["item_id"]);
    if(!isset($Item))
        ThrowAPIError(404);

    foreach ($Submission->authors as $steamid) {
        // Each one gets a self-made copy of an item.
        $Core->items->create($steamid, $_REQ["item_id"], Q_SELFMADE, [[
            "name" => SELFMADE_ATTRIBUTE_NAME,
            "value" => SELFMADE_ATTRIBUTE_VALUE
        ], [
            "name" => PARTICLE_ATTRIBUTE_NAME,
            "value" => PARTICLE_ATTRIBUTE_VALUE
        ]]);
    }

    ThrowResult(["recipents" => $Submission->authors]);
}
?>
