<div class='post'>

    [HIDE] TODO: Make a proper and not hardcoded server list. [/HIDE]

    <div class="warningboxalert">
        <div class="error_image">
            <img src="{CDN}/assets/images/warning_alert.png" style="margin: 0;">
        </div>
        <span class="error_summary">
            Economy features are currently disabled on all servers due to performance issues. We're working on resolving them as soon as possible.
        </span>
    </div>
    <br/>

    <center>
        <h2>Select a Creators.TF server to play with new community created content!</h2>
        <p class="post-content">Servers run vanilla Team Fortress 2, with new community-created cosmetics and weapons.</p>
    </center>

    <div class="serverlist">
        <table class="servertable">
            <tr>
                <th><a href="/servers?sort_by=id&method={id_dir}">#Page_Server_List_ID {id_arrow}</a></th>
                <th><a href="/servers?sort_by=region&method={region_dir}">#Page_Server_List_Region {region_arrow}</a></th>
                <th><a href="/servers?sort_by=hostname&method={hostname_dir}">#Page_Server_List_Hostname {hostname_arrow}</a></th>
                <th><a href="/servers?sort_by=online&method={online_dir}">#Page_Server_List_Online {online_arrow}</a></th>
                <th><a href="/servers?sort_by=map&method={map_dir}">#Page_Server_List_Map {map_arrow}</a></th>
                <th><a href="/servers?sort_by=heartbeat&method={heartbeat_dir}">#Page_Server_List_Heartbeat {heartbeat_arrow}</a></th>
                <th>#Page_Server_List_Connect</th>
                <th>IP</th>
                <th>#Page_Server_List_Status</th>
            </tr>
            {servers}
        </table>
    </div>

    <br />

    <center id="balancemod">
        <h2>Play on Creators.TF x Balance Mod servers!</h2>
        <p class="post-content">Run rebalanced version of Team Fortress 2, with some weapons being totally different and many new features for some classes.</br>These servers support Creators.TF custom items and contracts.</br>Check <a
                target="_blank" href="https://www.balancemod.tf/moddedweapons">balancemod.tf</a> for more information.</p>
    </center>
    [HIDE]
    <div class="warningboxalert">
        <div class="error_image">
            <img src="{CDN}/assets/images/warning_alert.png?width=100">
        </div>
        <div class="error_summary">
            message goes here
        </div>
    </div>
    [/HIDE]
    <div class="serverlist">
        <table class="servertable">
            <tr>
                <th><a href="/servers?sort_by=id&method={id_dir}">#Page_Server_List_ID {id_arrow}</a></th>
                <th><a href="/servers?sort_by=region&method={region_dir}">#Page_Server_List_Region {region_arrow}</a></th>
                <th><a href="/servers?sort_by=hostname&method={hostname_dir}">#Page_Server_List_Hostname {hostname_arrow}</a></th>
                <th><a href="/servers?sort_by=online&method={online_dir}">#Page_Server_List_Online {online_arrow}</a></th>
                <th><a href="/servers?sort_by=map&method={map_dir}">#Page_Server_List_Map {map_arrow}</a></th>
                <th><a href="/servers?sort_by=heartbeat&method={heartbeat_dir}">#Page_Server_List_Heartbeat {heartbeat_arrow}</a></th>
                <th>#Page_Server_List_Connect</th>
                <th>IP</th>
                <th>#Page_Server_List_Status</th>
            </tr>
            {servers_balancemod}
        </table>
    </div>
</div>
