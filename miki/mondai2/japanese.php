<?php

session_start();

$WID = $_SESSION["WID"];

require"dbc.php";

$JP = "SELECT Japanese FROM lquestion WHERE (WID= ".$WID.")";//日本文取得
$res = mysql_query($JP, $conn) or die("日本文抽出エラー");
$count = mysql_num_rows($res);
//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo mb_ditect_encoding($row['Japanese']);
	//echo mb_convert_encoding($row['Japanese'],"UTF-8","auto");
	echo $_SESSION["WID"];
	mysql_free_result($res);
}
else{
	echo "エラー";
	}
?>