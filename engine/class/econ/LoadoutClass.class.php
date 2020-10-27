<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class LoadoutClass extends Loadout
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hItems = $data["items"];
        $this->m_sClass = $data["class"];
        $this->m_hOwner = $data["owner"];
        $this->m_hLoadout = $data["loadout"];
    }

    function equipItem($Item, $Slot)
    {
        $raw = $this->getOwner()->loadout;

        if(isset($Item))
        {
            if(!$this->canEquipItem($Item, $Slot)) return;
            if($this->canConflictItem($Item, $Slot)) return;

            foreach ($raw->{$this->m_sClass} as $k => $v)
            {
                if($v == $Item->id)
                {
                    unset($raw->{$this->m_sClass}->{$k});

                    $i = array_ksearch($this->m_hItems, "id", $v);
                    if($i != NULL)
                    {
                        array_splice($this->m_hItems, $i, 1);
                    }
                }
            }
            $raw->{$this->m_sClass}->{$Slot} = $Item->id;
            array_push($this->m_hItems, $Item);
        } else {
            $idx = $raw->{$this->m_sClass}->{$Slot};
            $i = array_ksearch($this->m_hItems, "id", $idx);
            if($i != NULL)
            {
                array_splice($this->m_hItems, $i, 1);
            }

            $raw->{$this->m_sClass}->{$Slot} = 0;
        }

        $this->getOwner()->setLoadout($raw);

        $sClass = $this->m_sClass;
        if($sClass == "demo") $this->m_sClass = "demoman";

        $this->getOwner()->queryServerJob(format(
            "ce_resetloadout %s %s",
            [
                $this->getOwner()->steamid,
                $contract
            ]
        ));
    }

    function canEquipItem($Item, $Slot)
    {
        if($Item->slot >= $this->getOwner()->getMaxBackpackSlots()) return false;
        if($this->getSlot($Slot)->id == $Item->id) return true;

        switch ($Item->def->type)
        {
            case 'cosmetic':

                if(isset($Item->def->slot))
                {
                    // We may change the slot of an item to a specific one.
                    // For example, "Action" slot items.
                    if($Slot != $Item->def->slot) return false;
                } else {
                    // Cosmetics can only be equipped in Wearables slots.
                    if(!in_array($Slot, SLOTGROUP_COSMETICS)) return false;
                }
                // Cosmetics can be restricted to classes.
                if(($Item->def->used_by_classes[$this->m_sClass] ?? NULL) != 1) return false;
                break;

            case 'action':

                // Cosmetics can only be equipped in Wearables slots.
                if(!in_array($Slot, SLOTGROUP_ACTION)) return false;
                // Cosmetics can be restricted to classes.
                if(($Item->def->used_by_classes[$this->m_sClass] ?? NULL) != 1) return false;
                break;

            case 'weapon':

                // Weapons can only be equipped in Weapons slots.
                if(!in_array($Slot, SLOTGROUP_WEAPONS)) return false;

                // Weapons can be restricted to classes and slots.
                if($Slot == SLOT_PRIMARY    && ($Item->def->used_by_classes[$this->m_sClass] ?? -1) != SLOT_PRIMARY_INDEX) return false;
                if($Slot == SLOT_SECONDARY  && ($Item->def->used_by_classes[$this->m_sClass] ?? -1) != SLOT_SECONDARY_INDEX) return false;
                if($Slot == SLOT_MELEE      && ($Item->def->used_by_classes[$this->m_sClass] ?? -1) != SLOT_MELEE_INDEX) return false;
                if($Slot == SLOT_PDA        && ($Item->def->used_by_classes[$this->m_sClass] ?? -1) != SLOT_PDA_INDEX) return false;
                break;

            case 'taunt':

                // Taunts can only be equipped in Wearables slots.
                if(!in_array($Slot, SLOTGROUP_TAUNTS)) return false;

                // Taunts can be restricted to classes and slots.
                if(($Item->def->used_by_classes[$this->m_sClass] ?? NULL) != 1) return false;
                break;

            default:
                // All other types cannot be equipped.
                return false;
                break;
        }
        return true;
    }

    function canConflictItem($NewItem, $Slot)
    {
        foreach ($this->m_hItems as $Item) {
            if($this->getItemSlot($Item) == $Slot) continue;

            if(count(array_intersect($NewItem->def->equip_region, $Item->def->equip_region)) > 0)
                return true;
        }
        return false;
    }

    function getItemSlot($Item)
    {
        foreach ($this->getOwner()->loadout->{$this->m_sClass} as $k => $v){
            if($v == $Item->id){
                return $k;
            }
        }
        return NULL;
    }

    function getSlot($slot)
    {
        foreach ($this->getOwner()->loadout as $Class => $Slots) {
            if($Class != $this->m_sClass) continue;

            foreach ($Slots as $Slot => $Index) {
                if($Slot != $slot) continue;

                $i = array_ksearch($this->m_hItems, "id", $Index);
                if($i !== NULL) {
                    return $this->m_hItems[$i];
                }
            }
        }
        return NULL;
    }

    function getPreviewData($wep = null)
    {
        $Weapon = NULL;
        if(isset($wep)) $Weapon = $wep;
        else $Weapon = $this->getSlot(SLOT_PRIMARY);

        $Return = [
            "weapononly" => false,
            "cosmeticonly" => false,
            "cosmetics" => [],
            "cosmetictints" => []
        ];

        switch ($this->m_sClass)
        {
            case 'scout':       $Return["class_id"] = 0; break;
            case 'soldier':     $Return["class_id"] = 1; break;
            case 'pyro':        $Return["class_id"] = 2; break;
            case 'demo':        $Return["class_id"] = 3; break;
            case 'heavy':       $Return["class_id"] = 4; break;
            case 'engineer':    $Return["class_id"] = 5; break;
            case 'medic':       $Return["class_id"] = 6; break;
            case 'sniper':      $Return["class_id"] = 7; break;
            case 'spy':         $Return["class_id"] = 8; break;
        }

        foreach ($this->m_hItems as $Item)
        {
            if($Item->def->type == "cosmetic")
            {
                if(($Item->def->preview["per_class"] ?? false) == true)
                {
                    array_push($Return["cosmetics"], $Item->def->preview[ucfirst($this->m_sClass)]);
                }else{
                    array_push($Return["cosmetics"], $Item->def->preview);
                }
                array_push($Return["cosmetictints"], dechex($Item->getAttributeByName("set item tint RGB")));
            }
            if(isset($Weapon) && $Item->def->type == "weapon")
            {
                if($Item->id == $Weapon->id)
                {
                    if(($Item->def->preview["per_class"] ?? false) == true)
                    {
                        $Return["weapon"] = $Item->def->preview[$this->m_sClass];
                    }else{
                        $Return["weapon"] = $Item->def->preview;
                    }
                }
            }
        }
        return $Return;
    }
}

?>
