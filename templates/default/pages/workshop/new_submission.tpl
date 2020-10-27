<div class='mount'>
  <h2>Add a new Submission</h2>
  <div class="post-content">
    <p>By submitting your item to the Creators.TF Workshop, you agree and understand that your submission might appear in one of our future event or content updates.</p>
    <p><b>Important Notice:</b><br/>If there are any other contributors on the submission you want to import, make sure they all are fine with this. Get a "yes" from every single one of them, and import this submission only if everyone agrees.</p>
    <div class="part incontainer" style="padding: 5px 10px 10px;">
      <div class="part-title"><i class="mdi mdi-steam"></i> Import from Steam Workshop</div>
      <div>
        <div class="embed-text"><a target="_blank" href="https://steamcommunity.com/profiles/{steamid}/myworkshopfiles?appid=440"><i class="mdi mdi-open-in-new"></i> Open your Steam Workshop</a></div>
        <form id="form_import_workshop">
          <div class="embed-text" style="margin-bottom: 10px;">You can import your Steam Workshop submission directly into Creators.TF by simply providing the link to it. Copy and paste the link, hit "Search" and then "Import". You must be listed as author on the submission's page to be able to import it into the Creators.TF Workshop.</div>
          <div class="flex">
            <input type="text" class="input" name="workshop_link" placeholder="Enter Steam Workshop link or ID. Example: https://steamcommunity.com/sharedfiles/filedetails/?id=1234567890">
            <input style="margin-left: 5px;" class="tf-button" type="submit" value="Search">
          </div>
        </form>
        <div class="loading" id="workshop_preview" style="margin-top: 25px; transition: max-height 1s ease; max-height: 0;">
          <div class="flex">
            <div class="midavatar noshrink" id="workshop_preview_container" style="padding-right: 10px;">
              <a id="workshop_preview_url" target="_blank"><img id="workshop_preview_image" style="border-radius: 10px"></a>
            </div>
            <div>
              <div class="embed-text" style="font-family: 'TF2 Build', sans-serif;" id="workshop_preview_name"></div>
              <div class="embed-text" id="workshop_preview_description"></div>
            </div>
          </div>
          <div class="flex" id="workshop_preview_import" style="margin-top: 10px;">
            <div style="width:100%; color: #888;" id="workshop_preview_import_message"></div>
            <div class="noshrink">
              <button disabled class="tf-button" id="workshop_preview_import_button"><i class="mdi mdi-import"></i> Import</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{CDN}/assets/scripts/pages/submissions/creator{min}.js?v={Version}" charset="utf-8"></script>
