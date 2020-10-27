<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST')
{
    if(!(
        isset($Core->Server) &&
        isset($Core->Key) &&
        $Core->Key->special & APISPECIAL_SERVER_KEY &&
        $Core->Key->special & APISPECIAL_SERVER_KEY_WRITE
    )) ThrowAPIError(403);

    $Jobs = $Core->Server->getJobs();

    ThrowResult([
        "jobs" => $Jobs
    ]);

    if(count($Jobs) > 0)
    {
        $Core->Server->flushJobs();
    }

    $hMessages = $_REQ["messages"] ?? [];
    foreach ($hMessages as $hMessage)
    {
        $_MESSAGE = $hMessage["msg_name"] ?? NULL;
        $_CONTENT = $hMessage["msg_content"] ?? [];

        switch ($_MESSAGE)
        {
            // Message needed to track strange eater incrementation
            case 'strange_eater_increment':
                $User = $Core->users->find("steamid", $_CONTENT["steamid"]);
                if(!isset($User)) ThrowAPIError(403);

                $Item = $Core->items->findAND("id", $_CONTENT["item"], "steamid", $User->steamid);
                if(!isset($Item)) ThrowAPIError(403);

                $hStrangeData = $Item->getStrangeData();
                $hStrangeData->addPointsToPart($_CONTENT["part_id"], $_CONTENT["increment_value"]);
                break;

            case 'server_info':
                $SteamIDs = $_CONTENT["steamids"] ?? [];

                foreach ($SteamIDs as $steamid)
                {
                    $User = $Core->users->find("steamid", $steamid);
                    if(isset($User))
                    {
                        $User->updateServerPresenceKeepSession($Core->Server);
                    }
                }
                break;

            // Called when a player leaves the server.
            case 'player_join':
                $User = $Core->users->find("steamid", $_CONTENT["steamid"]);
                if(!isset($User))
                {
                    // Create new user if not exists yet.
                    $User = $Core->users->create($_CONTENT["steamid"]);
                }
                if(!isset($User)) ThrowAPIError(403);

                $User->updateServerPresenceNewSession($Core->Server);
                break;

            // Called when a player leaves the server.
            case 'player_left':
                $User = $Core->users->find("steamid", $_CONTENT["steamid"]);
                if(!isset($User)) ThrowAPIError(403);

                // Make sure we only nullify the presence info if saved server id matches requester server id.
                if($User->presence->server->id == $Core->Server->id)
                {
                    $User->clearServerPresence();
                }
                break;

            case 'quest_progress':

                $User = $Core->users->find("steamid", $_CONTENT["steamid"]);
                if(!isset($User)) ThrowAPIError(403);

                $Quest = $User->getContracker()->getQuest($_CONTENT["contract"]);
                if(!isset($Quest)) ThrowAPIError(403);

                $bIsTrusted = ($_CONTENT["trusted"] ?? 0) == 1;

                // Make sure we reset unsaved progress.
                $Quest->verifyUnsavedProgress();
                $Progress = $Quest->getProgress();

                $Progress->setValue("unsaved_server", $Core->Server->id);
                $Progress->setValue("unsaved_join_time", $User->presence->join_time ?? 0);

                foreach ($_CONTENT["points"] as $objective => $points)
                {

                    $iPoints = (+$points);

                    // $iPoints at this point contains doubled saved progress,
                    // because we can't detect how much progress we've made
                    // on a server.
                    // * Formula: Saved + Saved + Unsaved.
                    //
                    // So we just substract Saved amount one time to get actual progress.
                    // But we only do this if our progress is not trusted (automatically saved.)
                    if(!$bIsTrusted)
                    {
                        $iPoints = (+$points) - $Quest->getObjective($objective)->getProgress();
                    }

                    // Make sure we don't accidentally lower the progress.
                    // If saved progress is greater than suggested points amount,
                    // we skip it.
                    if($Quest->getObjective($objective)->getUnsavedProgress() > $iPoints) continue;

                    if($bIsTrusted)
                    {
                        $Quest->getObjective($objective)->setProgress($iPoints);
                    } else {
                        $Quest->getObjective($objective)->setUnsavedProgress($iPoints);
                    }
                }

                $Progress->save();
                break;

            case 'quest_save':

                $Users = $_CONTENT["users"] ?? [];
                foreach ($Users as $UserData)
                {
                    $User = $Core->users->find("steamid", $UserData["steamid"]);
                    if(!isset($User)) continue;

                    $Quest = $User->getContracker()->getQuest($UserData["contract"]);
                    if(!isset($Quest)) continue;


                    // Make sure we reset unsaved progress.
                    $Quest->verifyUnsavedProgress();

                    $Quest->saveUnsavedProgress();
                    $Quest->getProgress()->save();
                }
                break;

            case 'campaign_increment':
                $User = $Core->users->find("steamid", $_CONTENT["steamid"]);
                if(!isset($User)) ThrowAPIError(403);

                $Campaign = $User->getContracker()->getCampaign($_CONTENT["campaign"]);
                if(!isset($Campaign)) ThrowAPIError(403);

                $Campaign->addProgressPoints(+($_CONTENT["increment_value"] ?? 0));
                break;
        }
    }

}else{
    http_response_code(404);
}
?>
