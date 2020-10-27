<div class="flex miniprofile">
    <div class="avatar">
        <img src="{avatar}">
    </div>
    <div class="miniprofile-data">
        <a href="/profiles/{alias}"><h2>{name}</h2></a>
        [GUEST]<follow steamid="{steamid}" follow="{bFollowing}"></follow>[/GUEST]
    </div>
</div>
<div style="display: none" class="showcase_profile miniprofile flex" :class="{loading: loaded}">
        <div class="flex">
            <div class="avatar">
                <img src="{avatar}">
            </div>
            <div class="miniprofile-data">
                <h2>{name}</h2>
                <follow steamid="{steamid}" follow="{bFollowing}"></follow>
            </div>
        </div>
    </div>