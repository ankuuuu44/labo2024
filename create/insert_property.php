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
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" /> 
</head>

<body>
<div align="center">
	<FONT size="6">登録画面</FONT>
	</br>
<?php
session_start();
require "dbc.php";
$dtcnt = $_SESSION["dtcnt"];
$level = $_SESSION["level"];
$pro = $_SESSION["pro"];

	$sql_ins = "update question_info SET level ='".$level."', grammar ='".$pro."'
	 where WID = $dtcnt;";
print "<br>";

//SQLを実行
//echo $sql_ins;
if (!$res = mysql_query($sql_ins,$conn)) {
	echo "SQL実行時エラー" ;
    echo "res:".$res;
	exit ;
}
//データベースから切断
mysql_close($conn) ;

//メッセージ出力
 $_SESSION["Japanese"]="";
 $_SESSION["Sentence"]="";
 $_SESSION["dtcnt"]="";
 $_SESSION["divide"]="";
 $_SESSION["divide2"]="";
 $_SESSION["fix"]="";
 $_SESSION["fixlabel"]="";
 $_SESSION["num"]="";
 $_SESSION["pro"]="";
 $_SESSION["property"]="";
 $_SESSION["start"]="";
?>
<font size = 4>
<b>登録完了しました。</br></br></b>
</font>
<a href="./question.php" class="button">　問題表示画面へ　</a><br><br>
</div>
</body>
</html>