<?php
if(!defined("INCLUDED")) die("Access forbidden.");

  if(isset($Core->User))
  $Content = render('page', [
    'title' => $_DATA["page_name"],
    'content' => render('pages/items', ["alias" => $Core->User->alias ?? $Core->User->steamid])
  ]);
  else $Core->error = ERROR_NO_PERMISSION;
?>
