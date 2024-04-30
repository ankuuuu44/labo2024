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
	<title>部分点フレーズ削除</title>
</head>

<body background="image/checkgreen.jpg">
<div align="center">
	<FONT size="6">部分点フレーズ削除</FONT>
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


<font size = 4>
<b>以下の部分点フレーズを削除しますか？</br></br></b>
</font>


<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();


$MemberID = $_SESSION["MemberID"];

$WID =$_GET["WID"];
echo "問題番号".$WID."<br>";

$PartSentence =$_GET["PartSentence"];
$PartSentence = str_replace("'","\'",$PartSentence);
echo "部分点フレーズ".$PartSentence."<br>";

$type =$_GET["type"];
echo "形式".$type."<br>";
$Point =$_GET["Point"];
echo "得点".$Point."<br>";

$_SESSION["WID"] = $WID;
$_SESSION["PartSentence"] = $PartSentence;
$_SESSION["Point"] = $Point;
$_SESSION["type"] = $type;
?>


<form method="post" action="delete_sql.php">
<br>
<input type="submit" value="削除" />
</form>

<?php
    
    echo "<a href=\"revise.php?WID=".$WID."&mode=1\">部分点一覧に戻る";
    echo "<br>";
?>
</br>


</div>
</body>
</html>