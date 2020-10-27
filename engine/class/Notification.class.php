<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Notification extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_sSteamID = $data["steamid"];
        $this->m_sType = $data["type"];
        $this->m_hContent = json_decode($data["content"], true) ?? [];
    }

    function getValue($name)
    {
        return $this->m_hContent[$name] ?? NULL;
    }

    function getType()
    {
        return $this->m_sType;
    }

    function getOwner()
    {
        return $this->m_hCore->users->find("steamid", $this->m_sSteamID);
    }
}

?>
