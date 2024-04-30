<?php
session_start();
$_SESSION = array();
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>問題作成支援ログインページ</title>
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
</head>
<body>
<span style="line-height:20px"> 
<div align="center">
<img src="../mondai/image/logo.png">
<br>
<br>
ここは、<b>問題作成支援用ページのログインページ</b>です。<br>
<br>

<?php

require "dbc.php";

//idとpassの検証
//テキストボックスに入力されたデータを受け取る
if(isset($_POST["idtxt"]) && isset($_POST["passtxt"])){
	$id = @$_POST["idtxt"];
	$pass = @$_POST["passtxt"];

	echo "入力UserName：{$id}<br>";
	//echo "パス：{$pass}<br>";

	$_SESSION["URL"]="./";
	//データを取り出す
	$sql = "SELECT TID FROM teacher WHERE (Tname = '".$id."' && Pass = '".$pass."')";
    echo $sql;
	$res = mysql_query($sql, $conn) or die("Member抽出エラー");
    echo $res;
    $count = mysql_num_rows($res);

	//データが抽出できたときはログイン完了
	if($count > 0){
		$row = mysql_fetch_array($res);
		$_SESSION["MemberID"] = $row['UID'];
		$_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
		header("location: ./question.php");//本当のサーバー用
		$_SESSION["manager"] = "0";
	}else{
    	$sql = "SELECT MID FROM manager WHERE (Mname = '".$id."' && Pass = '".$pass."')";
		$res = mysql_query($sql, $conn) or die("Member抽出エラー");
		$count = mysql_num_rows($res);
		if($count > 0){
	    	$row = mysql_fetch_array($res);
		    $_SESSION["MemberID"] = $row['MID'];
		    $_SESSION["MemberName"] = $id;
	    	//現在時刻の取得
		    $AccessDate = date('Y-m-d H:i:s');
		    $_SESSION["AccessDate"] = $AccessDate;
		    header("location: ./question.php");//本当のサーバー用
		    $_SESSION["manager"] = "1";	
		}else{
		    echo "<p> ID[ $id ]が存在しないか、ユーザ名とパスワードの組み合わせが不正です。<br>";
		}
	}
}else{
	echo"ユーザー名とパスワードを入力してログインしてください。<br>";
}

mysql_close($conn);
?>
<br>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<table>
<tr>

<td>ユーザー名</td>
<td><input type="text" name="idtxt" class="input"></td>
</tr>
<tr>
<td>パスワード</td>
<td><input type="password" name="passtxt" class="input">
<?php
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
</body>
</html>