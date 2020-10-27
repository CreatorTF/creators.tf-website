<div class="video">
    <a href="https://youtu.be/{vid}">
        <div class="video-preview">
            <img alt="{vid}" src="https://i.ytimg.com/vi/{vid}/mqdefault.jpg">
            <div class="video-preview-avatar">
                <img src="{avatar}" alt="{name}">
            </div>
        </div>
    </a>
    <div class="video-info">
        <a href="https://youtu.be/{vid}"><h2>{title}</h2></a>
        <div class="post-content">
            <p>{Version}ideo_Author: <b><a href="/profiles/{alias}">{name}</a></b>
            </br>
            {Version}ideo_Uploaded: <b><time-ago long :datetime="{timestamp}"></time-ago></b></p>
            <p><small><a href="/report/video/{id}"><i class="mdi mdi-alert-circle-outline"></i> #Report_Error</a></small></p>
        </div>
    </div>
</div>