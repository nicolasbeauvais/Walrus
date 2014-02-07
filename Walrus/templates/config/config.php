<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Configuration | Walrus</title>
</head>
<body>

<h1>Create your project configuration</h1>

<style>
    html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn,
    em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu,
    nav, output, ruby, section, summary, time, mark, audio, video {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 100%;
        font: inherit;
        vertical-align: baseline; }

    article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {
        display: block; }

    body {
        line-height: 1; }

    ol, ul {
        list-style: none; }

    blockquote, q {
        quotes: none; }

    blockquote:before, blockquote:after, q:before,
    q:after {
        content: '';
        content: none; }

    table {
        border-collapse: collapse;
        border-spacing: 0; }

    textarea:focus, input:focus {
        outline: 0; }

    a {
        text-decoration: none; }

    a:link, a:visited, a:hover, a:active {
        color: #000; }
    body {
        height: 100%;
        width: 100%;
        background-color: #ddd;
        margin: 0;
    }
    h1 {
        font-family: Helvetica, Arial, sans-serif;
        color: #ffffff;
        text-align: center;
        background-color: #212a31;
        font-size: 22px;
        font-style: normal;
        font-weight: normal;
        letter-spacing: normal;
        line-height: 30px;
        padding: 15px 0;
        margin-bottom: 40px;
    }
    h2 {
        background-color: #212a31;
        margin: 0 -30px;
        text-align: center;
        font-family: Helvetica, Arial, sans-serif;
        color: #ffffff;
        font-size: 18px;
        font-style: normal;
        font-weight: normal;
        letter-spacing: normal;
        line-height: 18px;
        padding: 12px 0;
        margin-bottom: 20px;
    }
    form {
        width: 400px;
        margin: auto;
        background-color: #3e90ca;
        padding: 0 30px;
    }
    input {
        width: 100%;
    }
    input, select {
        border: none;
        padding: 5px 0;
        text-indent: 10px;
        line-height: 25px;
        color: #212a31;
        font-size: 18px;
        font-style: normal;
        font-weight: normal;
        letter-spacing: normal;
        background-color: #ddd;
    }
    ::-webkit-input-placeholder {
        color: #212a31;
    }
    :-moz-placeholder {
        color: #212a31;
    }
    ::-moz-placeholder {
        color: #212a31;
    }
    :-ms-input-placeholder {
        color: #212a31;
    }
    .coll, input {
        margin-bottom: 7px;
    }
    .coll span {
        float: left;
        line-height: 38px;
        vertical-align: middle;
        color: #ffffff;
        font-size: 18px;
        font-style: normal;
        font-weight: normal;
        letter-spacing: normal;
    }
    .coll select {
        width: 290px;
        float: right;
        display: inline-block;
        padding: 8px 0;
    }

    .last {
        margin-top: 20px;
    }
    .clear {
        clear: both;
    }
    .submit {
        background-color: #212a31;
        color: #ffffff;
        font-size: 18px;
        font-style: normal;
        font-weight: normal;
        letter-spacing: normal;
        margin: 30px -30px 0 -30px;
        width: 460px;
        cursor: pointer;
    }
    #infos {
        background-color: #58e34b;
        font-family: Helvetica, Arial, sans-serif;
        color: #ffffff;
        text-align: center;
        font-size: 22px;
        font-style: normal;
        font-weight: normal;
        letter-spacing: normal;
        line-height: 30px;
        padding: 15px 0;
        margin-top: -40px;
        margin-bottom: 40px;
    }
    .error {
        background-color: #dd3b35;
    }
    #more {
        text-align: center;
        width: 500px;
        font-family: Helvetica, Arial, sans-serif;
        color: #212a31;
        text-align: center;
        font-size: 22px;
        font-style: normal;
        font-weight: normal;
        margin: auto;
    }
    #more h3 {
        font-weight: bold;
        color: #dd3b35;
        line-height: 35px;
        font-size: 28px;
    }
    #more p {
        margin-top: 10px;
        text-align: left;
        font-size: 17px;
        font-style: italic;
    }
    #more span {
        color: #000;
        font-style: italic;
    }
</style>

<?php
if (isset($validation)) {
    if ($validation) {
?>
        <div id="infos">
            Walrus as been configured.
        </div>
        <div id="more">
            <h3>Important!</h3><br/>
            You must delete those files for safety reasons: <br/>
            <p>
                Walrus/controllers/ConfigController.php
                Walrus/templates/config/config.php
            </p>
            <br/>
            And remove the <span>_config</span> route from:
            <p>
                config/routes.yml
            </p>
        </div>
<?php
        die;
    } else {
?>
        <div id="infos" class="error">
            Please fill all inputs correctly
        </div>
<?php
    }
}
?>
    <form action="./" method="POST">
        <input type="hidden" name="config" value="ok"/>
        <h2>Database:</h2>
        <div class="coll">
            <span>RDBMS</span>
            <select name="RDBMS">
                <option value="mysql" selected>MySQL</option>
                <option value="sqlite">SQLite</option>
                <option value="postgresql">PostgreSQL</option>
                <option value="oracle">Oracle</option>
            </select>
            <div class="clear"></div>
        </div>
        <input type="text" name="database" placeholder="Database">
        <input type="text" name="host" placeholder="Hostname">
        <input type="text" name="name" placeholder="user">
        <input type="password" name="password" placeholder="password">

        <h2 class="last">Walrus:</h2>
        <div class="coll">
            <span>Templating</span>
            <select name="templating">
                <option value="haml" selected>HAML</option>
                <option value="smarty">Smarty</option>
                <option value="php">PHP</option>
            </select>
            <div class="clear"></div>
        </div>
        <div class="coll">
            <span>Environment</span>
            <select name="environment">
                <option value="dev" selected>Development</option>
                <option value="production">Production</option>
            </select>
            <div class="clear"></div>
        </div>

        <input type="submit" class="submit" value="Send"/>
    </form>
</body>
</html>