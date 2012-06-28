<?php

error_reporting (E_ERROR);

session_start();

$sid = $_GET["sid"];

echo 'document.getElementById("comment_captcha_value").value="'.$_SESSION[$sid].'";';
?>