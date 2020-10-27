<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class NotificationLootboxLoot extends NotificationEconItem
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function getLootboxDefinition()
    {
        return $this->m_hCore->items->getItemConfigByDefIndex($this->getValue("lootbox_def_index"));
    }
}

?>
