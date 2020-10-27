<script>
  const g_TagsTypes = {tags.json.types};
  const g_TagsThemes = {tags.json.themes};
  const g_TagsGameModes = {tags.json.gamemodes};
  const g_TagsClasses = {tags.json.classes};
</script>

<script src="{CDN}/assets/scripts/pages/submissions/manager{min}.js?v={Version}" charset="utf-8"></script>
<div class='mount ws'>
  <h2 style="margin-bottom: 10px;">{title}</h2>
  <div class="ws-header container">
    <div class="flex ws-block">
      <div class="ws-content">
        <div class="ws-image-slider">
          <div image-carousel>
            {images}
          </div>
        </div>
      </div>
      <div class="ws-side">
        <div class="ws-meta">
          <div class="ws-thumb">
            <img onclick="CShowFullImage('{thumb}')" class="cursor-pointer" ignore src="{thumb}" alt="{title}">
          </div>
          <div class="ws-tags whitelink">
            {tags}
          </div>
        </div>
      </div>
    </div>
    <div class="flex ws-block">
      <div class="ws-content">
        [VOTE_ENABLED]
        <span class="graytext">Would you like to see this on Creators.TF?</span>
        <div>
          [NOT_RATED_POS]<div class="tf-button" onclick="Submission.upvote()"><i class="mdi mdi-thumb-up"></i> Yes</div>[/NOT_RATED_POS]
          [RATED_POS]<div class="tf-button tooltip__nopointchildren" disabled tooltip="You have already voted for this submission."><i class="mdi mdi-thumb-up"></i> Yes</div>[/RATED_POS]
          [NOT_RATED_NEG]<div class="tf-button" onclick="Submission.downvote()"><i class="mdi mdi-thumb-down"></i></div>[/NOT_RATED_NEG]
          [RATED_NEG]<div class="tf-button tooltip__nopointchildren" disabled tooltip="You have already voted for this submission."><i class="mdi mdi-thumb-down"></i></div>[/RATED_NEG]
        </div>
        [/VOTE_ENABLED]
        [VOTE_DISABLED]
        <span class="graytext">Would you like to see this in Team Fortress 2?</span>
        <div>
          <a target="_blank" href="https://steamcommunity.com/sharedfiles/filedetails/?id={workshop_id}">
            <div class="tf-button"><i class="mdi mdi-steam"></i> Vote on Steam Workshop</div>
          </a>
        </div>
        [/VOTE_DISABLED]
      </div>
      <div class="ws-side">
        <div class="ws-steam">
          <a target="_blank" href="https://steamcommunity.com/sharedfiles/filedetails/?id={workshop_id}"><img src="{CDN}/assets/images/sws_button.png" alt=""></a>
        </div>
      </div>
    </div>
  </div>
  <div class="flex ws-block">
    <div class="ws-content">
      <div style="margin: 10px 0 0;">
        [CAN_MANAGE]
        <div class="ws-download flex">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-download" style="color: #999;"></i>
          </div>
          <div style="padding-top: 12px;">
            <span class="graytext">Download link. This is only visible to authors and Creators.TF staff members.</span>
            <div class="graytext"><a target="_blank" href="{download_link}">Open Link <i class="mdi mdi-open-in-new"></i></a></div>
          </div>
        </div>
        [/CAN_MANAGE]
        <div class="post-content cut m-b-10">
          {description}
        </div>
        <br/>
        <div>
          {comments}
        </div>
      </div>
    </div>
    <div class="ws-side" style="padding-top: 10px;">
      <div class="ws-info">
        <span class="graytext">Submission status:</span>
        [STATUS_MODERATION]
        <div class="ws-statusbar flex" style="background: #8f321b;">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-lock-clock"></i>
          </div>
          <div>
            <div class="ws-sbar-label">On Moderation</div>
            <div class="ws-sbar-help">This submission will be verified by website administration.</div>
          </div>
        </div>
        [/STATUS_MODERATION]
        [STATUS_PENDING]
        <div class="ws-statusbar flex" style="background: #305080;">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-refresh"></i>
          </div>
          <div>
            <div class="ws-sbar-label">Pending</div>
            <div class="ws-sbar-help">This submission is now in the process of voting.</div>
          </div>
        </div>
        [/STATUS_PENDING]
        [STATUS_COMPATIBLE]
        <div class="ws-statusbar flex" style="background: #706b2a;">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-star"></i>
          </div>
          <div>
            <div class="ws-sbar-label">Compatible</div>
            <div class="ws-sbar-help">This submission has been approved by website administration to be compatible with Team Fortress 2.</div>
          </div>
        </div>
        [/STATUS_COMPATIBLE]
        [STATUS_INTRODUCED]
        <div class="ws-statusbar flex" style="background: #40702a;">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-check"></i>
          </div>
          <div>
            <div class="ws-sbar-label">Introduced</div>
            <div class="ws-sbar-help">This submission has been introduced into Creators.TF's servers.</div>
          </div>
        </div>
        [/STATUS_INTRODUCED]
        [STATUS_ADDED]
        <div class="ws-statusbar flex" style="background: #ffdd57; color: #000">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-party-popper"></i>
          </div>
          <div>
            <div class="ws-sbar-label">Officially Added</div>
            <div class="ws-sbar-help">This submission has been officially added into the game by the TF Team.</div>
          </div>
        </div>
        [/STATUS_ADDED]
        [STATUS_INCOMPATIBLE]
        <div class="ws-statusbar flex" style="background: #141414">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-cancel"></i>
          </div>
          <div>
            <div class="ws-sbar-label">Incompatible</div>
            <div class="ws-sbar-help">This submission has been marked as incompatible by the website administration.</div>
          </div>
        </div>
        [/STATUS_INCOMPATIBLE]
        [UPDATE_NEEDED]
        <div class="ws-statusbar flex" style="background: #34312B;">
          <div class="ws-sbar-icon">
            <i class="mdi mdi-upload"></i>
          </div>
          <div>
            <span class="ws-sbar-label">Update Needed</span><br>
            <span class="ws-sbar-help">This submission has been updated. The corresponding files on Creators.TF will be updated in the next update.</span>
          </div>
        </div>
        [/UPDATE_NEEDED]
        <span class="graytext">Authors:</span>
        <div style="margin: 5px 0">
          {authors}
          [UNKNOWN_AUTHORS]
          <div class="incontainer">
            <div class="embed-text">+ {unknown_authors_count} unknown authors. <small class="tooltip" tooltip="We're not able to display users information unless they've logged in on Creators.TF at least once." style="opacity: .5;">?</small></div>
          </div>
          [/UNKNOWN_AUTHORS]
        </div>
        [CAN_MANAGE]
        <span class="graytext">Management:</span>
        <div class="ws-info-container">
          <div class="incontainer whitelink ws-link-menu">
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-update"></i></div>
              <div><a onclick="Submission.update()">Reimport Submission</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-pencil"></i></div>
              <div><a href="/submission/{id}/edit">Edit information</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-delete"></i></div>
              <div><a onclick="Submission.delete()">Delete</a></div>
            </div>
          </div>
        </div>
        <span class="graytext">Set Update State:</span>
        <div class="ws-info-container">
          <div class="incontainer whitelink ws-link-menu">
            <div class="flex">
              <i class="mdi mdi-upload" style="margin-right: 3px;"></i>
              <span><a onclick="Submission.setUpdateState(1)">Update Needed</a></span>
            </div>
            <div class="flex">
              <i class="mdi mdi-check" style="margin-right: 3px;"></i>
              <span><a onclick="Submission.setUpdateState(0)">Updated</a></span>
            </div>
          </div>
        </div>
        [/CAN_MANAGE]
      </div>

      <div class="ws-info">
        <span class="graytext">Unique visitors: <b>{unique_visitors}</b></span>
        <div class="graytext">Stars: <span class="tooltip__nopointchildren" style="cursor: pointer;" tooltip="{rating.stars.total}/5 stars, based on {rating.votes.total} votes.">
          [STARS_0]<i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_0]
          [STARS_1]<i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_1]
          [STARS_2]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_2]
          [STARS_3]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i><i class="mdi mdi-star-outline"></i>[/STARS_3]
          [STARS_4]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star-outline"></i>[/STARS_4]
          [STARS_5]<i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i><i class="mdi mdi-star"></i>[/STARS_5]
        </div>
        <span class="graytext">User Rating: <b>{rating.votes.balance}</b> <small>({rating.votes.percentage}% positive)</small></span>
      </div>
      [CAN_MOD]
      <div class="ws-info" style="background-color: #501717;">
        <span style="color: #F1F1F1">Set Status:</span>
        <div class="ws-info-container">
          <div class="incontainer whitelink ws-link-menu" style="background-color: #270D0D;">
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-lock-clock"></i></div>
              <div><a onclick="Submission.setStatus(0)">On Moderation</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-refresh"></i></div>
              <div><a onclick="Submission.setStatus(1)">Pending</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-cancel"></i></div>
              <div><a onclick="Submission.setStatus(5)">Incompatible</a></div>
            </div>
          </div>
        </div>
      </div>
      [/CAN_MOD]
      [CAN_ADMIN]
      <div class="ws-info" >
        <span style="color: #F1F1F1">Set Status:</span>
        <div class="ws-info-container">
          <div class="incontainer whitelink ws-link-menu" style="background-color: #270D0D;">
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-lock-clock"></i></div>
              <div><a onclick="Submission.setStatus(0)">On Moderation</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-refresh"></i></div>
              <div><a onclick="Submission.setStatus(1)">Pending</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-star"></i></div>
              <div><a onclick="Submission.setStatus(2)">Compatible</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-check"></i></div>
              <div><a onclick="Submission.setStatus(3)">Introduced</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-party-popper"></i></div>
              <div><a onclick="Submission.setStatus(4)">Officially Added</a></div>
            </div>
            <div class="flex">
              <div style="margin-right: 3px;"><i class="mdi mdi-cancel"></i></div>
              <div><a onclick="Submission.setStatus(5)">Incompatible</a></div>
            </div>
          </div>
        </div>
      </div>
      [/CAN_ADMIN]
    </div>
  </div>
</div>
<link rel="stylesheet" href="{CDN}/assets/styles/workshop{min}.css?v={Version}">
<script type="text/javascript">
  Creators.Submission = new CTFSubmissionsManager({
    id: {id},
    rating: {rating.votes.balance},
    percentage: {rating.votes.percentage},
    workshop_id: "{workshop_id}"
  });

  [SHOULD_HINT]
  Creators.Submission.OpenHintModal();
  [/SHOULD_HINT]
</script>
