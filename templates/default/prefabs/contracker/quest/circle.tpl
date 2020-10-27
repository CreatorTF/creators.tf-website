<div
    class="quest quest-circular[INACTIVE] inactive[/INACTIVE]"
    style="left: calc({posX} - 40px); top: calc({posY} - 40px); transform: scale(0.8);"
    data-title="{title}"
    data-connect="{connect}"
    data-index="{index}"

    data-posx="{posX}"
    data-posy="{posY}"
    onclick="CEContracker_ShowPreview(this)"
>
    <svg height="80" width="80">
        <circle cx="40" cy="40" r="38" stroke-dasharray="251.2" stroke-dashoffset="175.84" stroke-width="4"></circle>
        <circle cx="40" style="transform:rotate(120deg);" cy="40" r="38" stroke-dasharray="251.2" stroke-dashoffset="175.84" stroke-width="4"></circle>
        <circle cx="40" style="transform:rotate(240deg);" cy="40" r="38" stroke-dasharray="251.2" stroke-dashoffset="175.84" stroke-width="4"></circle>
    </svg>
    <div class="quest-name">{name}<br>
        [LOOT_CURRENCY]<i class="mdi mdi-currency-usd-circle-outline"></i>[/LOOT_CURRENCY]
        [LOOT_ITEM]<i class="mdi mdi-package"></i>[/LOOT_ITEM]
    </div>
    <div class="quest-icon" style="-webkit-mask-image: url({icon}); transform: scale(1);"></div>
</div>
