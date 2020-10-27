<div class="new_items_close" onclick="Creators.Actions.Modals.close()">X</div>
<div class="new_items_wrapper">
    <div class="new_items_header">{new_items_count} NEW ITEMS</div>
    <div id="new_items_container" class="new_items_container">
        {new_items_preview}
    </div>
    <div class="new_items_controls flex">
        <div class="noshrink btn_space">
            <div onclick="Preview_ShowPrevItem()" class="tf-button btn_prev_item">< View Prev</div>
        </div>
        <div class="fullwidth"></div>
        <div class="noshrink new_item_index">
            <span id="new_item_index">0</span>/{new_items_count}
        </div>
        <div class="fullwidth"></div>
        <div class="noshrink btn_space">
            <div onclick="Preview_ShowNextItem()" class="tf-button btn_next_item">View Next ></div>
            <a href="/my/inv"><div class="tf-button btn_backpack"><i class="mdi mdi-package"></i> Open Backpack</div></a>
        </div>
    </div>
</div>
