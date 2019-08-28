<?php

include "/var/www/defaults.php";
include "/var/www/functions.php";

$ip = $_SERVER['REMOTE_ADDR'];
$expires = time()+($expire*60);
$lf = "<BR><BR>";
$message = "Something has gone wrong. Ask Dave";
$fun = '<img src="http://thecatapi.com/api/images/get?format=src&type=gif">';

if (isset($_POST["tip"])) $ip=$_POST["tip"];

echo '
<form action="tether.php" method="post">
IP: <input type="text" name="tip" value="'.$ip.'">&nbsp;&nbsp;
<input type="submit">
</form>
';

if (!isset($_POST["tip"])) exit;

echo $ip;

echo $lf;

// check whitelist

$check = shell_exec ("/var/www/kadm.php check $ip");
$check = json_decode ($check,true);

if (isset ($check[0]["ip"])) {

	echo "Whitelisted IP";

	echo $lf;

	echo $fun;

	exit;

}

$result=shell_exec ("/var/www/kadm.php insert $ip");

if ($result == "true")

	$message =" IP Added to DB";

if ($result == "false") {

	$message = "Time updated for this IP";
	shell_exec ("/var/www/kadm.php update $ip");

}

echo $message;

echo $lf;

echo "<b>Now</b>: ".date('r',time());

echo $lf;

echo "<b>Expires</b>:  ";

echo date('r', $expires);

echo $lf;

echo $fun;

?>
