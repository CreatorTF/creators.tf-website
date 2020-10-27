<div class='mount'>
    <h2>#Page_Donate_Title</h2>
    <div class="post-content">
        <div class="donation-progress-bar vcontainer">
            <div class="container donation-progress-bar-inner" style="width:{goal.percent.capped}%;"></div>
            <div class="donation-progress-bar-pc donation-progress-text">{goal.percent}%</div>
            <div class="donation-progress-bar-gathered donation-progress-text">{goal.current}$</div>
            <div class="donation-progress-bar-monthly donation-progress-text">{goal.goal}$</div>
        </div>
        <br>
        <div class='flex error patreon-promote'>
            <div class="error_image">
                <i class="mdi mdi-patreon mdi-48px" style="transform: scale(1.7);color: #FFF;"></i>
            </div>
            <div class="error_summary">
                <h2>#Page_Donate_Embed_Title</h2>
                <div class="post-content"><p>#Page_Donate_Embed_Summary</p></div>
            </div>
        </div>
    </div>
    <div class="post-content">
        <p>#Page_Donate_Unconnected_Patreon</p>
        [PATREON_CONNECTED]
        <div class="showcase">
            <div class="showcase_title"><i class="mdi mdi-patreon"></i> #Page_Donate_Patreon_Connection_Title</div>
            <div class="showcase_profile embed" style="background-image: linear-gradient(90deg, #f86754, #c23927);">
                <div class="whitelink center" style="padding: 10px;">
                    <div class="avatar" style="margin: 0px;">
                        <img src="{patreon.avatar}" class="patreonConnectedAvatar">
                    </div>
                    <div class="miniprofile-data" style="padding: 0px 10px;">
                        <h2><i class="mdi mdi-patreon"></i> {patreon.name}</h2>
                        <span style="color:#fff;">#Page_Donate_Patreon_Connection_Donated({patreon.lifetime})</span>
                    </div>
                    <a href="/disconnect/patreon"><div class="showcase_join patreon_connectbutton" style="margin: 0px;">#Page_Donate_Patreon_Connection_Unconnect_Button</div></a>
                </div>
            </div>
        </div>
        <br>
        [/PATREON_CONNECTED]
        [PATREON_UNCONNECTED]
        <div class="showcase">
            <div class="showcase_title"><i class="mdi mdi-patreon"></i> #Page_Donate_Patreon_Connection_Title</div>
            <div class="showcase_profile embed" style="background-image: linear-gradient(90deg, #f86754, #c23927);">
                <div class="whitelink center" style="padding: 15px;">
                    <div class="avatar patreonicon center">
                        <i class="mdi mdi-patreon mdi-48px" style="transform: scale(1.1);color: #FFF;"></i>
                    </div>
                    <div style="margin: 0px 10px;">
                        <h2>#Page_Donate_Patreon_Connection_Unconnected_Title</h2>
                        <span style="color:#fff;">#Page_Donate_Patreon_Connection_Unconnected_Summary</span>
                    </div>
                    <div class="showcase_join patreon_connectbutton" style="margin: 0px"><a href="/connect/patreon" style="color: #ECECEC;">#Page_Donate_Patreon_Connection_Connect_Button</a></div>
                </div>
            </div>
        </div>
        <br>
        [/PATREON_UNCONNECTED]
    </div>
    <h2>Hall of Fame</h2>
    <div class="post-content">
        <p><b>#Page_Donate_Notes_Title</b>: #Page_Donate_Notes_Note_1</br>#Page_Donate_Notes_Note_2</p>
        <div id="mount_halloffame" class="haf-container"></div>
    </div>
</div>
<script src="{CDN}/assets/scripts/pages/halloffame.js?v={Version}" charset="utf-8"></script>
