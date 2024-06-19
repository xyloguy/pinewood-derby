<?php
session_start();
function clean_post($form_name) {
if (empty(trim($form_name)) || !isset($_POST[$form_name])) {
return false;
}

if(
!empty($_POST['checksum'])
&& !empty(get_checksum($form_name))
&& (
(is_array($_POST['checksum']) && in_array(get_checksum($form_name), $_POST['checksum']))
|| (is_string($_POST['checksum']) && get_checksum($form_name) == $_POST['checksum'])
)
){
unset($_SESSION['checksum'][$form_name]);
return true;
}
return false;
}

function get_checksum($key) {
if(empty($_SESSION['checksum']) or empty($_SESSION['checksum'][$key])) {
update_checksum($key);
}
return $_SESSION['checksum'][$key];
}

function update_checksum($key) {
if(empty($_SESSION['checksum'])) {
$_SESSION['checksum'] = [];
}
$_SESSION['checksum'][$key] = md5($key . rand());
}