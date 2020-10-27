<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class SteamWorkshopCrawler {
    function __construct($id, $core)
    {
        $this->m_hCore = $core;
        $this->id = $id;
        $this->html = file_get_contents("https://steamcommunity.com/sharedfiles/filedetails/?id=".$this->id."&l=english");
        libxml_use_internal_errors(true);

        $this->dom = new \DOMDocument;
        $this->dom->loadHTML($this->html);
        $this->finder = new \DomXPath($this->dom);

        if($this->dom->getElementById("message") !== NULL)
        {
            $this->error = true;
        }else{
            $this->error = false;
        }
    }

  function isCollection()
  {
    return $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' collectionHeaderContent ')]")->item(0) !== null;
  }

  function getName()
  {
    if($this->error) return;
    return $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' workshopItemTitle ')]")->item(0)->nodeValue ?? NULL;
  }

  function getGame()
  {
    if($this->error) return;
  	$game = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' breadcrumbs ')]")->item(0)->getElementsByTagName("a")->item(0);
  	$gameName = $game->nodeValue;
  	$gameID = explode("/",$game->getAttribute("href"))[4];

    return [
      "name" => $gameName,
      "id" => $gameID
    ];
  }

  function getAuthors()
  {
    if($this->error) return;
    $authors = array();
    $blocks = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' friendBlock ')]");
    for ($i = 0; $i < $blocks->length; $i++) {
      $node = $blocks->item($i);
      preg_match("'\r\n\t\t\t\t(.*?)\r\n\t\t'", $node->getElementsByTagName("div")->item(1)->nodeValue,$match);
      array_push($authors, array(
        "name" => $match[1],
        "profile" => $node->getElementsByTagName("a")->item(0)->getAttribute("href")
      ));
    }
    return $authors;
  }

  function getAuthorsSteamID()
  {
    if($this->error) return;
    if(isset($this->__authors)) return $this->__authors;
    $d = [];
    $a = $this->getAuthors();
    foreach ($a as $b) {
      if(strpos($b["profile"], "/id/") !== false)
      {
        $steamid = explode("/", $b["profile"])[4];
        $c = file_get_contents(format("http://api.steampowered.com/ISteamUser/ResolveVanityURL/v0001/?key=%s&vanityurl=%s", [$this->m_hCore->config->api->steam, $steamid]));
        if($c !== FALSE)
        {
          $c = json_decode($c, true);
          if($c["response"]["success"] == 1)
          {
            array_push($d, $c["response"]["steamid"]);
          }
        }
      }else if(strpos($b["profile"], "/profiles/") !== false){
        array_push($d, preg_replace("/[^0-9]/", "", $b["profile"] ));
      }
    }
    $this->__authors = $d;
    return $d;
  }

  function getImages()
  {
    if($this->error) return;
    $thumb = $this->getThumb();

    $images = array();
    preg_match_all('/ShowEnlargedImagePreview\( \'(.*?)\' \)/', $this->html, $matches);
    for ($i = 0; $i < count($matches[1]); $i++) {
      if($matches[1][$i] == $thumb) continue;
      array_push($images,$matches[1][$i]);
    }
    return $images;
  }

  function getThumb()
  {
    if($this->error) return;
    $image = null;
    if(($image = $this->dom->getElementById("ActualMedia")) === NULL){
      if(($image = $this->dom->getElementById("previewImageMain")) !== NULL){
        $image = explode("?",$image->getAttribute("src"))[0];
      }else{
        $image = isset($images[0])?$images[0]:NULL;
      }
    }else{
      $image = explode("?",$image->getAttribute("src"))[0];
    }
    return ($image == "") ? null : $image;
  }

  function getNumRating()
  {
    if($this->error) return;
    $el = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' numRatings ')]")->item(0);
    if($el != NULL) {
      return preg_replace("/[^0-9]/", "", $el->nodeValue ?? 0 );
    }else return 0;
  }

  function getRating()
  {
    if($this->error) return;
    $el = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' fileRatingDetails ')]")->item(0);
    if($el != NULL) {
      $el = $el->getElementsByTagName("img")->item("0");
      if($el != NULL)
      {
        switch ($el->getAttribute("src")) {
          case 'https://steamcommunity-a.akamaihd.net/public/images/sharedfiles/5-star_large.png?v=2': return 5; break;
          case 'https://steamcommunity-a.akamaihd.net/public/images/sharedfiles/4-star_large.png?v=2': return 4; break;
          case 'https://steamcommunity-a.akamaihd.net/public/images/sharedfiles/3-star_large.png?v=2': return 3; break;
          case 'https://steamcommunity-a.akamaihd.net/public/images/sharedfiles/2-star_large.png?v=2': return 2; break;
          case 'https://steamcommunity-a.akamaihd.net/public/images/sharedfiles/1-star_large.png?v=2': return 1; break;
          default: return 0; break;
        }
      }
    }else return 0;
  }

  function getTags()
  {
    if($this->error) return;
    $tags = [];
    $els = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' workshopTags ')]");
    foreach ($els as $el) {
      $name = $el->getElementsByTagName("span")->item(0)->nodeValue;
      $a = $el->getElementsByTagName("a");
      $value = [];
      foreach ($a as $v) {
        array_push($value, $v->nodeValue);
      }
      $name = substr($name, 0, -3);
      array_push($tags, ["name" => $name, "value" => $value]);
    }
    return $tags;
  }

  function getDesc()
  {
    if($this->error) return;
    $el = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' nonScreenshotDescription ')]")->item(0);
    if($el == NULL)
      $el = $this->finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' screenshotDescription ')]")->item(0);
    if($el == NULL)
      $el = $this->dom->getElementById("highlightContent");

    $innerHTML = '';
    $children = $el->childNodes;
    foreach ($children as $child) {
        $innerHTML .= $child->ownerDocument->saveXML( $child );
    }

    return $innerHTML;
  }

  function getInfo()
  {
  }
}

?>
