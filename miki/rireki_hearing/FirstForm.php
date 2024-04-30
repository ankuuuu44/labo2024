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
/*
if($_POST["mark"] != $_SESSION["mark"]){//正誤表示、部分点表示の場合分け処理
    if(isset($_POST["mark"])){
        $_SESSION["mark"] = $_POST["mark"];
    }
}
*/

?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>テスト用</title>
    <STYLE type="text/css">
<!--
.scr {
  overflow: scroll;   /* スクロール表示 */
  width: 300px;
  height: 500px;
  background-color: white;
  position: relative;
  top: 10px;
  left: 20px;
}
.dataset {
  overflow: scroll;   /* スクロール表示 */
  width: 1200px;
  height: 300px;
  background-color: white;
  position: absolute;
  top: 600px;
  left: 20px;
}
-->
</STYLE>
</head>


<body>

<div align ="center">
<?php
/*
   $student_count = $_POST["student_count"];
   $question_count = $_POST["question_count"];
   $data_count = $_POST["data_count"];
   $termr1 =  $_POST["term_r"];
   $termr2 = str_replace("linedata","b",$termr1);
   $termr3 = str_replace("trackdata","a",$termr2);
   $term_r = str_replace("AnswerQues","c",$termr3);
   */
   //echo $term_s."<br>";
   //echo $term_q."<br>";
   //echo "ddd:".$term_r."<br>";
   echo "得点データ更新中です<br>";
?>


<br>
</div>

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

?>





<?php
 //echo $_SESSION["correl_mode"];
        require "dbc.php";
        
        $sql = "select * from linedata";
        $res =  mysql_query($sql,$conn) or die("接続エラー");

        $count = 0;
        $UID_reserve =array();
        $AID_reserve =array();
/*
        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>UID</td>";
        echo "<td>WID</td>";
        echo "<td>Date</td>";
        echo "<td>TF</td>";
        echo "<td>Time</td>";
        echo "<td>First</td>";
        echo "<td>AID</td>";
        echo "</tr>";
*/

        while ($row = mysql_fetch_array($res)){
            /*
            echo "<tr>";
            echo "<td>".$row["UID"]."</td>";
            echo "<td>".$row["WID"]."</td>";
            echo "<td>".$row["Date"]."</td>";
            echo "<td>".$row["TF"]."</td>";
            echo "<td>".$row["Time"]."</td>";
            echo "<td>".$row["First"]."</td>";
            echo "<td>".$row["AID"]."</td>";
            echo "</tr>";
            */
            $UID_reserve[$count] = $row["UID"];
            $AID_reserve[$count] = $row["AID"];
            //echo $UID_reserve[$count].",".$AID_reserve[$count]."trackdata生成<br>";
            $count++;
        }
        //echo "</table>";
        //echo $count;
        
        //trackdata作成プログラム-------------------------------------------------------------------------
        for ($i = 0 ;$i <$count;$i++){
            
            $sql_track = "select count(*) from trackdata where UID = ".$UID_reserve[$i]. " and AID = ".$AID_reserve[$i];
            $res_track =  mysql_query($sql_track,$conn) or die("接続エラー");

            while ($row_track = mysql_fetch_array($res_track)){
                if($row_track["count(*)"] ==1){
                    //履歴データがあるので特に何もしない
                    echo "データあり<br>";
                }else{
                    
                    //echo $UID_reserve[$i].",".$AID_reserve[$i]."なし<br>";
                    //trackdata生成ここから
                    $mouse_count =0;//trackデータパラメータ生成に利用するカウンタ
                    //軌跡パラメータ初期化
                    $Distance = 0;//総移動距離
                    $AveSpeed = 0;//平均速度
                    $MaxSpeed = 0;//最大速度
                    $MinSpeed = 10000;//最小速度
                    $StartTime= 0;//初動時間
                    $DStartTime = 0;//ドラッグ開始時間
                    $MaxStopTime=0;//最大静止時間
                    $MinStopTime=10000;//最小静止時間
                    $MaxDragDropTime=0;//最大ドラッグ⇒ドロップ時間
                    $MinDragDropTime=10000;//最小ドラッグ⇒ドロップ時間
                    $MaxDropDragTime=0;//最大ドロップ⇒ドラッグ時間
                    $MinDropDragTime=10000;//最小ドロップ⇒ドラッグ時間
                    $GroupCount=0;//グルーピング回数
                    $ResistCount=0;//区切り追加回数


                    $DragDropCount=0;//ドラッグ＆ドロップ回数
                    $DD_QQ_Count = 0;//問題提示欄欄内でのDD（正確には単語がそのまま戻る）
                    $DD_QA_Count = 0;//問題提示欄→解答欄への動き
                    $DD_QR_Count = 0;//問題提示欄→レジスタへの動き
                    $DD_AQ_Count = 0;//解答欄→問題提示欄への動き
                    $DD_AA_Count = 0;//解答欄内での動き
                    $DD_AR_Count = 0;//解答欄→レジスタへの動き
                    $DD_RQ_Count = 0;//レジスタ→問題提示欄への動き
                    $DD_RA_Count = 0;//レジスタ→解答欄への動き
                    $DD_RR_Count = 0;//レジスタ内での動き
                    $Array_flag = -1;//ドラッグ開始時の場所 0=問題提示欄 1=レジスタ1 2=レジスタ2 3=レジスタ3 4=最終解答欄
                    $DD_flag =0; //DD中かどうか判定するフラグ

                    $UTurnCount=0;//Ｕターン回数
                    $UTurnCount_Y=0; //Y軸方向Uターン回数 //0414追加
                    $UTurnCount_XinDD = 0;//DD中のX軸方向Uターン
                    $UTurnCount_YinDD = 0;//DD中のY軸方向Uターン
                    $UturnPer=0;

                    $DStart_Flag=0;//DD開始時間判定フラグ
                    $before_Time = 0;
                    $before_X = 0;
                    $before_Y = 0;//前のデータ記録用
                    $Drag_X =0;
                    $Drop_X =0;
                    $Drag_Y =0;
                    $Drop_Y =0;
                    $Drag_Time =0;
                    $Drop_Time =0;
                    $DragDropTime = 0;
                    $DropDragTime = 0;
                    

                    $UTurnFlag = 0;//X軸の変化方向の判別用
                    $UTurnFlag_Y = 0;//Y軸の変化方向の判別用 0414追加
                    $SwitchFlag = -1;//FALSE
                    $UTurnPoint = 0; //ブレ判定用
                   
                    /*
                    $StartX =0;
                    $StartY =0;
                    $EndY = 0;
                    $StartCountFlag = -1;//Uターン回数カウント開始用
                    
                    
                    $SPSIZE = 10;//単語間隔の大きさ
                    $DefaultY =100;
                    $DefaultX = 30;//英単語のデフォルト座標
                    */
                    $WID = $AID_reserve[$i]+1;//仮のWID
                    $sql_sentence = "select * from lquestion2 where WID = ".$WID;
                    $res_sentence =  mysql_query($sql_sentence,$conn) or die("接続エラー");
                    while ($row_sentence = mysql_fetch_array($res_sentence)){
                        $Sentence= $row_sentence["divide"];
                        //echo $Sentence."<br>";
                    }
                    $WordTable =array();
                    $WordTable =explode("|",$Sentence);
                    $TermCount = count($WordTable);//単語数
                    
                    //$SWordWidth=array();//単語の始まり　横幅
                    //$EWordWidth=array();//単語のおわり
                    
                    //echo $TermCount;
                    //軌跡パラメータ初期化ここまで
                    //マウス軌跡情報の読み込み
                    $sql_mouse = "select * from linedatamouse where UID = ".$UID_reserve[$i]. " and AID = ".$AID_reserve[$i]." order by Time";
                    $res_mouse =  mysql_query($sql_mouse,$conn) or die("接続エラー");
                    //echo "ユーザID".$UID_reserve[$i].",AID".$AID_reserve[$i]."<br>";

                    $dis_count = 0;//距離関係計算用カウンター
                    while ($row_mouse = mysql_fetch_array($res_mouse)){//マウス軌跡情報がある限りループ
                        
                        if($mouse_count ==0){
                            $StartTime = $row_mouse["Time"];//初動時間
                            //echo "初動時間".$StartTime."<br>";
                        
                            
                        }else{//初回以外
                            $change_X = $row_mouse["X"]-$before_X;
                            $change_Y = $row_mouse["Y"]-$before_Y;
                            $change = sqrt(pow($change_X,2)+pow($change_Y,2));//座標の変化量
                            $Distance = $Distance + $change;//距離の計算
                            if($change !=0){//座標の変化量が０でないとき
                                if($row_mouse["Time"] - $before_Time !=0){
                                    $speed[$dis_count] = $change/($row_mouse["Time"] - $before_Time);
                                    if($UID_reserve[$i] ==18){
                                        //echo $speed[$dis_count].",<br>";
                                        //echo "-----".$row_mouse["X"].",".$row_mouse["Y"]."----<br>";
                                        }
                                    if($MaxSpeed <= $speed[$dis_count]){
                                        $MaxSpeed = $speed[$dis_count];//最大DropDrag時間
                                        }
                                    if($MinSpeed >= $speed[$dis_count]){
                                        $MinSpeed = $speed[$dis_count];//最小DropDrag時間
                                        }
                                }
                            
                                
                            }else{
                                $dis_count--;
                            }
                            $dis_count++;   
                        }

                        
                        if($row_mouse["DD"] == 2 and $DStart_Flag == 0){//Dragの初回のとき
                            $DStartTime = $row_mouse["Time"];//DD開始時間
                            //echo "DD開始時間".$DStartTime."<br>";
                            $DStart_Flag = 1;
                            
                        }
                        $interval_time = $row_mouse["Time"] - $before_Time;//Timeの間隔を計算
                        
                        if($interval_time != 0){
                            if($MaxStopTime <= $interval_time){
                                $MaxStopTime = $interval_time;//最大静止時間
                            }
                            if($MinStopTime >= $interval_time){
                                $MinStopTime = $interval_time;//最小静止時間
                            }
                        }
                        if($row_mouse["DD"]==2){//Dragした時
                            $DD_flag =1;//DD開始
                            $Drag_Time = $row_mouse["Time"];
                            $DropDragTime = $Drag_Time - $Drop_Time;
                            if($DragDropCount != 0){
                                if($MaxDropDragTime <= $DropDragTime){
                                    $MaxDropDragTime = $DropDragTime;//最大DropDrag時間
                                }
                                if($MinDropDragTime >= $DropDragTime){
                                    $MinDropDragTime = $DropDragTime;//最小DropDrag時間
                                }
                            }
                            if($row_mouse["Y"]<= 150){ $Array_flag =0; }//問題提示欄
                            else if($row_mouse["Y"] > 150 and $row_mouse["Y"]<= 240){ $Array_flag =4; }//解答欄
                            else if($row_mouse["Y"] > 240){ $Array_flag =1; }//レジスタ


                        }
                        if($row_mouse["DD"]==1){//Dropした時
                            $DD_flag =0;//DD終了
                            $Drop_Time = $row_mouse["Time"];
                            $DragDropTime = $Drop_Time - $Drag_Time;
                            
                            if($MaxDragDropTime <= $DragDropTime){
                                $MaxDragDropTime = $DragDropTime;//最大DragDrop時間
                            }
                            if($MinDragDropTime >= $DragDropTime){
                                $MinDragDropTime = $DragDropTime;//最小DragDrop時間
                            }
                                                      
                            $DragDropCount++;//DD回数
                            echo "DDした";
                            if($row_mouse["Y"]<= 150){
                                if($Array_flag ==0){$DD_QQ_Count++; echo "QQ++";}
                                else if($Array_flag ==4){$DD_AQ_Count++; echo "AQ++";}
                                else if($Array_flag ==1){$DD_RQ_Count++; echo "RQ++";}
                              }//問題提示欄
                            else if($row_mouse["Y"] > 150 and $row_mouse["Y"]<= 240){  
                                if($Array_flag ==0){$DD_QA_Count++; echo "QA++";}
                                else if($Array_flag ==4){$DD_AA_Count++; echo "AA++";}
                                else if($Array_flag ==1){$DD_RA_Count++; echo "RA++";}
                            }//解答欄
                            else if($row_mouse["Y"] > 240){
                                if($Array_flag ==0){$DD_QR_Count++; echo "QR++";}
                                else if($Array_flag ==4){$DD_AR_Count++; echo "AR++";}
                                else if($Array_flag ==1){$DD_RR_Count++; echo "RR++";}
                              }//レジスタ
                            
                        }
                        //}


                        $Group_check = strstr($row_mouse["Label"],"#");//複数個のラベルをグループ化したかチェック
                        if($Group_check){
                            $GroupCount++;//グループ化回数
                            $UTurnFlag =0;
                        }

                        $ResistCount = 0;//レジスタ回数は今回は0
                        /*
                        if($UID_reserve[$i]==15 and $AID_reserve[$i]==0){
                        echo $row_mouse["Time"]."<br>";
                        }
                        */
                        if($mouse_count !=0){
                        
                        /*    
                        if($SwitchFlag == -1 and $row_mouse["DD"]==2){
                            $SwitchFlag =1;
                            $UTurnFlag = 0;
                        }else if($SwitchFlag == 1 and $row_mouse["DD"]==1){
                            $SwitchFlag =-1;
                            $UTurnFlag = 0;
                        }
                        */
                        /*
                        if($row_mouse["addk"] ==1){//区切りカウント
                        }
                        */
                        echo "座標".$row_mouse["X"].",".$row_mouse["Y"];

                        //X軸方向Uターンカウント

                        if($UTurnFlag ==0){
                            if(($row_mouse["X"] - $before_X)==0){
                            }else if(($row_mouse["X"] - $before_X)>0){
                                $UTurnFlag =1;
                            }else if(($row_mouse["X"] - $before_X)<0){
                                //echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                $UTurnFlag =-1;
                            }
                            $UTurnPoint = $row_mouse["X"];//ブレ判定用メモ 
                        }else if($UTurnFlag ==1){
                            if(($row_mouse["X"] - $before_X)==0){
                            }else if(($row_mouse["X"] - $before_X)>0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                              $UTurnPoint = $row_mouse["X"];//ブレ判定用メモ 
                            }else if(($row_mouse["X"] - $before_X)<0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                                $UTurnFlag = -1;
                                /*
                                if(($row_mouse["X"] - $before_X)<-15){//移動がX軸方向15pixel以上
                                echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                    $UTurnCount++;
                                }
                                */
                                //echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                if($DD_flag == 0){
                                    $UTurnCount++;
                                }else if($DD_flag ==1){
                                    $UTurnCount_XinDD++;
                                }
                                //echo "X軸方向Uターン";
                                $UTurnPoint = $row_mouse["X"];//ブレ判定用メモ
                            } 
                        }else if($UTurnFlag ==-1){
                            if(($row_mouse["X"] - $before_X)==0){

                            }else if(($row_mouse["X"] - $before_X)>0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                                $UTurnFlag = 1;
                                /*
                                if(($row_mouse["X"] - $before_X)>15){
                                    $UTurnCount++;
                                }
                                */
                                //echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                if($DD_flag == 0){
                                    $UTurnCount++;
                                }else if($DD_flag ==1){
                                    $UTurnCount_XinDD++;
                                }
                                //echo "X軸方向Uターン";
                                $UTurnPoint = $row_mouse["X"];//ブレ判定用メモ
                            }else if(($row_mouse["X"] - $before_X)<0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                                $UTurnPoint = $row_mouse["X"];//ブレ判定用メモ

                            } 
                        }else{
                            echo "Uターン関連エラー";
                        }

                        

                        //0414追加 Y軸Uターン
                        

                         if($UTurnFlag_Y ==0){
                            if(($row_mouse["Y"] - $before_Y)==0){
                            }else if(($row_mouse["Y"] - $before_Y)>0){
                                $UTurnFlag_Y =1;
                            }else if(($row_mouse["Y"] - $before_Y)<0){
                                //echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                $UTurnFlag_Y =-1;
                            }
                            //$UTurnPoint = $row_mouse["X"];//ブレ判定用メモ 
                        }else if($UTurnFlag_Y ==1){
                            if(($row_mouse["Y"] - $before_Y)==0){
                            }else if(($row_mouse["Y"] - $before_Y)>0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                              //$UTurnPoint = $row_mouse["X"];//ブレ判定用メモ 
                            }else if(($row_mouse["Y"] - $before_Y)<0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                                $UTurnFlag_Y = -1;
                                /*
                                if(($row_mouse["X"] - $before_X)<-15){//移動がX軸方向15pixel以上
                                echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                    $UTurnCount++;
                                }
                                */
                                //echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                if($DD_flag == 0){
                                    $UTurnCount_Y++;
                                }else if($DD_flag ==1){
                                    $UTurnCount_YinDD++;
                                }
                                
                                //echo "Y軸方向Uターン";
                                //$UTurnPoint = $row_mouse["X"];//ブレ判定用メモ
                            } 
                        }else if($UTurnFlag_Y ==-1){
                            if(($row_mouse["Y"] - $before_Y)==0){

                            }else if(($row_mouse["Y"] - $before_Y)>0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                                $UTurnFlag_Y = 1;
                                /*
                                if(($row_mouse["X"] - $before_X)>15){
                                    $UTurnCount++;
                                }
                                */
                                //echo "座標".$row_mouse["X"].",".$row_mouse["Y"]."<br>";
                                if($DD_flag == 0){
                                    $UTurnCount_Y++;
                                }else if($DD_flag ==1){
                                    $UTurnCount_YinDD++;
                                }
                                
                                //echo "Y軸方向Uターン";
                                //$UTurnPoint = $row_mouse["X"];//ブレ判定用メモ
                            }else if(($row_mouse["Y"] - $before_Y)<0){
                                if($row_mouse["DD"]==2){
                                }else{
                                }
                                //$UTurnPoint = $row_mouse["X"];//ブレ判定用メモ

                            } 
                        }else{
                            echo "Uターン関連エラー";
                        }
                        echo "<br>";

                        }
                        $before_Time =$row_mouse["Time"];//次回のために今回の値を記録
                        $before_X= $row_mouse["X"];
                        $before_Y= $row_mouse["Y"];
                        $mouse_count++;
                    }
                    
                    $AveSpeed = $Distance/($before_Time - $StartTime);//平均速度の計算
                    $AveSpeed = round($AveSpeed,3);//平均速度
                    $Distance = round($Distance,3);//距離
                    $MaxSpeed = round($MaxSpeed,3);//最大速度
                    $MinSpeed = round($MinSpeed,3);//最小速度
                    
                    $UTurnPer = $UTurnCount / $TermCount;
                    $UTurnPer = round($UTurnPer,3);//最小速度
                    //$AveSpeed = ave($speed);

                    //echo "距離".$Distance."<br>";
                    //echo "最大速度".$MaxSpeed."<br>";
                    //echo "最小速度".$MinSpeed."<br>";
                    //echo "平均速度".$AveSpeed."<br>";
                    //echo "最大静止時間".$MaxStopTime."<br>";
                    //echo "最小静止時間".$MinStopTime."<br>";
                    //echo "最大Drag⇒Drop時間".$MaxDragDropTime."<br>";
                    //echo "最小Drag⇒Drop時間".$MinDragDropTime."<br>";
                    //echo "最大Drop⇒Drag時間".$MaxDropDragTime."<br>";
                    //echo "最小Drop⇒Drag時間".$MinDropDragTime."<br>";
                    //echo "グループ化回数".$GroupCount."<br>";
                    echo "DD回数".$DragDropCount."<br>";
                    $DragDropCount = $DragDropCount - $DD_QQ_Count;
                    //echo "問題提示欄内DD回数".$DD_QQ_Count."<br>";
                    echo "問題提示欄→解答欄DD回数".$DD_QA_Count."<br>";
                    echo "問題提示欄→レジスタDD回数".$DD_QR_Count."<br>";
                    echo "解答欄→問題提示欄DD回数".$DD_AQ_Count."<br>";
                    echo "解答欄内DD回数".$DD_AA_Count."<br>";
                    echo "解答欄→レジスタDD回数".$DD_AR_Count."<br>";
                    echo "レジスタ→問題提示欄DD回数".$DD_RQ_Count."<br>";
                    echo "レジスタ→解答欄DD回数".$DD_RA_Count."<br>";
                    echo "レジスタ内DD回数".$DD_RR_Count."<br>";
                    echo "X軸方向Uターン回数".$UTurnCount."<br>";
                    echo "Y軸方向Uターン回数".$UTurnCount_Y."<br>";
                    echo "X軸方向Uターン回数(DD)".$UTurnCount_XinDD."<br>";
                    echo "Y軸方向Uターン回数(DD)".$UTurnCount_YinDD."<br>";
                    //echo "Uターン回数/単語数".$UTurnPer."<br>";
                    $sql_ins = "insert into trackdata VALUES($UID_reserve[$i],$WID,$AID_reserve[$i],$Distance,$AveSpeed,$MaxSpeed,
                    $MinSpeed,$StartTime,$DStartTime,$MaxStopTime,$MinStopTime,$MaxDragDropTime,$MinDragDropTime,$MaxDropDragTime,
                    $MinDropDragTime,$GroupCount,$ResistCount,$DragDropCount,$UTurnCount,$UTurnPer,$DD_QA_Count,$DD_QR_Count,$DD_AQ_Count,
                    $DD_AA_Count,$DD_AR_Count,$DD_RQ_Count,$DD_RA_Count,$DD_RR_Count,$UTurnCount,$UTurnCount_Y,$UTurnCount_XinDD
                    ,$UTurnCount_YinDD,0)";
                    
                    echo $sql_ins;
                    //echo $sql_ins."<br>";
                    $res = mysql_query($sql_ins,$conn);
                    
                    if (!$res = mysql_query($sql_ins,$conn)) {
	                //   echo "SQL実行時エラー" ;
	                //exit ;
                    
                    }
                    
            }
            }
        }

        //trackdata作成プログラムおわり--------------------------------------------------------------------
        
        //echo "<br><br><br>採点プログラム開始<br><br><br>";

        //採点用プログラム---------------------------------------------------------------------------------
        $sql_point = "select UID,AID,EndSentence from AnswerQues order by AID,UID";//ユーザごとの解答文取得
        $res_point =  mysql_query($sql_point,$conn) or die("接続エラー");

        
        $count = 0;
        $WID_reserve =array();
        $UID_reserve =array();

        $score =0;//点数
        $insert_score;//記録用点数(重複避ける用)
        while ($row_point = mysql_fetch_array($res_point)){
            $WID_reserve[$count] = $row_point["AID"]+1;//WIDの値を記録（今回は仮 0124
            $UID_reserve[$count] = $row_point["UID"];//UIDの値を記録
            $EndSentence = $row_point["EndSentence"];
            //echo $EndSentence;
            if($count != 0){
                if($WID_reserve[$count] != $WID_reserve[$count-1]){//問題が変わる時
                    //echo "問題きりかえ<br>";
                    $sql_answer = "select WID,Sentence from question_info where WID= ".$WID_reserve[$count];//問題文読み込み
                    $res_answer =  mysql_query($sql_answer,$conn) or die("接続エラー");
                    $row_answer = mysql_fetch_array($res_answer);
                    $Answer = ucfirst($row_answer["Sentence"]);

                    //$sql_part = "select * from partans where WID= ".$WID_reserve[$count];//部分点フレーズ読み込み
                    //$res_part =  mysql_query($sql_part,$conn) or die("接続エラー");
                }
            }else{//最初の問題は無条件で問題更新
                //echo "初問<br>";
                $sql_answer = "select WID,Sentence from question_info where WID= ".$WID_reserve[$count];//問題文読み込み
                $res_answer =  mysql_query($sql_answer,$conn) or die("接続エラー");
                $row_answer = mysql_fetch_array($res_answer);
                $Answer = ucfirst($row_answer["Sentence"]);
                //echo $Answer;
                //$sql_part = "select * from partans where WID= ".$WID_reserve[$count]." order by type";//部分点フレーズ読み込み
                //$res_part =  mysql_query($sql_part,$conn) or die("接続エラー");
            }


            
            
            //echo $Answer.",".$EndSentence."<br>";

            


            if($Answer == $EndSentence){//解答文と正答が一致したとき
                //echo $UID_reserve[$count].",".$WID_reserve[$count]."は正解(10点)<br>";
                $score = 10;
            }else{//解答文と正答が異なっていたとき
                $sql_part = "select * from partans where WID= ".$WID_reserve[$count]." order by type";//部分点フレーズ読み込み
                $res_part =  mysql_query($sql_part,$conn) or die("接続エラー");
                while ($row_part = mysql_fetch_array($res_part)){//部分点フレーズがある限り読み込みを繰り返す
                    //echo "---部分点フレーズ読み込み---<br>";
                    /*
                    if($WID_reserve[$count] ==1){
                        echo "解答文".$EndSentence."<br>";
                        echo "部分点フレーズ".$row_part["PartSentence"]."<br>";
                    }
                    */
                    if($row_part["type"] == 0){//全文一致検索
                        //echo "全文一致<br>";
                        //echo $row_part["PartSentence"].",".$EndSentence."<br>";
                        //if(ucfirst($row_part["PartSentence"]) == $EndSentence){//全文一致の部分点フレーズと解答文が一致していたら
                        if(strcasecmp($row_part["PartSentence"],$EndSentence)==0){
                            $score =$row_part["Point"];//部分点付加
                            if($insert_score <= $score){
                                $insert_score = $score;
                            }
                            break;//一致する部分点があったので抜け
                        }                       
                        
                    }else if($row_part["type"] == 1){//文中一致検索
                        //echo "文中一致<br>";
                        $search_word =" ".$row_part["PartSentence"]." ";//部分点フレーズの前後にスペースを入れる（文中検索をするため）
                        $check = strstr($EndSentence,$search_word);//解答文が部分点フレーズを含んでいるか判定
                        if($check){//含んでいたら
                            //echo "検索ヒット<br>";
                            $score =$row_part["Point"];//部分点付加
                            if($insert_score <= $score){
                                $insert_score = $score;
                            }
                            break;//一致する部分点があったので抜け
                        }

                    }else if($row_part["type"] == 2){//前方一致検索
                        //echo "前方一致<br>";
                        $search_word = $row_part["PartSentence"]." ";
                        $search_word =ucfirst($search_word);//先頭文字を大文字に変更する
                        $check = strstr($EndSentence,$search_word);
                        if($check){
                            //echo "検索ヒット(頭)<br>";
                            $score =$row_part["Point"];//部分点付加
                            if($insert_score <= $score){
                                $insert_score = $score;
                            }
                            break;//一致する部分点があったので抜け
                        }
                        
                    }else if($row_part["type"] == 3){//後方一致検索
                        //echo "後方一致<br>";
                        $search_word1 =" ".$row_part["PartSentence"].".";
                        $search_word2 =" ".$row_part["PartSentence"]."?";//文末がピリオドと？と!のケースを考慮
                        $search_word3 =" ".$row_part["PartSentence"]."!";
                        $check = strstr($EndSentence,$search_word1);
                        $check2 = strstr($EndSentence,$search_word2);
                        $check3 = strstr($EndSentence,$search_word3);
                        if($check or $check2 or $check3){
                            //echo "検索ヒット(後ろ)<br>";
                            $score =$row_part["Point"];//部分点付加
                            if($insert_score <= $score){
                                $insert_score = $score;
                            }
                            break;//一致する部分点があったので抜け
                        }
                    }else{
                        echo "部分点データに異常あり<br>";
                    }
                }
                //echo $UID_reserve[$count].",".$WID_reserve[$count]."は不正解<br>";
                
            }
            //echo $UID_reserve[$count].",".$WID_reserve[$count]."は".$score."点<br>";
            //echo $UID_reserve[$count].",".$WID_reserve[$count]."を採点します<br>";

            $sql_ins = "update trackdata SET Point = ".$score." where UID = ".$UID_reserve[$count]." and WID = ".$WID_reserve[$count];
            //echo $sql_ins."<br>";
            if (!$res = mysql_query($sql_ins,$conn)) {
                echo "SQL実行時エラー" ;
                exit ;
                }
            $count++;
            $score = 0;
            $insert_score=0;
        }
        //採点用ここまで----------------------------------------------------------------------

        
        header('Location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki_hearing/main.php') ;
?>

</body>
</html>