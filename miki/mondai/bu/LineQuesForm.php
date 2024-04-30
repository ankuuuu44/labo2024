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
	//window.open("".$_SESSION["URL"]."ques.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/ques.php", "new", "width=861,height=513,resizable=0,menubar=0");
}

/*function openwin2() {
	//window.open("".$_SESSION["URL"]."readme.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/readme.php", "new", "width=861,height=513,resizable=0,menubar=0");
}*/
function openwin3() {
	//window.open("".$_SESSION["URL"]."exam.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/exam/exam.php", "new", "width=861,height=513,resizable=0,menubar=0");
	//window.open("http://localhost/exam/exam.php", "new", "width=861,height=513,resizable=0,menubar=0");
}
// -->
</script>
<body background="image/checkgreen.jpg">
<div align="right">
－<a href="logout.php">ログアウト</a>－
</div>
<center>
<div align="center"><img src="image/swaptop.jpg" width="300" height="200"></div>
</br>
</br>
<a href="javascript:openwin();">並び替え問題</a></br>
</br>
</br>
<a href="http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/attention0.html">説明書</a>
<!--
/*<a href="javascript:openwin2();">説明書</a>*/
-->

</br>
</br>
<a href="javascript:openwin3();">マウス検査</a>

<!--<iframe src="ques.php" name="ques" width="861" height="513" border="3" bordercolor ="yellow" >
</iframe>-->
<br>
<?php
//echo exec("http://lmo.cs.inf.shizuoka.ac.jp/~tsukuda/test/uegakipro/narabikae/bin/Debug/narabikae.exe");
?>

</center>
<br>
<br>

</body>
</html>