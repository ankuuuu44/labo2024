<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

mb_language("uni");
mb_internal_encoding("utf-8"); //内部文字コードを変更
mb_http_input("auto");
mb_http_output("utf-8");


$dbnl="niyon_kdb";  //研究室
//$dbnl="testy";    //ローカル

$examflag = $_SESSION["examflag"];
$dbname = $dbnl;
//****************************************
/*if($examflag == 2){
	echo $examflag;
	$dbname = $dbnl;
}
elseif($examflag == 1){
	$dbname = $dbnl;
	echo $examflag;
}
elseif($examflag == 0){
	//echo $examflag."aaa";
	if($_SESSION["page"] = "exam"){
		$dbname = $dbnl;
	}elseif($_SESSION["page"] = "ques"){
		$dbname = $dbnl;
	}else{
		$dbname = $dbnl;
	}
}***********************************************/
//接続設定
//$sv = "133.70.173.47";//研究室グルーバルＩＰ
$sv = "192.168.11.2";//研究室ローカルＩＰ
//$sv = "localhost"; //自分のPC

//$user = "root"; //自分
//$pass = "ttjhsh6";//自分
$user = "maintainer"; //サーバ
$pass = "789514";//サーバ
//接続
$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");
$sql = "SET NAMES utf8";

//mysqli_set_charset("ujis",$conn);    //文字コード指定
mysql_select_db($dbname) or die("接続エラー2");
mysql_query($sql,$conn);

?>