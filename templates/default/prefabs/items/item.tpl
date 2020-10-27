<div
    class="item [CONTEXT]contextmenu[/CONTEXT] {classname}"
    style=" background-image: url({tool_target_image}), url({image}?width=90);
    background-size: {tool_target_image_size}, {image_size};
    background-position: {tool_target_image_position}, center center;
    border-color: {quality_color}"
    data-name="{name}"
    data-image="{image}"
    data-quality="{quality}"
    data-quality_color="{quality_color}"
    data-index="{index}"
    data-type="{type}"
    data-scrap="{scrap}"
    [CAN_DELETE]data-can_delete="true"[/CAN_DELETE]
    [CAN_SCRAP]data-can_scrap="true"[/CAN_SCRAP]
    tooltip
    tooltip-timeout=0
    {dom_attributes}

    [PURCHASE]
    ignore
    onclick="CStore_AddElementToCart(this)"
    [/PURCHASE]
    [INSPECT]
    onclick="Creators.Items.instance.inspect({index}[OWNER], true[/OWNER])"
    sendevent
    [/INSPECT]
    [EQUIP]
    onclick="CItem_ButtonEquip(this, {index}, '{class}', '{slot}')"
    sendevent
    [/EQUIP]
    [USE]
    onclick="CItem_ButtonUse(this, {tool}, {index})"
    [/USE]>
        <div class="economy-item-icons">
            {icons}
        </div>
    <div class="tooltip__html">
        <div style="color: {quality_color}"class="white p-t-10 p-b-5">{name}</div>
        <div class="descriptor">{description}</div>
        {attributes_html}
    </div>
    [CONTEXT]
    <div class="contextmenu__html">
        <ul>
            [CAN_USE]<a href="/items/use/{index}"><li>Use</li></a>[/CAN_USE]
            [CAN_CAMPAIGN]<a href="/campaign/{campaign}"><li>Open Campaign Page</li></a>[/CAN_CAMPAIGN]
            [HIDE][CAN_INSPECT]<li ignore onclick="Preview_ShowSpecificItem({index})">Inspect</li>[/CAN_INSPECT][/HIDE]
            [CAN_EQUIP]
            <li>
                Go To Loadout ⮞
                <ul>
                [CAN_EQUIP_SCOUT]<a href="/loadout/scout"><li>Scout</li></a>[/CAN_EQUIP_SCOUT]
                [CAN_EQUIP_SOLDIER]<a href="/loadout/soldier"><li>Soldier</li></a>[/CAN_EQUIP_SOLDIER]
                [CAN_EQUIP_PYRO]<a href="/loadout/pyro"><li>Pyro</li></a>[/CAN_EQUIP_PYRO]
                [CAN_EQUIP_DEMOMAN]<a href="/loadout/demo"><li>Demoman</li></a>[/CAN_EQUIP_DEMOMAN]
                [CAN_EQUIP_HEAVY]<a href="/loadout/heavy"><li>Heavy</li></a>[/CAN_EQUIP_HEAVY]
                [CAN_EQUIP_ENGINEER]<a href="/loadout/engineer"><li>Engineer</li></a>[/CAN_EQUIP_ENGINEER]
                [CAN_EQUIP_MEDIC]<a href="/loadout/medic"><li>Medic</li></a>[/CAN_EQUIP_MEDIC]
                [CAN_EQUIP_SNIPER]<a href="/loadout/sniper"><li>Sniper</li></a>[/CAN_EQUIP_SNIPER]
                [CAN_EQUIP_SPY]<a href="/loadout/spy"><li>Spy</li></a>[/CAN_EQUIP_SPY]
                </ul>
            </li>
            [/CAN_EQUIP]
            [CAN_ADD_CART]<li>Add to Cart</li>[/CAN_ADD_CART]
            [CAN_STEAM_VOTE]<a target="_blank" href="https://steamcommunity.com/sharedfiles/filedetails/?id={workshop_id}"><li>Vote on Steam Workshop</li></a>[/CAN_STEAM_VOTE]
            [CAN_SCRAP]<li ignore onclick="CInventory_ButtonScrapItem({index}, {scrap})">Scrap</li>[/CAN_SCRAP]
            [CAN_DELETE]<li ignore onclick="CInventory_ButtonDeleteItem({index})">Delete</li>[/CAN_DELETE]
        </ul>
    </div>
    [/CONTEXT]
</div>
