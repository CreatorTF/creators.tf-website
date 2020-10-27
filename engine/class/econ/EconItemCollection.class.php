<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class EconItemCollection extends BaseConfigReader
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function getEconItemDefinitions()
    {
        $Items = [];
        foreach ($this->m_hConfig as $sItem)
        {
            $hDef = $this->m_hCore->items->findItemDefinition($sItem);
            if(isset($hDef)) array_push($Items, $hDef);
        }
        return $Items;
    }
}

?>
