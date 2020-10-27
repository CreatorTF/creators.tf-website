<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ItemDefinition extends BaseConfigReader
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_iIndex = $data["defid"];
    }

    function toDOM($tags = [], $brackets = [], $quality = Q_UNIQUE)
    {
        return $this->m_hCore->items->toDOMfromDefID(
            $this->m_iIndex,
            $quality
        );
    }
}

?>
