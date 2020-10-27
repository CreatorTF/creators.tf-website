<div class="progress_element[COMPLETED] completed[/COMPLETED]" data-index="{level}" data-progress="{progress.completed}">
    {loot}
    <div
        class="progress_label[HAS_CUSTOM_SKIN] has_custom_image[/HAS_CUSTOM_SKIN]"
        tooltip-top
        tooltip-timeout=0

        [IS_START_ELEMENT]
        tooltip="You start here!"
        [/IS_START_ELEMENT]

        [HAS_CUSTOM_SKIN]
        style="background-image: url({custom_image}); transform: scale({custom_size})";
        [/HAS_CUSTOM_SKIN]

    >   [SHOW_LEVEL_NUMBER]
        <span>{level}<span>
        <div class="tooltip__html">
            <span>Level {level}</span>
            <div class="campaign_level">
                <div class="campaign_level_label"><span class="campaign_level_label_completed">{progress.completed}</span>/{progress.limit}</div>
                <div class="campaign_level_progress" style="width: {progress.percent}%;"></div>
            </div>
        </div>
        [/SHOW_LEVEL_NUMBER]
    </div>

    <div class="progress_bar">
        <div class="progress_bar_progress" style="width: {progress.percent}%;"></div>
    </div>
</div>
