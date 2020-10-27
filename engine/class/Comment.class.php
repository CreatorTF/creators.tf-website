<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Comment extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->id = (int) $data["id"];
        $this->raw_content = $data["content"];
        $this->created = $data["created"];
        $this->updated = $data["updated"];

        $this->target = $data["targetid"];
        $this->author = $data["author"];

        $this->blacklist = json_decode($data["blacklist"],true);
        if(!isset($this->blacklist)) $this->blacklist = [];

        $this->content = $this->raw_content;
        $this->content = preg_replace('/\[b\](.*)\[\/b\]/U', '<b>$1</b>', $this->content);
        $this->content = preg_replace('/\[u\](.*)\[\/u\]/U', '<u>$1</u>', $this->content);
        $this->content = preg_replace('/\[i\](.*)\[\/i\]/U', '<i>$1</i>', $this->content);
        $this->content = preg_replace('/\[s\](.*)\[\/s\]/U', '<s>$1</s>', $this->content);
        $this->content = preg_replace('/\[spoiler\](.*)\[\/spoiler\]/U', '<span class="format_spoiler">$1</span>', $this->content);
    }

  function blacklist($User, $hide = true)
  {
    if($User->id == $this->author) return;
    foreach ($this->blacklist as $key => $value) {
      if($value == $User->id) array_splice($this->blacklist, $key, 1);
    }

    if($hide === true)
    {
      array_push($this->blacklist, $User->id);
    }
    $this->m_hCore->dbh->query("UPDATE tf_comments SET blacklist = %s WHERE id = %d", [json_encode($this->blacklist), $this->id]);
  }

  function toDOM($tags = [], $brackets = [])
  {
    $Author = $this->getAuthor();
    if(!isset($Author)) return null;
    return $this->m_hCore->comments->toDOM(
      array_merge([
        "alias" => $Author->alias,
        "avatar" => $Author->avatar,
        "name" => $Author->name,
        "date" => strftime("%B %d, %Y %H:%M", (new DateTime($this->created))->getTimestamp()),
        "content" => $this->content,
        "id" => $this->id,
        "special" => $Author->toDOMSpecial()
      ], $tags),
      array_merge([
        "CAN_FLAG" => false,
        "CAN_DELETE" => false
      ], $brackets)
    );
  }

  function delete()
  {
    $this->m_hCore->dbh->query("DELETE FROM tf_comments WHERE id = %d", [$this->id]);
  }

  function getAuthor()
  {
    if(isset($this->__author)) return $this->__author;
    $this->__author = $this->m_hCore->users->find("id", $this->author);
    return $this->__author;
  }

  function getTarget()
  {
    if(isset($this->__target)) return $this->__target;
    preg_match('/\[([A-Za-z]+)\:([0-9]+)\]/', $this->target, $a);
    switch ($a[1]) {
      case 'S': $this->__target = $this->m_hCore->submissions->find("id", $a[2]); break;
      case 'U': $this->__target = $this->m_hCore->users->find("id", $a[2]); break;
      case 'P': $this->__target = $this->m_hCore->posts->find("id", $a[2]); break;
    }
    return $this->__target;
  }
}

?>
