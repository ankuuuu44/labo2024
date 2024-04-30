<?php
error_reporting(0);   // 運用晁E
session_start();
mb_language("uni");
mb_internal_encoding("utf-8"); //冁E��斁E��コードを変更
mb_http_input("auto");
mb_http_output("utf-8");
$dbnl="2019su1";  //研究室	

$examflag = $_SESSION["examflag"];
$dbname = $dbnl;
$sv = "133.70.173.59";//研究室グルーバル�E��E� ローカル⇒グローバル接続�E時使用
$user = "domine"; //サーチE
$pass = "miyamoto0117";//サーチE
//接綁E
$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");
$sql = "SET NAMES utf8";
mysql_select_db($dbname) or die("接続エラー2");
mysql_query($sql,$conn);

?>
