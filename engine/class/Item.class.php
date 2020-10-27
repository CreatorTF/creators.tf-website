<?php
if(!defined("INCLUDED")) die("Access forbidden.");

require_once $_SERVER['DOCUMENT_ROOT']."/engine/class/Item.defines.php";

class Item extends BaseClass
{
    public function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->id = (integer) $data["id"];
        $this->defid = (integer) $data["defid"];
        $this->quality = (integer) $data["quality"];
        $this->quality_color = $this->m_hCore->Economy["Qualities"][$this->quality ?? 0]["color"] ?? "";
        $this->steamid = $data["steamid"];
        $this->slot = (integer) $data["slot"];
        $this->actions = [];
        $this->def = (object)($this->m_hCore->Economy["Items"][$this->defid] ?? NULL);
        $this->style = 0;

        // Prepare attributes. Merge prefab with overlay attributes.
        $this->def->attributes = $this->def->attributes ?? [];
        $this->_attributes = isset($data["attributes"])
            ? json_decode($data["attributes"],true)
            : [];

        $this->attributes = $this->getComputedAttributes();

        $this->_name = $this->def->name ?? NULL;
        $this->style = $this->getCompiledStyle();
        $this->setStyle($this->style);

        $this->name = $this->getCompiledName();

        $this->def->equip_region = ($this->def->equip_region ?? "") != ""
            ? explode(" ", $this->def->equip_region)
            : [];

        if(in_array("whole_head", $this->def->equip_region))
        {
            array_push($this->def->equip_region, "hat", "face", "glasses");
        }
        if(in_array("glasses", $this->def->equip_region))
        {
            array_push($this->def->equip_region, "face", "lenses");
        }

        if(in_array($this->getType(), ["cosmetic"]))
            $this->scrap = 60;

        if(in_array($this->getType(), ["weapon"]))
            $this->scrap = 30;

        if($this->quality == Q_STRANGE && $this->scrap > 0) $this->scrap += 440;

        $this->icons = [];

        if($this->getAttributeByName("attach particle effect") > 0)
        {
            array_push($this->icons, "{CDN}/assets/images/inventory/unusual_icon.png");
        }

        if($this->getAttributeByName("quest loaner item") == true)
        {
            array_push($this->icons, "{CDN}/assets/images/inventory/loaner_icon.png");
        }

        switch($this->getAttributeByName("holiday restricted"))
        {
            case HOLIDAY_HALLOWEEN:
                array_push($this->icons, "{CDN}/assets/images/inventory/halloween_restricted.png");
                break;
            case HOLIDAY_HALLOWEENORFULLMOON:
                array_push($this->icons, "{CDN}/assets/images/inventory/halloween_restricted.png");
                break;
        }

        $Paint = $this->getAttributeByName("set item tint RGB");
        if(isset($this->m_hCore->Economy["NameStrings"]["paintcan_splash"][$Paint]))
            array_push($this->icons, "{CDN}/assets/images/inventory/items/tools/paintsplashes/".$this->m_hCore->Economy["NameStrings"]["paintcan_splash"][$Paint].".png");

        for ($i = 0; $i < 10; $i++)
        {
            if($i == 0) {
                $sName = "strange eater";
            } else {
                $sName = "strange eater part ".$i;
            }
            if($this->getAttributeByName($sName) > 0) {
                array_push($this->icons, "{CDN}/assets/images/inventory/strange_icon.png");
                break;
            }
        }

        // Tool Image
        $this->tool_target_item_image = $this->m_hCore->Economy["Items"][$this->getAttributeByName("tool target item")]["image"] ?? NULL;
        $this->tool_target_item_image_offset = explode(",", $this->getAttributeByName("tool_target_item_icon_offset"))[0] ?? "center";
        $this->tool_target_item_image_size = explode(",", $this->getAttributeByName("tool_target_item_icon_offset"))[1] ?? "50% 50%";
    }

    function getQualityPrefix()
    {
        return $this->m_hCore->items->getQualityPrefix($this->quality);
    }

    function getType()
    {
        return $this->def->type ?? NULL;
    }

    function isLoaner()
    {
        return $this->getAttributeByName("quest loaner item") == true;
    }

    function isCampaignItem()
    {
        return $this->getAttributeByName("is_operation_pass") == true;
    }

    function canUseProperName()
    {
        return $this->m_hCore->items->canQualityUseProperName($this->quality);
    }

    function getStrangeLevelData($points)
    {
        return new StrangeData(["item" => $this, "points" => $points], $this->m_hCore);
    }

    function getStrangeData()
    {
        return $this->getStrangeLevelData($this->getAttributeByName("strange eater value"));
    }

    function getStyleData($style)
    {
        return new StyleData(["item" => $this, "style" => $style], $this->m_hCore);
    }

    function getQualityData()
    {
        return $this->m_hCore->items->getQualityData($this->quality);
    }

    function getCompiledStyle()
    {
        $Style = 0;

        // If we have this attribute set, we ignore style attribute and specify item
        // style according to the one, provided in the level data.
        if($this->getAttributeByName("style changes on strange level") > 0)
        {
            $Counter = $this->getAttributeByName("strange eater value");
            $StrangeLevelData = $this->getStrangeLevelData($Counter);
            $Style = $StrangeLevelData->getStyle();
        } else {
            $Style = $this->getAttributeByName("item style override");
        }

        return $Style;
    }

    function setStyle($style)
    {
        $Style = $this->getStyleData($style);

        $Image = $Style->getImage();
        if($Image !== NULL) $this->image = $Image;
        else $this->image = $this->def->image ?? NULL;

        $Name = $Style->getName();
        if($Name !== NULL) $this->_name = $Name;
    }

    function getCompiledName()
    {
        $Prefixes = [];

        // If this item has strange counter.
        if($this->getAttributeByName("strange eater") > 0)
        {
            $Counter = $this->getAttributeByName("strange eater value");
            $StrangeLevelData = $this->getStrangeLevelData($Counter);
            $Prefix = $StrangeLevelData->getPrefix();

            if($Prefix != NULL)
            {
                array_push($Prefixes, $Prefix);
            }
        } else {
            array_push($Prefixes, $this->getQualityPrefix());
        }

        if($this->getType() == "tool" && $this->getAttributeByName("tool target item") > 0)
        {
            $idx = $this->getAttributeByName("tool target item");
            $TargetItem = $this->m_hCore->items->getItemConfigByDefIndex($idx);

            $Name = (($TargetItem["propername"] ?? 0) == 1 ? "The " : NULL) . $TargetItem["name"];
            array_push($Prefixes, $Name);
        }

        $Name = join(" ", array_merge($Prefixes, [$this->_name]));
        $Name = trim($Name);

        if($this->canUseProperName())
        {
            if(($this->def->propername ?? 0) == 1)
            {
                $Name = "The " . $Name;
            }
        }

        return $Name;
    }

    function getComputedAttributes()
    {
        return $this->m_hCore->items->mergeAttributes(
            $this->def->attributes,
            $this->_attributes
        );
    }

    function remove()
    {
        $this->m_hCore->dbh->query("DELETE FROM tf_pack WHERE id = %d", [$this->id]);
    }

    function scrap()
    {
        // Another check because we don't want to get this running for weapons that can't be scrapped.
        if(!$this->canScrap()) return;

        $Owner = $this->getOwner();
        if($Owner != NULL)
        {
            $Owner->chargeCurrency(- ((integer)$this->scrap) );
            $this->remove();
        }
    }

    function toDOMPreview($tags = [], $brackets = [], $message = PREVIEW_MESSAGE_NULL, $template = "preview")
    {
        return $this->m_hCore->items->toDOMPreview(
            array_merge([
                "image" => $this->image ?? NULL,
                "name" => $this->name ?? NULL,
                "description" => $this->def->description ?? NULL,
                "quality_color" => $this->getQualityData()->getColor(),
                "attributes_html" => $this->toDOMAttributes(),

                "item_number" => 0,

                'icons' => join("", array_map(function($i){
                    return render('prefabs/items/icon',[
                        "image" => $i
                    ]);
                }, $this->icons))
            ], $tags),
            array_merge([
                // We don't have any yet, but we might need some in the other day?
            ], $brackets),
            $message,
            $template
        );
    }

    function toDOM($tags = [], $brackets = [])
    {
        return $this->m_hCore->items->toDOMfromDefID(
            $this->defid,
            $this->quality,
            array_merge([
                'name' => $this->name ?? "",
                'description' => $this->def->description ?? NULL,
                "attributes_html" => $this->toDOMAttributes(),
                'index' => $this->id,
                'image' => $this->image,

                'tool_target_image' => $this->tool_target_item_image,
                'tool_target_image_position' => $this->tool_target_item_image_offset,
                'tool_target_image_size' => $this->tool_target_item_image_size,

                'icons' => join("", array_map(function($i){
                    return render('prefabs/items/icon',[
                        "image" => $i
                    ]);
                }, $this->icons)),

                'scrap' => $this->scrap ?? NULL,
                'workshop_id' => $this->getAttributeByName("submission workshop link"),
                'campaign' => $this->getAttributeByName("item campaign name"),
            ], $tags),
            array_merge([
                'CAN_EQUIP' => count(array_keys($this->def->used_by_classes ?? [])) > 0,
                'CAN_EQUIP_SCOUT' => isset($this->def->used_by_classes["scout"]),
                'CAN_EQUIP_SOLDIER' => isset($this->def->used_by_classes["soldier"]),
                'CAN_EQUIP_PYRO' => isset($this->def->used_by_classes["pyro"]),
                'CAN_EQUIP_DEMOMAN' => isset($this->def->used_by_classes["demo"]),
                'CAN_EQUIP_HEAVY' => isset($this->def->used_by_classes["heavy"]),
                'CAN_EQUIP_ENGINEER' => isset($this->def->used_by_classes["engineer"]),
                'CAN_EQUIP_MEDIC' => isset($this->def->used_by_classes["medic"]),
                'CAN_EQUIP_SNIPER' => isset($this->def->used_by_classes["sniper"]),
                'CAN_EQUIP_SPY' => isset($this->def->used_by_classes["spy"]),

                'CAN_INSPECT' => true,
                'CAN_USE' => $this->canUse(),
                'CAN_SCRAP' => $this->canScrap(),
                'CAN_DELETE' => $this->canDelete(),
                'CAN_STEAM_VOTE' => $this->getAttributeByName("submission workshop link") != 0,
                'CAN_CAMPAIGN' => $this->getAttributeByName("item campaign name", NULL) !== NULL
            ], $brackets)
        );
    }

    function toDOMSlot($tags = [], $brackets = [])
    {
        $hash = md5(json_encode($tags).json_encode($brackets));
        if(isset($this->cache_slot[$hash]))
            return $this->cache_slot[$hash];
        else {
            $HTML = $this->m_hCore->items->toDOMSlotfromDefID(
                $this->defid,
                $this->quality,
                array_merge([
                    'name' => $this->name ?? "",
                    'description' => $this->def->description ?? NULL,
                    "attributes_html" => $this->toDOMAttributes(),
                    'image' => $this->image,

                    'tool_target_image' => $this->tool_target_item_image,
                    'tool_target_image_position' => $this->tool_target_item_image_offset,
                    'tool_target_image_size' => $this->tool_target_item_image_size,

                    'icons' => join("", array_map(function($i){
                        return render('prefabs/items/icon', [
                            "image" => $i
                        ]);
                    }, $this->icons))
                ], $tags),
                array_merge([
                    'SET' => true,
                ], $brackets)
            );
            $this->cache_slot[$hash] = $HTML;
            // $this->saveCache();
            return $HTML;
        }
    }

    function strangify()
    {
        // We only set quality to strange if original item is Unique.
        if($this->quality == Q_UNIQUE)
            $this->setQuality(Q_STRANGE);

        $EaterType = $this->m_hCore->items->getStrangeEaterFromType($this->def->type);
        if($EaterType != NULL)
            $this->setAttribute("strange eater", $EaterType);
    }

    function setQuality($quality)
    {
        $this->m_hCore->dbh->query('UPDATE tf_pack SET quality = %d WHERE id = %d', [
            $quality,
            $this->id
        ]);
    }

    function setAttribute($name, $value)
    {
        $this->_attributes = $this->m_hCore->items->mergeAttributes(
            $this->_attributes,
            [[
                "name" => $name,
                "value" => $value
            ]]
        );

        $this->attributes = $this->getComputedAttributes();

        $this->m_hCore->dbh->query('UPDATE tf_pack SET attributes = %s WHERE id = %d', [
            json_encode($this->_attributes),
            $this->id
        ]);
    }

    function getAttributeByName($name, $default = 0)
    {
        foreach ($this->attributes as $attr) {
            if($attr["name"] == $name) return $attr["value"];
        }
        return $default;
    }

    function setSlot($slot)
    {
        $this->slot = $slot;
        $this->m_hCore->dbh->query('UPDATE tf_pack SET slot = %d WHERE id = %d', [
            $slot,
            $this->id
        ]);
    }

    function canUse()
    {
        if($this->isLoaner()) return false;
        // We only allow usage of this item if it's a tool and it's not
        // green quality (selfmade or community).
        return (
            in_array($this->def->type, ["tool"]) &&
            !in_array($this->quality, [Q_COMMUNITY, Q_SELFMADE])
        );
    }

    function canScrap()
    {
        if($this->isCampaignItem()) return false;
        if($this->isLoaner()) return false;
        // Owner can only scrap an item if it is either weapon or cosmetic.
        // And also if scrap value is defined.
        return  in_array($this->def->type, ["cosmetic", "weapon"]) &&
                !in_array($this->quality, [Q_COMMUNITY, Q_SELFMADE]) &&
                $this->scrap != 0;
    }

    function canDelete()
    {
        if($this->isCampaignItem()) return false;
        if($this->isLoaner()) return false;
        // Owner can only delete an item if it's not green quality.
        return !in_array($this->quality, [Q_COMMUNITY, Q_SELFMADE]);
    }

    function parseAttributes()
    {
        return $this->m_hCore->items->parseAttributes($this->attributes, $this);
    }

    function toDOMAttributes()
    {
        return $this->m_hCore->items->toDOMAttributes($this->attributes, $this);
    }

    function toDOMOverlayAttributes()
    {
        return $this->m_hCore->items->toDOMAttributes($this->_attributes, $this);
    }

    public function getOwner()
    {
        return $this->m_hCore->users->find("steamid", $this->steamid);
    }
}
?>
