<?php
if(!defined("INCLUDED")) die("Access forbidden.");
define("CAMPAIGN_LEVEL_COMPLETE", 1);

class ContrackerCampaign extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hOwner = $data["owner"];
        $this->m_hConfig = $data["config"];
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getQuests()
    {
        $Quests = [];
        foreach ($this->m_hConfig["quests"] ?? [] as $quest) {
            $Quest = $this->getOwner()->getContracker()->getQuest($quest);
            if($Quest !== NULL)
            {
                array_push($Quests, $Quest);
            }
        }
        return $Quests;
    }

    function getQuestCount()
    {
        return count($this->getQuests());
    }

    function getCompletedQuests()
    {
        $Quests = [];
        foreach ($this->getQuests() as $Quest)
        {
            if($Quest->isCompleted())
            {
                array_push($Quests, $Quest);
            }
        }
        return $Quests;
    }

    function getQuestCompletion()
    {
        return count($this->getCompletedQuests());
    }

    function getQuestCompletionPercent()
    {
        return clamp($this->getQuestCompletion() / $this->getQuestCount() * 100, 0, 100);
    }

    function getLevels()
    {
        $Levels = [];
        foreach ($this->m_hConfig["levels"] ?? [] as $i => $level) {
            $Level = new ContrackerCampaignLevel([
                "config" => $level,
                "campaign" => $this,
                "owner" => $this->getOwner(),
                "level" => $i
            ], $this->m_hCore);

            array_push($Levels, $Level);
        }
        return $Levels;
    }

    function getActiveLevel()
    {
        $iLastLevel = 0;
        foreach ($this->getValue("levels") as $i => $level)
        {
            $iLastLevel = $i;
            if($this->getCompletion() < ($level["points"] ?? 0)) break;
        }
        return $this->getLevel($iLastLevel);
    }

    function getNextLevel()
    {
        $hActiveLevel = $this->getActiveLevel();
        if(isset($hActiveLevel)) return $hActiveLevel->getNextLevel();
        return $this->getLevel(0);
    }

    function getLevel($level)
    {
        $hConfig = $this->m_hConfig["levels"][$level] ?? NULL;

        return new ContrackerCampaignLevel([
            "config" => $hConfig,
            "campaign" => $this,
            "owner" => $this->getOwner(),
            "level" => $level
        ], $this->m_hCore);
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }

    function getCompletion()
    {
        return $this->getProgress()->getValue("progress") ?? 0;
    }

    function getSeenCompletion()
    {
        return $this->getProgress()->getValue("progress_seen") ?? 0;
    }

    function getFirstLevel()
    {
        return $this->getLevels(0);
    }

    function getLastLevel()
    {
        $hLevels = $this->getLevels();
        return end($hLevels);
    }

    function isCompleted()
    {
        return $this->getCompletion() >= $this->getLimit();
    }

    function getLimit()
    {
        return $this->getLastLevel()->getPoints();
    }

    function getCompletionPercent()
    {
        return clamp($this->getCompletion() / $this->getLimit() * 100, 0, 100);
    }

    function getSeenCompletionPercent()
    {
        return clamp($this->getSeenCompletion() / $this->getLimit() * 100, 0, 100);
    }

    function addProgressPoints($points)
    {
        $hOldActiveLevel = $this->getActiveLevel();
        if($this->isCompleted())
        {
            $iLevel = $this->getLastLevel()->m_iLevel + 1;
        } else {
            $iLevel = $hOldActiveLevel->m_iLevel;
        }

        $Progress = $this->getProgress();
        $Progress->setValue("progress", ($Progress->getValue("progress") ?? 0) + $points);
        $Progress->save();

        // Now we update all required items.
        $Items = $this->getCampaignItems();
        foreach ($Items as $Item)
        {
            // Let's make sure item exists first.
            $Item->validateEconItem();
            $Item->updateEconItemAttributes();
        }

        $hNewActiveLevel = $this->getActiveLevel();
        if($this->isCompleted())
        {
            $iLimit = $this->getLastLevel()->m_iLevel + 1;
        } else {
            $iLimit = $hNewActiveLevel->m_iLevel;
        }

        while($iLevel < $iLimit)
        {
            $hLevel = $this->getLevel($iLevel);
            $Reward = $hLevel->getReward();
            if(isset($Reward)) $Reward->distribute();
            $iLevel++;
        }
    }

    function getCampaignItems()
    {
        $Return = [];

        $hConf = $this->getValue("campaign_items") ?? [];
        foreach ($hConf as $hItemConf)
        {
            array_push($Return, new ContrackerCampaignItem([
                "owner" => $this->getOwner(),
                "campaign" => $this,
                "config" => $hItemConf
            ], $this->m_hCore));
        }
        return $Return;
    }

    function shouldMarkAsSeen()
    {
        $Progress = $this->getProgress();
        return $Progress->getValue("progress_seen") != $Progress->getValue("progress");
    }

    function markProgressAsSeen()
    {
        $Progress = $this->getProgress();
        $Progress->setValue("progress_seen", $Progress->getValue("progress"));
        $Progress->save();
    }

    function getProgress()
    {
        $sTitle = $this->getValue("title");
        if(!isset($sTitle)) return NULL;

        $Progress = $this->getOwner()->getProgress();
        foreach ($Progress as $i => $Prog)
        {
            if($Prog->m_sTarget == "[C:".$sTitle."]")
            {
                return $Progress[$i];
            }
        }

        // If we don't have a progress yet, create one.
        $Progress = new Progress([
            "steamid" => $this->getOwner()->steamid,
            "target" => "[C:".$sTitle."]",
            "created" => false
        ], $this->m_hCore);

        array_push($this->getOwner()->__progress, $Progress);

        return $Progress;
    }
}

?>
