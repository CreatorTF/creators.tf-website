<?php
class CommentCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_comments";
        $this->__object = "Comment";
    }

    function create($Author, $targetid, $content)
    {
        // Escaping slashes in written \n
        $content = htmlentities($content);
        $content = preg_replace('/\\\\(n|r)/', '&#x5C;$1', $content);
        $content = trim($content);
        $content = preg_replace('/\n{3,}/', '\n\n', $content);

        $this->m_hCore->dbh->query("INSERT INTO tf_comments (author, targetid, content) VALUES (%d, %s, %s)", [
            $Author->id,
            $targetid,
            $content
        ]);
    }

    function toDOM($tags = [], $brackets = [])
    {
        return render(
            "embed/comment",
            array_merge([
                "alias" => "",
                "avatar" => "",
                "name" => "",
                "date" => "",
                "content" => "",
                "id" => "",
                "special" => ""
            ], $tags),
            array_merge([
                "CAN_FLAG" => false,
                "CAN_DELETE" => false,
                'VISIBLE' => true,
                "BLACKLISTED" => false
            ], $brackets)
        );
    }
}
?>
