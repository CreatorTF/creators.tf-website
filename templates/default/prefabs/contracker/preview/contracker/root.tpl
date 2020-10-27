[NOT_UNLOCKED]
<div class="quest-preview-dependencies">
    <div class="quest_preview_dependencies_header"><i class="mdi mdi-lock"></i> Contract Locked</div>
    <div class="p-b-5">You need to complete these contracts to unlock this one:</div>
    {dependencies}
</div>
<div class="quest_preview_overlay"></div>
[/NOT_UNLOCKED]
<div class="quest-preview-class">{class}</div>
<div class="quest-preview-image" style="background-image: url({image});">
    <span class="quest-preview-image-name">{name}</span>
    [CAN_ACTIVATE]
    <div class="quest-preview-button" onclick="CEContracker_ButtonSetContract(this, {id}); Creators.Actions.Sounds.play(CONTRACKER_SOUND_CLICK);">Activate</div>
    [/CAN_ACTIVATE]
    [CAN_TURNIN]
    <div class="quest-turnin" onclick="CEContracker_ButtonTurnIn(this, {id});">Turn In.</div>
    [/CAN_TURNIN]
</div>
<div class="quest-preview-content"><span class="quest-preview-group-name">Primary:</span>
    {objective_primary}
    <div class="quest-preview-progress">
        [TURNED]<div class="quest-preview-progress-line" style="width: {progress.total.percentage}%; background: var(--cyoa-darkgreen);"></div>[/TURNED]
        [NOT_TURNED]
        <div class="quest-preview-progress-line" style="width: {progress.saved.percentage}%; background: var(--cyoa-orange);"></div>
        <div class="quest-preview-progress-line" style="left: {progress.saved.percentage}%; width: {progress.unsaved.percentage}%; background: orange;"></div>
        [/NOT_TURNED]
        <div class="quest-preview-progress-text">{progress.total}/{limit} CP</div>
    </div>
    [HAS_BONUS]
    <span class="quest-preview-group-name">Bonus:</span>
    {objectives_bonus}
    [/HAS_BONUS]
</div>
<div class="quest-rewards [TURNED]claimed[/TURNED]">
    <div class="quest-preview-content"><span class="quest-preview-group-name">Reward:</span>
        <center>{rewards}</center>
    </div>
</div>
