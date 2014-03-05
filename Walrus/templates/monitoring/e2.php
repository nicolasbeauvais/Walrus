<?php

/**
 * Walrus Framework
 * File maintened by: Nicolas Beauvais
 * Created: 23:10 26/02/14
 */

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>Walrus Debug</title>

    <style>
        walrus {
            display: block;
        }
        #WALRUS-e2 {
            position: fixed;
            z-index: 9999999;
            top: 0;
            left: 0;
            bottom: 50px;
            right: 0;
            background-color: #2a2a2a;
            color: #fff;
            min-width: 850px;
            font-family: 'Helvetica','arial',sans-serif;
        }
        #WALRUS-e2 #WALRUS-column {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 30%;
            background-color: #2a2a2a;
            overflow-y: auto;
        }
        #WALRUS-e2 .Walrus-column-multiple {
            display: none;
        }
        #WALRUS-e2 .Walrus-column-multiple._active {
            display: block;
        }
        #WALRUS-e2 #WALRUS-multiple-exception {
            border-bottom: 1px solid #111111;
        }
        #WALRUS-e2 #WALRUS-multiple-exception {
            font-weight: bold;
        }
        #WALRUS-e2 #WALRUS-multiple-exception span {
            background-color: #ff4441;
            border-right: 2px solid #2A2A2A;
            padding: 10px 14px;
            display: inline-block;
            float: left;
            cursor: pointer;
        }
        #WALRUS-e2 .WALRUS-trace {
            background-color: #202020;
            padding: 0 10px 0 15px;
            color: #ddd;
            word-wrap: break-word;
            cursor: pointer;
        }
        #WALRUS-e2 .WALRUS-trace p:first-child {
            padding-top: 20px;
            font-weight: bold;
            color: #5BCAF5;
            margin-bottom: 5px;
            margin-top: 0;
        }
        #WALRUS-e2 .WALRUS-trace p:first-child span {
            color:#fff;
        }
        #WALRUS-e2 .WALRUS-trace p:last-child {
            padding-bottom: 20px;
            margin-top: 0;
            margin-bottom: 0;
        }
        #WALRUS-e2 .WALRUS-trace p:last-child span {
            color: #5BCAF5;
            font-weight: bold;
        }
        #WALRUS-e2 .WALRUS-trace:hover, #WALRUS-e2 .WALRUS-trace._active {
            background-color: #151515;
        }
        #WALRUS-e2 .WALRUS-title {
            background-color: #222;
            border-left: 5px solid #5BCAF5;
            padding: 15px 0;
        }
        #WALRUS-e2 .WALRUS-title h1 {
            text-indent: 20px;
            color: #5BCAF5;
            margin-bottom: 0px;
            margin-top:0;
        }
        #WALRUS-e2 .WALRUS-title p {
            text-indent: 20px;
            font-size: 20px;
            margin-top: 10px;
        }
        #WALRUS-e2 .WALRUS-info {
            display: none;
            position: absolute;
            top: 0;
            left: 30%;
            right: 0;
            bottom: 0;
            background-color: #333333;
            overflow-y: auto;
        }
        #WALRUS-e2 .WALRUS-info._active {
            display: block;
        }
        #WALRUS-e2 .WALRUS-code {
            margin: 0 15px;
        }
        #WALRUS-e2 .WALRUS-code {
            display: none;
        }
        #WALRUS-e2 .WALRUS-code._active {
            display: block;
        }
        .syntaxhighlighter a,
        .syntaxhighlighter div,
        .syntaxhighlighter code,
        .syntaxhighlighter table,
        .syntaxhighlighter table td,
        .syntaxhighlighter table tr,
        .syntaxhighlighter table tbody,
        .syntaxhighlighter table thead,
        .syntaxhighlighter table caption,
        .syntaxhighlighter textarea {
            -moz-border-radius: 0 0 0 0 !important;
            -webkit-border-radius: 0 0 0 0 !important;
            background: none !important;
            border: 0 !important;
            bottom: auto !important;
            float: none !important;
            height: auto !important;
            left: auto !important;
            line-height: 1.1em !important;
            margin: 0 !important;
            outline: 0 !important;
            overflow: visible !important;
            padding: 0 !important;
            position: static !important;
            right: auto !important;
            text-align: left !important;
            top: auto !important;
            vertical-align: baseline !important;
            width: auto !important;
            box-sizing: content-box !important;
            font-family: "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace !important;
            font-weight: normal !important;
            font-style: normal !important;
            font-size: 1em !important;
            min-height: inherit !important;
            min-height: auto !important;
        }

        .syntaxhighlighter {
            width: 100% !important;
            margin: 1em 0 1em 0 !important;
            position: relative !important;
            overflow: auto !important;
            font-size: 1em !important;
            padding-bottom: 1px;
        }
        .syntaxhighlighter.source {
            overflow: hidden !important;
        }
        .syntaxhighlighter .bold {
            font-weight: bold !important;
        }
        .syntaxhighlighter .italic {
            font-style: italic !important;
        }
        .syntaxhighlighter .line {
            white-space: pre !important;
        }
        .syntaxhighlighter table {
            width: 100% !important;
        }
        .syntaxhighlighter table caption {
            text-align: left !important;
            padding: .5em 0 0.5em 1em !important;
        }
        .syntaxhighlighter table td.code {
            width: 100% !important;
        }
        .syntaxhighlighter table td.code .container {
            position: relative !important;
        }
        .syntaxhighlighter table td.code .container textarea {
            box-sizing: border-box !important;
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            background: white !important;
            padding-left: 1em !important;
            overflow: hidden !important;
            white-space: pre !important;
        }
        .syntaxhighlighter table td.gutter .line {
            text-align: right !important;
            padding: 0 0.5em 0 1em !important;
        }
        .syntaxhighlighter table td.code .line {
            padding: 0 1em !important;
        }
        .syntaxhighlighter.nogutter td.code .container textarea, .syntaxhighlighter.nogutter td.code .line {
            padding-left: 0em !important;
        }
        .syntaxhighlighter.show {
            display: block !important;
        }
        .syntaxhighlighter.collapsed table {
            display: none !important;
        }
        .syntaxhighlighter.collapsed .toolbar {
            padding: 0.1em 0.8em 0em 0.8em !important;
            font-size: 1em !important;
            position: static !important;
            width: auto !important;
            height: auto !important;
        }
        .syntaxhighlighter.collapsed .toolbar span {
            display: inline !important;
            margin-right: 1em !important;
        }
        .syntaxhighlighter.collapsed .toolbar span a {
            padding: 0 !important;
            display: none !important;
        }
        .syntaxhighlighter.collapsed .toolbar span a.expandSource {
            display: inline !important;
        }
        .syntaxhighlighter .toolbar {
            position: absolute !important;
            right: 1px !important;
            top: 1px !important;
            width: 11px !important;
            height: 11px !important;
            font-size: 10px !important;
            z-index: 10 !important;
        }
        .syntaxhighlighter .toolbar span.title {
            display: inline !important;
        }
        .syntaxhighlighter .toolbar a {
            display: block !important;
            text-align: center !important;
            text-decoration: none !important;
            padding-top: 1px !important;
        }
        .syntaxhighlighter .toolbar a.expandSource {
            display: none !important;
        }
        .syntaxhighlighter.ie {
            font-size: .9em !important;
            padding: 1px 0 1px 0 !important;
        }
        .syntaxhighlighter.ie .toolbar {
            line-height: 8px !important;
        }
        .syntaxhighlighter.ie .toolbar a {
            padding-top: 0px !important;
        }
        .syntaxhighlighter.printing .line.alt1 .content,
        .syntaxhighlighter.printing .line.alt2 .content,
        .syntaxhighlighter.printing .line.highlighted .number,
        .syntaxhighlighter.printing .line.highlighted.alt1 .content,
        .syntaxhighlighter.printing .line.highlighted.alt2 .content {
            background: none !important;
        }
        .syntaxhighlighter.printing .line .number {
            color: #bbbbbb !important;
        }
        .syntaxhighlighter.printing .line .content {
            color: black !important;
        }
        .syntaxhighlighter.printing .toolbar {
            display: none !important;
        }
        .syntaxhighlighter.printing a {
            text-decoration: none !important;
        }
        .syntaxhighlighter.printing .plain, .syntaxhighlighter.printing .plain a {
            color: black !important;
        }
        .syntaxhighlighter.printing .comments, .syntaxhighlighter.printing .comments a {
            color: #008200 !important;
        }
        .syntaxhighlighter.printing .string, .syntaxhighlighter.printing .string a {
            color: blue !important;
        }
        .syntaxhighlighter.printing .keyword {
            color: #006699 !important;
            font-weight: bold !important;
        }
        .syntaxhighlighter.printing .preprocessor {
            color: gray !important;
        }
        .syntaxhighlighter.printing .variable {
            color: #aa7700 !important;
        }
        .syntaxhighlighter.printing .value {
            color: #009900 !important;
        }
        .syntaxhighlighter.printing .functions {
            color: #ff1493 !important;
        }
        .syntaxhighlighter.printing .constants {
            color: #0066cc !important;
        }
        .syntaxhighlighter.printing .script {
            font-weight: bold !important;
        }
        .syntaxhighlighter.printing .color1, .syntaxhighlighter.printing .color1 a {
            color: gray !important;
        }
        .syntaxhighlighter.printing .color2, .syntaxhighlighter.printing .color2 a {
            color: #ff1493 !important;
        }
        .syntaxhighlighter.printing .color3, .syntaxhighlighter.printing .color3 a {
            color: red !important;
        }
        .syntaxhighlighter.printing .break, .syntaxhighlighter.printing .break a {
            color: black !important;
        }
        .syntaxhighlighter {
            background-color: #121212 !important;
        }
        .syntaxhighlighter .line.alt1 {
            background-color: #121212 !important;
        }
        .syntaxhighlighter .line.alt2 {
            background-color: #151515 !important;
        }
        .syntaxhighlighter .line.highlighted {
            background-color: rgba(242, 35, 46, 0.5) !important;
        }
        .syntaxhighlighter .line.highlighted.number {
            color: white !important;
        }
        .syntaxhighlighter table caption {
            color: white !important;
        }
        .syntaxhighlighter .gutter {
            color: #afafaf !important;
        }
        .syntaxhighlighter .gutter .line {
            border-right: 3px solid #5BCAF5 !important;
        }
        .syntaxhighlighter .gutter .line.highlighted {
            background-color: rgba(242, 35, 46, 0.5) !important;
            color: #fff !important;
        }
        .syntaxhighlighter.printing .line .content {
            border: none !important;
        }
        .syntaxhighlighter.collapsed {
            overflow: visible !important;
        }
        .syntaxhighlighter.collapsed .toolbar {
            color: #5BCAF5 !important;
            background: black !important;
            border: 1px solid #5BCAF5 !important;
        }
        .syntaxhighlighter.collapsed .toolbar a {
            color: #5BCAF5 !important;
        }
        .syntaxhighlighter.collapsed .toolbar a:hover {
            color: #d01d33 !important;
        }
        .syntaxhighlighter .toolbar {
            color: white !important;
            background: #5BCAF5 !important;
            border: none !important;
        }
        .syntaxhighlighter .toolbar a {
            color: white !important;
        }
        .syntaxhighlighter .toolbar a:hover {
            color: #96daff !important;
        }
        .syntaxhighlighter .plain, .syntaxhighlighter .plain a {
            color: white !important;
        }
        .syntaxhighlighter .comments, .syntaxhighlighter .comments a {
            color: #39ff41 !important;
        }
        .syntaxhighlighter .string, .syntaxhighlighter .string a {
            color: #e3e658 !important;
        }
        .syntaxhighlighter .keyword {
            color: #5BCAF5 !important;
        }
        .syntaxhighlighter .preprocessor {
            color: #435a5f !important;
        }
        .syntaxhighlighter .variable {
            color: #DB387C !important;
        }
        .syntaxhighlighter .value {
            color: #009900 !important;
        }
        .syntaxhighlighter .functions {
            color: #aaaaaa !important;
        }
        .syntaxhighlighter .constants {
            color: #96daff !important;
        }
        .syntaxhighlighter .script {
            font-weight: bold !important;
            color: #d01d33 !important;
            background-color: none !important;
        }
        .syntaxhighlighter .color1, .syntaxhighlighter .color1 a {
            color: #ffc074 !important;
        }
        .syntaxhighlighter .color2, .syntaxhighlighter .color2 a {
            color: #4a8cdb !important;
        }
        .syntaxhighlighter .color3, .syntaxhighlighter .color3 a {
            color: #96daff !important;
        }
        .syntaxhighlighter .functions {
            font-weight: bold !important;
        }
    </style>
</head>
<body>
<!-- CONTAINER -->
<walrus id="WALRUS-e2">

    <!-- COLUMN -->
    <walrus id="WALRUS-column">
        <!-- MULTIPLE EXCEPTION -->
        <?php if ($e2nb > 1): ?>
        <walrus id="WALRUS-multiple-exception">
        <?php foreach ($e2s as $key => $e2): ?>
            <span data-id="<?php echo $key; ?>">
                <?php echo (int)$key + 1; ?>
            </span>
        <?php endforeach; ?>
            <walrus style="clear:both;"></walrus>
        </walrus>
        <?php endif; ?>
        <!-- MULTIPLE EXCEPTION -->

        <?php foreach ($e2s as $key => $e2): ?>
        <walrus class="Walrus-column-multiple <?php if ($key == 0) echo '_active';?>" data-id="<?php echo $key; ?>">
            <!-- MAIN E2 -->
            <walrus class="WALRUS-trace _active" data-trace="0">
                <p>
                    <?php echo $e2['title']; ?>
                    <span></span>
                </p>
                <p>
                    ...<?php echo $e2['file']; ?>
                    <span>:<?php echo $e2['line']; ?></span>
                </p>
            </walrus>
            <!-- MAIN E2 -->

            <!-- TRACES -->
            <?php foreach($e2s[0]['trace'] as $key => $trace): ?>
                <walrus  class="WALRUS-trace" data-trace="<?php echo (int)$key + 1; ?>">
                    <p>
                        <?php echo $trace['class']; ?>
                        <span><?php echo $trace['function']; ?></span>
                    </p>
                    <p>
                        ...<?php echo $trace['file']; ?><!--
                     --><span>:<?php echo $trace['line']; ?></span>
                    </p>
                </walrus>
            <?php endforeach; ?>
            <!-- COLUMN -->
        </walrus>
        <?php endforeach; ?>
    </walrus>
    <!-- TRACE -->

    <!-- INFOS -->
    <?php foreach($e2s as $key => $e2): ?>
        <walrus class="WALRUS-info <?php if ($key == 0) echo '_active'; ?>" data-traceInfos="<?php echo $key; ?>">

            <!-- TITLE -->
            <walrus class="WALRUS-title">
                <h1><?php echo $e2['title']; ?></h1>
                <p><?php echo $e2['content']; ?></p>
            </walrus>
            <!-- TITLE -->

            <!-- CODE MAIN -->
            <walrus class="WALRUS-code _active" data-traceCode="0">
                <?php if(isset($e2['code']['comment'])): ?>
                <pre class="brush: php; toolbar: false;">    <?php echo $e2['code']['comment']; ?>
                </pre>
                <?php endif; ?>
                <pre class="brush: php; toolbar: false; highlight: <?php echo $e2['code']['highlight']; ?>"><?php
                    echo htmlspecialchars($e2['code']['code']); ?>
                </pre>
            </walrus>
            <!-- CODE MAIN -->

            <!-- CODE TRACE -->
            <?php foreach ($e2['trace'] as $key => $trace): ?>
                <walrus class="WALRUS-code" data-traceCode="<?php echo (int)$key + 1; ?>">
                    <?php if(isset($trace['code']['comment'])): ?>
                    <pre class="brush: php; toolbar: false;">    <?php echo $trace['code']['comment']; ?>
                    </pre>
                    <?php endif; ?>
                    <pre class="brush: php; toolbar: false; highlight: <?php echo $trace['code']['highlight']; ?>"><?php
                        echo htmlspecialchars($trace['code']['code']); ?>
                    </pre>
                </walrus>
            <?php endforeach; ?>
            <!-- CODE TRACE -->

            <!-- DATA -->
            <walrus>

            </walrus>
            <!-- DATA -->

        </walrus>
    <?php endforeach; ?>
    <!-- INFOS -->

</walrus>
<!-- CONTAINER -->

<script>
    <?php
        require_once(ROOT_PATH . 'Walrus/templates/scripts/jquery.js');
        require_once(ROOT_PATH . 'Walrus/templates/scripts/e2-scripts.js');
    ?>

    (function(){
        SyntaxHighlighter.highlight();

        $('#WALRUS-e2').find('#WALRUS-multiple-exception span').click(function() {
            var id = $(this).data('id');
            $('#WALRUS-e2').find('.WALRUS-column-multiple').removeClass('_active');
            $('#WALRUS-e2').find('.WALRUS-column-multiple[data-id="' + id + '"]').addClass('_active');
            $('#WALRUS-e2').find('.WALRUS-info').removeClass('_active');
            $('#WALRUS-e2').find('.WALRUS-info[data-traceinfos="' + id + '"]').addClass('_active');
        });

        $('#WALRUS-e2').find('.Walrus-column-multiple .WALRUS-trace').click(function() {
            var id = $(this).parents('.Walrus-column-multiple:first').data('id');
            $('#WALRUS-e2').find('.Walrus-column-multiple[data-id="' + id + '"] .WALRUS-trace, .WALRUS-code')
                .removeClass('_active');
            $('#WALRUS-e2').find('.WALRUS-info[data-traceinfos="' + id + '"] .WALRUS-code[data-traceCode="'
                + $(this).data('trace') + '"]').addClass('_active');
            $(this).addClass('_active');
        });
    }());
</script>