<script type="text/javascript">
  let g_FilterTags = {tags.json};
</script>
<link rel="stylesheet" href="{CDN}/assets/styles/workshop{min}.css?v={Version}">
<div class='mount'>
  <h2>Content Submissions</h2>
  <div class="post-content">
    <div class="m-b-10 flex">
      <div class="fullwidth">
        <ul class="ws-feed-nav no-offsets whitelink ul-dropdown">
          <li><a href="/submissions">Home</a></li>
          <li>
            <a>Browse ▼</a>
            <ul class="ul-dropdown-child">
              <li><a href="/submissions?requiredtags[]=Map">Maps</a></li>
              <li><a href="/submissions?requiredtags[]=Cosmetic">Cosmetics</a></li>
              <li><a href="/submissions?requiredtags[]=Taunt">Taunts</a></li>
              <li><a href="/submissions?requiredtags[]=Unusual Effect">Unusual Effects</a></li>
              <li><a href="/submissions?requiredtags[]=War Paint">War Paints</a></li>
              <li><a href="/submissions?requiredtags[]=Weapon">Weapons</a></li>
            </ul>
          </li>
          <li>
            <a>Status ▼</a>
            <ul class="ul-dropdown-child">
              <li><a href="/submissions?status=0">On Moderation</a></li>
              <li><a href="/submissions?status=1">Pending</a></li>
              <li><a href="/submissions?status=2">Compatible</a></li>
              <li><a href="/submissions?status=3">Introduced</a></li>
              <li><a href="/submissions?status=4">Added</a></li>
              <li><a href="/submissions?status=5">Incompatible</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="flex">
    <div class="ws-content">
      <div>
        {tags}
      </div>
      <div class="ws-info flex">
        <div class="fullwidth color-666">{actual_count} from {total_count} entries</div>
        <div class="noshrink color-666">Sort by:&nbsp;</div>
        <div class="noshrink">
          <ul class="ul-row-right no-offsets whitelink ul-dropdown">
          <li>
            <a>{sort_type_display} ▼</a>
            <ul class="ul-dropdown-child">
              <li><a href="{href_sort_recent}">Most Recent</a></li>
            </ul>
          </li>
          </ul>
        </div>
      </div>
      <div>
        {content}
        {nav}
      </div>
    </div>
    <div class="ws-side">
      [LOGIN]
      <div class="ws-info">
        <div class="incontainer whitelink ws-link-menu">
          <div class="flex">
            <div style="margin-right: 3px;"><i class="mdi mdi-plus-circle-outline"></i></div>
            <div><a href="/submission/create">Create Submission</a></div>
          </div>
          <div class="flex">
            <div style="margin-right: 3px;"><i class="mdi mdi-account"></i></div>
            <div><a href="/submissions?authors[]={steamid}">My Submissions</a></div>
          </div>
          [HIDE]
          <div class="flex">
            <div style="margin-right: 3px;"><i class="mdi mdi-help"></i></div>
            <div><a href="/help/submissions">What Is This?</a></div>
          </div>
          [/HIDE]
        </div>
      </div>
      [/LOGIN]
      <div class="ws-info">
        <div>
          <form method="get">
            {params}
            <input placeholder="Search..." class="input" type="text" name="search" value="{search}">
          </form>
        </div>
      </div>
      <div class="ws-info">
        <div class="graytext">Filter by types:</div>
        <div class="ws-info-container">
          {tags.filter.types}
        </div>
        <br>
        <div class="graytext">Filter by themes:</div>
        <div class="ws-info-container">
          {tags.filter.themes}
        </div>
        <br>
        <div class="graytext">Filter by classes:</div>
        <div class="ws-info-container">
          {tags.filter.classes}
        </div>
        <br>
        <div class="graytext">Filter by game modes:</div>
        <div class="ws-info-container">
          {tags.filter.gamemodes}
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  // TODO: Make it as a separate script file to save up bandwidth. Also refactor to be an Object.

  let a = document.querySelectorAll("input[name='requiredtags[]']");
  for (let b of a) {
    g_FilterTags["requiredtags"] = g_FilterTags["requiredtags"] || [];
    if (g_FilterTags["requiredtags"].includes(b.value))
      b.checked = true;
    b.onchange = (e) => {
      if (e.target.checked) {
        g_FilterTags["requiredtags"].push(e.target.value);
      } else {
        let j = g_FilterTags["requiredtags"].indexOf(e.target.value);
        if (j > -1)
          g_FilterTags["requiredtags"].splice(j, 1);
      }
      let c = "?" + Object.entries(g_FilterTags).map((v, i) => {
        if (typeof v[1] == "object") {
          return v[1].map((j) => {
            return v[0] + "[]=" + j
          }).join("&");
        } else {
          return v[0] + "=" + v[1];
        }
      }).join("&");

      document.location.href = document.location.pathname + c;
    };
  }
</script>
