<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ItemTool extends Item
{
    public function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function use($Target)
    {

    }

    public function canApplyTo($Target)
    {
        if($Target->isLoaner()) return false;
        // We can't apply tool on target if both items don't share at least one capability.
        // However that only applies if Tool item has capabilities in the first place.
        if( count(array_keys($this->def->usage_capabilities)) > 0 &&
            count(array_intersect(array_keys($this->def->usage_capabilities ?? []), array_keys($Target->def->capabilities ?? []))) == 0
        ) return false;

        // If Tool item is only usable with a specific item, we filter out
        // all uneligible items
        if( $this->getAttributeByName("tool target item") != NULL &&
            $this->getAttributeByName("tool target item") != $Target->defid
        ) return false;

        return true;
    }

    function removeIfNoMoreUses()
    {
        if($this->getAttributeByName("limited quantity item") > 1) {
            $this->setAttribute("limited quantity item", ((integer) $this->getAttributeByName("limited quantity item")) - 1);
        } else {
            $this->remove();
        }
    }
}
?>
