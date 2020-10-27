<script type="module" src="{CDN}/assets/scripts/loadout/loadoutpreview.js?v={Version}"></script>
<script type="module" src="{CDN}/assets/scripts/items.js?v={Version}"></script>
<div class="post">
  <h2>#Page_Loadout_Title</h2>
  <div class="post-content">
    <div>
      <a href="/items"><div class="tf-button">#Navigation_Back</div></a>
      <a href="/profiles/{alias}/inventory"><div class="tf-button">#Navigation_Open_Inventory</div></a>
      <a href="/store"><div class="tf-button">#Navigation_Open_Profile</div></a>
    </div>
    <div class="loadout-class-selector">
      <center>
        <a href="/loadout/scout">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_scout.png" />
          </div>
        </a>
        <a href="/loadout/soldier">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_soldier.png" />
          </div>
        </a>
        <a href="/loadout/pyro">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_pyro.png" />
          </div>
        </a>
        <a href="/loadout/demo">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_demoman.png" />
          </div>
        </a>
        <a href="/loadout/heavy">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_heavy.png" />
          </div>
        </a>
        <a href="/loadout/engineer">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_engineer.png" />
          </div>
        </a>
        <a href="/loadout/medic">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_medic.png" />
          </div>
        </a>
        <a href="/loadout/sniper">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_sniper.png" />
          </div>
        </a>
        <a href="/loadout/spy">
          <div class="loadout-class-button">
            <img src="{CDN}/assets/images/tf_classicon_spy.png" />
          </div>
        </a>
      </center>
    </div>
  </div>
</div>
<div class="loadout-wrapper flex">
  <div class="loadout-slots-column">
    [PRIMARY]{PRIMARY}[/PRIMARY]
    [SECONDARY]{SECONDARY}[/SECONDARY]
    [MELEE]{MELEE}[/MELEE]
    [PDA]{PDA}[/PDA]
  </div>
  <div class="loadout-slots-preview">
    <div class="class-stand" id="loadout-preview"></div>
  </div>
    <div class="loadout-slots-column">
      [WEAR_1]{WEAR_1}[/WEAR_1]
      [WEAR_2]{WEAR_2}[/WEAR_2]
      [WEAR_3]{WEAR_3}[/WEAR_3]
      [ACTION]{ACTION}[/ACTION]
    </div>
</div>

<script type="text/javascript">
  var loadoutpreview_setupdata = {loadout};
</script>
