<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];
if($method == 'GET')
{
    if(!CheckPermission_CanRead()) ThrowAPIError(403);

    $param = check($_REQ,['get']);
    if(isset($param)) ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

    if($_GET["get"] == "schema")
    {
        $Result = [];
        $Quests = [];
        if(isset($_GET["campaign"]))
        {
            $Campaign = $Core->User->getContracker()->getCampaign($_GET["campaign"]);
            if(isset($Campaign))
            {
                $Quests = $Campaign->getQuests();
            }

            $Levels = array_map(function($Level) {
                return [
                    "index" => $Level->m_iLevel,
                    "progress" => $Level->getDeltaCompletion(),
                    "limit" => $Level->getDeltaPoints(),

                    "is_completed" => $Level->isCompleted(),
                    "is_active" => $Level->isActive()
                ];
            }, $Campaign->getLevels());

            $Result["campaign"] = [
                "levels" => $Levels,
                "points" => $Campaign->getCompletion(),
                "limit" => $Campaign->getLimit()
            ];
        } else {
            $Quests = $Core->User->getContracker()->getQuests();
        }

        $Quests = array_map(function($Quest) {
            return [
                "id" => $Quest->m_iIndex,
                "title" => $Quest->getTitle(),

                "is_unlocked" => $Quest->isUnlocked(),
                "is_active" => $Quest->isActive(),
                "is_turned" => $Quest->isTurnedIn(),
                "is_completed" => $Quest->isCompleted(),
                "is_waiting_for_trusted" => $Quest->isWaitForTrustedState(),

                "can_activate" => $Quest->canActivate(),
                "can_turnin" => $Quest->canTurnIn(),

                "objectives" => array_map(function($Objective) {
                    return [
                        "index" => $Objective->m_iIndex,
                        "completed" => $Objective->isCompleted(),
                        "progress" => $Objective->getProgress(),
                        "unsaved_progress" => $Objective->getVerifiedUnsavedProgress(),
                        "limit" => $Objective->getLimit()
                    ];
                }, $Quest->getObjectives())
            ];
        }, $Quests);

        $Result["quests"] = $Quests;

        ThrowResult($Result);

        if(isset($Campaign))
        {
            if($Campaign->shouldMarkAsSeen())
            {
                $Campaign->markProgressAsSeen();
            }
        }

        $Core->User->getContracker()->portOldProgress();
    } else if ($_GET["get"] == "progress")
    {
        $Quests = $Core->User->getContracker()->getQuests();
        $Quests = array_filter($Quests, function($Quest) {
            return $Quest->getProgress()->m_bCreated;
        });
        $Contracts = array_map(function($Quest) {
            return array_map(function($Objective) {
                return $Objective->getProgress();
            }, $Quest->getObjectives());
        }, $Quests);


        ThrowResult([
            "steamid" => $Core->User->steamid,
            "contracts" => $Contracts,
        ]);
    } else if ($_GET["get"] == "contract")
    {
        $param = check($_REQ,['contract']);
        if(isset($param)) ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

        if($_REQ["contract"] == QUEST_INDEX_ACTIVE)
        {
            $Quest = $Core->User->getContracker()->getActiveContract();
        } else {
            $Quest = $Core->User->getContracker()->getQuest($_REQ["contract"]);
        }
        if(!isset($Quest)) ThrowAPIError(404);

        $Progress = $Quest->getProgress();
        $sTemplateName = $_REQ["html"];
        $sBaseURL = $_SERVER['DOCUMENT_ROOT']."/templates/default/prefabs/questpreview/".$sTemplateName;

        ThrowResult([
            "contract" => [
                "id" => $Quest->m_iIndex,
                "name" => $Quest->getName(),
                "objectives" => array_map(function($obj) {
                    return [
                        "name" => $obj->getName(),
                        "points" => $obj->getPoints(),
                        "name" => $obj->getLimit(),
                        "progress" => $obj->getTotalProgress()
                    ];
                }, $Quest->getObjectives()),
                "image" => $Quest->getValue("image"),

                "turned" => $Quest->isTurnedIn(),
                "active" => $Quest->isActive(),

                "is_unlocked" => $Quest->isUnlocked(),
                "can_turnin" => $Quest->canTurnIn(),

                "html" => $Quest->toDOMPreview([], [], $sTemplateName)
            ]
        ]);
    }
}else if($method == "POST")
{
    $param = check($_REQ,['action']);
    if(isset($param)) {
        ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
    }

    if($_REQ["action"] == "activate")
    {
        if(!CheckPermission_CanWrite())
            ThrowAPIError(403);

        $param = check($_REQ,['contract']);
        if(isset($param))
            ThrowAPIError(ERROR_API_INVALIDPARAM, $param);

        if(!is_numeric($_REQ["contract"]) || $_REQ["contract"] < 0)
            ThrowAPIError(ERROR_API_INVALIDPARAM, "contract");

        if($_REQ["contract"] > 0)
        {
            $Quest = $Core->User->getContracker()->getQuest($_REQ["contract"]);
            if(!isset($Quest)) {
                ThrowAPIError(ERROR_NOT_FOUND);
            }

            if(!$Quest->canActivate())
            {
                ThrowAPIError(403);
            }
        }

        $ActiveQuest = $Core->User->getContracker()->getActiveContract();
        if(isset($ActiveQuest))
        {
            $ActiveQuest->clearUnsavedProgress();
            $ActiveQuest->getProgress()->save();
        }

        $Core->User->getContracker()->setContract($_REQ["contract"]);
        ThrowResult();
    } else if ($_REQ["action"] == "turnin")
    {
        if(!CheckPermission_CanWrite())
            ThrowAPIError(403);

        $param = check($_REQ,['contract']);
        if(isset($param)) {
            ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
        }

        if(!is_numeric($_REQ["contract"]) || $_REQ["contract"] <= 0)
            ThrowAPIError(ERROR_API_INVALIDPARAM, "contract");

        $Quest = $Core->User->getContracker()->getQuest($_REQ["contract"]);
        if(!isset($Quest)) {
            ThrowAPIError(ERROR_NOT_FOUND);
        }

        if(!$Quest->canTurnIn())
        {
            ThrowAPIError(403);
        }
        $Quest->turnIn();

        ThrowResult();
    }
}
?>
