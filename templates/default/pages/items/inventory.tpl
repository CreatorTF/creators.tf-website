<script type="text/javascript">
  const Creators_Inventory_Profile = "{alias}";
  const Creators_Inventory_Pages = {pages};
  [OWNER]const Creators_Inventory_bIsOwner = true;[/OWNER]
  [NOT_OWNER]const Creators_Inventory_bIsOwner = false;[/NOT_OWNER]
</script>
<div class="flex">
  [OWNER]
  <a href="/items">
    <div class="tf-button">Back to Items</div>
  </a>
  [/OWNER]
  [NOT_OWNER]
  <a href="/profiles/{alias}">
    <div class="tf-button">Back to Profile</div>
  </a>
  [/NOT_OWNER]
</div>
<div id="inventory-main" class="flex">
  <div class="noshrink inventory-nav-arrow-wrapper">
    <div class="inventory-nav-arrow inv-nav-arrow-left click_dragging" onclick="Inventory.prevPage()"><</div>
  </div>
  <div style="box-sizing: border-box; width: 100%;">
    <div class="inv-fadein" id="inventory_section_overflow">
      <div class="flex inv-control">
        <div class="fullwidth">You've received this item, but you don't have room in your backpack.</div>
        <div class="fullwidth">
          <center>
            <div id="inventory_overflow_slot"></div>
          </center>
        </div>
        <div class="fullwidth">
          <div class="m-b-10">Delete any item from your backpack to make room or press delete to throw your new item away.</div>
          <div>
            <div class="tf-button" id="inventory_overflow_delete"><i class="mdi mdi-delete"></i>&nbsp;Delete</div>
            <div class="tf-button" tooltip='Your Creators.TF inventory has limited space. This item has oveflown the maximum allowed limit of items in your inventory. If you want to use it: expand your backpack using Backpack Expander or delete any other item from your backpack to free up space.'>?</div>
          </div>
        </div>
      </div>
    </div>
    <div id="inventory-items">
      <div class="inv-noitems" style="display: none;">
        <p>No items have been found in this inventory. <br/>You can get more by purchasing them in the <a href="/store">Mann Co. Work-shop</a> or by completing some <a href="/contracker">Contracts</a>.</p>
      </div>
      <div class="itemsgrid loading"></div>
    </div>
    <div class="inv-fadein" id="inventory_section_selected">
      <div class="flex inv-control inv-selected-outline">
        <div class="inv-label fullwidth"><i class="mdi mdi-checkbox-marked-outline"></i>&nbsp;<span id="inventory_label_counter">0</span> Items Selected</div>
        <div class="noshrink p-l-10" style="max-width: 500px;">
          <div class="tf-button click_dragging" id="inventory_button_selectpage" onclick="Inventory.selectPage()"><i class="mdi mdi-checkbox-multiple-marked"></i> Select Page</div>
          <div class="tf-button click_dragging" id="inventory_button_desellect" onclick="Inventory.desellectSelected()"><i class="mdi mdi-checkbox-multiple-blank-outline"></i> Deselect All</div>
          <div class="tf-button click_dragging" id="inventory_button_scrap" onclick="CInventory_ButtonBulkScrap()"><i class="mdi mdi-recycle"></i> Scrap Selected</div>
          <div class="tf-button click_dragging" id="inventory_button_delete" onclick="CInventory_ButtonBulkDelete()"><i class="mdi mdi-delete"></i> Delete Selected</div>
        </div>
      </div>
    </div>
    <div class="inv-control inv-nav flex">
      <div class="fullwidth">
        <input type="text" class="left input" placeholder="Search..." onkeyup="Inventory.searchString(this)">
      </div>
      [OWNER]
      <div class="noshrink flex p-l-5">
        <select id="inventory_sort_select" class="input" placeholder="Sort backpack">
          <option value="" selected disabled>Sort Backpack</option>
          <option value="0">Sort by Quality</option>
          <option value="1">Sort by Type</option>
          <option value="2">Sort by Class</option>
        </select>
      </div>
      [/OWNER]
    </div>
    <div class="flex inv-control">
      <div class="inv-label">Page:</div>
      <div class="inv-control inv-pagination"></div>
    </div>
  </div>
  <div class="noshrink inventory-nav-arrow-wrapper">
    <div class="inventory-nav-arrow inv-nav-arrow-right click_dragging" onclick="Inventory.nextPage()">></div>
  </div>
</div>
<script src="{CDN}/assets/scripts/page_inventory{min}.js?v={Version}"></script>
<script src="{CDN}/assets/scripts/econ_items_manager{min}.js?v={Version}"></script>
<script src="{CDN}/assets/scripts/econ_items_manager_bulk{min}.js?v={Version}"></script>
<script src="{CDN}/assets/scripts/pages/inventory{min}.js?v={Version}"></script>
<script src="{CDN}/assets/scripts/actions/items/manager{min}.js?v={Version}"></script>
