<?php
$hostname = "localhost";
$dbuserid = "root";
$dbpasswd = "";
$dbname = "test";

$mysqli = new mysqli($hostname, $dbuserid, $dbpasswd, $dbname);
if ($mysqli->connect_errno) {
    die('Connect Error: ' . $mysqli->connect_error);
}

$result = $mysqli->query("select * from board") or die("query error => ".$mysqli->error);
while($rs = $result->fetch_object()){
    $rsc[]=$rs;
}

// echo "<pre>";
// print_r($rsc);

?>