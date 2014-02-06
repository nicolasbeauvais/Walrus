<h1>Create your project configuration</h1>

<form action="./" method="POST">
    <table>
        <tr>
            <td>Templating :</td>
            <td>
                <select name="templating">
                    <option value="haml">HAML</option>
                    <option value="twig">Twig</option>
                    <option value="smarty">Smarty</option>
                    <option value="php" selected>PHP</option>
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
        <tr><td colspan="2">Database :</td></tr>
        <tr>
            <td>Language :
            <td>
                <select name="dbLanguage">
                    <option value="mysql" selected>MySQL</option>
                    <option value="sqlite">SQLite</option>
                    <option value="postgresql">PostgreSQL</option>
                    <option value="oracle">Oracle</option>
                </select>
            </td>
        <tr>
            <td>Name :</td>
            <td><input type="text" name="dbName"></td>
        </tr>
        <tr>
            <td>Hostname :</td>
            <td><input type="text" name="dbHost"></td>
        </tr>
        <tr>
            <td>User name :</td>
            <td><input type="text" name="dbUser"></td>
        </tr>
        <tr>
            <td>Password :</td>
            <td><input type="password" name="password"></td>
        </tr>
    </table>
</form>