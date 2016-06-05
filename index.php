<?php

// #################### LOGIN SYSTEM ################
// 
//
//
// set session variable "access_granted" to 1
//


session_start();

$target_address = "http://example.com";

function redirect($address){
	header("Location: ".$address);
	die();
}

if($_SESSION['access_granted']==1){
	redirect($target_address);
}

include('admin.php');
include('phpseclib/Crypt/RSA.php');

if(!isset($_SESSION['private'])){
	$rsa=new Crypt_RSA();
	extract($rsa->createKey());
	$_SESSION["private"] = $privatekey;
	$_SESSION["public"] = $publickey;
}

?>

<html>
<head>
<title>Login</title>
</head>
<body>
<div id='publickey' class='hidden'><?= $_SESSION['public'] ?></div>

asdf

</body>
</html>


