<?php
if(!defined("INCLUDED")) die("Access forbidden.");
define("QUEST_INDEX_ACTIVE", -1);

class ContrackerQuest extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_iIndex = $data["index"];
        $this->m_hOwner = $data["owner"];
        $this->m_hConfig = $data["config"];
    }

    function toDOM($tags = [], $brackets = [], $template = "circle")
    {
        return render("prefabs/contracker/quest/$template",
            array_merge([
                "id" => $this->m_iIndex,
                "icon" => $this->getValue("icon"),
                "name" => $this->getName(),
                "title" => $this->getTitle(),
                "index" => $this->m_iIndex,
                "connect" => "[]",
                "posX" => 0,
                "posY" => 0
            ], $tags),
            array_merge([
                "LOOT_ITEM" => false,
                "LOOT_CURRENCY" => true,
                "INACTIVE" => !$this->isUnlocked()
            ], $brackets)
        );
    }

    function toDOMPreview($tags = [], $brackets = [], $template = "contracker")
    {
        $GLOBALS["template"] = $template;
        return render("prefabs/contracker/preview/".$template."/root",
        [
            "id" => $this->m_iIndex,
            "class" => $this->getValue("class"),
            "name" => $this->getName(),

            "progress.total.percentage" => floor($this->getPrimaryObjective()->getTotalCompletion() * 100),
            "progress.saved.percentage" => floor($this->getPrimaryObjective()->getCompletion() * 100),
            "progress.unsaved.percentage" => floor($this->getPrimaryObjective()->getUnsavedCompletion() * 100),
            "progress.total" => floor($this->getPrimaryObjective()->getTotalProgress()),
            "progress.saved" => floor($this->getPrimaryObjective()->getProgress()),
            "progress.unsaved" => floor($this->getPrimaryObjective()->getVerifiedUnsavedProgress()),

            "dependencies" => join("", array_map(function($Quest)
            {
                return render("prefabs/contracker/preview/".$GLOBALS["template"]."/dependency", [
                    "progress" => $Quest->getPrimaryObjective()->getProgress(),
                    "limit" => $Quest->getPrimaryObjective()->getLimit(),
                    "name" =>  $Quest->getName(),
                    "id" =>  $Quest->m_iIndex
                ]);
            }, $this->getUncompletedDependencies())),

            "limit" => $this->getPrimaryObjective()->getLimit(),
            "image" => $this->getValue("image"),

            "objective_primary" => render("prefabs/contracker/preview/".$GLOBALS["template"]."/objective",
            [
                "progress" => $this->getPrimaryObjective()->getProgress(),
                "limit" => $this->getPrimaryObjective()->getLimit(),
                "objective" => $this->getPrimaryObjective()->getName(),
                "points" => $this->getPrimaryObjective()->getPoints()
            ], [
                "SHOW_LIMIT" => false
            ]),

            "objectives_bonus" => join("", array_map(function($Objective)
            {
                return render("prefabs/contracker/preview/".$GLOBALS["template"]."/objective", [
                    "progress" => $Objective->getTotalProgress(),
                    "limit" => $Objective->getLimit(),
                    "objective" => $Objective->getName(),
                    "points" => $Objective->getPoints()
                ], [
                    "SHOW_LIMIT" => $Objective->hasLimit()
                ]);
            }, $this->getBonusObjectives())),

            "rewards" => join("", array_map(function($r){
                return $r->toDOM([
                    "image_size" => '100% 100%'
                ], [], $GLOBALS["template"]);
            }, $this->getRewards()))
        ], [
            "CAN_ACTIVATE" => $this->canActivate(),
            "CAN_TURNIN" => $this->canTurnIn(),

            "TURNED" => $this->isTurnedIn(),
            "NOT_TURNED" => !$this->isTurnedIn(),
            "ACTIVE" => $this->isActive(),
            "NOT_ACTIVE" => !$this->isActive(),
            "UNLOCKED" => $this->isUnlocked(),
            "NOT_UNLOCKED" => !$this->isUnlocked(),

            "HAS_BONUS" => count($this->getBonusObjectives()) > 0,

            "IS_WAITING_FOR_TRUSTED" => $this->isWaitForTrustedState()
        ]);
    }

    function getObjective($obj)
    {
        if(!$this->isValidObjective($obj)) return NULL;
        if($obj >= $this->getObjectiveCount()) return;

        return new ContrackerQuestObjective([
            "index" => $obj,
            "quest" => $this,
            "owner" => $this->getOwner(),
            "config" => $this->m_hConfig["objectives"][$obj]
        ], $this->m_hCore);
    }

    function getObjectives()
    {
        $return = [];
        for($i = 0; $i < $this->getObjectiveCount(); $i++)
        {
            array_push($return, $this->getObjective($i));
        }
        return $return;
    }

    function getPrimaryObjective()
    {
        return $this->getObjectives()[QUEST_OBJECTIVE_PRIMARY];
    }

    function getBonusObjectives()
    {
        $return = $this->getObjectives();
        for($i = 0; $i < count($return); $i++)
        {
            if($i == QUEST_OBJECTIVE_PRIMARY)
            {
                array_splice($return, $i, 1);
            }
        }
        return $return;
    }

    function isValidObjective($obj)
    {
        return $obj >= 0 && $obj < QUEST_MAX_OBJECTIVES;
    }

    function saveUnsavedProgress()
    {
        foreach ($this->getObjectives() as $Objective)
        {
            $count = $Objective->getUnsavedProgress();
            if($count > 0)
            {
                $Objective->addProgress($count);
            }
        }
        $this->clearUnsavedProgress();
    }

    function getCampaign()
    {
        $Campaigns = $this->getOwner()->getContracker()->getCampaigns();
        foreach ($Campaigns as $hCampaign)
        {
            $hQuests = $hCampaign->getQuests();
            foreach ($hQuests as $hQuest)
            {
                if($hQuest->m_iIndex == $this->m_iIndex) return $hCampaign;
            }
        }
        return NULL;
    }

    function isWaitForTrustedState()
    {
        return $this->isUnsavedCompleted() && !$this->isCompleted();
    }

    function isSavedProgressActive()
    {
        $User = $this->getOwner();

        if(!$this->isActive()) return false;
        if(!$User->isOnServer()) return false;

        $SavedServerID = $this->getProgress()->getValue("unsaved_server");
        $RealServerID = $User->presence->server->id ?? NULL;;
        if($SavedServerID != $RealServerID) return false;

        $SavedJoinTime = $this->getProgress()->getValue("unsaved_join_time");
        $RealJoinTime = $User->presence->join_time ?? NULL;
        if($SavedJoinTime != $RealJoinTime) return false;

        return true;
    }

    function verifyUnsavedProgress()
    {
        if(!$this->isSavedProgressActive())
        {
            $this->clearUnsavedProgress();
        }
    }

    function clearUnsavedProgress()
    {
        for($i = 0; $i < QUEST_MAX_OBJECTIVES; $i++)
        {
            $key = "unsaved_".$i;
            $this->getProgress()->deleteValue($key);
        }
    }

    function getRewards()
    {
        $Rewards = [];
        foreach ($this->m_hConfig["rewards"] ?? [] as $reward) {
            array_push($Rewards, new ContrackerQuestReward([
                "config" => $reward,
                "owner" => $this->getOwner(),
                "quest" => $this,
                "campaign" => $this->getCampaign()
            ], $this->m_hCore));
        }

        return $Rewards;
    }

    function isTurnedIn()
    {
        $Progress = $this->getProgress();
        if(!isset($Progress)) return false;
        else return $Progress->getValue("turned") === true;
    }

    function getObjectiveCount()
    {
        return count($this->m_hConfig["objectives"]);
    }

    function isActive()
    {
        $Quest = $this->getOwner()->getContracker()->getActiveContract();
        if(!isset($Quest)) return false;
        else return $Quest->m_iIndex == $this->m_iIndex;
    }

    function isObjectiveCompleted($objective)
    {
        return $this->getObjectiveProgress($objective) >= ($this->getObjectiveValue($objective, "limit") ?? 100);
    }

    function isFullyCompleted()
    {
        foreach ($this->getObjectives() as $Objective)
        {
            if(!$Objective->isCompleted()) return false;
        }
        return true;
    }

    function turnIn()
    {
        $Rewards = $this->getRewards();

        foreach ($Rewards as $Reward)
        {
            $Reward->distribute();
        }

        $Progress = $this->getProgress();
        $Progress->setValue("turned", true);
        $Progress->save();

        $this->cleanLoanerItems();

        return;
    }

    function isUnlocked()
    {
        return count($this->getUncompletedDependencies()) == 0;
    }

    function getUncompletedDependencies()
    {
        return array_filter($this->getDependencies(), function($Quest) {
            return !$Quest->isCompleted();
        });
    }

    function getDependencies()
    {
        $Page = $this->getParent();
        if(!isset($Page)) return [];

        $List = [];
        foreach ($Page->getQuestParams($this)["required"] ?? [] as $title)
        {
            $Quest = $this->getOwner()->getContracker()->getQuest($title);
            if(isset($Quest))
            {
                array_push($List, $Quest);
            }
        }

        if(count($List) == 0)
        {
            $List = $Page->getDependencies();
        }

        return $List;
    }

    function getRequiredItems()
    {
        return $this->getValue("required_items") ?? [];
    }

    function cleanLoanerItems()
    {
        foreach ($this->getOwner()->getLoanerItems() as $Item)
        {
            $Item->remove();
        }
    }

    function distributeLoanerItems()
    {
        $this->cleanLoanerItems();
        foreach ($this->getRequiredItems() as $ItemName)
        {
            $idx = $this->m_hCore->items->getItemIndexByName($ItemName);
            if(isset($idx))
            {
                $EconItems = $this->getOwner()->getOwnedItemsByDefinitionIndex($idx);
                if(count($EconItems) == 0)
                {
                    $this->m_hCore->items->create($this->getOwner()->steamid, $idx, Q_UNIQUE, [[
                        "name" => "quest loaner item",
                        "value" => true
                    ]], PREVIEW_MESSAGE_QUEST_LOANER);
                }
            }
        }
    }

    function canActivate()
    {
        if(!$this->isUnlocked()) return false;
        if($this->isActive()) return false;
        if($this->isFullyCompleted()) return false;

        return true;
    }

    function canTurnIn()
    {
        if($this->isTurnedIn()) return false;
        return $this->isCompleted();
    }

    function getParent()
    {
        foreach ($this->getOwner()->getContracker()->getPages() as $Page) {
            if($Page->getQuest($this->getTitle()) !== NULL)
                return $Page;
        }
    }

    function getProgress()
    {
        $Progress = $this->getOwner()->getProgress();
        foreach ($Progress as $Prog) {
            if($Prog->m_sTarget == "[Q:".$this->m_iIndex."]") {
                return $Prog;
            }
        }

        // If we don't have a progress yet, create one.
        $Progress = new Progress([
            "steamid" => $this->getOwner()->steamid,
            "target" => "[Q:".$this->m_iIndex."]",
            "created" => false
        ], $this->m_hCore);

        array_push($this->getOwner()->__progress, $Progress);

        return $Progress;
    }

    function isCompleted()
    {
        if($this->getProgress() === NULL) return false;
        return $this->getObjective(QUEST_OBJECTIVE_PRIMARY)->isCompleted();
    }

    function isUnsavedCompleted()
    {
        if($this->getProgress() === NULL) return false;
        return $this->getObjective(QUEST_OBJECTIVE_PRIMARY)->isUnsavedCompleted();
    }

    function getObjectiveValue($objective, $key)
    {
        return $this->m_hConfig["objectives"][$objective][$key] ?? NULL;
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }

    function getName()
    {
        return $this->getValue("name");
    }

    function getTitle()
    {
        return $this->getValue("title");
    }
}

?>
