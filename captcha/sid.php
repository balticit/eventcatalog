<?php

error_reporting (E_ERROR);

include('kcaptcha.php');

if(isset($_REQUEST[session_name()])){
	session_start();
}

$captcha = new KCAPTCHA();

$sid = $_GET["sid"];
$_SESSION[$sid] = $captcha->getKeyString();

?>