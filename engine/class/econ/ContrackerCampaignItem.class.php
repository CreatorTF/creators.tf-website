<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ContrackerCampaignItem extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hOwner = $data["owner"];
        $this->m_hCampaign = $data["campaign"];
        $this->m_hConfig = $data["config"];
    }

    function getEconItem()
    {
        $iDefID = $this->getDefinitionIndex();
        if(!isset($iDefID)) return NULL;

        return $this->getOwner()->getOwnedItemsByDefinitionIndex($iDefID)[0];
    }

    function updateEconItemAttributes()
    {
        $Item = $this->getEconItem();
        if(!isset($Item)) return NULL; // Item must exist for us to be able to do this.

        $iValue = $this->getCampaign()->getCompletion();

        $hParts = $this->getValue("sync_strange_parts") ?? [];
        foreach ($hParts as $iPart)
        {
            $Item->getStrangeData()->setPointsToPart($iPart, $iValue);
        }
    }

    function getDefinitionIndex()
    {
        $iDefID = $this->m_hCore->items->getItemIndexByName($this->getValue("item"));
        if(!isset($iDefID)) $iDefID = $this->getValue("item");
        return $iDefID;
    }

    function validateEconItem()
    {
        $Item = $this->getEconItem();
        if(!isset($Item))
        {
            $Item = $this->createEconItem();
            $this->getOwner()->rememberItem($Item);

            $this->updateEconItemAttributes();
            return false;
        }
        return true;
    }

    function createEconItem()
    {
        $iDefID = $this->getDefinitionIndex();
        if(!isset($iDefID)) return NULL;

        return $this->m_hCore->items->create($this->getOwner()->steamid, $iDefID, $this->getValue("quality") ?? Q_UNIQUE, [], PREVIEW_MESSAGE_FOUND);
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
}

?>
