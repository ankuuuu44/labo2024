<?php
session_start();
$_SESSION = array();
//session_destroy();
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>問題ログインページ</title>
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
</head>


<?php
//学内アクセスかどうか
// if(substr($_SERVER['REMOTE_ADDR'],0,7)=="133.70.") {
?> 

<body>
<span style="line-height:20px"> 
<div align="center">
<img src="image/logo.png">
<br><br>
ここは、<b>英語並べ替え問題のログインページ</b>です。<br>
<br>
aaaa
<?php

require "dbc.php";

//idとpassの検証
//テキストボックスに入力されたデータを受け取る
if(isset($_POST["idtxt"]) && isset($_POST["passtxt"])){
	$id = @$_POST["idtxt"];
	$pass = @$_POST["passtxt"];

	echo "入力Name：{$id}<br>";
//	echo "パス：{$pass}<br>";

	$_SESSION["URL"]="./";
	//データを取り出す
	$sql = "SELECT UID FROM member WHERE (Name = '".$id."' && Pass = '".$pass."')";
	$res = mysql_query($sql, $conn) or die("Member抽出エラー");
	$count = mysql_num_rows($res);

	//データが抽出できたときはログイン完了
	if($count > 0){
		$row = mysql_fetch_array($res);
		$_SESSION["MemberID"] = $row['UID'];
		$_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
        header("location: LineQuesForm.php");//本当のサーバー用
	}else{
		echo "<p> ID[ $id ]が存在しないか、ユーザ名とパスワードの組み合わせが不正です。<br>";
	}
}else{
	echo"ユーザ名とパスワードを入力してログインしてください。<br>";
    echo"新しくユーザを登録したい方は下のボタンをクリックしてください。<br><br>";

}
mysql_close($conn);
?>
<br>
<a class="btn_yellow" href="./register/register.html">ユーザ新規登録</a><br><br><br><br>
<table style="border:3px dotted red;" cellpadding="5"><tr><td>
<font size = 2>
<b>*ゲストログイン</b>： [ Name = guest , PASSWORD = guest]<br>
</font>
</td></tr></table><br>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>">
<table>
<tr>

<td>ユーザ名</td>
<td><input type="text" name="idtxt" class="input"></td>
</tr>
<tr>
<td>パスワード</td>
<td><input type="password" name="passtxt" class="input">
<php?
$idtxt=mb_convert_encoding($idtxt,"UTF-8","sjis");
?>
</td>
</tr>

<tr>
<td colspan="2" align="center">
<input type="submit" name="b1" value="OK" class="button"></td>
</tr>
</table>
</form>
<br>
<br>
<a class="btn_mini" href="../main.html">戻る</a>

<br>
</div>
<br>
<br>
<br>
</span>
</div>
</body>
<?php
// }
// else
// {
// echo "学内からアクセスしてください";
// }
?>
</html>