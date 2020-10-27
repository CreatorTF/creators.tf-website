<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class StyleData extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hItem = $data["item"];
        $this->m_nStyle = $data["style"];
    }

    function getItem()
    {
        return $this->m_hItem;
    }

    function getStyleData()
    {
        if(!isset($this->getItem()->def->visuals)) return NULL;
        else return $this->getItem()->def->visuals["styles"][$this->m_nStyle] ?? NULL;
    }

    function getParamIfExists($param)
    {
        $Data = $this->getStyleData();
        if($Data == NULL)
        {
            return NULL;
        } else {
            return $Data[$param] ?? NULL;
        }
    }

    function getImage()
    {
        return $this->getParamIfExists("image");
    }

    function getName()
    {
        return $this->getParamIfExists("name");
    }
}

?>
