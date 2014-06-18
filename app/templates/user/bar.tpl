<div id="userBar">
    <div class="grid">
        <div class="user">
            <img src="./assets/images/avatar.png" class="avatar">
            <span>
                <span class="profil">
                    <span class="h3">@{$currentUser}</span>
                    <br/>
                </span>
            </span>
        </div>
        <div class="infos">
            <div class="stat">
                <span class="value">{$stats}</span>
                <br/>
                <span class="info">Messages</span>
            </div>
            <div class="stat">
                <span class="value">0</span>
                <br/>
                <span class="info">Following</span>
            </div>
            <div class="stat">
                <span class="value">0</span>
                <br/>
                <span class="info">Followers</span>
            </div>
        </div>
        <div id="post"></div>
    </div>
</div>

<div id="post-pop">
    <form action="./" method="POST">
        <input type="hidden" name="post" value="ok"/>
        <textarea name="message" cols="30" rows="10"></textarea>
        <input type="submit" value="Send"/>
    </form>
    <div class="clear"></div>
</div>