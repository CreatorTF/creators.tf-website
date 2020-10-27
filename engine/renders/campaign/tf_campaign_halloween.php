<?php
if(!defined("INCLUDED")) die("Access forbidden.");
define("CAMPAIGN_HALLOWEEN", "halloween");
define("HALLOWEEN_QUEST_TEMPLATE", "scroll");
define("HALLOWEEN_LEVEL_TEMPLATE", "halloween");

if($_GET["campaign"] == "halloween")
{
    if(isset($Core->User))
    {
        $Campaign = $Core->User->getContracker()->getCampaign(CAMPAIGN_HALLOWEEN);
        $bWasValidated = $Campaign->getCampaignItems()[0]->validateEconItem();

        $CampaignItem = $Campaign->getCampaignItems()[0]->getEconItem();
        if(isset($CampaignItem))
        {
            define("RENDER_ONLY_CONTENT", true);
            $Content = render("pages/operation/core", [
                "content" => render("pages/operation/halloween/root", [
                    "elements" => join("", array_map(function($Level) {
                        return $Level->toDOM([], [], HALLOWEEN_LEVEL_TEMPLATE);
                    }, $Campaign->getLevels())),

                    "quests" => join("", array_map(function($Quest) {
                        return $Quest->toDOM([], [], HALLOWEEN_QUEST_TEMPLATE);
                    }, $Campaign->getQuests())),

                    "campaign_item.index" => $CampaignItem->id ?? NULL,
                    "campaign_item.name" => $CampaignItem->name ?? NULL,
                    "campaign_item.image" => $CampaignItem->image ?? NULL,
                    "campaign_item.description" => $CampaignItem->def->description ?? NULL,
                    "campaign_item.attributes" => $CampaignItem->toDOMAttributes(),
                    "campaign_item.quality_color" => $CampaignItem->getQualityData()->getColor(),

                    "progress.completion" => $Campaign->getCompletion(),
                    "progress.limit" => $Campaign->getLimit(),
                    "progress.percent" => $Campaign->getCompletionPercent(),

                    "level.index" => $Campaign->getActiveLevel()->m_iLevel + 1,
                    "level.limit" => $Campaign->getActiveLevel()->getDeltaPoints(),
                    "level.progress" => $Campaign->getActiveLevel()->getDeltaCompletion(),
                    "level.percent" => $Campaign->getActiveLevel()->getCompletionPercent(),

                    "quests.limit" => $Campaign->getQuestCount(),
                    "quests.progress" => $Campaign->getQuestCompletion(),
                    "quests.percent" => $Campaign->getQuestCompletionPercent()
                ], [
                    "NEW_ITEMS" => !$bWasValidated,
                    "NO_NEW_ITEMS" => $bWasValidated
                ])
            ]);
        } else {
            $Core->error = 500;
        }
    } else {
        $Core->error = 403;
    }
}

?>
