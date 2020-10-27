<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ApiKey extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->key = $data["apikey"];
        $this->owner = $data["owner"];
        $this->special = $data["special"];
        $this->created = $data["created"];
    }

    public function getOwner()
    {
        $u = $this->m_hCore->users->find('id', $this->owner);
        if(isset($u)) $u->key = $this;
        return $u;
    }

    function hasPermission($bit)
    {
        if(!is_integer($bit)) throw new TypeError("Argument #1 must be an int.");
        return ($this->special & $bit) == $bit;
    }
}
?>
