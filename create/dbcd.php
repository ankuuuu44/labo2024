<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // チE��チE��晁E
error_reporting(0);   // 運用晁E
session_start();

mb_language("uni");
mb_internal_encoding("utf-8"); //冁E��斁E��コードを変更
mb_http_input("auto");
mb_http_output("utf-8");


//$dbnl="niyon_kdb";  //研究室
$dbnl="melty0604";  //研究室	
//$dbnl="tester";    //ローカル

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
//接続設宁E
//$sv = "133.70.173.47";//研究室グルーバル�E��E� ローカル⇒グローバル接続�E時使用
$sv = "192.168.11.2";//研究室ローカル�E��E� upするとき�Eこれのコメントアウト外す
//$sv = "localhost"; //自刁E�EPC

//$user = "root"; //自刁E
//$pass = "ttjhsh6";//自刁E
$user = "maintainer"; //サーチE
$pass = "maintainer987654";//サーチE
//接綁E
$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");
$sql = "SET NAMES utf8";


//mysqli_set_charset("ujis",$conn);    //斁E��コード指宁E
mysql_select_db($dbname) or die("接続エラー2");
mysql_query($sql,$conn);

?>