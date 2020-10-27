<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
    if(!CheckPermission_CanRead())
        ThrowAPIError(403);

    // User may request class information for one specific class.
    // However if nothing specified we return information for all classes.
    $Classes = [];
    if(isset($_REQ["class"]))
    {
        $Classes = preg_split( "/(,|;)/", $_REQ["class"] ?? "");
        $Classes = array_intersect($classes, $Core->config->economy->classes);
    }

    $Loadout = $Core->User->getLoadout();
    $Return = [];

    foreach ($Core->config->economy->classes as $Class)
    {
        // If user requests specific classes, we skil all classes that they didn't
        // request.
        if(count($Classes) > 0) {
            if(!in_array($Class, $Classes)) continue;
        }

        // Init array for items of a class.
        $Return[$Class] = [];

        $ClassLoadout = $Loadout->getClass($Class);
        $Items = $ClassLoadout->getItems();

        if(count($Items) > 0) {
            foreach ($Items as $Item) {
                array_push($Return[$Class], [
                    'id' => $Item->id,
                    'defid' => $Item->defid,
                    'quality' => $Item->quality,
                    'attributes' => $Item->_attributes
                ]);
            }
        }
    }

    ThrowResult(["loadout" => $Return]);
}else if($method == 'POST')
{
    if(!CheckPermission_CanWrite())
        ThrowAPIError(403);

    $param = check($_REQ,['class','index','slot'], true);
    if(isset($param))
        ThrowAPIError(ERROR_API_INVALIDPARAM,$param);

    if(!in_array($_REQ["slot"],array_keys((array) $Core->config->economy->slots)))
        ThrowAPIError(ERROR_API_INVALIDPARAM, $_REQ["slot"]);

    $Loadout = $Core->User->getLoadout();
    $Loadout = $Loadout->getClass($_REQ["class"]);

    $Item = NULL;
    $OldItem = $Loadout->getSlot($_REQ["slot"]);

    if($_REQ["index"] > 0)
    {
        $Item = $Core->items->findAND('steamid', $Core->User->steamid,'id', $_REQ["index"]);
        if(!isset($Item))
            ThrowAPIError(403);

        if(!$Loadout->canEquipItem($Item, $_REQ["slot"]))
        {
            // If we naturally can't equip this item, we throw an error.
            ThrowAPIError(403);
        }

        if($Loadout->canConflictItem($Item, $_REQ["slot"]))
        {
            // If this items conflicts with any other one with equip regions, we throw an error.
            ThrowAPIError(403);
        }

        if($Item->def->type == "weapon")
        {
            $_SESSION["last_id"] = $Item->id;
        }
    } else {
        $_SESSION["last_id"] = 0;
    }

    $Loadout->equipItem($Item, $_REQ["slot"]);

    ThrowResult(['url'=>'/loadout/'.$_REQ["class"]]);

    /*
    // Logging.
    $Core->logs->create($Core->User->steamid, "item_equip", [
        "class" => $_REQ["class"],

        "new_item_index" => isset($Item) ? $Item->id : 0,
        "new_item_defindex" => isset($Item) ? $Item->defid : 0,

        "old_item_index" => isset($OldItem) ? $OldItem->id : 0,
        "old_item_defindex" => isset($OldItem) ? $OldItem->defid : 0,

        "slot" => $_REQ["slot"]
    ]);*/

    $Class = $_REQ["class"];
    if($Class == "demo") $Class = "demoman";

    $Core->User->_Server_CleanInventoryCache($Class);
}
?>
