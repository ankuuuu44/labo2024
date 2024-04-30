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
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title></title>
    <STYLE type="text/css">
<!--

.hesitate_info {
  overflow: hidden;   /* スクロール表示 */
  width: 10px;
  height: 600px;
  background-color: white;
  position: absolute;
  top: 130px;
  left: 1500px;
}
-->
</STYLE>
</head>
<body>
<font size="5">
<?php
    echo "ユーザ: ".$_SESSION["studentlist"]." の解答情報<br><br>";
    require "dbc.php";
 

    $param = $_REQUEST["param"];
    $sort = $_REQUEST["sort"];

    if($param ==""){ $param = "AID";}
    if($sort ==""){ $sort ="ASC";}
    
?>
</font>

<?php
    	// 合計値を求めるメソッド sum()
	 function sum($array1){
		
		// 対象配列の抽出
		$target = $array1;
		
		// ここから合計値の計算
		$result = 0.0; // 合計値
		
		for ( $i=0; $i<count($target); $i++ ){
			$result += $target[$i];
		}
		
		return $result;	// 合計値を返して終了
		
	}
	
	// 平均値・期待値を求めるメソッド ave()
	 function ave($array1){
		
		// 対象配列の抽出
		$target = $array1;
		
		// 平均値の計算　配列の合計値を算出して、要素数で割る
		$sum = sum($target);
		if ( count($target)>0 ){
			$result = $sum / count($target);
		}else{
			$result = 0;
		}
		
		return $result;	
		
	}
	
	// 分散を求めるメソッド varp()
	 function varp($array1){
		
		// 対象配列の抽出
		$target = $array1;
		
		// 分散 E{X-(E(X))^2}　により求められる
		$ave = ave($target);
		
		$tmp; // 作業用変数
		
		// X-(E(X))^2 の値を入れておく配列
		$tmparray = array();
		
		// 配列の1要素ずつ、 (X-E(X))^2 を計算
		for ( $i=0; $i<count($target); $i++ ){
			$tmp = $target[$i] - $ave;		// X-E(X)
			$tmparray[$i] = $tmp * $tmp; 	// (X-E(X))^2
		}
		
		// 最後に、その平均値をもとめて終わり
		$result = ave($tmparray);
		
		return $result;
		
	}
	
	// 標準偏差を求めるメソッド sd()
	 function sd($array1){
		// 対象配列の抽出
		$target = $array1;
		
		// 標準偏差は分散の平方根により求められる
		$varp = varp($target);	// 分散の算出
		$result = sqrt($varp);			// その平方根をとる
		
		return $result;
		
	}

   

?>


<form name="navi" method="post" action="hesitate.php">
<select name="param">
<option value="AID" <?php echo $_REQUEST['param'] == "AID"? 'selected' : ''?>>解答順</option>
<option value="WID" <?php echo $_REQUEST['param'] == "WID"? 'selected' : ''?>>問題番号順</option>
<option value="Point" <?php echo $_REQUEST['param'] == "Point"? 'selected' : ''?>>得点順</option>
<option value="Understand"<?php echo $_REQUEST['param'] == "Understand"? 'selected' : ''?>>自信度順</option>
<option value="Time"<?php echo $_REQUEST['param'] == "Time"? 'selected' : ''?>>解答時間順</option>
<option value="AveSpeed" <?php echo $_REQUEST['param'] == "AveSpeed"? 'selected' : ''?>>平均速度順</option>
<option value="MaxStopTime" <?php echo $_REQUEST['param'] == "MaxStopTime"? 'selected' : ''?>>最大静止時間順</option>
<option value="DDCount" <?php echo $_REQUEST['param'] == "DDCount"? 'selected' : ''?>>DD回数順</option>
<option value="UTurnCount" <?php echo $_REQUEST['param'] == "UTurnCount"? 'selected' : ''?>>Uターン回数順</option>
<option value="Hesitate"<?php echo $_REQUEST['param'] == "Hesitate"? 'selected' : ''?>>迷い順</option>

</select>
<select name="sort">
<option value="asc" <?php echo $_REQUEST['sort'] == "asc"? 'selected' : ''?>>昇順</option>
<option value="desc" <?php echo $_REQUEST['sort'] == "desc"? 'selected' : ''?>>降順</option>
</select>
<input type="submit" name="Submit" value="OK">
</form>


<?php
    

    $WID_array =array(); //WID保存用配列
    $AID_array =array();
    $Time_array = array();//解答時間保存用配列
    $Answer_array = array();//解答文保存用配列
    $point_array = array();//得点保存用配列
    $AACount = array();
    $Understand_array =array();//自信度保存用
    $DS_array =array();//DDStart保存用配列
    $AveSpeed_info= array();
    $Distance_info = array();//総移動距離保存用
    $MaxStopTime_info =array();
    $MaxDCTime_info =array();
    $DDCount_info = array();
    $DDCount_rev_info = array();
    $UTurnCount_X_info = array();
    $UTurnCount_Y_info = array();
    $Sum_UTurnCount_info = array();
    $Label_count = array();

    $Hesitate_param = array();





    $sql_data ="select * from trackdata where UID = ".$_SESSION["studentlist"]." order by AID";
    
    $res_data = mysql_query($sql_data,$conn) or die("接続エラー");
    $data_count = 0;
    while($row_data = mysql_fetch_array($res_data)){
        $DS_array[$data_count] = $row_data["DStartTime"];
        $WID_array[$data_count] = $row_data["WID"];
        $AID_array[$data_count] = $row_data["AID"];
        $point_array[$data_count] =$row_data["point"];
        $MaxStopTime_info[$data_count]= $row_data["MaxStopTime"];
        $MaxDCTime_info[$data_count]= $row_data["MaxDCTime"];
        $AACount[$data_count] =$row_data["DD_AA_Count"];
        $RRCount[$data_count] =$row_data["DD_RR_Count"];
        $AveSpeed_info[$data_count] =$row_data["AveSpeed"];
        $Distance_info[$data_count] =$row_data["Distance"];
        $DDCount_info[$data_count] = $row_data["DragDropCount"];
        $DDCount_rev_info[$data_count] = $row_data["DragDropCount_rev"];
        $UTurnCount_X_info[$data_count] =$row_data["UTurnCount_X"]+$row_data["UTurnCount_XinDD"];
        $UTurnCount_Y_info[$data_count] =$row_data["UTurnCount_Y"]+$row_data["UTurnCount_YinDD"];
        //$Sum_UTurnCount_info[$data_count] = $UTurnCount_X_info[$data_count] + $UTurnCount_Y_info[$data_count];
        $data_count++;
    }
    $data_count=0;
    for($i=0;$i<=max($AID_array);$i++){
    $sql_Time ="select MAX(Time) from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID= ".$i;
    $res_Time = mysql_query($sql_Time,$conn) or die("接続エラー");
    while($row_Time = mysql_fetch_array($res_Time)){
        $Time_array[$data_count] = $row_Time["MAX(Time)"];
        $data_count++;
    }
    }
    $data_count=0;

    $sql_Answer ="select * from AnswerQues where UID = ".$_SESSION["studentlist"]." order by AID";
    $res_Answer = mysql_query($sql_Answer,$conn) or die("接続エラー");
    while($row_Answer = mysql_fetch_array($res_Answer)){
        $Answer_array[$data_count] = $row_Answer["EndSentence"];
        $Understand_array[$data_count] = 5 - $row_Answer["Understand"];
        $data_count++;
    }

    //$data_count=0;
    for($i=0;$i<=max($AID_array);$i++){

    $sql_Sentence ="select * from question_info where WID = ".$WID_array[$i].";";
    $res_Sentence = mysql_query($sql_Sentence,$conn) or die("接続エラー");
    $row_Sentence = mysql_fetch_array($res_Sentence);
    $Sentence = $row_Sentence["start"];
    $SLabel = explode("|",$Sentence);
    $Label_count[$i] =  count($SLabel);
    $UTurnCount_Y_info[$i] -= $Label_count[$i]*2-1;//UターンY軸の補正
    $Sum_UTurnCount_info[$i] = $UTurnCount_X_info[$i] + $UTurnCount_Y_info[$i];
    }

    for($i=0;$i<$data_count;$i++){
        $Dev_Time[$i] = round(($Time_array[$i] - ave($Time_array))*10 / sd($Time_array)+50,3);
        $Dev_Ave_Speed[$i] = round(100-(($AveSpeed_info[$i] - ave($AveSpeed_info))*10 / sd($AveSpeed_info)+50),3);
        $Dev_Distance[$i] = round(($Distance_info[$i] - ave($Distance_info))*10 / sd($Distance_info)+50,3);
        $Dev_DDrev[$i] = round(($DDCount_rev_info[$i] - ave($DDCount_rev_info))*10 / sd($DDCount_rev_info)+50,3);
        $Dev_UTurn[$i] = round(($Sum_UTurnCount_info[$i] - ave($Sum_UTurnCount_info))*10 / sd($Sum_UTurnCount_info)+50,3);
        $Dev_MaxStopTime[$i] = round(($MaxStopTime_info[$i] - ave($MaxStopTime_info))*10 / sd($MaxStopTime_info)+50,3);
        $Dev_MaxDCTime[$i] = round(($MaxDCTime_info[$i] - ave($MaxDCTime_info))*10 / sd($MaxDCTime_info)+50,3);
        $Dev_UX[$i] = round(($UTurnCount_X_info[$i] - ave($UTurnCount_X_info))*10 / sd($UTurnCount_X_info)+50,3);
        $Dev_UY[$i] = round(($UTurnCount_Y_info[$i] - ave($UTurnCount_Y_info))*10 / sd($UTurnCount_Y_info)+50,3);
        $Hesitate_param[$i] = 0;//必要な分迷い抽出用配列に0を入れておく
        //$Hesitate_param[$i] = $Dev_Time[$i] + (100-$Dev_Ave_Speed[$i]) + $Dev_DDrev[$i] + $Dev_UTurn[$i] + $Dev_MaxStopTime[$i];
    }
    
    foreach($Dev_Time as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }
       foreach($Dev_MaxDCTime as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }
      foreach($Dev_Ave_Speed as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }
     foreach($Dev_MaxStopTime as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }
    foreach($Dev_UY as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }
    foreach($Dev_UX as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }   
   foreach($Dev_Distance as $key => $value){
        if ($value >=60){
            $Hesitate_param[$key]++;
        }
    }            
   
    ?>
    <table border=\"1\">
    <tr>
    <td width="25">AID</td>
    <td width="25">WID</td>
    <td width="100">解答文</td>
    <td width="25">正誤</td>
    <td width="25">得点</td>
    <td width="25">自信度</td>
    <td width="40">解答時間</td>
    <td width="40">総移動距離</td>
    <td width="40">平均速度</td>
    <td width="40">最大静止時間</td>
    <td width="40">最大入れ替え間時間</td>
    <td width="25">D&D回数(補正)</td>
    <td width="25">A→A回数</td>
    <td width="25">Uターン回数(横軸)</td>
    <td width="25">Uターン回数(縦軸)</td>
    <td width="25">迷い値</td>
    <td width="25">単語数</td>
    <!--
    <td width="40">解答時間</td>
    <td width="40">平均速度</td>
    <td width="40">初動時間</td>
    <td width="25">D&D回数</td>
    <td width="25">D&D回数(補正)</td>
    <td width="25">A→A回数</td>
    <td width="25">Uターン回数(X軸)</td>
    <td width="25">Uターン回数(Y軸)</td>
    <td width="25">Uターン回数(合計)</td>
    
    -->
    <td width="50">軌跡再現</td>
    </tr>
    <?php
    
     function sort_alg($array1){
        global $order_array,$sort;
        $num = 0;
        if($sort == "asc"){ asort($array1);
        }else if($sort =="desc"){ arsort($array1);}

        foreach($array1 as $key => $value){
            $order_array[$num] = $key;
            $num++;
        }
    }
        
    
    function DevJudge($element){
        if($elemet >=60){
            return 1;
        }else{
            return 0;
        }
    }
        
    $order_array = array();

    if($param == "AID"){
        sort_alg($AID_array);
    }else if($param == "WID"){
        sort_alg($WID_array);
    }else if($param == "Point"){
        sort_alg($point_array);
    }else if($param == "Understand"){
        sort_alg($Understand_array);
    }else if($param == "Time"){
        sort_alg($Time_array);
    }else if($param == "AveSpeed"){
        sort_alg($AveSpeed_info);
    }else if($param == "MaxStopTime"){
        sort_alg($MaxStopTime_info);
    }else if($param == "DDCount"){
        sort_alg($DDCount_rev_info);
    }else if($param == "UTurnCount"){
        sort_alg($Sum_UTurnCount_info);
    }else if($param == "Hesitate"){
        sort_alg($Hesitate_param);
    }

    for($i=0;$i<$data_count;$i++){
        echo "<tr>";
        echo "<td>".($AID_array[$order_array[$i]]+1)."</td>";//AID
        echo "<td>".$WID_array[$order_array[$i]]."</td>";//AID
        echo "<td>".$Answer_array[$order_array[$i]]."</td>";//解答文

        if($point_array[$order_array[$i]] == 10){//正誤
            echo "<td>○</td>";
        }else if($point_array[$order_array[$i]] == 0){
            echo "<td>×</td>";
        }else{
            echo "<td>△</td>";
        }

        echo "<td>".$point_array[$order_array[$i]]."</td>";//得点
        echo "<td>".$Understand_array[$order_array[$i]]."</td>";//自信度

        if(DevJudge($Dev_Time[$order_array[$i]])==1){
            echo "<td bgcolor ='#ffffc0'>".$Dev_Time[$order_array[$i]]."</td>";//解答時間
        }else{
            echo "<td>".$Dev_Time[$order_array[$i]]."</td>";//解答時間
        }

        echo "<td>".$Dev_Distance[$order_array[$i]]."</td>";//総移動距離
        echo "<td>".$Dev_Ave_Speed[$order_array[$i]]."</td>";//平均速度
        echo "<td>".$Dev_MaxStopTime[$order_array[$i]]."</td>";//最大静止時間
        echo "<td>".$Dev_MaxDCTime[$order_array[$i]]."</td>";//最大入れ替え間時間
        echo "<td>".$DDCount_rev_info[$order_array[$i]]."</td>";//DD回数（補正）
        echo "<td>".($AACount[$order_array[$i]]+$RRCount[$order_array[$i]])."</td>";//AA回数＋同レジスタ内入れ替え回数
        echo "<td>".($Dev_UX[$order_array[$i]] )."</td>";//Ｕターン回数（X)
        echo "<td>".($Dev_UY[$order_array[$i]])."</td>";//Ｕターン回数（Y)
        echo "<td>".$Hesitate_param[$order_array[$i]]."</td>";//迷いポイント（仮）
        echo "<td>".$Label_count[$order_array[$i]]."</td>";//単語数
        /*
        echo "<td>".$Time_array[$i]."</td>";//解答時間
        echo "<td>".$AveSpeed_info[$i]."</td>";//平均速度
        echo "<td>".$DS_array[$i]."</td>";//初動時間
        echo "<td>".$DDCount_info[$i]."</td>";//DD回数
        echo "<td>".$DDCount_rev_info[$i]."</td>";//DD回数(補正)
        echo "<td>".$AACount[$i]."</td>";//AA回数
        echo "<td>".$UTurnCount_X_info[$i]."</td>";//DD回数(X)
        echo "<td>".$UTurnCount_Y_info[$i]."</td>";//DD回数(Y)
        echo "<td>".$Sum_UTurnCount_info[$i]."</td>";//DD回数(合計)
        */
        echo "<td><a href=\"mousemove.php?UID=".$_SESSION["studentlist"]."&WID=".$WID_array[$order_array[$i]]."&AID=".$AID_array[$order_array[$i]]."\" target=_blank>軌跡再現</td>";
        echo "</tr>";
    }

    echo "</table>";
?>

<div class="hesitate_info">
    
    </br>
    <?php
        /*
        $AID = 7;
        $WID = 43;
        $AA_Flag =0;
        $Label ="";
        $Label_num =0;
        $AALabel = array();

        $sql_Sentence ="select * from question_info where WID = ".$WID.";";
        $res_Sentence = mysql_query($sql_Sentence,$conn) or die("接続エラー");
        $row_Sentence = mysql_fetch_array($res_Sentence);
        $Sentence = $row_Sentence["start"];
        $SLabel = explode("|",$Sentence);
        

        $sql_DD ="select * from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$AID." order by Time;";
        $res_DD = mysql_query($sql_DD,$conn) or die("接続エラー");

        while($row_DD = mysql_fetch_array($res_DD)){
            if($row_DD["DD"] ==2){
                $Label = $row_DD["Label"];
                if($row_DD["Y"] > 150 and $row_DD["Y"]<= 240){ 
                     $AA_Flag = 1; 
                }//解答欄
            }else if($row_DD["DD"] ==1){
                if ($AA_Flag ==1 and ($row_DD["Y"] > 150 and $row_DD["Y"]<= 240)){
                    $AALabel[$Label_num] = $Label;
                    $Label_num++;
                }
                $AA_Flag = 0;
            }
        }
        
        

        echo "問題番号: ".($AID_array[7]+1)."<br>";
        echo "解答欄内入れ替え: ";
        foreach($AALabel as $value){
            for($i = count($SLabel)-1;$i>=0;$i--){
                $value = str_replace($i,$SLabel[$i],$value);
                //echo $value;
            }
            echo $value."<br>";
        }
        echo "D→C間はずれ値:<br> ";
        echo "複数移動単語:<br> ";
        */
    ?>
</div>
</body>
</html>