<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];
if($method == 'POST')
{
    if(!CheckPermission_CanWrite())
    ThrowAPIError(403);

    $param = check($_REQ,['item_id']);
    if(isset($param))
        ThrowAPIError(ERROR_API_INVALIDPARAM,$param);

    $Item = $Core->items->findAND("id", $_REQ["item_id"], "steamid", $Core->User->steamid);
    if(!isset($Item))
        ThrowAPIError(403);

    if($Item->slot >= $Core->User->getMaxBackpackSlots())
        ThrowAPIError(403);

    if($Item->def->type == "tool")
    {
        switch ($Item->def->tool_type) {
            case 'collection_drop':
                if(!$Core->User->canGetMoreItems())
                    ThrowCustomAPIError(403, "Not enough space in your backpack. This crate wasn't deleted.");

                $Item = new ItemToolLootbox($Item->m_hData, $Core);
                $Loot = $Item->use();

                ThrowResult([
                    'loot' => array_map(function($a) {
                        global $iQuality;
                        global $Attributes;
                        global $Core;
                        return [
                            'name' => $a->name,
                            'attributes' => json_encode($Core->items->parseAttributes($a->_attributes, $a)),
                            'quality' => $a->quality,
                            'quality_color' => $a->quality_color,
                            'image' => $a->image
                        ];
                    }, $Loot)
                ]);

                foreach ($Loot as $hLootItem)
                {
                    $Core->notifications->create($Core->User->steamid, "lootbox_loot", [
                        "item_index" => $hLootItem->id,
                        "lootbox_def_index" => $Item->defid
                    ]);
                }

                break;

            case 'item_modifier':

                // This tool requires target item.
                if(!isset($_REQ["target"]))
                    ThrowAPIError(403);

                // Check if this item truly exists.
                $Target = $Core->items->findAND("id", $_REQ["target"], "steamid", $Core->User->steamid);
                if(!isset($Target))
                    ThrowAPIError(404);

                // Treat this item as tool.
                $Item = new ItemToolModifier($Item->m_hData, $Core);

                // Check if we can apply target item to the tool.
                if(!$Item->canApplyTo($Target))
                    ThrowAPIError(403);

                // Use the tool on the target.
                $Item->use($Target);

                ThrowResult();

                // Clean loadout cache on active server.
                $Core->User->_Server_CleanInventoryCache();
                break;

            case 'backpack_expander':

                // Treat this item as a tool.
                $Item = new ItemToolBackpackExpander($Item->m_hData, $Core);
                // Use this item.
                $Item->use();

                ThrowResult();
                break;
        }
    }else{
        ThrowAPIError(ERROR_NO_PERMISSION);
    }
} else if($method == "PATCH") {
    if(!CheckPermission_CanWrite())
        ThrowAPIError(403);

    $Item = $Core->items->findAND("id", $_REQ["item_id"], "steamid", $Core->User->steamid);
    if(isset($Item))
    {
        if(!$Item->canScrap() || $Item->slot >= $Core->User->getMaxBackpackSlots())
            ThrowAPIError(ERROR_NO_PERMISSION);
        $iSlot = $Item->slot;

        $Item->scrap();

        // Check if we have any overflow items that may take this slot.
        $Overflow = $Core->User->getFirstOverflowItem();
        if(isset($Overflow)) {
            $Overflow->setSlot($iSlot);
            ThrowResult([
                "overflows" => [[
                    "id" => $Overflow->id,
                    "slot" => $iSlot
                ]],
                "value" => $Item->scrap
            ]);
        } else {
            ThrowResult([
                "overflows" => [],
                "value" => $Item->scrap
            ]);
        }
        $Core->User->_Server_CleanInventoryCache();
    }else{
        ThrowAPIError(ERROR_NO_PERMISSION);
    }
}else if($method == 'DELETE')
{
    if(!CheckPermission_CanWrite())
    {
        ThrowAPIError(403);
    }

    $param = check($_REQ,['item_id'], true);
    if(isset($param))
    {
        ThrowAPIError(ERROR_API_INVALIDPARAM,$param);
    }

    $Item = $Core->items->findAND("id", $_REQ["item_id"], "steamid", $Core->User->steamid);
    if(isset($Item))
    {
        $iSlot = $Item->slot;

        $Item->remove();

        // Check if we have any overflow items that may take this slot.
        $Overflow = $Core->User->getFirstOverflowItem();
        if(isset($Overflow)) {
            $Overflow->setSlot($iSlot);
            ThrowResult(["overflows" => [
                ["id" => $Overflow->id, "slot" => $iSlot]
            ]]);
        } else {
            ThrowResult(["overflows" => []]);
        }

        $Core->User->_Server_CleanInventoryCache();
    }else{
        ThrowAPIError(ERROR_NO_PERMISSION);
    }
}
?>
