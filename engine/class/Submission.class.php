<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Submission extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->m_hCore = $core;
        $this->id = (int) $data["id"];
        $this->name = $data["name"];
        $this->owner = $data["owner"];

        $this->authors = json_decode($data["authors"],true);
        if(!isset($this->authors)) $this->authors = [];

        $this->description = $data["descr"];

        $this->images = json_decode($data["images"],false);
        if(!isset($this->images)) $this->images = [];

        $this->tags = json_decode($data["tags"],true);
        if(!isset($this->tags)) $this->tags = [];

        $this->download_link = $data["download_link"];
        $this->workshop_id = $data["workshop_id"];
        $this->created_at = $data["created_at"];
        $this->updated_at = $data["updated_at"];
        $this->thumb = $data["thumb"];
        if(!isset($this->thumb) || $this->thumb == "") {
        $this->thumb = $this->images[0];
        }

        $this->status = (int) $data["status"];
        $this->updated = ($data["updated"] ?? 0) == 0;
        $this->archived = ($data["trashed"] ?? 0) == 1;
    }

  function comment($Author, $content)
  {
    $this->m_hCore->comments->create($Author, "[S:".$this->id."]", $content);
  }

  function trash()
  {
    // We clean up all records about this submission and set "trashed" to 1.
    $this->m_hCore->dbh->query("DELETE FROM tf_actions WHERE targetid = %s", ['[S:'.$this->id.']']);
    $this->m_hCore->dbh->query("UPDATE tf_submissions SET trashed = 1, download_link = NULL WHERE id = %d", [$this->id]);
  }

  function getType()
  {
    if(($a = array_ksearch($this->tags, "name", "Type")) !== null)
    {
      $a = $this->tags[$a]["value"][0];
    }
    return $a;
  }

  function getRating()
  {
    if(isset($this->__rating)) return $this->__rating;

    $a = $this->m_hCore->dbh->getRow(
      "SELECT
        sum(case when action = 'rate_pos' then 1 else 0 end) AS pos,
        sum(case when action = 'rate_neg' then 1 else 0 end) AS neg
      FROM tf_actions WHERE targetid = %s AND (action = 'rate_pos' or action = 'rate_neg')",
      [
        '[S:'.$this->id.']',
      ]
    );

    if($a["pos"] == null) $a["pos"] = 0;
    if($a["neg"] == null) $a["neg"] = 0;

    $this->__rating = $a;
    return $a;
  }

  function getRateState($steamid)
  {
    $a = $this->m_hCore->dbh->getRow("SELECT action FROM tf_actions WHERE steamid = %s AND targetid = %s AND (action = 'rate_pos' OR action = 'rate_neg')", [
      $steamid,
      '[S:'.$this->id.']',
    ]) ["action"];

    switch ($a) {
      case 'rate_pos': return true; break;
      case 'rate_neg': return false; break;
      default: return null; break;
    }
  }

  function setRateState($steamid, $state)
  {
    $Query = $this->m_hCore->dbh->getRow(
      "SELECT id, action FROM tf_actions WHERE (action = 'rate_neg' OR action = 'rate_pos') AND targetid = %s AND steamid = %s",
      [
        '[S:'.$this->id.']',
        $steamid
      ]
    );

    if(!isset($Query))
    {
      $this->m_hCore->dbh->query(
        "INSERT INTO tf_actions (steamid, targetid, action, created) VALUES ( %s, %s, %s, NOW())",
        [
          $steamid,
          '[S:'.$this->id.']',
          $state === true ? "rate_pos" : "rate_neg"
        ]
      );
    }else{
      if($Query["action"] == ($state === true ? "rate_pos" : "rate_neg")) return;
      $this->m_hCore->dbh->query(
        "UPDATE tf_actions SET action = %s, created = NOW() WHERE id = %d",
        [
          $state === true ? "rate_pos" : "rate_neg",
          $Query["id"]
        ]
      );
    }
  }

  function setViewState($steamid, $state)
  {
    if($state === true)
    {
      if($this->m_hCore->dbh->getRow(
        "SELECT count(*) as count FROM tf_actions WHERE action = 'view' AND targetid = %s AND steamid = %s",
        [
          '[S:'.$this->id.']',
          $steamid
        ]
      )["count"] == 0)
      {
        $this->m_hCore->dbh->query(
          "INSERT INTO tf_actions (steamid, targetid, action, created) VALUES (%s, %s, 'view', NOW())",
          [
            $steamid,
            '[S:'.$this->id.']',
          ]
        );
      }
    }else{
      $this->m_hCore->dbh->query(
        "DELETE FROM tf_actions WHERE targetid = %s AND steamid = %s",
        [
          '[S:'.$this->id.']',
          $steamid
        ]
      );
    }
  }

  function toDOMSquare()
  {
    $_RATING = $this->getRating();
    $_RATING_SUM = $_RATING["pos"] + $_RATING["neg"];

    // TODO: Make it part of the config.
    // Also change it from zero to something like 50. Just so we don't have 5/5 for just one vote.
    if($_RATING_SUM == 0)
      $_RATING_STARS = 0;
    else
      $_RATING_STARS = round($_RATING["pos"] / $_RATING_SUM * 5);

    $tags = join(array_map(function($i){return join($i["value"], ", ");}, $this->tags), ", ");
    return render("embed/submission_square", [
      "id" => $this->id,
      "name" => $this->name,
      "thumb" => $this->thumb,
      "status" => $tags,
      "rating.stars.total" => $_RATING_STARS,
      "rating.votes.total" => $_RATING_SUM
    ], [
      "STATUS_MODERATION" => $this->status == 0,
      "STATUS_PENDING" => $this->status == 1,
      "STATUS_COMPATIBLE" => $this->status == 2,
      "STATUS_INTRODUCED" => $this->status == 3,
      "STATUS_ADDED" => $this->status == 4,
      "STATUS_INCOMPATIBLE" => $this->status == 5,

      "STARS_0" => $_RATING_STARS == 0,
      "STARS_1" => $_RATING_STARS == 1,
      "STARS_2" => $_RATING_STARS == 2,
      "STARS_3" => $_RATING_STARS == 3,
      "STARS_4" => $_RATING_STARS == 4,
      "STARS_5" => $_RATING_STARS == 5
    ]);
  }

  function toDOM()
  {
    $_RATING = $this->getRating();
    $_RATING_SUM = $_RATING["pos"] + $_RATING["neg"];

    // TODO: Make it part of the config.
    // Also change it from zero to something like 50. Just so we don't have 5/5 for just one vote.
    if($_RATING_SUM == 0)
      $_RATING_STARS = 0;
    else
      $_RATING_STARS = round($_RATING["pos"] / $_RATING_SUM * 5);

    $tags = join(array_map(function($i){return join($i["value"], ", ");}, $this->tags), ", ");
    return render("embed/submission", [
      "id" => $this->id,
      "name" => $this->name,
      "thumb" => $this->thumb,
      "status" => $tags,
      "rating.stars.total" => $_RATING_STARS,
      "rating.votes.total" => $_RATING_SUM
    ], [
      "STATUS_MODERATION" => $this->status == 0,
      "STATUS_PENDING" => $this->status == 1,
      "STATUS_COMPATIBLE" => $this->status == 2,
      "STATUS_INTRODUCED" => $this->status == 3,
      "STATUS_ADDED" => $this->status == 4,
      "STATUS_INCOMPATIBLE" => $this->status == 5,

      "STARS_0" => $_RATING_STARS == 0,
      "STARS_1" => $_RATING_STARS == 1,
      "STARS_2" => $_RATING_STARS == 2,
      "STARS_3" => $_RATING_STARS == 3,
      "STARS_4" => $_RATING_STARS == 4,
      "STARS_5" => $_RATING_STARS == 5
    ]);
  }

  function setStatus($status)
  {
    if(!is_numeric($status)) throw new TypeError("Argument #1 must be an integer.");
    $this->m_hCore->dbh->query("UPDATE tf_submissions SET status = %s WHERE id = %s",[
      $status,
      $this->id
    ]);
  }

  function setUpdateState($status)
  {
    if(!is_numeric($status)) throw new TypeError("Argument #1 must be an integer.");
    $this->m_hCore->dbh->query("UPDATE tf_submissions SET updated = %d WHERE id = %d",[
      $status,
      $this->id
    ]);
  }

  function getUniqueViewers()
  {
    if(isset($this->__viewers)) return $this->__viewers;
    $a = $this->m_hCore->db->getRow(format("SELECT count(*) as `count` FROM tf_actions WHERE targetid = '[S:%s]' AND action = 'view'",[$this->id]))["count"];
    $this->__viewers = $a;
    return $a;
  }

  function getAuthors()
  {
    if(isset($this->__authors)) return $this->__authors;
    $a = $this->m_hCore->db->getAllRows(format("SELECT * FROM tf_users WHERE steamid IN (%s)", [
      join(array_map(function($b){return "'".$b."'";}, $this->authors), ", ")
    ]));
    $b = [];
    foreach ($a as $c) {
      array_push($b, new User($c, $this->m_hCore));
    }
    $this->__authors = $b;
    return $b;
  }

  function getTags()
  {
    $a = [];
    foreach ($this->tags as $value) {
      if(is_array($value["value"]))
        $a = array_merge($a, $value["value"]);
    }
    return array_unique($a);
  }

    function reimport($Sub, $sTags)
    {
        $this->m_hCore->dbh->query(
            "UPDATE tf_submissions SET authors = %s, name = %s, descr = %s, images = %s, updated_at = NOW(), thumb = %s, tags = %s, trashed = 0 WHERE id = %s",
            [
                json_encode($Sub->getAuthorsSteamID()),
                htmlentities($Sub->getName()),
                strip_tags($Sub->getDesc(), '<b><u><i><br><a><p><img>'),
                json_encode($Sub->getImages()),
                $Sub->getThumb(),
                json_encode($sTags),
                $this->id
            ]
        );
    }

  function setFilesLink($url)
  {
    $this->m_hCore->dbh->query("UPDATE tf_submissions SET download_link = %s WHERE id = %d",[
      htmlentities($url),
      $this->id
    ]);
  }

  function setTags($tags, $clear = false)
  {
    if($clear === true)
    {
      $this->tags = [];
      foreach ($tags as $value) {
        array_push($this->tags, ["name" => $value["name"], "value" => $value["value"]]);
      }
    } else {
      foreach ($tags as $value) {
        $a = array_ksearch($this->tags, "name", $value["name"]);
        if(isset($a))
        {
          array_splice($this->tags, $a, 1);
        }
        array_push($this->tags, ["name" => $value["name"], "value" => $value["value"]]);
      }
    }

    $this->m_hCore->dbh->query("UPDATE tf_submissions SET tags = %s WHERE id = %d", [
      json_encode($this->tags),
      $this->id
    ]);
  }
}

?>
