<?php
if(!defined("INCLUDED")) die("Access forbidden.");

require_once $_SERVER['DOCUMENT_ROOT']."/engine/renders/economy_use.php";

if($_GET["page"] == "inventory")
{
    $Profile = $Core->users->findOR("steamid",$_GET["profile"],"alias",$_GET["profile"]);
    if(isset($Profile))
    {
        $_DATA["page_name"] = $Profile->name." :: #Navigation_Inventory";
        if(!$Profile->canSeeInventory($Core->User))
        {
            $Core->error = ERROR_PRIVATE_INVENTORY;
        } else {
            $Content = render("page",
            [
                'title' => $Profile->name.'<span class="qp-options-context"> » Inventory</span>',
                'content' => render('pages/items/inventory',
                    [
                        'alias' => $Profile->alias ?? $Profile->steamid,
                        'pages' => $Profile->backpack_pages
                    ], [
                        "OWNER" => $Core->User->id == $Profile->id,
                        "NOT_OWNER" => $Core->User->id != $Profile->id,
                    ]
                )
            ]);
        }
    }else{
        $Core->error = ERROR_NOT_FOUND;
    }
}

if($_GET["page"] == "item_store")
{
    $_DATA["page_name"] = "#Navigation_Store";
    if(isset($Core->User))
    {
        $Content = render('pages/items/store',[
            'balance' => $Core->User->credit,
            'max_checkout_items' => $Core->config->economy->max_checkout_items
        ]);
    }else{
        $Core->error = ERROR_NO_PERMISSION;
    }
}

if($_GET["page"] == "loadout") {

    $_DATA["page_name"] = "#Navigation_Loadout";

    if(isset($Core->User)){

        $Loadout = $Core->User->getLoadout();
        $Loadout = $Loadout->getClass($_GET["class"]);

        $Slots = [];
        switch ($_GET["page"]) {
            case 'loadout':
                $Slots = [ "WEAR_1","WEAR_2","WEAR_3","ACTION",
                            "PRIMARY","SECONDARY","MELEE"];

                if(in_array($_GET["class"], ["engineer", "spy"]))
                    array_push($Slots, "PDA");

                break;
            case 'taunts':
                $Slots = [ "TAUNT_1","TAUNT_2","TAUNT_3","TAUNT_4",
                            "TAUNT_5","TAUNT_6","TAUNT_7","TAUNT_8"];
                break;
        }

        $Content = render(
            "pages/items/loadout",
            [
                "PRIMARY" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("PRIMARY"), "PRIMARY"),
                "SECONDARY" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("SECONDARY"), "SECONDARY"),
                "MELEE" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("MELEE"), "MELEE"),
                "PDA" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("PDA"), "PDA"),

                "WEAR_1" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("WEAR_1"), "WEAR_1"),
                "WEAR_2" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("WEAR_2"), "WEAR_2"),
                "WEAR_3" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("WEAR_3"), "WEAR_3"),
                "ACTION" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("ACTION"), "ACTION"),

                "TAUNT_1" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_1"), "TAUNT_1"),
                "TAUNT_2" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_2"), "TAUNT_2"),
                "TAUNT_3" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_3"), "TAUNT_3"),
                "TAUNT_4" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_4"), "TAUNT_4"),
                "TAUNT_5" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_5"), "TAUNT_5"),
                "TAUNT_6" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_6"), "TAUNT_6"),
                "TAUNT_7" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_7"), "TAUNT_7"),
                "TAUNT_8" => $Core->items->toDOMSlotIndexed($Loadout->getSlot("TAUNT_8"), "TAUNT_8"),

                "class" => $_GET["class"],
                "loadout" => json_encode($Loadout->getPreviewData($Loadout->getItemByID($_SESSION["last_id"])))
            ], [
                "PRIMARY" => in_array("PRIMARY", $Slots),
                "SECONDARY" => in_array("SECONDARY", $Slots),
                "MELEE" => in_array("MELEE", $Slots),
                "PDA" => in_array("PDA", $Slots),

                "WEAR_1" => in_array("WEAR_1", $Slots),
                "WEAR_2" => in_array("WEAR_2", $Slots),
                "WEAR_3" => in_array("WEAR_3", $Slots),
                "ACTION" => in_array("ACTION", $Slots),

                "TAUNT_1" => in_array("TAUNT_1", $Slots),
                "TAUNT_2" => in_array("TAUNT_2", $Slots),
                "TAUNT_3" => in_array("TAUNT_3", $Slots),
                "TAUNT_4" => in_array("TAUNT_4", $Slots),
                "TAUNT_5" => in_array("TAUNT_5", $Slots),
                "TAUNT_6" => in_array("TAUNT_6", $Slots),
                "TAUNT_7" => in_array("TAUNT_7", $Slots),
                "TAUNT_8" => in_array("TAUNT_8", $Slots),
            ]
        );

    } else $Core->error = 403;
}
if($_GET["page"] == "item_chooser"){
    $_DATA["page_name"] = "#Navigation_Inventory";
    if(isset($Core->User)){
        $Backpack = $Core->User->getBackpack();

        $Loadout = $Core->User->getLoadout();
        $Loadout = $Loadout->getClass($_GET["class"]);


        $Content = render("page",[
            'title' => "#TFClass_".ucfirst($_GET["class"])."_Name <span class=\"qp-options-context\"> » ".$Core->config->economy->slots->{$_GET["slot"]}->name."</span>",
            'content' => render('item_chooser',
            [
                'items' => join("", array_merge([
                    $Core->items->toDOMfromDefID(-1, 0, [
                        "class" => $_GET["class"],
                        "slot" => $_GET["slot"],
                        'index' => 0
                    ],[
                        "EQUIP" => true
                    ]),
                    $Loadout->getSlot($_GET["slot"]) != NULL
                        ?   $Loadout->getSlot($_GET["slot"])->toDOM([
                                "class" => $_GET["class"],
                                "slot" => $_GET["slot"]
                            ],[
                                "EQUIP" => true
                            ])
                        : null
                    ],
                    array_map(function($Item) {
                        global $Core;
                        global $Loadout;

                        if(!$Loadout->canEquipItem($Item, $_GET["slot"])) return;
                        if($Loadout->getItemSlot($Item) == $_GET["slot"]) return;

                        $CanCoflict = $Loadout->canConflictItem($Item, $_GET["slot"]);

                        return $Item->toDOM([
                            "class" => $_GET["class"],
                            "slot" => $_GET["slot"],
                            "classname" => $CanCoflict ? "disabled" : ""
                        ],[
                            "EQUIP" => !$CanCoflict
                        ]);
                    }, $Backpack)
                ))
            ])
        ]);
    }else{
      $Core->error = ERROR_NO_PERMISSION;
    }
}
?>
