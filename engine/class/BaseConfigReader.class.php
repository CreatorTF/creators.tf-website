<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class BaseConfigReader extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hConfig = $data["config"];
    }

    function getValue($name)
    {
        return $this->m_hConfig[$name] ?? NULL;
    }
}

?>
