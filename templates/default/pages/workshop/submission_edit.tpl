<script src="{CDN}/assets/scripts/pages/submissions/manager{min}.js?v={Version}" charset="utf-8"></script>
<div class='mount'>
    <h2>{title} <span class="qp-options-context">» Edit</span></h2>
    <div class="post-content">
      <a href="/submission/{id}">« Back to Submission page</a>
      <div class="showcase">
        <div class="showcase_title"><i class="mdi mdi-file"></i> Files Access</div>
        <div class="m-b-5">For us to be able to add content from the workshop to the game, we need the game files required for this submission. Since we cannot retrieve the files from the Steam Workshop, we ask you to help us by uploading files to some external file storage (for example, the most preferable one is <a target="_blank" href="https://drive.google.com/">Google Drive</a>) and provide us the link to these files in the field below. The contents of this field is not visible to the public, and will be available only to the authors of this submission and to the Creators.TF Staff.</div>
        <form class="flex" id="form_submission_files">
            <input class="input" name="access_link" value="{download_link}" required type="url">
            <input class="tf-button m-l-5" value="Save Changes" type="submit">
        </form>
      </div>
      <div class="showcase">
        <div class="showcase_title"><i class="mdi mdi-label"></i> Type</div>
        <div class="m-b-5">The “Type” field is one of the most important descriptors that the Creators.TF Submissions system provides. There are a lot of things that depend on this field internally, meaning it should be as accurate as possible. Please fill in this field with accurate information about your submission. Please note, that selected Tags (from the form below) will be reset up if you change the value of this field.</div>
        <form id="form_submission_type" class="flex">
          <select required class="input" name="type">
            <option disabled selected value=""> -- Select a Type --</option>
            {tags.option.types}
          </select>
          <input class="tf-button m-l-5" value="Save Changes" type="submit">
        </form>
      </div>
      <div class="showcase">
        <div class="showcase_title"><i class="mdi mdi-label"></i> Tags</div>
        <div class="m-b-5">Tags are used for better filtering, searching, and sorting. Please select all the categories that accurately describe the style and type of your submission. If you provide inaccurate tags about your submission on purpose, this submission may be suspended.</div>
        <form id="form_submission_tags">
          [TAGS_THEMES]
          <div class="p-t-5">
            <div class="graytext">Themes:</div>
            <div class="ws-info-container">
              {tags.filter.themes}
            </div>
          </div>
          [/TAGS_THEMES]
          [TAGS_GAMEMODES]
          <div class="p-t-5">
            <div class="graytext">Game Modes:</div>
            <div class="ws-info-container">
              {tags.filter.gamemodes}
            </div>
          </div>
          [/TAGS_GAMEMODES]
          [TAGS_CLASSES]
          <div class="p-t-5">
            <div class="graytext">Classes:</div>
            <div class="ws-info-container">
              {tags.filter.classes}
            </div>
          </div>
          [/TAGS_CLASSES]
        </div>
        <br/>
        <div class="flex">
          <div class="fullwidth"></div>
          <div class="noshrink">
            <input type="submit" class="tf-button" value="Save Changes">
          </div>
        </div>
      </form>
      <div class="showcase">
        <div class="showcase_title"><i class="mdi mdi-store"></i> Steam Workshop Connection</div>
        <div class="part incontainer" style="padding: 5px 10px 0;">
          <div class="part-title"><i class="mdi mdi-steam"></i> Refresh Submission from Steam Workshop</div>
          <div>
            <div class="embed-text" style="margin-bottom: 10px;">This section may help if you have updated your Steam Workshop submission after you've imported it on Creators.TF and want to update it here as well. By clicking the button bellow, you will update this submission's information to correspond to information that is presented on respective Steam Workshop submission. Please note that all custom tags (from the form above) will be reset up.</div>
            <div>
              <div class="tf-button" onclick="Submission.update()"><i class="mdi mdi-refresh"></i> Refresh</div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
<script type="text/javascript">
  Creators.Submission = new CTFSubmissionsManager({
    id: {id},
    workshop_id: "{workshop_id}"
  })
  new CTFForm({
    selector: "#form_submission_files",
    url: "/api/ISubmissions/GEditSubmission",
    request: {
      data: {
        id: {id}
      }
    },
    beforesuccess: ()=>{setTimeout(()=>{document.location.href = document.location.href}, 300)}
  });
  new CTFForm({
    selector: "#form_submission_type",
    url: "/api/ISubmissions/GEditSubmission",
    request: {
      data: {
        id: {id}
      }
    },
    beforesuccess: ()=>{setTimeout(()=>{document.location.href = document.location.href}, 300)}
  });
  new CTFForm({
    selector: "#form_submission_tags",
    url: "/api/ISubmissions/GEditSubmission",
    request: {
      data: {
        id: {id}
      }
    },
    beforesuccess: ()=>{setTimeout(()=>{document.location.href = document.location.href}, 300)}
  });
</script>
<link rel="stylesheet" href="{CDN}/assets/styles/workshop{min}.css?v={Version}">
