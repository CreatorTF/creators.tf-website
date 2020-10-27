<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("EXTRALOOT_BRUSHES_CHANCE", 2);
define("EXTRALOOT_BRUSHES_COLLECTION", "ExtraLootPaints_collection");

define("EXTRALOOT_STRANGIFIER_CHANCE", 2);
define("EXTRALOOT_STRANGIFIER_COLLECTION", "ExtraLootStrangifier_collection");

define("EXTRALOOT_BACKPACK_EXPANDER_CHANCE", 2);
define("EXTRALOOT_BACKPACK_EXPANDER_COLLECTION", "ExtraLootBackpackExpander_collection");

class ItemToolLootbox extends ItemTool
{
    public function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function getCollection()
    {
        return $this->m_hCore->items->getCollectionByName($this->def->collection_reference);
    }

    function use($Target = NULL)
    {
        parent::use($Target);
        $idx = $this->m_hCore->items->getRandomCollectionItemIndex($this->def->collection_reference);
        $hConfig = $this->m_hCore->items->getItemConfigByDefIndex($idx);

        $iQuality = Q_UNIQUE;
        $hAttributes = [];

        if(($hConfig["capabilities"]["drop_as_unusual"] ?? false) == true)
        {
            // Check if this item will be unusual.
            $iChance = $this->getAttributeByName("lootbox unusual chance");
            if(rand(0,100) < $iChance && $iQuality == Q_UNIQUE){
                $iParticle = $this->m_hCore->items->getRandomUnusualGroupParticle($this->def->unusual_chance_group);

                if($iParticle != 0)
                {
                    $iQuality = Q_UNUSUAL;
                    array_push($hAttributes, ['name' => 'attach particle effect', 'value' => $iParticle]);
                }
            }
        }

        if(($hConfig["capabilities"]["can_strangify"] ?? false) == true)
        {
            // Check if this item will be strange.
            $iChance = $this->getAttributeByName("lootbox strange chance");
            if(rand(0,100) < $iChance && $iQuality == Q_UNIQUE){
                $iQuality = Q_STRANGE;

                $EaterType = $this->m_hCore->items->getStrangeEaterFromType($hConfig["type"]);
                if($EaterType != NULL)
                {
                    array_push($hAttributes, ['name' => 'strange eater', 'value' => $EaterType]);
                }
            }
        }

        $Loot = [];
        array_push($Loot, ["id" => $idx, "quality" => $iQuality, "attributes" => $hAttributes]);

        foreach (($this->def->additional_collection_references ?? []) as $k => $v) {
            switch ($k) {
                case 'strangifier':
                    if(rand(0,100) < $v)
                    {
                        $idx = $this->m_hCore->items->getRandomCollectionItemIndex(EXTRALOOT_STRANGIFIER_COLLECTION);
                        if($idx != NULL)
                            array_push($Loot, ["id" => $idx, "quality" => Q_UNIQUE, "attributes" => []]);
                    }
                    break;
                case 'paint_brushes':
                    if(rand(0,100) < $v)
                    {
                        $idx = $this->m_hCore->items->getRandomCollectionItemIndex(EXTRALOOT_BRUSHES_COLLECTION);
                        if($idx != NULL)
                            array_push($Loot, ["id" => $idx, "quality" => Q_UNIQUE, "attributes" => []]);
                    }
                    break;
                case 'backpack_expander':
                    if(rand(0,100) < $v)
                    {
                        $idx = $this->m_hCore->items->getRandomCollectionItemIndex(EXTRALOOT_BACKPACK_EXPANDER_COLLECTION);
                        if($idx != NULL)
                            array_push($Loot, ["id" => $idx, "quality" => Q_UNIQUE, "attributes" => []]);
                    }
                    break;
            }
        }

        $this->removeIfNoMoreUses();
        $Items = $this->m_hCore->items->createMultiple($this->m_hCore->User->steamid, $Loot);

        return $Items;
    }
}
?>
