<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Log extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_iID = $data["id"];
        $this->m_sTime = $data["time"];
        $this->m_sType = $data["type"];
        $this->m_sSteamID = $data["steamid"];

        $this->m_hDataValues = json_decode($data["data"], false);
        if(!isset($this->m_hDataValues)) $this->m_hDataValues = [];
    }
}

?>
