<?php

//DB書き込み
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
//error_reporting(0);   // 運用時

session_start();
require"dbc.php";
extract($_POST);


//echo $id;

$Question = "SELECT count(*) as cnt FROM linedata WHERE UID = '".$id."'";//ＤＢから英文を得る
//echo $Question;
$res = mysql_query($Question, $conn) or die("英文抽出エラー");
$row = mysql_fetch_array($res);

echo "解答数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$row['cnt']."問</br>";
$Correct = "SELECT count(*) as cnt FROM linedata WHERE UID = '".$id."' 
			and TF = 1";//ＤＢから英文を得る

$res_correct = mysql_query($Correct, $conn) or die("英文抽出エラー");
$row_correct = mysql_fetch_array($res_correct);

$correct_per = sprintf("%.1f",$row_correct['cnt'] / $row['cnt'] * 100);
echo "正解:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp    ".$correct_per."% (".$row_correct['cnt']."/".$row['cnt'].")<br>";

$login = "SELECT count(*) as cnt FROM lastday WHERE UID = '".$id."'";//ＤＢから英文を得る
$res_login = mysql_query($login, $conn) or die("英文抽出エラー");
$row_login = mysql_fetch_array($res_login);
echo "アクセス回数: ".$row_login['cnt']."回<br>";

$avgtime = "SELECT avg(Time) FROM linedata WHERE UID = '".$id."'";//ＤＢから英文を得る
$res_time = mysql_query($avgtime, $conn) or die("英文抽出エラー");
$row_time = mysql_fetch_array($res_time);
$row_time['avg(Time)'] = sprintf("%.2f",$row_time['avg(Time)']/ 1000);
echo "平均解答時間: ".$row_time['avg(Time)']."秒<br>";



?>