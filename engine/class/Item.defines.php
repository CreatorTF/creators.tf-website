<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("HOLIDAY_NONE", 0);
define("HOLIDAY_BIRTHDAY", 1);
define("HOLIDAY_HALLOWEEN", 2);
define("HOLIDAY_HALLOWEENORFULLMOON", 3);

define("SLOT_PRIMARY", "PRIMARY");
define("SLOT_PRIMARY_INDEX", 0);

define("SLOT_SECONDARY", "SECONDARY");
define("SLOT_SECONDARY_INDEX", 1);

define("SLOT_MELEE", "MELEE");
define("SLOT_MELEE_INDEX", 2);

define("SLOT_PDA", "PDA");
define("SLOT_PDA_INDEX", 3);

define("SLOTGROUP_WEAPONS", [SLOT_PRIMARY, SLOT_SECONDARY, SLOT_MELEE, SLOT_PDA]);

define("SLOT_WEAR_1", "WEAR_1");
define("SLOT_WEAR_2", "WEAR_2");
define("SLOT_WEAR_3", "WEAR_3");
define("SLOT_ACTION", "ACTION");

define("SLOTGROUP_COSMETICS", [SLOT_WEAR_1, SLOT_WEAR_2, SLOT_WEAR_3]);
define("SLOTGROUP_ACTION", [SLOT_ACTION]);

define("SLOT_TAUNT_1", "TAUNT_1");
define("SLOT_TAUNT_2", "TAUNT_2");
define("SLOT_TAUNT_3", "TAUNT_3");
define("SLOT_TAUNT_4", "TAUNT_4");
define("SLOT_TAUNT_5", "TAUNT_5");
define("SLOT_TAUNT_6", "TAUNT_6");
define("SLOT_TAUNT_7", "TAUNT_7");
define("SLOT_TAUNT_8", "TAUNT_8");

define("SLOTGROUP_TAUNTS", [SLOT_TAUNT_1, SLOT_TAUNT_2, SLOT_TAUNT_3, SLOT_TAUNT_4,
                            SLOT_TAUNT_5, SLOT_TAUNT_6, SLOT_TAUNT_7, SLOT_TAUNT_8]);

define("PREVIEW_MESSAGE_NULL", -1);
define("PREVIEW_MESSAGE_FOUND", 0);
define("PREVIEW_MESSAGE_PURCHASED", 1);
define("PREVIEW_MESSAGE_REWARD", 2);
define("PREVIEW_MESSAGE_DISTRIBUTED", 3);
define("PREVIEW_MESSAGE_ITEM_UPGRADED", 4);
define("PREVIEW_MESSAGE_CURRENCY", 5);
define("PREVIEW_MESSAGE_QUEST_LOANER", 6);

define("COINS_IMAGE_SMALL", "{CDN}/assets/images/inventory/items/tools/item_tool_manncoin_small.png");
define("COINS_MAX_AMOUNT_SMALL", 250);

define("COINS_IMAGE_MEDIUM", "{CDN}/assets/images/inventory/items/tools/item_tool_manncoin_mid.png");
define("COINS_MAX_AMOUNT_MEDIUM", 500);

define("COINS_IMAGE_BIG", "{CDN}/assets/images/inventory/items/tools/item_tool_manncoin_big.png");

?>
