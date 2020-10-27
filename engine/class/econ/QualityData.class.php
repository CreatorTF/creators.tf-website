<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class QualityData extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hConfig = $data["config"];
    }

    function getName()
    {
        return $this->getValue("name");
    }

    function getPrefix()
    {
        return $this->getValue("prefix");
    }

    function useProperName()
    {
        return ($this->getValue("propername") ?? false) == true;
    }

    function getColor()
    {
        return $this->getValue("color");
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }
}

?>
