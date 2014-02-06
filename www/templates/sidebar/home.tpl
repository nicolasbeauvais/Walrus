<h2>People to follow</h2>

<div id="userToFollow">

    {foreach $users as $user}
        <div class="user">
            <img src="./assets/images/avatar.png" class="avatar">
            <div class="infos">
                <span class="name">{$user->name}</span>
                <span class="pseudo">@{$user->pseudo}</span>
            </div>
        </div>
    {/foreach}
</div>

<div id="copyright">
    <span>&copy; Walurs framework.</span><br/>
    This website is a demo for the Walrus PHP framework
</div>
