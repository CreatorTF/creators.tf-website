<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class NotificationCurrency extends Notification
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function getAmount()
    {
        return $this->getValue("amount");
    }
}

?>
