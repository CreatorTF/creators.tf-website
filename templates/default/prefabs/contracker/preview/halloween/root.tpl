<div class="merasmission_buttons">

</div>
<div class="merasmission_stamp"[TURNED] style="display:block;"[/TURNED]></div>

<div class="merasmission_turnin"[CAN_TURNIN] style="display:block;"[/CAN_TURNIN]>
    <div class="merasmission_turnin_progress"></div>
    <div class="merasmission_turnin_text" onclick="CCHalloween_OnClickQuestTurnIn(this, {id})">CLICK TO TURN IN</div>
</div>

<div class="merasmission_content">
    <p>Completing a Merasmission will reward you with additional Souls for your Tainted Tome.</p>
    <p>Perform any of the following tasks to earn Merasmission points.</p>
    <p style="color: #962e1d;[IS_WAITING_FOR_TRUSTED] display:block;[/IS_WAITING_FOR_TRUSTED]" class="display_none merasmission_wait_for_round"><b>Finish the round to Turn In the Merasmission.</b></p>
    <div class="quest-preview-progress">
        [TURNED]
            <div class="quest-preview-progress-line" style="width: {progress.total.percentage}%; background: #551b12"></div>
        [/TURNED]
        [NOT_TURNED]
            <div class="quest-preview-progress-line saved" style="width: {progress.saved.percentage}%"></div>
            <div class="quest-preview-progress-line unsaved" style="left: {progress.saved.percentage}%; width: {progress.unsaved.percentage}%"></div>
        [/NOT_TURNED]
        <div class="quest-preview-progress-text">{progress.total}/{limit} MP</div>
    </div>
    [CAN_ACTIVATE]<div class="merasmission_activate" onclick="Creators.Actions.Sounds.play(HALLOWEEN_SOUND_CLICK); CCHalloween_ButtonSetContract(this, {id});"><span>Activate</span></div>[/CAN_ACTIVATE]
    {objective_primary}
    {objectives_bonus}
    <hr>
    {rewards}
</div>
