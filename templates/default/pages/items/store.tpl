<link rel='stylesheet' href='{CDN}/assets/styles/store{min}.css?v={Version}'>
<script type="text/javascript">
    let Creators_Store_Balance = {balance};
    const Creators_Store_Checkout_Max = {max_checkout_items};
</script>
<h2>Mann Co. Work-Shop</h2>
<div class="flex">
    <a href="/items">
        <div class="tf-button">Back to Items</div>
    </a>
</div>
<div class="post post-content" id="store-main">
    <div class="ws-info">
        <div class="flex">
            <div class="white text_color_active nav-primary-hor noshrink input-height">
                <ul>
                    <li class="active" onclick="Creators.Store.setFilters({feature: false}); CStore_NestedChangeActive(this)">Items</li>
                </ul>
            </div>
            <div class="fullwidth">
            </div>
            <div class="noshrink m-r-10">
                <div class="flex flex-vertical text-right">
                    <div class="color-666 tf2-secondary font-size-14">Balance:</div>
                    <div class="tf2-secondary font-size-18"><span id="store_balance">{balance}</span> <i class="mdi mdi-currency-usd-circle-outline"></i></div>
                </div>
            </div>
            <div class="noshrink">
                <input type="text" placeholder="Search..." class="input" name="" value="" ignore onkeyup="Creators.Store.setFilters({search: this.value})">
            </div>
        </div>
    </div>
    <div class="ws-info">
        <div class="flex nav-primary-hor">
            <div class="white noshrink img_opacity_active">
                <div ignore onclick="Creators.Store.setFilters({class: 'all'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small active">
                    <img src="{CDN}/assets/images/contracker/cyoa_classchoice_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'scout'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_scout_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'soldier'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_soldier_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'pyro'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_pyro_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'demo'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_demo_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'heavy'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_heavy_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'engineer'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_engineer_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'medic'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_medic_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'sniper'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_sniper_icon.svg" />
                </div>
                <div ignore onclick="Creators.Store.setFilters({class: 'spy'}); CStore_NestedChangeActive(this)" class="loadout-class-button tfclass-icon-small">
                    <img src="{CDN}/assets/images/contracker/cyoa_spy_icon.svg" />
                </div>
            </div>
            <div class="fullwidth">
            </div>
            <div class="noshrink p-r-5">
                <select id="store_filter_select" class="input" onchange="CStore_SelectChangeType(this)">
                    <option value="all">All Items</option>
                    <option value="weapon">Weapons</option>
                    <option value="cosmetic">Cosmetics</option>
                    <option value="tool">Tools</option>
                </select>
            </div>
            <div class="noshrink">
                <select id="store_sort_select" disabled class="input" onchange="CStore_SelectChangeSort(this)">
                    <option value="">Newest First</option>
                    <option value="new">Newest First</option>
                    <option value="old">Oldest First</option>
                    <option value="hipri">Highest Price</option>
                    <option value="lowpri">Lowest Price</option>
                    <option value="abc">Alphabetical</option>
                </select>
            </div>
        </div>
    </div>

    <div class="ws-info loading store-page-container" id="itemsgrid"></div>
    <div class="ws-info">
        <div class="flex">
            <div class="noshrink">
                <div class="tf-button" ignore style="position: relative;" id="store_checkout_race" onclick="Creators.Store.openCheckoutPage()"><i class="mdi mdi-cart"></i> (<span id="store_cart_items">0</span>)</div>
            </div>
            <div class="noshrink">
                <div class="cart-container">
                </div>
            </div>
            <div class="fullwidth"></div>
            <div class="noshrink">
                <div class="tf-button selected" ignore onclick="CStore_PrevPage()">&lt;</div>
                <span class="white color-666" style="width: 50px; text-align: center; display: inline-block;" id="store_pages">1/8</span>
                <div class="tf-button selected" ignore onclick="CStore_NextPage()">&gt;</div>
            </div>
        </div>
    </div>
</div>
<script src="{CDN}/assets/scripts/pages/store{min}.js?v={Version}"></script>
