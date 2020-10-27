<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class StrangeData extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hItem = $data["item"];
        $this->m_iPoints = $data["points"];
    }

    function getStrangeLevelsData()
    {
        $DataName = $this->getItem()->def->strange_level_data ?? NULL;
        if(!isset($DataName))
        {
            $DataName = $this->m_hCore->items->getDefaultStrangeLevelDataForType($this->getItem()->def->type);
        }

        return $this->m_hCore->items->getStrangeLevelData($DataName);
    }

    function addPointsToPart($part, $points)
    {
        $this->setPointsToPart($part, $this->m_iPoints + $points);
    }

    function setPointsToPart($part, $points)
    {
        $sAttr = $this->getPartAttribute($part);
        $sAttr .= " value";

        $iOldLevel = $this->getLevel();

        $Item = $this->getItem();
        $Item->setAttribute($sAttr, $points);
        $this->m_iPoints = $points;

        $iNewLevel = $this->getLevel();

        if($iNewLevel > $iOldLevel)
        {
            $this->broadcastNewLevel();
        }
    }

    function broadcastNewLevel()
    {
        $Item = $this->getItem();
        $User = $Item->getOwner();

        $User->queryServerJob(format(
            'ce_broadcast_announce %s @1{name}\'s %s@1 has achieved a new rank: %s!; ce_strange_item_levelup %s %s "%s" "%s"',
            [
                $User->steamid,
                $Item->_name,
                $Item->getStrangeData()->getPrefix(),

                $User->steamid,
                $Item->id,
                $Item->_name,
                $Item->getStrangeData()->getPrefix()
            ]
        ));
    }

    function getItem()
    {
        return $this->m_hItem;
    }

    function getLevel()
    {
        $Levels = $this->getStrangeLevelsData();

        $StrangeLevel = 0;
        foreach ($Levels as $Points => $Level)
        {
            if(!is_numeric($Points)) continue;
            if((+$Points) > $this->m_iPoints) break;

            $StrangeLevel = (+$Points);
        }
        return $StrangeLevel;
    }

    function getPrefix()
    {
        $Levels = $this->getStrangeLevelsData();
        $Level = $this->getLevel();

        $Prefix = NULL;
        foreach ($Levels as $Points => $LevelData)
        {
            if(!is_numeric($Points)) continue;
            if((+$Points) > $Level) break;

            if(isset($LevelData["item_prefix"]))
            {
                $Prefix = $LevelData["item_prefix"];
            }
        }

        return $Prefix;
    }

    function getStyle()
    {
        $Levels = $this->getStrangeLevelsData();
        $Level = $this->getLevel();

        $Style = 0;
        foreach ($Levels as $Points => $LevelData)
        {
            if(!is_numeric($Points)) continue;
            if((+$Points) > $Level) break;

            if(isset($LevelData["item_style"]))
            {
                $Style = (+$LevelData["item_style"]);
            }
        }

        return $Style;
    }

    function getPartAttribute($part)
    {
        if($part == 0) return "strange eater";
        else return "strange eater part $part";
    }
}

?>
