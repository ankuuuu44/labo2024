<?php
session_start();
if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
	require"notlogin";
	session_destroy();
	exit;
}
$_SESSION["examflag"] = 0;
?>
<html>
<head><title>並び替え問題のページ</title></head>
<script type="text/javascript">
<!--
function openwin() {
	//window.open("http://localhost/ques.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("ques.php", "new", "width=861,height=700,resizable=0,menubar=0");
}

//function checkBrowser(){
var a = window.navigator.userAgent.toLowerCase();
if(a.indexOf("msie") > -1){    ;}
else if(a.indexOf("chrome") > -1){   ;}
else if(a.indexOf("firefox") > -1){   alert("お使いのブラウザでは使用できません");  history.back();}
else if(a.indexOf("safari") > -1){    alert("お使いのブラウザでは使用できません");  history.back();}
else if(a.indexOf("opera") > -1){    ;  }
else{    alert("お使いのブラウザでは使用できません");  history.back();}//}

/*function openwin2() {
	//window.open("".$_SESSION["URL"]."readme.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/readme.php", "new", "width=861,height=513,resizable=0,menubar=0");
}*/
function openwin3() {
	//window.open("".$_SESSION["URL"]."exam.php", "new", "width=861,height=513,resizable=0,menubar=0");
	//window.open("/exam/exam.php", "new", "width=861,height=513,resizable=0,menubar=0");
	//window.open("http://localhost/exam/exam.php", "new", "width=861,height=513,resizable=0,menubar=0");
}
/*
function openwintes(){
	//window.open("http://localhost/select.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/select.php", "new", "width=861,height=513,resizable=0,menubar=0");
}


*/
// -->
</script>
<body background="image/checkgreen.jpg">
<div align="right">
－<a href="logout.php">ログアウト</a>－
</div>
<center>
<div align="center"><img src="image/logo.png"></div>
</br>
</br>
<a href="javascript:openwin();">並び替え問題を始める</a></br>
</br>
</br>
<a href="attention/attention.html">説明書</a>
<!--
/*<a href="javascript:openwin2();">説明書</a>*/
-->

</br>
</br>
<!--
<a href="javascript:openwin3();">マウス検査</a></br>
-->
</br>
<!--/*<a href="javascript:openwintes();">テスト</a></br>*/-->
<?php
//echo exec("http://lmo.cs.inf.shizuoka.ac.jp/~tsukuda/test/uegakipro/narabikae/bin/Debug/narabikae.exe");
?>

<?php
//データベースに接続
$sv = "192.168.11.2";//研究室ローカル
$user = "maintainer"; //ドメイン名
$pass = "maintainer987654";//パスワード
$dbname ="rireki201310";//データベース名

$conn = mysql_connect($sv,$user,$pass) or die("接続エラー1");

//データベース選択
mysql_select_db($dbname) or die("接続エラー2");
mysql_query($sql,$conn);

//文字コード指定
$sql = "SET NAMES utf8";

//GID検索用
$sql_search = 'select GID from member where Name = "'.$_SESSION["MemberName"].'";';
$rs = mysql_query($sql_search, $conn);

$GID = array();

while($row = mysql_fetch_array($rs)) {
     $GID = $row['GID'];
}

if($GID == 0){echo "<a href=\"register/register.html\">ユーザー新規登録</a>";}

?>

</center>
<br>
<br>

</body>
</html>