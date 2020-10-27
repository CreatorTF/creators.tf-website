<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Page extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->name = $data["name"];
        $this->surname = $data["surname"];
        $this->title = $data["title"];
        $this->content = $data["content"];
        $this->fullscreen = $data["fullscreen"] == 1?true:false;
        $this->include = $data["include"];
        $this->redirect = $data["redirect"];
        $this->right = $data["noright"] == 0 ?true:false;
    }
}
?>
