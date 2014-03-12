<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walrus | Configuration</title>

    <style>
    html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym,
    address, big, cite, code, del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u,
    i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td,
    article, aside, canvas, details, embed, figure, figcaption, footer, header, hgroup, menu, nav, output, ruby,
    section, summary, time, mark, audio, video {
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
        font-family: 'helvetica', 'arial', sans-serif;
        background-color: #252831;
    }
    #header {
        background-color: #222222;
        color: #ddd;
        height: 70px;
    }
    #header img {
        padding: 10px;
        float: left;
    }
    #header h1 {
        float: left;
        font-size: 30px;
        line-height: 70px;
        margin-right: 10px;
    }
    #header .version {
        float: left;
        line-height: 80px;
    }
    .container {
        width: 600px;
        margin: auto;
    }
    form {
        color: #ddd;
    }
    label {
        display: block;
        padding: 20px 0;
    }
    .case {
        background-color: #363b41;
        color: #B1BAC3;
        display: inline-block;
        padding: 15px 20px;
        margin-right: 15px;
        -webkit-border-radius: 1px;
        -moz-border-radius: 1px;
        border-radius: 1px;
        cursor: pointer;
        -webkit-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
        -moz-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
    }
    .case:hover {
        background: #454c54;
        color: #72767b;
    }
    .case._selected {
        background-color: #4C9743;
        color: #ddd;
    }
    .case_container .case:last-child {
        margin-right: 0;
    }
    .input {
        overflow: hidden;
        -webkit-border-radius: 1px;
        -moz-border-radius: 1px;
        border-radius: 1px;
        background-color: #3b4148;
        position: relative;
        margin: 20px 0;
    }
    .input._url {
        margin-top: 0;
    }
    .input._check {
        -webkit-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
        -moz-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
    }
    .input label {
        position: absolute;
        left: 0;
        padding: 0 10px;
        width: 120px;
        line-height: 40px;
        background-color: #363b41;
        color: #B1BAC3;
    }
    .input input {
        display: inline-block;
        height: 30px;
        width: 580px;
        padding: 5px 10px;
        border: none;
        float: right;
        background-color: #3b4148;
        text-indent: 140px;
        color: #ddd;
        font-weight: bold;
    }
    .input._check label {
        line-height: 46px;
        display: none;
    }
    label.title {
        font-size: 25px;
    }
    label.success {
        background-color: #4C9743;
        color: #eee;
    }
    label.fail {
        background-color: #ff5a5a;
        color: #eee;
    }
    .input:hover input {
        background-color: #454c54;
    }
    input[type="submit"] {
        margin-top: 40px;
        padding: 15px 20px;
    }
    input[type="button"] {
        padding: 5px 10px;
        height: 46px;
        float: right;
        text-indent: 0;
    }
    input[type="submit"], input[type="button"] {
        border: none;
        color: #eee;
        text-transform: uppercase;
        font-weight: bold;
        cursor: pointer;
        background-color: #363b41;
        display: block;
        text-align: center;
        width: 100%;
        -webkit-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
        -moz-box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
        box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.6);
    }
    input[type="submit"]:hover, input[type="button"]:hover {
        background-color: #454c54;
    }
    #fail {
        background-color: #ff5a5a;
        color: #fff;
        padding: 10px 15px;
        margin-top: 20px;
        -webkit-border-radius: 1px;
        -moz-border-radius: 1px;
        border-radius: 1px;
    }
    #infos {
        color: #fff;
        padding: 15px;
        margin-top: 40px;
        -webkit-border-radius: 1px;
        -moz-border-radius: 1px;
        border-radius: 1px;
        font-size: 20px;
        text-align: center;
    }
    #more {
        padding-top: 40px;
        color: #ddd;
    }
    #more h3 {
        text-align: center;
        color: #ff5a5a;
        font-size: 25px;
        margin-bottom: 40px;
    }
    #more p {
        font-style: italic;
        margin: 15px;
        line-height: 20px;
        color: #ff5a5a;
    }
    #more .button {
        text-align: center;
    }
    #more .button a {
        text-align: center;
        color: #ddd;
        background-color: #4C9743;
        display: inline-block;
        margin-top: 40px;
        padding: 15px 40px;
    }
    #database._loading {
        -webkit-filter: blur(3px);
        -moz-filter: blur(3px);
        -o-filter: blur(3px);
        -ms-filter: blur(3px);
        filter: blur(3px);
    }
    .left {
        float: left;
        width: 290px;
        display: inline-block;
    }
    .right {
        float: right;
        width: 275px;
        display: inline-block;
    }
    </style>
</head>
<body>
<header id="header">
    <img alt="" src="data:image/png;base64,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
        wHy7wEAxX+9esnqEs4AAAAASUVORK5CYII=" />
    <h1>Walrus Framework</h1>
    <p class="version"><?php echo WALRUS_VERSION; ?></p>
    <div style="clear:both"></div>
</header>

<div class="container">

    <?php
    if (isset($validation)):
    if ($validation):
    ?>
        <div id="infos">
            Walrus as been configured.
        </div>
        <div id="more">
            <h3>Important!</h3><br/>
            You must delete those files for safety reasons: <br/>
            <p>
                Walrus/controllers/ConfigController.php<br/>
                Walrus/templates/config/config.php
            </p>
            <br/>
            And remove the <span>_config</span> route from:
            <p>
                config/routes.yml
            </p>
            <div class="button">
                <a href="./">Hello Walrus</a>
            </div>
        </div>
    <?php
        die;
    else:
    ?>
        <div id="fail" class="error">
            Please fill all inputs correctly
        </div>
    <?php
    endif;
    endif;
    ?>

    <form action="./" method="post">
        <input type="hidden" name="config" value="true"/>

        <label for="database" class="title">Database:</label>

        <div id="database">

            <div id="rdbms" class="case_container">
                <input type="hidden" name="RDBMS" value="<?php echo isset($post['RDBMS']) ? $post['RDBMS'] : 'mysql'; ?>"/>
                <div class="case <?php if((isset($post['RDBMS']) && $post['RDBMS'] == 'mysql')
                    || !isset($post['RDBMS'])) { echo '_selected';} ?>">
                    <i></i> MySql
                </div>
                <div class="case <?php if(isset($post['RDBMS']) && $post['RDBMS'] == 'sqllite') { echo '_selected';} ?>">
                    <i></i>SQLite
                </div>
                <div class="case <?php if(isset($post['RDBMS']) && $post['RDBMS'] == 'postgresql') { echo '_selected';} ?>">
                    <i></i>PostgreSQL
                </div>
                <div class="case <?php if(isset($post['RDBMS']) && $post['RDBMS'] == 'cubrid') { echo '_selected';} ?>">
                    <i></i>CUBRID
                </div>
            </div>

            <div class="input">
                <label for="hostname">Hostname</label>
                <input type="text" id="hostname" name="hostname" value="<?php echo $post['hostname']; ?>"/>
            </div>
            <div class="input">
                <label for="databasename">Database name</label>
                <input type="text" id="databasename" name="databasename" value="<?php echo $post['databasename']; ?>"/>
            </div>
            <div class="input">
                <label for="user">User</label>
                <input type="text" id="user" name="user" value="<?php echo $post['user']; ?>"/>
            </div>
            <div class="input">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="<?php echo $post['password']; ?>"/>
            </div>

            <div class="input _check">
                <label for="database-test" class="success">Succes</label>
                <label for="database-test" class="fail" <?php ?>>Failed</label>
                <input type="button" id="database-test" value="Check database" />
            </div>
        </div>

        <label for="url" class="title">Base Url:</label>
        <div class="input _url">
            <label for="url">Base url</label>
            <input type="text" id="url" name="url" value="<?php echo isset($post['url']) ?
                $post['url'] : "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>"/>
        </div>

        <div class="left">
            <label for="templating" class="title">Templating language:</label>
            <div id="templating" class="case_container">
                <input type="hidden" name="templating" value="<?php echo isset($post['templating']) ? $post['templating'] : 'php'; ?>"/>
                <div class="case <?php if((isset($post['templating']) && $post['templating'] == 'php')
                    || !isset($post['templating'])) { echo '_selected';} ?>">
                    <i></i> PHP
                </div>
                <div class="case <?php if(isset($post['templating'])
                    && $post['templating'] == 'smarty') { echo '_selected';} ?>">
                    <i></i> Smarty
                </div>
                <div class="case <?php if(isset($post['templating'])
                    && $post['templating'] == 'haml') { echo '_selected';} ?>">
                    <i></i> HAML
                </div>
            </div>
        </div>
        <div class="right">
            <label for="env" class="title">Environment mode:</label>
            <div id="env" class="case_container">
                <input type="hidden" name="environment"
                       value="<?php echo isset($post['environment']) ? $post['environment'] : 'development'; ?>"/>
                <div class="case  <?php if((isset($post['environment']) && $post['environment'] == 'development')
                    || !isset($post['environment'])) { echo '_selected';} ?>">
                    <i></i> Development
                </div>
                <div class="case <?php if(isset($post['environment'])
                    && $post['environment'] == 'production') { echo '_selected';} ?>">
                    <i></i> Production
                </div>
            </div>
        </div>

        <div style="clear:both"></div>

        <input type="submit" value="Send"/>
    </form>
</div>

<script>
    <?php require_once(ROOT_PATH . 'Walrus/templates/scripts/jquery.js') ?>

    $('.case_container .case').click(function () {
        var $parent = $(this).parent();
        $parent.find('.case').removeClass('_selected');
        $parent.find('input:first').val($(this).text().toLowerCase().replace(/\s+/g, ''));
        $(this).addClass('_selected');
    });

    $('#database-test').click(function () {
        $('#database').addClass('_loading');
        $('#database').find('.input._check label').hide();
        var data = {
            check: true,
            RDBMS: $('#rdbms').find('input:first').val(),
            hostname: $('#database').find('#hostname').val(),
            databasename: $('#database').find('#databasename').val(),
            user: $('#database').find('#user').val(),
            password: $('#database').find('#password').val()
        }
        $.post('./', data, 'json').done(function (data) {
            if (data.indexOf('<') === -1) {
                data = JSON.parse(data);
                if (data && data.success && data.success === true) {
                $('#database').find('.input._check label.success').show();
                } else {
                    $('#database').find('.input._check label.fail').show();
                }
            } else {
                $('#database').find('.input._check label.fail').show();
            }
            }).fail(function () {
            $('#database').find('.input._check label.fail').show();
        }).always(function () {
            $('#database').removeClass('_loading');
        });
    });
</script>
</body>
</html>