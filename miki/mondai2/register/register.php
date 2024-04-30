<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>登録完了</title>
</head>

<body background="image/checkgreen.jpg">
<div align="center">
<br><br><br>
<img src="image/logo_register.png"><br>
<br>
<?php

//データベースに接続

$sv = "192.168.11.2";//研究室ローカル
$user = "maintainer"; //ドメイン名
$pass = "maintainer987654";//パスワード
$dbname ="melty0118";//データベース名

$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");

//文字コード指定
$sql = "SET NAMES utf8";

$uid = $_POST['uid'];
$Mail = $_POST['Mail'];
$Name = $_POST['Name'];
$Pass = $_POST['Pass'];
$GID = $_POST['GID'];

//データベース選択
mysql_select_db($dbname) or die("接続エラー2");
mysql_query($sql,$conn);

//フォームで送られてきたデータでINSERT文を作成
$sql_ins = "insert into member(uid, Name, Pass, GID, Mail) VALUES(".$uid.",'".$Name."', '".$Pass."', ".$GID.",'".$Mail."')";

//SQLを実行
if (!$res = mysql_query($sql_ins,$conn)) {
	echo "SQL実行時エラー" ;
	exit ;
}

//データベースから切断
mysql_close($conn) ;

//メッセージ出力
echo "登録完了しました<br><br>";
echo '<a href="register.html">続けて登録する</a><br>';
echo '<a href="../test.php">ログインページへ戻る</a>';

?>
</div>
</body>
</html>