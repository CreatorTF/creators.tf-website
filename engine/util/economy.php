<?php
if(!defined("INCLUDED")) die("Access forbidden.");
$_ECONOMY = NULL;
try{
    $_ECONOMY = [
        "Version" => [],
        "Items" => json_throw_named("/engine/economy/items.json"),
        "item_collections" => json_throw_named("/engine/economy/collections.json"),
        "Qualities" => json_throw_named("/engine/economy/qualities.json"),
        "NameStrings" => json_throw_named("/engine/economy/namestrings.json"),
        "Attributes" => json_throw_named("/engine/economy/attributes.json"),
        "LogicPrefabs" => json_throw_named("/engine/economy/logic_prefabs.json"),
        "Contracker" => [
            "Campaigns" => json_throw_named("/engine/economy/contracker/campaigns.json"),
            "Directory" => json_throw_named("/engine/economy/contracker/directory.json"),
            "Quests" => json_throw_named("/engine/economy/contracker/quests.json")
        ],
        "Stranges" => [
            "StrangeParts" => json_throw_named("/engine/economy/stranges/strange_parts.json"),
            "LevelData" => json_throw_named("/engine/economy/stranges/level_data.json")
        ],
        "unusual_groups" => json_throw_named("/engine/economy/unusual_groups.json"),
        "Store" => json_throw_named("/engine/economy/store.json")
    ];
    $_ECONOMY["Version"]["build"] = md5(json_encode($_ECONOMY));
}catch(Exception $e)
{
    $_ECONOMY = ["Economy"=>["Version"=>["failed"=>"1", "file"=>$e->getMessage()]]];
}

function json_throw_named($path)
{
    try{
        return json_decode_with_error(file_get_contents($_SERVER['DOCUMENT_ROOT'].$path));
    }catch(Exception $e)
    {
        $filename = basename($path, ".json");
        throw new Exception($filename);
    }
}

function json_decode_with_error($content, $assoc = true)
{
    $obj = json_decode($content, $assoc);
    if(empty($obj))
        throw new Exception("Invalid JSON");
    return $obj;
}
?>
