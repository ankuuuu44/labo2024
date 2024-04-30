<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
    session_start();
    if(isset($_SESSION["mark"]) && isset($_POST["mark"])){
        if($_POST["mark"] != $_SESSION["mark"]){//正誤表示、部分点表示の場合分け処理
            if(isset($_POST["mark"])){
                $_SESSION["mark"] = $_POST["mark"];
            }
        }
    }
    
    error_reporting(0);
?>

<html>
<head>

<script type="text/javascript" src="./prototype.js"></script>
<script type="text/javascript" src="./d3.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>-->
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

    
    <?php
    $_SESSION["student"]="";
    $_SESSION["question"]="";
    $mode =0;

    error_reporting(0);

    //検索の条件
    if(isset($_POST["commando_s1"])){
        $commando_s1 = $_POST["commando_s1"];//学習者コマンド
    }

    if(isset($_POST["commando_s2"])){
        $commando_s2 = $_POST["commando_s2"];//学習者コマンド
    }

    if(isset($_POST["commando_s3"])){
        $commando_s3 = $_POST["commando_s3"];//学習者コマンド
    }
    
    if(isset($_POST["commando_s4"])){
        $commando_s4 = $_POST["commando_s4"];//学習者コマンド
    }

    if(isset($_POST["commando_q1"])){
        $commando_q1 = $_POST["commando_q1"];//問題コマンド
    }

    if(isset($_POST["commando_q2"])){
        $commando_q2 = $_POST["commando_q2"];//問題コマンド
    }

    if(isset($_POST["commando_q3"])){
        $commando_q3 = $_POST["commando_q3"];//問題コマンド
    }

    if(isset($_POST["commando_q4"])){
        $commando_q4 = $_POST["commando_q4"];//問題コマンド
    }

    if(isset($_POST["commando_q5"])){
        $commando_q5 = $_POST["commando_q5"];//問題コマンド
    }

    if(isset($_POST["commando_q6"])){
        $commando_q6 = $_POST["commando_q6"];//問題コマンド
    }

    if(isset($_POST["commando_q7"])){
        $commando_q7 = $_POST["commando_q7"];//問題コマンド
    }
    
    if(isset($_POST["commando_r1"])){
        $commando_r1 = $_POST["commando_r1"];//履歴コマンド
    }

    if(isset($_POST["commando_r2"])){
        $commando_r2 = $_POST["commando_r2"];//履歴コマンド
    }

    if(isset($_POST["commando_r3"])){
        $commando_r3 = $_POST["commando_r3"];//履歴コマンド
    }

    if(isset($_POST["commando_r4"])){
        $commando_r4 = $_POST["commando_r4"];//履歴コマンド
    }

    if(isset($_POST["commando_r5"])){
        $commando_r5 = $_POST["commando_r5"];//履歴コマンド
    }

    if(isset($_POST["commando_r6"])){
        $commando_r6 = $_POST["commando_r6"];//履歴コマンド
    }

    if(isset($_POST["commando_r7"])){
        $commando_r7 = $_POST["commando_r7"];//履歴コマンド
    }

    if(isset($_POST["commando_r8"])){
        $commando_r8 = $_POST["commando_r8"];//履歴コマンド
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    //if($commando_s1[0] =="1")みたいなのをすべてif(isset($commando_s1[0])){if($commando_s1[0] =="1"){}}とする．
    if(isset($commando_s1[0])){
        if($commando_s1[0] =="1"){
            $stu = $_POST["stu"];//学習者選択
        }
    }
    if(isset($commando_s2[0])){
        if($commando_s2[0] =="2"){
            $maxpoint_s = $_POST["maxpoint_s"];
            $minpoint_s = $_POST["minpoint_s"];
            $maxcorrect_s = $_POST["maxcorrect_s"];//最小正解率(学習者)
            $mincorrect_s=  $_POST["mincorrect_s"];//最大正解率(学習者)
        }
    }
    if(isset($commando_s3[0])){
        if($commando_s3[0] =="3"){
            $maxtime_s = $_POST["maxtime_s"];//最小解答時間(学習者)
            $mintime_s=  $_POST["mintime_s"];//最大解答時間(学習者)
        }
    }

    if(isset($commando_s4[0])){
        if($commando_s4[0] =="4"){
            $maxanswernum_s = $_POST["maxanswernum_s"];
            $minanswernum_s = $_POST["minanswernum_s"];
        }
    }

    if(isset($commando_q1[0])){
        if($commando_q1[0] =="1"){
            $sent =$_POST["sent"];//問題選択
        }
    }
    
    if(isset($commando_q2[0])){
        if($commando_q2[0] =="2"){
            $maxcorrect_q = $_POST["maxcorrect_q"];//最小正解率(問題)
            $mincorrect_q=  $_POST["mincorrect_q"];//最大正解率(問題)
            $maxpoint_q = $_POST["maxpoint_q"];
            $minpoint_q = $_POST["minpoint_q"];
        }
    }
    
    if(isset($commando_q3[0])){
        if($commando_q3[0] =="3"){
            $maxtime_q = $_POST["maxtime_q"];//最小時間(問題)
            $mintime_q=  $_POST["mintime_q"];//最大時間(問題)
        }
    }
    
    if(isset($commando_q4[0])){
        if($commando_q4[0] =="4"){
            $maxword = $_POST["maxword"];//最大単語数
            $minword = $_POST["minword"];//最小単語数
        }
    }
    
    if(isset($commando_q5[0])){
        if($commando_q5[0] =="5"){
            $level =$_POST["level"];//難易度(問題)
        }
    }
    
    if(isset($commando_q6[0])){
        if($commando_q6[0] =="6"){
            $grammar = $_POST["grammar"];//文法項目(問題)
        }
    }
    
    if(isset($commando_q7[0])){
        if($commando_q7[0] =="7"){
            $word = $_POST["word"];//単語検索(問題)
        }
    }
    

    if(isset($commando_r1[0])){
        if($commando_r1[0] =="1"){
            $truefalse =$_REQUEST["truefalse"];//正誤(履歴データ)
            $maxpoint_r = $_POST["maxpoint_r"];
            $minpoint_r = $_POST["minpoint_r"];
            $maxword = $_POST["maxword"];//最小得点(履歴データ)
            $minword=  $_POST["minword"];//最大得点(履歴データ)
    
        }
    }

    if(isset($commando_r2[0])){
        if($commando_r2[0] =="2"){
            $maxtime_r = $_POST["maxtime_r"];//最小時間(履歴データ)
            $mintime_r=  $_POST["mintime_r"];//最大時間(履歴データ)
        }
    }
    
    if(isset($commando_r3[0])){
        if($commando_r3[0] =="3"){
            $year_s = $_POST["year_s"];
            $month_s = $_POST["month_s"];
            $dath_s = $_POST["dath_s"];
            $year_e = $_POST["year_e"];
            $month_e = $_POST["month_e"];
            $dath_e = $_POST["dath_e"];
        }
    }
    
    if(isset($commando_r4[0])){
        if($commando_r4[0] =="4"){
            $confidence =$_POST["confidence"];//自信度
        }
    }
    
    if(isset($commando_r5[0])){
        if($commando_r5[0] =="5"){
            $maxUTurn_r = $_POST["maxUTurn_r"];//最小時間(履歴データ)
            $minUTurn_r=  $_POST["minUTurn_r"];//最大時間(履歴データ)
        }
    }
    
    if(isset($commando_r6[0])){
        if($commando_r6[0] =="6"){
            $maxDD_r = $_POST["maxDD_r"];//最小回数(履歴データ)
            $minDD_r=  $_POST["minDD_r"];//最大回数(履歴データ)
        }
    }
    
    if(isset($commando_r7[0])){
        if($commando_r7[0] =="7"){
            $GroupingTF =$_REQUEST["GroupingTF"];//グルーピング有無(履歴データ)
        }
    }

    if(isset($commando_r8[0])){
        if($commando_r8[0] =="8"){
            $maxDDrev_r = $_POST["maxDDrev_r"];//最小回数(履歴データ)
            $minDDrev_r=  $_POST["minDDrev_r"];//最大回数(履歴データ)
        }
    }
    

    //検索用要素
    if(isset($_REQUEST["radio"])){
        $radio = $_REQUEST["radio"];
    }
    
    if(isset($_REQUEST["radiobutton"])){
        $radiobutton = $_REQUEST["radiobutton"];
    }
    


    //履歴参照用
    if(isset($_POST["exe"])){
        $aaa = $_REQUEST["studentlist"];
    }

    if(isset($_POST["Hesitate"])){
        $_SESSION["studentlist"] = $_REQUEST["studentlist"];
        header("Location:hesitate.php");
    }
    if(isset($_POST["StartTime"])){
        $_SESSION["cmd"] = "StartTime";
        $_SESSION["studentlist"] = $_REQUEST["studentlist"];
        header("Location:outlier.php") ;
    }
    if(isset($_POST["StartTimeQ"])){
        $_SESSION["cmd"] = "StartTime";
        $_SESSION["queslist"] = $_REQUEST["queslist"];
        header("Location:outlier2.php") ;
    }

    if(isset($_POST["DD"])){
        $_SESSION["cmd"] = "DD";
        $_SESSION["studentlist"] = $_REQUEST["studentlist"];
        header("Location:outlier.php") ;
    }

    if(isset($_POST["linedatamouse"])){
        $_SESSION["cmd"] = "linedatamouse";
        $_SESSION["studentlist"] = $_REQUEST["studentlist"];
        header("Location:outlier.php") ;
    }
    if(isset($_REQUEST["queslist"])){
        $bbb = $_REQUEST["queslist"];
    }
    
    if(isset($aaa)){
	    $_SESSION["student"]= " and linedata.UID = ".$aaa;
    }
    if(isset($bbb)){
	    $_SESSION["question"]= " and linedata.WID = ".$bbb;
    }
    $term = $_SESSION["student"]." ".$_SESSION["question"];
    $term_rireki = $_SESSION["student"]." ".$_SESSION["question"];
    $term_stu ="";
    $term_ques="";


    //学習者検索関連ここから------------------------
    if(isset($stu[0])){
        $mode = 1;
        $level_c =0;
        foreach($_POST['stu'] as $val){
            if($level_c == 0){
                $term_stu =  $term_stu." AND (UID = ".$val;
            }else{
                $term_stu = $term_stu." OR UID = ".$val;
            }
            $level_c++;
        }
        $term_stu = $term_stu." ) ";
    }

    if(isset($maxcorrect_s)){
	    if($maxcorrect_s != ""){
            $mode = 1;
            $maxcorrect_s2 = $maxcorrect_s/100;
  	        $term_stu = $term_stu." and sum(linedata.TF)/count(*) <= ".$maxcorrect_s2." ";
	    }
    }
    if(isset($mincorrect_s)){
	    if($mincorrect_s != ""){
            $mode = 1;
        $mincorrect_s2 = $mincorrect_s/100;
  	    $term_stu = $term_stu." and sum(linedata.TF)/count(*) >= ".$mincorrect_s2." ";
	    }
    }
    if(isset($maxpoint_s)){//得点
	    if($maxpoint_s != ""){
            $mode = 1;
            $term_stu = $term_stu." and AVG(trackdata.Point) <= ".$maxpoint_s." ";
	    }
    }
    if(isset($minpoint_s)){//得点
	    if($minpoint_s != ""){
            $mode = 1;
            $term_stu = $term_stu." and AVG(trackdata.Point) >= ".$minpoint_s." ";
	    }
    }
    if(isset($maxtime_s)){
	    if($maxtime_s !="" ){
            $mode = 1;
		    $maxtime_s2 = $maxtime_s*1000;
		    $term_stu = $term_stu." and AVG(linedata.Time) <= ".$maxtime_s2." ";
	    }
    }
    if(isset($mintime_s)){
	    if($mintime_s !="" ){
            $mode = 1;
		    $mintime_s2 = $mintime_s*1000;
		    $term_stu = $term_stu." and AVG(linedata.Time) >= ".$mintime_s2." ";
	    }
    }
    if(isset($maxanswernum_s)){//解答数(最大）
	    if($maxanswernum_s != ""){
            $mode = 1;
     	    $term_stu = $term_stu." and count(*) <= ".$maxanswernum_s." ";
	    }
    }
    if(isset($minanswernum_s)){//解答数（最小）
	    if($minanswernum_s != ""){
            $mode = 1;
     	    $term_stu = $term_stu." and count(*) >= ".$minanswernum_s." ";
	    }
    }
    require "dbc.php";
    if($mode ==1){  
        $sql ="select linedata.UID,AVG(Point) from linedata inner join trackdata on 
            linedata.UID = trackdata.UID group by linedata.UID having AVG(linedata.time)>=0 ".$term_stu.
            " ORDER BY linedata.UID"; 
        $res = mysqli_query($conn,$sql) or die("接続エラー学習者1");
        $Count = 0;
        while($row = mysqli_fetch_array($res)){
             $ID[$Count] = $row["UID"];
            $Count++;
        }
        for($i = 0 ; $i<$Count ; $i++){
            if($i == 0){
                $search_ID_stu = " and (linedata.UID = ".$ID[$i];
            }else{
                $search_ID_stu = $search_ID_stu." or linedata.UID = ".$ID[$i]; 
            }
        }
        if($Count != 0){
            $search_ID_stu = $search_ID_stu.")";
        }
        $sql ="select distinct linedata.UID from linedata,trackdata
        where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$search_ID_stu." order by linedata.UID";  
        $sql2 ="select distinct linedata.WID from linedata,trackdata
        where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID  ".$search_ID_stu." order by linedata.WID";  
        $res2 = mysqli_query($conn,$sql2) or die("接続エラー学習者2");
        $Count = 0;
        while($row2 = mysqli_fetch_array($res2)){
            $ID_ques[$Count] = $row2["WID"];
            $Count++;
        }
        for($i = 0 ; $i<$Count ; $i++){
            if($i == 0){
                $WID_stu = " and (linedata.WID = ".$ID_ques[$i];
            }else{
                $WID_stu = $WID_stu." or linedata.WID = ".$ID_ques[$i]; 
            }
        }
        if($Count != 0){
            $WID_stu = $WID_stu.")";
        }

        $sql2 ="select distinct linedata.WID from linedata,trackdata 
        where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$WID_stu." order by linedata.WID";    
        $sql3 ="select linedata.UID,linedata.WID,linedata.Date,linedata.TF,linedata.Time,trackdata.Point,linedata.Understand from linedata,trackdata
        where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$search_ID_stu." order by linedata.UID,linedata.WID";
    }
    //学習者検索関連ここまで------------------------

    //問題検索関連ここから------------------------
    if(isset($sent[0])){
        $mode = 2;
        $level_c =0;
        foreach($_POST['sent'] as $val){
           if($level_c == 0){
                $term_ques = $term_ques." AND (question_info.WID = ".$val;
            }else{
                $term_ques = $term_ques." OR question_info.WID = ".$val;
            }
        $level_c++;
        }
        $term_ques = $term_ques." ) ";
    }

    if(isset($maxcorrect_q)){
	    if($maxcorrect_q != ""){
            $mode = 2;
            $maxcorrect_q2 = $maxcorrect_q/100;
            $term_ques = $term_ques." and (select sum(TF)/count(*) from linedata where question_info.WID = linedata.WID)<= ".$maxcorrect_q2." ";
	    }
    }
    if(isset($mincorrect_q)){
	    if($mincorrect_q != ""){
            $mode = 2;
            $mincorrect_q2 = $mincorrect_q/100;
            $term_ques = $term_ques." and (select sum(TF)/count(*) from linedata where question_info.WID = linedata.WID)>= ".$mincorrect_q2." ";
	    }
    }
    if(isset($maxpoint_q)){//得点
	    if($maxpoint_q != ""){
            $mode = 2;
            $having = " having AVG(Point) <= ".$maxpoint_q." ";
	    }
    }
    if(isset($minpoint_q)){//得点
	    if($minpoint_q != ""){
            $mode = 2;
            if($having !=""){
                $having = $having." and AVG(Point) >= ".$minpoint_q." ";
            }else{
                $having = " having AVG(Point) >= ".$minpoint_q." ";
            }
	    }
    }
    if(isset($maxtime_q)){
	    if($maxtime_q !="" ){
            $mode = 2;
	        $maxtime_q2 = $maxtime_q*1000;
	        $term_ques = $term_ques." and (select AVG(linedata.Time) from linedata where question_info.WID = linedata.WID)<= ".$maxtime_q2." ";
	    }
    }
    if(isset($mintime_q)){
	    if($mintime_q != ""){
            $mode = 2;
	        $mintime_q2 = $mintime_q*1000;
	        $term_ques = $term_ques." and (select AVG(linedata.Time) from linedata where question_info.WID = linedata.WID)>= ".$mintime_q2." ";
	    }
    }
    if(isset($maxword)){//単語数(最大)
	    if($maxword != ""){
            $mode = 2;
     	    $term_ques = $term_ques." and wordnum <= ".$maxword." ";
	    }
    }
    if(isset($minword)){//単語数(最小)
	    if($minword != ""){
            $mode = 2;
     	    $term_ques = $term_ques." and wordnum >= ".$minword." ";
	    }
    }
    if(isset($level[0])){
        $mode = 2;
        $level_c =0;
        foreach($_POST['level'] as $val){
            if($level_c == 0){
                $term_ques = $term_ques." AND (level = ".$val;
            }else{
                $term_ques = $term_ques." OR level = ".$val;
            }
            $level_c++;
        }
        $term_ques = $term_ques." ) ";
    }
    if(isset($grammar[0])){//文法項目
        $mode = 2;
        $grammar_c =0;
        foreach($_POST['grammar'] as $val){
            if($grammar_c == 0){
                $term_ques = $term_ques." AND (grammar like '%#".$val."#%'" ;
            }else{
                if($radiobutton == "AND"){
                    $term_ques = $term_ques." AND grammar like '%#".$val."#%'" ;
                }else if($radiobutton == "OR"){
                    $term_ques = $term_ques." OR grammar like '%#".$val."#%'" ;
                }
            }
            $grammar_c++;
        }
        $term_ques = $term_ques." ) ";
    }
    if(isset($word)){
        if($word != ""){
            $mode = 2;
    	    $term_ques = $term_ques."and (Sentence like \"% ".$word." %\" ".
		    "or Sentence like \"".$word." %\" ".
		    "or Sentence like \"% ".$word.".\" ".
	        "or Sentence like \"% ".$word."[?]\")";
        }
    }
    if($mode == 2){//問題検索
        $sql2 = "select question_info.WID,Sentence,wordnum,AVG(Point) from question_info inner join trackdata on question_info.WID
                  = trackdata.WID where question_info.WID like '%%' ".$term_ques. " group by question_info.WID ".$having;
        $res2 = mysqli_query($conn,$sql2) or die("接続エラー2");
        $Count = 0;
        while($row2 = mysqli_fetch_array($res2)){
            $WID_ques[$Count] = $row2["WID"];
            $Count++;
        }
        for($i = 0 ; $i<$Count ; $i++){
            if($i == 0){
                $search_WID_ques = " and (question_info.WID = ".$WID_ques[$i];
                $term_WID_ques = " and (linedata.WID = ".$WID_ques[$i];
            }else{
                $search_WID_ques = $search_WID_ques." or question_info.WID = ".$WID_ques[$i];
                $term_WID_ques = $term_WID_ques." or linedata.WID = ".$WID_ques[$i];
            }
        }
        if($Count != 0){
            $search_WID_ques = $search_WID_ques.")";
            $term_WID_ques = $term_WID_ques.")";
        }
        else{
            $search_WID_ques = "and false";
            $term_WID_ques = "and false";
        }


        // $sql2 = "select * from question_info where question_info.WID ".$search_WID_ques;
        $sql ="select distinct linedata.UID from linedata,trackdata
        where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$term_WID_ques." order by linedata.UID";  
        $res = mysqli_query($conn,$sql) or die("接続エラー問題1");
        $Count = 0;
        while($row = mysqli_fetch_array($res)){
            $UID_ques[$Count] = $row["UID"];
            $Count++;
        }
        for($i = 0 ; $i<$Count ; $i++){
            if($i == 0){
                $term_UID_ques = " and (linedata.UID = ".$UID_ques[$i];
            }else{
                $term_UID_ques = $term_UID_ques." or linedata.UID = ".$UID_ques[$i]; 
            }
        }
        if($Count != 0){
            $term_UID_ques = $term_UID_ques.")";
        }
        $sql ="select distinct linedata.UID from linedata,trackdata
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$term_UID_ques." ".$search_ID_stu." order by linedata.UID"; 
        $sql2 ="select distinct linedata.WID from linedata,trackdata
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$WID_stu." ".$term_WID_ques." order by linedata.WID"; 
        $sql3 = "select UID,WID,Date,TF,Time from linedata 
	        where UID like '%%' ".$term_UID_ques." ".$term_WID_ques." ".$search_ID_stu." ".$WID_stu.
	        " ORDER BY UID,WID";
    }
    //問題検索関連ここまで------------------------

    //履歴データ検索関連ここから-----------------------
    if(isset($truefalse)){
        $mode = 3;
	    $term_rireki = $term_rireki." and linedata.TF = ".$truefalse;
    }
    if(isset($maxpoint_r)){//得点
	    if($maxpoint_r != ""){
            $mode = 3;
     	    $term_rireki = $term_rireki." and trackdata.Point <= ".$maxpoint_r." ";
	    }
    }
    if(isset($minpoint_r)){//得点
	    if($minpoint_r != ""){
            $mode = 3;
     	    $term_rireki = $term_rireki." and trackdata.Point >= ".$minpoint_r." ";
	    }
    }
    if(isset($maxtime_r)){
	    if($maxtime_r !="" ){
            $mode = 3;
	    $maxtime_r2 = $maxtime_r*1000;
	    $term_rireki = $term_rireki." and linedata.Time <= ".$maxtime_r2." ";
	    }
    }
    if(isset($mintime_r)){
	    if($mintime_r != ""){
            $mode = 3;
	    $mintime_r2 = $mintime_r*1000;
	    $term_rireki = $term_rireki." and linedata.Time  >= ".$mintime_r2." ";
	    }
    }
    if(isset($year_s)){//期間スタート
        if($year_s !=""){
            $mode = 3;
            $datetime_s = $year_s."-".$month_s."-".$dath_s;
        }
    }
    if(isset($year_e)){//期間ラスト
        if($year_e !=""){
            $mode = 3;
            $datetime_e = $year_e."-".$month_e."-".$dath_e;
        }
    }
    if(isset($datetime_s)){//期間処理用
        if(isset($datetime_e)){
            $term_rireki = $term_rireki." and linedata.Date between '".$datetime_s." 00:00:00' and '".$datetime_e." 23:59:59'";
        }else{
            $term_rireki = $term_rireki." and linedata.Date >= '".$datetime_s." 00:00:00'";
        }
    }else{
        if(isset($datetime_e)){
            $term = $term." and linedata.Date <= '".$datetime_e." 23:59:59'";
        }
    }
    if(isset($confidence[0])){//自信度
        $mode = 3;
        $level_c = 0;
        foreach($_POST['confidence'] as $val){
            if($level_c == 0){
               $term_rireki = $term_rireki." AND (linedata.Understand = ".$val;
            }else{
                $term_rireki = $term_rireki." OR linedata.Understand = ".$val;
            }
            $level_c++;
        }
        $term_rireki = $term_rireki." ) ";
    }
    if(isset($maxUTurn_r)){//Ｕターン
	    if($maxUTurn_r !="" ){
            $mode = 3;
    	    $term_rireki = $term_rireki." and trackdata.UTurnCount_X <= ".$maxUTurn_r." ";
	    }
    }
    if(isset($minUTurn_r)){
	    if($minUTurn_r !="" ){
            $mode = 3;
	        $term_rireki = $term_rireki." and trackdata.UTurnCount_X >= ".$minUTurn_r." ";
	    }
    }

    if(isset($maxDD_r)){//DD回数
	    if($maxDD_r !="" ){
            $mode = 3;
	        $term_rireki = $term_rireki." and trackdata.DragDropCount <= ".$maxDD_r." ";
	    }
    }
    if(isset($minDD_r)){//DD回数
	    if($minDD_r !="" ){
            $mode = 3;
	        $term_rireki = $term_rireki." and trackdata.DragDropCount >= ".$minDD_r." ";
	    }
    }
    if(isset($maxDDrev_r)){//DDrev回数
	    if($maxDDrev_r !="" ){
            $mode = 3;
	        $term_rireki = $term_rireki." and trackdata.DragDropCount_rev <= ".$maxDDrev_r." ";
	    }
    }
    if(isset($minDDrev_r)){//DDrev回数
	    if($minDDrev_r !="" ){
            $mode = 3;
	        $term_rireki = $term_rireki." and trackdata.DragDropCount_rev >= ".$minDDrev_r." ";
	    }
    }

    if(isset($GroupingTF)){//グルーピング
        $mode = 3;
        $term_rireki = $term_rireki." and exists ( select * from linedatamouse where Label like '%#%' and linedatamouse.UID=linedata.UID and linedatamouse.WID=linedata.WID )";
    }

    if($mode ==3){
        $sql ="select distinct linedata.UID from linedata,trackdata
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$term_rireki." ".$term_UID_ques." ".$search_ID_stu." order by linedata.UID"; 
        $sql2 ="select distinct linedata.WID from linedata,trackdata
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$term_rireki." ".$WID_stu." ".$term_WID_ques." order by linedata.WID";
        $sql3 ="select linedata.UID,linedata.WID,linedata.Date,linedata.TF,linedata.Time,trackdata.Point,linedata.Understand from linedata,trackdata
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$term_rireki." ".$search_ID_stu." ".$term_UID_ques." ".$WID_stu." ".$term_WID_ques." order by linedata.UID,linedata.WID";

        echo "%%".$sql;
    }

    //履歴データ検索関連ここまで-----------------------
    if(isset($term_UID_ques) && isset($search_ID_stu)){
        $term_s = $term_rireki." ".$term_UID_ques." ".$search_ID_stu;
    }
    if(isset($WID_stu) && isset($term_WID_ques)){
        $term_q = $term_rireki." ".$WID_stu." ".$term_WID_ques;
    }
    if(isset($search_ID_stu) && isset($term_UID_ques) && isset($WID_stu) && isset($term_WID_ques)){
        $term_r = $term_rireki." ".$search_ID_stu." ".$term_UID_ques." ".$WID_stu." ".$term_WID_ques;
    }
    
    
    

    if($term == " "){//検索が空の時
        $commando_s1 = "1";//学習者コマンド
        $commando_s2 = "2";
        $commando_s3 = "3";
        $commando_s4 = "4";
        $commando_q1 = "1";//問題コマンド
        $commando_q2 = "2";
        $commando_q3 = "3";
        $commando_q4 = "4";
        $commando_q5 = "5";
        $commando_q6 = "6";
        $commando_q7 = "7";
        $commando_r1 = "1";//履歴コマンド
        $commando_r2 = "2";
        $commando_r3 = "3";
        $commando_r4 = "4";
        $commando_r5 = "5";
        $commando_r6 = "6";
        $commando_r7 = "7";
        $commando_r8 = "8";
    }

    if($mode ==0){
        $sql ="select distinct linedata.UID,AVG(Point) from linedata inner join trackdata on 
            linedata.UID = trackdata.UID ".$term_rireki." group by linedata.UID having AVG(linedata.time)>=0 ".$term_stu."
            ORDER BY linedata.UID"; 
        $res = mysqli_query($conn,$sql) or die("接続エラー1");
        $Count = 0;
        while($row = mysqli_fetch_array($res)){
            $ID[$Count] = $row["UID"];
            $Count++;
        }
        for($i = 0 ; $i<$Count ; $i++){
            if($i == 0){
                $search_ID_stu = " and (linedata.UID = ".$ID[$i];
            }else{
                $search_ID_stu = $search_ID_stu." or linedata.UID = ".$ID[$i]; 
            }
        }
        if($Count != 0){
            $search_ID_stu = $search_ID_stu.")";
        }
        $sql3 ="select linedata.UID,linedata.WID,linedata.Date,linedata.TF,linedata.Time,trackdata.Point,linedata.Understand from linedata,trackdata
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID ".$term_rireki." ".$search_ID_stu." order by linedata.UID,linedata.WID";
        $sql2 ="select distinct linedata.WID from linedata,trackdata 
            where linedata.WID=trackdata.WID and linedata.UID=trackdata.UID order by linedata.WID";
    }
    //問題の絞り込み用
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>履歴分析（仮）</title>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
    <script type="text/javascript">
    function ChangeTab(tabname) {
       // 全部消す
       document.getElementById('tab1').style.display = 'none';
       document.getElementById('tab2').style.display = 'none';
       // 指定箇所のみ表示
       document.getElementById(tabname).style.display = 'block';
    }
    </script>
    <!--
    
-->
    <style type="text/css">
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
        p.tabs a.tab1 { background-color: red;  color: white; }
        p.tabs a.tab2 { background-color: orange; color:white;}
        p.tabs a:hover { color: black; }
        /* ▼(C)タブ中身のボックス */
        div.tab {
           /* ☆ボックス共通の装飾 */
        overflow: auto; clear: left;
        }
        /* ◇各ボックスの配色 */
        div#tab1 { border: 4px solid red; background-color: #ffffbb; }
        div#tab2 { border: 4px solid orange; background-color: #ffffbb; }
        div.tab p { margin: 0.5em; }
        .stuques {
          overflow: hidden;   /* スクロール表示 */
          width: 700px;
          height: 370px;
          background-color: white;
          position: absolute;
          top: 210px;
          left: 10px;
          border:2px dotted yellow;
        }
        .rireki {
          overflow: hidden;   /* スクロール表示 */
          width: 400px;
          height: 580px;
          background-color: white;
          position: absolute;
          top: 210px;
          left: 750px;
          border:2px dotted yellow;
        }
        .count {
          width: 1200px;
          height: 190px;
          background-color: white;
          position: absolute;
          top: 10px;
          left: 10px;
          border:2px dotted yellow;
        }
        .search{
          width: 700px;
          height: 450px;
          background-color: white;
          position: absolute;
          top: 590px;
          left: 10px;
          border:2px dotted yellow;
        }   
        /*
        #canvasddd{
            width: 500px;
            height: 500px;
            position: absolute;
            top:1000px;
            left: 1000px;
            right: 200px;
            border: 2px dotted yellow;

        }
        */
        #chart-js-graph{
            width: 1500px;
            height: 600px;
            position: absolute;
            display: flex;  /*要素横並び */
            flex-wrap:wrap;
            position: absolute;
            top: 210px;
            left: 1200px;
            border: 2px dotted yellow;

        }
        #answer_quesWID{
            background-color: crimson;
            width: 25%;
            height: 50%;
        }
        #grammar-All{
            background-color: blue;
            width: 50%;
            height: 50%;
            /*max-width: 500px;*/
        }
        #level-All{
            background-color: green;
            width: 25%;
            height: 50%;
            /*max-width: 350px;*/
        }
        #accuracy-All{
            background-color: red;
            width: 50%;
            height: 50%;
        }
        #accuracy-rank{
            /*background-color: aqua;*/
            width: 25%;
            height: 50%;

        }
        
        #hesitate-rank{
            /*background-color: blueviolet;*/
            width: 25%;
            height: 50%;

        }
        
        .tablerank{
            display: none;
        }

        .ans_quesWID{
            display: none;
        }

    </style>


    <script type="text/javascript">

    function grammar_dispQues(grammar){
        ChangeTab("tab2");
        var selectElementques = document.getElementById('tempqueslist');
        var selectElementstu = document.getElementById('tempstudentlist');
        //grammar_select.phpに
        if(grammar == '文法項目設定なし'){
            gramnum = -1;
        }else if(grammar == '仮定法'){
            gramnum = 1;
        }else if(grammar == 'It,There'){
            gramnum = 2;
        }else if(grammar == '無生物主語'){
            gramnum = 3;
        }else if(grammar == '接続詞'){
            gramnum = 4;
        }else if(grammar == '倒置'){
            gramnum = 5;
        }else if(grammar == '関係詞'){
            gramnum = 6;
        }else if(grammar == '間接話法'){
            gramnum = 7;
        }else if(grammar == '前置詞'){
            gramnum = 8;
        }else if(grammar == '分詞'){
            gramnum = 9;
        }else if(grammar == '動名詞'){
            gramnum = 10;
        }else if(grammar == '不定詞'){
            gramnum = 11;
        }else if(grammar == '受動態'){
            gramnum = 12;
        }else if(grammar == '助動詞'){
            gramnum = 13;
        }else if(grammar == '比較'){
            gramnum = 14;
        }else if(grammar == '否定'){
            gramnum = 15;
        }else if(grammar == '後置修飾'){
            gramnum = 16;
        }else if(grammar == '完了形'){
            gramnum = 17;
        }else if(grammar == '句動詞'){
            gramnum = 18;
        }else if(grammar == '挿入'){
            gramnum = 19;
        }else if(grammar == '使役動詞'){
            gramnum = 20;
        }else if(grammar == '補語/二重目的語'){
            gramnum = 21;
        }

        if(selectElementques != null){
            var numberoption = selectElementques.options.length;    //選択肢の行数把握
            var $a = 'id='+encodeURIComponent(gramnum);

            new Ajax.Request('./grammar_select.php',
            {
                method: 'post',
                onSuccess: getA,
                onFailure: getE,
                parameters: $a
            });
            function getA(req){
                //現在の問題選択肢を削除
                for(i = 0; i<numberoption; i++){
                    selectElementques.remove(0);
                }
                var response = JSON.parse(req.responseText);
                var optionnum = response.grammarquesid.length
                console.log(response.grammarquesid.length);
                for(i = 0; i<optionnum; i++){
                    var optinoElement = document.createElement("option");
                    optinoElement.value = response.grammarquesid[i];
                    optinoElement.textContent = response.grammarquessentence[i];
                    optinoElement.setAttribute("ondbclick", "dispQues(" + response.grammarquesid[i] + ")" );
                    if(i == 0){
                        optinoElement.selected = true;
                    }
                    selectElementques.appendChild(optinoElement);
                    
                }


                
            }
            function getE(req){
                alert("問題分析エラー");
            }


                
                
        }else{
            console.log("もともとの問題選択肢が空です．");
        }


    }





    function dispData(msg){
	    var b = msg;    //UIDがmsgに入っている．
	    var $a = 'id='+encodeURIComponent(b);
        //▲マウスデータの取得
	    //ドラッグ開始地点の保存
	    new Ajax.Request('./search_info.php',
        {
	        method: 'post',
	        onSuccess: getA,
	        onFailure: getE,
	        parameters: $a
        });
	    function getA(req){

            var response = JSON.parse(req.responseText);
            var accuracygrammararray = []


            //ここから先は選択した学習者が解答した問題を表示する
            //ここから背景色を透明にする．
            var backgroundquesWID = document.getElementById("answer_quesWID");  
            backgroundquesWID.style.backgroundColor = "transparent";
            var select_quesWID = document.getElementById("WIDques");
            var ans_quesWID = document.querySelector(".ans_quesWID");
            console.log(select_quesWID);

            
            if(select_quesWID != null){
                console.log("yolo");
                
                var select_quesWIDlength = select_quesWID.options.length;
                for(i = 0; i<select_quesWIDlength; i++){
                    select_quesWID.remove(0);
                }
                
            }
            

            
            ans_quesWID.style.display = "inline-block";
            console.log(response.Allques);
            
            
            
            
            //option要素の生成
            AllquesWID = response.Allques;
            AllquesSentence = response.AllquesSentence;
            
            for(i = 0; i<AllquesWID.length; i++){
                var optionelement = document.createElement('option');
                optionelement.value = b + "," + AllquesWID[i];
                optionelement.textContent = AllquesWID[i] + "." +  AllquesSentence[i];
                select_quesWID.appendChild(optionelement);

            }
            
            




            //ここから先は正解率上位の表の表示
            
            var tableclass = document.querySelectorAll(".tablerank");
            console.log(tableclass);
            tableclass.forEach(element => element.style.display = 'inline-block');

            //response.accuracy_grammarを逆順で表示
            var tempaccuracyarray = Object.keys(response.accuracy_grammar).map((k) => ({key:k, value:response.accuracy_grammar[k]}));
            tempaccuracyarray.sort((a,b) => b.value - a.value);
            var topFiveItems = tempaccuracyarray.slice(0,5);
            console.log(topFiveItems);
            


            tableElementaccuracy = document.getElementById("tableaccuracy");
            if(tableElementaccuracy.rows.length > 1){
                for(i=0;i<5;i++){
                tableElementaccuracy.deleteRow(-1);
            }
            }
            


            for (i=0;i<5;i++){
                var mytr = tableElementaccuracy.insertRow(-1);
                var mytd1 = mytr.insertCell(0);
                var mytd2 = mytr.insertCell(1);
                var mytd3 = mytr.insertCell(2);
                mytd1.textContent = i+1;
                if(topFiveItems[i].key == -1){
                    mytd2.textContent = "文法項目設定なし";
                }else if(topFiveItems[i].key == 1){
                    mytd2.textContent ="仮定法";
                }else if(topFiveItems[i].key == 2){
                    mytd2.textContent ="It,There";
                }else if(topFiveItems[i].key == 3){
                    mytd2.textContent ="無生物主語";
                }else if(topFiveItems[i].key == 4){
                    mytd2.textContent ="接続詞";
                }else if(topFiveItems[i].key == 5){
                    mytd2.textContent ="倒置";
                }else if(topFiveItems[i].key == 6){
                    mytd2.textContent ="関係詞";
                }else if(topFiveItems[i].key == 7){
                    mytd2.textContent ="間接話法";
                }else if(topFiveItems[i].key == 8){
                    mytd2.textContent ="前置詞";
                }else if(topFiveItems[i].key == 9){
                    mytd2.textContent ="分詞";
                }else if(topFiveItems[i].key == 10){
                    mytd2.textContent ="動名詞";
                }else if(topFiveItems[i].key == 11){
                    mytd2.textContent ="不定詞";
                }else if(topFiveItems[i].key == 12){
                    mytd2.textContent ="受動態";
                }else if(topFiveItems[i].key == 13){
                    mytd2.textContent ="助動詞";
                }else if(topFiveItems[i].key == 14){
                    mytd2.textContent ="比較";
                }else if(topFiveItems[i].key == 15){
                    mytd2.textContent ="否定";
                }else if(topFiveItems[i].key == 16){
                    mytd2.textContent ="後置修飾";
                }else if(topFiveItems[i].key == 17){
                    mytd2.textContent ="完了形";
                }else if(topFiveItems[i].key == 18){
                    mytd2.textContent ="句動詞";
                }else if(topFiveItems[i].key == 19){
                    mytd2.textContent ="挿入";
                }else if(topFiveItems[i].key == 20){
                    mytd2.textContent ="使役動詞";
                }else if(topFiveItems[i].key == 21){
                    mytd2.textContent ="補語/二重目的語";
                }

                mytd3.textContent = (topFiveItems[i].value * 100).toPrecision(4) + '%';

            }

            
            //迷い率上位五件表示
            //response.hesitate_grammarを逆順で表示
            
            
            var temphesitate_grammar = Object.keys(response.hesitate_grammar).map((k) => ({key:k, value:response.hesitate_grammar[k]}));
            temphesitate_grammar.sort((a,b) => b.value - a.value);
            var topFiveItems_hesitate = temphesitate_grammar.slice(0,5);
            console.log(topFiveItems_hesitate);
            

            
            tableElementhesitate = document.getElementById("tablehesitate");
            console.log(tableElementhesitate);
            
            if(tableElementhesitate.rows.length > 1){
                for(i=0;i<5;i++){
                tableElementhesitate.deleteRow(-1);
            }
            }
            


            for (i=0;i<5;i++){
                var mytr_hesitate = tableElementhesitate.insertRow(-1);
                var mytd1_hesitate = mytr_hesitate.insertCell(0);
                var mytd2_hesitate = mytr_hesitate.insertCell(1);
                var mytd3_hesitate = mytr_hesitate.insertCell(2);
                mytd1_hesitate.textContent = i+1;
                if(topFiveItems_hesitate[i].key == -1){
                    mytd2_hesitate.textContent = "文法項目設定なし";
                }else if(topFiveItems_hesitate[i].key == 1){
                    mytd2_hesitate.textContent ="仮定法";
                }else if(topFiveItems_hesitate[i].key == 2){
                    mytd2_hesitate.textContent ="It,There";
                }else if(topFiveItems_hesitate[i].key == 3){
                    mytd2_hesitate.textContent ="無生物主語";
                }else if(topFiveItems_hesitate[i].key == 4){
                    mytd2_hesitate.textContent ="接続詞";
                }else if(topFiveItems_hesitate[i].key == 5){
                    mytd2_hesitate.textContent ="倒置";
                }else if(topFiveItems_hesitate[i].key == 6){
                    mytd2_hesitate.textContent ="関係詞";
                }else if(topFiveItems_hesitate[i].key == 7){
                    mytd2_hesitate.textContent ="間接話法";
                }else if(topFiveItems_hesitate[i].key == 8){
                    mytd2_hesitate.textContent ="前置詞";
                }else if(topFiveItems_hesitate[i].key == 9){
                    mytd2_hesitate.textContent ="分詞";
                }else if(topFiveItems_hesitate[i].key == 10){
                    mytd2_hesitate.textContent ="動名詞";
                }else if(topFiveItems_hesitate[i].key == 11){
                    mytd2_hesitate.textContent ="不定詞";
                }else if(topFiveItems_hesitate[i].key == 12){
                    mytd2_hesitate.textContent ="受動態";
                }else if(topFiveItems_hesitate[i].key == 13){
                    mytd2_hesitate.textContent ="助動詞";
                }else if(topFiveItems_hesitate[i].key == 14){
                    mytd2_hesitate.textContent ="比較";
                }else if(topFiveItems_hesitate[i].key == 15){
                    mytd2_hesitate.textContent ="否定";
                }else if(topFiveItems_hesitate[i].key == 16){
                    mytd2_hesitate.textContent ="後置修飾";
                }else if(topFiveItems_hesitate[i].key == 17){
                    mytd2_hesitate.textContent ="完了形";
                }else if(topFiveItems_hesitate[i].key == 18){
                    mytd2_hesitate.textContent ="句動詞";
                }else if(topFiveItems_hesitate[i].key == 19){
                    mytd2_hesitate.textContent ="挿入";
                }else if(topFiveItems_hesitate[i].key == 20){
                    mytd2_hesitate.textContent ="使役動詞";
                }else if(topFiveItems_hesitate[i].key == 21){
                    mytd2_hesitate.textContent ="補語/二重目的語";
                }

                mytd3_hesitate.textContent = (topFiveItems_hesitate[i].value * 100).toPrecision(4) + '%';

            }
            


            //円グラフ描画
            var chartgrammar = new CanvasJS.Chart("grammar-All",
            {
                title:{
                    text: "文法項目"
                },
                legend: {
                    itemWidth: 120
                },
                data: [
                {
                    type: "pie",
                    showInLegend: true,
                    legendText: "{indexLabel}",
                    
                    dataPoints: [
                        { y: response.countgrammar[-1], indexLabel: "文法項目設定なし"},
                        { y: response.countgrammar[1], indexLabel: "仮定法" },
                        { y: response.countgrammar[2], indexLabel: "It,There" },
                        { y: response.countgrammar[3], indexLabel: "無生物主語" },
                        { y: response.countgrammar[4], indexLabel: "接続詞"},
                        { y: response.countgrammar[5], indexLabel: "倒置" },
                        { y: response.countgrammar[6], indexLabel: "関係詞"},
                        { y: response.countgrammar[7], indexLabel: "間接話法"},
                        { y: response.countgrammar[8], indexLabel: "前置詞" },
                        { y: response.countgrammar[9], indexLabel: "分詞" },
                        { y: response.countgrammar[10], indexLabel: "動名詞" },
                        { y: response.countgrammar[11], indexLabel: "不定詞"},
                        { y: response.countgrammar[12], indexLabel: "受動態" },
                        { y: response.countgrammar[13], indexLabel: "助動詞"},
                        { y: response.countgrammar[14], indexLabel: "比較"},
                        { y: response.countgrammar[15], indexLabel: "否定" },
                        { y: response.countgrammar[16], indexLabel: "後置修飾" },
                        { y: response.countgrammar[17], indexLabel: "完了形" },
                        { y: response.countgrammar[18], indexLabel: "句動詞"},
                        { y: response.countgrammar[19], indexLabel: "挿入" },
                        { y: response.countgrammar[20], indexLabel: "使役動詞"},
                        { y: response.countgrammar[21], indexLabel: "補語/二重目的語"}
                        
                    ]
                }],
                
            });
            chartgrammar.options.data[0].click = function(e) {
                var ypoint = e.dataPoint.indexLabel;
                console.log("クリックされたデータポイント: " + ypoint);
                grammar_dispQues(ypoint);
            
            };
            chartgrammar.render();
            
            
            
            
            var chartlevel = new CanvasJS.Chart("level-All",
            {
                title:{
                    text: "難易度"
                },
                legend: {
                    itemWidth: 120
                },
                data: [
                {
                    type: "pie",
                    showInLegend: true,
                    legendText: "{indexLabel}",
                    
                    dataPoints: [
                        { y: response.levelAll["level1count"], indexLabel: "初級" },
                        { y: response.levelAll["level2count"], indexLabel: "中級" },
                        { y: response.levelAll["level3count"], indexLabel: "上級" },
                        
                    ]
                }
                ]
            });
            chartlevel.options.data[0].click = function(e) {
            console.log("クリックされたデータポイント: " + e.dataPoint.indexLabel);
            };
            chartlevel.render();


            var accuracy_graph = new CanvasJS.Chart("accuracy-All",
            {
                title:{
                    text: "正解率推移"
                },

                data: [
                {
                    type: "column",
                    showInLegend: true,
                    legendText: "{indexLabel}",
                    
                    dataPoints: [
                        { label: "10月25日", y:30 },
                        { label: "10月29日", y:50 },
                        { label: "10月31日", y:70 },
                        
                    ]
                }
                ]
            });
            accuracy_graph.render();
            
                
                
                
        }
	    function getE(req){
		    alert("学習者分析エラー");
	    }
    }


    function dispQues(msg){
        var b = msg;
	    var $a = 'id='+encodeURIComponent(b);
        //▲マウスデータの取得
	    //ドラッグ開始地点の保存
        new Ajax.Request('./ques_info.php',
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
    function allcheck(formName, checkboxName, bool) {
	    for(i = 0; i<eval('document.' + formName + '.elements["' + checkboxName + '"].length'); i++) {
		    eval('document.' + formName + '.elements["' + checkboxName + '"][' + i + '].checked = ' + bool);
	    }
    }
</script>

</head>
<body>


<div align="center">
<DIV class="stuques">
    <div class="tabbox">
       <p class="tabs">
          <a href="#tab1" class="tab1" onclick="ChangeTab('tab1'); return false;">学習者</a>
          <a href="#tab2" class="tab2" onclick="ChangeTab('tab2'); return false;">問題</a>
       </p>
        <br>
    </div>
    <div id ="tab1" class="tab">
        <br>
        <?php
            //echo $sql;
            //$sqlは$mode = 0の
            $res = mysqli_query($conn,$sql) or die("接続エラー1");
        ?>
        <form action = "main.php" method="post" target="_blank">
            <table border ="0" width="600" align="center">
            <tr><th width="150"></th><th width="150"></th><th width="150"></th><th width="150"></th></tr>
            <tr><td>
                <SELECT NAME="studentlist" id = "tempstudentlist" SIZE=15 style="width:150px">
                <?php
                    $Count = 0;
                    while($row = $res -> fetch_assoc()){
    	                $StudentName[$Count] = $row["Name"];
    	                $StudentID[$Count] = $row["UID"];
                        if($Count == 0){
                ?>
                            <option value="<?php echo $StudentID[$Count];?>" ondblclick='javascript:dispData(<?php echo $StudentID[$Count];?>)' selected><?php echo $StudentID[$Count];?></option>
                <?php
                        }else{
                ?>
                            <option value="<?php echo $StudentID[$Count];?>" ondblclick='javascript:dispData(<?php echo $StudentID[$Count];?>)'><?php echo $StudentID[$Count];?></option>
                <?php
                        }
                        $Count++;
                    }  
                    $student_count = $Count;
                ?>
                </SELECT></td>
                <td colspan="2" height="100" bgcolor="#ffffff">
	                <div id="memberp">
                        
                        <div id = "countques">aaa</div>
                        <?php
                        $filename = "Python/classify_crossval_fromQues.py";
                        $command = "py " . $filename;
                        //$pydata = array(5,2.9,1,0.2);
                        /*
                        山川追記分
                        この部分の変数pydataで配列の中の要素の値をデータベースから引っ張ってこれれば機械学習を動的にすることが可能
                        また，多次元の配列を渡すことによっていくつものデータを渡すことで複数機械学習行うこと可能かも．でもこれはpython側も変更する必要ありね
                         */
                        $pydata = array(18310001,395,2,0,0,64717,12035.0567,0.185964379,6347.577158,2134,62583,21546,8384,45630,13322,1920,380,20,0,0,36,35,4,8,8,0,1,1,1,0,20,8,18,12,1493);
                        exec($command." ". implode(" ",$pydata) . " 2>&1", $dum, $return_var);
                        echo "python:" . $dum[0] . "<br>";
                        echo "Output: " . implode("\n", $dum) . "\n";
                        echo "Return Code: " . $return_var . "\n";
                        ?>
                        <!--
                        <div id = "correctPer"></div>"
                        <div id = "avgTime"></div>
                        -->
			                学習者の情報が出力されます。
	                </div>
                </td>
            </tr>
            </TABLE>
            <input type="submit" name="exe" value="履歴参照" class="btn_mini2">
            <input type="submit" name="Hesitate" value="迷い分析"  class="btn_mini2">
            <input type="submit" name="StartTime" value="問題別情報" class="btn_mini2">
            <input type="submit" name="DD" value="DD分析" class="btn_mini2">
            <input type="submit" name="linedatamouse" value="座標情報" class="btn_mini2">
        </form>
    </div>
    <div id ="tab2" class="tab2">
    <br>
    <form action = "main.php" method="post" target="_blank">
        <table border ="0" width="600" align="center">
        <tr><th width="150"></th><th width="150"></th><th width="50"></th><th width="250"></th></tr>
        <tr><td colspan="3">
            <br><br>
            <SELECT NAME="queslist" id = "tempqueslist" SIZE=15 style="width:400px">
            <?php
                $res2 = mysqli_query($conn,$sql2) or die("接続エラー2");
    	        $Count = 0;
    	        while($row2 = $res2 -> fetch_assoc()){
                    $sql_q = "select Sentence from question_info where WID =".$row2["WID"];
                    $res_q = mysqli_query($conn,$sql_q) or die("接続エラーq");
                    $row_q = $res_q -> fetch_assoc();
                    $QuesName[$Count] = $row2["WID"].":".$row_q["Sentence"];
  	 		        $QuesID[$Count] = $row2["WID"];
                    if($Count == 0){
            ?>
                        <option value="<?php echo $QuesID[$Count];?>" ondblclick='javascript:dispQues(<?php echo $QuesID[$Count];?> )' selected><?php echo $QuesName[$Count];?></option>
            <?php
                    }else{
            ?>
                        <option value="<?php echo $QuesID[$Count];?>" ondblclick='javascript:dispQues(<?php echo $QuesID[$Count];?>)'><?php echo $QuesName[$Count];?></option>
            <?php
                    }
    	            $Count++;
		        }
                $question_count = $Count;
            ?>
            </SELECT>
            </td>
            <td colspan ="1" height="100" width ="50%" bgcolor="#ffffff">
	            <div id="questionq">
			            問題の情報が出力されます。
	            </div>
            </td>
        </tr></table>
        <input type="submit" name="exe" value="履歴参照" class="btn_mini2">
        <input type="submit" name="StartTimeQ" value="問題別情報" class="btn_mini2">
    </form>
    </div>
</div>
<script type="text/javascript">
ChangeTab('tab1');
</script>
<!--div class = "rireki"が軌跡再現のclass-->


<!--
<canvas id="canvasTF" width="500" height="500">
        ※表示にはcanvas要素を解釈可能なブラウザが必要です。
</canvas>
-->

<div id ="chart-js-graph">
    <div id = "answer_quesWID">
        <!--ここは解答した問題番号一覧-->
            <div class= "ans_quesWID">
            <form action = "mousemove.php" method="post" target="_blank">
                <select name = "datalist" id = "WIDques" size = 14>
                </select>
                <input type="submit" value="軌跡再現"  class="btn_mini2">
            </form>

            </div>
                
    </div>
    <div id ="grammar-All">
                ここは文法項目
    </div>

    <div id ="level-All">
                ここは難易度
    </div>

    <div id ="accuracy-All">
                ここは正解率，迷い率
    </div>

    <div id = "accuracy-rank">
        <!--ここは正答率上位文法項目-->
        <div class = "tablerank">
            <table border="1" id="tableaccuracy">
                <tr>
                    <th>順位</th>
                    <th>文法項目</th>
                    <th>正解率</th>
                </tr>
            </table>
        </div>

    </div>



    <div id = "hesitate-rank">
        <!--ここは迷い上位文法項目-->
        <div class = "tablerank">
            <table border="1" id="tablehesitate">
                <tr>
                    <th>順位</th>
                    <th>文法項目</th>
                    <th>迷い率</th>
                </tr>
            </table>
        </div>

    </div>
            
</div>


<script>

</script>






<div class="rireki">
<form action = "mousemove.php" method="post" target="_blank">
    <SELECT NAME="datalist"　id = "rirekidatalist" SIZE=30  style="width:300px">
    <?php
        echo "sql3:".$sql3."<br>";
	    $res3 = mysqli_query($conn,$sql3) or die("接続エラー3");
        $Count = 0;
        if($_SESSION["mark"] == "part"){
            while($row3 = $res3 -> fetch_assoc()){
                $sql_point = "select Point from trackdata where UID =".$row3["UID"]." and WID=".$row3["WID"];
                $res_point = mysqli_query($conn,$sql_point) or die("接続エラーq");
                $row_point = $res_point -> fetch_assoc();
  	            $DataName[$Count] = $row3["UID"]." (".$row3["WID"].") "." ".$row3["Date"]."  ".$row_point["Point"]."点  ".$row3["Time"];
   	            $Pass_ID[$Count] = $row3["UID"].",".$row3["WID"];
                if($Count == 0){
   	?>
                    <option value="<?php echo $Pass_ID[$Count];?>" selected><?php  echo $DataName[$Count];?></option>
    <?php
                }else{
    ?>
                    <option value="<?php echo $Pass_ID[$Count];?>"><?php  echo $DataName[$Count];?></option>
    <?php
                }
                $Count++;
	        }
        }else{
            while($row3 = $res3 -> fetch_assoc()){
                if($row3["TF"] == 1){
                    $row3["TF"] = "○";
                }else{
                    $row3["TF"] = "×";
                }
  	            $DataName[$Count] = $row3["UID"]." (".$row3["WID"].") "." ".$row3["Date"]."  ".$row3["TF"]." ".$row3["Time"];
                $Pass_ID[$Count] = $row3["UID"].",".$row3["WID"];
   	            if($Count == 0){
    ?>
                    <option value="<?php echo $Pass_ID[$Count];?>" selected><?php  echo $DataName[$Count];?></option>
    <?php
                }else{
    ?>
                    <option value="<?php echo $Pass_ID[$Count];?>"><?php  echo $DataName[$Count];?></option>
    <?php
                }
                $Count++;
            }
        }
        $data_count =$Count;
	?>
    </SELECT>
    <input type="submit" value="軌跡再現"  class="btn_mini2">
</form>
</div>
<div class="count">
    <font size="5">
    ■学習者：<?php echo $student_count;?>人　
    ■問題：<?php echo $question_count;?>問 
    ■履歴データ数：<?php echo $data_count;?>問
    </font>
    <a href="../main.html" class="btn_yellow" style="width: 100px;	height: 25px;">トップへ</a>
    <br>
    <?php
        if(isset($_SESSION["mark"])){
            if($_SESSION["mark"] =="part"){
    ?>
            <form action = "main.php" method="post" style="display: inline">
                <input type="hidden" name="mark" value="all">
                <input type="submit" value="正誤表示に変更する" class="btn_mini">
            </form>
    <?php
            }else{ 
    ?>
            <form action = "main.php" method="post" style="display: inline">
                <input type="hidden" name="mark" value="part">
                <input type="submit" value="得点表示に変更する" class="btn_mini">
            </form>
    <?php
            }
        }
    ?>
    <form action = "main.php" method="post" style="display: inline">
        <input type="submit" name="exe" value="絞り込みを初期に戻す" class="btn_mini">
    </form>
    <br><br>
    <form action = "correl.php" method="post" target="_blank">
        <input name="correl" type="radio" value="student" checked>学習者
        <input name="correl" type="radio" value="ques">問題
        <input name="correl" type="radio" value="data">履歴データ
            <br>
        <input type="hidden" name="term_s" value="<?php echo $term_s;?>">
        <input type="hidden" name="term_q" value="<?php echo $term_q;?>">
        <input type="hidden" name="term_r" value="<?php echo $term_r;?>">           
        <input type="hidden" name="student_count" value="<?php echo $student_count;?>">
        <input type="hidden" name="question_count" value="<?php echo $question_count;?>">
        <input type="hidden" name="data_count" value="<?php echo $data_count;?>">       
        <input type="submit" name="Submit" value="相関分析" class="btn_mini2">
    </form>
    <form action = "cluster.php" method="post" target="_blank">
        <input name="correl" type="radio" value="student" checked>学習者
        <input name="correl" type="radio" value="ques">問題
            <br>
        <input type="hidden" name="term_r" value="<?php echo $term_r;?>"> 
        <?php 
        if(isset($term_r)) {echo $term_r;}?>
        <input type="hidden" name="student_count" value="<?php echo $student_count;?>">
        <input type="hidden" name="question_count" value="<?php echo $question_count;?>">
        <input type="hidden" name="data_count" value="<?php echo $data_count;?>">  
        <input type="submit" name="Submit" value="クラスタリング" class="btn_mini2">
    </form>
</div>
<DIV class="search">
    <div align="left">
        <?php
            echo "学習者検索条件".$term_stu."<br><br>";
            echo "問題検索条件".$term_ques."<br><br>";
            echo "履歴データ条件".$term_rireki."<br><br>";
        ?>
    </div>
    <form action = "main.php" method="post" name="form1">
    <div align="left">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <A href="javaScript:treeMenu('treeMenu1')" >■ 学習者検索</a><br>
        <DIV id="treeMenu1" style="display:none">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
            if($commando_s1 =="1"){
            ?>
                <input type="checkbox" name="commando_s1" value="1" checked>
            <?php
            }else{    
            ?>
                <input type="checkbox" name="commando_s1" value="1">
            <?php
            }    
            ?>
            ┣ <A href="javaScript:treeMenu('treeMenu2')">・対象学習者選択</A><BR>
            <DIV id="treeMenu2" style="display:none">
                <font size= "1">※対象学習者を選択してください。(複数チェック可)<br><br></font>
                <?php
                    $student_sql = "select UID from member ORDER BY UID;";
                    $student_res = mysqli_query($conn,$student_sql) or die("接続エラー");
                    $num = 0;
                    //問題情報をテーブルで表示する
                    while ($student_row = $student_res -> fetch_assoc()){
                ?>
                
                        <input type="checkbox" name="stu[]" value="<?php echo $student_row["UID"]; ?>" 
                        <?php 
                            if(isset($stu)) {
                                if(is_numeric(array_search($student_row["UID"], $stu))){
                                    echo "checked";
                                } 
                            }
                            
                        ?> >
                        <?php 
                            echo $student_row["UID"]; 
                        ?>
                <?php
                        $num++;
                        if($num %5 == 0){
                            echo "<br>";
                        }
                    }
                ?>
                <input type="button" name="button" value="全て選択" onclick="allcheck('form1', 'stu[]', true);" />
                <input type="button" name="button" value="全て解除" onclick="allcheck('form1', 'stu[]', false);" />
                <br><br>
            </div>
            <?php
                if(isset($_SESSION["mark"])){
                    if($_SESSION["mark"] == "part"){
                
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                    if($commando_s2 == 2){
            ?>
                    <input type="checkbox" name="commando_s2" value="2"checked>
            <?php
                    }else{        
            ?>
                    <input type="checkbox" name="commando_s2" value="2">
            <?php
                    }
            ?>
            ┣<A href="javaScript:treeMenu('treeMenu3')">・得点率</A><br>
            <DIV id="treeMenu3" style="display:none">
　               <font size= "1">※数字で入力してください(0～10点)<br><br></font>
                 &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="minpoint_s" value="<?php echo $minpoint_s;?>">～
                 <b>上限</b><input type="text" size="8" name="maxpoint_s" value="<?php echo $maxpoint_s;?>"><br><br>
            </div>
            <?php
                }else{
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                    if($commando_s2 == 2){
            ?>
                    <input type="checkbox" name="commando_s2" value="2"checked>
            <?php
                    }else{        
            ?>
                    <input type="checkbox" name="commando_s2" value="2">
            <?php
                    }
            ?>
            ┣<A href="javaScript:treeMenu('treeMenu3')">・正解率</A><br>
            <DIV id="treeMenu3" style="display:none">
                        <font size= "1">※数字で入力してください(0～100%)<br><br></font>
                        &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mincorrect_s" value="<?php if(isset($mincorrect_s)){echo $mincorrect_s;}?>">～
                        <b>上限</b><input type="text" size="8" name="maxcorrect_s" value="<?php if(isset($maxcorrect_s)){echo $maxcorrect_s;}?>" ><br><br>
                    </div>
            <?php
                }   
            }    
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if($commando_s3 =="3"){
            ?>
                    <input type="checkbox" name="commando_s3" value="3" checked>
            <?php
                }else{
            ?>
                    <input type="checkbox" name="commando_s3" value="3">
            <?php    
                }
            ?>
            ┣<A href="javaScript:treeMenu('treeMenu4')">・平均解答時間</a><br>
            <DIV id="treeMenu4" style="display:none">
                    <font size= "1">※数字で入力してください(単位：秒)<br><br></font>
                    &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mintime_s" value="<?php if(isset($mintime_s)){echo $mintime_s;}?>">～
                    <b>上限</b><input type="text" size="8" name="maxtime_s" value="<?php if(isset($maxtime_s)){echo $maxtime_s;}?>"><br><br>
                </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if($commando_s4 =="4"){
            ?>
                    <input type="checkbox" name="commando_s4" value="4" checked>
            <?php
                }else{
            ?>
                    <input type="checkbox" name="commando_s4" value="4">
            <?php
                }
            ?>
            ┣<A href="javaScript:treeMenu('tree_answernum')">・解答数</a><br>
            <DIV id="tree_answernum" style="display:none">
                    <font size= "1">※数字で入力してください(単位：問)<br><br></font>
                    &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="minanswernum_s" value="<?php if(isset($minanswernum_s)){echo $minanswernum_s;}?>">～
                    <b>上限</b><input type="text" size="8" name="maxanswernum_s" value="<?php if(isset($maxanswernum_s)){echo $maxanswernum_s;}?>" ><br><br>
                </div>
        </div>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <A href="javaScript:treeMenu('treeMenu5')">■ 問題検索</a><br>
        <DIV id="treeMenu5" style="display:none">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                if($commando_q1 == "1"){
                ?>
                    <input type="checkbox" name="commando_q1" value="1"checked>
                <?php
                }else{        
                ?>
                    <input type="checkbox" name="commando_q1" value="1">
                <?php
                }
                ?>
                ┣ <A href="javaScript:treeMenu('treeMenu6')">・対象問題選択</A><BR>
                <DIV id="treeMenu6" style="display:none">
                    <font size= "1">※対象問題を選択してください。(複数チェック可)<br><br></font>
                    <?php
                        $sentence_sql = "select WID from question_info ORDER BY WID;";
                        $sentence_res = mysqli_query($conn,$sentence_sql) or die("接続エラー");
                        $num = 0;
                        //問題情報をテーブルで表示する
                        while ($sentence_row = $sentence_res -> fetch_assoc()){
                    ?>
                            <input type="checkbox" name="sent[]" value="
                            <?php 
                                echo $sentence_row["WID"]; 
                            ?>"
                            <?php 
                                if(isset($sent)){
                                    if(is_numeric(array_search($sentence_row["WID"], $sent))){
                                        echo "checked";
                                    } 
                                }
                                
                            ?> >
                            <?php 
                                echo $sentence_row["WID"]; 
                            ?>
                    <?php
                          $num++;
                          if($num %5 == 0){
                              echo "<br>";
                          }
                    }
                    ?>
                            <br><br>
                </div>
                <?php
                if(isset($_SESSION["mark"])){
                    if($_SESSION["mark"] == "part"){
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if($commando_q2 == "2"){
                ?>
                        <input type="checkbox" name="commando_q2" value="2"checked>
                <?php
                    }else{        
                ?>
                        <input type="checkbox" name="commando_q2" value="2">
                <?php
                    }
                ?>
                ┣<A href="javaScript:treeMenu('treeMenu7')">・得点率</A><br>
                <DIV id="treeMenu7" style="display:none">
                    <font size= "1">※数字で入力してください(0～10点)<br><br></font>
                    &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="minpoint_q" value="<?php if(isset($minpoint_q)){echo $minpoint_q;}?>">～
                    <b>上限</b><input type="text" size="8" name="maxpoint_q" value="<?php if(isset($maxpoint_q)){echo $maxpoint_q;}?>"><br><br>
                </div>
                <?php
                    }else{
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if($commando_q2 == "2"){
                ?>
                        <input type="checkbox" name="commando_q2" value="2"checked>
                <?php
                    }else{        
                ?>
                        <input type="checkbox" name="commando_q2" value="2">
                <?php
                    }
                ?>
                ┣<A href="javaScript:treeMenu('treeMenu7')">・正解率</A><br>
                <DIV id="treeMenu7" style="display:none">
                            <font size= "1">※数字で入力してください(0～100%)<br><br></font>
                            &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mincorrect_q" value="<?php echo $mincorrect_q;?>">～
                            <b>上限</b><input type="text" size="8" name="maxcorrect_q" value="<?php echo $maxcorrect_q;?>"><br><br>
                        </div>
                <?php
                    }
                }
                ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                if($commando_q3 == "3"){
                ?>
                        <input type="checkbox" name="commando_q3" value="3"checked>
                <?php
                }else{        
                ?>
                        <input type="checkbox" name="commando_q3" value="3">
                <?php
                }
                ?>
                ┣<A href="javaScript:treeMenu('treeMenu8')">・平均解答時間</a><br>
                <DIV id="treeMenu8" style="display:none">
                        <font size= "1">※数字で入力してください(単位：秒)<br><br></font>
                        &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mintime_q" value="<?php if(isset($mintime_q)){echo $mintime_q;}?>">～
                        <b>上限</b><input type="text" size="8" name="maxtime_q" value="<?php if(isset($maxtime_q)){echo $maxtime_q;}?>"><br><br>
                    </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if($commando_q4 == "4"){
                    ?>
                        <input type="checkbox" name="commando_q4" value="4"checked>
                <?php
                    }else{        
                    ?>
                        <input type="checkbox" name="commando_q4" value="4">
                <?php
                    }
                    ?>
                ┣ <A href="javaScript:treeMenu('treeMenu9')">・単語数</A><BR>
                <DIV id="treeMenu9" style="display:none">
                        <font size= "1">※単語数を入力してください(単位：語)<br><br></font>
                        &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="2" name="minword" value="<?php if(isset($minword)){echo $minword;}?>">～
                        <b>上限</b><input type="text" size="2" name="maxword" value="<?php if(isset($maxword)){echo $maxword;}?>"><br><br>
                    </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if($commando_q5 == "5"){
                    ?>
                        <input type="checkbox" name="commando_q5" value="5"checked>
                <?php
                    }else{        
                    ?>
                        <input type="checkbox" name="commando_q5" value="5">
                <?php
                    }
                    ?>
                ┣ <A href="javaScript:treeMenu('treeMenu10')">・難易度</A><BR>
                <DIV id="treeMenu10" style="display:none">
                        <font size= "1">※難易度を選択してください(複数チェック可)<br><br></font>
                        &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="level[]" value="1" <?php if(isset($level)){if(is_numeric(array_search("1", $level))){echo "checked";}} ?>>初級
                        <input type="checkbox" name="level[]" value="2" <?php if(isset($level)){if(is_numeric(array_search("2", $level))){echo "checked";}} ?>>中級
                        <input type="checkbox" name="level[]" value="3" <?php if(isset($level)){if(is_numeric(array_search("3", $level))){echo "checked";}} ?>>上級
                        <br><br>
                    </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if($commando_q6 == "6"){
                    ?>
                        <input type="checkbox" name="commando_q6" value="6" checked>
                <?php
                    }else{        
                    ?>
                        <input type="checkbox" name="commando_q6" value="6">
                <?php
                    }
                    ?>
                ┣ <A href="javaScript:treeMenu('treeMenu11')">・文法項目</A><BR>
                <DIV id="treeMenu11" style="display:none"> 
                    <font size= "1">※文法項目を選択してください。(複数チェック可)<br><br></font>
                    <?php
                    $g_sql = "select Item from grammar ORDER BY GID;";
                    $grammarID = 1;
                    $g_res = mysqli_query($conn,$g_sql) or die("接続エラー");
                    $num = 0;
                    //問題情報をテーブルで表示する
                    while ($g_row = $g_res -> fetch_assoc()){
                    ?>
                        <input type="checkbox" name="grammar[]" value="<?php if(isset($grammarID)){echo $grammarID;} ?>" <?php if(isset($grammar)){if(is_numeric(array_search($grammarID, $grammar))){echo "checked";}} ?> ><?php echo $g_row["Item"]; ?>
                    <?php
	                    if($grammarID % 4 == 0){
		                    echo "<br>";
	                    }
                        $num++;
                        $grammarID++;
                    }
                    ?>
                    <br><br>
                    <input type="radio" name="radiobutton" value="AND"  checked>AND検索
                    <input type="radio" name="radiobutton" value="OR">OR検索
                </div>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if($commando_q7 == "7"){
                ?>
                        <input type="checkbox" name="commando_q7" value="7"checked>
                <?php
                    }else{        
                ?>
                        <input type="checkbox" name="commando_q7" value="7">
                <?php
                    }
                ?>
                ┗ <A href="javaScript:treeMenu('treeMenu12')">・単語検索</A><BR>
                <DIV id="treeMenu12" style="display:none">
                    <font size= "1">※検索する英単語を入力してください<br><br></font>
                    &nbsp;&nbsp;&nbsp;<input type="text" size="30" name="word" value="<?php if(isset($word)){echo $word;}?>"><br><br>
                </div>
            </div>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <A href="javaScript:treeMenu('treeMenu13')">■ 履歴データ検索</a><br>
        <DIV id="treeMenu13" style="display:none">
            <?php
                if(isset($_SESSION["mark"])){
                    if($_SESSION["mark"] == "part"){
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php 
                if(isset($commando_r1)){
                    if($commando_r1 =="1"){
            ?>
                        <input type="checkbox" name="commando_r1" value="1" checked>
            <?php
                    }else{    
            ?>
                        <input type="checkbox" name="commando_r1" value="1">
            <?php
                    }
                }    
            ?>
            <A href="javaScript:treeMenu('treeMenu14')">・得点</A><br>
            <DIV id="treeMenu14" style="display:none">
                <font size= "1">※数字で入力してください(0～10点)<br><br></font>
                &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="2" name="minpoint_r" value="<?php if(isset($minpoint_r)){echo $minpoint_r;}?>">～
                <b>上限</b><input type="text" size="2" name="maxpoint_r" value="<?php if(isset($maxpoint_r)){echo $maxpoint_r;}?>"><br><br>
            </div>
            <?php
                }else{
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if(isset($commando_r1)){
                    if($commando_r1 =="1"){
            ?>
                        <input type="checkbox" name="commando_r1" value="1" checked>
            <?php
                    }else{    
            ?>
                        <input type="checkbox" name="commando_r1" value="1">
            <?php
                    }
                }    
            ?>
            <A href="javaScript:treeMenu('treeMenu14')">・正誤</A><br>
            <DIV id="treeMenu14" style="display:none">
                <font size= "1">※正誤を選択してください<br><br></font>
                &nbsp;&nbsp;&nbsp;
                <input type="radio" name="truefalse" value="1" <?php if(isset($truefalse)){if($truefalse=="1"){echo "checked";}} ?>>正答
	            <input type="radio" name="truefalse" value="0" <?php if(isset($truefalse)){if($truefalse=="0"){echo "checked";}} ?>>誤答<br><br>
            </div>
            <?php
                }
            }
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r2 =="2"){
            ?>
                    <input type="checkbox" name="commando_r2" value="2" checked>
            <?php
                }else{    
            ?>
                    <input type="checkbox" name="commando_r2" value="2">
            <?php
                }    
            ?>
            ┣<A href="javaScript:treeMenu('treeMenu15')">・解答時間</A><br>
            <DIV id="treeMenu15" style="display:none">
                <font size= "1">※数字で入力してください(単位：秒)<br><br></font>
                &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="8" name="mintime_r" value="<?php if(isset($mintime_r)){echo $mintime_r;}?>">～
                <b>上限</b><input type="text" size="8" name="maxtime_r" value="<?php if(isset($maxtime_r)){echo $maxtime_r;}?>"><br><br>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r3 =="3"){
            ?>
                    <input type="checkbox" name="commando_r3" value="3" checked>
            <?php
                }else{    
            ?>
                    <input type="checkbox" name="commando_r3" value="3">
            <?php
                }
            ?>
            ┣<A href="javaScript:treeMenu('tree_term')">・期間</A><br>
            <DIV id="tree_term" style="display:none">
                <font size= "1">※数字で入力してください<br><br></font>
                &nbsp;&nbsp;&nbsp;<b>開始日</b><input type="text" size="4" name="year_s" value="<?php if(isset($year_s)){echo $year_s;}?>">年
                <input type="text" size="2" name="month_s" value="<?php if(isset($month_s)){echo $month_s;}?>">月
                <input type="text" size="2" name="dath_s" value="<?php if(isset($dath_s)){echo $dath_s;}?>">日
                <br>
                &nbsp;&nbsp;&nbsp;<b>終了日</b><input type="text" size="4" name="year_e" value="<?php if(isset($year_e)){echo $year_e;}?>">年
                <input type="text" size="2" name="month_e" value="<?php if(isset($month_e)){echo $month_e;}?>">月
                <input type="text" size="2" name="dath_e" value="<?php if(isset($dath_e)){echo $dath_e;}?>">日
                <br><br>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r4 =="4"){
            ?>
                    <input type="checkbox" name="commando_r4" value="4" checked>
            <?php
                }else{    
            ?>
                    <input type="checkbox" name="commando_r4" value="4">
            <?php
                }
            ?>
            ┣<A href="javaScript:treeMenu('treeMenu16')">・自信度</a><br>
            <DIV id="treeMenu16" style="display:none">
　　　　          <input type="checkbox" name="confidence[]" value="4"<?php if(isset($confidence)){if(is_numeric(array_search("4", $confidence))){echo "checked";}} ?>>自信がある(75%以上)<br>
　　　　          <input type="checkbox" name="confidence[]" value="3"<?php if(isset($confidence)){if(is_numeric(array_search("3", $confidence))){echo "checked";}} ?>>完全には自信がない(50～75%程度)<br>
　　　　          <input type="checkbox" name="confidence[]" value="2"<?php if(isset($confidence)){if(is_numeric(array_search("2", $confidence))){echo "checked";}} ?>>あまり自信がない(25～50%程度)<br>
　　　　          <input type="checkbox" name="confidence[]" value="1"<?php if(isset($confidence)){if(is_numeric(array_search("1", $confidence))){echo "checked";}} ?>>自信がない(25%未満)<br>
　　　　          <input type="checkbox" name="confidence[]" value="0"<?php if(isset($confidence)){if(is_numeric(array_search("0", $confidence))){echo "checked";}} ?>>誤って決定ボタンを押した<br>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r5 =="5"){
            ?>
                    <input type="checkbox" name="commando_r5" value="5" checked>
            <?php
                }else{
            ?>
                    <input type="checkbox" name="commando_r5" value="5">
            <?php
                }
            ?>
            ┣<A href="javaScript:treeMenu('tree_UTurn')">・Uターン回数X</A><br>
            <DIV id="tree_UTurn" style="display:none">
                <font size= "1">※数字で入力してください(単位：回)<br><br></font>
                &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="3" name="minUTurn_r" value="<?php if(isset($minUTurn_r)){echo $minUTurn_r;}?>">～
                <b>上限</b><input type="text" size="3" name="maxUTurn_r" value="<?php if(isset($maxUTurn_r)){echo $maxUTurn_r;}?>"><br><br>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r6 =="6"){
            ?>
                    <input type="checkbox" name="commando_r6" value="6" checked>
            <?php
                }else{
            ?>
                    <input type="checkbox" name="commando_r6" value="6">
            <?php
                }    
            ?>
            ┣<A href="javaScript:treeMenu('tree_DD')">・D&D回数</A><br>
            <DIV id="tree_DD" style="display:none">
                <font size= "1">※数字で入力してください(単位：回)<br><br></font>
                &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="3" name="minDD_r" value="<?php if(isset($minDD_r)){echo $minDD_r;}?>">～
                <b>上限</b><input type="text" size="3" name="maxDD_r" value="<?php if(isset($maxDD_r)){echo $maxDD_r;}?>"><br><br>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r7 =="7"){
            ?>
                    <input type="checkbox" name="commando_r7" value="7" checked>
            <?php
                }else{
            ?>
                    <input type="checkbox" name="commando_r7" value="7">
            <?php
                }    
            ?>
            ┣<A href="javaScript:treeMenu('tree_GroupingTF')">・グルーピング</A><br>
            <DIV id="tree_GroupingTF" style="display:none">
                <font size= "1">※有無を選択してください<br><br>※結果の表示には少し時間がかかります。<br><br></font>
                &nbsp;&nbsp;&nbsp;<input type="radio" name="GroupingTF"  value="1">あり
	            <input type="radio" name="GroupingTF" value="0">なし<br><br>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <?php
                if($commando_r8 =="8"){
            ?>
                    <input type="checkbox" name="commando_r8" value="8" checked>
            <?php
                }else{
            ?>
                    <input type="checkbox" name="commando_r8" value="8">
            <?php
                }    
            ?>
            ┣<A href="javaScript:treeMenu('tree_DDrev')">・D&D_rev回数</A><br>
            <DIV id="tree_DDrev" style="display:none">
                <font size= "1">※数字で入力してください(単位：回)<br><br></font>
                &nbsp;&nbsp;&nbsp;<b>下限</b><input type="text" size="3" name="minDDrev_r" value="<?php if(isset($minDDrev_r)){echo $minDDrev_r;}?>">～
                <b>上限</b><input type="text" size="3" name="maxDDrev_r" value="<?php if(isset($maxDDrev_r)){echo $maxDDrev_r;}?>"><br><br>
            </div>
    </DIV>
    </div>
    <input type="hidden" name="mode" value="3">
　　<input type="submit" value="絞り込み" class="btn_mini2">
    </form>
</div>
</div>
</body>
</html>