<div class='post'>
    <div class="relative" style="min-height:500px;">
        <h2>{name} <span class="qp-options-context">» #Economy_Items_Use_Action_Unbox</span></h2>
        <br/>
        <center>
            {lootbox}
            <br />
            <div class="vcontainer" style="color:#fff;width:60%;padding: 10px 0;">
                <div class="descriptor">{description}</div>
                {attributes}
            </div>
        </center>
        <br />
        <h2>#Page_OpenCase_Crate_Contents_Warning</h2>
        <br />
        <center>{contents}</center>

        <center>
            <a href="javascript:history.back()">
                <div class="tf-button">#Navigation_Back</div>
            </a>
            <div class="tf-button" ignore onclick="CItem_ButtonUseLootbox({index})">#Page_OpenCase_Button_Unbox</div>
        </center>
    </div>
</div>

<script src="{CDN}/assets/scripts/econ_items_manager{min}.js?v={Version}"></script>
<script src="{CDN}/assets/scripts/page_inventory{min}.js?v={Version}"></script>
