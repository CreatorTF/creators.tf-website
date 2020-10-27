<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ErrorCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_error";
        $this->__object = "CodeError";
    }
}
?>
