<?php
if(!defined("INCLUDED")) die("Access forbidden.");

class ItemToolModifier extends ItemTool
{
    public function __construct($data, $core)
    {
        parent::__construct($data, $core);
    }

    function use($Target)
    {
        parent::use($Target);

        foreach (($this->def->apply_attributes ?? []) as $attribute) {
            $Target->setAttribute($attribute["name"], $attribute["value"]);
        }

        switch ($this->def->tool_action) {
            case 'strangify':
                $Target->strangify();
                break;

            case 'add_strange_part':
                for ($i = 1; $i <= 3; $i++) {
                    $sName = "strange eater part ".$i;
                    if($Target->getAttributeByName($sName) == 0)
                    {
                        $Target->setAttribute($sName, $this->getAttributeByName("strange part new counter ID"));
                        break;
                    }
                }
                break;
        }
        $this->removeIfNoMoreUses();
    }

    function canApplyTo($Target)
    {
        if(parent::canApplyTo($Target) == false) return false;

        switch ($this->def->tool_action)
        {
            case 'strangify':
                // Only unique items can be strangified
                if( $Target->quality != Q_UNIQUE )
                    return false;

                // We should also check if none strange eater parts are
                // defined, because strange item may not have that quality
                // in the first place.
                for ($i = 0; $i < 10; $i++) {
                    if($i == 0) {
                        $sName = "strange eater";
                    } else {
                        $sName = "strange eater part ".$i;
                    }

                    if($Target->getAttributeByName($sName) > 0)
                    {
                        return false;
                    }
                }
                break;

            case 'strangify':
                // Only strange items can get new strange parts
                if( $Target->quality != Q_STRANGE )
                    return false;

                // We check if there are any strange part slots that we can add to.
                // If there are none - skip this item.
                for ($i = 1; $i <= 3; $i++) {
                    $sName = "strange eater part ".$i;
                    if($Target->getAttributeByName($sName) == 0) break;
                    if($i == 3) return false;
                }
                break;
        }
        return true;
    }
}
?>
