<div class='post'>
    <div class="post_header flex">
        <div class="flex">
            <div class="avatar">
                <a href="/profiles/{alias}"><img src="{avatar}"></a>
            </div>
            <div>
                <a href="/post/{alias}/{id}">
                    <h2>{title}</h2>
                </a>
                <span class='date-author'>[NOT_PUBLISHED]<b><span class="bancolor tooltip" tooltip="This post is not published yet.">Not Public!</span></b> {created_date}[/NOT_PUBLISHED][PUBLISHED]{date}[/PUBLISHED] • <a href="/profiles/{alias}">{author}</a> </span>
            </div>
        </div>
    </div>
    <div class="post-content cut">{story}</div>
    <div class="post-social">
      <div><a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="{title}" data-url="https://creators.tf/post/{alias}/{id}" data-hashtags="CreatorsTF,TF2" data-related="creatorstf" data-show-count="true">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script></div>
    </div>
    [EMBED]
    <div class="post-embed">
        <a href="{embed-url}">
            <div class="showcase_profile embed">
                <div class="flex">
                    <div class="avatar">
                        <img src="{embed-image}">
                    </div>
                    <div class="miniprofile-data">
                        <h2>{embed-title}</h2>
                        <span>{embed-content}</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    [/EMBED]
</div>
[COMMENTS]
{comments}
[/COMMENTS]
