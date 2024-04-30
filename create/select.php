<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<?php
session_start();

ini_set('display_errors',1);

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
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
	<title>出題問題一覧</title>
</head>

<body>
<div align="right">
アカウント：<b><?php echo $_SESSION["MemberName"];?></b>
－<a href="logout.php">ログアウト</a>－
</div>
<div align="center">
	<FONT size="6">出題問題一覧</FONT>
	</br>
	
<?php
require "dbc.php";

$sql_teacher = "select TID from teacher 
        WHERE Tname ='" .$_SESSION["MemberName"]."'";
$res = mysql_query($sql_teacher,$conn) or die("接続エラー");
$row = mysql_fetch_array($res,MYSQL_ASSOC);
$TID = $row["TID"];

$check = implode(",", $_POST["check"]);
//echo $check."<br>";

$sql_ins = "update set_question SET choose = '".$check."' where TID =" .$TID.";";
print "<br>";
//print $sql_ins;
if (!$res = mysql_query($sql_ins,$conn)) {
	echo "SQL実行時エラー" ;
	exit ;
}
//SQLを実行
//echo $sql_ins;

$choose_num = explode(",",$check);
$choose_count = count($choose_num);

for($i=0; $i<$choose_count; $i++){
	if($i==0){
	$term = "WID = ".$choose_num[$i];
	}else{
	$term = $term." or WID = ".$choose_num[$i];
	}
}

$sql = "select count(*) as cnt from question_info 
        WHERE $term;";

//}
//print $sql;
//echo "<br><br>";
$res = mysql_query($sql,$conn) or die("接続エラー");
$row = mysql_fetch_array($res);
$dtcnt = $row["cnt"];

$lim =100;//1画面に表示する問題数
$p = intval(@$_GET["p"]);
if ($p <1){
	$p = 1;
}

$st = ($p - 1)* $lim;

$prev = $p - 1;
if ($prev < 1 ) {
	$prev = 1;
}
$next = $p + 1;


//echo "<a href=\"new.php?mode=0\">問題新規登録</a>";
//echo"<br><br>";
//echo "<a href=\"allsearch.php\">詳細検索</a>";
//echo "<br><br>";
//echo " <a href=\"?term=1\">検索条件リセット</a>";
//echo "<br>";
//問題情報を取り出す

$sql = "SELECT * FROM question_info  
	WHERE  $term 
	ORDER BY WID 
	LIMIT $st, $lim;";

//print $sql;
$res = mysql_query($sql,$conn) or die("接続エラー");


$sql2 = "SELECT * FROM grammar";//文法項目の取得
$res2 = mysql_query($sql2,$conn) or die("接続エラー");
$hai=1;
while ($row2 = mysql_fetch_array($res2)){
$pro[$hai] = $row2["Item"];
$hai++;
}

//問題情報をテーブルで表示する
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>番号</td>";
//echo "<td>出題</td>";
echo "<td>日本文</td>";
echo "<td>英文</td>";
echo "<td>固定ラベル</td>";
echo "<td>難易度</td>";
echo "<td>文法項目</td>";
echo "<td>初期順序</td>";
echo "<td>作成者</td>";
echo "</tr>";
while ($row = mysql_fetch_array($res)){
	if($row["Fix"] == "-1"){
		$row["Fix"] = "なし";
	}
	
	if($row["level"] == "1"){
		$row["level"] = "初級";
	}else if($row["level"] == "2"){
		$row["level"] = "中級";
	}else if($row["level"] == "3"){
		$row["level"] = "上級";
	}
	
	for($j=24 ; $j>0; $j--){
		$row["grammar"] = str_replace($j,$pro[$j],$row["grammar"]);
	}
	echo "<tr>";
	echo "<td>" .$row["WID"]."</td>";
	//echo "<td>"
?>
<form method="post" action="question.php">
<!--
<input type="checkbox" name="check[]" value="<?php echo $row["WID"]; ?>">
-->
<?php
//	"</td>";
    echo "<td>" .$row["Japanese"]."</td>";
    echo "<td>" .$row["Sentence"]."</td>";
    echo "<td>" .$row["Fix"]."</td>";
    echo "<td>" .$row["level"]."</td>";
    echo "<td>" .$row["grammar"]."</td>";
    echo "<td>" .$row["start"]."</td>";
    echo "<td>" .$row["author"]."</td>";
    echo "</tr>";
}
echo "</table>";

if($p >1){
	echo " <a href=\"".$_SERVER["PHP_SELF"]."?p=$prev\">
		前のページ</a>";
}
if (($next - 1) * $lim < $dtcnt){
	echo " <a href=\"".$_SERVER["PHP_SELF"]."?p=$next\">
		次のページ</a>";
}
mysql_close($conn);
?>
</br>
<input type="submit" value="問題リスト表示に戻る" class="button"/>
<br>
</form>


</br>
<a href = "question.php" class="btn_mini">先頭ページに戻る</a>
</br>
<br>

</br>
</br></br></br></br>
</br></br>

<?php
//print $_POST["Japanese"];
?>
<p>参照：<a href='http://veerle.duoh.com/blog/comments/a_css_styled_table/'>A CSS styled table</a></p>
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

</div>
</body>
</html>
