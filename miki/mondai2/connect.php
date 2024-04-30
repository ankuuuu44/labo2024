<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

require"dbc.php";

$MemberID = $_SESSION["MemberID"];

$_SESSION["StartSentence"] = $_GET['param1'];
$StartSentence = $_GET['param1'];
echo $StartSentence;
?>