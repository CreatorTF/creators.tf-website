<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Progress extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_sSteamID = $data["steamid"];
        $this->m_sTarget = $data["target"];
        $this->m_hProgress = json_decode($data["progress"] ?? NULL, true) ?? [];

        $this->m_bCreated = ($data["created"] ?? true) === true;
    }

    function getOwner()
    {
        return $this->m_hCore->users->find("steamid", $this->m_sSteamID);
    }

    function getValue($key)
    {
        return $this->m_hProgress[$key] ?? NULL;
    }

    function deleteValue($key)
    {
        unset($this->m_hProgress[$key]);
    }

    function setValue($key, $value = NULL)
    {
        $this->m_hProgress[$key] = $value;
    }

    function save()
    {
        $sJson = json_encode($this->m_hProgress);

        if($this->m_bCreated)
        {
            if($sJson == "[]" || $sJson == "{}")
            {
                $this->m_hCore->dbh->query(
                    "DELETE FROM tf_progress_new WHERE steamid = %s AND target = %s", [
                        $this->m_sSteamID,
                        $this->m_sTarget
                    ]
                );
                $this->m_bCreated = false;
            } else {
                $this->m_hCore->dbh->query(
                    "UPDATE tf_progress_new SET progress = %s WHERE steamid = %s AND target = %s", [
                        $sJson,
                        $this->m_sSteamID,
                        $this->m_sTarget
                    ]
                );
            }
        } else {
            if($sJson == "[]" || $sJson == "{}") return;
            $this->m_hCore->dbh->query(
                "INSERT INTO tf_progress_new (steamid, target, progress) VALUES (%s, %s, %s)", [
                    $this->m_sSteamID,
                    $this->m_sTarget,
                    json_encode($this->m_hProgress)
                ]
            );
        }
    }
}
?>
