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
  background-color: white;
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

function chitest($targetarray){
	
	// カイ2乗検定を行うプログラム
	$expectarray = array();
	
	// 期待値の計測を行う
	$sum = 0;
	for ( $i=0; $i<count($targetarray); $i++ ){
		$sum += $targetarray[$i];
	}
	
	$average = $sum / count($targetarray);
	for ( $i=0; $i<count($targetarray); $i++ ){
		$expectarray[$i] = $average;
	}
	
	// カイ2乗検定
	$chi = 0.0;
	for ( $i=0; $i<count($targetarray); $i++ ){
		$tmp = $targetarray[$i] - $expectarray[$i];
		$chi += ( $tmp * $tmp ) / $expectarray[$i];
	}
	
	//print_r($expectarray);
	//echo $chi."<br/>";
	
	$test = 0.0;
	$count = count($targetarray) - 1;
	
	if ( $count <= 10 ){
		$test = 18.30;
	}else if ( $count > 10 && $count <= 20 ){
		$test = 31.41;
	}else if ( $count > 20 && $count <= 30 ){
		$test = 43.77;
	}else if ( $count > 30 && $count <= 40 ){
		$test = 55.75;
	}else if ( $count > 40 && $count <= 50 ){
		$test = 67.50;
	}else if ( $count > 50 && $count <= 60 ){
		$test = 79.08;
	}else if ( $count > 60 && $count <= 70 ){
		$test = 90.53;
	}else if ( $count > 70 && $count <= 80 ){
		$test = 101.87;
	}else if ( $count > 80 && $count <= 90 ){
		$test = 113.14;
	}else if ( $count > 90 && $count <= 100 ){
		$test = 124.34;
	}else{
		$test = 135.48;
	}
	
	$result = 0;
	if ( $chi > $test ){
		$result = 1;
	}
	
	return $result;
}

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
        //echo "d";
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
    $Last_time = array();//解答時間保存用配列
    $Label_array = array();

    $Answer_array = array();//解答文保存用配列
    $Check_array = array();//確認時間保存用配列
    $Label_array = array();
    $Time2_array = array();
    $point_array = array();//得点保存用配列
    $AACount = array();
    $Understand_array =array();//自信度保存用
    $AveSpeed_info= array();
    $DDCount_info = array();
    $DDCount_rev_info = array();
    $UTurnCount_X_info = array();
    $UTurnCount_Y_info = array();
    $UTurnCount_info = array();
    $Distance_array = array();
    $MaxStop_array = array();
    $DStart_array = array();
    //$word_num = array();
    

    $AveSpeed = array();
    $Stime = array();
    $Etime = array();
    $Time_interval = array();
    $U_X=array_fill(0,1000,0);
    $U_Y=array_fill(0,1000,0);
    $DDU_X = array_fill(0,1000,0);//DD間Uターン回数のカウント
    $DDU_Y = array_fill(0,1000,0);//

    $Drag_posi =array();//ドラッグ座標保存用
    $Drop_posi =array();//ドロップ座標保存用

    $Time = array();

    $data_count =0;

    $sql_ques ="select count(*) as cnt from question_info;";
    $res_ques = mysql_query($sql_ques,$conn) or die("接続エラー");
    $row_ques = mysql_fetch_array($res_ques,MYSQL_ASSOC);
    $ques_count = $row_ques["cnt"];

?>
    <font size="4">
        <?php
        echo "ユーザID:".$_SESSION["studentlist"]."<br><br>";
        ?>
        </font>


<?php
    if($_SESSION["cmd"]=="StartTime"){//DD開始時間の値保存
        for($i=0;$i<$ques_count;$i++){
            $sql_DS ="select * from trackdata where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_DS = mysql_query($sql_DS,$conn) or die("接続エラー");
            while($row_DS = mysql_fetch_array($res_DS)){
                $DS_array[$i] = $row_DS["DStartTime"];
                //echo $row_DS["DStartTime"];
                $WID_array[$i] = $row_DS["WID"];
                $point_array[$i] =$row_DS["point"];
                $AACount[$i] =$row_DS["DD_AA_Count"];
                $RRCount[$i] =$row_DS["DD_RR_Count"];
                $AveSpeed_info[$i] =$row_DS["AveSpeed"];
                $Distance_array[$i] =$row_DS["Distance"];
                $DStart_array[$i] =$row_DS["DStartTime"];
                $MaxStop_array[$i] =$row_DS["MaxStopTime"];
                $MaxDCTime_array[$i] = $row_DS["MaxDCTime"];
                $DDCount_info[$i] = $row_DS["DragDropCount"];
                $DDCount_rev_info[$i] = $row_DS["DragDropCount_rev"];
                $UTurnCount_X_info[$i] =$row_DS["UTurnCount_X"]+$row_DS["UTurnCount_XinDD"];
                $UTurnCount_Y_info[$i] =$row_DS["UTurnCount_Y"]+$row_DS["UTurnCount_YinDD"];
                $UTurnCount_info[$i] = $UTurnCount_X_info[$i] + $UTurnCount_Y_info[$i];
                //echo "←".$row_DS["AID"];
                //echo "<br>";
                $data_count++;
            }
            //$row_DS = mysql_fetch_array($res_DS,MYSQL_ASSOC);
            
        }

        for($i=0;$i<$ques_count;$i++){
            $sql_Answer ="select * from AnswerQues where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_Answer = mysql_query($sql_Answer,$conn) or die("接続エラー");
            while($row_Answer = mysql_fetch_array($res_Answer)){
                $Answer_array[$i] = $row_Answer["EndSentence"];
                $Understand_array[$i] = 5 - $row_Answer["Understand"];
                //$data_count++;
            }
        }

        for($i=0;$i<$ques_count;$i++){
            $sql_last ="select * from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_last = mysql_query($sql_last,$conn) or die("接続エラー");
            while($row_last = mysql_fetch_array($res_last)){
                //$Time_array[$i] = $row_last["MAX(Time)"];
                if($row_last["DD"] ==1){
                    $Last_time[$i] = $row_last["Time"];
                }
            }
        }

        for($i=0;$i<$ques_count;$i++){
            $sql_time ="select MAX(Time) from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_time = mysql_query($sql_time,$conn) or die("接続エラー");
            while($row_time = mysql_fetch_array($res_time)){
                //$Time_array[$i] = $row_last["MAX(Time)"];
                $Time_array[$i] = $row_time["MAX(Time)"];
            }
            $Check_array[$i] = $Time_array[$i] - $Last_time[$i];
            
        }                
                //$data_count++;
        ?>

        
        <?php
        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>WID</td>";
        //echo "<td>初動時間</td>";
        //echo "<td>解答時間</td>";
        //echo "<td>決定後時間</td>";
        echo "<td>解答文</td>";
        echo "<td>正誤</td>";
        echo "<td>自信度</td>";
        echo "<td>得点</td>";
        echo "<td>正誤(数字)</td>";
        echo "</tr>";
        
        for($i=0;$i<$data_count;$i++){
            echo "<tr>";
            echo "<td>".$WID_array[$i]."</td>";
            //echo "<td>".$DS_array[$i]."</td>";
            //echo "<td>".$Time_array[$i]."</td>";
            //echo "<td>".$Check_array[$i]."</td>";
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
            echo "</tr>";
            //echo "<br>";
        }
        echo "</table>";
        echo "<br><br>";
        
    }else if($_SESSION["cmd"]=="linedatamouse"){
        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>WID</td>";
        echo "<td>Time</td>";
        echo "<td>X</td>";
        echo "<td>Y</td>";
        echo "</tr>";
             for($i=0;$i<$ques_count;$i++){
            $sql_last ="select * from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_last = mysql_query($sql_last,$conn) or die("接続エラー");
            while($row_last = mysql_fetch_array($res_last)){

                            echo "<tr>";
            echo "<td>".$row_last["AID"]."</td>";
            echo "<td>".$row_last["Time"]."</td>";
            echo "<td>".$row_last["X"]."</td>";
            echo "<td>".$row_last["Y"]."</td>";
            echo "</tr>";
                //$data_count++;
            }
        }
        echo "</table>";








        
    }else if($_SESSION["cmd"]=="DD"){
        
        
        $DD_flag = 0;//DD中かどうかの判定用フラグ

        for($i=0;$i<$ques_count;$i++){
            
            $sql_DD ="select * from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$i." order by Time;";
            
            //echo $sql_DD."<br>";
            $res_DD = mysql_query($sql_DD,$conn) or die("接続エラー");
            
            $UTurnCount=0;
            $UTurnCount_XinDD=0;
            $UTurnCount_Y=0;
            $UTurnCount_YinDD=0;
            $UTurnFlag_X = 0;
            $UTurnFlag_Y = 0;
            $mouse_count = 0;
            $border = 20;//Uターン判定の基準値

            $move_distanceX = 0;
            $move_distanceY = 0;
            while($row_DD = mysql_fetch_array($res_DD)){
              
                
                if($row_DD["DD"] ==2){
                    /*
                    if($i ==5){
                    echo "<br><br>Drag<br>";
                    }
                    */
                    $DD_flag = 1;//DD開始
                    $Start_time = $row_DD["Time"];//DD開始時間の記録
                    $Distance = 0;//総移動距離
                    $change = 0;//座標の変化量
                    //$before_X = $row_DD["X"];//次回用にX座標の記録
                    //$before_Y = $row_DD["Y"];//次回用にY座標の記録
                    $Label_array[$data_count] = $row_DD["Label"];
                    $Time2_array[$data_count] = $row_DD["Time"];//時間記録用（最初に単語を動かした時間記録用）
                    $sub_Y = $row_DD["Y"]; 
                    if($row_DD["Y"] <= 130){
                        $Drag_posi[$data_count] ="Q";
                    }else if($row_DD["Y"] > 130 and $row_DD["Y"]<= 215){ $Drag_posi[$data_count] ="A"; }//解答欄
                    else if($row_DD["Y"] > 215 and $row_DD["Y"]<= 295){ $Drag_posi[$data_count] ="1"; }//レジスタ1 
                    else if($row_DD["Y"] > 295 and $row_DD["Y"]<= 375){ $Drag_posi[$data_count] ="2"; }//レジスタ2
                    else if($row_DD["Y"] > 375){ $Drag_posi[$data_count] ="3"; }//レジスタ3          
                }
                if($DD_flag == 1){//DD中の時
                    $change_X = $row_DD["X"]-$before_X;
                    $change_Y = $row_DD["Y"]-$before_Y;
                    $change = sqrt(pow($change_X,2)+pow($change_Y,2));//座標の変化量
                    $Distance = $Distance + $change;//総距離の計算


                    //$before_X = $row_DD["X"];
                    //$before_Y = $row_DD["Y"];
                }
                if($row_DD["DD"] == 1){
                    
                    $DD_flag = 0;//DD終了
                    $AveSpeed[$data_count] = $Distance/($row_DD["Time"]- $Start_time);
                    $AveSpeed[$data_count] = round($AveSpeed[$data_count],3);
                    $Stime[$data_count] = $Start_time;
                    $Etime[$data_count] = $row_DD["Time"];
                    $Time[$data_count] = $Etime[$data_count] - $Stime[$data_count];
                    $WID_array[$data_count] = $row_DD["AID"];

                    if($row_DD["Y"] <= 130){
                        $Drop_posi[$data_count] ="Q";
                    }else if($row_DD["Y"] > 130 and $row_DD["Y"]<= 215){ $Drop_posi[$data_count] ="A"; }//解答欄
                    else if($row_DD["Y"] > 215 and $row_DD["Y"]<= 295){ $Drop_posi[$data_count] ="1"; }//レジスタ1 
                    else if($row_DD["Y"] > 295 and $row_DD["Y"]<= 375){ $Drop_posi[$data_count] ="2"; }//レジスタ2
                    else if($row_DD["Y"] > 375){ $Drop_posi[$data_count] ="3"; }//レジスタ3  
                    //echo "平均速度.$AveSpeed[$data_count]";
                    //echo "開始時間".$Stime[$data_count]."終了時間".$Etime[$data_count];
                    $data_count++;
                    //echo "<br>";
                   /* 
                    if($i ==5){
                    echo "<br>Drop<br><br>";
                    }
                    */
                    
                }
                

                /*
                if($i==5){
                 echo "(".$row_DD["X"].",".$row_DD["Y"].")";
                }
                */

/*
 function UTurn_decision($DD_flag,$pos,$time){
        //UTurn_decision($UTurnFlag_X,$DD_flag,$UTurnCount_X,$UTurnCount_XinDD,$move_distanceX,$UTurn_TimeX);
        



           global $UTurnCount,$UTurnCountinDD,$UTurnFlag,$move_distance,
           $variate,$time_sub,$UTurn_Time,$point_sub;

            $move_distance = 0;
            $UTurnFlag = -1*$UTurnFlag;
            
            $UTurn_Time[$UTurnCount + $UTurnCountinDD] = $time_sub;
            //echo "Uターン座標".$point_sub."時間".$time_sub;
            if($DD_flag == 0){  
                //echo "UX";
                $UTurnCount++;
            }else if($DD_flag ==1){
                //echo "duX1";
                $UTurnCountinDD++;
            }
            $point_sub = $pos;
            $time_sub = $time;  

     }



     function UFlag1($DD_flag,$pos,$time){

         global $UTurnFlag,$move_distance,$variate,$time_sub,$point_sub,$border;


             if(($UTurnFlag * $variate) <0){
                 $move_distance +=  $variate;
                //echo "変化量".$move_distance;
                if(abs($move_distance) > $border){//変化量絶対値がborderを超えたらUターン
                    UTurn_decision($DD_flag,$pos,$time);
                 }
             }else if(($UTurnFlag * $variate) >0){
                 $move_distance +=  $variate;
                //echo "変化量".$move_distance;
                if(($move_distance * $UTurnFlag>0)){
                    $move_distance = 0;
                    $point_sub = $pos;//Uターン判定に使う座標を現在の座標に変更 
                    $time_sub = $time;                             
                } 
             }else{
                 if($variate !=0){
                 $point_sub = $pos;
                 $time_sub = $time;
                 
                 if($UTurnFlag ==0){
                     if($variate>0){
                        $UTurnFlag =1;
                     }else if($variate<0){
                        $UTurnFlag =-1;
                     }
                 }
                 }
                
             }
         
         
     }
     */




    /*
                  foreach($X_preserve as $X1){
                        
                        if($mouse_count != 0){
                            //echo "座標".$X1;
                            $variate = $X1 - $X2;
                            UFlag1($DD_flag_preserve[$mouse_count],$X1,$Time_preserve[$mouse_count]);
                        }
                        $X2 = $X1;
                        $mouse_count++;
                        //echo "<br>";
                    }

                    */

                
                if($mouse_count >0){
                 //if($i == 5){


                        if($UTurnFlag_X ==0){
                            if(($row_DD["X"] - $before_X)==0){
                            }else if(($row_DD["X"] - $before_X)>0){
                                $UTurnFlag_X =1;
                            }else if(($row_DD["X"] - $before_X)<0){
                                $UTurnFlag_X =-1;
                            }
          
                        }else if($UTurnFlag_X ==1){//X軸正方向の動きの時
                        
                            if(($row_DD["X"] - $before_X)> -1*($border) and ($row_DD["X"] - $before_X)<0){//-(基準値)<変化量<0
                                //echo "X維持";
                                
                                $move_distanceX = $move_distanceX +  ($row_DD["X"] - $before_X);
                                //echo $move_distanceX;
                                if($move_distanceX < -1 * $border){//変化量合計が負方向15を超えたらUターン
                                    $move_distanceX = 0;
                                    $UTurnFlag_X = -1;
                                    if($DD_flag == 0){  
                                    $U_X[$data_count]++;
                                    $UTurnCount++;
                                    //echo "刻みUターンX(+⇒-)";
                                    
                                    
                                    }else if($DD_flag ==1){
                                        $DDU_X[$data_count]++;
                                        $UTurnCount_XinDD++;
                                        //echo "刻みduX1(+⇒-)";
                                    }
                                }
                            }else if(($row_DD["X"] - $before_X)> 0 and $move_distanceX < 0){//変化が正方向でここまでの負方向の変化量より多くなったら
                                   $move_distanceX = $move_distanceX +  ($row_DD["X"] - $before_X);
                                    //echo $move_distanceX;
                                    if($move_distanceX >0){
                                        $move_distanceX = 0;                             
                                    }
                            }else if(($row_DD["X"] - $before_X) <=(-1)*$border){//変化量が負方向15pixel以上ならば
                            
                                $UTurnFlag_X = -1;
                                $move_distanceX = 0;
                                if($DD_flag == 0){
                                    $U_X[$data_count]++;
                                    $UTurnCount++;
                                    
                                    //echo "uX(+⇒-)";
                                }else if($DD_flag ==1){
                                    $DDU_X[$data_count]++;
                                    $UTurnCount_XinDD++;
                                    //echo "duX(+⇒-)";
                                }
                                
                            }
                            



                            
                        }else if($UTurnFlag_X ==-1){
                            if(($row_DD["X"] - $before_X)< $border and ($row_DD["X"] - $before_X)>0){//変化量が正方向のに基準値以下ならば
                                //echo "X維持";
                                
                                $move_distanceX = $move_distanceX +  ($row_DD["X"] - $before_X);
                                //echo $move_distanceX;
                                if($move_distanceX > $border ){//変化量合計が正方向15を超えたらUターン
                                    $move_distanceX = 0;
                                    $UTurnFlag_X = 1;

                                    if($DD_flag == 0){  
                                    $U_X[$data_count]++;
                                    $UTurnCount++;
                                    //echo "刻みUターンX(-⇒+)";
                                    
                                    
                                    }else if($DD_flag ==1){
                                        $DDU_X[$data_count]++;
                                        $UTurnCount_XinDD++;
                                        //echo "刻みduX(-⇒+)";
                                    }
                                }
                            }else if(($row_DD["X"] - $before_X)< 0 and $move_distanceX > 0){//変化が負方向でここまでの正方向の変化量より多くなったら
                                   
                                   $move_distanceX = $move_distanceX +  ($row_DD["X"] - $before_X);
                                    //echo $move_distanceX;
                                    if($move_distanceX <0){
                                        $move_distanceX = 0;                             
                                    }                        
                             
                            }else if(($row_DD["X"] - $before_X) >=$border){//変化量が正方向15pixel以上ならば
                            
                                $UTurnFlag_X = 1;
                                $move_distanceX = 0;
                                if($DD_flag == 0){
                                    $U_X[$data_count]++;
                                    $UTurnCount++;
                                    
                                    //echo "uX(-⇒+)";
                                }else if($DD_flag ==1){
                                    $DDU_X[$data_count]++;
                                    $UTurnCount_XinDD++;
                                    //echo "duX(-⇒+)";
                                }
                            }
   
                        }else{
                            echo "Uターン関連エラー";
                        }




                        if($UTurnFlag_Y ==0){
                            if(($row_DD["Y"] - $before_Y)==0){
                            }else if(($row_DD["Y"] - $before_Y)>0){
                                $UTurnFlag_Y =1;
                            }else if(($row_DD["Y"] - $before_Y)<0){
                                $UTurnFlag_Y =-1;
                            }
          
                        }else if($UTurnFlag_Y ==1){//Y軸正方向の動きの時
                        
                            if(($row_DD["Y"] - $before_Y)> -1*($border) and ($row_DD["Y"] - $before_Y)<0){//-(基準値)<変化量<0
                                //echo "Y維持";
                                
                                $move_distanceY = $move_distanceY +  ($row_DD["Y"] - $before_Y);
                                //echo "Y(".$move_distanceY.")";
                                if($move_distanceY < -1 * $border){//変化量合計が負方向15を超えたらUターン
                                    $move_distanceY = 0;
                                    $UTurnFlag_Y = -1;
                                    if($DD_flag == 0){  
                                    $U_Y[$data_count]++;
                                    $UTurnCount_Y++;
                                    //echo "刻みUターンY(+⇒-)";
                                    
                                    
                                    }else if($DD_flag ==1){
                                        $DDU_Y[$data_count]++;
                                        $UTurnCount_YinDD++;
                                       // echo "刻みduY1(+⇒-)";
                                    }
                                }
                            }else if(($row_DD["Y"] - $before_Y)> 0 and $move_distanceY < 0){//変化が正方向でここまでの負方向の変化量より多くなったら
                                   $move_distanceY = $move_distanceY +  ($row_DD["Y"] - $before_Y);
                                    //echo "Y(".$move_distanceY.")";
                                    if($move_distanceY >0){
                                        $move_distanceY = 0;                             
                                    }
                            }else if(($row_DD["Y"] - $before_Y) <=(-1)*$border){//変化量が負方向15pixel以上ならば
                            
                                $UTurnFlag_Y = -1;
                                $move_distanceY = 0;
                                if($DD_flag == 0){
                                    $U_Y[$data_count]++;
                                    $UTurnCount_Y++;
                                    
                                    //echo "uY(+⇒-)";
                                }else if($DD_flag ==1){
                                    $DDU_Y[$data_count]++;
                                    $UTurnCount_YinDD++;
                                    //echo "duY(+⇒-)";
                                }
                                
                            }
                            



                            
                        }else if($UTurnFlag_Y ==-1){
                            if(($row_DD["Y"] - $before_Y)< $border and ($row_DD["Y"] - $before_Y)>0){//変化量が正方向のに基準値以下ならば
                                //echo "Y維持";
                                
                                $move_distanceY = $move_distanceY +  ($row_DD["Y"] - $before_Y);
                                //echo "Y(".$move_distanceY.")";
                                if($move_distanceY > $border ){//変化量合計が正方向15を超えたらUターン
                                    $move_distanceY = 0;
                                    $UTurnFlag_Y = 1;

                                    if($DD_flag == 0){  
                                    $U_Y[$data_count]++;
                                    $UTurnCount_Y++;
                                    //echo "刻みUターンY(-⇒+)";
                                    
                                    
                                    }else if($DD_flag ==1){
                                        $DDU_Y[$data_count]++;
                                        $UTurnCount_YinDD++;
                                        //echo "刻みduY(-⇒+)";
                                    }
                                }
                            }else if(($row_DD["Y"] - $before_Y)< 0 and $move_distanceY > 0){//変化が負方向でここまでの正方向の変化量より多くなったら
                                   
                                   $move_distanceY = $move_distanceY +  ($row_DD["Y"] - $before_Y);
                                    //echo "Y(".$move_distanceY.")";
                                    if($move_distanceY <0){
                                        $move_distanceY = 0;                             
                                    }                        
                             
                            }else if(($row_DD["Y"] - $before_Y) >=$border){//変化量が正方向15pixel以上ならば
                            
                                $UTurnFlag_Y = 1;
                                $move_distanceY = 0;
                                if($DD_flag == 0){
                                    $U_Y[$data_count]++;
                                    $UTurnCount_Y++;
                                    
                                    //echo "uY(-⇒+)";
                                }else if($DD_flag ==1){
                                    $DDU_Y[$data_count]++;
                                    $UTurnCount_YinDD++;
                                    //echo "duY(-⇒+)";
                                }
                            }
   
                        }else{
                            echo "Uターン関連エラー";
                        }




                        //echo "<br>";
               //}
                

                }




                    $before_X = $row_DD["X"];
                    $before_Y = $row_DD["Y"];
                    $mouse_count++;
            }
            /*
            if($i==1or $i==2){
            echo "UターンX:".$UTurnCount."<br>";
            echo "DDUターンX:".$UTurnCount_XinDD."<br>";
            echo "UターンY:".$UTurnCount_Y."<br>";
            echo "DDUターンY:".$UTurnCount_YinDD."<br>";
            }
            */
            
        }
?>



<?php
        $word_sub = array();
        $word_output = array();
        
        $j = 0;

        $WID_record = array();
        $rec_count = 0;

        $word_array = array();
        $word_record = array();

        $i_record = array(); //問題が切り替わったときのiの値保存用
        $i_num = 0;//$i_recordの配列インクリメント用

        $outU_X = array();
        $outU_Y = array();//はずれ値検定Uターンの配列の保存
        $out_speed = array();
        $reverse_speed = array();
        $out_num = 0;

        $record_num = 0;
        $memo = 0;
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>WID</td>";
echo "<td>開始</td>";
echo "<td>終了</td>";
echo "<td>平均速度</td>";
echo "<td>DD時間</td>";
echo "<td>DD間時間</td>";
echo "<td>単語</td>";
echo "<td>移動情報</td>";
echo "<td>DD中UTurnX</td>";
echo "<td>DD中UTurnY</td>";
echo "<td>UTurnX</td>";
echo "<td>UTurnY</td>";
//echo "<td>値（平均速度）</td>";

//echo "<td>値（時間）</td>";
//echo "<td>はずれ値（平均速度）</td>";
//echo "<td>はずれ値（時間）</td>";
echo "<td>A⇒A</td>";
//echo "<td>はずれ値(UX)</td>";


//echo "<td>軌跡再生</td>";
echo "</tr>";


        for($i=0;$i<=$data_count;$i++){
            $sql_word ="select * from quesorder where OID= ".($WID_array[$i]+1);
            if($i !=0 and $WID_array[$i] != $WID_array[$i-1] or $i==$data_count){//問題切り替わり　ｺｺ！！！

                $i_record[$i_num] = $i;

                for($z = ($i_record[$i_num - 1]); $z <$i_record[$i_num]; $z++) {
                    if($z == ($i_record[$i_num - 1])){
                        $out_interval[$out_num] = $Time_interval[$z];
                        $out_speed[$out_num] = $AveSpeed[$z];

                    }else{
                        $out_UX[$out_num] = $U_X[$z];
                        $out_UY[$out_num] = $U_Y[$z];
                        $out_speed[$out_num] = $AveSpeed[$z];
                        if($out_speed[$out_num] !=0){
                            $reverse_speed[$out_num] = 1/$out_speed[$out_num];
                        }else{
                            $reverse_speed[$out_num] = 1000;
                        }
                        $out_Time[$out_num] = $Time[$z];
                        $out_interval[$out_num] = $Time_interval[$z];


                        $out_num++;                                

                    }
                }
                $i_num++;

                echo "<tr>";
echo "<td>WID</td>";
echo "<td>開始</td>";
echo "<td>終了</td>";
echo "<td>平均速度</td>";
echo "<td>DD時間</td>";
echo "<td>DD間時間</td>";
echo "<td>単語</td>";
echo "<td>移動情報</td>";
echo "<td>DD中UTurnX</td>";
echo "<td>DD中UTurnY</td>";
echo "<td>UTurnX</td>";
echo "<td>UTurnY</td>";
//echo "<td>値（平均速度）</td>";

//echo "<td>値（時間）</td>";
//echo "<td>はずれ値（平均速度）</td>";
//echo "<td>はずれ値（時間）</td>";
echo "<td>A⇒A</td>";
//echo "<td>軌跡再生</td>";
echo "</tr>";
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
                $i_record[0] = 0;//最初の問題の1つめなので
                $i_num++;
            }

            $word_DD = $Label_array[$i];
            $Time_DD = $Time2_array[$i];
            
            //echo $Time_DD."<br>";
            

            //$test_int = intval($Label_array[$i]);
            //$word_sub[$j] = $word_array[$test_int];
            //$word_output[$i] = $word_array[$test_int];
            $j++;
            //echo $sql_word;
            $res_word = mysql_query($sql_word,$conn) or die("接続エラーe");
            while($row_word = mysql_fetch_array($res_word)){
                    $ques_num = $row_word["WID"];
                    $AID_num = $row_word["OID"]-1;
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



            $Time_interval[$i] = $Stime[$i]- $Etime[$i-1];
            if($Time_interval[$i] <0){
                $Time_interval[$i] = "";
            }
            
            echo "<tr>";
            
            echo "<td>".$ques_num."</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>".$Time_interval[$i]."</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>".$U_X[$i]."</td>";
            echo "<td>".$U_Y[$i]."</td>";
            //echo "<td>-</td>";
            echo "<td>-</td>";
            //echo "<td>-</td>";
            echo "</tr>";
            
                            
            
            echo "<tr>";
            
            echo "<td>".$ques_num."</td>";
            echo "<td>".$Stime[$i]."</td>";
            echo "<td>".$Etime[$i]."</td>";
            echo "<td>".$AveSpeed[$i]."</td>";
            echo "<td>".$Time[$i]."</td>";
            


            echo "<td>-</td>";

            //echo $AveSpeed[$i]."←WID=".$ques_num." 時間".$Stime[$i]."～".$Etime[$i];
            $sample = ($AveSpeed[$i] - ave($AveSpeed))/ sd($AveSpeed);
            
            
            
            //echo "サンプル１".$border1."<br>";
            //echo "サンプル2".$border2."<br>";
            //echo " 単語".$test_int;
            //echo "<td>".$word_array[$test_int]."</td>";
            echo "<td>".$word_DD."</td>";
            echo "<td>".$Drag_posi[$i]."⇒".$Drop_posi[$i]."</td>";
            echo "<td>".$DDU_X[$i]."</td>";
            echo "<td>".$DDU_Y[$i]."</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            $word_output[$WID_array[$i]][$record_num] = $word_DD2;
            $Time_output[$WID_array[$i]][$record_num] = $Time_DD;

            //echo $Time_output[0][0];
            $record_num ++; //単語NO記録用フラグインクリメント

            //echo " 時間".$Time[$i];
            $sample2 = ($Time[$i] - ave($Time))/ sd($Time);
            /*
            if(($sample2<=-2) or ($sample2>=2) ){
                echo "<td>■</td>";
                //echo "■はずれ値(時間)";
            }else{
                echo "<td>-</td>";
            }
            */
            if(($Drag_posi[$i] == "A" && $Drop_posi[$i] =="A")or($Drag_posi[$i] == "1" && $Drop_posi[$i] =="1")or($Drag_posi[$i] == "2" && $Drop_posi[$i] =="2")or
            ($Drag_posi[$i] == "3" && $Drop_posi[$i] =="3"))
            {
                echo "<td>△</td>";
            }else{
                echo "<td>-</td>";
            }

            echo "</tr>";
            //echo "<br>";
        }

    echo "</table>";

    echo "<br><br>";
    
    }

    /*
    foreach($i_record as $value2){ 
               echo $value2."<br>";
               }
      */      
                //1023 一旦コメントアウト
                
                
                $out2_UX = $out_UX;
                $out2_UY = $out_UY;
                $out2_Time = $out_Time;
                $out2_interval = $out_interval;
                $out2_Speed = $out_speed;

                foreach($out2_UX as $value){
                    $value = ($value-ave($out2_UX))/sd($out2_UX);
                }
                foreach($out2_UY as $value){
                    $value = ($value-ave($out2_UY))/sd($out2_UY);
                }
                foreach($out2_Time as $value){
                    $value = ($value-ave($out2_Time))/sd($out2_Time);
                }
                foreach($out2_interval as $value){

                    $value = ($value-ave($out2_interval))/sd($out2_interval);
                    echo $value."<br>";
                }
                foreach($out2_Speed as $value){
                    $value = ($value-ave($out2_Speed))/sd($out2_Speed);
                }

                
                echo "①UターンX<br>";
                foreach($out_UX as $value){
                    $hazure_UX = ($value - ave($out_UX))/ sd($out_UX);
                    if($hazure_UX>3){
                        echo $value."はずれ値<br>";
                        //echo "■はずれ値(時間)";
                    }else if($hazure_UX>2 and $hazure_UX<=3){
                        echo $value."はずれ(候補)<br>";
                    }else{
                        echo $value."<br>";
                    }
                }
                
                /*
                echo "②UターンY<br>";
                foreach($out_UY as $value){
                    $hazure_UY = ($value - ave($out_UY))/ sd($out_UY);
                    if($hazure_UY>3){
                        echo $value."はずれ値<br>";
                        //echo "■はずれ値(時間)";
                    }else if($hazure_UY>2 and $hazure_UY<=3){
                        echo $value."はずれ(候補)<br>";
                    }else{
                        echo $value."<br>";
                    }
                }
                */
                /*
                echo "③D&D時間<br>";

                foreach($out_Time as $value){
                    $hazure_Time = ($value - ave($out_Time))/ sd($out_Time);
                    if($hazure_Time>3){
                        echo $value."はずれ値<br>";
                        //echo "■はずれ値(時間)";
                    }else if($hazure_Time>2 and $hazure_Time<=3){
                        echo $value."はずれ(候補)<br>";
                    }else{
                        echo $value."<br>";
                    }
                }
                */

                
                echo "④D&D間時間<br>";
                /*
                foreach($out_interval as $value){
                    $hazure_interval = ($value - ave($out_interval))/ sd($out_interval);
                    if($hazure_interval>3){
                        echo $value."はずれ値<br>";
                        //echo "■はずれ値(時間)";
                    }else if($hazure_interval>2 and $hazure_interval<=3){
                        echo $value."はずれ(候補)<br>";
                    }else{
                        echo $value."<br>";
                    }
                }
                */
                /*
              echo "⑤平均移動速度<br>";

                $memo2 =0;
                foreach($out_speed as $value){
                    $hazure_speed = ($value - ave($out_speed))/ sd($out_speed);
                    if($hazure_speed<-3){
                        echo $out_speed[$memo2]."はずれ値<br>";
                        //echo "■はずれ値(時間)";
                    }else if($hazure_speed<-2 and $hazure_speed<=-3){
                        echo $out_speed[$memo2]."はずれ(候補)<br>";
                    }else{
                        echo $out_speed[$memo2]."<br>";
                    }
                    $memo2++;
                }
                */


                /*
                
                 echo "⑥平均移動速度（逆数）<br>";

                $memo2 =0;
                foreach($reverse_speed as $value){
                    $hazure_speed = ($value - ave($reverse_speed))/ sd($reverse_speed);
                    if($hazure_speed>3){
                        echo $out_speed[$memo2]."はずれ値<br>";
                        //echo "■はずれ値(時間)";
                    }else if($hazure_speed>2 and $hazure_speed<=3){
                        echo $out_speed[$memo2]."はずれ(候補)<br>";
                    }else{
                        echo $out_speed[$memo2]."<br>";
                    }
                    $memo2++;
                }

                */

                

?>
</div>
<DIV class="question">
<?php


    $output = array();
    $output_sub = array();
    $ques_memo =array();
    //$AACount = array();

    if ($_SESSION["cmd"]=="StartTime"){
        echo "<br><br>";

        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>AID</td>";
        echo "<td>WID</td>";
        echo "<td>得点</td>";
        echo "<td>正誤(数字)</td>";
        echo "<td>自信度</td>";
        //echo "<td>初動時間</td>";
        echo "<td>解答時間</td>";
        //echo "<td>決定後時間</td>";
        echo "<td>平均速度</td>";
        echo "<td>総移動距離</td>";
        echo "<td>DD開始時間</td>";
        echo "<td>最大静止時間</td>";
        echo "<td>最大入れ替え間時間</td>";
        echo "<td>決定後時間</td>";
        //echo "<td>D&D回数</td>";
        echo "<td>D&D回数(補正)</td>";        
        echo "<td>A→A回数</td>";
        echo "<td>Uターン回数(X)</td>";
        echo "<td>Uターン回数(Y)</td>";
        echo "<td>Uターン回数合計</td>";
        echo "<td>語句数</td>";
        echo "</tr>";
        
        for($i=0;$i<$data_count;$i++){

            $sql_wordnum ="select * from question_info where WID = ".$WID_array[$i];
            $res_wordnum = mysql_query($sql_wordnum,$conn);
            $row_wordnum = mysql_fetch_array($res_wordnum);

            $word_num = $row_wordnum["wordnum"];
            

            echo "<tr>";
            echo "<td>".($i+1)."</td>";
            echo "<td>".$WID_array[$i]."</td>";
            echo "<td>".$point_array[$i]."</td>";
            if($point_array[$i] ==5){
                echo "<td>0</td>";
            }else{
                echo "<td>".$point_array[$i]."</td>";
            }
            echo "<td>".$Understand_array[$i]."</td>";
            
            //echo "<td>".$DS_array[$i]."</td>";
            echo "<td>".$Time_array[$i]."</td>";
            //echo "<td>".$Check_array[$i]."</td>";
            echo "<td>".$AveSpeed_info[$i]."</td>";
            echo "<td>".$Distance_array[$i]."</td>";
            echo "<td>".$DStart_array[$i]."</td>";
            echo "<td>".$MaxStop_array[$i]."</td>";
            echo "<td>".$MaxDCTime_array[$i]."</td>";
            echo "<td>".$Check_array[$i]."</td>";
            //echo "<td>".$DDCount_info[$i]."</td>";
            echo "<td>".$DDCount_rev_info[$i]."</td>";
            echo "<td>".($AACount[$i]+$RRCount[$i])."</td>";   
            echo "<td>".$UTurnCount_X_info[$i]."</td>";   
            echo "<td>".($UTurnCount_Y_info[$i]-2*$word_num+1)."</td>";  
            echo "<td>".($UTurnCount_info[$i]-2*$word_num+1)."</td>"; 
            echo "<td>".$word_num."</td>"; 
            echo "</tr>";
            //echo "<br>";
        }
        echo "</table>";
        echo "<br><br>";

        

    }else if ($_SESSION["cmd"]=="DD"){
    echo "<br><br>";
    echo "<table border=\"1\">";
    echo "<tr>";
    echo "<td>WID</td>";
    echo "<td>単語ごと移動回数</td>";
    echo "<td>単語ごと初移動時間</td>";
    echo "</tr>";
    /*
        for($i=0;$i<$data_count;$i++){
        if($i !=0 and $WID_array[$i] != $WID_array[$i-1]){
            
            echo "<td>".$WID_array[$i]."</td>";
        }
        }

        */

        $Count_array = array();//DDカウント記録用
        
    for($i = 0;$i<=$memo;$i++){
        $j = 0;
        $k = 0;
        foreach($word_output[$i] as $value){
            $output_sub = explode("#",$value);
            foreach($output_sub as $value2){ 
                $output[$i][$j] = $value2;
                $Time2_output[$i][$j] = $Time_output[$i][$k];
                //$output[$i][$j] = str_replace($output[$i][$j],$word_array[$i][$output[$i][$j]]."(".$output[$i][$j].")",$output[$i][$j]);
                //echo $output[$i][$j]."<br>";
                $j++;
            }
            $k++;
        }

        $word_first = $output;//単語の最初記録用 
        $max_num = max($word_first[$i]);//単語の最後の番号取得
        //echo $max_num."<br>";

        //$x = 0;
        /*
        foreach($output[$i] as $value){
            echo $value." ".$Time2_output[$i][$x]."<br>";
            $x++;
        }
        */
        for($a=0;$a<=$max_num;$a++){
            $b = 0;
            foreach($output[$i] as $value){
                if($value == $a){
                    //echo "一致<br>";
                    $min_time[$i][$a] = $Time2_output[$i][$b];
                    break;
                }else{
                    //echo "不一致<br>";
                }
                $b++;
            }
        }
        $m=0;
        /*
 foreach($min_time[0] as $value2){
            echo "ddd".$m." ".$value2."<br>";
            $m++;
        }
        */
        
        

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
        echo "<td>";
        $output_exam[$i] = array_merge(array_unique($output[$i]));
        $m = 0;
         foreach($min_time[$i] as $value2){
            echo "[".$output_exam[$i][$m]."]⇒ ".$value2."<br>";
                $m++;    
            }
        echo "</td>";
        echo "</tr>";
        //echo "<br><br>";
    }



    echo "</table>";
 }



    
?>
</div>
<?php
$_SESSION["cmd"]="";
$_SESSION["studentlist"]="";    
?>


</body>
</html>