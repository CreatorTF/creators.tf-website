<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ApikeyCollection extends BaseCollection
{
    function __construct($core){
        parent::__construct($core);
        $this->__table = "tf_apikeys";
        $this->__object = "ApiKey";
    }
}
?>
