<?php
if(!defined("INCLUDED")) die("Access forbidden.");

  $SORT_FIELD; $SORT_DIR;
  $SORT_FIELD = $_GET["sort_by"] ?? NULL;
  $SORT_DIR = $_GET["method"] ?? NULL;
  if(!isset($_GET["sort_by"]) || !in_array($_GET["sort_by"], ["id", "region", "hostname", "online", "map", "heartbeat", "status"])) $SORT_FIELD = "id";
  if(!isset($_GET["method"]) || !in_array($_GET["method"], ["ASC", "DESC"])) $SORT_DIR = "ASC";

  $Servers = [];

  if(in_array($SORT_FIELD, ["id", "region"])){
    $Servers = $Core->db->getAllRows("SELECT *, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(cache_ts)) as seconds FROM `tf_servers` WHERE is_cached = 1 ORDER BY $SORT_FIELD $SORT_DIR");
  }else if($SORT_FIELD == "heartbeat"){
    $Servers = $Core->db->getAllRows("SELECT *, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(cache_ts)) as seconds FROM `tf_servers` WHERE is_cached = 1 ORDER BY seconds $SORT_DIR");
  }else{
    $Servers = $Core->db->getAllRows("SELECT *, (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(cache_ts)) as seconds FROM `tf_servers` WHERE is_cached = 1");
  }

  $S = array_map(function($ar){
      $a = explode(",",$ar["cache"]);
      $Data = [];
      foreach ($a as $b) {
        $k = explode("=", $b)[0];
        $v = explode("=", $b)[1];
        $Data[$k] = $v;
      }
      if(isset($Data["m"]))
      {
        $f = array_map(function($word) { return ucfirst($word); }, explode("_", $Data["m"]));
        $f[0] = strtoupper($f[0]);
        if(is_numeric(substr(end($f), -1))) array_splice($f, -1, 1);
      }
      return [
        "id" => $ar["id"],
        "region" => $ar["region"],
        "hostname" => $Data["h"] ?? "~",
        "online" => (integer) $Data["o"],
        "maxplayers" => (integer) $Data["mp"],
        "map" => $Data["m"],
        "map_f" => isset($Data["m"]) ? join($f," ") : "~",
        "time" => (integer) $ar["seconds"],
        "ip" => $ar["ip"],
        "port" => $ar["port"],
        "group" => $ar["srv_group"],
        "locked" => $Data["p"] ?? 0
      ];
    }, $Servers);

    if($SORT_FIELD == "hostname"){
      uasort($S, function($a, $b){
        return strcmp($a["hostname"], $b["hostname"]);
      });
      if($SORT_DIR == "DESC") $S = array_reverse($S);
    }

    if($SORT_FIELD == "map"){
      uasort($S, function($a, $b){
        return strcmp($a["map_f"], $b["map_f"]);
      });
      if($SORT_DIR == "DESC") $S = array_reverse($S);
    }

    if($SORT_FIELD == "online"){
      uasort($S, function($a, $b){
        if($a["online"] < $b["online"]) return 1;
        if($a["online"] > $b["online"]) return -1;
        if($a["online"] == $b["online"]) return 0;
      });
      if($SORT_DIR == "DESC") $S = array_reverse($S);
    }
    $Content = render('pages/serverlist/list',[
      "servers" => join(array_map(function($s){
        return render('pages/serverlist/server',
        [
          "id" => $s["id"],
          "region" => $s["region"],
          "hostname" => $s["hostname"],
          "online" => $s["online"],
          "maxplayers" => $s["maxplayers"],
          "map" => $s["map"],
          "map_f" => $s["map_f"],
          "time" => $s["time"],
          "ip" => $s["ip"],
          "port" => $s["port"]
        ],
        [
          "TIMEOUT" => $s["time"] >= 40,
          "OK" => $s["time"] < 40,
          "LOCKED" => $s["locked"] == 1,
          "UNLOCKED" => $s["locked"] != 1
        ]);
      },array_filter($S, function($b){return $b["group"] == 0;})),""),
      "servers_balancemod" => join(array_map(function($s){
        return render('pages/serverlist/server',
        [
          "id" => $s["id"],
          "region" => $s["region"],
          "hostname" => $s["hostname"],
          "online" => $s["online"],
          "maxplayers" => $s["maxplayers"],
          "map" => $s["map"],
          "map_f" => $s["map_f"],
          "time" => $s["time"],
          "ip" => $s["ip"],
          "port" => $s["port"]
        ],
        [
          "TIMEOUT" => $s["time"] >= 40,
          "OK" => $s["time"] < 40,
          "LOCKED" => $s["locked"] == 1,
          "UNLOCKED" => $s["locked"] != 1
        ]);
      },array_filter($S, function($b){return $b["group"] == 1;})),""),
      "id_dir" => $SORT_FIELD == "id" ? ($SORT_DIR == "ASC" ? "DESC" : "ASC") : "ASC",
      "region_dir" => $SORT_FIELD == "region" ? ($SORT_DIR == "ASC" ? "DESC" : "ASC") : "ASC",
      "hostname_dir" => $SORT_FIELD == "hostname" ? ($SORT_DIR == "ASC" ? "DESC" : "ASC") : "ASC",
      "online_dir" => $SORT_FIELD == "online" ? ($SORT_DIR == "ASC" ? "DESC" : "ASC") : "ASC",
      "map_dir" => $SORT_FIELD == "map" ? ($SORT_DIR == "ASC" ? "DESC" : "ASC") : "ASC",
      "heartbeat_dir" => $SORT_FIELD == "heartbeat" ? ($SORT_DIR == "ASC" ? "DESC" : "ASC") : "ASC",

      "id_arrow" => $SORT_FIELD == "id" ? ($SORT_DIR == "ASC" ? "▼" : "▲") : "",
      "region_arrow" => $SORT_FIELD == "region" ? ($SORT_DIR == "ASC" ? "▼" : "▲") : "",
      "hostname_arrow" => $SORT_FIELD == "hostname" ? ($SORT_DIR == "ASC" ? "▼" : "▲") : "",
      "online_arrow" => $SORT_FIELD == "online" ? ($SORT_DIR == "ASC" ? "▼" : "▲") : "",
      "map_arrow" => $SORT_FIELD == "map" ? ($SORT_DIR == "ASC" ? "▼" : "▲") : "",
      "heartbeat_arrow" => $SORT_FIELD == "heartbeat" ? ($SORT_DIR == "ASC" ? "▼" : "▲") : ""
    ]);
?>
