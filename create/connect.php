<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

require"dbc.php";

$MemberID = $_SESSION["MemberID"];

if($_GET['param3'] == "part"){
 $_SESSION["Part_Sentence"] = $_GET['param1'];
 $_SESSION["Part_Point"] = $_GET['param2'];
 $_SESSION["type"] = 0;
}else if($_GET['param3'] == "another"){
 $_SESSION["Part_Sentence"] = $_GET['param1'];
 $_SESSION["Part_Point"] = $_GET['param2'];
 $_SESSION["type"] = 0;
}else{
$_SESSION["StartSentence"] = $_GET['param1'];
echo $_SESSION["StartSentence"]; 
}
?>