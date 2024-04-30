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
	<title>固定ラベル検索</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
</head>

<body>
<div align="center">
	<FONT size="6">固定ラベル検索</FONT>
	</br><br><br>
<?php
session_start();
require "dbc.php";
$sql = "select count(*) as cnt from question_info";
$res = mysql_query($sql,$conn) or die("接続エラー");
$row = mysql_fetch_array($res);
$dtcnt = $row["cnt"];
$_SESSION["dtcnt"] = $dtcnt;
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
?>
<FONT size="3">固定ラベルの形式を選択してください。</FONT>
<br><br>
	
<form action = "question.php" method="post">
      　　　  <b>**固定ラベル**</b><br><select name ="Fix">
　　			 <option value="1">指定なし</option>
　　  			 <option value="2">無し</option>
　　　			 <option value="3">有り</option>
　　　			 </select><br><br>
<input type="submit" name="exe" value="検索" class="button"><br><br>
<input type="reset" name="exe" value="リセット" class="button">
</form>
<br><br>
<a href = "question.php" class="btn_mini">戻る</a>


</div>
</body>
</html>
