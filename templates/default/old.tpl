<!DOCTYPE html>
<html>

<head>
    {headers}
    <link rel='stylesheet' href='{CDN}/assets/styles/style.#StyleExtension?v=#V'>
    <link rel='stylesheet' href='{CDN}/assets/styles/contracts.css?v=#V'>
    <meta charset="utf-8">
    <meta property="og:title" content="{og:title}" />
    <meta property="og:type" content="{og:type}" />
    <meta property="og:url" content="{og:url}" />
    <meta property="og:image" content="{og:image}" />
    <meta property="og:description" content="{og:description}" />
    <script src="#Vue"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/vue2-timeago@1.2.2/dist/vue2-timeago.js'></script>
    <script src="https://unpkg.com/vue-i18n/dist/vue-i18n.js"></script>
    <script src="{CDN}/assets/scripts/cookies.js?v=#V"></script>
    <link href="https://cdn.materialdesignicons.com/2.8.94/css/materialdesignicons.min.css" rel="stylesheet">
</head>

<body l="{Language}" v="#V">
    <div id="core" :class="{loading: !loaded, loaded: loaded}">
        <div class="quickplay-status-wrapper" :class="{visible: quickplay.search.active}">
            <div class="quickplay-status">
                <div class="qps-menu">
                    <div class="qps-title qp-white">Search in Progress</div>
                    <div class="qps-log qp-white">{{quickplay.search.log[quickplay.search.log.length - 1] || "..."}}</div>
                    <div class="qps-progress-wrapper">
                        <div class="qps-progress" :style="{width: quickplay.search.progress+'%'}"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="quickplay-status-wrapper" :class="{visible: socket.failed}">
            <div class="quickplay-status">
                <div class="qps-menu">
                    <div class="qps-title qp-white">CONNECTION FAILED</div>
                    <div class="qps-log qp-white">Lost connection to quickplay coordinator. Trying to reconnect...</div>
                </div>
            </div>
        </div>
        <div class="quickplay-status-wrapper result" :class="{visible: quickplay.search.result}">
            <div class="quickplay-status" v-if="quickplay.search.result">
                <div class="qps-menu">
                    <div class="qps-title qp-white">Search Results</div>
                    <div class="qps-log qp-white" v-if="quickplay.search.result.length == 0">Unfortunately we couldn't find any suitable game server for your request. Try changing the criteria or try again later.</div>
                    <div v-if="quickplay.search.result.length > 0">
                        <div class="qps-log qp-white">Warning! Search results may not be 100% accurate.</div>
                        </br>
                        <div v-for="server in quickplay.search.result"><server :id="server.id"></server></div>
                        </br>
                    </div>
                </div>
                <div class="modal-x qp-white" @click="quickplay.search.result = undefined">X</div>
            </div>
        </div>
        <div class="modal-container modal-close" @click.self="CloseModal" :class="{visible: modals.active.index > 0}">
            <div class="modal container" :class="{visible: modals.active.index == modals.indexes.MODAL_LOGINBOX}" v-if="modals.active.index == modals.indexes.MODAL_LOGINBOX">
                <div class="modal-header">
                    <h2>{{modals.active.title}}</h2>
                    <div @click.self="CloseModal" class="modal-close modal-x">X</div>
                </div>
                <div class="modal-content">
                    <p v-for="string in modals.active.content">{{string}}</p>
                    <p><center><a href='/login?redirect={uri}'><img src='{CDN}/assets/images/steam_login.png'></a></center></p>
                </div>
            </div>
            <div class="modal container contracker" :class="{visible: modals.active.index == modals.indexes.MODAL_CONTRACKER}"  v-if="modals.active.index == modals.indexes.MODAL_CONTRACKER">
                <div class="modal-header">
                    <h2>{{modals.active.title}}</h2>
                    <div @click.self="CloseModal" class="modal-close modal-x">X</div>
                </div>
                <iframe seamless height="700px" width="980px" src="/iframe/contracker" frameborder="0" style="margin:0 auto;max-width:100%;display: block;"></iframe>
            </div>
            <div class="quickplay-wrapper flex container" :class="{visible: modals.active.index == modals.indexes.MODAL_QUICKPLAY}">
                    <div class="quickplay-header">
                        <h1>Community Quickplay</h1>
                        <h2>Map Selection</h2>
                        <p class="quickplay-maps-counter">{{Quickplay_GetActiveMapCount}} maps selected</p>
                    </div>
                    <div class="vcontainer" :class="{loading: !quickplay.loaded}">
                        <div v-if="quickplay.loaded" class="scrollY">
                            <div v-for="group in quickplay.groups" class="qp-group">
                                <div class="qp-group-name container">
                                    <div class="checkbox">
                                        <input type="checkbox" v-model="group.enabled" @change="Quickplay_ToggleAllGamemodes(group)">
                                        <div class="checkmark"></div>
                                    </div>
                                    <span class="qp-white">{{group.name}}</span>
                                </div>
                                <div v-for="gamemode in group.gamemodes" class="qp-group-elements">
                                    <div class="gp-gamemode">
                                        <div class="gp-gamemode-preview">
                                            <div class="gp-gamemode-image" :class="{disabled: !gamemode.enabled}" :style="{backgroundImage: 'url('+gamemode.image+')'}"></div>
                                            <div class="gp-gamemode-overlay">
                                                <div class="gp-gamemode-title" :class="{disabled: !gamemode.enabled}">
                                                    <span class="qp-white">{{gamemode.name}}</span>
                                                    <div class="checkbox">
                                                        <input type="checkbox" v-model="gamemode.enabled" @change="Quickplay_ToggleAllMaps(gamemode, group)">
                                                        <div class="checkmark"></div>
                                                    </div>
                                                </div>
                                                <div class="gp-gamemode-desc qp-white">
                                                    {{gamemode.motd}}
                                                </div>
                                                <div @click="$set(gamemode,'viewMaps',!gamemode.viewMaps)" class="gp-gamemode-maps-button">{{gamemode.viewMaps?"▲":"▼"}} View Maps</div>
                                            </div>
                                            <div class="gp-gamemode-maps" :class="{active: gamemode.viewMaps}">
                                                <div v-for="map in gamemode.maps" class="gp-gamemode-map" :class="{active: map.enabled}">
                                                    <div class="checkbox">
                                                        <input v-model="map.enabled" type="checkbox" @change="Quickplay_OnMapToggles(gamemode, group)">
                                                        <div class="checkmark"></div>
                                                    </div>
                                                    <span class="qp-white">{{map.name}}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="quickplay-options-manager" :class="{visible: quickplay.optionsView}">
                            <div class="scrollY">
                                <div class="qp-group-name container">
                                    <span class="qp-white">Search Settings</span>
                                </div>
                                <div class="qp-settings-checkbox">
                                    <div class="flex">
                                        <div class="checkbox">
                                            <input v-model="quickplay.options.restrictToRegion" type="checkbox">
                                            <div class="checkmark"></div>
                                        </div>
                                        <div class="checkbox-label">
                                            <span class="qp-white">Restrict search results to local region.<br><span style="opacity: .5">If set to False, search result will contain any worldwide server. If True, only servers in local region. (e.g. only European servers, if you are in Europe)</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="quickplay-find-wrapper">
                        <div class="qp-options-btn" @click="quickplay.optionsView = !quickplay.optionsView">{{quickplay.optionsView?"▲":"▼"}} Options</div>
                        <div class="qp-find-btn" @click="Quickplay_StartSearch()" :class="{disabled: socket.failed || Quickplay_GetActiveMapCount == 0 || quickplay.search.active}">START SEARCH</div>
                    </div>
                </div>
        </div>
        <div class='container header'>
            <a href="/"><img src='{CDN}/assets/images/tf2_logo.png' class='header-logo'></a>
            <ul class='header-links white'>
                <li><a href="/videos"><i class="mdi mdi-youtube"></i> #Header_Nav_VideoFeed</a></li>
                <li @click="CallModal(modals.indexes.MODAL_CONTRACKER, 'Community Contracker', '', true);"><a href="#"><i class="mdi mdi-tablet"></i> ConTracker</a></li>
            </ul>
            [LOGIN]
            <div class='header-user'>
                <a href='/profiles/{alias}'><img src='{avatar}' class='header-avatar'></a>
                <div class='header-username'>
                    <div class='white username'>{username}</div>
                    <i class="mdi mdi-logout"></i>
                    <span class='logout'><a href='/logout'>#Header_Profile_Logout</a></span>
                </div>
            </div>
            [/LOGIN]
            [NOLOGIN]
            <div class='header-user'>
                <div class="steam_login_button"><a href="/login?redirect={uri}"><img src="{CDN}/assets/images/steam_login.png"></a></div>
            </div>
            [/NOLOGIN]
        </div>
        <div class='logo-container'>
            <img class='logo-img' src='{CDN}/assets/images/tf2_square_event_sf2019.png'>
        </div>
        <div class='main'>
            <div class='container pre-main'>
                <ul class='main-links white'>
                    <li :class="{'focus': location.pathname.startsWith('/feed')}"><a href="/feed">#Header_Nav_Feed</a></li>
                    <li :class="{'focus': location.pathname.startsWith('/videos')}"><a href="/videos">#Header_Nav_VideoFeed</a></li>
                </ul>
                <div class='play-banner stamping' @click="CallModal(modals.indexes.MODAL_QUICKPLAY)">
                    <div class='small-container'>
                        <img class='small-logo' src='{CDN}/assets/images/small_logo.png'>
                    </div>
                    <div class='play-button white'>
                        <span>#PreMain_Button_Play</span>
                    </div>
                </div>
            </div>
            <div class="flex bicol">
                <div class='posts'>
                    {content}
                </div>
                [RIGHT]
                <div class='right-panels'>
                    <div class='container monthly-players infobox'>
                        <div class="panel-title white">#Panels_Monthly_Title</div>
                        <div class='vcontainer monthly'>{monthly}</div>
                    </div>
                    <img src="{CDN}/assets/images/banner_workshop.png" class='banner-right'>
                    <div class='container infobox'>
                        <div class="panel-title white">#Panels_Links_Title</div>
                        <div class='vcontainer links'>
                            <a href="https://steamcommunity.com/groups/CreatorsTF">
                                <div><i class="mdi mdi-steam-box link-mini"></i>Steam Group</div>
                            </a>
                            <a href="https://twitter.com/CreatorsTF">
                                <div><i class="mdi mdi-twitter-box link-mini"></i>Twitter</div>
                            </a>
                            <a href="https://reddit.com/r/tf2">
                                <div><i class="mdi mdi-reddit link-mini"></i>Subreddit</div>
                            </a>
                        </div>
                    </div>
                    <div class='container infobox'>
                        <div class="panel-title white">#Panels_Locale_Title</div>
                        <div class='vcontainer language'>
                            <select @change="ChangeLocales($event)">
                                <option v-for="loc in locales" :selected="loc.code == locale" :value="loc.code">
                                    {{loc.title}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                [/RIGHT]
            </div>
            <div class="footer_pre"></div>
            <div class="footer flex">
                <div class="col flex">
                    <a href="https://twitter.com/palettestds"><div class="company_copyright palette"></div></a>
                    <div class="copyright">#Footer_Palette_Copyright | 2019</br>#Footer_Palette_Copyright_Text</br><a href="/terms">#Terms_Of_Service</a> | <a href="/privacy">#Privacy_Policy</a> | #V</div>
                </div>
                <div class="col flex">
                    <a href="https://valvesoftware.com"><div class="company_copyright valve"></div></a>
                    <div class="copyright">#Footer_Valve_Copyright</div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="{CDN}/assets/scripts/app.js?v=#V"></script>
</html>