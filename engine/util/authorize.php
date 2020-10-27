<?php
if(!defined("INCLUDED")) die("Access forbidden.");

// We bump once a day.
define("CLIENT_BUMP_INTERVAL", 60 * 60 * 24);

if(isset($_SERVER["HTTP_ACCESS"]))
{
    $key = explode(" ", $_SERVER["HTTP_ACCESS"]);
    $Core->Key = $Core->apikeys->find("apikey", $key[1] ?? null);

    if(isset($Core->Key))
    {
        if(strtolower($key[0] ?? null) == "server")
        {
            // If we're authed as server we check for "Server Access" key permission.
            // And if present give requester access to any user info the want.
            if($Core->Key->special & APISPECIAL_SERVER_KEY)
            {
                if(isset($key[2]))
                {
                    $Core->Server = $Core->servers->findAND("id", $key[2], "owner", $Core->Key->owner);
                    if(isset($Core->Server) && isset($key[3]))
                    {
                        $Core->User = $Core->users->find("steamid", $key[3]);
                    }
                }
            }
        }
    }
}

if(!isset($Core->User))
{
    $token = $_COOKIE["session_id"] ?? NULL;

    if(isset($token) && $token != "")
    {
        $Core->User = $Core->users->find('token', $token);
        if(!isset($Core->User))
        {
            $Core->logout();
        } else {
            if(($_SESSION["m_bNextBump"] ?? 0) < time())
            {
                $Core->User->bump();
                $_SESSION["m_bNextBump"] = time() + CLIENT_BUMP_INTERVAL;
            }
        }
    }
}
?>
