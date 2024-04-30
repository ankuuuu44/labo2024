<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
/*
if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
	require"notlogin.html";
	session_destroy();
	exit;
}
*/

if($_POST["mark"] != $_SESSION["mark"]){//正誤表示、部分点表示の場合分け処理
    if(isset($_POST["mark"])){
        $_SESSION["mark"] = $_POST["mark"];
    }
}

?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>履歴分析（仮）</title>



<script type="text/javascript"><!--
function ChangeTab(tabname) {
   // 全部消す
   document.getElementById('tab1').style.display = 'none';
   document.getElementById('tab2').style.display = 'none';
   document.getElementById('tab3').style.display = 'none';
   // 指定箇所のみ表示
   document.getElementById(tabname).style.display = 'block';
}
// --></script>


<style type="text/css">
<!--
/* ▼(A)表示領域全体 */
div.tabbox { margin: 0px; padding: 0px; width: 650px; }

/* ▼(B)タブ部分 */
p.tabs { margin: 0px; padding: 0px; }
p.tabs a {
   /* ☆リンクをタブのように見せる */
   display: block; width: 5em; float: left;
   margin: 0px 1px 0px 0px; padding: 3px;
   text-align: center;
}
/* ◇各タブの配色 */
p.tabs a.tab1 { background-color: blue;  color: white; }
p.tabs a.tab2 { background-color: #aaaa00; color:white;}
p.tabs a.tab3 { background-color: red;   color: white; }
p.tabs a:hover { color: yellow; }

/* ▼(C)タブ中身のボックス */
div.tab {
   /* ☆ボックス共通の装飾 */
overflow: auto; clear: left;
}
/* ◇各ボックスの配色 */
div#tab1 { border: 4px solid blue; background-color: #ececec; }
div#tab2 { border: 4px solid #aaaa00; background-color: #ececec; }
div#tab3 { border: 4px solid red; background-color: #ececec; }
div.tab p { margin: 0.5em; }
</style>

</head>

<body>
<div align="center">




<script type="text/javascript" src="./prototype.js"></script>
<script type="text/javascript">
<!--



function dispData(msg){
	//document.getElementById('memberp').innerHTML = msg;
	var b = msg;
	var $a = 'id='+encodeURIComponent(b);

		//▲マウスデータの取得
	//ドラッグ開始地点の保存


	new Ajax.Request('http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki/search_info.php',
{
	method: 'post',
	onSuccess: getA,
	onFailure: getE,
	parameters: $a
});
	function getA(req){
		document.getElementById('memberp').innerHTML=req.responseText;
	}
	function getE(req){
		alert("学習者分析エラー");
	}
}


function dispQues(msg){
	//document.getElementById('memberp').innerHTML = msg;
	var b = msg;
	var $a = 'id='+encodeURIComponent(b);

		//▲マウスデータの取得
	//ドラッグ開始地点の保存


	new Ajax.Request('http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki/ques_info.php',
{
	method: 'post',
	onSuccess: getA,
	onFailure: getE,
	parameters: $a
});
	function getA(req){
		document.getElementById('questionq').innerHTML=req.responseText;
	}
	function getE(req){
		alert("問題分析エラー");
	}
}

// ツリーメニュー
flag = false;
function treeMenu(tName) {
  tMenu = document.all[tName].style;
  if(tMenu.display == 'none') tMenu.display = "block";
  else tMenu.display = "none";
}

function correct(){
	var $abc =2
}
// -->
</script>







<?php
$_SESSION["student"]="";
$_SESSION["question"]="";

$mode = $_POST["mode"];

//検索用要素
$maxcorrect_s = $_POST["maxcorrect_s"];//最小得点(学習者)
$mincorrect_s=  $_POST["mincorrect_s"];//最大得点(学習者)
$maxcorrect_q = $_POST["maxcorrect_q"];//最小得点(学習者)
$mincorrect_q=  $_POST["mincorrect_q"];//最大得点(学習者)
$maxtime_s = $_POST["maxtime_s"];//最小得点(履歴データ)
$mintime_s=  $_POST["mintime_s"];//最大得点(履歴データ)
$maxtime_q = $_POST["maxtime_q"];//最小得点(履歴データ)
$mintime_q=  $_POST["mintime_q"];//最大得点(履歴データ)
$maxtime_r = $_POST["maxtime_r"];//最小得点(履歴データ)
$mintime_r=  $_POST["mintime_r"];//最大得点(履歴データ)

$maxword = $_POST["maxword"];//最小得点(履歴データ)
$minword=  $_POST["minword"];//最大得点(履歴データ)
$maxpoint_s = $_POST["maxpoint_s"];
$minpoint_s = $_POST["minpoint_s"];
$maxpoint_r = $_POST["maxpoint_r"];
$minpoint_r = $_POST["minpoint_r"];
$level =$_POST["level"];
$grammar = $_POST["grammar"];
$sent =$_POST["sent"];//問題選択
$stu = $_POST["stu"];//学習者選択
$word = $_POST["word"];
$truefalse =$_REQUEST["truefalse"];//正誤(履歴データ)
$radio = $_REQUEST["radio"];
$radiobutton = $_REQUEST["radiobutton"];



require "dbc.php";
//履歴参照用
$aaa = $_REQUEST["studentlist"];
$bbb = $_REQUEST["queslist"];
//echo $abc."だよ";
if(isset($aaa)){
	$_SESSION["student"]= "and linedata.UID = '".$aaa."'";
}
if(isset($bbb)){
	$_SESSION["question"]= "and linedata.WID = '".$bbb."'";
}
$term = $_SESSION["student"]." ".$_SESSION["question"];


if(isset($maxcorrect_s)){
	if($maxcorrect_s != ""){
    $maxcorrect_s = $maxcorrect_s/100;
  	$term = $term." and sum(TF)/count(*) <= ".$maxcorrect_s." ";
	}
}

if(isset($mincorrect_s)){
	if($mincorrect_s != ""){
    $mincorrect_s = $mincorrect_s/100;
  	$term = $term." and sum(TF)/count(*) >= ".$mincorrect_s." ";
	}
}

if(isset($maxcorrect_q)){
    echo "dddddd";
	if($maxcorrect_q != ""){
    $maxcorrect_q = $maxcorrect_q/100;
    $term = $term." and (select sum(TF)/count(*) from linedata where question_info.WID = linedata.WID)<= ".$maxcorrect_q." ";
	}
}

if(isset($mincorrect_q)){
	if($mincorrect_q != ""){
    $mincorrect_q = $mincorrect_q/100;
    $term = $term." and (select sum(TF)/count(*) from linedata where question_info.WID = linedata.WID)>= ".$mincorrect_q." ";
	}
}

 if(isset($maxtime_s)){
	if($maxtime_s !="" ){
		$maxtime_s = $maxtime_s*1000;
		$term = $term." and AVG(linedata.Time) <= ".$maxtime_s." ";
	}
}
if(isset($mintime_s)){
	if($mintime_s !="" ){
		$mintime_s = $mintime_s*1000;
		$term = $term." and AVG(linedata.Time) >= ".$mintime_s." ";
	}
}
if(isset($maxtime_q)){
	if($maxtime_q !="" ){
	$maxtime_q = $maxtime_q*1000;
	$term = $term." and (select AVG(linedata.Time) from linedata where question_info.WID = linedata.WID)<= ".$maxtime_q." ";
	}
}
if(isset($mintime_q)){
	if($mintime_q != ""){
	$mintime_q = $mintime_q*1000;
	$term = $term." and (select AVG(linedata.Time) from linedata where question_info.WID = linedata.WID)>= ".$mintime_q." ";
	}
}
if(isset($maxtime_r)){
	if($maxtime_r !="" ){
	$maxtime_r = $maxtime_r*1000;
	$term = $term." and linedata.Time <= ".$maxtime_r." ";
	}
}
if(isset($mintime_r)){
	if($mintime_r != ""){
	$mintime_r = $mintime_r*1000;
	$term = $term." and linedata.Time  >= ".$mintime_r." ";
	}
}

if(isset($maxpoint_s)){//得点
	if($maxpoint_s != ""){
     	//$term = $term." and trackdata.Point <= ".$maxpoint_s." ";
        $term = $term." and AVG(trackdata.Point) <= ".$maxpoint_s." ";
	}
}



if(isset($maxpoint_r)){//得点
	if($maxpoint_r != ""){
     	$term = $term." and trackdata.Point <= ".$maxpoint_r." ";
	}
}
if(isset($minpoint_r)){//得点
	if($minpoint_r != ""){
     	$term = $term." and trackdata.Point >= ".$minpoint_r." ";
	}
}

if(isset($sent[0])){
    $level_c =0;
    foreach($_POST['sent'] as $val){
       if($level_c == 0){
           $term = " AND (WID = ".$val;
        }else{
            $term = $term." OR WID = ".$val;
        }
    $level_c++;
    }
    $term = $term." ) ";
}
if(isset($stu[0])){
    $level_c =0;
    foreach($_POST['stu'] as $val){
       if($level_c == 0){
           $term = " AND (UID = ".$val;
        }else{
            $term = $term." OR UID = ".$val;
        }
    $level_c++;
    }
    $term = $term." ) ";
}

if(isset($level[0])){
    $level_c =0;
    foreach($_POST['level'] as $val){
        if($level_c == 0){
            $term = " AND (level = ".$val;
        }else{
            $term = $term." OR level = ".$val;
        }
    $level_c++;
    }
    $term = $term." ) ";
}
if(isset($grammar[0])){//文法項目
        $grammar_c =0;
        echo $radio."おおお";
    foreach($_POST['grammar'] as $val){
        if($grammar_c == 0){
            $term = " AND (grammar like '%#".$val."#%'" ;
        }else{
            if($radiobutton == "AND"){
            $term = $term." AND grammar like '%#".$val."#%'" ;
            }else if($radiobutton == "OR"){
            $term = $term." OR grammar like '%#".$val."#%'" ;
            }
        }
    $grammar_c++;
    }
    $term = $term." ) ";
}
if(isset($word)){
    	$term = " and (Sentence like '% ".$word." %' 
		or Sentence like '".$word." %' 
		or Sentence like '% ".$word.".' 
	    or Sentence like '% ".$word."?')";
}


if(isset($truefalse)){
	$term = " and linedata.TF = ".$truefalse;
}
if(isset($confidence[0])){
    $term = $term." and linedata.Time <= ".$maxtime_r." ";
}

//問題の絞り込み用
if ($mode ==1){//学習者検索

    $sql = "select UID from linedata group by UID having AVG(linedata.time)>=0".$term."
	ORDER BY uid;";

$res = mysql_query($sql,$conn) or die("接続エラー");
$Count = 0;
 while($row = mysql_fetch_array($res)){
     $ID[$Count] = $row["UID"];
     $Count++;
 }
 //echo $Count;
    $sql2 = "select WID,Sentence from question_info where exists (select WID from linedata where linedata.WID = question_info.WID ";
    for($i = 0 ; $i<$Count ; $i++){
        if($i == 0){
            $search_ID = " and (linedata.UID = ".$ID[$i];
        }else{
            $search_ID = $search_ID." or linedata.UID = ".$ID[$i]; 
        }
        
    }
    if($Count != 0){
        $search_ID = $search_ID.")";
    }
    $sql2 = $sql2.$search_ID.") ORDER BY question_info.WID;";
    //echo $search_ID;

    $sql3 = "select UID,WID,Date,TF,Time from linedata
	where UID like '%%'".$search_ID. 
	" ORDER BY UID,AID";
   // echo $sql3;
   // echo $sql;
    //echo $sql2;
    /*
	$sql2 = "select WID,Sentence from question_info where exists (select WID from linedata where linedata.WID = question_info.WID".$term." )
	ORDER BY question_info.WID;";
	*/
}else if($mode == 2){//問題検索
	$sql2 = "select WID,Sentence from question_info where WID like '%%'".$term. 
	"ORDER BY question_info.WID;";
   
    $res2 = mysql_query($sql2,$conn) or die("接続エラー");
    $Count = 0;
    while($row2 = mysql_fetch_array($res2)){
         $ID[$Count] = $row2["WID"];
        $Count++;
    }

    $sql = "select Name, UID from member where exists (select uid from linedata where linedata.uid = member.uid ";
    for($i = 0 ; $i<$Count ; $i++){
        if($i == 0){
            $search_ID = " and (linedata.WID = ".$ID[$i];
        }else{
            $search_ID = $search_ID." or linedata.WID = ".$ID[$i]; 
        }
        
    }
    if($Count != 0){
        $search_ID = $search_ID.")";
    }
    $sql = $sql.$search_ID.") ORDER BY member.UID;";

    $sql3 = "select UID,WID,Date,TF,Time from linedata
	where UID like '%%'".$search_ID. 
	" ORDER BY UID,AID";
    //echo $search_ID;
    /*
    $sql3 = "select UID,WID,Date,TF,Time from linedata
	where UID like '%%' 
	ORDER BY UID,AID";
    $sql = "select Name, UID from member where exists (select uid from linedata where linedata.uid = member.uid)
	ORDER BY member.uid;";
    */
    //echo $sql;
}else if($mode ==3){//履歴データ検索

$sql3 = "select linedata.UID,linedata.WID,linedata.Date,linedata.TF,linedata.Time,trackdata.Point from linedata,trackdata 
where linedata.UID=trackdata.UID and linedata.AID=trackdata.AID " .$term.
" ORDER BY linedata.UID,linedata.AID";

$sql = "select Name, UID from member where exists (select linedata.uid from linedata,trackdata where linedata.uid = member.uid".$term." )
 ORDER BY member.uid;";
$sql2 = "select WID,Sentence from question_info where exists (select linedata.WID from linedata,trackdata where linedata.WID = question_info.WID".$term." )
 ORDER BY question_info.WID;";
}else{//デフォ
/*
	$sql3 = "select UID,WID,Date,TF,Time from linedata
	where UID like '%%'" .$term.
	" ORDER BY UID";
    */
    $sql3 = "select linedata.UID,linedata.WID,linedata.Date,linedata.TF,linedata.Time,trackdata.Point from linedata,trackdata 
where linedata.UID=trackdata.UID and linedata.AID=trackdata.AID " .$term.
" ORDER BY linedata.UID,linedata.AID";
	$sql = "select Name, UID from member where exists (select linedata.UID from linedata,trackdata where linedata.uid = member.uid".$term." )
	ORDER BY member.uid;";
    $sql2 = "select WID,Sentence from question_info where exists (select linedata.WID from linedata,trackdata where linedata.WID = question_info.WID".$term." )
	ORDER BY question_info.WID;";
    
    $q1 = "select count(*) from member where exists (select linedata.UID from linedata,trackdata where linedata.uid = member.uid".$term." )";
	$q2 = "select count(*) from question_info where exists (select linedata.WID from linedata,trackdata where linedata.WID = question_info.WID".$term." );";
    $q3 = "select count(*) from linedata,trackdata 
            where linedata.UID=trackdata.UID and linedata.AID=trackdata.AID " .$term;
$q1_c = mysql_query($q1);
$q2_c = mysql_query($q2);
$q3_c = mysql_query($q3);
$rowq1 = mysql_fetch_array($q1_c);
$rowq2 = mysql_fetch_array($q2_c);
$rowq3 = mysql_fetch_array($q3_c);
//echo $rowq1["count(*)"];
/*
	$sql = "select Name, UID from member where exists (select uid from linedata where linedata.uid = member.uid".$term." )
	ORDER BY member.uid;";
	$sql2 = "select WID,Sentence from question_info where exists (select WID from linedata where linedata.WID = question_info.WID".$term." )
	ORDER BY question_info.WID;";
    */
}

//echo $sql2;
?>
<font size="6">
■学習者：<?php echo $rowq1["count(*)"];?>人　
■問題：<?php echo $rowq2["count(*)"];?>問 
■履歴データ数：<?php echo $rowq3["count(*)"];?>問
</font>
</br>

<?php
if($_SESSION["mark"] =="part"){
?>
<form action = "main.php" method="post">

    <input type="hidden" name="mark" value="all">
    <input type="submit" value="正誤表示に変更する">
</form>
<?php

}else{ 
?>
<form action = "main.php" method="post">

    <input type="hidden" name="mark" value="part">
    <input type="submit" value="得点表示に変更する">
</form>
<?php
}
?>
<form action = "main.php" method="post">
<input type="submit" name="exe" value="リセット">
</form>
</br>
<div class="tabbox">
   <p class="tabs">
      <a href="#tab1" class="tab1" onclick="ChangeTab('tab1'); return false;">学習者</a>
      <a href="#tab2" class="tab2" onclick="ChangeTab('tab2'); return false;">問題</a>
      <a href="#tab3" class="tab3" onclick="ChangeTab('tab3'); return false;">（仮）</a>
   </p>
</br>
<div id ="tab1" class="tab">
    </br>
<?php


$res = mysql_query($sql,$conn) or die("接続エラー");

?>
    <div align="left">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<A href="javaScript:treeMenu('treeMenu1')">■ 学習者検索</a><br>
<DIV id="treeMenu1" style="display:none">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣ <A href="javaScript:treeMenu('treeMenu2')">・対象学習者選択</A><BR>
<DIV id="treeMenu2" style="display:none">

<form action = "main.php" method="post">
<font size= "1"></font>※対象学習者を選択してください。(複数チェック可)</br></br></font>

<?php
$student_sql = "select UID from member ORDER BY UID;";
$student_res = mysql_query($student_sql,$conn) or die("接続エラー");
$num = 0;
//問題情報をテーブルで表示する

while ($student_row = mysql_fetch_array($student_res)){

?>
<input type="checkbox" name="stu[]" value="<?php echo $student_row["UID"]; ?>"><?php echo $student_row["UID"]; ?>
<?php
      $num++;
      if($num %5 == 0){
          echo "<br>";
      }
}
?>
<br><br>

      <input type="hidden" name="mode" value="1">
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    if($_SESSION["mark"] == part){
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<A href="javaScript:treeMenu('treeMenu3')">・得点率</A></br>
<DIV id="treeMenu3" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(0～100%)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="minpoint_s" >～
<b>上限</b><input type="text" size="8" name="maxpoint_s" ><br><br>
      <input type="hidden" name="mode" value="1">
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    }else{
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<A href="javaScript:treeMenu('treeMenu3')">・正解率</A></br>
<DIV id="treeMenu3" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(0～100%)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mincorrect_s" >～
<b>上限</b><input type="text" size="8" name="maxcorrect_s" ><br><br>
      <input type="hidden" name="mode" value="1">
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    }       
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<A href="javaScript:treeMenu('treeMenu4')">・平均解答時間</a><br>
<DIV id="treeMenu4" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(単位：秒)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mintime_s" >～
<b>上限</b><input type="text" size="8" name="maxtime_s" ><br><br>
       <input type="hidden" name="mode" value="1">  
 　　　<input type="submit" value="絞り込み">
</form>
</div>

</div>

</div>
<form action = "main.php" method="post">
<table border ="0" width="600" align="center">
<tr>
<th width="150"></th><th width="150"></th><th width="150"></th><th width="150"></th>
</tr>
<tr>
<td>

<SELECT NAME="studentlist" SIZE=15 style="width:150px">
     <?php
    $Count = 0;
    while($row = mysql_fetch_array($res)){
    	$StudentName[$Count] = $row["Name"];
    	$StudentID[$Count] = $row["UID"];
     ?>

<option value="<?php echo $StudentName[$Count];?>" ondblclick='javascript:dispData(<?php echo $StudentID[$Count];?>)'><?php echo $StudentID[$Count];?>
<?php
    $Count++;
    }
?>

</SELECT>


</td>
<td colspan="2" height="100" bgcolor="#ffffff">
	<div id="memberp">
			学習者の情報が出力されます。
	</div>
</td>
</tr>
</TABLE>

<input type="submit" name="exe" value="履歴参照">
</form>




</div>

<div id ="tab2" class="tab2">
    </br>
<div align="left">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<A href="javaScript:treeMenu('treeMenu5')">■ 問題検索</a><br>
<DIV id="treeMenu5" style="display:none">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣ <A href="javaScript:treeMenu('treeMenu6')">・対象問題選択</A><BR>
<DIV id="treeMenu6" style="display:none">
    　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※対象問題を選択してください。(複数チェック可)</br></br></font>

<?php
$sentence_sql = "select WID from question_info ORDER BY WID;";
$sentence_res = mysql_query($sentence_sql,$conn) or die("接続エラー");
$num = 0;
//問題情報をテーブルで表示する

while ($sentence_row = mysql_fetch_array($sentence_res)){

?>
<input type="checkbox" name="sent[]" value="<?php echo $sentence_row["WID"]; ?>"><?php echo $sentence_row["WID"]; ?>
<?php
      $num++;
      if($num %5 == 0){
          echo "<br>";
      }
}
?>
<br><br>

      <input type="hidden" name="mode" value="2">
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    if($_SESSION["mark"] == part){
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<A href="javaScript:treeMenu('treeMenu7')">・得点率</A></br>
<DIV id="treeMenu7" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(0～100%)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mincorrect_q" >～
<b>上限</b><input type="text" size="8" name="maxcorrect_q" ><br><br>
      <input type="hidden" name="mode" value="2"> 
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    }else{
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<A href="javaScript:treeMenu('treeMenu7')">・正解率</A></br>
<DIV id="treeMenu7" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(0～100%)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mincorrect_q" >～
<b>上限</b><input type="text" size="8" name="maxcorrect_q" ><br><br>
      <input type="hidden" name="mode" value="2"> 
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    }
?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣<A href="javaScript:treeMenu('treeMenu8')">・平均解答時間</a><br>
<DIV id="treeMenu8" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(単位：秒)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mintime_q" >～
<b>上限</b><input type="text" size="8" name="maxtime_q" ><br><br>
      <input type="hidden" name="mode" value="2">
　　　<input type="submit" value="絞り込み">
</form>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣ <A href="javaScript:treeMenu('treeMenu9')">・単語数</A><BR>
<DIV id="treeMenu9" style="display:none">
    <form action = "main.php" method="post">
　　   <font size= "1"></font>※単語数を入力してください(単位：語)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="minword" >～
<b>上限</b><input type="text" size="8" name="maxword" ><br><br>
      <input type="hidden" name="mode" value="2">
　　　<input type="submit" value="絞り込み">
</form>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣ <A href="javaScript:treeMenu('treeMenu10')">・難易度</A><BR>
<DIV id="treeMenu10" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※難易度を選択してください(複数チェック可)</br></br></font>
      &nbsp;&nbsp;&nbsp;
       <input type="checkbox" name="level[]" value="1">初級
       <input type="checkbox" name="level[]" value="2">中級
       <input type="checkbox" name="level[]" value="3">上級
<br><br>

      <input type="hidden" name="mode" value="2">
　　　<input type="submit" value="絞り込み">
</form>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┣ <A href="javaScript:treeMenu('treeMenu11')">・文法項目</A><BR>
<DIV id="treeMenu11" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※文法項目を選択してください。(複数チェック可)</br></br></font>

<?php
$g_sql = "select Item from grammar 
        ORDER BY PID;";
$PID = 1;
$g_res = mysql_query($g_sql,$conn) or die("接続エラー");
$num = 0;
//問題情報をテーブルで表示する

while ($g_row = mysql_fetch_array($g_res)){

?>
<input type="checkbox" name="grammar[]" value="<?php echo $PID; ?>"><?php echo $g_row["Item"]; ?>
<?php
	if($PID % 4 == 0){
		echo "<br>";
	}
  $num++;
  $PID++;
}
?>
<br><br>
      <input type="radio" name="radiobutton" value="AND">AND検索
      <input type="radio" name="radiobutton" value="OR">OR検索
      <input type="hidden" name="mode" value="2">
　　　<input type="submit" value="絞り込み">
</form>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┗ <A href="javaScript:treeMenu('treeMenu12')">・単語検索</A><BR>
<DIV id="treeMenu12" style="display:none">
    　 <form action = "main.php" method="post">
　　   <font size= "1"></font>※検索する英単語を入力してください</br></br></font>
      &nbsp;&nbsp;&nbsp;<input type="text" size="30" name="word"><br><br>
      <input type="hidden" name="mode" value="2">
　　　<input type="submit" value="絞り込み">
</form>
</div>
</div>

</div>

<form action = "main.php" method="post">
<table border ="0" width="600" align="center">
<tr>
<th width="150"></th><th width="150"></th><th width="50"></th><th width="250"></th>
</tr>
<tr>
<td colspan="3">
<SELECT NAME="queslist" SIZE=15 style="width:400px">
</br></br>
    <?php

		$res2 = mysql_query($sql2,$conn) or die("接続エラー");
    	$Count = 0;
    	while($row2 = mysql_fetch_array($res2)){
  	 		$QuesName[$Count] = $row2["WID"].":".$row2["Sentence"];
  	 		$QuesID[$Count] = $row2["WID"];
   		//echo $row2["WID"].":".$QuesName[$Count];
   		 ?>
<option value="<?php echo $Count;?>" ondblclick='javascript:dispQues(<?php echo $QuesID[$Count];?>)'><?php echo $QuesName[$Count];?>
   	 <?php
    	$Count++;
		}
    	?>
</SELECT>
</td>

<td colspan ="1" height="100" bgcolor="#ffffff">
	<div id="questionq">
			問題の情報が出力されます。
	</div>
</td>
</tr>

</table>

<input type="submit" name="exe" value="履歴参照">
</form>



</div>

<div id ="tab3" class="tab3">



</div>
</div>


<script type="text/javascript">
<!--

ChangeTab('tab1');
</script>

</br>
<?php

?>
<div align="center">
<A href="javaScript:treeMenu('treeMenu13')">■ 履歴データ検索</a><br>
<DIV id="treeMenu13" style="display:none">
<?php
    if($_SESSION["mark"] == part){
?>
┣<A href="javaScript:treeMenu('treeMenu14')">・得点</A></br>
<DIV id="treeMenu14" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(単位：点)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="2" name="minpoint_r" >～
<b>上限</b><input type="text" size="2" name="maxpoint_r" ><br><br>
      <input type="hidden" name="mode" value="3">
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    }else{
?>
┣<A href="javaScript:treeMenu('treeMenu14')">・正誤</A></br>
<DIV id="treeMenu14" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※正誤を選択してください</br></br></font>
      &nbsp;&nbsp;&nbsp;<input type="radio" name="truefalse"  value="1">正答
	<input type="radio" name="truefalse value="0">誤答<br><br>
	 <input type="hidden" name="mode" value="3">
　　　<input type="submit" value="絞り込み">
</form>
</div>
<?php
    }
?>
┣<A href="javaScript:treeMenu('treeMenu15')">・解答時間</A></br>
<DIV id="treeMenu15" style="display:none">
　　   <form action = "main.php" method="post">
　　   <font size= "1"></font>※数字で入力してください(単位：秒)</br></br></font>
      &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mintime_r" >～
<b>上限</b><input type="text" size="8" name="maxtime_r" ><br><br>
<input type="hidden" name="mode" value="3">
　　　<input type="submit" value="絞り込み">
</form>
</div>
┗<A href="javaScript:treeMenu('treeMenu16')">・自信度</a><br>
<DIV id="treeMenu16" style="display:none">
　　   <form action = "main.php" method="post">
　　   <input type="checkbox" name="confidence[]" value="1">自信がある(75%以上)</br>
　　   <input type="checkbox" name="confidence[]" value="2">完全には自信がない(50～75%程度)</br>
　　   <input type="checkbox" name="confidence[]" value="3">あまり自信がない(25%未満)</br>
　　   <input type="checkbox" name="confidence[]" value="4">完全には自信がない(50～75%程度)</br>
　　   <input type="checkbox" name="confidence[]" value="5">間違って決定ボタンを押した</br>
	<input type="hidden" name="mode" value="3">
　　　<input type="submit" value="絞り込み">
</form>
</div>
</DIV>

<BR>
</div>
<table border ="0" width="600" align="center">
<tr>
<th width="100">履歴データ</th><th width="150"></th><th width="150"></th><th width="150"></th>
</tr>
<tr>
<td>
    <SELECT NAME="datalist" SIZE=20  style="width:600px">
    <?php


	$res3 = mysql_query($sql3,$conn) or die("接続エラー");
    $Count = 0;
    if($_SESSION["mark"] == "part"){
     while($row3 = mysql_fetch_array($res3)){

  	 $DataName[$Count] = $row3["UID"]." (".$row3["WID"].") "." ".$row3["Date"]."  ".$row3["Point"]."点  ".$row3["Time"];
   	 //echo $DataName[$Count];
   	 ?>
    <option value="<?=$QuesName[$Count]?>"><?php  echo $DataName[$Count];?>
    <?php
    $Count++;
	}
    }else{
    while($row3 = mysql_fetch_array($res3)){
             if($row3["TF"] == 1){
         $row3["TF"] = "○";
     }else{
         $row3["TF"] = "×";
     }
  	 $DataName[$Count] = $row3["UID"]." (".$row3["WID"].") "." ".$row3["Date"]."  ".$row3["TF"]." ".$row3["Time"];
   	 //echo $DataName[$Count];
   	 ?>
    <option value="<?=$QuesName[$Count]?>"><?php  echo $DataName[$Count];?>
    <?php
    $Count++;
	}
    }
	?>
    </SELECT>
</td>
</tr>
</table>

</div>
</body>
</html>