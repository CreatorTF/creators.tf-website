<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if(isset($Core->User))
$Content = render(
    'pages/contracker',
    [
        'username' => $Core->User->name,
        'avatar' => $Core->User->avatar,
        'balance' => $Core->User->credit,

        'pages' => join("", array_map(function($Page){
            global $Core;

            return $Page->toDOM([],[
                "VISIBLE" => $Core->User->getContracker()->getDefaultPage()->getTitle() == $Page->getTitle()
            ]);
        }, $Core->User->getContracker()->getPages()))
    ],
    [
        'LOGIN' => isset($Core->User),
        'NOLOGIN' => !isset($Core->User)
    ]
);
else $Core->error = ERROR_NO_PERMISSION;
?>
