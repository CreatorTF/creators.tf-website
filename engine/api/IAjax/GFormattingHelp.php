<?php
    define("INCLUDED", true);
    require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
    $method = $_SERVER['REQUEST_METHOD'];

    if($method == 'GET')
    {
        $param = check($_REQ, ["spreadsheet"]);
        if(isset($param))
            ThrowAPIError(5, $param);

        if(!in_array($_REQ["spreadsheet"], ["comment"]))
            ThrowAPIError(5, "spreadsheet");

        switch ($_REQ["spreadsheet"]) {
            case 'comment': ThrowResult(["ajax"=>render("ajax/formatting_help_comments")]); break;
        }
    }
?>
