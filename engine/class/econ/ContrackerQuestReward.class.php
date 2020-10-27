<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ContrackerQuestReward extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hConfig = $data["config"];
        $this->m_hOwner = $data["owner"];
        $this->m_hCampaign = $data["campaign"] ?? NULL;
        $this->m_hQuest = $data["quest"] ?? NULL;
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getCampaign()
    {
        return $this->m_hCampaign;
    }

    function getQuest()
    {
        return $this->m_hQuest;
    }

    function toDOM($tags = [], $brackets = [], $template = "contracker")
    {
        switch ($this->getType())
        {
            case 'item':

                $idx = $this->getValue("index");

                // If index is null, try getting it from name.
                if($idx === NULL)
                {
                    $idx = $this->m_hCore->items->getItemIndexByName($this->getValue("item"));
                }

                if($idx === NULL) return NULL;

                // We check if this item exists.
                if($this->m_hCore->items->getItemConfigByDefIndex($idx) === NULL) return NULL;

                return $this->m_hCore->items->toDOMfromDefID($idx, $this->getValue("quality") ?? Q_UNIQUE, $tags, $brackets);
                break;

            case 'currency':

                $amount = $this->getValue("amount");
                if($amount <= 0) return NULL;

                return $this->m_hCore->items->toDOM(
                    array_merge([
                        "name" => "$amount Mann Coins",
                        "image" => $this->m_hCore->items->getCoinsImage($amount),
                        "description" => "Used to purchase items in Mann Co. Work-Shop.",
                        "quality" => Q_UNIQUE,
                        "quality_color" => $this->m_hCore->items->getQualityData(Q_UNIQUE)->getColor()
                    ], $tags),
                    $brackets
                );
                break;

            case 'campaign_points':
                return render("prefabs/contracker/preview/$template/reward_points",
                    array_merge([
                        "points" => $this->getValue("amount") ?? 0
                    ], $tags),
                    $brackets
                );
                break;

            case 'upgrade_item':
                $sName = $this->getValue("name");
                $sImage = $this->getValue("image");
                $sMessage = $this->getValue("message");
                $nQuality = $this->getValue("quality") ?? Q_UNIQUE;

                return $this->m_hCore->items->toDOM(
                    array_merge([
                        "name" => $sName,
                        "image" => $sImage,
                        "description" => $sMessage,
                        "quality" => $nQuality,
                        "quality_color" => $this->m_hCore->items->getQualityData($nQuality)->getColor()
                    ], $tags),
                    $brackets
                );
                break;
        }
    }

    function getValue($key)
    {
        return $this->m_hConfig[$key] ?? NULL;
    }

    function getType()
    {
        return $this->getValue("type");
    }

    function distribute()
    {
        switch ($this->getType())
        {
            case 'item':
                $idx = $this->getValue("index");
                if(!isset($idx)) $idx = $this->m_hCore->items->getItemIndexByName($this->getValue("item"));
                if(!isset($idx)) break;

                $Item = $this->m_hCore->items->create(
                    $this->getOwner()->steamid,
                    $idx,
                    $this->getValue("quality") ?? Q_UNIQUE,
                    $this->getValue("attributes") ?? NULL,
                    PREVIEW_MESSAGE_REWARD
                );

                break;

            case 'currency':
                if($this->getValue("amount") <= 0) break;

                $this->getOwner()->chargeCurrency($this->getValue("amount") * -1);
                $this->m_hCore->notifications->create($this->getOwner()->steamid, "currency_reward", ["amount" => $this->getValue("amount")]);

                break;

            case 'campaign_points':
                $Campaign = $this->getCampaign();
                if(!isset($Campaign)) break;
                $Campaign->addProgressPoints($this->getValue("amount") ?? 0);

                break;

            case 'upgrade_item':
                $idx = $this->m_hCore->items->getItemIndexByName($this->getValue("item"));
                $Item = $this->getOwner()->getOwnedItemsByDefinitionIndex($idx)[0];

                if(isset($Item))
                {
                    $this->m_hCore->notifications->create($this->getOwner()->steamid, "item_upgraded", ["item_index" => $Item->id]);
                }

                break;
        }
    }
}

?>
