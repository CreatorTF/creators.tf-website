<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class CodeError extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->title = $data["title"];
        $this->content = $data["content"];
        $this->code = $data["code"];
        $this->http = $data["http_code"] ?? 404;
        $this->type = $data["type"];
    }
}

?>
