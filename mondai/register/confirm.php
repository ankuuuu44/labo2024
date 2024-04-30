<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>内容確認</title>
    <link rel="stylesheet" href="../../StyleSheet.css" type="text/css" /> 
</head>

<body>
<div align="center">
<br><br><br>
<img src="image/logo_register.png"><br>
<br>
<?php

require "../dbc.php";

//文字コード指定
$sql = "SET NAMES utf8";

$uid = $_POST['uid'];
$Name = $_POST['Name'];
$Pass = $_POST['Pass'];
$Pass2 = $_POST['Pass2'];

//Name検索用
$sql_search = 'select * from member where Name = "'.$Name.'";';
$rs = mysql_query($sql_search, $conn);
$row = mysql_num_rows($rs);

//エラー処理のためのフラグ
$flag = array();
$flagmessage = array("<b>※その名前は既に登録されています</b><br><br>","<b>※名前が入力されていません</b><br><br>","<b>※名前が半角英数字で入力されていません</b><br><br>","<b>※Passは6～20文字の範囲で入力してください</b><br><br>","<b>※Passが一致していません</b><br><br>","<b>※そのメールアドレスは使用できません</b><br><br>");
$i = 0;
$error = 0;


//Nameが既に登録されている
if($row != 0){$flag[0] = 1;}

//Nameの文字数が0
if(mb_strlen($Name) == 0){$flag[1] = 1;}

//Nameが半角英数字以外
if(!ctype_alnum($Name)){$flag[2] = 1;}

//Passの文字数が規定外
if(mb_strlen($Pass)<6 || 20<mb_strlen($Pass)){$flag[3] = 1;}

//Passが一致していない
if($Pass != $Pass2){$flag[4] = 1;}


//エラーメッセージ出力
for($i=0; $i<=5; $i++){
	if($flag[$i] == 1){$error = 1; echo $flagmessage[$i];}
}
//エラーが出ていたら戻るボタン表示
if($error == 1){echo '<a href="register.html"class="btn_mini">戻る</a>';}

if($error == 0){
	//確認画面
	echo "<b>**以下の内容で登録します**</b><br><br>";
	echo "<b>uid</b>：".$uid."<br>";
	echo "<b>Name</b>：".$Name."<br>";
	echo "<b>Pass</b>：".$Pass."<br>";

?>

<form method="post" action="register.php">
<input type="hidden" name="uid" value="<?php echo $uid; ?>" />
<input type="hidden" name="Name" value="<?php echo $Name; ?>" />
<input type="hidden" name="Pass" value="<?php echo $Pass; ?>" />
<input type="submit" value="登録" class="button"/><br><br>
<input type="button" value="1つ前に戻る" onclick="history.back();"class="btn_mini">
</form>
	
<?php
	
}

?>

</div>
</body>
</html>