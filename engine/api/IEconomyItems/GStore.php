<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];
if($method == 'GET')
{
    if($_REQ["get"] == "listings")
    {
        $_RETURN = [];
        foreach ($Core->Economy["Store"] as $Listing => $value) {
            $_PRICE = is_array($value) ? $value["price"] : $value;
            $_isNEW = is_array($value) ? ($value["new"] ?? false) === true : false;
            $_DISCOUNT = is_array($value) ? ($value["discount"] ?? 1) : 1;
            $_FEATURE = is_array($value) ? ($value["feature"] ?? false) === true : false;

            if(isset($Core->Economy["Items"][$Listing]))
                $iDefID = $Listing;
            else $iDefID = array_ksearch($Core->Economy["Items"], "name", $Listing);

            if(isset($iDefID))
            {
                array_push($_RETURN, [
                    "id" => $iDefID,
                    "classes" => array_keys($Core->Economy["Items"][$iDefID]["used_by_classes"] ?? []),
                    "name" => $Core->Economy["Items"][$iDefID]["name"] ?? NULL,
                    "price" => $_PRICE,
                    "image" => $Core->Economy["Items"][$iDefID]["image"] ?? NULL,
                    "description" => $Core->Economy["Items"][$iDefID]["description"] ?? NULL,
                    "attributes" => $Core->Economy["Items"][$iDefID]["attributes"] ?? [],
                    "type" => $Core->Economy["Items"][$iDefID]["type"] ?? NULL,
                    "feature" => $_FEATURE ?? NULL,
                    "html" => render("prefabs/items/listing", [
                        'price' => $_PRICE * (+$_DISCOUNT),
                        'discount' => ($_DISCOUNT > 1 ? "+" : NULL).(-100 + (+$_DISCOUNT) * 100),
                        "item" => $Core->items->toDOMFromDefID($iDefID, Q_UNIQUE, [
                            'price' => $_PRICE,
                            'balance' => $Core->User->credit,
                            'image_size' => '90px 90px'
                        ], [
                            'PURCHASE' => true
                        ])
                    ], [
                        'NEW' => $_isNEW && $_DISCOUNT == 1,
                        'DISCOUNT' => $_DISCOUNT != 1,
                    ])
                ]);
            }
        }
        ThrowResult(["listings" => $_RETURN]);
    }else if ($_GET["get"] == "checkout")
    {
        $_RETURN = [];

        $IDs = explode(",", $_REQ["cart"]);
        $Listings = [];
        foreach ($IDs as $id) {
            if(!isset($Listings[$id])) $Listings[$id] = 0;
            $Listings[$id]++;
        }
        $_RETURN = array_map(function($a, $b) {
            global $Core;

            $price = $Core->Economy["Store"][$Core->Economy["Items"][$a]["name"] ?? $a];
            if(!isset($price)) return;
            if(is_array($price)) $price = $price["price"];
            $price *= $b;

            return render("prefabs/items/checkout_listing", [
                'image' => $Core->Economy["Items"][$a]["image"],
                'name' => $Core->Economy["Items"][$a]["name"],
                'count' => $b,
                'price' => $price
            ]);
        }, array_keys($Listings), $Listings);

        ThrowResult(["cart" => $_RETURN]);
    }
}else if($method == 'POST')
{
    if(!CheckPermission_CanWrite())
        ThrowAPIError(403);

    $param = check($_REQ,['cart'], true);
    if(isset($param))
        ThrowAPIError(ERROR_API_INVALIDPARAM,$param);

    $IDS = explode(",", $_REQ["cart"]);
    $COST = 0;

    if(!$Core->User->canGetMoreItems())
        ThrowCustomAPIError(403, "Your backpack is full. This order has been declined.");

    if(count($IDS) > $Core->config->economy->max_checkout_items)
        ThrowCustomAPIError(403, "You can't purchase more than 50 items at once.");

    foreach ($IDS as $ID) {
        if(!is_numeric($ID)) {
            ThrowCustomAPIError(400, "Invalid Store item provided.");
            break;
        }
        if(!isset($Core->Economy["Items"][$ID]))
        {
            ThrowCustomAPIError(400, "Invalid Store item provided.");
            break;
        }
        $_PRICE = -1;
        $_NAME = $Core->Economy["Items"][$ID]["name"];

        $Listing = $Core->Economy["Store"][$ID] ?? $Core->Economy["Store"][$_NAME];

        if(is_array($Listing))
        {
            $_PRICE = $Listing["price"];
        } else $_PRICE = $Listing;

        if($_PRICE == NULL)
            ThrowCustomAPIError(400, "Couldn't find the price value of a listing.");
        $COST += $_PRICE;
    }

    if($Core->User->credit < $COST)
        ThrowCustomAPIError(403, "You don't have enough currency to purchase this.");

    $Items = $Core->items->createMultiple($Core->User->steamid, array_map(function($a){ return ["id" => $a]; }, $IDS), PREVIEW_MESSAGE_PURCHASED);
    if(isset($Items))
    {
        $Core->User->chargeCurrency($COST);
        ThrowResult(["cost" => $COST]);
    } else {
        ThrowCustomAPIError(500, "Unexpected server error.");
    }

}
?>
