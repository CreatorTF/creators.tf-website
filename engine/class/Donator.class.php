<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Donator extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_sIndex = $data["pledge_id"];
        $this->m_sChargeTime = $data["charge_time"];
        $this->m_iCentsAmount = $data["cents_amount"];
        $this->m_sSource = $data["source"];
        $this->m_sChargerID = $data["source"];

        $this->m_hUser = $data["user"];
    }

    function getOwner()
    {
        return $this->m_hUser;
    }
}
?>
