<div id="wallpaper"></div>
<div id="form">
    <h3>Login</h3>

    <form action="./" method="POST">
        <input type="hidden" name="type" value="login"/>
        <input type="text" name="pseudo" placeholder="Pseudo" value=""/>
        <input type="password" name="password" placeholder="Password" value=""/>
        <input class="first" type="submit" value="Login"/>
    </form>

    <h3>Signup</h3>

    <form action="./" method="POST">
        <input type="hidden" name="type" value="signup"/>
        <input type="text" name="name" placeholder="Name" value=""/>
        <input type="text" name="pseudo" placeholder="Pseudo" value=""/>
        <input type="password" name="password" placeholder="Password" value=""/>
        <input type="submit" value="Signup"/>
    </form>
</div>