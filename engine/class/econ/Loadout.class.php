<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Loadout extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hItems = $data["items"];
        $this->m_hOwner = $data["owner"];
    }

    function getItems()
    {
        return $this->m_hItems;
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getItemByID($id)
    {
        foreach ($this->getItems() as $Item) {
            if($Item->id == $id) return $Item;
        }
        return NULL;
    }

    function getClass($class)
    {
        $Items = [];
        foreach ($this->getOwner()->loadout as $Class => $Slots) {
            if($Class != $class) continue;
            foreach ($Slots as $Slot => $Index) {
                $i = array_ksearch($this->m_hItems, "id", $Index);
                if($i !== NULL) {
                    array_push($Items, $this->m_hItems[$i]);
                }
            }
        }

        return new LoadoutClass([
            "owner" => $this->getOwner(),
            "class" => $class,
            "loadout" => $this,
            "items" => $Items
        ], $this->m_hCore);
    }
}

?>
