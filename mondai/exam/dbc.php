<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

$dbn = "melty0118";     //研究室
//$dbn = "testy";          //ローカル

$examflag = $_SESSION["examflag"];
$dbname = $dbn;

// ********************************************
/*if($examflag == 2){
	echo $examflag;
	$dbname = "studylog_u"; //研究室
}
elseif($examflag == 1){
	$dbname = "examlog_u"; //研究室
	echo $examflag;
}
elseif($examflag == 0){
	//echo $examflag."aaa";
	if($_SESSION["page"] = "exam"){
		$dbname = "examlog_u";
	}
	elseif($_SESSION["page"] = "ques"){
		$dbname = "examlog_u";
	}
	else{
	$dbname = "studylog_u"; //研究室
	}
}*/
// *************************************不要

//接続設定
//$sv = "133.70.173.47";//研究室グルーバルＩＰ
$sv = "192.168.11.2";//研究室ローカルＩＰ
//$sv = "localhost"; //自分のPC

//$dbname = "testx"; //自分のＰＣ
$user = "maintainer"; //研究室ローカル
$pass = "maintainer987654";//研究室ローカル
//接続
$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");
mysql_select_db($dbname) or die("接続エラー2");
$sql = "SET NAMES utf8";
mysql_query($sql,$conn);

?>