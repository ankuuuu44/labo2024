<?php

error_reporting(0);
session_start();

//文字コード指定
mb_language("uni");
mb_internal_encoding("utf-8");
mb_http_input("auto");
mb_http_output("utf-8");

$sv = "192.168.11.2";//研究室ローカル
$user = "maintainer"; //ドメイン名
$pass = "maintainer987654";//パスワード
$dbname ="rireki201310";//データベース名

$examflag = $_SESSION["examflag"];

$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");

$sql = "SET NAMES utf8";

//データベース選択
mysql_select_db($dbname,$conn) or die("接続エラー2");
mysql_query($sql,$conn);

?>