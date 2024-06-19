<?php
if (
    !empty($_POST['u'])
    && !empty($_POST['p'])
) {
    $username = trim(stripslashes($_POST['u']));
    $password = trim(stripslashes($_POST['p']));

    unset($_POST['u']);
    unset($_POST['p']);

    $encrypted_password = crypt($password, base64_encode($password));
    echo '<p>Copy and paste the following on a newline in the <code>.httpasswd</code> file and run <code>docker compose up -d</code></p>';
    echo '<textarea>' . $username . ':' .$encrypted_password . '</textarea>';
} else {

?>
<form method="post" action="gen_password.php">
    Username:<br>
    <input type="text" name="u" autocomplete="off"><br>
    <br>
    Password:<br>
    <input type="password" name="p" autocomplete="off"><br>
    <br>
    <input type="submit" value="Generate Password">
</form>
<?php
}