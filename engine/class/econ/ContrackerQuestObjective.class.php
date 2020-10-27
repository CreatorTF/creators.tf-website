<?php
if(!defined("INCLUDED")) die("Access forbidden.");
define("QUEST_MAX_OBJECTIVES", 10);
define("QUEST_OBJECTIVE_PRIMARY", 0);

class ContrackerQuestObjective extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_iIndex = $data["index"];
        $this->m_hQuest = $data["quest"];
        $this->m_hOwner = $data["owner"];
        $this->m_hConfig = $data["config"];
    }

    function getProgress()
    {
        return $this->getQuest()->getProgress()->getValue("objective_".$this->m_iIndex) ?? 0;
    }

    function setProgress($value)
    {
        $this->getQuest()->getProgress()->setValue("objective_".$this->m_iIndex, $value);
    }

    function addProgress($value)
    {
        $this->setProgress($this->getProgress() + $value);
    }

    function getVerifiedUnsavedProgress()
    {
        if(!$this->getQuest()->isSavedProgressActive()) return 0;
        return $this->getUnsavedProgress();
    }

    function getUnsavedProgress()
    {
        return $this->getQuest()->getProgress()->getValue("unsaved_".$this->m_iIndex) ?? 0;
    }

    function setUnsavedProgress($value)
    {
        echo "unsaved_".$this->m_iIndex." :: ".$value.PHP_EOL;
        $this->getQuest()->getProgress()->setValue("unsaved_".$this->m_iIndex, $value);
        $this->getQuest()->getProgress()->setValue("not_bugged", true);
    }

    function addUnsavedProgress($value)
    {
        $this->setUnsavedProgress($this->getUnsavedProgress() + $value);
    }

    function getTotalProgress()
    {
        return $this->getProgress() + $this->getVerifiedUnsavedProgress();
    }

    function getCompletion()
    {
        return $this->getProgress() / $this->getLimit();
    }

    function getUnsavedCompletion()
    {
        return $this->getVerifiedUnsavedProgress() / $this->getLimit();
    }

    function getTotalCompletion()
    {
        return ($this->getProgress() + $this->getVerifiedUnsavedProgress()) / $this->getLimit();
    }

    function isCompleted()
    {
        if(!$this->hasLimit()) return true;
        return $this->getProgress() >= $this->getLimit();
    }

    function isUnsavedCompleted()
    {
        if(!$this->hasLimit()) return true;
        return $this->getVerifiedUnsavedProgress() >= $this->getLimit();
    }

    function hasLimit()
    {
        return $this->getLimit() != 0;
    }

    function getLimit()
    {
        return $this->getValue("limit") ?? 100;
    }

    function getPoints()
    {
        return $this->getValue("points") ?? 0;
    }

    function getName()
    {
        return $this->getValue("name");
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getQuest()
    {
        return $this->m_hQuest;
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }
}

?>
