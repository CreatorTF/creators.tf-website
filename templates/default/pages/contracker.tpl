<body>
    <div class="contracker">
        <div class="cyoa-contracker-outlines">
            <div class="cyoa-dial-container">
                <div class="cyoa-dial"></div>
            </div>
        </div>
        <div class="cyoa-monitor">
            <div class="cyoa-off-fade">
                <div class="cyoa-overlay cyoa-shadow"></div>
                <div class="cyoa-content flex-col flex">
                    <div class="cyoa-overlay cyoa-noise"></div>
                    <div class="cyoa-overlay cyoa-preview loading"></div>
                    <div class="cyoa-toolbar flex">
                        <div class="cyoa-button" tooltip="Back" onclick="CEContracker_GotoUpperNode(this)"><i class="mdi mdi-arrow-left"></i></div>
                        <div class="cyoa-toolbar-directory-input" id="contracker_directory_field"></div>
                        <div class="cyoa-button" tooltip="Refresh Progress" onclick="CEContracker_RefreshProgress(this)"><i class="mdi mdi-refresh"></i></div>
                        <div class="cyoa-button" tooltip="Change Sound Volume" onclick="Settings_ShowSettings()"><i class="mdi mdi-volume-high"></i></div>
                        <div class="cyoa-button" tooltip="Disable Current Contract" onclick="CEContracker_ButtonSetContract(this, 0)"><i class="mdi mdi-folder-remove"></i></div>
                        <div class="cyoa-toolbar-profile flex">
                            <div class="cyoa-profile-name">{username}</div>
                            <div class="cyoa-avatar-wrapper">
                                <img class="cyoa-avatar" src="{avatar}">
                            </div>
                        </div>
                        <div class="cyoa-button" tooltip="Toggle Fullscreen Mode" onclick="CEContracker_ToggleFullscreen()"><i class="mdi mdi-fullscreen"></i></div>
                    </div>
                    <div class="cyoa-mount" inkable>
                        {pages}
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="{CDN}/assets/scripts/contracker{min}.js?v={Version}"></script>
