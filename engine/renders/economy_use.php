<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "item_use")
{
    $_DATA["page_name"] = "#Navigation_Inventory";
    $Item = $Core->items->findAND("id", $_GET["item"], "steamid", $Core->User->steamid);
    if(isset($Item))
    {
        if($Item->slot >= $Core->User->getMaxBackpackSlots())
        {
            $Core->error = 403;
        } else {
            if($Item->def->type == "tool")
            {
                switch($Item->def->tool_type)
                {
                    case "collection_drop":
                        $Item = $Item->cast_as("ItemToolLootbox");
                        $hCollection = $Item->getCollection();

                        if(isset($hCollection))
                        {
                            $Content = render('pages/items/lootbox', [
                                'lootbox' => $Item->toDOM(),
                                'index' => $Item->id,
                                'name' => $Item->name,
                                'description' => $Item->def->description,
                                'attributes' => $Item->toDOMAttributes(),

                                'contents' => join("", array_map(function($hDef) {
                                    return $hDef->toDOM();
                                }, $hCollection->getEconItemDefinitions())),
                            ]);
                        } else {
                            $Core->error = ERROR_UNEXPECTED;
                        }

                        break;

                    case "item_modifier":
                        $Item = $Item->cast_as("ItemToolModifier");
                        if(isset($_GET["target"]))
                        {
                            $Target = $Core->items->findAND("id", $_GET["target"], "steamid", $Core->User->steamid);
                            if(isset($Target))
                            {
                                if($Item->canApplyTo($Target))
                                {
                                    $Content = render('pages/items/use', [
                                        'title' => $Target->name,
                                        'paint' => $Item->name,
                                        'description' => $Item->def->description,
                                        'items' => $Item->toDOM().$Target->toDOM(),
                                        'attributes' => $Item->toDOMAttributes(),
                                        'item-index' => $Item->id,
                                        'target-index' => $Target->id
                                    ]);
                                } else $Core->error = 403;
                            } else $Core->error = ERROR_NO_PERMISSION;
                        } else {
                            $Targets = $Core->User->getBackpack();
                            $Candidates = [];

                            foreach($Targets as $Target)
                            {
                                if(!$Item->canApplyTo($Target)) continue;
                                array_push($Candidates, $Target);
                            }

                            $Content = render("page", [
                                'title' => $Item->name."<span class=\"qp-options-context\"> » Use with:</span>",
                                'content' => render('item_chooser',
                                [
                                    'items'=> join("", array_map(
                                        function($Target)
                                        {
                                            global $Item;
                                            return $Target->toDOM([
                                                'tool' => $Item->id
                                            ],  [
                                                "USE" => true
                                            ]);
                                        }, $Candidates
                                    )),
                                    'class' => ""
                                ])
                            ]);
                        }
                        break;

                    case "backpack_expander":
                        $Content = render('pages/items/backpack', [
                            'title' => $Item->name,
                            'items' => $Item->toDOM(),
                            'description' => $Item->def->description,
                            'attributes' => $Item->toDOMAttributes(),
                            'index' => $Item->id
                        ]);
                        break;
                }
            } else {
                $Core->error = ERROR_NO_PERMISSION;
            }
        }
    }else{
        $Core->error = ERROR_NO_PERMISSION;
    }
}
?>
