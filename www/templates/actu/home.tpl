<div id="container" class="grid">
    <div id="content">
        <h2>Latest Messages</h2>

        <div id="messageContainer" data-poll="posts">
            {foreach $posts as $post}
                <div class="msg">
                    <img src="./assets/images/avatar.png" class="avatar">
                    <div class="msgContent">
                        <span class="name">{$post.name}</span>
                        <span class="pseudo">@{$post.pseudo}</span>
                        <br/>
                        <p class="message">
                            {$post.message}
                        </p>
                        <br/>
                        <span class="time">some time ago</span>
                    </div>
                    <div class="clear"></div>
                </div>
            {/foreach}
        </div>
    </div>
    <div id="sidebar" data-lazyload="sidebar/home"></div>
    <div class="clear"></div>
</div>