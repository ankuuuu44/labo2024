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
<head><title>並べ替え問題のページ</title>
<link rel="stylesheet" href="../StyleSheet.css" type="text/css" /> 
</head>
<script type="text/javascript">
<!--
function openwin(Qid) {
	//window.open("http://localhost/ques.php", "new", "width=861,height=513,resizable=0,menubar=0");
	window.open("ques.php?Qid="+Qid, "new", "width=861,height=700,resizable=0,menubar=0");
}

//function checkBrowser(){
var a = window.navigator.userAgent.toLowerCase();
//document.writeln("browser:" + a);
if(a.indexOf("msie") > -1){    ;}
else if(a.indexOf("chrome") > -1){   ;}
else if(a.indexOf("firefox") > -1){   alert("お使いのブラウザでは使用できません");  history.back();}
else if(a.indexOf("safari") > -1){    alert("お使いのブラウザでは使用できません");  history.back();}
else if(a.indexOf("opera") > -1){    ;  }


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
<a href="javascript:openwin(0);" class="btn_yellow" style="width: 200px;height: 54px;">並べ替え問題　1～30問</a></br>
</br>

<!--
</br>
<a href="javascript:openwin(1);" class="btn_yellow" style="width: 200px;height: 54px;">課題1　31～55問</a></br>
</br>
</br>
<a href="javascript:openwin(2);" class="btn_yellow" style="width: 200px;height: 54px;">課題2　56～80問</a></br>
</br>
</br>
<a href="javascript:openwin(3);" class="btn_yellow" style="width: 200px;height: 54px;">課題3　81～105問</a></br>
</br>
</br>

-->

<!--この部分のコメントアウトはこのままコメントアウトしておく<a href="attention/attention.html" class="button">説明書</a>-->
<!--
/*<a href="javascript:openwin2();">説明書</a>*/ここまで
-->

</br>
</br>
<!--
<a href="javascript:openwin3();">マウス検査</a></br>
-->
</br>
<!--/*<a href="javascript:openwintes();">テスト</a></br>*/-->


</center>
<br>
<br>

</body>
</html>
