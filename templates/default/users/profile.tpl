<div class="flex">
    <div class="flex profile_info">
        <div class="avatar">
            <img src="{avatar}" class="offline">
        </div>
        <div class="profile_summary">
            <div class="profile_name">{username} {special}</div>
            <div class="profile_motd">{summary}</div>
        </div>
    </div>
    <div class="profile_leveling">
        [FAKE]
        <p><i class="mdi mdi-alert"></i><b> #Profile_Fake_Profile_Title</b><br><br>#Profile_Fake_Profile_Text</p>
        [/FAKE]
        <ul class="profile_navigation_menu">
            [REAL]<li><a href="https://steamcommunity.com/profiles/{steamid}"><b>Steam <i class="mdi mdi-steam"></i></b></a></li>[/REAL]
            [REAL]<li><a href="/profiles/{alias}/inventory">#Navigation_Inventory <i class="mdi mdi-package"></i></a></li>[/REAL]
            [REAL]<li><a href="/posts?authors[]={alias}">Posts <i class="mdi mdi-newspaper"></i></a></li>[/REAL]
            [REAL]<li><a href="/submissions?authors[]={steamid}">Submissions <i class="mdi mdi-store"></i></a></li>[/REAL]
        </ul>
        [DEV]
        [RICH_PRESENCE]
        Currently on:<br/><b>{server_name}</b><br/>
        <a href="steam://connect/{ip}:{port}">Join</a>
        [/RICH_PRESENCE]
        [/DEV]
    </div>
</div>
<br>
{comments}
