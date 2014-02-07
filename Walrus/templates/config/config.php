<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Configuration | Walrus</title>
</head>
<body>

<h1>Create your project configuration</h1>

<style>
    body {
        background-color: #2b81af;
    }
    h1 {
        font-family: 'arial';
        color: #ffffff;
        text-align: center;
    }
</style>

<?php
if (isset($validation)) {
    if ($validation) {
?>
        <div id="infos">

        </div>
<?php
    } else {

    }
}
?>
    <form action="./" method="POST">
        <input type="hidden" name="config" value="ok"/>
        <table>
            <tr>
                <td colspan="2" style="">Database:</td>
            </tr>
            <tr>
                <td>RDBMS :
                <td>
                    <select name="RDBMS">
                        <option value="mysql" selected>MySQL</option>
                        <option value="sqlite">SQLite</option>
                        <option value="postgresql">PostgreSQL</option>
                        <option value="oracle">Oracle</option>
                    </select>
                </td>
            <tr>
                <td>Database :</td>
                <td><input type="text" name="database"></td>
            </tr>
            <tr>
                <td>Hostname :</td>
                <td><input type="text" name="host"></td>
            </tr>
            <tr>
                <td>User name :</td>
                <td><input type="text" name="name"></td>
            </tr>
            <tr>
                <td>Password :</td>
                <td><input type="password" name="password"></td>
            </tr>
            <tr>
                <td>Templating :</td>
                <td>
                    <select name="templating">
                        <option value="haml" selected>HAML</option>
                        <option value="smarty">Smarty</option>
                        <option value="php">PHP</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Environment :</td>
                <td>
                    <select name="environment">
                        <option value="dev" selected>Development</option>
                        <option value="production">Production</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" value="Send"/>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>