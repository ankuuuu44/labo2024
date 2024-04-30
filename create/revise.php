<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
ini_set('display_errors',1);

if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
	require"notlogin.html";
	session_destroy();
	exit;
}

$_SESSION["mode"] = $_GET["mode"];
//echo $_SESSION["mode"];
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>問題情報修正</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
<style type="text/css">
#mytable {
    width:700px;
    margin:0 0 0 1px; padding:0;
    border:0;
    border-spacing:0;
    border-collapse:collapse;
}
caption {
    padding:0 0 5px 0;
    font:italic 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
    text-align:right;
}
th {
    font:bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
    color:#4f6b72;
    border:1px solid #c1dad7;
    letter-spacing:2px;
    text-transform:uppercase;
    text-align:left;
    padding:6px 6px 6px 12px;
    background:#cae8ea url("img/css/bg_header.jpg") no-repeat;
}
th.nobg {
    border-top:0;
    border-left:0;
    background:none;
}
td {
    border:1px solid #c1dad7;
    background:#fff;
    padding:6px 6px 6px 12px;
    color:#4f6b72;
}
td.alt {
    background:#F5FAFA;
    color:#797268;
}
th.spec {
    background:#fff url("img/css/bullet1.gif") no-repeat;
    font:bold 10px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
}
th.specalt {
    background:#f5fafa url("img/css/bullet2.gif") no-repeat;
    font:bold 10px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
    color:#797268;
}
</style>
</head>

<body>
<div align="center">
	<FONT size="6">問題情報表示・修正</FONT>
	</br>
<?php
// session_start();
require "dbc.php";
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$divide2 = $_SESSION["divide2"];
echo "<br>";

$WID= $_GET["WID"];

$sql_cnt = "SELECT COUNT(*) as CNT FROM question_info;";
$res_cnt = mysql_query($sql_cnt,$conn) or die("接続エラー");
$row_cnt =mysql_fetch_assoc($res_cnt);


$sql_data = "SELECT COUNT(*) as CNT FROM linedata WHERE WID=$WID;";
$res_data = mysql_query($sql_data,$conn) or die("接続エラー");
$row_data =mysql_fetch_assoc($res_data);

//echo "件数：{$row_data['CNT']}";

if($_SESSION["mode"] ==1 && $_SESSION["manager"] == "0"){
	if($row_data['CNT'] >0){
		echo "履歴データがすでにあるので問題をコピーして作成します。";
		$_SESSION["mode"] = 0;//insertもーどに変更。
		$_SESSION["dtcnt"] = $row_cnt['CNT'];
	}else{
		echo "問題の編集を開始します。";
		$_SESSION["dtcnt"] = $WID;
	}
}else if($_SESSION["manager"] == "1"){
		if($row_data['CNT'] >0){
		echo "履歴データがすでにあるので問題をコピーして作成します。";
		$_SESSION["mode"] = 0;//insertもーどに変更。
		$_SESSION["dtcnt"] = $row_cnt['CNT'];
	}else{
		echo "問題の編集を開始します。";
		$_SESSION["dtcnt"] = $WID;
	}
}else{
	echo "作成者が異なるので新規問題として作成します。";
	$_SESSION["dtcnt"] = $row_cnt['CNT'];
}
$sql = "SELECT * FROM question_info WHERE WID=$WID;";

$res = mysql_query($sql,$conn) or die("接続エラー");

while ($row = mysql_fetch_array($res,MYSQL_ASSOC)){

    ?>
    <table style="border:3px dotted red;" cellpadding="5"><tr><td>
<font size = 5>
<b>問題番号</b>：<?php echo $row["WID"]; ?></br>
<b>日本文</b>：<?php echo $row["Japanese"]; ?></br>
<b>問題文</b>：<?php echo $row["divide"]; ?></br>
<b>初期順序</b>：<?php echo $row["start"]; ?></br>
<b>固定ラベル</b>：<?php echo $row["Fix"]; ?></br>
<b>文法項目</b>：<?php echo $row["grammar"]; ?></br>
</font>
</td></tr></table><br>
<br><br>
<font size = 4>
部分点情報
<br>
</font>
<?php
$_SESSION["Japanese"] = $row["Japanese"];
$_SESSION["Sentence"] = $row["Sentence"];
$_SESSION["divide2"] = $row["divide"];
$_SESSION["start"] = $row["start"];
$_SESSION["Fix"] = $row["Fix"];
$WID = $row["WID"];
$_SESSION["WID"] = $WID;
}
echo "</table>";


$sql_part = "select * from partans WHERE WID=".$WID;
$res_part = mysql_query($sql_part,$conn) or die("接続エラー");

echo "<table border=\"1\">";
    echo "<tr>";
    echo "<td>部分点フレーズ</td>";
    echo "<td>形式</td>";
    echo "<td>部分点フレーズ2</td>";
    echo "<td>形式2</td>";
    echo "<td>点数</td>";
    echo "<td></td>";
    echo "</tr>";
while($row_part = mysql_fetch_array($res_part)){
    echo "<tr>";
    echo "<td>".$row_part["PartSentence"]."</td>";
    if($row_part["type"] ==0){
        echo "<td>全文</td>";
    }else if($row_part["type"] ==1){
        echo "<td>部分（文中）</td>";
    }else if($row_part["type"] ==2){
        echo "<td>部分（文頭）</td>";
    }else if($row_part["type"] ==3){
        echo "<td>部分（文末）</td>";
    }
    echo "<td>".$row_part["PartSentence2"]."</td>";
    if($row_part["type2"] ==0){
        echo "<td>全文</td>";
    }else if($row_part["type2"] ==1){
        echo "<td>部分（文中）</td>";
    }else if($row_part["type2"] ==2){
        echo "<td>部分（文頭）</td>";
    }else if($row_part["type2"] ==3){
        echo "<td>部分（文末）</td>";
    }else{
        echo "<td>-1</td>";
    }
    echo "<td>".$row_part["Point"]."</td>";
    echo "<td><a href=\"delete_part.php?WID=".$row_part['WID']."&type=".$row_part['type']."&Point=".$row_part['Point']."&PartSentence=".$row_part['PartSentence']."\">削除</td>";
    echo "</tr>"; 
}
echo "</table>";
?>
<br>
<form action = "part_select.php" method="post">
    <input type="submit" name="part" value="部分一致" class="btn_mini">
    <input type="submit" name="all" value="全文一致" class="btn_mini">
    <input type="submit" name="another" value="別解" class="btn_mini">
</form>
<br>

<font size = 4>
<b>修正を始める箇所を選択してください。<br><br><br></b>
</font>




<?php
echo "<a href=\"new.php\">問題修正</a>";
echo "＞";
echo "<a href=\"divide.php\">区切り決定</a>";
echo "＞";
echo "<a href=\"start.php\">初期順序決定</a>";
echo "＞";
echo "<a href=\"fix.php\">固定ラベル決定</a>";
echo "＞登録<br><br>";
echo "<a href=\"property.php\" class=\"button\">難易度・文法項目編集</a>";
?>
</br>
<br>
<form action = "stop.php" method="post">
<input type="submit" name="exe" value="修正を中止する" class="btn_mini">
</form>

</div>
</body>
</html>