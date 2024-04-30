<?php
session_start()
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
<title>学習支援ログ分析ページ</title>
</head>
<body>
<span style="line-height:20px">         <!--行間隔指定-->
<div align="center">                    <!--</div>まで文字を中央に指定-->
<img src="../mondai/image/logo.png">    <!-- 背景-->
<br>
<br>
ここは、<b>学習支援ログ分析ページ</b>です。<br> <!--太文字-->
<br>
<?php
    require "dbc.php";

   

//idとpassの検証
//テキストボックスに入力されたデータを受け取る
if(isset($_POST["idtxt"]) && isset($_POST["passtxt"]) && !empty($_POST["idtxt"]) && !empty($_POST["passtxt"])){
	$id = @$_POST["idtxt"];
	$pass = @$_POST["passtxt"];

	echo "入力id：{$id}<br>";
	echo "パス：{$pass}<br>";

	$_SESSION["URL"]="./";
	//$_SESSION["URL"]="http://localhost/";
	//データを取り出す
	$sqlteacher = "SELECT TID FROM teacher WHERE (Tname = '".$id."' && Pass = '".$pass."' )";
    //echo "$sqlteacher<br>";

    $sqlstu = "SELECT UID FROM member WHERE (Name = '".$id."' && Pass = '".$pass."')";
    //echo $sqlstu;
	$resteach = mysqli_query($conn,$sqlteacher);
    $numteach = mysqli_num_rows($resteach);
    
    $resstu   = mysqli_query($conn,$sqlstu);
    $numstu = mysqli_num_rows($resstu);

    echo "numteach:$numteach<br>numstu:$numstu<br>";
    
	

    if ($numteach > 0){
        $rowteach = mysqli_fetch_array($resteach);
        $_SESSION["MemberID"] = $rowteach['TID'];
        $_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
        echo "上のif文に入りました";
        //echo "$resteach<br>";
        //header("location: ./teacher.php");
        header("location: ./main.php");
    }else if($numstu > 0){
        $rowstu = mysqli_fetch_array($resstu);
        $_SESSION["MemberID"] = $rowstu['UID'];
        $_SESSION["MemberName"] = $id;
		//現在時刻の取得
		$AccessDate = date('Y-m-d H:i:s');
		$_SESSION["AccessDate"] = $AccessDate;
        echo "下のif文に入りました<br>";
        echo "MemberID:{$_SESSION["MemberID"]}";
        header("location: ./student.php");
    }else{
		echo "<p> ID[ $id ]が存在しないか、IDとパスワードの組み合わせが不正です。<br>";
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