<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Contracker extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hOwner = $data["owner"];
    }

    function getActiveContract()
    {
        if($this->getOwner()->contract === 0) return NULL;
        return $this->getQuest($this->getOwner()->contract);
    }

    function setContract($contract)
    {
        $this->getOwner()->contract = $contract;
        $this->m_hCore->dbh->query("UPDATE tf_users SET contract = %d WHERE steamid = %s", [$contract, $this->getOwner()->steamid]);
        $this->getOwner()->flush();

        $this->getOwner()->queryServerJob(format(
            "ce_quest_activate %s %s",
            [
                $this->getOwner()->steamid,
                $contract
            ]
        ));

        $Quest = $this->getQuest($contract);
        if(isset($Quest))
        {
            $Quest->distributeLoanerItems();
        }
    }

    // temp function to backport stuff.
    function portOldProgress()
    {
        if($this->getCore()->getCache()->get("port_progress_".$this->getOwner()->steamid) == true) return;

        // Let's do it this way. Contracts there were backported dont have "v_2"
        // label. So we automatically mark these contracts as "Turned In", if they
        // are not in the Halloween campaign and are completed.
        //
        // However we don't disitrbute the reward in this case, which fixes
        // infinite Mann Coins issue.

        $Campaign = $this->getCampaign("halloween");
        foreach ($this->getQuests() as $Quest)
        {
            if(!$Quest->canTurnIn()) continue;

            $bShouldMark = true;
            foreach ($Campaign->getQuests() as $hCampaignQuest)
            {
                if($Quest->m_iIndex == $hCampaignQuest->m_iIndex)
                {
                    $bShouldMark = false;
                }
            }

            if($bShouldMark)
            {
                if($hProgress->getValue("not_bugged") != true)
                {
                    $hProgress = $Quest->getProgress();
                    $hProgress->setValue("turned", true);
                    $hProgress->save();
                }
            }
        }

        $rows = $this->m_hCore->dbh->getAllRows("SELECT * FROM tf_progress WHERE steamid = %s", [
            $this->getOwner()->steamid
        ]);

        foreach ($rows as $row)
        {
            $hJson = [];
            foreach (explode(";", $row["progress"]) as $k => $v)
            {
                $v = (+$v);
                if($v > 0)
                {
                    $hJson["objective_".$k] = $v;
                }
                $hJson["turned"] = $row["turned"] == 1;
            }

            $this->m_hCore->dbh->query("INSERT INTO tf_progress_new (steamid, target, progress) VALUES (%s, %s, %s)", [
                $this->getOwner()->steamid,
                "[Q:".$row["contract"]."]",
                json_encode($hJson)
            ]);
            /*
            $this->m_hCore->dbh->query("DELETE FROM tf_progress WHERE steamid = %s", [
                $this->getOwner()->steamid
            ]);*/
        }
        $this->getCore()->getCache()->set("port_progress_".$this->getOwner()->steamid, true);
    }

    function getOwner()
    {
        return $this->m_hOwner;
    }

    function getDefaultPage()
    {
        foreach ($this->getPages() as $Page) {
            if($Page->isDefault())
                return $Page;
        }
    }

    function getCampaigns()
    {
        $Result = [];
        foreach ($this->m_hCore->Economy["Contracker"]["Campaigns"] as $hCampaign)
        {
            array_push($Result, new ContrackerCampaign([
                "config" => $hCampaign,
                "owner" => $this->getOwner()
            ], $this->m_hCore));
        }
        return $Result;
    }

    function getCampaign($name)
    {
        foreach ($this->getCampaigns() as $hCampaign)
        {
            if($hCampaign->getValue("name") == $name) return $hCampaign;
            if($hCampaign->getValue("title") == $name) return $hCampaign;
        }
        return NULL;
    }

    function getCampaignQuests($name)
    {
        $hCampaign = $this->getCampaign($name);
        if(!isset($hCampaign)) return [];
        return $hCampaign->getQuests();
    }

    function getProgress()
    {
        $Result = [];
        $Progress = $this->getOwner()->getProgress();
        foreach ($Progress as $LocalProgress) {
            foreach ($this->getQuests() as $Quest) {

                if($LocalProgress->m_sTarget == "[Q:".$Quest->m_iIndex."]") {
                    array_push($Result, $Prog);
                }
            }
        }
        return $Result;
    }

    function getPages()
    {
        return $this->getPages_STEP($this->m_hCore->Economy["Contracker"]["Directory"]["nodes"], $this);
    }

    private function getPages_STEP($nodes)
    {
        $Return = [];
        foreach ($nodes ?? [] as $k => $v)
        {
            array_push($Return, new ContrackerPage([
                "owner" => $this->getOwner(),
                "config" => $v
            ], $this->m_hCore));

            $Nodes = $this->getPages_STEP($v["nodes"] ?? [], $this);
            $Return = array_merge($Return, $Nodes);
        }
        return $Return;
    }

    function getQuests()
    {
        $return = [];

        foreach ($this->m_hCore->Economy["Contracker"]["Quests"] as $idx => $config)
        {
            $Quest = new ContrackerQuest([
                "index" => $idx,
                "owner" => $this->getOwner(),
                "config" => $config
            ], $this->m_hCore);
            array_push($return, $Quest);
        }

        return $return;
    }

    function getQuest($name)
    {
        foreach ($this->getQuests() as $Quest) {
            if($Quest->m_iIndex == $name || $Quest->m_hConfig["title"] == $name)
                return $Quest;
        }

        return NULL;
    }

    function getPage($name)
    {
        foreach ($this->getPages() as $Page) {
            if($Page->m_hConfig["title"] == $name)
                return $Page;
        }

        return NULL;
    }
}

?>
