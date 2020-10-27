<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class PostCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_posts";
        $this->__object = "Post";
    }

    function getPost($id, $author)
    {
      $a = $this->m_hCore->db->getRow(format(
        "SELECT tf_posts.* FROM tf_posts LEFT JOIN tf_users ON tf_posts.author = tf_users.id WHERE tf_posts.id = %s AND (tf_users.steamid = '%s' OR tf_users.alias = '%s')",
        [
          escape($id),
          escape($author),
          escape($author)
        ]
      ));

      if(isset($a)) return new Post($a, $this->m_hCore);
      else return NULL;
    }

    function toDOM($tags = [], $brackets = [])
    {
      return render(
        "pages/posts/post",
        array_merge([
          "alias" => "",
          "avatar" => "",
          "id" => "",
          "author" => "",
          "date" => "",
          "story" => "",

          "embed-url" => "",
          "embed-image" => "",
          "embed-title" => "",
          "embed-content" => "",

          "comments" => ""
        ], $tags),
        array_merge([
          "EMBED" => false,
          "NOT_PUBLISHED" => false,
          "PUBLISHED" => false,
          "COMMENTS" => false
        ], $brackets)
      );
    }
}
?>
