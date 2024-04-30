<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();

ini_set("display_errors",1);
error_reporting(E_ALL);
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
   echo "得点データ更新中です<br>";

   $UTurnCount = 0;
   $UTurnCountinDD = 0;
   $UTurnFlag = 0;
   $move_distance = 0;
   $variate = 0;
   $time_sub = 0;
   $point_sub = 0;
   $UTurn_Time = array();
   $border = 30;
      
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
        return $result; // 合計値を返して終了    
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
            $tmp = $target[$i] - $ave;      // X-E(X)
            $tmparray[$i] = $tmp * $tmp;    // (X-E(X))^2
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
        $varp = varp($target);  // 分散の算出
        $result = sqrt($varp);          // その平方根をとる
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
    
    function UTurn_decision($DD_flag,$pos,$time){   
        global $UTurnCount,$UTurnCountinDD,$UTurnFlag,$move_distance,
        $variate,$time_sub,$UTurn_Time,$point_sub;
        $move_distance = 0;
        $UTurnFlag = -1*$UTurnFlag;
        $UTurn_Time[$UTurnCount + $UTurnCountinDD] = $time_sub;
        if($DD_flag == 0){  
           $UTurnCount++;
        }else if($DD_flag ==1){
           $UTurnCountinDD++;
        }
        $point_sub = $pos;
        $time_sub = $time;  
     }

     function UFlag1($DD_flag,$pos,$time){
         global $UTurnFlag,$move_distance,$variate,$time_sub,$point_sub,$border;
         if(($UTurnFlag * $variate) <0){
            $move_distance +=  $variate;
            if(abs($move_distance) > $border){//変化量絶対値がborderを超えたらUターン
                UTurn_decision($DD_flag,$pos,$time);
            }
         }else if(($UTurnFlag * $variate) >0){
            $move_distance +=  $variate;
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
?>





<?php
    require "dbc.php";
    $ques_order = array();
    $sql_order = "select * from quesorder order by OID";
    $res_order =  mysql_query($sql_order,$conn) or die("接続エラー*quesorder");
    $count = 0;
    while ($row_order = mysql_fetch_array($res_order)){
        $ques_order[$count] = $row_order["WID"];
        $count++;
    }
    $sql = "select * from linedata";
    $res =  mysql_query($sql,$conn) or die("接続エラー*linedata");
    $count = 0;
    $UID_reserve =array();
    $OID_reserve =array();
    $WID_reserve =array();

    while ($row = mysql_fetch_array($res)){
        $UID_reserve[$count] = $row["UID"];
        $WID_reserve[$count] = $row["WID"];
        $count++;
    }
    //trackdata作成プログラム-------------------------------------------------------------------------
    for ($i = 0 ;$i <$count;$i++){
        $sql_track = "select count(*) from trackdata where UID = ".$UID_reserve[$i]. " and WID = ".$WID_reserve[$i];
        $res_track =  mysql_query($sql_track,$conn) or die("接続エラー1_trackdata_cnt");
        while ($row_track = mysql_fetch_array($res_track)){
            if($row_track["count(*)"] ==1){
                    //履歴データがあるので特に何もしない
            }else{//履歴データがない場合　　はじめ
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
                $MaxDCTime=0;//最大入れ替え間時間
                $DCTime = 0;
                $MaxDragDropTime=0;//最大ドラッグ⇒ドロップ時間
                $MinDragDropTime=10000;//最小ドラッグ⇒ドロップ時間
                $MaxDropDragTime=0;//最大ドロップ⇒ドラッグ時間
                $MinDropDragTime=10000;//最小ドロップ⇒ドラッグ時間
                $GroupCount=0;//グルーピング回数
                $ResistCount=0;//区切り追加回数
                $DC_Flag = 0;
                $DragDropCount=0;//ドラッグ＆ドロップ回数
                $DragDropCount_rev=0;//ドラッグ＆ドロップ回数(補正ver)
                $DD_QQ_Count = 0;//問題提示欄欄内でのDD（正確には単語がそのまま戻る）
                $DD_QA_Count = 0;//問題提示欄→解答欄への動き
                $DD_QR_Count = 0;//問題提示欄→レジスタへの動き
                $DD_AQ_Count = 0;//解答欄→問題提示欄への動き
                $DD_AA_Count = 0;//解答欄内での動き
                $DD_AR_Count = 0;//解答欄→レジスタへの動き
                $DD_RQ_Count = 0;//レジスタ→問題提示欄への動き
                $DD_RA_Count = 0;//レジスタ→解答欄への動き
                $DD_RR_Count = 0;//レジスタ内での動き
                $DD_RR_rev = 0; //レジスタ間動き（控え用）
                $Array_flag = -1;//ドラッグ開始時の場所 0=問題提示欄 1=レジスタ1 2=レジスタ2 3=レジスタ3 4=最終解答欄
                $DD_flag =0; //DD中かどうか判定するフラグ
                $QQFlag = 0;//単語移動元、先のフラグ
                $QA = 0;
                $QR = 0;
                $RQ = 0;
                $RR = 0;
                $RA = 0;
                $AQ = 0;
                $AR = 0;
                $AA = 0;
                $UTurnCount_X=0;//Ｕターン回数
                $UTurnCount_Y=0; //Y軸方向Uターン回数 //0414追加
                $UTurnCount_XinDD = 0;//DD中のX軸方向Uターン
                $UTurnCount_YinDD = 0;//DD中のY軸方向Uターン
                $sub_UY = 0; //解答に必要なY方向Uターン
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
                $X_preserve = array();
                $Y_preserve = array();
                $Time_preserve = array();
                $DD_flag_preserve = array();
                $UTurnCount = 0;
                $UTurnCountinDD = 0;
                $UTurnFlag= 0;
                $point_sub =0;//座標記録用
                $time_sub = 0;
                $UTurn_Time = array();
                $border = 30;//Uターン判定の基準値
                $move_distance = 0;
                $sql_sentence = "select * from question_info where WID = ".$WID_reserve[$i];
                $res_sentence =  mysql_query($sql_sentence,$conn) or die("接続エラー2question_info_*_WID");
                while ($row_sentence = mysql_fetch_array($res_sentence)){
                    $Sentence= $row_sentence["divide"];
                }
                $WordTable =array();
                $WordTable =explode("|",$Sentence);
                $TermCount = count($WordTable);//単語数
                //軌跡パラメータ初期化ここまで
                
                //マウス軌跡情報の読み込み
                $sql_mouse = "select * from linedatamouse where UID = ".$UID_reserve[$i]. " and WID = ".$WID_reserve[$i]." order by Time";
                $res_mouse =  mysql_query($sql_mouse,$conn) or die("接続エラー3linedatamouse_WID");
                $dis_count = 0;//距離関係計算用カウンター
                while ($row_mouse = mysql_fetch_array($res_mouse)){//マウス軌跡情報がある限りループ
                    if($mouse_count ==0){
                        $StartTime = $row_mouse["Time"];//初動時間
                    }else{//初回以外
                        $change_X = $row_mouse["X"]-$before_X;
                        $change_Y = $row_mouse["Y"]-$before_Y;
                        $change = sqrt(pow($change_X,2)+pow($change_Y,2));//座標の変化量
                        $Distance = $Distance + $change;//距離の計算
                        if($change !=0){//座標の変化量が０でないとき
                            if($row_mouse["Time"] - $before_Time !=0){
                                $speed[$dis_count] = $change/($row_mouse["Time"] - $before_Time);
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
                        if($DStart_Flag == 0){
                            $DStartTime = $row_mouse["Time"];//DD開始時間
                            $DStart_Flag = 1;
                        }else{
                            $DD_flag =1;//DD開始
                            $Drag_Time = $row_mouse["Time"];
                            $DropDragTime = $Drag_Time - $Drop_Time;
                            $label = $row_mouse["Label"];//Dragした単語の識別番号を取得
                            $label_array = explode("#",$label);
                            $label_count = count($label_array);//Dragした単語の数
                            $label_array = array();
                            if($DragDropCount != 0){
                                if($MaxDropDragTime <= $DropDragTime){
                                    $MaxDropDragTime = $DropDragTime;//最大DropDrag時間
                                }
                                if($MinDropDragTime >= $DropDragTime){
                                    $MinDropDragTime = $DropDragTime;//最小DropDrag時間
                                }
                            }
                            if($MaxDCTime < $DropDragTime){
                                $MaxDCTime = $DropDragTime;
                            }
                            if($row_mouse["Y"]<= 150){ $Array_flag =0; }//問題提示欄
                            else if($row_mouse["Y"]<= 240){ $Array_flag =4; }//解答欄
                            else if($row_mouse["Y"]<=320){  $Array_flag =1; }//レジスタ1
                            else if($row_mouse["Y"]<=400){  $Array_flag =2; }//レジスタ2
                            else if($row_mouse["Y"] > 400){ $Array_flag =3; }//レジスタ3
                        }
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
                        if($row_mouse["Y"]<= 150){//問題提示欄
                            if($Array_flag ==0){
                                $DD_QQ_Count += $label_count; 
                                $QQFlag = 1;//問題提示欄内起き直し動作なので、DDに含まない
                                if($GP ==1){ 
                                    $GroupCount--;//QQはDD回数に含まない
                                }
                            }else if($Array_flag ==4){
                                $DD_AQ_Count += $label_count; //解答欄→問題提示欄
                                $DragDropCount_rev += $label_count;
                                if($GP ==1){ $AQ++;}
                            }else if($Array_flag>=1 && $Array_flag<=3){
                                $DD_RQ_Count += $label_count;//レジスタ→問題提示欄
                                $DragDropCount_rev += $label_count;
                                if($GP ==1){ $RQ++;}
                            }
                        }else if($row_mouse["Y"]<= 240){//解答欄  
                            if($Array_flag ==0){$DD_QA_Count += $label_count; //問題提示欄→解答欄
                                if($GP ==1){ $QA++;}
                                $sub_UY +=2;
                            }else if($Array_flag ==4){$DD_AA_Count += $label_count; //解答欄内入れ替え
                                $DragDropCount_rev += $label_count;
                                if($GP ==1){ $AA++;}
                            }else if($Array_flag>=1 && $Array_flag<=3) {$DD_RA_Count += $label_count; //レジスタ→解答欄
                                if($GP ==1){ $RA++;}
                                $sub_UY +=2;
                            }
                        }else if($row_mouse["Y"] > 240){   
                            if($Array_flag ==0){$DD_QR_Count += $label_count; //問題提示欄→レジスタ
                                if($GP ==1){ $QR++;} 
                                $sub_UY +=2;
                            }else if($Array_flag ==4){$DD_AR_Count += $label_count; //解答欄→レジスタ
                                if($GP ==1){ $AR++;} 
                                $sub_UY +=2;
                            }else if($Array_flag ==1){ 
                                if($GP ==1){ $RR++;} 
                                if($row_mouse["Y"]<=320){ //レジスタ1内入れ替え
                                    $DD_RR_Count += $label_count; 
                                    $DragDropCount_rev += $label_count;
                                }else{ //レジスタ1→他のレジスタ
                                    $DD_RR_rev += $label_count; 
                                    $sub_UY +=2;
                                } 
                            }else if($Array_flag ==2){ 
                                if($GP ==1){ $RR++;} 
                                if($row_mouse["Y"] > 320 and $row_mouse["Y"]<=400){ //レジスタ2内入れ替え
                                    $DD_RR_Count += $label_count; 
                                    $DragDropCount_rev += $label_count;
                                }else{ //レジスタ2→他のレジスタ
                                    $DD_RR_rev += $label_count; 
                                    $sub_UY +=2;
                                } 
                            }else if($Array_flag ==3){ 
                                if($GP ==1){ $RR++;} 
                                if($row_mouse["Y"] >400){ //レジスタ3内入れ替え
                                   $DD_RR_Count += $label_count; 
                                   $DragDropCount_rev += $label_count;
                                }else{ //レジスタ3→他のレジスタ
                                   $DD_RR_rev += $label_count; 
                                   $sub_UY +=2;
                                }
                            }
                        }
                        $GP = 0;
                        if($QQFlag == 0){
                            $DragDropCount++;//DD回数
                        }
                    }
                    $Group_check = strstr($row_mouse["Label"],"#");//複数個のラベルをグループ化したかチェック
                    if($Group_check){
                        $GroupCount++;//グループ化回数
                        $GP =1;
                    }
                    $QQFlag = 0;
                    $ResistCount = 0;//レジスタ回数は今回は0
                    $X_preserve[$mouse_count] = $row_mouse["X"];
                    $Y_preserve[$mouse_count] = $row_mouse["Y"];
                    $Time_preserve[$mouse_count] = $row_mouse["Time"];
                    $DD_flag_preserve[$mouse_count] = $DD_flag;
                    $before_Time =$row_mouse["Time"];//次回のために今回の値を記録
                    $before_X= $row_mouse["X"];
                    $before_Y= $row_mouse["Y"];
                    $mouse_count++;
                }
                $mouse_count = 0;
                foreach($X_preserve as $X1){
                    if($mouse_count != 0){
                        $variate = $X1 - $X2;
                        UFlag1($DD_flag_preserve[$mouse_count],$X1,$Time_preserve[$mouse_count]);
                    }
                    $X2 = $X1;
                    $mouse_count++;
                }
                $TimeX_req ="";
                $TimeY_req ="";
                $z=0;
                foreach($UTurn_Time as $value){
                    if($z==0){
                        $TimeX_req = $TimeX_req." and (Time = ".$value;
                    }else{
                        $TimeX_req = $TimeX_req." or Time = ".$value;
                    }
                        $z++; 
                }
                if($z!=0){
                    $TimeX_req = $TimeX_req.")";
                    $z =0;
                }
                $sql_UTurndef = "update linedatamouse set UTurnX = 0,UTurnY=0  where UID= ".$UID_reserve[$i]. " and WID = ".$WID_reserve[$i];
                $res_UTurndef = mysql_query($sql_UTurndef,$conn) or die("接続エラーupdata1");
                $sql_UTurnX = "update linedatamouse set UTurnX = 1 where UID= ".$UID_reserve[$i]. " and WID = ".$WID_reserve[$i].$TimeX_req;
                $res_UTurnX = mysql_query($sql_UTurnX,$conn) or die("接続エラーupdate2");
                $UTurnCount_X = $UTurnCount;
                $UTurnCount_XinDD = $UTurnCountinDD;
                $mouse_count = 0;
                $UTurnCount = 0;
                $UTurnCountinDD = 0;
                $UTurnFlag = 0;
                $move_distance = 0;
                $variate = 0;
                $time_sub = 0;
                $point_sub = 0;
                $UTurn_Time = array();
  
                foreach($Y_preserve as $Y1){
                    if($mouse_count != 0){
                        $variate = $Y1 - $Y2;
                        UFlag1($DD_flag_preserve[$mouse_count],$Y1,$Time_preserve[$mouse_count]);
                    }
                    $Y2 = $Y1;
                    $mouse_count++;
                }
                foreach($UTurn_Time as $value2){
                    if($z==0){
                        $TimeY_req = $TimeY_req." and (Time = ".$value2;
                    }else{
                        $TimeY_req = $TimeY_req." or Time = ".$value2;
                    }
                    $z++; 
                }
                if($z!=0){
                    $TimeY_req = $TimeY_req.")";
                }
                $sql_UTurnY = "update linedatamouse set UTurnY = 1 where UID= ".$UID_reserve[$i]. " and WID = ".$WID_reserve[$i].$TimeY_req;
                $res_UTurnY = mysql_query($sql_UTurnY,$conn) or die("接続エラーupdata3");
                $UTurnCount_Y = $UTurnCount;
                $UTurnCount_YinDD = $UTurnCountinDD;
                $UTurnCount =0;
                $UTurnCountinDD = 0;
                $AveSpeed = $Distance/($before_Time - $StartTime);//平均速度の計算
                $AveSpeed = round($AveSpeed,3);//平均速度
                $Distance = round($Distance,3);//距離
                $MaxSpeed = round($MaxSpeed,3);//最大速度
                $MinSpeed = round($MinSpeed,3);//最小速度

                $sql_ins = "insert into trackdata (UID,WID,Distance,AveSpeed,MaxSpeed,MinSpeed,StartTime,DStartTime,MaxStopTime,MinStopTime,MaxDCTime,MaxDragDropTime,MinDragDropTime,
                            MaxDropDragTime,MinDropDragTime,GroupCount,ResistCount,DragDropCount,DragDropCount_rev,DD_QA_Count,DD_QR_Count,DD_AQ_Count,DD_AA_Count,DD_AR_Count,
                            DD_RQ_Count,DD_RA_Count,DD_RR_Count,DD_RR_rev,UTurnCount_X,UTurnCount_Y,UTurnCount_XinDD,UTurnCount_YinDD,point)
                 VALUES($UID_reserve[$i],$WID_reserve[$i],$Distance,$AveSpeed,$MaxSpeed,
                $MinSpeed,$StartTime,$DStartTime,$MaxStopTime,$MinStopTime,$MaxDCTime,$MaxDragDropTime,$MinDragDropTime,$MaxDropDragTime,
                $MinDropDragTime,$GroupCount,$ResistCount,$DragDropCount,$DragDropCount_rev,$DD_QA_Count,$DD_QR_Count,$DD_AQ_Count,
                $DD_AA_Count,$DD_AR_Count,$DD_RQ_Count,$DD_RA_Count,$DD_RR_Count,$DD_RR_rev,$UTurnCount_X,$UTurnCount_Y,$UTurnCount_XinDD,$UTurnCount_YinDD,0)";
                $res = mysql_query($sql_ins,$conn) or die("接続エラーinsert1");
                $sql_Time = "update linedata set Time =".$before_Time." where UID =".$UID_reserve[$i]." and WID=".$WID_reserve[$i];
                $res_Time = mysql_query($sql_Time,$conn) or die("接続エラーupdata4");                    
            }//履歴データがない場合　　　おわり
        }
    }
    //trackdata作成プログラムおわり--------------------------------------------------------------------
  
    //採点用プログラム---------------------------------------------------------------------------------
    $sql_point = "select linedata.UID,quesorder.OID,linedata.EndSentence from linedata,quesorder where linedata.WID=quesorder.WID order by quesorder.OID,linedata.UID";//ユーザごとの解答文取得
    echo $sql_point."<br>";
    $res_point =  mysql_query($sql_point,$conn) or die("接続エラー5_linedata_quesorder");
    $count = 0;
    $WID_reserve =array();
    $UID_reserve =array();
    $score =0;//点数
    $insert_score;//記録用点数(重複避ける用)

    while ($row_point = mysql_fetch_array($res_point)){
        $WID_reserve[$count] = $ques_order[$row_point["OID"]];//WIDの値を記録
        $UID_reserve[$count] = $row_point["UID"];//UIDの値を記録
        $EndSentence = $row_point["EndSentence"];
        if($count != 0){
            if($WID_reserve[$count] != $WID_reserve[$count-1]){//問題が変わる時
                $sql_answer = "select WID,Sentence from question_info where WID= ".$WID_reserve[$count];//問題文読み込み
                $res_answer =  mysql_query($sql_answer,$conn) or die("接続エラー6");
                $row_answer = mysql_fetch_array($res_answer);
                $Answer = ucfirst($row_answer["Sentence"]);
            }
        }else{//最初の問題は無条件で問題更新
            $sql_answer = "select WID,Sentence from question_info where WID= ".$WID_reserve[$count];//問題文読み込み
            $res_answer =  mysql_query($sql_answer,$conn) or die("接続エラー7");
            $row_answer = mysql_fetch_array($res_answer);
            $Answer = ucfirst($row_answer["Sentence"]);
        }
        
        if($Answer == $EndSentence){//解答文と正答が一致したとき
            $score = 10;
        }else{//解答文と正答が異なっていたとき
            $sql_part = "select * from partans where WID= ".$WID_reserve[$count]." order by type";//部分点フレーズ読み込み
            $res_part =  mysql_query($sql_part,$conn) or die("接続エラー8");
            while ($row_part = mysql_fetch_array($res_part)){//部分点フレーズがある限り読み込みを繰り返す
                if($row_part["type"] == 0){//全文一致検索
                    if(strcasecmp($row_part["PartSentence"],$EndSentence)==0){
                        $score =$row_part["Point"];//部分点付加
                        if($insert_score <= $score){
                            $insert_score = $score;
                        }
                        break;//一致する部分点があったので抜け
                    }                             
                }else if($row_part["type"] == 1){//文中一致検索
                    $search_word =" ".$row_part["PartSentence"]." ";//部分点フレーズの前後にスペースを入れる（文中検索をするため）
                    $check = strstr($EndSentence,$search_word);//解答文が部分点フレーズを含んでいるか判定
                    if($check){//含んでいたら
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
                        if($check and $row_part["type2"] == -1){
                            //echo "検索ヒット(頭)<br>";
                            $score =$row_part["Point"];//部分点付加
                            if($insert_score <= $score){
                                $insert_score = $score;
                            }
                            break;//一致する部分点があったので抜け

                        }else if($check and $row_part["type2"] == 3){
                            $search_word1 =" ".$row_part["PartSentence2"].".";
                            $search_word2 =" ".$row_part["PartSentence2"]."?";//文末がピリオドと？と!のケースを考慮
                            $search_word3 =" ".$row_part["PartSentence2"]."!";
                            $check1 = strstr($EndSentence,$search_word1);
                            $check2 = strstr($EndSentence,$search_word2);
                            $check3 = strstr($EndSentence,$search_word3);
                            if($check1 or $check2 or $check3){
                            //echo "検索ヒット(後ろ)<br>";
                            $score =$row_part["Point"];//部分点付加
                                if($insert_score <= $score){
                                    $insert_score = $score;
                                }
                                break;//一致する部分点があったので抜け
                            }
                        }else{
                            $score = 0;
                        }
                        
                }else if($row_part["type"] == 3){//後方一致検索
                    $search_word1 =" ".$row_part["PartSentence"].".";
                    $search_word2 =" ".$row_part["PartSentence"]."?";//文末がピリオドと？と!のケースを考慮
                    $search_word3 =" ".$row_part["PartSentence"]."!";
                    $check = strstr($EndSentence,$search_word1);
                    $check2 = strstr($EndSentence,$search_word2);
                    $check3 = strstr($EndSentence,$search_word3);
                    if($check or $check2 or $check3){
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
        }
        $sql_ins = "update trackdata SET Point = ".$score." where UID = ".$UID_reserve[$count]." and WID = ".$WID_reserve[$count];
        $res = mysql_query($sql_ins,$conn) or die("接続エラーupdate5");
        $count++;
        $score = 0;
        $insert_score=0;
    }
    //採点用ここまで----------------------------------------------------------------------
    header('Location: ./main.php');
?>

</body>
</html>