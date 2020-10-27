<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Database__DEPRECATED
{
    function __construct($SQL)
    {
        $this->SQL = $SQL;
    }
    function query($q)
    {
        return mysqli_query($this->SQL,$q);
    }
    function getRow($q)
    {
        return mysqli_fetch_assoc($this->query($q));
    }
    function getAllRows($q)
    {
        return mysqli_fetch_all($this->query($q),MYSQLI_ASSOC);
    }
}

function ApiLog($command)
{
    global $Core;
    $hJobs = $Core->getCache()->get("logs");
    if($hJobs == false) $hJobs = [];

    array_push($hJobs, $command);

    $Core->getCache()->set("logs", $hJobs, false, 0);
}

class Database
{
    function __construct($SQL)
    {
        $this->SQL = $SQL;
        $this->__cache_row = [];
        $this->__cache_all = [];
    }

    function query($q, $arr)
    {
        if(!is_string($q)) throw new TypeError("Argument #1 must be string.");
        if(!is_array($arr)) throw new TypeError("Argument #2 must be array.");

        preg_match_all('/\%d|\%s|\%i|\%b/', $q, $types);
        $count = count($types[0]);
        if($count != count($arr)) throw new Error("Values should match parameters.");

        $types = join("", array_map(function($a){return substr($a, 1);}, $types[0]));
        $q = preg_replace('/\%d|\%s|\%i|\%b/', '?', $q);

        $stmt = $this->SQL->prepare($q);
        if ( false === $stmt )
        {
            die('prepare() failed: ' . htmlspecialchars($this->SQL->error));
        }
        array_unshift($arr, $types);

        call_user_func_array([$stmt, 'bind_param'], ref_array($arr));

        $stmt->execute();

        $results = $stmt->get_result();
        return $results;
    }

    function getRow($q, $arr, $force = false)
    {
        if(!$force)
        {
            $hash = md5(md5($q).md5(json_encode($arr)));

            if(($this->__cache_row[$hash] ?? NULL) == -1)
            {
                return NULL;
            }

            if(isset($this->__cache_row[$hash]))
            {
                return $this->__cache_row[$hash];
            }

            $data = mysqli_fetch_assoc($this->query($q, $arr));
            if(isset($data))
            {
                $this->__cache_row[$hash] = $data;
            } else {
                $this->__cache_row[$hash] = -1;
            }

            return $data;
        } else {
            return mysqli_fetch_assoc($this->query($q, $arr));
        }
    }

    function getAllRows($q, $arr, $force = false)
    {
        if(!$force)
        {
            $hash = md5(md5($q).md5(json_encode($arr)));
            if(isset($this->__cache_all[$hash]))
            {
                return $this->__cache_all[$hash];
            }

            $data = mysqli_fetch_all($this->query($q, $arr), MYSQLI_ASSOC);
            $this->__cache_all[$hash] = $data;

            return $data;
        } else {
            return mysqli_fetch_all($this->query($q, $arr), MYSQLI_ASSOC);
        }
    }
}

$SQL = mysqli_connect(
    $Core->config->database->hostname,
    $Core->config->database->username,
    $Core->config->database->password,
    $Core->config->database->database
);

mysqli_set_charset($SQL, "utf8mb4");

$Core->db = new Database__DEPRECATED($SQL);
$Core->dbh = new Database($SQL);
?>
