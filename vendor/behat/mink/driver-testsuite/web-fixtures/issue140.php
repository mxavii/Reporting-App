<?php
require_once 'utils.php';

if (!empty($_POST)) {
    setcookie("tc", $_POST['cookie_value'], null, '/');
} elseif (isset($_GET["show_value"])) {
    echo html_escape_value($_COOKIE["tc"]);
    die();
}
?>
<!DOCTYPE html>
<html>
<body>
    <form method="post">
        <input name="cookie_value">
        <input type="submit" value="Set cookie">
    </form>
</body>
