<?php

session_start();
$_SESSION = array();
setcookie(session_name(), '', time() - 2592000, '/');
session_destroy();

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
// echo "<p>Done. <a href='index.php'>Go back?</a></p>";
?>