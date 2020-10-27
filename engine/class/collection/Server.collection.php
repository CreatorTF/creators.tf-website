<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ServerCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_servers";
        $this->__object = "Server";
    }
}
?>
