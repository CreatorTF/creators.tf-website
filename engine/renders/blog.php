<?php
if(!defined("INCLUDED")) die("Access forbidden.");

if($_GET["page"] == "posts") {
  $_DATA['page_name'] = "Blog";
  if(isset($_GET["p"]) && is_numeric($_GET["p"]) && $_GET["p"] > 0) $_OFFSET = ((integer) $_GET["p"]) - 1;
  else $_OFFSET = 0;
  $_PAGE = $_OFFSET + 1;

  if(isset($_GET["sorting"]) && in_array($_GET["sorting"], ["recent", "oldest"])) $_SORTING = $_GET["sorting"];
  else $_SORTING = "recent";
  $Conditions = [];
  $Authors = [];

  if(is_array($_GET["authors"] ?? null) && count($_GET["authors"] ?? []) > 0)
  {
    $Authors = array_filter($_GET["authors"], function ($a){return !preg_match('/[^A-Za-z0-9]/', $a);});
    $Authors = $Core->db->getAllRows(format(
      "SELECT id FROM tf_users WHERE steamid IN (%s) OR alias IN (%s)",
      [
        join(",", array_map(function($a){return "\"".$a."\"";}, $Authors)),
        join(",", array_map(function($a){return "\"".$a."\"";}, $Authors))
      ]
    ));
  }
  if(count($Authors) > 0)
  {
    array_push(
      $Conditions,
      join(array_map(function($i){
        return format("author = %s", [$i["id"]]);
      }, $Authors), " AND ")
    );
  }

  if(isset($Core->User) && $Core->User->hasAdminBit(ADMINFLAG_POSTS))
  {

  }else{
    array_push(
      $Conditions,
      "published is not null"
    );
  }

  if(isset($_GET["search"]) && $_GET["search"] != "")
  {
    $_FILTER = false;
    array_push(
      $Conditions,
      "story like '%".escape($_GET["search"])."%' or title like '%".escape($_GET["search"])."%'"
    );
  }

  if(count($Conditions) == 0) $Conditions = [1];

  $Query = format("WHERE %s", [
    join(" AND ", array_map(function($i){return format("(%s)", [$i]);}, $Conditions))
  ]);

  $_NAV_MAXENTRIES = $Core->db->getRow(format("SELECT count(*) as count FROM tf_posts %s", [$Query])) ["count"];
  $_NAV_MAXPAGES = ceil($_NAV_MAXENTRIES / $Core->config->website->postsPerPage);

  $Results;
  if($_SORTING == "recent")
  {
    $Results = $Core->db->getAllRows(format("SELECT * FROM tf_posts %s ORDER BY published DESC LIMIT %s OFFSET %s", [
      $Query,
      $Core->config->website->postsPerPage,
      $Core->config->website->postsPerPage * $_OFFSET
    ]));
  }else if($_SORTING == "oldest")
  {
    $Results = $Core->db->getAllRows(format("SELECT * FROM tf_posts %s ORDER BY published ASC LIMIT %s OFFSET %s", [
      $Query,
      $Core->config->website->postsPerPage,
      $Core->config->website->postsPerPage * $_OFFSET
    ]));
  }
  $Results = array_map(function($i){global $Core; return new Post($i, $Core);}, $Results);

  // GET params that need to be kept during pagination movements
  $_KEEP = array_filter($_GET, function($k){return in_array($k, ["authors", "search", "sorting"]);}, ARRAY_FILTER_USE_KEY);
  // GET params that need to be kept after search
  $_KEEP_SEARCH = array_filter($_GET, function($k){return in_array($k, ["authors"]);}, ARRAY_FILTER_USE_KEY);
  // GET params that need to shown as Tags
  $_TAGS = array_filter($_GET, function($k){return in_array($k, ["authors", "search"]);}, ARRAY_FILTER_USE_KEY);

  $Content = render("pages/posts/feed", [
    "title" => "Posts",
    "nav" => render("nav", [
      "back" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_OFFSET])),
      "next" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_OFFSET + 2])),

      "link_m2_value" => $_PAGE-2,
      "link_m2_href" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_PAGE-2])),
      "link_m1_value" => $_PAGE-1,
      "link_m1_href" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_PAGE-1])),
      "link_0_value" => $_PAGE,
      "link_p1_value" => $_PAGE+1,
      "link_p1_href" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_PAGE+1])),
      "link_p2_value" => $_PAGE+2,
      "link_p2_href" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_PAGE+2])),

      "link_first_href" => params_build_url("/posts", array_merge($_KEEP, ["p" => 1])),
      "link_first_value" => 1,
      "link_last_href" => params_build_url("/posts", array_merge($_KEEP, ["p" => $_NAV_MAXPAGES])),
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

    "actual_count" => count($Results),
    "total_count" => $_NAV_MAXENTRIES,

    "href_sort_recent" => params_build_url("/posts", array_merge($_KEEP, ["sorting" => "recent"])),
    "href_sort_oldest" => params_build_url("/posts", array_merge($_KEEP, ["sorting" => "oldest"])),

    "sort_type_display" => (function ($a){
      switch ($a) {
        case 'popular': return "Most Popular"; break;
        case 'oldest': return "Most Oldest"; break;
        default: return "Most Popular"; break;
      };
    })($_SORTING),

    "content" => join("", array_map(function($i){ return $i->toDOM();}, $Results)),
    "params" => join("",
      array_map(function($i, $s){
        if(is_array($i))
        {
          $a = [];
          foreach ($i as $v) {
            array_push($a, format("<input type='hidden' name='%s[]' value='%s'>", [$s, $v]));
          }
          return join("", $a);
        }else{
          return format("<input type='hidden' name='%s' value='%s'>", [$s, $i]);
        }
      },
      $_KEEP_SEARCH,
      array_keys($_KEEP_SEARCH)
    )),
    "search" => $_GET["search"] ?? null
  ],
  [
    "LOGIN" => isset($Core->User)
  ]);
}

if($_GET["page"] == "post")
{
  $Post = $Core->posts->getPost($_GET["post"], $_GET["author"]);
  if(isset($Post))
  {
    preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $Post->story, $_FIRST_IMAGE);
    $_DATA['page_name'] = $Post->title ?? "#Post_By_Author ".$Post->getAuthor()->name;
    $_DATA["og"]["image"] = $_FIRST_IMAGE['src'] ?? $Post->getAuthor()->avatar;
    $_DATA["og"]["type"] = 'article';
    $_DATA["og"]["description"] = escape(strip_tags($Post->story));
    if(mb_strlen($_DATA["og"]["description"]) > 150)
    {
        $_DATA["og"]["description"] = mb_substr($_DATA["og"]["description"], 0, 150)."...";
    }

    $Content = $Post->toDOM([
      "comments" => render("prefabs/comments", [
        "target" => "post",
        "id" => $Post->id,
        "count" => 6
      ], [
        "LOGIN" => isset($Core->User)
      ])
    ], [
      "COMMENTS" => true
    ]);
  }else $Core->error = 404;
}

?>
