<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();
//$WID = rand(0,199);
//$_SESSION["WID"] = $WID;
require"dbc.php";
//英文の参照
if($_GET['param2']=="q"){


$Question = "SELECT Sentence FROM lquestion WHERE (WID= ".$_GET['param1'].")";//ＤＢから英文を得る


$res = mysql_query($Question, $conn) or die("英文抽出エラー");

$count = mysql_num_rows($res);



//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo $row['Sentence'];
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
else if($_GET['param2']=="j"){
	$JP = "SELECT Japanese FROM lquestion WHERE (WID= ".$_GET['param1'].")";//日本文取得
$res = mysql_query($JP, $conn) or die("日本文抽出エラー");
$count = mysql_num_rows($res);
//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	//echo mb_ditect_encoding($row['Japanese']);
	//echo mb_convert_encoding($row['Japanese'],"UTF-8","EUC-JP");
	//echo mb_convert_encoding($row['Japanese'],"EUC-JP","auto");
	echo $row['Japanese'];
	mysql_free_result($res);
}
else{
	echo "エラー";
	}	
}
//----------------------------------------------------------------
//Fixの参照
else if($_GET['param2']=="f"){
	$Fix = "SELECT Fix FROM lquestion WHERE (WID= ".$_GET['param1'].")";//日本文取得
$res = mysql_query($Fix, $conn) or die("固定抽出エラー");
$count = mysql_num_rows($res);
//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo mb_convert_encoding($row['Fix'],"UTF-8","EUC-JP");
	mysql_free_result($res);
}
else{
	echo "エラー";
	}
}
//----------------------------------------------------------------
//別解１の参照
else if($_GET['param2']=="s1"){


$Question = "SELECT Sentence1 FROM lquestion WHERE (WID= ".$_GET['param1'].")";//ＤＢから英文を得る


$res = mysql_query($Question, $conn) or die("英文抽出エラー");

$count = mysql_num_rows($res);



//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo $row['Sentence1'];
	mysql_free_result($res);
}
else{
	echo "エラー";
	}
}
//---------------------------------------------
//別解2の参照
else if($_GET['param2']=="s2"){


$Question = "SELECT Sentence2 FROM lquestion WHERE (WID= ".$_GET['param1'].")";//ＤＢから英文を得る


$res = mysql_query($Question, $conn) or die("英文抽出エラー");

$count = mysql_num_rows($res);



//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo $row['Sentence2'];
	mysql_free_result($res);
}
else{
	echo "エラー";
	}
}
?>