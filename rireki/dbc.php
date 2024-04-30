<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);   // 運用晁E
if(!isset($_SESSION))
    {session_start();}

mb_language("uni");
mb_internal_encoding("utf-8"); //冁E��斁E��コードを変更
mb_http_input("S");
mb_http_output("utf-8");
$dbnl="2019su1";  //研究室	

$examflag = $_SESSION["examflag"];
$dbname = $dbnl;
$sv = "127.0.0.1";//研究室グルーバル�E��E� ローカル⇒グローバル接続�E時使用
$user = "root"; //サーチE
$pass = "8181saisaI";//サーチE
//接綁E
$conn = mysqli_connect($sv,$user,$pass) or die("接続エラー1");
$sql = "SET NAMES utf8";
mysqli_select_db($conn,$dbname) or die("接続エラー2");
mysqli_query($conn,$sql);

?>
