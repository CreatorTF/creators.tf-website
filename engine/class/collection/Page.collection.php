<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class PageCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_pages";
        $this->__object = "Page";
    }
}
?>
