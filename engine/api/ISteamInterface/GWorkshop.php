<?php
define("INCLUDED", true);
require_once $_SERVER['DOCUMENT_ROOT']."/engine/api.php";
$method = $_SERVER['REQUEST_METHOD'];

if($method == "GET"){
  $param = check($_REQ,['id', 'request']);
  if(isset($param)) {
    ThrowAPIError(ERROR_API_INVALIDPARAM, $param);
  }
  $list = preg_split( "@(\,|\;)@", $_REQ["request"] );
  $response = [];
  $tags = ["id", "name", "description", "images", "thumb", "authors", "authors_steamid", "tags", "rating", "game"];
  $list = array_intersect($list, $tags);
  if(count($list) > 0)
  {
    $Sub = new SteamWorkshopCrawler($_REQ["id"], $Core);
    if($Sub->error)
      ThrowAPIError(ERROR_NOT_FOUND);
    if(isset($_REQ["game_id"]) && $Sub->getGame()["id"] != $_REQ["game_id"])
      ThrowAPIError(ERROR_NOT_FOUND);
    if($_REQ["filter_noname"] && $Sub->getName() == ("" || NULL))
      ThrowAPIError(ERROR_NOT_FOUND);
    if($_REQ["filter_nocollections"] && $Sub->isCollection())
      ThrowAPIError(ERROR_NOT_FOUND);

    foreach ($list as $i) {
      switch ($i) {
        case 'id': $response["id"] = (+$Sub->id); break;
        case 'name': $response["name"] = $Sub->GetName(); break;
        case 'description': $response["description"] = strip_tags($Sub->GetDesc()); break;
        case 'thumb': $response["thumb"] = $Sub->getThumb(); break;
        case 'images': $response["images"] = $Sub->getImages(); break;
        case 'authors': $response["authors"] = $Sub->getAuthors(); break;
        case 'authors_steamid': $response["authors_steamid"] = $Sub->getAuthorsSteamID(); break;
        case 'tags': $response["tags"] = $Sub->getTags(); break;
        case 'rating': $response["rating"] = $Sub->getNumRating(); break;
        case 'game': $response["game"] = $Sub->getGame(); break;
      }
    }
  }
  ThrowResult([
    "can_import" => isset($Core->User) && (in_array($Core->User->steamid, $Sub->getAuthorsSteamID()) || $Core->User->hasPermission(ADMINFLAG_SUBMISSIONS)),
    "submissions" => $response
  ]);
}


?>
