<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class BaseCollection extends BaseClass
{
    function __construct($core)
    {
        parent::__construct([], $core);
        $this->__cache = [];
    }

    function cacheObject($hObject)
    {
    }

    function removeFromCache($param, $query)
    {
    }

    function getCachedObjects()
    {
    }

    function getCacheKey()
    {
        return $this->__table . "__cache";
    }

    function flushCache()
    {
    }

    function __debug_ShowCache()
    {
        $hCache = $this->getCachedObjects();
        echo "<br/> == Cache: ==</br>";
        foreach ($hCache as $i => $hCache)
        {
            echo $i.": ".json_encode($hCache)."<br/>";
        }
        echo " ============</br>";
    }

    // TODO: Mark all function as deprecated and make a new find function.
    function find($param, $query)
    {
        // See if we have request object in cache.
        foreach ($this->__cache as $hCache)
        {
            if(($hCache[$param] ?? NULL) == $query)
            {
                // Return found element in cache.
                return new $this->__object($hCache, $this->m_hCore);
            }
        }

        // Otherwise ask DB to return what we need.
        $hObject = $this->m_hCore->dbh->getRow(
            format("SELECT * FROM %u WHERE %u = %s", [$this->__table, $param], "u"),
            [$query]
        );
        if(empty($hObject)) return NULL;

        // Cache what we found.
        array_push($this->__cache, $hObject);

        return new $this->__object($hObject, $this->m_hCore);
    }

    function findAND($param, $query, $param2, $query2)
    {
        foreach ($this->__cache as $hElement)
        {
            if(($hElement[$param] ?? NULL) == $query && ($hElement[$param2] ?? NULL) == $query2)
            {
                // Return found element in cache.
                return new $this->__object($hElement, $this->m_hCore);
            }
        }

        // Otherwise ask DB to return what we need.
        $hObject = $this->m_hCore->dbh->getRow(
            format("SELECT * FROM %u WHERE %u = %s AND %u = %s", [$this->__table, $param, $param2], "u"),
            [$query, $query2]
        );
        if(empty($hObject)) return NULL;

        // Cache what we found.
        array_push($this->__cache, $hObject);

        return new $this->__object($Object, $this->m_hCore);
    }

    function findOR($param, $query, $param2, $query2)
    {
        foreach ($this->__cache as $hElement)
        {
            if(($hElement[$param] ?? NULL) == $query || ($hElement[$param2] ?? NULL) == $query2)
            {
                // Return found element in cache.
                return new $this->__object($hElement, $this->m_hCore);
            }
        }

        // Otherwise ask DB to return what we need.
        $hObject = $this->m_hCore->dbh->getRow(
            format("SELECT * FROM %u WHERE %u = %s OR %u = %s", [$this->__table, $param, $param2], "u"),
            [$query, $query2]
        );
        if(empty($hObject)) return NULL;

        // Cache what we found.
        array_push($this->__cache, $hObject);

        return new $this->__object($Object, $this->m_hCore);
    }

    function findLIKE($param, $query)
    {
        foreach ($this->__cache as $hElement)
        {
            if(strpos($cache[$param], (string) $query) !== false)
            {
                // Return found element in cache.
                return new $this->__object($hElement, $this->m_hCore);
            }
        }

        // Otherwise ask DB to return what we need.
        $hObject = $this->m_hCore->dbh->getRow(
            format("SELECT * FROM %u WHERE %u LIKE %s", [$this->__table, $param], "u"),
            ["%".$query."%"]
        );
        if(empty($hObject)) return NULL;

        // Cache what we found.
        array_push($this->__cache, $hObject);

        return new $this->__object($Object,$this->m_hCore);
    }
}
?>
