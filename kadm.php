#!/usr/bin/php
<?php

include "/var/www/functions.php";
include "/var/www/defaults.php";

@$option = $argv[1];
@$value =  $argv[2];

$security_group='sg-YOURSECURITYGROUP';
$port_to_open='';

if (!$option)

	exit ("Ask Dave for Help\n");


$now=time();

$time=time()+($expire*60);
$result=null;

if ($option == "whitelist") {

        $query = "insert into addresses set ip='$value', time='$time', flag='W'";
        $method = "single";

}

if ($option == "check") {

	$query = "select * from addresses where ip='$value' and flag='W'";
	$method = "multi";

}


if ($option == "create") {

	$query = "create table addresses (ip varchar(16), time decimal(10,0), flag varchar(1), unique (ip))";
	$method = "single";

}

if ($option == "drop") {

        $query = "drop table addresses";
        $method = "single";

}

if ($option == "insert") {

	$query = "insert into addresses set ip='$value', time='$time', flag='N'";
	$method = "single";

}

if ($option == "update") {

        $query = "update addresses set time='$time' where ip='$value'";
        $method = "single";

}

if ($option == "delete") {

	$query = "delete from addresses where ip='$value'";
	$method = "single";

}

if ($option == "clean") {

        $query = "truncate addresses";
        $method = "single";

}

if ($option == "list") {

	$query = "select * from addresses where ip='$value'";
	$method = "multi";

}

if ($option == "listall") {

	$query = "select * from addresses";
	$method = "multi";

}

if ($option == "cycle") {

        $query = "select * from addresses where flag='Y'";
        $method = "multi";

	$result = dodb ($query,$method);

	if (isset($result)) {

		foreach ($result as $value) {

			if ($now > $value["time"]) {

				$ip=$value["ip"];
			        $query = "delete from addresses where ip='$ip'";
			        $method = "single";
				dodb ($query,$method);

				$exec="/usr/local/bin/aws ec2 revoke-security-group-ingress --group-id '".$security_group."' --protocol all --port '".$port_to_open."' --cidr '".$ip."/32'";

				shell_exec ($exec);

			}

		}

	}


        $query = "select ip from addresses where flag='N'";
        $method = "multi";

        $result = dodb ($query,$method);

	if (isset($result)) {

		foreach ($result as $value) {

			$ip=$value["ip"];
                        $query = "update addresses set flag='Y' where ip='$ip'";
                        $method = "single";
                        dodb ($query,$method);

                        $exec="/usr/local/bin/aws ec2 authorize-security-group-ingress --group-id '".$security_group."' --protocol all --port '".$port_to_open."' --cidr '".$ip."/32'";

                        shell_exec ($exec);

		}

	}

	unset ($query);

}





if (isset($query)) {

	$result = dodb ($query,$method);

	$result = json_encode ($result,true);

	echo $result;

}



?>
