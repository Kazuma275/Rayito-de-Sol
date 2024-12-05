<?php
session_start();

$_SESSION = [];

session_unset();
session_destroy();

header("location : __DIR__ . '/index.php");

exit();
?>
