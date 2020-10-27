<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
    $_OFFSET = $_REQ["offset"] ?? 0;
    $_OFFSET = (+$_OFFSET);
    if(!is_numeric($_OFFSET))
        ThrowAPIError(ERROR_UNEXPECTED);

    if($_OFFSET < 0) $_OFFSET = 0;
    $_MONTH = +date("n");
    $_YEAR = +date("Y");

    $_MONTH -= $_OFFSET;
    while($_MONTH <= 0)
    {
        $_MONTH += 12;
        $_YEAR -= 1;
    }

    $_TOTAL = (+$Core->dbh->getRow("SELECT sum(cents_amount) as total FROM tf_pledges WHERE MONTH(charge_time) = %s AND YEAR(charge_time) = %s",[$_MONTH, $_YEAR]) ["total"]);

    $People = $Core->users->getDonators($_MONTH, $_YEAR);
    $_RESULT = [];

    if(isset($_REQ["ajax"]))
    {
        $_RESULT = join(array_map(function($a){
            return $a->m_hUser->toDOMEmbed([
                "name" => addslashes(addslashes($a->m_hUser->name)),
                "classes" => "width-30-pc m-b-5 m-l-5 inline-block",
                "status" => "Donated <b>".($a->m_iCentsAmount/100)."$</b>"
            ]);
        }, $People), "");
    }else{
        $_RESULT = array_map(function($a){
            return [
                "avatar" => $a->m_hUser->avatar,
                "cents_amount" => $a->m_iCentsAmount,
                "charge_time" => $a->m_sChargeTime,
                "name" => $a->m_hUser->name,
                "steamid" => $a->m_hUser->steamid,
            ];
        }, $_RESULT);
    }

  if(count($People) > 0)
  {
    ThrowResult([
      "cursor" => [
        "time" => [
          "year" => $_YEAR,
          "month" => (+$_MONTH)
        ],
        "total" => $_TOTAL,
        "offset" => $_OFFSET
      ],
      'donors' => $_RESULT
    ]);
  }else{
    ThrowCustomAPIError(404, "No Donators were found for this period.");
  }
}else{
    http_response_code(404);
}
?>
