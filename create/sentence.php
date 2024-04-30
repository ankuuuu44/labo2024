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
	<title>英文検索</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
</head>

<body>
<div align="center">
	<FONT size="6">英文検索</FONT>
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
<FONT size="3">英文に含むフレーズを入力してください。</FONT>
<br>
※大文字小文字は区別しません。<br><br>
	
<form action = "question.php" method="post">
      <b>**英文フレーズ**</b><br>※英語で入力して下さい。<br><input type="text" name="Sentence" ><br><br>
      <input name="radiobutton" type="radio" value="all" >完全一致
　　　<input name="radiobutton" type="radio" value="part" checked>部分一致
　　<br><br>
　　　<input type="submit" name="exe" value="検索" class="button">
　　　<input type="reset" name="exe" value="リセット" class="button">
</form>
<br><br>
<a href = "question.php" class="btn_mini">戻る</a>


</div>
</body>
</html>
