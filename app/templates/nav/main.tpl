<nav id="top">
    <div class="grid">
        <h1>Walrus demo</h1>
        {if isset($_SESSION.id)}
            <ul id="menu">
                <li class="actual"><a href="./">Home</a></li>
                <li><a href="./me">Me</a></li>
            </ul>
        {/if}
    </div>
</nav>