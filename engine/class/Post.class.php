<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class Post extends BaseClass
{
    function __construct($data, $core)
    {
        parent::__construct($data, $core);
        $this->id = $data["id"];
        $this->author_id = $data["author"];

        // TODO: Remove this once all posts are switched to Markdown.
        $this->isHTML = $data["is_html"] == 1;

        $this->title = $data["title"];
        $this->story = $data["story"];
        $this->created = $data["created"];
        $this->published = $data["published"];

        $this->embed = json_decode($data["embed"],true);
        if(!isset($this->embed)) $this->embed = [];
    }

  function comment($Author, $content)
  {
    $this->m_hCore->comments->create($Author, "[P:".$this->id."]", $content);
  }

  function getAuthor()
  {
    if(isset($this->__author)) return $this->__author;
    $a = $this->m_hCore->users->find("id", $this->author_id);
    $this->__author = $a;
    return $a;
  }

  function toDOM($tags = [], $brackets = [])
  {
    $Author = $this->getAuthor();
    if($Author == NULL) return;
    return $this->m_hCore->posts->toDOM(
      array_merge([
        "id" => $this->id,
        "title" => $this->title,
        "alias" => $Author->alias ?? $Author->steamid,
        "avatar" => $Author->avatar,
        "author" => $Author->name,
        "date" => strftime("%B %d, %Y %H:%M", (new DateTime($this->published))->getTimestamp()),
        "created_date" => strftime("%B %d, %Y %H:%M", (new DateTime($this->created))->getTimestamp()),
        "story" => $this->story,

        "embed-image" => $this->embed[0]["image"] ?? NULL,
        "embed-title" => $this->embed[0]["title"] ?? NULL,
        "embed-content" => $this->embed[0]["content"] ?? NULL,
        "embed-url" => $this->embed[0]["url"] ?? NULL
      ], $tags),
      array_merge([
        "EMBED" => isset($Post->embed) ? count($Post->embed) > 0 : false,
        "PUBLISHED" => isset($this->published),
        "NOT_PUBLISHED" => !isset($this->published)
      ], $brackets)
    );
  }
}
?>
