<?php
session_start();
$_SESSION = array();
//session_destroy();
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
<title>履歴分析ログインページ</title>
</head>
<body>
<span style="line-height:20px"> 
<div align="center">
<img src="../mondai/image/logo.png">
<br>
<br>
ここは、<b>履歴分析のログインページ</b>です。<br>
<br>
<?php
  // $Agent = getenv( "HTTP_USER_AGENT" );
    /* 環境変数 HTTP_USER_AGENT を見て、正規表現のマッチングをする(ereg)。*/
  // if(( ereg( "Firefox", $Agent ) ) or ( ereg( "Safari", $Agent ) )or(ereg("Opera",$Agent))) {//ブラウザがfirefoxかサファリ（なぜかクロームが（
    require "dbc.php";

   

//idとpassの検証
//テキストボックスに入力されたデータを受け取る
if(isset($_POST["idtxt"]) && isset($_POST["passtxt"])){
	$id = @$_POST["idtxt"];
	$pass = @$_POST["passtxt"];

	echo "入力id：{$id}<br>";
	echo "パス：{$pass}<br>";
    //echo "conn:{"."$conn"."}<br>";

	$_SESSION["URL"]="./";
	//$_SESSION["URL"]="http://localhost/";
	//データを取り出す
	$sql = "SELECT TID FROM teacher WHERE (Tname = '".$id."' && Pass = '".$pass."')";
         echo "sql:".$sql."<br>";
	$res = mysqli_query($conn, $sql);
      echo mysqli_errno($conn).": ".mysqli_error($conn)."<br>";
    // echo "res:".$res."<br>";
	$count = mysqli_num_rows($res);
	/*
	$lsql = "SELECT SLimit FROM Level,member WHERE (member.Name = '".$id."' && Level.GID=member.GID)";
	$resl=mysql_query($lsql, $conn) or die("Level抽出エラー");
	$_SESSION["Level"]=mysql_result($resl,0);
	*/
	//データが抽出できたときはログイン完了
	if($count > 0){
		$row = mysqli_fetch_array($res);
		$_SESSION["MemberID"] = $row['UID'];
		//echo $_SESSION["MemberID"];
		//if(!isset($_SESSION["name"])){
		$_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
		//echo "<p>".$_SESSION["MemberName"]."さん ようこそ";
		header("location: ./main.php");//本当のサーバー用
		//require "LineQuesForm.php";//ローカル用
		//header("location: http://localhost/LineQuesForm.php");//
		$_SESSION["manager"] = "0";
		//}
	}else{
			$sql = "SELECT MID FROM manager WHERE (Mname = '".$id."' && Pass = '".$pass."')";
			$res = mysqli_query($conn,$sql) or die("Member抽出エラー");
			$count = mysqli_num_rows($res);
		if($count > 0){
		$row = mysqli_fetch_array($res);
		$_SESSION["MemberID"] = $row['MID'];
		//echo $_SESSION["MemberID"];
		//if(!isset($_SESSION["name"])){
		$_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
		//echo "<p>".$_SESSION["MemberName"]."さん ようこそ";
		header("location: ./main.php");//本当のサーバー用
		//require "LineQuesForm.php";//ローカル用
		//header("location: http://localhost/LineQuesForm.php");//
		$_SESSION["manager"] = "1";
		
		}else{
		echo "<p> ID[ $id ]が存在しないか、IDとパスワードの組み合わせが不正です。<br>";
		}
	}
}else{
	echo"ユーザーIDとパスワードを入力してログインしてください。<br>";
}

//データを取り出す
/*
$sql = "SELECT UID, Name, Pass FROM member ORDER BY UID";
$res = mysql_query($sql, $conn) or die("データ抽出エラー");
*/

mysqli_close($conn);

?>
<br>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
<table>
<tr>
<td>ユーザーID</td>
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
</body>
</html>