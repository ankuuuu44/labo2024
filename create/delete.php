<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
ini_set('display_errors',1);

if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
	require"notlogin.html";
	session_destroy();
	exit;
}

//echo $_SESSION["mode"];
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>問題の削除</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />
</head>

<body>
<div align="center">
	<FONT size="6">問題の削除</FONT>
	</br>
<?php
// session_start();
require "dbc.php";
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$divide2 = $_SESSION["divide2"];
echo "<br>";

$WID= $_GET["WID"];

$sql_data = "SELECT COUNT(*) as CNT FROM linedata WHERE WID=$WID;";
$res_data = mysql_query($sql_data,$conn) or die("接続エラー");
$row_data =mysql_fetch_assoc($res_data);

//echo "件数：{$row_data['CNT']}";

if($row_data['CNT'] >0){//履歴データが存在するかの判定スタート
	echo "履歴データがすでに存在するため削除はできません。";
	}else{
	$_SESSION["dtcnt"] = $WID;

$sql = "SELECT * FROM question_info WHERE WID=$WID;";

$res = mysql_query($sql,$conn) or die("接続エラー");

while ($row = mysql_fetch_array($res,MYSQL_ASSOC)){

    ?>
    <table style="border:3px dotted red;" cellpadding="5"><tr><td>
<font size = 4>
<b>問題番号</b>：<?php echo $row["WID"]; ?></br>
<b>日本文</b>：<?php echo $row["Japanese"]; ?></br>
<b>問題文</b>：<?php echo $row["Sentence"]; ?></br>
<b>区切り</b>：<?php echo $row["divide"]; ?></br>
<b>初期順序</b>：<?php echo $row["start"]; ?></br>
<b>固定ラベル</b>：<?php echo $row["Fix"]; ?></br>
<b>文法項目</b>：<?php echo $row["grammar"]; ?></br>
</font>
</td></tr></table><br>
<?php
$_SESSION["Japanese"] = $row["Japanese"];
$_SESSION["Sentence"] = $row["Sentence"];
$_SESSION["divide2"] = $row["divide"];
$_SESSION["start"] = $row["start"];
$_SESSION["Fix"] = $row["Fix"];
$_SESSION["WID"]=$WID;

}
echo "</table>";
?>

<FONT size="4">この問題を削除しますか？</FONT>
</br></br>
<form action = "delete_sql.php" method="post">
<input type="submit" name="exe" value="削除する" class="button"><br><br>
<input type="button" value="やめる" onclick="history.back();" class="btn_mini">
</form>

<?php


}//履歴データが存在するかの判定おわり
?>
</br>
<br>

<a href = "question.php" class="button">問題表示画面に戻る</a>
</div>
</body>
</html>