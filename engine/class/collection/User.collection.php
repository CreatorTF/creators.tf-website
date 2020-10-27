<?php
if(!defined("INCLUDED")) die("Access forbidden.");
define("MONTHLY_USERS_CACHE_EXPIRATION", 60 * 60 * 12);

class UserCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_users";
        $this->__object = "User";
    }

    function getPlayersThisMonth()
    {
        $iPlayers = $this->m_hCore->getCache()->get("monthly_users");
        if(empty($iPlayers))
        {
            $iPlayers = $this->m_hCore->dbh->getRow("SELECT count(id) as count FROM tf_users WHERE UNIX_TIMESTAMP(lastlogin) > UNIX_TIMESTAMP(NOW()) - %d", [60 * 60 * 24 * 30])['count'];
            $this->m_hCore->getCache()->set("monthly_users", $iPlayers, false, MONTHLY_USERS_CACHE_EXPIRATION);
        }
        return $iPlayers;
    }

    function create($steamid, $q = -1)
    {
        // TODO: Replace this with dynamically generated session tokens.
        // Generating user access token.

        if(!isset($steamid) || $steamid == "") return;

        $user = $this->find("steamid", $steamid);
        if(isset($user)) return $user;

        $token = sha1(random_int(0,100).$steamid).md5(time());
        $this->m_hCore->dbh->query(
            "INSERT INTO tf_users (steamid, token, queried) VALUES (%s,%s,%d)",
            [
                $steamid,
                $token,
                $q
            ]
        );
        return $this->find("steamid", $steamid);
    }

    function getDonators($month, $year)
    {
        $People = $this->m_hCore->dbh->getAllRows(
            "SELECT tf_users.*, tf_pledges.charge_time, tf_pledges.cents_amount, tf_pledges.charger_id, tf_pledges.source, tf_pledges.id as pledge_id
            FROM tf_users
            RIGHT JOIN tf_pledges ON tf_users.connections LIKE CONCAT('%\"patreon\":{\"id\":\"',tf_pledges.charger_id,'\"%')
            WHERE tf_users.connections LIKE '%\"patreon\"%'
            AND MONTH(tf_pledges.charge_time) = %d
            AND YEAR(tf_pledges.charge_time) = %d
            ORDER BY tf_pledges.charge_time ASC",
            [$month, $year]
        );

        for($i = 0; $i < count($People); $i++)
        {
            for($j = 0; $j < count($People); $j++)
            {
                if($j >= $i) break;
                if($People[$i]["steamid"] == $People[$j]["steamid"])
                {
                    $People[$j]["cents_amount"] += $People[$i]["cents_amount"];

                    array_splice($People, $i, 1);
                    $i--;
                    break;
                }
            }
        }

        usort($People, function($a, $b) { return $a["cents_amount"] < $b["cents_amount"] ? 1 : -1;});

        $People = array_map(function($u) {
            return new Donator(
                array_merge($u, ["user" => new User($u, $this->m_hCore)]),
                $this->m_hCore
            );
        }, $People);

        return $People;
    }
}
?>
