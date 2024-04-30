<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();
//$WID = rand(0,199);
//$_SESSION["WID"] = $_GET['param2'];
require"dbc.php";
//英文の参照
if($_GET['param2']=="q"){


$Question = "SELECT Text FROM examnums WHERE (NID= ".$_GET['param1'].")";//ＤＢから英文を得る


$res = mysql_query($Question, $conn) or die("英文抽出エラー");

$count = mysql_num_rows($res);



//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo $row['Text'];
	$Answer = ucfirst($Question); //回答（先頭を大文字に)
    $_SESSION["Answer"] = $Answer;
	mysql_free_result($res);
}
else{
	echo "エラー";
	}
}
//-------------------------------------------------------------------------------------
//日本語の参照
else if($_GET['param2']=="ans"){
	$JP = "SELECT Answer FROM examnums WHERE (NID= ".$_GET['param1'].")";//日本文取得
$res = mysql_query($JP, $conn) or die("答え抽出エラー");
$count = mysql_num_rows($res);
//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo $row['Answer'];
	mysql_free_result($res);
}
else{
	echo "エラー";
	}	
}
//----------------------------------------------------------------
//Fixの参照
else if($_GET['param2']=="term"){
	$TM = "SELECT Term FROM examnums WHERE (NID= ".$_GET['param1'].")";//条件取得
$res = mysql_query($TM, $conn) or die("条件抽出エラー");
$count = mysql_num_rows($res);
//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo mb_convert_encoding($row['Term'],"UTF-8","EUC-JP");
	mysql_free_result($res);
}
else{
	echo "エラー";
	}
}
?>