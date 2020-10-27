<?php
if(!defined("INCLUDED")) die("Access forbidden.");

require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class/SourceQuery/bootstrap.php';
use xPaw\SourceQuery\SourceQuery;

class Server extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->id = $data["id"];
        $this->ip = $data["ip"];
        $this->port = $data["port"];
        $this->region = $data["region"];
        $this->group = $data["srv_group"];
        $this->Query = new SourceQuery( );
        $this->connected = false;

        $this->caching = $data["is_cached"] == 1;
        $this->cache = $data["cache"];
    }

    function flush()
    {
        $this->getCore()->servers->removeFromCache("id", $this->id);
    }

    function queryJob($command)
    {
        $command = str_replace("\"", "&quot;", $command);

        $hJobs = $this->getCore()->getCache()->get("serverjobs_".$this->id);
        if($hJobs == false) $hJobs = [];

        array_push($hJobs, ["command" => $command, "expires" => time() + 60]);

        $this->getCore()->getCache()->set("serverjobs_".$this->id, $hJobs, false, 0);
    }

    function filterJobs()
    {
        $hJobs = $this->getCore()->getCache()->get("serverjobs_".$this->id);

        $bChanged = false;
        foreach ($hJobs as $i => $hJob)
        {
            if($hJob["expires"] < time())
            {
                $bChanged = true;
                unset($hJobs[$i]);
            }
        }
        if($bChanged)
        {
            $this->getCore()->getCache()->set("serverjobs_".$this->id, $hJobs, false, 0);
        }
    }

    function getJobs()
    {
        $this->filterJobs();

        $hJobs = $this->getCore()->getCache()->get("serverjobs_".$this->id);
        if($hJobs == false) $hJobs = [];
        return array_map(function($hJob) {
            return $hJob["command"];
        }, $hJobs);
    }

    function flushJobs()
    {
        $this->getCore()->getCache()->delete("serverjobs_".$this->id);
    }

    function connect()
    {
        try{
            $this->Query->Connect( $this->ip, $this->port, 1, SourceQuery::SOURCE );
            $this->connected = true;
        }catch(Exception $e){
            throw new $e;
        }
    }

	function getInfo(){
    if(!$this->connected) return;
    try{
      return $this->Query->GetInfo();
    }catch(Exception $e)
    {
      throw new $e;
    }
	}

	function getPlayers(){
    if(!$this->connected) return;
    try{
      return $this->Query->GetPlayers();
    }catch(Exception $e)
    {
      throw new $e;
    }
	}

	function Rcon($pass, $string){
    if(!$this->connected) return;
    try{
      $this->Query->SetRconPassword($this->rcon ?? $pass);
      return $this->Query->Rcon($string);
    }catch(Exception $e)
    {
      throw new $e;
    }
	}
}
?>
