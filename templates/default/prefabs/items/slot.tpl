<div
  onclick="document.location.href='/loadout/{class}/{slot_url}'; this.classList.add('loading')"
  ignore
  class="slot"
  [SET]
  tooltip
  tooltip-timeout=0
  [/SET]
  style=" background-image: url({tool_target_image}), url({image});
          background-size: {tool_target_image_size}, auto 110%;
          background-position: {tool_target_image_position}, center -16px;
          border-color: {quality_color}">
  <div class="economy-item-icons">
    {icons}
  </div>
  <div style="color: {quality_color}" class="white loadout-slot-name">{name}</div>
  [SET]
  <div class="tooltip__html">
      <div style="color: {quality_color}"class="white p-t-10 p-b-5">{name}</div>
      <div class="descriptor">{description}</div>
      {attributes_html}
  </div>
  [/SET]
</div>
