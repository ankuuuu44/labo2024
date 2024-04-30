<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
	require"notlogin.html";
	session_destroy();
	exit;
}
$_SESSION["examflag"] = 0;
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>初期順序決定</title>
</head>

<body background="image/checkgreen.jpg">
<div align="center">
	<FONT size="6">初期順序決定</FONT>
	</br>
<?php
session_start();
require "dbc.php";
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$dtcnt = $_SESSION["dtcnt"];
$divide2 = $_SESSION["divide2"];
$view_Sentence = $_SESSION["view_Sentence"];
$rock =$_SESSION["rock"];
echo "<br>";

?>

<table style="border:3px dotted blue;" cellpadding="5"><tr><td>
<font size = 4>
<b>日本文</b>：<?php echo $Japanese; ?></br>
<b>問題文</b>：<?php echo $Sentence; ?></br>
</font>
</td></tr></table><br>

<font size = 4>
<b>初期順序を以下のように登録しますか？</br></br></b>



<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();
$MemberID = $_SESSION["MemberID"];


$WID = $_SESSION["WID"];
echo "問題番号".$WID."<br>";


$Part_Sentence = $_SESSION["Part_Sentence"]; 
//$Part_Sentence= str_replace(" ", "", $Part_Sentence);
echo "部分点フレーズ".$Part_Sentence."<br>";
$_SESSION["Part_Sentence"] = $Part_Sentence;
$point = $_SESSION["Part_Point"]; 
echo "得点".$point."<br>";
$type = $_SESSION["type"];
echo "形式".$type."<br>";

$type2 = gettype($Part_Sentence);
echo $type2;
?>
</font>

<form method="post" action="insert_point.php">
<br>
<input type="submit" value="登録" />
</form>


<a href="javascript:history.go(-7);">問題登録</a>
＞
<a href="javascript:history.go(-5);">区切り決定</a>
＞
<a href="javascript:history.go(-3);">固定ラベル決定</a>
＞
<font size="4" color="red"><u>初期順序決定</u></font>
＞登録
</br>


</div>
</body>
</html>