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
	<title>文法項目選択</title>
</head>

<body background="image/checkgreen.jpg">
<div align="center">
	<FONT size="6">文法項目選択</FONT>
	</br>
<?php
session_start();
require "dbc.php";
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$divide2 = $_SESSION["divide2"];
$fix = $_SESSION["fix"];
$fixlabel = $_SESSION["fixlabel"];
$WID = $_SESSION["dtcnt"];
echo "<br>";
?>
<table style="border:3px dotted blue;" cellpadding="5"><tr><td>
<font size = 4>
<b>問題番号</b>:<?php echo $WID; ?></br>
<b>日本文</b>：<?php echo $Japanese; ?></br>
<b>問題文</b>：<?php echo $Sentence; ?></br>
</font>
</td></tr></table><br>

<font size = 4>
<b>難易度を選択してください。</br></b>
</font>
<form method="post" action="property_rec.php">

<input type="radio" name="level" value="1" checked>初級
<input type="radio" name="level" value="2">中級
<input type="radio" name="level" value="3">上級
</br></br>
	
<font size = 4>
<b>文法項目を選択してください。</br></b>
</font>
<p style="width:45%; margin-left:auto;margin-right:auto;text-align:left;">
<?php
$sql = "select Item from grammar 
        ORDER BY PID;";
$PID = 1;
$res = mysql_query($sql,$conn) or die("接続エラー");
$num = 0;
//問題情報をテーブルで表示する

while ($row = mysql_fetch_array($res)){

?>
	<input type="checkbox" name="check[]" value="<?php echo $PID; ?>"><?php echo $row["Item"]; ?>
<?php
	if($PID % 4 == 0){
		echo "<br>";
	}
  $num++;
  $PID++;
}
mysql_close($conn);
$_SESSION["num"] = $num;
?>
</p>
<input type="submit" value="決定" />
<input type="button" value="1つ前に戻る" onclick="history.back();">
</form>

</div>
</body>
</html>