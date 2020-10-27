<div class="modal_preview_wrapper flex flex-vertical" [SHOW_ITEM_NUMBER]data-index="{item_number}"[/SHOW_ITEM_NUMBER]>
    [HAS_MESSAGE]
    <div class="header_title">
        [MESSAGE_FOUND]You found <span>new items</span>:[/MESSAGE_FOUND]
        [MESSAGE_REWARD]You have been <span>rewarded</span> with:[/MESSAGE_REWARD]
        [MESSAGE_PURCHASED]You have <span>purchased</span>:[/MESSAGE_PURCHASED]
        [MESSAGE_DISTRIBUTED]<span>Community Support</span> has <span>Distributed</span> to you:[/MESSAGE_DISTRIBUTED]
        [MESSAGE_ITEM_UPGRADED]Your item has a <span>New Rank</span>:[/MESSAGE_ITEM_UPGRADED]
        [MESSAGE_CURRENCY]You recieved some <span>Mann Coins</span>:[/MESSAGE_CURRENCY]
        [MESSAGE_LOANER]You're <span>borrowing for a contract</span>:[/MESSAGE_LOANER]
    </div>
    [/HAS_MESSAGE]
    <div class="flex preview_wrapper">
        [SHOW_ITEM_NUMBER]
        <div class="preview_item_counter">
            <div class="new_items_item_counter_label">Item</div>
            <div class="new_items_item_counter_count">#{item_number}</div>
        </div>
        [/SHOW_ITEM_NUMBER]
        <div class="econ_item_image">
            <img class="item_image" src="{image}" alt="">
            <div class="economy-item-icons">
                {icons}
            </div>
        </div>
        <div class="econ_item_stats">
            <div style="color: {quality_color}" class="white p-t-10 p-b-5">{name}</div>
            <div class="descriptor" style="color: #fff;">{description}</div>
            {attributes_html}
        </div>
    </div>
</div>
