<div class="modal_preview_wrapper" [SHOW_ITEM_NUMBER]data-index="{item_number}"[/SHOW_ITEM_NUMBER]>
    <div class="preview_wrapper">
        <div class="econ_item_stats">
            <div style="color: {quality_color}" class="white p-t-10 p-b-5">{name}</div>
            {attributes_html}
        </div>
        <div class="econ_item_image">
            <img class="item_image" src="{image}" alt="">
        </div>
    </div>
    <div class="item_crate_effect" style="background-image: url({item_crate_image});"></div>
</div>
