<?php
session_start();
$_SESSION = array();
session_destroy();
?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>ログインページ</title></head>
<body background="image/checkgreen.jpg">
<div align="center">
<div align="center"><img src="image/swaptop.jpg" width="300" height="200"></div>
</br>
</br>
<div align="center"><p>並び替え問題ログイン画面</p></div>

<?php

require "dbc.php";

//idとpassの検証
//テキストボックスに入力されたデータを受け取る
if(isset($_POST["idtxt"]) && isset($_POST["passtxt"])){
$id = @$_POST["idtxt"];
$pass = @$_POST["passtxt"];

echo "入力id：{$id}<br>";
//echo "パス：{$pass}<br>";

$_SESSION["URL"]="http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/";
//$_SESSION["URL"]="http://localhost/";
//データを取り出す
$sql = "SELECT UID FROM member WHERE (Name = '".$id."' && Pass = '".$pass."')";

$res = mysql_query($sql, $conn) or die("Member抽出エラー");

$count = mysql_num_rows($res);
/*
$lsql = "SELECT SLimit FROM Level,member WHERE (member.Name = '".$id."' && Level.SID=member.SID)";
$resl=mysql_query($lsql, $conn) or die("Level抽出エラー");
$_SESSION["Level"]=mysql_result($resl,0);
*/
//データが抽出できたときはログイン完了
if($count > 0){
	$row = mysql_fetch_array($res);
	$_SESSION["MemberID"] = $row['UID'];
	//echo $_SESSION["MemberID"];
	//if(!isset($_SESSION["name"])){
	$_SESSION["MemberName"] = $id;
	//現在時刻の取得
	$AccessDate = date('Y-m-d H:i:s');
	$_SESSION["AccessDate"] = $AccessDate;
	//echo "<p>".$_SESSION["MemberName"]."さん、ようこそ";
	header("location: http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/LineQuesForm.php");//本当のサーバー用
	//require "LineQuesForm.php";//ローカル用
	//}
}else{
	echo "<p> $id は存在しません、もしくはパスワードが間違っています";
	}
}else{
	echo"idとpassを入力してください(対応ブラウザはIE,Google Chrome,Operaです。ファイアフォックスなどは動かないですm(_ _)m)";
	}
//データを取り出す
/*
$sql = "SELECT UID, Name, Pass FROM member ORDER BY UID";
$res = mysql_query($sql, $conn) or die("データ抽出エラー");
*/

mysql_close($conn);
?>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>">
<iframe src="new.php" name="news" width="200" height="100" border="3" bordercolor ="yellow" size="2"></iframe>
	</br>
<table>
<tr>
<td>ユーザーID</td>
<td><input type="text" name="idtxt"></td>
</tr>
<tr>
<td>パスワード</td>
<td><input type="password" name="passtxt"></td>
</tr>
<tr>
<td><input type="submit" name="b1" value="OK"></td>
</tr>
</table>
</form>

<marquee bgcolor="#ff9999">部外者の方でやってみたい方は、id:guest pass:guest でログイン!</marquee>
</br>
</br>
<h2>只今、実験始動中（実験は5/17～7月初旬まで)</h2>
実験に協力してくれる方、大歓迎!
希望のユーザID、Pass(3～10文字)をcs07042@s.inf.shizuoka.ac.jpへご連絡ください!	
</br>
	</div>
</br>
</br>
</br>

</body>
</html>