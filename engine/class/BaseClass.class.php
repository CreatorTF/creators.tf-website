<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class BaseClass
{
    function __construct($data, $core)
    {
        $this->m_hCore = $core;
        $this->m_hData = $data;
    }

    function getCore()
    {
        return $this->m_hCore;
    }

    function castAs($classname)
    {
        return new $classname($this->m_hData, $this->m_hCore);
    }

    function cast_as($classname)
    {
        return $this->castAs($classname);
    }
}

?>
