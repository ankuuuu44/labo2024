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
	<title>登録</title>
</head>

<body background="image/checkgreen.jpg">
<div align="center">
	<FONT size="6">登録画面</FONT>
	</br>
<?php
session_start();
require "dbc.php";

$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$divide2 =$_SESSION["divide2"];
$dtcnt = $_SESSION["dtcnt"];
$start = $_SESSION["start"];
$fix = $_SESSION["fix"];
$fixlabel = $_SESSION["fixlabel"];
$num = $_SESSION["num"];
$level = $_SESSION["level"];
$pro = $_SESSION["pro"];
?>

<table style="border:3px dotted blue;" cellpadding="5"><tr><td>
<font size = 4>
<b>問題番号</b>：<?php echo $dtcnt; ?></br>
<b>日本文</b>：<?php echo $Japanese; ?></br>
<b>問題文</b>：<?php echo $Sentence; ?></br>
<b>区切り</b>：<?php echo $divide2; ?></br>
<b>初期順序</b>：<?php echo $start; ?></br>
<b>固定ラベル</b>：<?php echo $fixlabel; ?></br>

</font>
</td></tr></table><br>
<?php

?>
<font size = 4>
<b>登録を中止しますか？</br></br></b>
</font>

<form method="post" action="question.php">

<input type="submit" value="中止する" />
<input type="button" value="戻る" onclick="history.back();">
</form>
	


</div>
</body>
</html>