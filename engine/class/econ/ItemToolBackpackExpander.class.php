<?php
if(!defined("INCLUDED")) die("Access forbidden.");

define("BACKPACK_EXPANDER_ADD_SLOTS", 2);

class ItemToolBackpackExpander extends ItemTool
{
    public function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function use($Target = NULL)
    {
        parent::use($Target);

        $Owner = $this->getOwner();
        if($Owner != NULL)
            $Owner->addBackpackPages(BACKPACK_EXPANDER_ADD_SLOTS);

        $this->removeIfNoMoreUses();
    }
}
?>
