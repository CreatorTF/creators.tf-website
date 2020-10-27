<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class DepotCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_depots";
        $this->__object = "Depot";
    }
}
?>
