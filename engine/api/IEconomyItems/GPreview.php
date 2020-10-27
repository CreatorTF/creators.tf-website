<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
define("NOTIFICATION_NEW_ITEM_TYPE", "new_item");
define("NOTIFICATION_ITEM_UPGRADED_TYPE", "item_upgraded");
define("NOTIFICATION_CURRENCY_TYPE", "currency_reward");
define("NOTIFICATION_LOOTBOX_LOOT", "lootbox_loot");

define("PREVIEW_LOOTBOX_TEMPLATE", "lootbox");

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'GET')
{
    if(!CheckPermission_CanRead()) ThrowAPIError(403);

    $param = check($_REQ,['show']);
    if(isset($param)) ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

    if($_REQ["show"] == "new_items")
    {
        $hPreviews = [];

        $iCount = 0;
        $iCurrency = 0;
        $hCoinsNotifs = $Core->User->getNotificationsOfType(NOTIFICATION_CURRENCY_TYPE);
        foreach ($hCoinsNotifs as $Notif)
        {
            $Notif = $Notif->cast_as("NotificationCurrency");
            $iCurrency += $Notif->getAmount();
        }

        if($iCurrency > 0)
        {
            array_push($hPreviews, $Core->items->toDOMPreview([
                "item_number" => $iCount + 1,
                "name" => "$iCurrency Mann Coins",
                "image" => $Core->items->getCoinsImage($iCurrency),
                "description" => "Used to purchase items in Mann Co. Work-Shop.",
                "quality" => Q_UNIQUE,
                "quality_color" => $Core->items->getQualityData(Q_UNIQUE)->getColor()
            ], [
                "SHOW_ITEM_NUMBER" => true
            ], PREVIEW_MESSAGE_CURRENCY));
            $iCount++;
        }

        $hItemUpgradedNotifs = $Core->User->getNotificationsOfType(NOTIFICATION_ITEM_UPGRADED_TYPE);
        foreach ($hItemUpgradedNotifs as $Notif)
        {
            $Notif = $Notif->cast_as("NotificationEconItem");

            $Item = $Notif->getEconItem();
            if(!isset($Item)) continue;

            array_push($hPreviews, $Item->toDOMPreview([
                "item_number" => $iCount + 1
            ], [
                "SHOW_ITEM_NUMBER" => true
            ], PREVIEW_MESSAGE_ITEM_UPGRADED));
            $iCount++;
        }

        $hNewItemsNotifs = $Core->User->getNotificationsOfType(NOTIFICATION_NEW_ITEM_TYPE);
        foreach ($hNewItemsNotifs as $Notif)
        {
            $Notif = $Notif->cast_as("NotificationEconItem");

            $Item = $Notif->getEconItem();
            if(!isset($Item)) continue;

            array_push($hPreviews, $Item->toDOMPreview([
                "item_number" => $iCount + 1
            ], [
                "SHOW_ITEM_NUMBER" => true
            ], $Notif->getValue("origin")));
            $iCount++;
        }

        ThrowResult([
            "count" => count($hPreviews),
            "html" => count($hPreviews) > 0
                ? render("prefabs/preview_context/new_items", [
                    "new_items_count" => count($hPreviews),
                    'new_items_preview' => join("", $hPreviews)
                ])
                : NULL
        ]);

        $Core->User->purgeNotificationsOfType(NOTIFICATION_NEW_ITEM_TYPE);
        $Core->User->purgeNotificationsOfType(NOTIFICATION_ITEM_UPGRADED_TYPE);
        $Core->User->purgeNotificationsOfType(NOTIFICATION_CURRENCY_TYPE);
        die();
    }
    if($_REQ["show"] == "lootbox_loot")
    {
        $hPreviews = [];

        $iCount = 0;
        $hLootNotifs = $Core->User->getNotificationsOfType(NOTIFICATION_LOOTBOX_LOOT);
        $hBroadcastItems = [];

        foreach ($hLootNotifs as $Notif)
        {
            $Notif = $Notif->cast_as("NotificationLootboxLoot");

            $Item = $Notif->getEconItem();
            if(!isset($Item)) continue;
            $Lootbox = $Notif->getLootboxDefinition();

            array_push($hPreviews, $Item->toDOMPreview([
                    "item_number" => $iCount + 1,
                    "item_crate_image" => $Lootbox["image"] ?? NULL,
                    "attributes_html" => $Item->toDOMOverlayAttributes()
                ], [
                    "SHOW_ITEM_NUMBER" => true
                ],
                PREVIEW_MESSAGE_NULL,
                PREVIEW_LOOTBOX_TEMPLATE
            ));
            $iCount++;

            array_push($hBroadcastItems, $Item);
        }

        ThrowResult([
            "count" => count($hPreviews),
            "html" => count($hPreviews) > 0
                ? render("prefabs/preview_context/lootbox_loot", [
                    "new_items_count" => count($hPreviews),
                    'new_items_preview' => join("", $hPreviews)
                ])
                : NULL
        ]);

        foreach ($hBroadcastItems as $hItem)
        {
            $Core->User->queryServerJob(format(
                "ce_broadcast_announce %s #{team}{name}@1 has unboxed: %s%s",
                [
                    $Core->User->steamid,
                    $hItem->getQualityData()->getColor(),
                    $hItem->name
                ]
            ));
        }

        $Core->User->purgeNotificationsOfType(NOTIFICATION_LOOTBOX_LOOT);
        die();
    }
}
?>
