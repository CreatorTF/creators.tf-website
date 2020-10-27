<tr class="servertable-item [TIMEOUT]bancolor[/TIMEOUT]">
  <td class="server-list-col centered-text">{id}</td>
  <td class="server-list-col centered-text small-item-col"><img src="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.4.3/flags/4x3/{region}.svg"></td>
  <td class="server-list-col centered-text">{hostname}</td>
  <td class="server-list-col centered-text">{online}/{maxplayers}</td>
  <td class="server-list-col centered-text map-cover"><div class="cover" style="background-image:url(/api/mapthumb?map={map})"></div>{map_f}</td>
  <td class="server-list-col centered-text" tooltip="#Page_Server_List_Heartbeat_Tooltip">{time}s ago</td>
  <td>
    [UNLOCKED]<a href="steam://connect/{ip}:{port}"><div class="tf-button centered-text">CONNECT</div></a>[/UNLOCKED]
    [LOCKED]<div tooltip="This server is password protected. You are not able to join it." style="cursor: help;" class="centered-text">🔒</div>[/LOCKED]
  </td>
  <td>
    [UNLOCKED]<div ignore onclick="prompt('{hostname}\nIP address of the server:','{ip}:{port}')" class="tf-button centered-text"> IP </div></a>[/UNLOCKED]
  </td>
  <td class="server-list-col centered-text" [TIMEOUT]title="#Page_Server_List_Timeout_Message"[/TIMEOUT]>[OK]<i style="color: green;" class="mdi mdi-check-circle"></i>[/OK][TIMEOUT]<i style="color: red;" class="mdi mdi-alert-circle"></i>[/TIMEOUT]</td>
</tr>
