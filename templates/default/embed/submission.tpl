<a href="/submission/{id}">
  <div class="flex part incontainer">
    <div class="midavatar noshrink">
      <img src="{thumb}">
    </div>
    <div style="padding-left: 6px;" class="fullwidth">
      <div class="embed-text" style="font-family: 'TF2 Build', sans-serif;">{name}</div>
      <div class="embed-text">
        <span class="tooltip__nopointchildren" style="cursor: pointer;" tooltip="{rating.stars.total}/5 stars, based on {rating.votes.total} votes.">
          [STARS_0]<i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_0]
          [STARS_1]<i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_1]
          [STARS_2]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_2]
          [STARS_3]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_3]
          [STARS_4]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i>[/STARS_4]
          [STARS_5]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i>[/STARS_5]
        </span>
      </div>
      <div class="embed-text">{status}</div>
    </div>
    <div class="noshrink" style="padding-left: 10px;width: 40px;">
      [STATUS_MODERATION]
      <div class="ws-statusbar ws-statusbar-small flex" style="background: #8f321b;">
        <div class="ws-sbar-icon">
          <i class="mdi mdi-lock-clock" tooltip="On Moderation"></i>
        </div>
      </div>
      [/STATUS_MODERATION]
      [STATUS_PENDING]
      <div class="ws-statusbar ws-statusbar-small flex" style="background: #305080;">
        <div class="ws-sbar-icon">
          <i class="mdi mdi-refresh" tooltip="Pending"></i>
        </div>
      </div>
      [/STATUS_PENDING]
      [STATUS_COMPATIBLE]
      <div class="ws-statusbar ws-statusbar-small flex" style="background: #706b2a;">
        <div class="ws-sbar-icon">
          <i class="mdi mdi-star" tooltip="Compatible"></i>
        </div>
      </div>
      [/STATUS_COMPATIBLE]
      [STATUS_INTRODUCED]
      <div class="ws-statusbar ws-statusbar-small flex" style="background: #40702a;">
        <div class="ws-sbar-icon">
          <i class="mdi mdi-check" tooltip="Introduced on Creators.TF"></i>
        </div>
      </div>
      [/STATUS_INTRODUCED]
      [STATUS_ADDED]
      <div class="ws-statusbar ws-statusbar-small flex" style="background: #ffdd57; color: #000">
        <div class="ws-sbar-icon">
          <i class="mdi mdi-party-popper" tooltip="Officially Added"></i>
        </div>
      </div>
      [/STATUS_ADDED]
      [STATUS_INCOMPATIBLE]
      <div class="ws-statusbar ws-statusbar-small flex" style="background: #141414">
        <div class="ws-sbar-icon">
          <i class="mdi mdi-cancel" tooltip="Incompatible"></i>
        </div>
      </div>
      [/STATUS_INCOMPATIBLE]
    </div>
  </div>
</a>
