<div class="showcase">
  <div class="showcase_title"><i class="mdi mdi-comment"></i> Comments</div>
  <div id="comments">
    [LOGIN]
    <div class="incontainer m-b-10" style="padding: 8px;">
      <div class="part-title"><i class="mdi mdi-comment"></i> Leave a comment</div>
      <form id="form_post_comment">
        <div class="flex">
          <div class="miniavatar noshrink p-r-5">
            <img src="{avatar}" alt="">
          </div>
          <div class="fullwidth">
            <textarea placeholder="Add a comment" type="text" class="input textarea_noresize textarea_autoresize" name="content"></textarea>
          </div>
        </div>
        <div class="flex">
          <div class="fullwidth"></div>
          <div class="noshrink" style="margin-top: 3px;">
            <span onclick="CComments_FormatingHelp()" ignore class="tf-button-2">Formatting Help</span>
            <input type="submit" value="Post Comment" class="tf-button">
          </div>
        </div>
      </form>
    </div>
    [/LOGIN]
    <div id="mount-comments" class="loading">

    </div>
    <div class="flex">
      <div class="fullwidth"></div>
      <div class="noshrink whitelink" style="color: #aaa">
        <div class="tf-button m-r-5" ignore onclick="Creators.Comments.prevPage()">◀</div>
        <select style="background: #000; color: #999;" id="comments_select_page">
        </select>
        <div class="tf-button m-l-5" ignore onclick="Creators.Comments.nextPage()">▶</div>
      </div>
    </div>
  </div>
</div>
<script src="{CDN}/assets/scripts/actions/comments{min}.js?v={Version}" charset="utf-8"></script>


<script type="text/javascript">
  Creators.Comments = new CTFComments({
    selector: "#comments",
    target: "{target}",
    count: {count},
    id: {id}
  });
  new CTFForm({
    selector: "#form_post_comment",
    url: "/api/IComments/GComments",
    recaptcha: true,
    recaptcha_action: "Comment",
    request: {
      data: {
        id: {id},
        target: "{target}"
      }
    },
    success: ()=>{Creators.Comments.loadPage(0); document.querySelector("#form_post_comment textarea[name=content]").value = "";},
  });
</script>
