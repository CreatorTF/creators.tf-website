<div class='post'>
  <div class="relative" style="min-height:500px;">
    <h2>{title} <span class="qp-options-context">» Use with </span>{paint}</h2>
    <br />
    <center>
      {items}
      <br />
      <div class="vcontainer" style="color:#fff;width:60%;padding: 10px 0;">
        <div class="descriptor">{description}</div>
        {attributes}
      </div>
    </center>
    <br />
    <center>
      <a href="javascript:history.back()"><div class="tf-button">#Navigation_Back</div></a>
      <div class="tf-button" onclick="CItem_ButtonUseConfirm({item-index}, {target-index})">Apply this Tool</div>
    </center>
  </div>
</div>
<script src="{CDN}/assets/scripts/econ_items_manager{min}.js?v={Version}"></script>
<script src="{CDN}/assets/scripts/page_inventory{min}.js?v={Version}"></script>
