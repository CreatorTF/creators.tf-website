<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "short"){
    $Link = $Core->db->getRow(format("SELECT redirect FROM tf_short WHERE link = '%s'",[$_GET["link"]]))["redirect"];
    if(isset($Link))
    {
        die(header("Location: ".$Link));
    }else{
        $Core->error = ERROR_NOT_FOUND;
    }
}
?>
