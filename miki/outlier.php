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
  width: 900px;
  height: 800px;
  background-colo: white;
  position: absolute;
  top: 30px;
  left: 20px;
}
.question {
  overflow: scroll;   /* スクロール表示 */
  width: 500px;
  height: 800px;
  background-color: white;
  position: absolute;
  top: 30px;
  left: 950px;
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
    
    $Answer_array = array();//解答文保存用配列
    $Check_array = array();//確認時間保存用配列
    $Label_array = array();
    $Time2_array = array();
    $point_array = array();//得点保存用配列
    $Understand_array =array();//自信度保存用

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


    if($_SESSION["cmd"]=="StartTime"){//DD開始時間の値保存
        for($i=0;$i<$ques_count;$i++){
            $sql_DS ="select * from trackdata where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_DS = mysql_query($sql_DS,$conn) or die("接続エラー");
            while($row_DS = mysql_fetch_array($res_DS)){
                $DS_array[$i] = $row_DS["DStartTime"];
                //echo $row_DS["DStartTime"];
                $WID_array[$i] = $row_DS["WID"];
                $point_array[$i] =$row_DS["point"];
                //echo "←".$row_DS["AID"];
                //echo "<br>";
                $data_count++;
            }
            //$row_DS = mysql_fetch_array($res_DS,MYSQL_ASSOC);
            
        }
        
        for($i=0;$i<$ques_count;$i++){
            $sql_Time ="select * from linedata where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_Time = mysql_query($sql_Time,$conn) or die("接続エラー");
            while($row_Time = mysql_fetch_array($res_Time)){
                $Time_array[$i] = $row_Time["Time"];
                //$data_count++;
            }
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
            $sql_last ="select MAX(Time) from linedatamouse where UID = ".$_SESSION["studentlist"]." and AID = ".$i;
            $res_last = mysql_query($sql_last,$conn) or die("接続エラー");
            while($row_last = mysql_fetch_array($res_last)){
                $Last_time = $row_last["MAX(Time)"];
                $Check_array[$i] = $Time_array[$i] - $Last_time;
                //$data_count++;
            }
        }
        

        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>WID</td>";
        echo "<td>初動時間</td>";
        echo "<td>解答時間</td>";
        echo "<td>決定後時間</td>";
        echo "<td>解答文</td>";
        echo "<td>正誤</td>";
        echo "<td>自信度</td>";
        //echo "<td>値</td>";
        
        echo "<td>はずれ値(初動)</td>";
        echo "<td>はずれ値(解答)</td>";
        echo "</tr>";
        
        for($i=0;$i<$data_count;$i++){
            echo "<tr>";
            echo "<td>".$WID_array[$i]."</td>";
            echo "<td>".$DS_array[$i]."</td>";
            echo "<td>".$Time_array[$i]."</td>";
            echo "<td>".$Check_array[$i]."</td>";
            echo "<td>".$Answer_array[$i]."</td>";
            
            if($point_array[$i] == 10){
                echo "<td>○</td>";
            }else if($point_array[$i] == 0){
                echo "<td>×</td>";
            }else{
                echo "<td>△</td>";
            }
            echo "<td>".$Understand_array[$i]."</td>";
            //echo $DS_array[$i]."←".$WID_array[$i];
            $sample = ($DS_array[$i] - ave($DS_array))/ sd($DS_array);
              //echo "<td>".$sample."</td>";
            //echo "サンプル１".$border1."<br>";
            //echo "サンプル2".$border2."<br>";
            //echo "<td>".$sample."</td>";
            
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
            $border = 30;//Uターン判定の基準値

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
                    if($row_DD["Y"] <= 150){
                        $Drag_posi[$data_count] ="Q";
                    }else if($row_DD["Y"] > 150 and $row_DD["Y"]<= 240){ $Drag_posi[$data_count] ="A"; }//解答欄
                    else if($row_DD["Y"] > 240){ $Drag_posi[$data_count] ="R"; }//レジスタ           
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

                    if($row_DD["Y"] <= 150){
                        $Drop_posi[$data_count] ="Q";
                    }else if($row_DD["Y"] > 150 and $row_DD["Y"]<= 240){ $Drop_posi[$data_count] ="A"; }//解答欄
                    else if($row_DD["Y"] > 240){ $Drop_posi[$data_count] ="R"; }//レジスタ
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
echo "<td>はずれ値（時間）</td>";
echo "<td>A⇒A</td>";
echo "<td>軌跡再生</td>";
echo "</tr>";


        for($i=0;$i<$data_count;$i++){
            $sql_word ="select * from quesorder where OID= ".($WID_array[$i]+1);
            if($i !=0 and $WID_array[$i] != $WID_array[$i-1]){
                //echo "問題変更";
                //echo $wdd;
                //echo "<tr><td>"."-"."</td></tr>";
                
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
echo "<td>はずれ値（時間）</td>";
echo "<td>A⇒A</td>";
echo "<td>軌跡再生</td>";
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
                //$rec_count++;
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
                $Time_interval[$i] = $Stime[$i];
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
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            
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

            if(($sample2<=-2) or ($sample2>=2) ){
                echo "<td>■</td>";
                //echo "■はずれ値(時間)";
            }else{
                echo "<td>-</td>";
            }
            
            if($Drag_posi[$i] == "A" && $Drop_posi[$i] =="A"){
                echo "<td>△</td>";
            }else{
                echo "<td>-</td>";
            }
            
            echo "<td>";
            
            
            
            //echo '<a href="http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki/mousemove2.php">再生</a>';
            ?>
            <form action = "mousemove2.php" method="post" target="_blank">
                <?php
                $Pass_ID = $_SESSION["studentlist"].",".$ques_num.",".$AID_num;
                ?>
                <input type="hidden" name="datalist" value="<?php echo $Pass_ID;?>">
                <input type="submit" name="Submit" value="再生">
            </form>
                <?php
                //echo " <a href=\"".$_SERVER["PHP_SELF"]."?p=$prev\">
            echo "</td>";


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

 if($_SESSION["cmd"]=="DD"){
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