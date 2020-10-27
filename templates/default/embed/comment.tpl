[VISIBLE]
<div class="m-b-10">
  <div class="post_header">
    <div class="flex">
      <div class="avatar">
        <a href="/profiles/{alias}"><img data-miniprofile="{id}" src="{avatar}"></a>
      </div>
      <div class="fullwidth">
        <div class="flex">
          <div class="fullwidth whitelink">
            <span style="font-size: 18px;"><a href="/profiles/{alias}">{name}{special}</a> <span class="date-author graytext"> @ {date}</span></a></span>
          </div>
          <div class="noshrink whitelink">
            [CAN_FLAG]<a><i tooltip="Report" ignore onclick="Creators.Comments.hide({id})" class="mdi mdi-flag"></i></a>[/CAN_FLAG]
            [CAN_DELETE]<a><i ignore onclick="Creators.Comments.delete({id})" tooltip="Delete" class="mdi mdi-delete"></i></a>[/CAN_DELETE]
          </div>
        </div>
        <div class="post-content cut whitespace_preline">{content}</div>
      </div>
    </div>
  </div>
</div>
[/VISIBLE]
[BLACKLISTED]
<div class="m-b-10">
  <div class="post_header">
      <div class="fullwidth">
        <div class="flex">
          <div class="fullwidth whitelink">
            <span style="font-size: 18px;"><span class="date-author graytext">Unknown Author @ {date}</span></a></span>
          </div>
        </div>
      </div>
      <div class="post-content cut whitespace_preline whitelink">This comment has been reported and hidden from you. <a onclick="Creators.Comments.unhide({id})" href="javascript:void()">Undo.</a></div>
  </div>
</div>
[/BLACKLISTED]
