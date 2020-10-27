<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("USER_RP_SERVER_PERIOD", 20); // 20 seconds.
$_ORDER_QUALITIES = [8,9,7,5,14,11,2,4,10,12,15,1,3,6,0];
$_ORDER_TYPES = ["tool", "action", "cosmetic", "weapon", "taunt"];
$_ORDER_CLASSES = ["scout", "soldier", "pyro", "demoman", "heavy", "engineer", "medic", "sniper", "spy"];

class User extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);

        $this->id = (int)$data["id"];
        $this->steamid = $data["steamid"];
        $this->avatar = $data["avatar"];
        $this->name = $data["name"];
        $this->alias = $data["alias"] ?? $this->steamid;
        $this->token = $data["token"];
        $this->motd = $data["motd"];
        $this->backpack_pages = (int) ($data["backpack_pages"] ?? $this->m_hCore->config->economy->default_pages);

        // This is for backwards compatibility.
        $this->items_slotted = ($data["backpack_pages"] ?? NULL) !== NULL;

        if((+$data["queried"]) > 0 || ($this->avatar == NULL))
        {
            $this->import();
            $this->m_hCore->dbh->query("UPDATE tf_users SET queried = 0 WHERE id = %s", [$this->id]);
        }

        $this->bans = (int) $data["bans"];
        $this->admin = (int) $data["admin"];
        $this->special = (int) $data["special"];

        $this->real = !($this->steamid < 4000);
        $this->contract = (int)$data["contract"];
        $this->credit = (int)$data["credit"];

        $this->presence = json_decode(($data["presence"] ?? NULL), false) ?? new stdClass();
        $this->connections = json_decode($data["connections"], false) ?? new stdClass();
        $this->loadout = json_decode($data["loadout"],false) ?? new stdClass();
        foreach ($this->m_hCore->config->economy->classes as $class) {
            if(
                is_array($this->loadout->{$class}) ||
                !isset($this->loadout->{$class})
            ) $this->loadout->{$class} = new stdClass();
        }

        $this->settings = json_decode($data["settings"], true) ?? [];
    }

    function flush()
    {
        $this->getCore()->users->removeFromCache("id", $this->id);
    }

    function getNotifications()
    {
        if(isset($this->__notifications)) return $this->__notifications;

        $hNotifications = $this->m_hCore->dbh->getAllRows("SELECT * FROM tf_notifications where steamid = %s", [
            $this->steamid
        ]);

        foreach ($hNotifications as $key => $value)
        {
            $hNotifications[$key] = new Notification($value, $this->m_hCore);
        }

        $this->__notifications = $hNotifications;
        return $hNotifications;
    }

    function getNotificationsOfType($type)
    {
        $Return = [];
        foreach ($this->getNotifications() as $Notif)
        {
            if($Notif->getType() != $type) continue;
            array_push($Return, $Notif);
        }
        return $Return;
    }

    function purgeNotificationsOfType($type)
    {
        $this->m_hCore->dbh->query("DELETE FROM tf_notifications WHERE steamid = %s AND type = %s", [
            $this->steamid,
            $type
        ]);
    }

    function sortBackpack($type)
    {
        $Backpack = $this->getBackpack();
        if(count($Backpack) == 0) return;
        if($type == BPSORT_CLASS) {
            // Sort by count of the classes, if they're equal, sort by specific class
            usort($Backpack, function($a, $b) {
                global $_ORDER_TYPES;
                global $_ORDER_CLASSES;

                $OA = count(array_keys($a->def->used_by_classes ?? []));
                $OB = count(array_keys($b->def->used_by_classes ?? []));

                // If class count for both items is both then one we sort by the count.
                if( $OA > 1 && $OB > 1 )
                {
                    return ($OA > $OB) ? -1 : 1;
                } else if ($OA == 1 && $OB > 1 || $OA > 1 && $OB == 1) {
                    // If one of them is one and other is greater than zero we prioritize greater.
                    if( $OA == 1 && $OB > 1 )
                        return 1;
                    if( $OA > 1 && $OB == 1 )
                        return -1;

                } else {
                    // In this case both counts are equal, so we prioritize class by order.
                    $OA = array_search(array_keys($a->def->used_by_classes ?? [])[0] ?? -1, $_ORDER_CLASSES);
                    $OB = array_search(array_keys($b->def->used_by_classes ?? [])[0] ?? -1, $_ORDER_CLASSES);

                    if($OA === false) return -1;
                    if($OB === false) return 1;
                    if($OA == $OB)
                    {
                        if ($a->def->type == $b->def->type)
                        {
                            // If types are equal, we sort by defid.
                            if ($a->defid == $b->defid)
                                return 0;
                            return ($a->defid < $b->defid) ? -1 : 1;
                        } else {
                            $OA = array_search($a->def->type, $_ORDER_TYPES);
                            $OB = array_search($b->def->type, $_ORDER_TYPES);
                            return ($OA < $OB) ? -1 : 1;
                        }
                    } else return ($OA < $OB) ? -1 : 1;
                }
                if (
                    count(array_keys($a->def->used_by_classes ?? [])) ==
                    count(array_keys($b->def->used_by_classes ?? []))
                ) {
                    // If class counts are equal we sort by class index.
                    $OA = array_search($a->def->type, $_ORDER_TYPES);
                    $OB = array_search($b->def->type, $_ORDER_TYPES);
                }else{
                    $OA = array_search($a->def->type, $_ORDER_TYPES);
                    $OB = array_search($b->def->type, $_ORDER_TYPES);
                    return ($OA < $OB) ? -1 : 1;
                }
            });
        }
        if($type == BPSORT_TYPE) {
            usort($Backpack, function($a, $b) {
                global $_ORDER_TYPES;
                if ($a->def->type == $b->def->type)
                {
                    // If types are equal, we sort by defid.
                    if ($a->defid == $b->defid)
                        return 0;
                    return ($a->defid < $b->defid) ? -1 : 1;
                }else{
                    $OA = array_search($a->def->type, $_ORDER_TYPES);
                    $OB = array_search($b->def->type, $_ORDER_TYPES);
                    return ($OA < $OB) ? -1 : 1;
                }
            });
        }
        if($type == BPSORT_QUALITY)
        {
            usort($Backpack, function($a, $b) {
                global $_ORDER_QUALITIES;
                global $_ORDER_TYPES;

                if ($a->quality == $b->quality) {
                    // If qualities are equal, we sort by type.
                    if ($a->def->type == $b->def->type)
                    {
                        // If types are equal, we sort by defid.
                        if ($a->defid == $b->defid)
                            return 0;
                        return ($a->defid < $b->defid) ? -1 : 1;
                    }else{
                        $OA = array_search($a->def->type, $_ORDER_TYPES);
                        $OB = array_search($b->def->type, $_ORDER_TYPES);
                        return ($OA < $OB) ? -1 : 1;
                    }
                }else {
                    $OA = array_search($a->quality, $_ORDER_QUALITIES);
                    $OB = array_search($b->quality, $_ORDER_QUALITIES);
                    return ($OA < $OB) ? -1 : 1;
                }
            });
        }

        $Values = [];
        $Cases = "";
        for($i = 0; $i < count($Backpack); $i++)
        {
            $Cases .= "WHEN (id = %d) THEN %d ";
            array_push($Values, $Backpack[$i]->id, $i);
        }

        $q = format("UPDATE tf_pack SET slot = CASE %s END WHERE id IN (%s)", [
            $Cases,
            implode(',', array_fill(0, count($Backpack), '%d'))
        ]);

        $this->m_hCore->dbh->query($q, array_merge($Values, array_map(function($a){ return $a->id; }, $Backpack)));
    }

    function getMaxBackpackSlots()
    {
        return $this->backpack_pages * ($this->m_hCore->config->economy->items_per_page ?? 1);
    }

    function canGetMoreItems()
    {
        return $this->getBackpackSize() < $this->getMaxBackpackSlots();
    }

    function getBackpackSize()
    {
        return count($this->getBackpack());
    }

    function addBackpackPages($count)
    {
        $new_count = $this->backpack_pages + (+$count);
        $this->setBackpackPages($new_count);
    }

    function setBackpackPages($count)
    {
        if( isset($this->m_hCore->config->economy->max_pages) &&
            $count > $this->m_hCore->config->economy->max_pages
        ) $count = $this->m_hCore->config->economy->max_pages;

        $this->backpack_pages = $count;
        $this->m_hCore->dbh->query("UPDATE tf_users SET backpack_pages = %d WHERE id = %d",
        [
            $this->backpack_pages,
            $this->id
        ]);
        $this->flush();
    }

    function hasPermission($bit)
    {
        if(!is_integer($bit)) throw new TypeError("Argument #1 must be an int.");
        if($this->admin & ADMINFLAG_ROOT == ADMINFLAG_ROOT) return true;
        return ($this->admin & $bit) == $bit;
    }

    function hasAdminBit($bit)
    {
        if(!is_integer($bit)) throw new TypeError("Argument #1 must be an int.");
        if($this->admin & ADMINFLAG_ROOT == ADMINFLAG_ROOT) return true;
        return ($this->admin & $bit) == $bit;
    }

    function canSeeInventory($User)
    {
        if($this->canSeeProfile($User) === false) return false;
        if(isset($User))
        {
            if($this->id == $User->id) return true;
            else return !(isset($this->settings["privacy"]["hideInventory"]) && $this->settings["privacy"]["hideInventory"] == "all");
        }else{
            return !(isset($this->settings["privacy"]["hideInventory"]) && $this->settings["privacy"]["hideInventory"] == "all");
        }
    }

    function toDOMSpecial($tags = [], $brackets = [])
    {
        switch ($this->special) {
            case USERSPECIAL_VERIFIED: return render("users/special_verified", $tags, $brackets); break;
            case USERSPECIAL_BOT: return render("users/special_bot", $tags, $brackets); break;
            case USERSPECIAL_BLOG: return render("users/special_news", $tags, $brackets); break;
            default: return null; break;
        }
    }

    function toDOMEmbed($tags = [], $brackets = [])
    {
        return render(
            "embed/user",
            array_merge([
                "steamid" => $this->alias ?? $this->steamid,
                "avatar" => $this->avatar,
                "name" => $this->name,
                "status" => "",
                "special" => $this->toDOMSpecial()
            ], $tags),
            $brackets
        );
    }

    function canSeeProfile($User)
    {
        if(isset($User))
        {
            if($this->id == $User->id) return true;
            else return !(isset($this->settings["privacy"]["hideProfile"]) && $this->settings["privacy"]["hideProfile"] == "all");
        }else{
            return !(isset($this->settings["privacy"]["hideProfile"]) && $this->settings["privacy"]["hideProfile"] == "all");
        }
    }

    function ConnectIntegration($name, $data)
    {
        $this->connections->{$name} = $data;
        $this->m_hCore->dbh->query("UPDATE tf_users SET connections = %s WHERE id = %s",[
            json_encode($this->connections),
            $this->id
        ]);
        $this->flush();
    }

    function DisconnectIntegration($name)
    {
        $this->connections->{$name} = NULL;
        $this->m_hCore->dbh->query("UPDATE tf_users SET connections = %s WHERE id = %d", [
            json_encode($data),
            $this->id
        ]);
        $this->flush();
    }

    function bump()
    {
        $this->m_hCore->dbh->query("UPDATE tf_users SET lastlogin = NOW() WHERE id = %i", [$this->id]);
    }

    function clearServerPresence()
    {
        $this->updateServerPresence(null);
    }

    function updateServerPresenceNewSession($server)
    {
        $this->updateServerPresence($server, true);
    }

    function updateServerPresenceKeepSession($server)
    {
        $this->updateServerPresence($server, false);
    }

    function updateServerPresence($server, $joined = false)
    {
        $data = null;
        if(isset($server))
        {
            $data = json_encode([
                "server" => [
                    "id" => $server->id,
                    "ip" => $server->ip,
                    "port" => $server->port
                ],
                "time" => time(),
                "join_time" => $joined ? time() : ($this->presence->join_time ?? 0)
            ]);
        }

        $this->m_hCore->dbh->query("UPDATE tf_users SET presence = %s WHERE steamid = %s", [
            $data,
            $this->steamid
        ]);
        $this->flush();
    }

    function rememberItem($Item)
    {
        if(!isset($this->__backpack)) return;
        array_push($this->__backpack, $Item);
    }

    function getOwnedItems()
    {
        if(isset($this->__backpack)) return $this->__backpack;

        $Items = $this->m_hCore->dbh->getAllRows("SELECT * FROM tf_pack where steamid = %s", [
            $this->steamid
        ]);

        foreach ($Items as $key => $value) {
            $Items[$key] = new Item($value, $this->m_hCore);
        }

        $this->__backpack = $Items;
        return $Items;
    }

    function getLoanerItems()
    {
        $Owned = $this->getOwnedItems();

        $Items = [];
        foreach ($Owned as $Item)
        {
            if(!$Item->isLoaner()) continue;
            array_push($Items, $Item);
        }

        return $Items;
    }

    function getBackpack()
    {
        $Owned = $this->getOwnedItems();
        $MaxSlots = $this->getMaxBackpackSlots();

        $Items = [];
        foreach ($Owned as $Item) {
            if($Item->slot >= $MaxSlots) continue;
            array_push($Items, $Item);
        }

        return $Items;
    }

    function getOverflowItems()
    {
        $Owned = $this->getOwnedItems();
        $MaxSlots = $this->getMaxBackpackSlots();

        $Items = [];
        foreach ($Owned as $Item) {
            if($Item->slot < $MaxSlots) continue;
            array_push($Items, $Item);
        }

        return $Items;
    }

    function getOwnedItemsByDefinitionIndex($defid)
    {
        $Items = [];

        foreach ($this->getOwnedItems() as $Item) {
            if($Item->defid == $defid)
            {
                array_push($Items, $Item);
            }
        }
        return $Items;
    }

    function getItemByItemIndex($id)
    {
        foreach ($this->getOwnedItems() as $Item)
        {
            if($Item->id == $id)
            {
                return $Item;
            }
        }
        return NULL;
    }

    function getFirstOverflowItem()
    {
        $Items = $this->getOverflowItems();
        $Item = reset($Items);

        if($Item === false) return NULL;
        return $Item;
    }

    function setLoadout($Loadout)
    {
        $this->loadout = $Loadout;

        $this->m_hCore->dbh->query("UPDATE tf_users SET loadout = %s WHERE id = %d",[
            json_encode($Loadout ?? []),
            $this->id
        ]);
        $this->flush();
    }

    function chargeCurrency($amount)
    {
        $this->m_hCore->dbh->query(
            "UPDATE tf_users SET credit = credit - %s WHERE id = %s",
            [
                $amount,
                $this->id
            ]
        );
        $this->credit = $this->credit - $amount;
        $this->flush();
    }

    function getLoadout()
    {
        $Indexes = [];

        foreach ($this->m_hCore->config->economy->classes as $Class) {
          foreach ((array) ($this->loadout->{$Class} ?? []) as $Slot => $idx) {
            array_push($Indexes, $idx);
          }
        }

        $Items = [];
        if(count($Indexes) > 0)
        {
            $Backpack = $this->getBackpack();
            foreach ($Backpack as $Item) {
                if(in_array($Item->id, $Indexes))
                {
                    array_push($Items, $Item);
                }
            }
        }

        return new Loadout([
            "owner" => $this,
            "items" => $Items
        ], $this->m_hCore);
    }

    function getContracker()
    {
        return new Contracker([
            "owner" => $this
        ], $this->m_hCore);
    }

    function getProgress()
    {
        if(isset($this->__progress)) return $this->__progress;

        $Progress = $this->m_hCore->dbh->getAllRows("SELECT * FROM tf_progress_new WHERE steamid = %s", [
            $this->steamid
        ]);

        foreach ($Progress as $key => $value)
        {
            $Progress[$key] = new Progress($value, $this->m_hCore);
        }

        $this->__progress = $Progress;
        return $Progress;
    }

    function isOnServer()
    {
        return (($this->presence->time ?? 0) + USER_RP_SERVER_PERIOD) > time();
    }


    function getServer()
    {
        if(!$this->isOnServer()) return NULL;
        return $this->m_hCore->servers->find("id", $this->presence->server->id);
    }

    function queryServerJob($msg)
    {
        $Server = $this->getServer();
        if(isset($Server))
        {
            $Server->queryJob($msg);
        }
    }

    function import()
    {
        $url = format(
            "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s",
            [
                $this->m_hCore->config->api->steam,
                $this->steamid
            ]
        );

        $json_object= file_get_contents($url);
        $json_decoded = json_decode($json_object);

        $name = htmlentities($json_decoded->response->players[0]->personaname);
        $avatar = $json_decoded->response->players[0]->avatarfull;

        $this->m_hCore->dbh->query(
            "UPDATE tf_users SET name = %s, avatar = %s WHERE id = %d",
            [
                $name,
                $avatar,
                $this->id
            ]
        );

        $this->name = $name;
        $this->avatar = $avatar;
    }
}
?>
