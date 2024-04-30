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
	<title>部分点登録</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" /> 
</head>

<body>
<div align="center">
	<FONT size="6">部分点登録画面</FONT>
	</br>
<?php
session_start();
require "dbc.php";

$WID = $_SESSION["WID"];
$Part_Sentence = $_SESSION["Part_Sentence"];
if(strstr($Part_Sentence,"'")){
   $Part_Sentence = str_replace("'","\'",$Part_Sentence);
}
 
$point = $_SESSION["Part_Point"]; 
//$type = $_SESSION["type"];
$posi = $_POST["posi"];
//$type = implode(",", $_POST["posi"]);

if($posi[0]=="1"){
    $sql_ins = "insert into partans VALUES($WID,'".$Part_Sentence."',$point,1)";
    echo $sql_ins."<br>";

	if (!$res = mysql_query($sql_ins,$conn)) 
		echo "SQL実行時エラー" ;
		exit ;
	}
}
if($posi[0] =="2" or $posi[1]=="2"){
    $sql_ins = "insert into partans VALUES($WID,'".$Part_Sentence."',$point,2)";
    echo $sql_ins."<br>";

	if (!$res = mysql_query($sql_ins,$conn)) {
		echo "SQL実行時エラー" ;
		exit ;
	}
}
if ($posi[0] =="3" or $posi[1]=="3" or $posi[2]=="3"){
    $sql_ins = "insert into partans VALUES($WID,'".$Part_Sentence."',$point,3)";
    echo $sql_ins."<br>";

	if (!$res = mysql_query($sql_ins,$conn)) {
		echo "SQL実行時エラー" ;
		exit ;
	}
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
 $_SESSION["Part_Sentence"] ="";
 $_SESSION["Part_Point"]="a"; 
 $_SESSION["type"]="";
?>
<font size = 4>
<b>登録完了しました。</br></br></b>
</font>
<?php
    echo "<a href=\"revise.php?WID=".$WID."&mode=1\">続けて部分点を登録する";
    echo "<br>";
?>
<a href="./question.php" class="button">　問題表示画面へ　</a><br>
</div>
</body>
</html>