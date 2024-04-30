<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

require"dbc.php";

$MemberID = $_SESSION["MemberID"];
$FName = "elinedata";

if($_GET['param1']=="aca"){
	$sql = "select count(*) from ".$FName." where TF = 1 and uid = ".$MemberID.";";
	$result = mysql_query($sql);
	if ($result) {
    	$row = mysql_fetch_array($result);
    	echo $row['count(*)'];
    	mysql_free_result($result);
	}
}
else if($_GET['param1']=="ara"){
	$sql = "select count(*) from ".$FName." where uid = ".$MemberID.";";
	$result = mysql_query($sql);
	if ($result) {
    	$row = mysql_fetch_array($result);
    	echo $row['count(*)'];
    	mysql_free_result($result);
	}
}
?>