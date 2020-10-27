<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Depot extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->id = $data["id"];
        $this->hash = $data["dir_hash"];
        $this->created = $data["created"];
    }

  function getBaseDir()
  {
    return $_SERVER['DOCUMENT_ROOT'].'/cdn/depot/'.$this->hash;
  }

  function getManifest()
  {
    return json_decode(file_get_contents($this->getBaseDir()."/manifest.json"));
  }

  function getHashPath()
  {
    return $_SERVER['DOCUMENT_ROOT'].'/cdn/depot_hash/'.$this->hash;
  }

  function updateHash()
  {
    $cachepath = $this->getHashPath();
    $hash = md5_dir($this->getBaseDir());
    $file = fopen($cachepath, "w");
    fwrite($file, $hash);
    return $hash;
  }

  function getHash()
  {
    $cachepath = $this->getHashPath();
    if($this->cacheValid()) {
      return file_get_contents($cachepath);
    } else {
      return $this->updateHash();
    }
  }

  function cacheValid()
  {
    return  file_exists($_SERVER['DOCUMENT_ROOT'].'/cdn/depot_hash/'.$this->hash) &&
            filemtime($this->getHashPath()) + 60 * 60 * 24 * 10 > time();
  }
}
?>
