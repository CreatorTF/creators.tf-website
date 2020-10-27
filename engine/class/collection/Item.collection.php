<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("Q_NORMAL", 0);
define("Q_GENUINE", 1);
define("Q_VINTAGE", 3);
define("Q_UNUSUAL", 5);
define("Q_UNIQUE", 6);
define("Q_COMMUNITY", 7);
define("Q_VALVE", 8);
define("Q_SELFMADE", 9);
define("Q_STRANGE", 11);
define("Q_HAUNTED", 13);
define("Q_COLLECTORS", 14);

class ItemCollection extends BaseCollection
{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_pack";
        $this->__object = "Item";
    }

    function createMultiple($steamid, $array, $origin = PREVIEW_MESSAGE_NULL)
    {
        // Finding all occupied slots of this user.
        $_SLOTS = $this->m_hCore->dbh->getAllRows("SELECT slot FROM tf_pack WHERE steamid = %s", [$steamid], true);

        // Defaulting limit to default value of items in a backpack.
        $_LIMIT = $this->m_hCore->config->economy->max_pages;

        // Multiplying page number by items per page value.
        $_LIMIT *= $this->m_hCore->config->economy->items_per_page;

        // Making sure we can add all items.
        if($_LIMIT < (count($_SLOTS) + count($array)))
            return null;

        $rows = [];
        $params = [];
        $hashes = [];

        foreach ($array as $item)
        {
            // Finding next free slot.
            $_SLOT = -1;
            $_HASH = sha1(time().time().$steamid.$item["id"].($item["quality"] ?? Q_STRANGE).rand(0,100));

            for($i = 0; $i < $_LIMIT; $i++)
            {
                if(array_ksearch($_SLOTS, "slot", $i) === null)
                {
                    $_SLOT = $i;
                    break;
                }
            }
            if($_SLOT == -1) return null;
            array_push($_SLOTS, ["slot" => $_SLOT]);
            array_push($rows, "(%s,%d,%d,%s,%s,%d)");
            array_push($hashes, $_HASH);

            $params = array_merge($params, [
                $steamid,
                $item["id"],
                $item["quality"] ?? 6,
                count($item["attributes"] ?? []) > 0 ? json_encode($item["attributes"]) : NULL,
                $_HASH,
                $_SLOT
          ]);
        }
        $this->m_hCore->dbh->query("INSERT INTO tf_pack (steamid,defid,quality,attributes,hash,slot) VALUES ".join(", ", $rows), $params);

        $Clauses = join(",", array_fill(0, count($rows), "%s"));
        $hItems = array_map(function($a){
            return new Item($a, $this->m_hCore);
        }, $this->m_hCore->dbh->getAllRows("SELECT * FROM tf_pack WHERE hash IN ($Clauses)", $hashes));

        if($origin > PREVIEW_MESSAGE_NULL)
        {
            foreach ($hItems as $hItem)
            {
                $this->m_hCore->notifications->create($steamid, "new_item", ["item_index" => $hItem->id, "origin" => $origin]);
            }
        }

        return $hItems;
    }

    function create($steamid, $def, $quality = Q_UNIQUE, $attributes = [], $origin = PREVIEW_MESSAGE_NULL)
    {
        // Finding all occupied slots of this user.
        $_SLOTS = $this->m_hCore->dbh->getAllRows("SELECT slot FROM tf_pack WHERE steamid = %s", [$steamid], true);

        // Defaulting limit to default value of items in a backpack.
        $_SLOT = -1;
        $_LIMIT = $this->m_hCore->config->economy->max_pages;

        // Multiplying page number by items per page value.
        $_LIMIT *= $this->m_hCore->config->economy->items_per_page;

        // Finding next free slot.
        for($i = 0; $i < $_LIMIT; $i++)
        {
            if(array_ksearch($_SLOTS, "slot", $i) === null)
            {
                $_SLOT = $i;
                break;
            }
        }
        if($_SLOT == -1) return null;

        $hash = sha1(time().time().$steamid.$def.$quality.rand(0,100));
        $this->m_hCore->dbh->query(
            "INSERT INTO tf_pack (steamid,defid,quality,attributes,hash,slot) VALUES (%s,%d,%d,%s,%s,%d)",
            [
                $steamid,
                $def,
                $quality,
                count($attributes) > 0 ? json_encode($attributes) : NULL,
                $hash,
                $_SLOT
            ]
        );

        $hItem = $this->find('hash', $hash);

        if($origin > PREVIEW_MESSAGE_NULL)
        {
            $this->m_hCore->notifications->create($steamid, "new_item", ["item_index" => $hItem->id, "origin" => $origin]);;
        }

        return $hItem;
    }

    function toDOMSlotIndexed($item, $slot)
    {
        return isset($item)
            ? $item->toDOMSlot([
                "slot_url" => $this->m_hCore->config->economy->slots->{$slot}->url
            ])
            : $this->toDOMSlotfromDefID(-1, 0, [
                "slot_url" => $this->m_hCore->config->economy->slots->{$slot}->url,
                "name" => $this->m_hCore->config->economy->slots->{$slot}->name
            ]);
    }

    function toDOMSlot($tags = [], $brackets = [])
    {
        return render(
            "prefabs/items/slot",
            array_merge([
                "image" => "",
                "name" => "",
                "description" => "",
                "quality" => 0,
                'quality_color' => $this->m_hCore->Economy["Qualities"][0]["color"],
                "attributes_html" => "",
                "slot_url" => "",
                'icons' => "",
                'tool_target_image_size' => '50% 50%',
                'tool_target_image_position' => 'center',
                'tool_target_image' => '',
            ],$tags),
            array_merge([
                "SET" => false
            ],$brackets)
        );
    }

    function toDOM($tags = [], $brackets = [])
    {
        return render(
            "prefabs/items/item",
            array_merge([
                'name' => NULL,
                'image' => NULL,
                'type' => NULL,
                'quality' => 0,
                'quality_color' => NULL,
                'description' => NULL,
                "attributes_html" => NULL,
                'index' => 0,
                'icons' => NULL,
                'scrap' => 0,
                'classname' => NULL,
                'campaign' => NULL,
                'workshop_id' => NULL,
                'image_size' => '100% 100%',
                'dom_attributes' => NULL,

                'tool_target_image_size' => '50% 50%',
                'tool_target_image_position' => 'center',
                'tool_target_image' => NULL,

                'sound_drop' => NULL,
                'sound_pickup' => NULL,

                // [PURCHASE]
                'price' => 0,
                'balance' => 0,
                // [EQUIP]
                'class' => NULL,
                // [LINK]
                'link' => NULL
                // [SOUNDS]
            ], $tags),
            array_merge([
                'SCRAP' => false,
                'PURCHASE' => false,
                'INSPECT' => false,
                'EQUIP' =>  false,
                'OWNER' => false,
                'LINK' => false,
                'USE' => false,
                'CONTEXT' => false,

                'CAN_EQUIP' => false,
                'CAN_EQUIP_SCOUT' => false,
                'CAN_EQUIP_SOLDIER' => false,
                'CAN_EQUIP_PYRO' => false,
                'CAN_EQUIP_DEMOMAN' => false,
                'CAN_EQUIP_HEAVY' => false,
                'CAN_EQUIP_ENGINEER' => false,
                'CAN_EQUIP_MEDIC' => false,
                'CAN_EQUIP_SNIPER' => false,
                'CAN_EQUIP_SPY' => false,

                'CAN_USE' => false,
                'CAN_SCRAP' => false,
                'CAN_DELETE' => false,
                'CAN_ADD_CART' => false,
                'CAN_CAMPAIGN' => false,
                'CAN_INSPECT' => false
            ],$brackets)
        );
    }

    function toDOMPreview($tags = [], $brackets = [], $message = PREVIEW_MESSAGE_NULL, $template = "preview")
    {
        return render("prefabs/preview/$template",
            array_merge([
                "image" => "",
                "name" => "",
                "description" => "",
                "quality_color" => "",
                "attributes_html" => "",

                "item_number" => 0,

                'icons' => ""
            ], $tags),
            array_merge([
                "MESSAGE_FOUND" => $message == PREVIEW_MESSAGE_FOUND,
                "MESSAGE_REWARD" => $message == PREVIEW_MESSAGE_REWARD,
                "MESSAGE_PURCHASED" => $message == PREVIEW_MESSAGE_PURCHASED,
                "MESSAGE_DISTRIBUTED" => $message == PREVIEW_MESSAGE_DISTRIBUTED,
                "MESSAGE_ITEM_UPGRADED" => $message == PREVIEW_MESSAGE_ITEM_UPGRADED,
                "MESSAGE_CURRENCY" => $message == PREVIEW_MESSAGE_CURRENCY,
                "MESSAGE_LOANER" => $message == PREVIEW_MESSAGE_QUEST_LOANER,

                "HAS_MESSAGE" => $message > PREVIEW_MESSAGE_NULL,

                "SHOW_ITEM_NUMBER" => false
            ], $brackets)
        );
    }

    function getCoinsImage($amount)
    {
        if($amount <= COINS_MAX_AMOUNT_SMALL) return COINS_IMAGE_SMALL;
        else if($amount <= COINS_MAX_AMOUNT_MEDIUM) return COINS_IMAGE_MEDIUM;
        else return COINS_IMAGE_BIG;
    }

    function toDOMfromDefID($id, $quality = 6, $tags = [], $brackets = [])
    {
        $Item = $this->m_hCore->Economy["Items"][$id] ?? NULL;
        if(!isset($Item)) return;

        return $this->toDOM(
            array_merge([
                'name' => $Item["name"] ?? "",
                'image' => $Item["image"] ?? ($Item["visuals"][0]["image"] ?? NULL),
                'type' => $Item["type"] ?? "",
                'description' => $Item["description"] ?? "",
                'quality' => $quality,
                'quality_color' => $this->m_hCore->Economy["Qualities"][$quality]["color"] ?? "",
                "attributes_html" => $this->toDOMAttributes($Item["attributes"] ?? [])
            ], $tags),
            $brackets
        );
    }

    function toDOMSlotfromDefID($id, $quality = 6, $tags = [], $brackets = [])
    {
        $Item = $this->m_hCore->Economy["Items"][$id] ?? NULL;
        if(!isset($Item)) return;

        return $this->toDOMSlot(
            array_merge([
                'quality' => $quality,
                'quality_color' => $this->m_hCore->Economy["Qualities"][$quality]["color"] ?? "",
                'name' => $Item["name"] ?? "",
                'image' => $Item["image"] ?? ($Item["visuals"][0]["image"] ?? NULL),
                'description' => $Item["description"] ?? "",
                "attributes_html" => $this->toDOMAttributes($Item["attributes"] ?? [])
            ], $tags),
            $brackets
        );
    }

    /**
    * Purpose:  Merges two arrays of attributes into one.
    */
    function mergeAttributes($attrs1, $attrs2)
    {
        $return = [];
        foreach ($attrs2 as $i => $attr2) {
            $attr2 = (object) $attr2;
            foreach ($attrs1 as $j => $attr1) {
                $attr1 = (object) $attr1;
                if($attr1->name == $attr2->name)
                {
                    array_splice($attrs1, $j, 1);
                }
            }
        }
        return array_merge($attrs2, $attrs1);
    }

    /**
    * Purpose:  Prepares and compiles attribute values and names into an array of strings.
    */
    function parseAttributes($Attributes, $Item = null)
    {
        $return = [];
        foreach ($Attributes as $Attribute)
        {
            $Attribute = (object) $Attribute;
            $AttributeDef = (object) $this->m_hCore->Economy["Attributes"][$Attribute->name];
            if(!isset($AttributeDef)) continue;

            // If attribute is marked as "hidden" we're skiping it since it is not
            // supposed to be shown.
            if(($AttributeDef->hidden ?? false) == true) continue;

            // Defining the color of the attribute.
            // -1 is negative, 1 is positive, 2 is dark gray, 0 is neutral white.
            // Other values are parsed as HEX values.
            switch ($AttributeDef->color ?? NULL) {
                case "-1":
                    $AttributeColor = "#d83636";
                    break;
                case "1":
                    $AttributeColor = "#7ea9d1";
                    break;
                case "2":
                    $AttributeColor = "#626262";
                    break;
                case "0":
                    $AttributeColor = "inherit";
                    break;
                default:
                    $AttributeColor = "#".($AttributeDef->color ?? NULL);
                    break;
            }

            // Parsing the way how value is supposed to be shown.
            // $AttributeValue contains transformed value, *NOT* full attrubute string.
            switch ($AttributeDef->display_as ?? "") {
                case 'percentage':
                    // Value will be treated as super percentage.
                    // I.e. 0.5 will be transformed into -50%.
                    //      1.5 will be transformed into 50%.
                    $AttributeValue = ((float) $Attribute->value) * 100 - 100;
                    break;
                case 'inverted_percentage':
                    // Value will be treated as sub percentage.
                    // I.e. 0.5 will be transformed into 50%.
                    //      1.5 will be transformed into -50%.
                    $AttributeValue = 100 - ((float) $Attribute->value) * 100;
                    break;
                case 'name_string':
                    // Value will be treated as name_string. We're searching this exact value in the namestrings.json
                    // and replacing it with found value.
                    // Keep in mind that in this case "name_string" needs to be set in the attribute config.

                    // I.e. "3100495" will be replaced with "A Color Similar to Slate".
                    // * Only if "name_string" key is set to "paintcan_names".
                    if(isset($this->m_hCore->Economy["NameStrings"][$AttributeDef->name_string][$Attribute->value]))
                        $AttributeValue = format($this->m_hCore->Economy["NameStrings"][$AttributeDef->name_string][$Attribute->value], [$AttributeValue ?? NULL]);
                    break;
                case 'strange_part_name':
                    // Value will be treated as a Strange Part name.
                    // I.e. 1 will be "Kills".
                    $AttributeValue = $this->m_hCore->Economy["Stranges"]["StrangeParts"][$Attribute->value]["name"] ?? $Attribute->value;
                    break;
                case 'date':
                    // Value will be treated as a date.
                    // I.e. 1 will be transformed into "Jan 1, 1970 00:00".
                    $AttributeValue = strftime("%B %d, %Y %H:%M", $Attribute->value);
                    break;
                case 'item_name':
                    // Value will be treated as a item name.
                    // I.e. 1 will be transformed into "Self-Made Smissmas Keyless Cosmetic Crate".
                    $AttributeValue =
                        (($this->m_hCore->Economy["Items"][$Attribute->value]["propername"] ?? 0) == 1 ? "The " : NULL).
                        ($this->m_hCore->Economy["Items"][$Attribute->value]["name"] ?? $Attribute->value);
                    break;
                default:
                    // And by default, value will be shown as it is.
                    $AttributeValue = $Attribute->value;
                    break;
            }
            // Compiling value into attribute name.
            $AttributeName = format(($AttributeDef->value ?? NULL), [$AttributeValue]);

            // If Item is provided we may also inject value from another attribute, that this one is defined to include.
            // In this case, the value of another attribute will replace "%v" mark.
            // NOTE: Keep in mind that this only works if $Item is provided.
            if(isset($AttributeDef->include_value_from))
                $AttributeName = format($AttributeName, [isset($Item) ? $Item->getAttributeByName($AttributeDef->include_value_from) : 0], 'v');

            // And we push the color, name and the weigth into the result array.
            array_push($return, [
                'color' => $AttributeColor,
                'value' => $AttributeName,
                'weight' => $AttributeDef->weight ?? array_search(($AttributeDef->color ?? NULL), [-1,1])
            ]);
        }
        // Sorting attributes by their weight.
        usort($return, function($a, $b){
            if($a['weight'] == $b['weight']) return 0;
            return ($a['weight'] > $b['weight']) ? -1 : 1;
        });

        $return = array_map(function($a) {
            return [
                'color' => $a['color'],
                'value' => $a['value']
            ];
        }, $return);
        return $return;
    }

    function toDOMAttributes($Attributes, $Item = null)
    {
        $result = $this->parseAttributes($Attributes, $Item);
        $result = array_map(function($a){
            return render("prefabs/items/attribute",
            [
                "color" => $a["color"],
                "name" => $a["value"]
            ]);
        }, $result);
        return join("",$result);
    }

    function getRandomCollectionItemIndex($name)
    {
        if($this->m_hCore->Economy["item_collections"][$name] == NULL) return NULL;
        $Pool = $this->m_hCore->Economy["item_collections"][$name];
        $idx = $Pool[array_rand($Pool)];

        if(is_array($this->m_hCore->Economy["Items"][$idx] ?? NULL)) return $idx;

        $idx = array_ksearch($this->m_hCore->Economy["Items"], "name", $idx);
        return $idx;
    }

    function getRandomUnusualGroupParticle($name)
    {
        if($this->m_hCore->Economy["unusual_groups"][$name] == NULL) return NULL;
        $Pool = $this->m_hCore->Economy["unusual_groups"][$name];
        $idx = $Pool[array_rand($Pool)];
        return $idx;
    }

    function getStrangeEaterFromType($type)
    {
        switch ($type) {
            case "cosmetic": return SP_COSMETIC; break;
            case "weapon": return SP_WEAPON; break;
            default:
                // In all other item types, the application of strange quality
                // is probably bugged, so if this gets fired - we will get
                // a bugged item.
                break;
        }
        return NULL;
    }

    function getStrangeLevelData($data)
    {
        return $this->m_hCore->Economy["Stranges"]["LevelData"][$data] ?? NULL;
    }

    function getDefaultStrangeLevelDataForType($type)
    {
        return array_ksearch($this->m_hCore->Economy["Stranges"]["LevelData"] ?? [], "default_for_type", $type);
    }

    function getQualityPrefix($quality)
    {
        return $this->m_hCore->Economy["Qualities"][$quality]["prefix"] ?? NULL;
    }

    function canQualityUseProperName($quality)
    {
        return ($this->m_hCore->Economy["Qualities"][$quality]["propername"] ?? 0) == 1;
    }

    function getItemConfigByDefIndex($id)
    {
        return $this->m_hCore->Economy["Items"][$id] ?? NULL;
    }

    function getItemDefinitionByDefIndex($id)
    {
        if($this->m_hCore->Economy["Items"][$id] === NULL) return NULL;
        return new ItemDefinition([
            "config" => $this->m_hCore->Economy["Items"][$id],
            "defid" => $id
        ], $this->m_hCore);
    }

    function getItemIndexByName($name)
    {
        return array_ksearch($this->m_hCore->Economy["Items"], "name", $name);
    }

    function findItemDefinition($sSearch)
    {
        $hDef = $this->getItemDefinitionByDefIndex($sSearch);
        if(isset($hDef)) return $hDef;

        $idx = $this->getItemIndexByName($sSearch);
        return $this->getItemDefinitionByDefIndex($idx);
    }

    function getQualityData($quality)
    {
        if($this->m_hCore->Economy["Qualities"][$quality] === NULL) return NULL;
        return new QualityData([
            "config" => $this->m_hCore->Economy["Qualities"][$quality]
        ], $this->m_hCore);
    }

    function getCollectionByName($sName)
    {
        if(($this->m_hCore->Economy["item_collections"][$sName] ?? NULL) === NULL) return NULL;
        return new EconItemCollection([
            "config" => $this->m_hCore->Economy["item_collections"][$sName]
        ], $this->m_hCore);
    }
}
?>
