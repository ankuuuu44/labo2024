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
	<title>難易度・文法項目編集</title>
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" /> 
</head>

<body>
<div align="center">
	<FONT size="6">難易度・文法項目編集</FONT>
	</br>
<?php
session_start();
require "dbc.php";
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$divide2 =$_SESSION["divide2"];
$fix = $_SESSION["fix"];
$fixlabel = $_SESSION["fixlabel"];
$num = $_SESSION["num"];
$WID = $_SESSION["dtcnt"];
echo "<br>";

?>

<table style="border:3px dotted red;" cellpadding="5"><tr><td>
<font size = 4>
<b>問題番号</b>：<?php echo $WID; ?></br>
<b>日本文</b>：<?php echo $Japanese; ?></br>
<b>問題文</b>：<?php echo $Sentence; ?></br>
</font>
</td></tr></table><br>

<font size = 4>
<b>以下のように決定しました。</br></br></b>
</font>

<?php
$sql = "select Item from grammar 
        ORDER BY GID;";
$PID = 1;
$res = mysql_query($sql,$conn) or die("接続エラー");
while ($row = mysql_fetch_array($res)){
	}

$pro ="#";//文法項目記録用
$level = $_POST["level"];//難易度記録用
if(isset($_POST["check"])){
$check = $_POST["check"];
}
$j=0;
for ($i = 1; $i < $num+1; $i++){
	if($check[$j] == $i){
		$pro = $pro.$i."#";
		$j++;
	}
}
$_SESSION["level"] = $level;
$_SESSION["pro"] = $pro;
?>

	
<b>難易度</b>：<?php echo $level; ?></br>
<b>文法項目</b>：<?php echo $pro; ?></br>


<form method="post" action="insert_property.php">
<input type="submit" value="決定" class="button"/><br><br>
<input type="button" value="1つ前に戻る" onclick="history.back();" class="btn_mini">
</form>


</div>
</body>
</html>