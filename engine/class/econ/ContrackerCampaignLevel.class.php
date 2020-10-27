<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ContrackerCampaignLevel extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hOwner = $data["owner"];
        $this->m_hConfig = $data["config"];
        $this->m_hCampaign = $data["campaign"];
        $this->m_iLevel = $data["level"];
    }

    function toDOM($tags = [], $brackets = [], $template = "halloween")
    {
        return render("pages/operation/$template/element", [
            "loot" => $this->getReward() !== NULL ? $this->getReward()->toDOM([
                "dom_attributes" => "tooltip-top"
            ]) : NULL,
            "level" => $this->m_iLevel + 1,
            "progress.percent" => $this->getSeenCompletionPercent(),
            "progress.completed" => $this->getSeenDeltaCompletion(),
            "progress.limit" => $this->getDeltaPoints(),
            "custom_image" => $this->getValue("custom_image"),
            "custom_size" => $this->getValue("custom_size")
        ], [
            "COMPLETED" => $this->isSeenCompleted(),
            "NOT_COMPLETED" => !$this->isSeenCompleted(),
            "HAS_CUSTOM_SKIN" => $this->getValue("custom_image") !== NULL,
            "HAS_CUSTOM_SIZE" => $this->getValue("custom_size") !== NULL,
            "SHOW_LEVEL_NUMBER" => ($this->getValue("show_level_number") ?? true) === true,
            "IS_START_ELEMENT" => ($this->getValue("is_start_element") ?? true) === true
        ]);
    }

    function isActive()
    {
        return $this->getCampaign()->getActiveLevel()->m_iLevel == $this->m_iLevel;
    }

    function getPoints()
    {
        return $this->getValue("points") ?? 0;
    }

    function getDeltaPoints()
    {
        return $this->getPoints() - $this->getMinimumPoints();
    }

    function getDeltaCompletion()
    {
        return clamp($this->getCampaign()->getCompletion() - $this->getMinimumPoints(), 0, $this->getDeltaPoints());
    }

    function getMinimumPoints()
    {
        $hPrevLevel = $this->getPrevLevel();
        if(isset($hPrevLevel)) return $hPrevLevel->getPoints();
        return 0;
    }

    function getPrevLevel()
    {
        return $this->getCampaign()->getLevel($this->m_iLevel - 1);
    }

    function getNextLevel()
    {
        return $this->getCampaign()->getLevel($this->m_iLevel + 1);
    }

    function getCompletionPercent()
    {
        $iDelta = $this->getDeltaPoints();
        $iCompleted = $this->getDeltaCompletion();

        $Percent = min($iCompleted / $iDelta * 100, 100);
        return $Percent;
    }

    function getSeenCompletionPercent()
    {
        $iDelta = $this->getDeltaPoints();
        $iCompleted = $this->getSeenDeltaCompletion();

        $Percent = min($iCompleted / $iDelta * 100, 100);
        return $Percent;
    }

    function getSeenDeltaCompletion()
    {
        return clamp($this->getCampaign()->getSeenCompletion() - $this->getMinimumPoints(), 0, $this->getDeltaPoints());
    }

    function isCompleted()
    {
        return $this->getCampaign()->getCompletion() >= $this->getPoints();
    }

    function isSeenCompleted()
    {
        return $this->getCampaign()->getSeenCompletion() >= $this->getPoints();
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getCampaign()
    {
        return $this->m_hCampaign;
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }

    function getReward()
    {
        if($this->getValue("reward") === NULL) return NULL;
        return new ContrackerQuestReward([
            "config" => $this->getValue("reward"),
            "owner" => $this->getOwner(),
            "campaign" => $this
        ], $this->m_hCore);
    }

}

?>
