<?php
if(!defined("INCLUDED")) die("Access forbidden.");

require_once $_SERVER['DOCUMENT_ROOT']."/engine/class/bootstrap.php";

class AmperEngine
{
    function __construct()
    {
        $this->config = new stdClass();
        $this->pages = new PageCollection($this);
        $this->users = new UserCollection($this);
        $this->errors = new ErrorCollection($this);
        $this->apikeys = new ApikeyCollection($this);
        $this->posts = new PostCollection($this);
        $this->servers = new ServerCollection($this);
        $this->items = new ItemCollection($this);
        $this->depots = new DepotCollection($this);
        $this->submissions = new SubmissionCollection($this);
        $this->comments = new CommentCollection($this);
        $this->notifications = new NotificationCollection($this);
        $this->logs = new LogCollection($this);

        $this->m_hCache = new Memcache();
        $this->m_hCache->connect('localhost', 11211);
    }

    function getCache()
    {
        return $this->m_hCache;
    }

    function login($token)
    {
        apply_cookie($token, $this->config->website->cookie_domain, 60 * 60 * 24 * 31, request_issecure());
    }

    function logout()
    {
        apply_cookie("", $this->config->website->cookie_domain, -60 * 60 * 24 * 31, request_issecure());
    }
}

?>
