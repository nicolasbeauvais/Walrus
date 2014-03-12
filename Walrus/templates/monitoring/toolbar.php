<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 23:56 24/02/14
 */

?>

<style>
html {
    /**
     * STYLE GIVEN FOR WALRUS TOOLBAR
     * DEV MODE ONLY
     */
    padding-bottom: 50px;
}

#WALRUS-toolbar {
    position: fixed;
    z-index: 99999999;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50px;
    background-color: #222222;
    font-family: 'helvetica', 'arial', sans-serif;
    border-top: 1px solid #111;
}
#WALRUS-toolbar > #WALRUS-logo:hover {
    cursor: pointer;
    background-color: #2a2a2a;
}
#WALRUS-toolbar #WALRUS-logo {
    width: 50px;
    height: 50px;
    overflow: hidden;
    float: left;
    display: inline-block;
    border-right: 1px solid #1a1a1a;
    -webkit-box-shadow: 1px 0 0 #2a2a2a;
    -moz-box-shadow: 1px 0 0 #2a2a2a;
    -o-box-shadow: 1px 0 0 #2a2a2a;
    -ms-box-shadow: 1px 0 0 #2a2a2a;
    box-shadow: 1px 0 0 #2a2a2a;
}
#WALRUS-toolbar #WALRUS-alert {
    height: 50px;
    width: 50px;
    display: inline-block;
    float: left;
    color: #eee;
    font-size: 20px;
    line-height: 50px;
    text-align: center;
    border-right: 1px solid #1a1a1a;
    -webkit-box-shadow: 1px 0 0 #2a2a2a;
    -moz-box-shadow: 1px 0 0 #2a2a2a;
    -o-box-shadow: 1px 0 0 #2a2a2a;
    -ms-box-shadow: 1px 0 0 #2a2a2a;
    box-shadow: 1px 0 0 #2a2a2a;
}
#WALRUS-toolbar #WALRUS-alert i {
    color: #ff5a5a;
    font-size: 30px;
    font-weight: bold;
    font-style: normal;
    text-align: center;
    line-height: 50px;
}
#WALRUS-toolbar #WALRUS-executionTime {
    color: #fff;
    display: inline-block;
    line-height: 50px;
    text-align: center;
    padding: 0 15px;
}
#WALRUS-toolbar #WALRUS-http-code {
    display: inline-block;
    padding: 15px;
    border-right: 1px solid #1a1a1a;
    -webkit-box-shadow: 1px 0 0 #2a2a2a;
    -moz-box-shadow: 1px 0 0 #2a2a2a;
    -o-box-shadow: 1px 0 0 #2a2a2a;
    -ms-box-shadow: 1px 0 0 #2a2a2a;
    box-shadow: 1px 0 0 #2a2a2a;
}
#WALRUS-toolbar #WALRUS-http-code span {
    padding: 2px 8px;
    color: #fff;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    -ms-border-radius: 10px;
    border-radius: 10px;
}
#WALRUS-toolbar #WALRUS-http-code span.green {
    background-color: #40bd4b;
}
#WALRUS-toolbar #WALRUS-http-code span.orange {
     background-color: #f49f0b;
}
#WALRUS-toolbar #WALRUS-http-code span.red {
    background-color: #ff4441;
}
</style>

<?php
if ($e2nb > 0) {
    require_once(ROOT_PATH . 'Walrus/templates/monitoring/e2.php');
}
?>

<!-- TOOLBAR -->
<div id="WALRUS-toolbar">
    <div id="WALRUS-logo">
        <a href="http://www.walrus-framework.com/doc/<?php echo WALRUS_VERSION; ?>"
           title="Walrus v<?php echo WALRUS_VERSION; ?> documentation">
        <img alt="" src="data:image/png;base64,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
        wHy7wEAxX+9esnqEs4AAAAASUVORK5CYII=" />
        </a>
    </div>

    <?php
    if ($e2nb > 0) {
        ?>
        <div id="WALRUS-alert">
            <i>!</i>
            <?php echo $e2nb; ?>
        </div>
    <?php
    }
    ?>

    <div id="WALRUS-http-code">
        <span class="<?php echo $http_code === 200 ? 'green' : ($http_code >= 500 ? 'red' : 'orange');?>">
            <?php echo $http_code; ?>
        </span>
    </div>

    <div id="WALRUS-executionTime">
        <?php echo $executionTime . 'ms'; ?>
    </div>
</div>
<!-- TOOLBAR -->

<?php if ($e2nb > 0): ?>
    </body>
    </html>
<?php endif; ?>
