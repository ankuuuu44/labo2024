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
	<title>外れ値判定</title>
    <STYLE type="text/css">
<!--
.dataset {
  overflow: scroll;   /* スクロール表示 */
  width: 700px;
  height: 800px;
  background-colo: white;
  position: absolute;
  top: 30px;
  left: 20px;
}
.question {
  overflow: scroll;   /* スクロール表示 */
  width: 600px;
  height: 800px;
  background-color: white;
  position: absolute;
  top: 30px;
  left: 750px;
}
-->
</STYLE>
</head>
<body>

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
	
	// 共分散を求めるメソッド cov()
	 function cov($array1,$array2){
		
		// 対象配列の抽出
		$target1 = $array1;
		$target2 = $array2;
		
		// これは作業用変数
		$targetx = array();
		$targety = array();		
		$target = array();
		
		// 共分散 E[(X-E(X))(Y-E(Y))]により求められる

		// X-E(X)の算出
		$avex = ave($target1);
		for ($i=0; $i<count($target1); $i++ ){
			$targetx[$i] = $target1[$i] - $avex;
		}
		
		// Y-E(Y)の算出
		$avey = ave($target2);
		for ($i=0; $i<count($target2); $i++ ){
			$targety[$i] = $target2[$i] - $avey;
		}
		
		// (X-E(X))(Y-E(Y)) の算出
		for ($i=0; $i<count($target1) || $i<count($target2); $i++ ){
			$target[$i] = $targetx[$i] * $targety[$i];
		}
		
		// (X-E(X))(Y-E(Y)) の平均値をとって終了
		$result = ave($target);
		
		return $result;
	}
	
	// 相関係数を求めるメソッド cc()
	 function cc($array1,$array2){
		
		// 対象配列の抽出
		$target1 = $array1;
		$target2 = $array2;
		
		// 相関係数の求め方 (X,Yの共分散)/((Xの標準偏差)(Yの標準偏差))
		$cov = cov($target1,$target2);
		
		// 2つの配列のそれぞれの標準偏差を求める
		$sdx = sd($target1);
		$sdy = sd($target2);
		
		// 相関係数を求める際の分母を算出
		$tmp = $sdx * $sdy;
		
		if($tmp == 0){
			// 分母が0のときは、相関係数を算出できない
			$result = "Undefined";
		}else{
			// 分母はそれ以外の時は、相関係数の求め方に従って算出
			$result = $cov / $tmp;
		}
		
		return $result;
	}

    function array_isunique($array){

	if(!is_array($array)){
		return false;
	}

	$arrayValue = array_count_values($array);	//配列の値の数をカウントする
	$arraykey = array_keys($arrayValue,1);	//重複していない値のキーを取り出す
	
	for($i=0;$i<count($arraykey);$i++){
		unset($arrayValue[$arraykey[$i]]);	//重複していない要素を削除
        echo "d";
	}
	if(count($arrayValue)!=0){
        echo $arrayValue;
		return $arrayValue;
	}else{
		return false;
	}
}
    ?>

<DIV class="dataset">
<?php
    //echo $_SESSION["cmd"]."<br><br>";
    require "dbc.php";
    
    $DS_array =array();//DDStart保存用配列
    $WID_array =array(); //WID保存用配列
    $Time_array = array();//解答時間保存用配列
    $Answer_array = array();//解答文保存用配列
    $Check_array = array();//確認時間保存用配列
    $Label_array = array();
    $point_array = array();//得点保存用配列
    $Understand_array = array();//自信度保存用配列

    $AveSpeed = array();


    $AACount = array();
    $Understand_array =array();//自信度保存用
    $AveSpeed_info= array();
    $DDCount_info = array();
    $DDCount_rev_info = array();
    $UTurnCount_X_info = array();
    $UTutnCount_Y_info = array();
    $UTurnCount_info = array();
    $UID=array();



    $Stime = array();
    $Etime = array();
    $Time_interval = array();
    
    $Time = array();

    $data_count =0;

    $sql_ques ="select count(*) as cnt from question_info;";
    $res_ques = mysql_query($sql_ques,$conn) or die("接続エラー");
    $row_ques = mysql_fetch_array($res_ques,MYSQL_ASSOC);
    $ques_count = $row_ques["cnt"];


    if($_SESSION["cmd"]=="StartTime"){//DD開始時間の値保存
        //for($i=0;$i<$ques_count;$i++){
            $i=0;
            $sql_DS ="select * from trackdata where WID = ".$_SESSION["queslist"]." order by uid";
            $res_DS = mysql_query($sql_DS,$conn) or die("接続エラー");
            while($row_DS = mysql_fetch_array($res_DS)){
                $DS_array[$i] = $row_DS["DStartTime"];
                //echo $row_DS["DStartTime"];
                $WID_array[$i] = $row_DS["UID"];
                $point_array[$i] = $row_DS["point"];
                $AID = $row_DS["AID"];
                $UID[$i] = $row_DS["UID"];
                $AACount[$i] =$row_DS["DD_AA_Count"];
                $AveSpeed_info[$i] =$row_DS["AveSpeed"];
                $DDCount_info[$i] = $row_DS["DragDropCount"];
                $DDCount_rev_info[$i] = $row_DS["DragDropCount_rev"];
                $UTurnCount_X_info[$i] =$row_DS["UTurnCount_X"]+$row_DS["UTurnCount_XinDD"];
                $UTurnCount_Y_info[$i] =$row_DS["UTurnCount_Y"]+$row_DS["UTurnCount_YinDD"];
                $UTurnCount_info[$i] = $UTurnCount_X_info[$i] + $UTurnCount_Y_info[$i];
                //echo "←".$row_DS["AID"];
                //echo "<br>";
                $data_count++;
                $i++;
            }
            $i = 0;
            //$row_DS = mysql_fetch_array($res_DS,MYSQL_ASSOC);
            
        //}
        
        //for($i=0;$i<$ques_count;$i++){
            $sql_Time ="select * from linedata where WID = ".$_SESSION["queslist"]." order by uid";
            $res_Time = mysql_query($sql_Time,$conn) or die("接続エラー");
            while($row_Time = mysql_fetch_array($res_Time)){
                $Time_array[$i] = $row_Time["Time"];
                //$data_count++;
                $i++;
            }
        //}
        $i = 0;
        //for($i=0;$i<$ques_count;$i++){
            $sql_Answer ="select * from AnswerQues where AID = ".$AID." order by uid";
            $res_Answer = mysql_query($sql_Answer,$conn) or die("接続エラー");
            while($row_Answer = mysql_fetch_array($res_Answer)){
                $Answer_array[$i] = $row_Answer["EndSentence"];
                $Understand_array[$i] = 5 - $row_Answer["Understand"];
                //$data_count++;
                $i++;
            }
            
            for($i=0;$i<$data_count;$i++){
            $sql_last ="select MAX(Time) from linedatamouse where AID = ".$AID." and UID = ".$i;
            $res_last = mysql_query($sql_last,$conn) or die("接続エラー");
            while($row_last = mysql_fetch_array($res_last)){
                $Last_time = $row_last["MAX(Time)"];
                $Check_array[$i] = $Time_array[$i] - $Last_time;
                //$data_count++;
            }
        }
        

        //}
        

        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>UID</td>";
        //echo "<td>初動時間</td>";
        //echo "<td>解答時間</td>";
        
        echo "<td>解答文</td>";
        echo "<td>正誤</td>";
        echo "<td>自信度</td>";
        //echo "<td>値</td>";
        echo "<td>得点</td>";
        echo "<td>正誤(数字)</td>";
        echo "</tr>";
        
        for($i=0;$i<$data_count;$i++){
            echo "<tr>";
            echo "<td>".$WID_array[$i]."</td>";
            //echo "<td>".$DS_array[$i]."</td>";
            //echo "<td>".$Time_array[$i]."</td>";
            
            echo "<td>".$Answer_array[$i]."</td>";
            if($point_array[$i] == 10){
                echo "<td>○</td>";
            }else if($point_array[$i] == 0){
                echo "<td>×</td>";
            }else{
                echo "<td>△</td>";
            }

            echo "<td>".$Understand_array[$i]."</td>";

            echo "<td>".$point_array[$i]."</td>";
            if($point_array[$i] ==5){
                echo "<td>0</td>";
            }else{
                echo "<td>".$point_array[$i]."</td>";
            }



            //echo $DS_array[$i]."←".$WID_array[$i];
            $sample = ($DS_array[$i] - ave($DS_array))/ sd($DS_array);
              //echo "<td>".$sample."</td>";
            //echo "サンプル１".$border1."<br>";
            //echo "サンプル2".$border2."<br>";
            //echo "<td>".$sample."</td>";
            /*
            if(($sample<=-2) or ($sample>=2)){
                echo "<td>○</td>";
            }else{
                echo "<td></td>";
            }
            $sample2 = ($Time_array[$i] - ave($Time_array))/ sd($Time_array);
            if(($sample2<=-2) or ($sample2>=2)){
                echo "<td>■</td>";
            }else{
                echo "<td></td>";
            }
            */
            echo "</tr>";
            //echo "<br>";
        }
        echo "</table>";
        echo "<br><br>";
        
    }else if($_SESSION["cmd"]=="DD"){
        
        
        $DD_flag = 0;//DD中かどうかの判定用フラグ

        for($i=0;$i<$ques_count;$i++){
            $sql_DD ="select * from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$i." order by Time;";
            
            //echo $sql_DD."<br>";
            $res_DD = mysql_query($sql_DD,$conn) or die("接続エラー");
            
            while($row_DD = mysql_fetch_array($res_DD)){
                
                //echo $row_DD["X"]."<br>";
                if($row_DD["DD"] ==2){
                    $DD_flag = 1;//DD開始
                    $Start_time = $row_DD["Time"];//DD開始時間の記録
                    $Distance = 0;//総移動距離
                    $change = 0;//座標の変化量
                    $before_X = $row_DD["X"];//次回用にX座標の記録
                    $before_Y = $row_DD["Y"];//次回用にY座標の記録
                    $Label_array[$data_count] = $row_DD["Label"];            
                }
                if($DD_flag == 1){//DD中の時
                    $change_X = $row_DD["X"]-$before_X;
                    $change_Y = $row_DD["Y"]-$before_Y;
                    $change = sqrt(pow($change_X,2)+pow($change_Y,2));//座標の変化量
                    $Distance = $Distance + $change;//総距離の計算
                    $before_X = $row_DD["X"];
                    $before_Y = $row_DD["Y"];
                }
                if($row_DD["DD"] == 1){
                    $DD_flag = 0;//DD終了
                    $AveSpeed[$data_count] = $Distance/($row_DD["Time"]- $Start_time);
                    $AveSpeed[$data_count] = round($AveSpeed[$data_count],3);
                    $Stime[$data_count] = $Start_time;
                    $Etime[$data_count] = $row_DD["Time"];
                    $Time[$data_count] = $Etime[$data_count] - $Stime[$data_count];
                    $WID_array[$data_count] = $row_DD["AID"];
                    //echo "平均速度.$AveSpeed[$data_count]";
                    //echo "開始時間".$Stime[$data_count]."終了時間".$Etime[$data_count];
                    $data_count++;
                    //echo "<br>";
                }
                
            }
        }
?>



<?php
        $word_sub = array();
        $word_output = array();
        $word_num = array();
        $j = 0;

        $WID_record = array();
        $rec_count = 0;

        $word_array = array();
        $word_record = array();
        $record_num = 0;
        $memo = 0;
        echo "<table border=\"1\">";
echo "<tr>";
echo "<td>WID</td>";
echo "<td>開始</td>";
echo "<td>終了</td>";
echo "<td>DD平均速度</td>";
echo "<td>DD時間</td>";
echo "<td>DD間時間</td>";
echo "<td>単語</td>";
//echo "<td>値（平均速度）</td>";

//echo "<td>値（時間）</td>";
echo "<td>はずれ値（平均速度）</td>";
echo "<td>はずれ値（時間）</td>";
echo "<td>軌跡再生</td>";
echo "</tr>";


        for($i=0;$i<$data_count;$i++){
            $sql_word ="select * from quesorder where OID= ".($WID_array[$i]+1);
            if($i !=0 and $WID_array[$i] != $WID_array[$i-1]){
                //echo "問題変更";
                //echo $wdd;
                echo "<tr><td>"."-"."</td></tr>";
                $WID_record[$memo+1] = $WID_array[$i];
                $record_num = 0;
                $memo++;
                for($k = 0;$k<$j;$k++){
                    for($m =0;$m<$k;$m++){
                        if($word_sub[$m] == $word_sub[$k]){
                            //echo $word_array[$m]." ";
                            //echo "単語かぶり";
                        }
                        //echo $word_sub[$k]." ".$word_sub[$m]."<br>";
                    }
                }
                //echo "<br>";
                $j = 0;
            }else if ($i ==0){
                $WID_record[0] = $WID_array[$i];
                //$rec_count++;
            }

            $word_DD = $Label_array[$i];
            
            

            //$test_int = intval($Label_array[$i]);
            //$word_sub[$j] = $word_array[$test_int];
            //$word_output[$i] = $word_array[$test_int];
            $j++;
            //echo $sql_word;
            $res_word = mysql_query($sql_word,$conn) or die("接続エラーe");
            while($row_word = mysql_fetch_array($res_word)){
                    $ques_num = $row_word["WID"];
                }
            $sql_sentence ="select * from question_info where WID= ".$ques_num;
            //echo $sql_sentence;
            $res_sentence = mysql_query($sql_sentence,$conn) or die("接続エラーf");
            while($row_sentence = mysql_fetch_array($res_sentence)){
                    $divide = $row_sentence["start"];
                }
            $word_array[$WID_array[$i]] = explode("|",$divide);
            $word_DD2 = $word_DD;
            for($k =20;$k>=0;$k--){
                $word_DD = str_replace($k,$word_array[$WID_array[$i]][$k],$word_DD);
            }
            
            //echo $word_DD."<br>";

            //echo $word_array[0];
            //echo $word_array[5];
            echo "<tr>";
            
            echo "<td>".$ques_num."</td>";
            echo "<td>".$Stime[$i]."</td>";
            echo "<td>".$Etime[$i]."</td>";
            echo "<td>".$AveSpeed[$i]."</td>";
            echo "<td>".$Time[$i]."</td>";
            

            $Time_interval[$i] = $Stime[$i]- $Etime[$i-1];
            if($Time_interval[$i] <0){
                $Time_interval[$i] = $Stime[$i];
            }
            echo "<td>".$Time_interval[$i]."</td>";

            //echo $AveSpeed[$i]."←WID=".$ques_num." 時間".$Stime[$i]."～".$Etime[$i];
            $sample = ($AveSpeed[$i] - ave($AveSpeed))/ sd($AveSpeed);
            
            
            
            //echo "サンプル１".$border1."<br>";
            //echo "サンプル2".$border2."<br>";
            //echo " 単語".$test_int;
            //echo "<td>".$word_array[$test_int]."</td>";
            echo "<td>".$word_DD."</td>";
            /*
            $word_num[$ques_num][$memo] = $test_int;
            $word_output[$ques_num][$memo] = $word_array[$test_int];
            $memo++;
            */
            $word_output[$WID_array[$i]][$record_num] = $word_DD2;
            $record_num ++; //単語NO記録用フラグインクリメント
            //$wdd .=$word_array[$test_int];
            //echo "<td>".$word_output[$i]."</td>";
            
            
            //echo " 値(平均速度)";
            /*
            echo "<td>";
            echo round($sample,4);
            echo "</td>";
            */
            
            //echo " 時間".$Time[$i];
            $sample2 = ($Time[$i] - ave($Time))/ sd($Time);
            //echo " 値(時間)";
            /*
            echo "<td>";
            echo round($sample2,4);
            echo "</td>";
            */
            if(($sample<=-2) or ($sample>=2) ){
                echo "<td>○</td>";
                //echo "※はずれ値(平均速度)";
            }else{
                echo "<td></td>";
            }
            if(($sample2<=-2) or ($sample2>=2) ){
                echo "<td>■</td>";
                //echo "■はずれ値(時間)";
            }else{
                echo "<td></td>";
            }
            echo "<td>再生</td>";
            echo "</tr>";
            //echo "<br>";
            
        }

    echo "</table>";

    echo "<br><br>";

    }
     
?>
</div>
<DIV class="question">
<?php


    $output = array();
    $output_sub = array();
    $ques_memo =array();


    if($_SESSION["cmd"]=="StartTime"){//DD開始時間の値保存

        

        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>UID</td>";
        echo "<td>得点</td>";
        echo "<td>正誤(数字)</td>";
        echo "<td>自信度</td>";
        echo "<td>初動時間</td>";
        echo "<td>解答時間</td>";
        //echo "<td>決定後時間</td>";
        echo "<td>平均速度</td>";
        echo "<td>D&D回数</td>";
        echo "<td>D&D回数(補正)</td>";          
        echo "<td>A→A回数</td>";
        echo "<td>Uターン回数(X)</td>";
        echo "<td>Uターン回数(Y)</td>";        
        echo "<td>Uターン回数合計</td>";  
        //echo "<td>値</td>";
        
        echo "</tr>";
        
        for($i=0;$i<$data_count;$i++){
            echo "<tr>";
            echo "<td>".$WID_array[$i]."</td>";
            echo "<td>".$point_array[$i]."</td>";
            if($point_array[$i] ==5){
                echo "<td>0</td>";
            }else{
                echo "<td>".$point_array[$i]."</td>";
            }
            echo "<td>".$Understand_array[$i]."</td>";
            echo "<td>".$DS_array[$i]."</td>";
            echo "<td>".$Time_array[$i]."</td>";
            //echo "<td>".$Check_array[$i]."</td>";
            echo "<td>".$AveSpeed_info[$i]."</td>";
            echo "<td>".$DDCount_info[$i]."</td>";
            echo "<td>".$DDCount_rev_info[$i]."</td>";
            echo "<td>".$AACount[$i]."</td>";   
            echo "<td>".$UTurnCount_X_info[$i]."</td>";   
            echo "<td>".$UTurnCount_Y_info[$i]."</td>";  
            echo "<td>".$UTurnCount_info[$i]."</td>";  

            //echo $DS_array[$i]."←".$WID_array[$i];
            $sample = ($DS_array[$i] - ave($DS_array))/ sd($DS_array);
              //echo "<td>".$sample."</td>";
            //echo "サンプル１".$border1."<br>";
            //echo "サンプル2".$border2."<br>";
            //echo "<td>".$sample."</td>";

            echo "</tr>";
            //echo "<br>";
        }
        echo "</table>";
        echo "<br><br>";
        
    }else if($_SESSION["cmd"]=="DD"){
    echo "<table border=\"1\">";
    echo "<tr>";
    echo "<td>WID</td>";
    echo "<td>単語ごと移動回数</td>";
    echo "</tr>";
    /*
        for($i=0;$i<$data_count;$i++){
        if($i !=0 and $WID_array[$i] != $WID_array[$i-1]){
            
            echo "<td>".$WID_array[$i]."</td>";
        }
        }

        */
    for($i = 0;$i<=$memo;$i++){
        $j = 0;
        foreach($word_output[$i] as $value){
            $output_sub = explode("#",$value);
            foreach($output_sub as $value2){ 
                $output[$i][$j] = $value2;
                //$output[$i][$j] = str_replace($output[$i][$j],$word_array[$i][$output[$i][$j]]."(".$output[$i][$j].")",$output[$i][$j]);
                //echo $output[$i][$j]."<br>";
                $j++;
            }
        }
            sort($output[$i]);
            $j=0;
            foreach($output[$i] as $value2){ 
                //$output[$i][$j] = $value2;
                $output[$i][$j] = str_replace($output[$i][$j],$word_array[$i][$output[$i][$j]]."(".$output[$i][$j].")",$output[$i][$j]);
                //echo $output[$i][$j]."<br>";
                $j++;
            }
        
        $sql_word ="select * from quesorder where OID= ".($i+1);
        $res_word = mysql_query($sql_word,$conn) or die("接続エラーe");
            while($row_word = mysql_fetch_array($res_word)){
                    $ques_num = $row_word["WID"];
                }
        echo "<tr>";
        echo "<td>".$ques_num."</td>";
        echo "<td>";
        echo "<pre>";
        print_r(array_count_values($output[$i]));
        echo "</pre>";
        echo "</td>";
        echo "</tr>";
        //echo "<br><br>";
    }



    echo "</table>";
 }
    /*
    if($_SESSION["cmd"]=="DD"){
    echo "<table border=\"1\">";
    echo "<tr>";
    echo "<td>WID</td>";
    echo "<td>複数単語</td>";
    echo "</tr>";
    
    $sumword ="";
    $j = 0;
    for($i=0;$i<$data_count;$i++){
        if($i !=0 and $WID_array[$i] != $WID_array[$i-1]){
            echo "<tr>";
            echo "<td>".$WID_array[$i]."</td>";
            //echo "<td>".$word_output[$WID_array[$i]][2]."</td>";
            
            echo "<td>";
            //echo count($word_output[$WID_array[$i]]);
            //echo $word_output[$WID_array[$i]][0];
            echo $word_num[$WID_array[$i]][0].$word_num[$WID_array[$i]][1];
            echo "</td>";
            echo "</tr>";
            $sumword ="";
            $j = $i;
        }
        if($i !=0 and $WID_array[$i] == $WID_array[$i-1]){
            
        }
    }
    //array_isunique($word_output[1]);
    
    echo "</table>";
    }
    */
    
?>
</div>
<?php
$_SESSION["cmd"]="";
$_SESSION["studentlist"]="";    
?>


</body>
</html>