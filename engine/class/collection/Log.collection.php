<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class LogCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_logs";
        $this->__object = "Log";
    }

    function create($steamid, $type, $data)
    {
        $this->m_hCore->dbh->query("INSERT INTO tf_logs (steamid, `time`, type, data) VALUES (%s, NOW(), %s, %s)", [
            $steamid,
            $type,
            json_encode($data)
        ]);
    }
}
?>
