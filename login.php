<?php

// #################### LOGIN SYSTEM ################
// 
//
//
// set session variable "access_granted" to 1
//

/*
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);*/
session_start();

$target_address = "http://example.com";

function redirect($address){
	header("Location: ".$address);
	die();
}

if(isset($_SESSION["access_granted"]) && $_SESSION["access_granted"]==1){
	redirect($target_address);
}

include('sqlConfig.php');

set_include_path(get_include_path() . PATH_SEPARATOR . "phpseclib");
include('Crypt/RSA.php');
$sql_tablename = "users";

$rsa=new Crypt_RSA();



if(!isset($_SESSION['private'])){
	
	extract($rsa->createKey());
	$_SESSION["private"] = $privatekey;
	$_SESSION["public"] = $publickey;
}

$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);

if(isset($_POST["cusr"]) && isset($_POST["cpwd"])){
	$cusr = $_POST["cusr"];
	$cpwd = $_POST["cpwd"];
	$rsa->loadkey($_SESSION["private"]);


	$pusr = $rsa->decrypt(base64_decode($cusr));
	$ppwd = $rsa->decrypt(base64_decode($cpwd));

	$sql = new mysqli($sql_hostname, $sql_username, $sql_password, $sql_database);
	$stm = $sql->prepare("select hash from {$sql_tablename} where username = ?");
	$stm->bind_param("s", $pusr);
	$stm->execute();
	$stm->bind_result($hash);
	$stm->fetch();
	
	if(password_verify($ppwd, $hash) == true){
		$_SESSION["access_granted"] = 1;
		redirect($target_address);
	}
	

	$stm->close();

}


?>

<html>
<head>
<title>Login</title>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="jsencrypt.min.js"></script>
</head>
<body>

<style type="text/css">

body{
	text-align: center;
}

</style>
<script type="text/javascript">

function onSend(){
	var encrypt = new JSEncrypt();
	encrypt.setPublicKey($('#public').val());

	var cpwd = encrypt.encrypt($("#pwd").val());
	$("#pwd").val(cpwd);

	var cusr = encrypt.encrypt($("#usr").val());
	$("#usr").val(cusr);

	return true;
}

</script>


<?php print("<textarea id='public' style='display:none'>".$_SESSION["public"]."</textarea>");?>

<form method="post" onsubmit="return onSend()">
<p>Username</p>
<input id="usr" type="text" name="cusr">
<p>Password</p>
<input id="pwd" type="password" name="cpwd">
<br>
<br>
<input type="submit" value="OK">
</form>

</body>
</html>


