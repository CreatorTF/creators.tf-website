<?php
if(!defined("INCLUDED")) die("Access forbidden.");

    if(!isset($Core->User))
    {
        $Core->error = ERROR_NO_PERMISSION;
    }else{
        if(isset($_GET["switch_to"]))
        {
            $SwitchTo = $Core->users->findOR("steamid",$_GET["switch_to"],'alias',$_GET["switch_to"]);
            if(isset($SwitchTo))
            {
                if(in_array($Core->User->id,$SwitchTo->owners))
                {
                    setcookie("token",$SwitchTo->token,time()+60*60*24*30,"/");
                    Header('Location: /');
                }else{
                    $Core->error = ERROR_NO_PERMISSION;
                }
            }
        }else{
            $Content = render(
                'page',
                [
                    'title'=>'Account Switcher',
                    'content'=>'Switch your main account to another one, associated with your main account.'
                ]
            );
        }
    }
?>
