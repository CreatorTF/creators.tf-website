<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class SubmissionCollection extends BaseCollection{
    function __construct($core)
    {
        parent::__construct($core);
        $this->__table = "tf_submissions";
        $this->__object = "Submission";
    }

    function import($Sub, $sTags, $iStatus, $iOwner)
    {
        $this->m_hCore->dbh->query(
            "INSERT INTO tf_submissions
            (owner, authors, name, descr, images, workshop_id, created_at, updated_at, thumb, status, tags)
            VALUES (%d, %s, %s, %s, %s, %s, NOW(), NOW(), %s, %d, %s)", [
                $iOwner,
                json_encode($Sub->getAuthorsSteamID()),
                htmlentities($Sub->getName()),
                strip_tags($Sub->getDesc(), '<b><u><i><br><a><p><img>'),
                json_encode($Sub->getImages()),
                $Sub->id,
                $Sub->getThumb(),
                $iStatus,
                json_encode($sTags)
            ]
        );
    }
}
?>
