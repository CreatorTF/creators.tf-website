<div class="community flex">  
    <div class="profile_summary">
        <div class="profile_name">{title}</div>
        <div class="profile_motd">
            {summary}
        </div>
        <div class="profile_navigation flex">
            <div class="pro_nav_element"><a href="/community/{alias}"><i class="mdi mdi-home"></i> Home</a></div>
            <div class="pro_nav_element"><a href="/discord"><i class="mdi mdi-discord"></i> Discord</a></div>
        </div>
        [SHOWCASE_SERVERS]
        <div class="showcase">
            <div class="showcase_title"><i class="mdi mdi-server"></i> Серверы сообщества</div>
            {servers}
        </div>
        [/SHOWCASE_SERVERS]
        [SHOWCASE_DISCORD]
        <div class="showcase">
            <div class="showcase_title"><i class="mdi mdi-discord"></i> Discord сервер</div>
            {discords}
        </div>
        [/SHOWCASE_DISCORD]
    </div>
    <div class="avatar">
        <img src="{avatar}" class="offline">
        <div class="profile_follow_block">
            <div @click="ToggleGroupJoin('{id}')" class="profile_follow"><i class="mdi mdi-check-circle-outline"></i> Join</div>
        </div>
        <ul class="profile_navigation_menu">
            <li><a href="/community/{alias}/members">Members <b>0</b></a></li>
        </ul>
    </div>  
</div>