<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
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
	<title>区切り決定</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
</head>

<body>
<div align="center">
	<FONT size="6">区切り決定</FONT>
	</br>
<?php
session_start();
require "dbc.php";
$Japanese = $_SESSION["Japanese"];
$Sentence = $_SESSION["Sentence"];
$divide2 =$_SESSION["divide2"];
$fix = $_SESSION["fix"];
$fixlabel = $_SESSION["fixlabel"];
$num = $_SESSION["num"];
echo "<br>";

?>

<font size = 4>
<b>以下の条件で検索を行います。</br></br></b>
</font>

<?php
$radio = $_REQUEST["radiobutton"];//Japaneseファイルのradioボタン取得
	
$sql = "select Item from grammar
        WHERE GID>=4 ORDER BY GID;";
$PID = 4;
$res = mysql_query($sql,$conn) or die("接続エラー");
while ($row = mysql_fetch_array($res)){
	}

$pro ="#";//文法項目記録用
//$radio = $_POST["radio"];//検索モード記録用
$level = $_POST["level"];//難易度記録用
if(isset($_POST["check"])){
$check = $_POST["check"];
}
$g_count =count($check);//文法項目数

$j=0;
$k=1;
for ($i = 4; $i < $num+4; $i++){
	if($check[$j] == $i){
		$pro = $pro.$i."#";
		if($radio == "all"){
		$prosql = $prosql."and grammar like '%#".$i."#%'";
		}else if($radio =="part"){
			if($k==1){
				$prosql = $prosql."and (grammar like '%#".$i."#%'";
			}else if($k==$g_count){
				$prosql = $prosql."or grammar like '%#".$i."#%')";
			}else{
				$prosql = $prosql."or grammar like '%#".$i."#%'";
			}
			$k++;
		}
		$j++;
	}
	//echo "i:".$i." j:".$j." k:".$k."<br>";
}
if($k==2) $prosql=$prosql.")";
//$prosql = $prosql." and Property like '%#".$_SESSION["level"]."#%'";
//echo $prosql;
$_SESSION["level"] = $level;
$_SESSION["pro"] = $pro;
?>

	
<b>文法項目</b>：<?php echo $pro; ?></br>
<?php
$prolevel ="#".$level.$pro;
?>

<form method="post" action="question.php">
<input type="hidden" name="prosql" value="<?php echo $prosql; ?>" />
<input type="submit" value="決定" class="button"/><br><br>
<input type="button" value="1つ前に戻る" onclick="history.back();" class="button">
</form>


</div>
</body>
</html>
