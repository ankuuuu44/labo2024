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
	<title>削除</title>
</head>

<body background="image/checkgreen.jpg">
<div align="center">
	<FONT size="6">削除画面</FONT>
	</br>
<?php
session_start();
require "dbc.php";

$WID=$_SESSION["WID"]; 
$PartSentence = $_SESSION["PartSentence"];
$Point = $_SESSION["Point"];
$type =$_SESSION["type"];

$sql_ins = "delete from partans where WID=".$WID." and PartSentence = '".$PartSentence."' 
and Point= ".$Point." and type =".$type;

print "<br>";

//SQLを実行
//echo $sql_ins;

if (!$res = mysql_query($sql_ins,$conn)) {
	echo "SQL実行時エラー" ;
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
<b>削除完了しました。</br></br></b>
</font>
<?php
    echo "<a href=\"revise.php?WID=".$WID."&mode=1\">続けて部分点を編集する";
    echo "<br>";
?>
<a href="http://lmo.cs.inf.shizuoka.ac.jp/~miki/create/question.php">　問題表示画面へ　</a><br><br>
</div>
</body>
</html>