<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class NotificationCollection extends BaseCollection
{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_notifications";
        $this->__object = "Notification";
    }

    function create($steamid, $type, $content)
    {
        $this->m_hCore->dbh->query("INSERT INTO tf_notifications (steamid, type, content) VALUES (%s, %s, %s)", [
            $steamid,
            $type,
            json_encode($content)
        ]);
    }
}
?>
