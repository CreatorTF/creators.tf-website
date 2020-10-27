<script type="text/javascript">
    [NEW_ITEMS]
    const m_bShouldShowNewItems = true;
    [/NEW_ITEMS] [NO_NEW_ITEMS]
        const m_bShouldShowNewItems = false;
        [/NO_NEW_ITEMS]
</script>

<title>Community Scream Fortress Campaign :: Creators.TF</title>
<link rel="stylesheet" href="{CDN}/assets/styles/operation/halloween{min}.css?v={Version}">

<div class="flex flex-vertical wrapper fullscreen">
    <div class="fullcontainer noradius topbar">
        <div class="fixed_container flex">
            <div class="noshrink">
                <a href="/items"><div class="tf-button">Back to Items</div></a>
            </div>
            <div class="fullwidth"></div>
            <div class="noshrink">
                <div class="tf-button" onclick="Settings_ShowSettings()"><i class="mdi mdi-cog"></i> Open Settings</div>
            </div>
        </div>
    </div>
    <div class="fullheight drawer">
        <div class="fixed_container fullheight" style="max-width: 1200px;">
            <div class="flex fullheight">
                <div class="quests_wrapper noshrink">
                    <div class="quests_search">
                        <div class="flex">
                            <input class="input" onkeyup="CCHalloween_KeyUpSearch(this)" placeholder="Search Merasmissions..." type="text" name="search" id="quests_search" value="">
                            <div class="noshrink">
                                <div class="quest_search_remove" onclick="CCHalloween_ClearSearch()">X</div>
                            </div>
                        </div>
                        <div class="flex">
                            <div class="checkbox_field">
                                <div class="checkbox smaller">
                                    <input value="community" onchange="CCHalloween_FilterQuests()" checked type="checkbox">
                                    <div class="flex">
                                        <div class="checkmark noshrink"></div>
                                        <div class="flex flex-vertical">
                                            <div class="checklabel">Community Maps</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox_field">
                                <div class="checkbox smaller">
                                    <input value="bosses" onchange="CCHalloween_FilterQuests()" checked type="checkbox">
                                    <div class="flex">
                                        <div class="checkmark noshrink"></div>
                                        <div class="flex flex-vertical">
                                            <div class="checklabel">Bosses</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="checkbox_field">
                                <div class="checkbox smaller">
                                    <input value="official" onchange="CCHalloween_FilterQuests()" checked type="checkbox">
                                    <div class="flex">
                                        <div class="checkmark noshrink"></div>
                                        <div class="flex flex-vertical">
                                            <div class="checklabel">Official Maps</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="quests noshrink">
                        <div class="quest_loading_overlay loading"></div>
                        <div class="quests_container">{quests}</div>
                    </div>
                </div>
                <div class="preview fullwidth"></div>
            </div>
        </div>
    </div>
    <div class="fullcontainer noradius reward" style="padding-left: 0; padding-right: 0;">
        <div class="flex">
            <div class="noshrink levels flex">
                <div class="campaign_item_wrapper noshrink">
                    <div class="campaign_item" data-index="{campaign_item.index}" tooltip tooltip-top tooltip-duration=0>
                        <div class="campaign_item_image" style="background-image: url({campaign_item.image})"></div>
                        <div class="tooltip__html">
                            <div style="color: {campaign_item.quality_color}" class="white p-t-10 p-b-5">{campaign_item.name}</div>
                            <div class="descriptor">{campaign_item.description}</div>
                            {campaign_item.attributes}
                        </div>
                    </div>
                </div>
                <div class="campaign_levels fullwidth">
                    <div class="campaign_levels_title">Level <span id="campaign_next_level">{level.index}</span> Progress</div>
                    <div class="campaign_level" tooltip="Amount of souls you need to progress to the next level." tooltip-top>
                        <div class="campaign_level_label">
                            <span id="campaign_level_completed">{level.progress}</span>
                            /
                            <span id="campaign_level_limit">{level.limit}</span>
                        </div>
                        <div class="campaign_level_progress" id="campaign_level_bar" style="width: {level.percent}%;"></div>
                    </div>
                    <div class="campaign_levels_title">Total Souls Collected</div>
                    <div class="campaign_level" tooltip="Your total amount of souls collected during the event." tooltip-top>
                        <div class="campaign_level_label"><span id="campaign_total_completed">{progress.completion}</span>/{progress.limit}</div>
                        <div class="campaign_level_progress" id="campaign_total_bar" style="width: {progress.percent}%;"></div>
                    </div>
                    <div class="campaign_levels_title">Merasmissions Completed</div>
                    <div class="campaign_level" tooltip="Amount of completed merasmissions." tooltip-top>
                        <div class="campaign_level_label"><span id="campaign_quests_completed">{quests.progress}</span>/{quests.limit}</div>
                        <div class="campaign_level_progress" id="campaign_quests_bar" style="width: {quests.percent}%;"></div>
                    </div>
                </div>
            </div>
            <div class="progress_wrapper fullwidth">
                <div class="progress flex">
                    <div class="progress_element">
                        <div class="progress_label" tooltip-top="" tooltip-timeout="0" tooltip="You start here!" style="background-image: url(/cdn/assets/images/inventory/items/action/tome_gravel.png); transform: scale(1.5)"></div>
                    </div>
                    {elements}
                </div>
            </div>
        </div>
    </div>

    <script src="{CDN}/assets/scripts/pages/operation/halloween{min}.js?v=${Version}" charset="utf-8"></script>
