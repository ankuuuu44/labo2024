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
$dtcnt = $_SESSION["dtcnt"];
$divide2 =$_SESSION["divide2"];
$fix = $_SESSION["fix"];
$property = $_SESSION["property"];
$start =$_SESSION["start"];
$mode = $_SESSION["mode"];
$member = $_SESSION["MemberName"];
$wordnum =$_SESSION["wordnum"];
$radio = $_REQUEST["open"];


error_reporting(-1);

if($mode == 0){
$sql_ins = "insert into question_info VALUES($dtcnt,'".$Japanese."','".$Sentence."','".$fix."','-1'
,'-1','".$start."','".$divide2."',$wordnum,'".$member."','".$radio."')";
}else{
	$sql_ins = "update question_info SET Japanese = '".$Japanese."',Sentence = '".$Sentence."'
	,fix ='".$fix."',divide = '".$divide2."',wordnum =$wordnum,start = '".$start."',author = '".$member."'
	,open = '".$radio."' where WID = $dtcnt;";
}
print "<br>";

//SQLを実行
echo $sql_ins."<br>";
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
<b>登録完了しました。</br></br></b>
</font>

<a href="http://lmo.cs.inf.shizuoka.ac.jp/~miki/create/question.php">　問題表示画面へ　</a><br><br>
</div>
</body>
</html>