<?php

if($_GET["page"] == "submission_edit"){
  $Core->cache->enabled = false;
  if(isset($Core->User))
  {
    $Sub = $Core->submissions->find("id", $_GET["submission"]);
    if(isset($Sub))
    {
      if((in_array($Core->User->steamid, $Sub->authors) || $Core->User->hasPermission(ADMINFLAG_SUBMISSIONS)) && !$Sub->archived)
      {
        $_DATA['page_name'] = "Submissions :: ".$Sub->name." :: Editing";
        $_DATA["og"]["image"] = $Sub->thumb;
        $_DATA["og"]["description"] = htmlentities(strip_tags($Sub->description));

        $Content = render("pages/workshop/submission_edit", [
          "title" => htmlentities($Sub->name),
          "id" => $Sub->id,
          "workshop_id" => $Sub->workshop_id,
          "download_link" => $Sub->download_link,

          "tags.option.types" => join(array_map(function($b){
            global $Sub;
            if($Sub->getType() == $b)
              return "<option value=\"".$b."\" selected>".$b."</option>";
            else
              return "<option value=\"".$b."\">".$b."</option>";
          }, $Core->config->submissions->types), ""),
          "tags.filter.themes" => join(
            array_map(
              function($v)
              {
                global $Sub;
                return render("prefabs/checkbox", ["value" => $v, "key" => "Theme", "custom" => "smaller inline"], ["CHECKED" => in_array($v, $Sub->getTags())]);
              },
              $Core->config->submissions->themes
            ),
            ""
          ),
          "tags.filter.classes" => join(
            array_map(
              function($v)
              {
                global $Sub;
                return render("prefabs/checkbox", ["value" => $v, "key" => "Class", "custom" => "smaller inline"], ["CHECKED" => in_array($v, $Sub->getTags())]);
              },
              $Core->config->submissions->classes
            ),
            ""
          ),
          "tags.filter.gamemodes" => join(
            array_map(
              function($v)
              {
                global $Sub;
                return render("prefabs/checkbox", ["value" => $v, "key" => "GameMode", "custom" => "smaller inline"], ["CHECKED" => in_array($v, $Sub->getTags())]);
              },
              $Core->config->submissions->gamemodes
            ),
            ""
          ),
        ], [
          "TAGS_THEMES" => $Sub->getType() != NULL,
          "TAGS_GAMEMODES" => in_array($Sub->getType(), ["Map"]),
          "TAGS_CLASSES" => in_array($Sub->getType(), ["Cosmetic", "Unusual Effect", "War Paint", "Taunt", "Weapon", "Client Mod"])
        ]);
      } else {
        $Core->error = 403;
      }
    }
  }else{
    $Core->error = 403;
  }
}

if($_GET["page"] == "submission"){
  $Core->cache->enabled = false;
  if(empty($_GET["action"]))
  {
    $Sub = $Core->submissions->find("id", $_GET["submission"]);
    if(isset($Sub) && !$Sub->archived)
    {
      $_DATA['page_name'] = "Submissions :: ".htmlentities($Sub->name);
      $_DATA["og"]["image"] = $Sub->thumb;
      $_DATA["og"]["description"] = htmlentities(strip_tags($Sub->description));

      $Authors = $Sub->getAuthors();

      $_UNKNOWN_AUTHORS = count($Sub->authors) - count($Authors);

      $_MESSAGE = false;
      $_RATING_STATE = null;
      if(isset($Core->User))
      {
        $Sub->setViewState($Core->User->steamid, true);
        $_RATING_STATE = $Sub->getRateState($Core->User->steamid);
      }

      $_RATING = $Sub->getRating();
      $_RATING_SUM = $_RATING["pos"] + $_RATING["neg"];

      // TODO: Make it part of the config.
      // Also change it from zero to something like 50. Just so we don't have 5/5 for just one vote.
      if($_RATING_SUM == 0)
        $_RATING_STARS = 0;
      else
        $_RATING_STARS = round($_RATING["pos"] / $_RATING_SUM * 5);


      $_IMAGES = $Sub->images;
      if(count($_IMAGES) == 0)
      {
        array_push($_IMAGES, $Sub->thumb);
      }

      $Content = render("pages/workshop/submission", [
        "id" => $Sub->id,
        "title" => $Sub->name,
        "thumb" => $Sub->thumb,
        "images" => join(array_map(function($i){return "<img class='cursor-pointer' onclick='CShowFullImage(\"".$i."\")' ignore src='".$i."?imw=580&imh=326&ima=fit&impolicy=Letterbox&imcolor=%23000000&letterbox=true'/>";}, $_IMAGES), ""),
        "description" => $Sub->description,
        "workshop_id" => $Sub->workshop_id,
        "unique_visitors" => $Sub->getUniqueViewers(),
        "authors" => join(array_map(function($u) {
          global $Sub;
          return $u->toDOMEmbed(
            $Sub->owner == $u->id
              ? ["special" => render("users/special_submission_uploader")]
              : []
            );
        }, $Authors), ""),
        "download_link" => $Sub->download_link,
        "tags" => join(array_map(function($i){return '<div class="ws-tag"><span class="ws-tag-name">'.$i["name"].':</span> '.join(array_map(function($j){return "<a href='/submissions?requiredtags[]=".$j."'>".$j."</a>";}, $i["value"]), ", ").'</div>';}, $Sub->tags), ""),

        "rating.stars.total" => $_RATING_STARS,
        "rating.votes.total" => $_RATING_SUM,
        "rating.votes.percentage" => $_RATING_SUM == 0 ? 0 : round($_RATING["pos"] / $_RATING_SUM * 100),
        "rating.votes.balance" => $_RATING["pos"] - $_RATING["neg"],

        "tags.json.types" => json_encode($Core->config->submissions->types),
        "tags.json.themes" => json_encode($Core->config->submissions->themes),
        "tags.json.gamemodes" => json_encode($Core->config->submissions->gamemodes),
        "tags.json.classes" => json_encode($Core->config->submissions->classes),

        "unknown_authors_count" => $_UNKNOWN_AUTHORS,

        "comments" => render("prefabs/comments", [
          "target" => "submission",
          "id" => $Sub->id,
          "count" => 6
        ], [
          "LOGIN" => isset($Core->User)
        ])
      ], [
        "STATUS_MODERATION" => $Sub->status == 0,
        "STATUS_PENDING" => $Sub->status == 1,
        "STATUS_COMPATIBLE" => $Sub->status == 2,
        "STATUS_INTRODUCED" => $Sub->status == 3,
        "STATUS_ADDED" => $Sub->status == 4,
        "STATUS_INCOMPATIBLE" => $Sub->status == 5,

        "VOTE_ENABLED" => in_array($Sub->status, [1,2,3]),
        "VOTE_DISABLED" => !in_array($Sub->status, [1,2,3]),

        "CAN_MANAGE" => isset($Core->User) && (in_array($Core->User->steamid, array_map(function($a){return $a->steamid;}, $Authors)) || $Core->User->hasPermission(ADMINFLAG_SUBMISSIONS)),
        "CAN_ADMIN" => isset($Core->User) && $Core->User->hasPermission(ADMINFLAG_SUBMISSIONS),
        "CAN_MOD" => isset($Core->User) && $Core->User->hasAdminBit(ADMINFLAG_SUBMISSIONS_MODERATOR) && !$Core->User->hasPermission(ADMINFLAG_SUBMISSIONS),
        "UPDATE_NEEDED" => !$Sub->updated,

        "NOT_RATED_POS" => $_RATING_STATE !== true,
        "NOT_RATED_NEG" => $_RATING_STATE !== false,
        "RATED_POS" => $_RATING_STATE === true,
        "RATED_NEG" => $_RATING_STATE === false,

        "STARS_0" => $_RATING_STARS == 0,
        "STARS_1" => $_RATING_STARS == 1,
        "STARS_2" => $_RATING_STARS == 2,
        "STARS_3" => $_RATING_STARS == 3,
        "STARS_4" => $_RATING_STARS == 4,
        "STARS_5" => $_RATING_STARS == 5,

        "UNKNOWN_AUTHORS" => $_UNKNOWN_AUTHORS > 0,

        "SHOULD_HINT" => isset($Core->User) &&
        (
          $Core->User->hasPermission(ADMINFLAG_SUBMISSIONS) ||
          in_array($Core->User->steamid, array_map(function($a){return $a->steamid;}, $Authors))
        ) && isset($_GET["show_hint"])
      ]);
    }else $Core->error = 404;
  }else if($_GET["action"] == "create")
  {
    if(isset($Core->User))
    {
      $Content = render("pages/workshop/new_submission", ["steamid" => $Core->User->steamid]);
    }else {
      $Core->error = ERROR_NO_PERMISSION;
    }
  }
}


if($_GET["page"] == "submissions") {
  $_DATA['page_name'] = "Submissions";
  if(isset($_GET["p"]) && is_numeric($_GET["p"]) && $_GET["p"] > 0) $_OFFSET = ((integer) $_GET["p"]) - 1;
  else $_OFFSET = 0;
  $_PAGE = $_OFFSET + 1;

  if(isset($_GET["sorting"]) && in_array($_GET["sorting"], ["recent"])) $_SORTING = $_GET["sorting"];
  else $_SORTING = "recent";

  $Conditions = [];

  $_FILTER = true;

  $_TAGS = [];
  if(is_array($_GET["requiredtags"] ?? null) && count($_GET["requiredtags"] ?? []) > 0)
  {
    $_FILTER = false;
    $_TAGS = array_merge(
      $Core->config->submissions->types,
      $Core->config->submissions->classes,
      $Core->config->submissions->gamemodes,
      $Core->config->submissions->themes
    );
    $_TAGS = array_intersect($_GET["requiredtags"], $_TAGS);
    $_TAGS = array_unique($_TAGS);
  }
  if(count($_TAGS) > 0)
  {
    $_FILTER = false;
    array_push(
      $Conditions,
      join(array_map(function($i){return format("JSON_CONTAINS(tags,'[{\"value\":[\"%s\"]}]')", [$i]);}, $_TAGS), " AND ")
    );
  }
  $Authors = [];
  $AuthorsSteamIDs = [];
  if(is_array($_GET["authors"] ?? null) && count($_GET["authors"] ?? []) > 0)
  {
    $_FILTER = false;
    $Authors = array_filter($_GET["authors"], function($i){return is_numeric($i);});
    $Authors = array_unique($Authors);
  }
  if(count($Authors) > 0)
  {
    $_FILTER = false;
    array_push(
      $Conditions,
      join(array_map(function($i){
        if(!is_numeric($i)) return;
        return format("JSON_CONTAINS(authors,'[\"%s\"]')", [addslashes($i)]);
      }, $Authors), " AND ")
    );

    $AuthorsSteamIDs = $Core->db->getAllRows("SELECT name, steamid FROM tf_users WHERE ".join(array_map(function($i){return format("steamid = '%s'",[$i]);}, $Authors), " OR "));
  }

  if(isset($_GET["search"]) && $_GET["search"] != "")
  {
    $_SEARCH = preg_replace('/[^A-Za-z0-9\'\"\s]/', '', $_GET["search"]);
    $_FILTER = false;
    array_push(
      $Conditions,
      "name like '%".addslashes($_SEARCH)."%' or descr like '%".addslashes($_SEARCH)."%'"
    );
  }

  if(isset($_GET["status"]) && in_array($_GET["status"],[0,1,2,3,4,5]))
  {
    array_push(
      $Conditions,
      "status = ".$_GET["status"]
    );
  }else {
    if($_FILTER)
      array_push(
        $Conditions,
        "status != 0 AND status != 5"
      );
  }

  array_push(
    $Conditions,
    "trashed = 0"
  );

  if(count($Conditions) == 0) $Conditions = [1];


  $Query = format("WHERE %s", [
    join(array_map(function($i){return format("(%s)", [$i]);}, $Conditions), " AND ")
  ]);

  $_NAV_MAXENTRIES = $Core->db->getRow(format("SELECT count(*) as count FROM tf_submissions %s", [$Query])) ["count"];
  $_NAV_MAXPAGES = ceil($_NAV_MAXENTRIES / $Core->config->submissions->page);

  $Subs;
  if($_SORTING == "recent")
  {
    $Subs = $Core->db->getAllRows(format("SELECT * FROM tf_submissions %s ORDER BY created_at DESC LIMIT %s OFFSET %s", [
      $Query,
      $Core->config->submissions->page,
      $Core->config->submissions->page * $_OFFSET
    ]));
  }
  $Subs = array_map(function($i){global $Core; return new Submission($i, $Core);}, $Subs);

  $SearchKeep = array_filter($_GET, function($k){return in_array($k, ["requiredtags", "authors"]);}, ARRAY_FILTER_USE_KEY);
  $_KEEP = array_filter($_GET, function($k){return in_array($k, ["requiredtags", "authors", "search", "status", "sorting"]);}, ARRAY_FILTER_USE_KEY);
  $Tags = array_filter($_GET, function($k){return in_array($k, ["requiredtags", "authors", "search", "status"]);}, ARRAY_FILTER_USE_KEY);

  $Content = render("pages/workshop/feed", [
    "title" => "Submissions",
    "nav" => render("nav", [
      "back" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_OFFSET])),
      "next" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_OFFSET + 2])),

      "link_m2_value" => $_PAGE-2,
      "link_m2_href" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_PAGE-2])),
      "link_m1_value" => $_PAGE-1,
      "link_m1_href" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_PAGE-1])),
      "link_0_value" => $_PAGE,
      "link_p1_value" => $_PAGE+1,
      "link_p1_href" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_PAGE+1])),
      "link_p2_value" => $_PAGE+2,
      "link_p2_href" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_PAGE+2])),

      "link_first_href" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => 1])),
      "link_first_value" => 1,
      "link_last_href" => Workshop_BuildFilterURL(array_merge($_KEEP, ["p" => $_NAV_MAXPAGES])),
      "link_last_value" => $_NAV_MAXPAGES,
    ], [
      "BACK" => $_PAGE > 1,
      "NO_BACK" => !($_PAGE > 1),
      "NEXT" => $_PAGE < $_NAV_MAXPAGES,
      "NO_NEXT" => !($_PAGE < $_NAV_MAXPAGES),
      "LINK_M2" => $_PAGE > 2,
      "LINK_M1" => $_PAGE > 1,
      "LINK_0" => $_PAGE > 0 && $_PAGE <= $_NAV_MAXPAGES,
      "LINK_P1" => $_PAGE < $_NAV_MAXPAGES,
      "LINK_P2" => $_PAGE < $_NAV_MAXPAGES - 1,

      "LINK_FIRST" => $_PAGE > 3,
      "LINK_LAST" => $_PAGE < $_NAV_MAXPAGES - 2,
    ]),

    "actual_count" => count($Subs),
    "total_count" => $_NAV_MAXENTRIES,

    "steamid" => $Core->User->steamid,

    "href_sort_recent" => Workshop_BuildFilterURL(array_merge($_KEEP, ["sorting" => "recent"])),
    "href_sort_popular" => Workshop_BuildFilterURL(array_merge($_KEEP, ["sorting" => "popular"])),

    "sort_type_display" => (function ($a){
      switch ($a) {
        case 'popular': return "Most Popular"; break;
        case 'recent': return "Most Recent"; break;
        default: return "Most Popular"; break;
      };
    })($_SORTING),

    "content" => render("pages/workshop/feed_grid_3", ["content" => join(array_map(function($i){ $i->getUniqueViewers(); return $i->toDOMSquare();}, $Subs),"")]),
    "params" => join(
      array_map(function($i, $s){
        if(is_array($i))
        {
          $a = [];
          foreach ($i as $v) {
            array_push($a, format("<input type='hidden' name='%s[]' value='%s'>", [$s, $v]));
          }
          return join($a, "");
        }else{
          return format("<input type='hidden' name='%s' value='%s'>", [$s, $i]);
        }
      },
      $SearchKeep,
      array_keys($SearchKeep)
    ), ""),
    "search" => htmlentities($_GET["search"] ?? null),
    "tags.json" => json_encode($_KEEP),
    "tags.filter.types" => join(
      array_map(
        function($v)
        {
          return render("pages/workshop/filter_checkbox", ["value" => $v]);
        },
        $Core->config->submissions->types
      ),
      ""
    ),
    "tags.filter.themes" => join(
      array_map(
        function($v)
        {
          return render("pages/workshop/filter_checkbox", ["value" => $v]);
        },
        $Core->config->submissions->themes
      ),
      ""
    ),
    "tags.filter.classes" => join(
      array_map(
        function($v)
        {
          return render("pages/workshop/filter_checkbox", ["value" => $v]);
        },
        $Core->config->submissions->classes
      ),
      ""
    ),
    "tags.filter.gamemodes" => join(
      array_map(
        function($v)
        {
          return render("pages/workshop/filter_checkbox", ["value" => $v]);
        },
        $Core->config->submissions->gamemodes
      ),
      ""
    ),
    "tags" => join(
      array_map(
        function($v, $k){
          global $AuthorsSteamIDs;
          global $Tags;

          $Name = null;
          switch ($k) {
            case 'requiredtags': $Name = "Tag"; break;
            case 'authors': $Name = "Author"; break;
            case 'search': $Name = "Search"; break;
            case 'status': $Name = "Status"; break;
          }
          if(is_array($v))
          {
            $v = array_unique($v);
            $a = [];
            foreach ($v as $j => $i) {
              $LinkBase = $Tags;
              unset($LinkBase[$k][$j]);

              if($k == "authors") {
                $j = array_ksearch($AuthorsSteamIDs, "steamid", $i);
                if($j !== NULL)
                {
                  $i = $AuthorsSteamIDs[$j]["name"];
                }
              }else $i = htmlentities($i);

              array_push($a, render("pages/workshop/filter_tag", ["key" => $Name, "value" => $i, "url" => Workshop_BuildFilterURL($LinkBase)]));
            }
            return join($a, "");
          }else{
            $LinkBase = $Tags;
            unset($LinkBase[$k]);
            if($k == "status")
            {
              switch ($v) {
                case 0: $v = "On Moderation"; break;
                case 1: $v = "Pending"; break;
                case 2: $v = "Compatible"; break;
                case 3: $v = "Introduced"; break;
                case 4: $v = "Officially Added"; break;
                case 5: $v = "Incompatible"; break;
              }
            }
            return render("pages/workshop/filter_tag", ["key" => $Name, "value" => htmlentities($v), "url" => Workshop_BuildFilterURL($LinkBase)]);
          }
        },
        $Tags,
        array_keys($Tags)
      )
    ,"")
  ],
  [
    "LOGIN" => isset($Core->User)
  ]);
}

function Workshop_BuildFilterURL($base)
{
  return strtok($_SERVER["REQUEST_URI"], '?')."?".join(array_map(function($a, $b){
    if(is_array($a))
    {
      $c = [];
      foreach ($a as $d) {
        array_push($c, $b."[]=".urlencode($d));
      }
      return join($c, "&");
    }else return $b."=".urlencode($a);
  }, $base, array_keys($base)),"&");
}

?>
