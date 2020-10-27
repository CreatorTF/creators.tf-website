<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class NotificationEconItem extends Notification
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function getEconItem()
    {
        return $this->getOwner()->getItemByItemIndex($this->getValue("item_index"));
    }
}

?>
