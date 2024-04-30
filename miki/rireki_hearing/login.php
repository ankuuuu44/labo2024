<?php
session_start();
$_SESSION = array();
//session_destroy();
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>履歴分析ログインページ</title></head>
<body>
<span style="line-height:20px"> 
<div align="center">
</br>
</br>
<div align="center">
ここは、<b>履歴分析のログインページ</b>です。<br>
<br>

</div>

<?php
   $Agent = getenv( "HTTP_USER_AGENT" );
    /* 環境変数 HTTP_USER_AGENT を見て、正規表現のマッチングをする(ereg)。*/
    if(( ereg( "Firefox", $Agent ) ) or ( ereg( "Safari", $Agent ) )or(ereg("Opera",$Agent))) {//ブラウザがfirefoxかサファリ（なぜかクロームが（




require "dbc.php";

//idとpassの検証
//テキストボックスに入力されたデータを受け取る
if(isset($_POST["idtxt"]) && isset($_POST["passtxt"])){
	$id = @$_POST["idtxt"];
	$pass = @$_POST["passtxt"];

	echo "入力id：{$id}<br>";
	//echo "パス：{$pass}<br>";

	$_SESSION["URL"]="http://lmo.cs.inf.shizuoka.ac.jp/~miki/create/";
	//$_SESSION["URL"]="http://localhost/";
	//データを取り出す
	$sql = "SELECT TID FROM teacher WHERE (Tname = '".$id."' && Pass = '".$pass."')";
	$res = mysql_query($sql, $conn) or die("Member抽出エラー");

	$count = mysql_num_rows($res);
	/*
	$lsql = "SELECT SLimit FROM Level,member WHERE (member.Name = '".$id."' && Level.GID=member.GID)";
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
		//echo "<p>".$_SESSION["MemberName"]."さん ようこそ";
		header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki_hearing/FirstForm.php");//本当のサーバー用
		//require "LineQuesForm.php";//ローカル用
		//header("location: http://localhost/LineQuesForm.php");//
		$_SESSION["manager"] = "0";
		//}
	}else{
			$sql = "SELECT MID FROM manager WHERE (Mname = '".$id."' && Pass = '".$pass."')";
			$res = mysql_query($sql, $conn) or die("Member抽出エラー");
			$count = mysql_num_rows($res);
		if($count > 0){
		$row = mysql_fetch_array($res);
		$_SESSION["MemberID"] = $row['MID'];
		//echo $_SESSION["MemberID"];
		//if(!isset($_SESSION["name"])){
		$_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
		//echo "<p>".$_SESSION["MemberName"]."さん ようこそ";
		header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki_hearing/FirstForm.php");//本当のサーバー用
		//require "LineQuesForm.php";//ローカル用
		//header("location: http://localhost/LineQuesForm.php");//
		$_SESSION["manager"] = "1";
		
		}else{
			echo "<p> $id は存在しません、もしくはパスワードが間違っています";
		}
	}
}else{
	echo"ユーザーIDとパスワードを入力してログインしてください。</br>";
	echo "<b>※対応ブラウザはGoogle Chrome(推奨),Firefoxです</b><br>";
}

//データを取り出す
/*
$sql = "SELECT UID, Name, Pass FROM member ORDER BY UID";
$res = mysql_query($sql, $conn) or die("データ抽出エラー");
*/

mysql_close($conn);
?>
<br>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<table>
<tr>

<td>ユーザーID</td>
<td><input type="text" name="idtxt"></td>
</tr>
<tr>
<td>パスワード</td>
<td><input type="password" name="passtxt">
<php?
$idtxt=mb_convert_encoding($idtxt,"UTF-8","sjis");
?>
</td>
</tr>
<tr>
<td>

<input type="submit" name="b1" value="OK"></td>
</tr>
</table>
</form>

<!--
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>">
<iframe src="new.php" name="news" width="400" height="100" border="1" bordercolor ="yellow" bgcolor ="white" size="2"></iframe>
	</br>
</form>
-->
<?php
    } else {
      echo "<b>※対応ブラウザはGoogle Chrome(推奨),Firefoxです</b><br>";
      echo "<br><br><br>";
    }
?>
ご意見・ご質問等は、担当者：三木(gs12035☆s.inf.shizuoka.ac.jp)<br>
または、副担当者：出海(cs09055☆s.inf.shizuoka.ac.jp)まで。<br>
(☆を@に変換して下さい。)<br>

</br>
</div>
</br>
</br>
</br>
</span>
</body>
</html>