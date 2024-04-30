﻿<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
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
echo $_POST["correl"];
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>クラスタリング</title>
<script type="text/javascript" src="./prototype.js"></script>
<script type="text/javascript">
<!--
function clus_result(num) {
    //alert(num);
    var b = num;
	var $a = 'id='+encodeURIComponent(b);

		//▲マウスデータの取得
	//ドラッグ開始地点の保存


	new Ajax.Request('http://lmo.cs.inf.shizuoka.ac.jp/~miki/rireki/output.php',
{
	method: 'post',
	onSuccess: getA,
	onFailure: getE,
	parameters: $a
});
	function getA(req){
		document.getElementById('test_test').innerHTML=req.responseText;
	}
	function getE(req){
		alert("学習者分析エラー");
	}    
}
// -->
</script>


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


<div align ="left">
<?php
   $student_count = $_POST["student_count"];
   $question_count = $_POST["question_count"];
   $data_count = $_POST["data_count"];
   $termr1 =  $_POST["term_r"];
   $termr2 = str_replace("linedata","b",$termr1);
   $termr3 = str_replace("trackdata","a",$termr2);
   $term_r = str_replace("AnswerQues","c",$termr3);

   $final = $_REQUEST['cluster_num'];//最終クラスタ数
   //echo $final."<br>";
   //echo $term_s."<br>";
   //echo $term_q."<br>";
   //echo "ddd:".$term_r."<br>";

?>
<font size="6">
<?php
    if($_POST["correl"] =="student"){
?>
クラスタリング(生徒ごと)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
標本数:<?php echo $student_count;?>
<?php
    $sample = $student_count;
    }else if($_POST["correl"] =="ques"){
?>
クラスタリング(問題ごと)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
標本数:<?php echo $question_count;?>
<?php
    $sample = $question_count;
    }else{
?>
クラスタリング(履歴データごと)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
標本数:<?php echo $data_count;?>
<?php
    $sample = $data_count;
    }
    $stu_count =$student_count;
?>
</font>
<?php
   /*
       if (is_numeric($final)) {
           if(fmod($final,1) == 0){
               if($final > $sample){
                   echo "クラスタ数が標本数よりも大きいです<br>";
                   exit();
               }
           }else{
               echo "整数ではありません<br>";
               exit();
           }
    } else {
        echo "入力が整数ではありません";
        exit();
    }
    */

?>

<br>



<?php
 //echo $_SESSION["correl_mode"];
        require "dbc.php";
        $x_sub =array();
        $y_sub =array();
        $cluster_ID =array();
        $parameter = $_REQUEST['parameter'];
        //echo $parameter[0];
        //echo $parameter[1];
if($_POST["correl"] == "student"){
/*
        $sql = "select a.UID,AVG(a.point),AVG(b.Time),AVG(a.Distance),AVG(a.Avespeed),AVG(a.MaxSpeed),AVG(a.MinSpeed),AVG(a.StartTime),
        AVG(a.DStartTime),AVG(a.MaxStopTime),AVG(a.MinStopTime),AVG(a.MaxDragDropTime),AVG(a.MinDragDropTime),AVG(a.MaxDropDragTime),
        AVG(a.MinDropDragTime),AVG(a.GroupCount),AVG(a.DragDropCount),AVG(a.UTurnCount) from trackdata as a, linedata as b 
        where a.UID = b.UID ".$term_r." group by a.UID ";
        */
        //echo $sql;
        $sql = "select a.UID,AVG(a.point),AVG(b.TF),AVG(b.Time),AVG(a.Distance),AVG(a.Avespeed),AVG(a.MaxSpeed),AVG(a.MinSpeed),AVG(a.StartTime),
        AVG(a.DStartTime),AVG(a.MaxStopTime),AVG(a.MinStopTime),AVG(a.MaxDragDropTime),AVG(a.MinDragDropTime),AVG(a.MaxDropDragTime),
        AVG(a.MinDropDragTime),AVG(a.GroupCount),AVG(a.DragDropCount),AVG(a.UTurnCount) from trackdata as a, linedata as b, AnswerQues as c   
        where a.UID = b.UID and a.WID = b.WID and a.UID = c.UID and b.UID = c.UID and b.AID = c.AID ".$term_r. "group by a.UID 
        order by a.UID";
        $res =  mysql_query($sql,$conn) or die("接続エラー");
        /*
        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>UID</td>";
        echo "<td>平均得点</td>";
        echo "<td>正解率</td>";
        echo "<td>平均解答時間</td>";
        echo "<td>総移動距離</td>";
        echo "<td>平均速度</td>";
        echo "<td>最大速度</td>";
        echo "<td>最小速度</td>";
        echo "<td>初動時間</td>";
        echo "<td>Drag開始時間</td>";
        echo "<td>最大静止時間</td>";
        echo "<td>最小静止時間</td>";
        echo "<td>最大Drag⇒Drop時間</td>";
        echo "<td>最小Drag⇒Drop時間</td>";
        echo "<td>最大Drop⇒Drag時間</td>";
        echo "<td>最小Drop⇒Drag時間</td>";
        echo "<td>区切り追加回数</td>";
        echo "<td>DD回数</td>";
        echo "<td>Uターン回数</td>";
        echo "</tr>";
        */

        $count = 0;
        $sum_point_hokan = 0;

        $counter = 1;
        while ($row = mysql_fetch_array($res)){
            if($parameter[0] =="point"){
                $x_sub[$count] =$row["AVG(a.point)"];
            }else if($parameter[0] =="Time"){
                $x_sub[$count] =$row["AVG(b.Time)"];
            }else if($parameter[0] =="DragDropCount"){
                $x_sub[$count] =$row["AVG(a.DragDropCount)"];
            }else if($parameter[0] =="UTurnCount"){
                $x_sub[$count] =$row["AVG(a.UTurnCount)"];
            }

            if($parameter[1] =="point"){
                $y_sub[$count] =$row["AVG(a.point)"];
            }else if($parameter[1] =="Time"){
                $y_sub[$count] =$row["AVG(b.Time)"];
            }else if($parameter[1] =="DragDropCount"){
                $y_sub[$count] =$row["AVG(a.DragDropCount)"];
            }else if($parameter[1] =="UTurnCount"){
                $y_sub[$count] =$row["AVG(a.UTurnCount)"];
            }else{
                $y_sub[$count] =0;
            }


            $cluster_ID[$count] =$row["UID"];
            //$cluster_NO[$count] =$counter;
            $counter++;
            /*
            echo "<tr>";
            echo "<td>".$row["UID"]."</td>";
            echo "<td>".$row["AVG(a.point)"]."</td>";
            $row["AVG(b.TF)"] = round($row["AVG(b.TF)"] *100,1);
            echo "<td>".$row["AVG(a.point)"]."</td>";
            echo "<td>".$row["AVG(b.Time)"]."</td>";
            echo "<td>".$row["AVG(a.Distance)"]."</td>";
            echo "<td>".$row["AVG(a.Avespeed)"]."</td>";
            echo "<td>".$row["AVG(a.MaxSpeed)"]."</td>";
            echo "<td>".$row["AVG(a.MinSpeed)"]."</td>";
            echo "<td>".$row["AVG(a.StartTime)"]."</td>";
            echo "<td>".$row["AVG(a.DStartTime)"]."</td>";
            echo "<td>".$row["AVG(a.MaxStopTime)"]."</td>";
            echo "<td>".$row["AVG(a.MinStopTime)"]."</td>";
            echo "<td>".$row["AVG(a.MaxDragDropTime)"]."</td>";
            echo "<td>".$row["AVG(a.MinDragDropTime)"]."</td>";
            echo "<td>".$row["AVG(a.MaxDropDragTime)"]."</td>";
            echo "<td>".$row["AVG(a.MinDropDragTime)"]."</td>";
            echo "<td>".$row["AVG(a.GroupCount)"]."</td>";
            echo "<td>".$row["AVG(a.DragDropCount)"]."</td>";
            echo "<td>".$row["AVG(a.UTurnCount)"]."</td>";
            echo "</tr>";
            */
            $correl_array[0][$count] = $row["AVG(a.point)"]."</td>";
            $correl_array[1][$count] = $row["AVG(a.point)"]."</td>";
            $correl_array[2][$count] = $row["AVG(b.Time)"]."</td>";
            $correl_array[3][$count] = $row["AVG(a.Distance)"]."</td>";
            $correl_array[4][$count] = $row["AVG(a.Avespeed)"]."</td>";
            $correl_array[5][$count] = $row["AVG(a.MaxSpeed)"]."</td>";
            $correl_array[6][$count] = $row["AVG(a.MinSpeed)"]."</td>";
            $correl_array[7][$count] = $row["AVG(a.StartTime)"]."</td>";
            $correl_array[8][$count] = $row["AVG(a.DStartTime)"]."</td>";
            $correl_array[9][$count] = $row["AVG(a.MaxStopTime)"]."</td>";
            $correl_array[10][$count] = $row["AVG(a.MinStopTime)"]."</td>";
            $correl_array[11][$count] = $row["AVG(a.MaxDragDropTime)"]."</td>";
            $correl_array[12][$count] = $row["AVG(a.MinDragDropTime)"]."</td>";
            $correl_array[13][$count] = $row["AVG(a.MaxDropDragTime)"]."</td>";
            $correl_array[14][$count] = $row["AVG(a.MinDropDragTime)"]."</td>";
            $correl_array[15][$count] = $row["AVG(a.GroupCount)"]."</td>";
            $correl_array[16][$count] = $row["AVG(a.DragDropCount)"]."</td>";
            $correl_array[17][$count] = $row["AVG(a.UTurnCount)"]."</td>";
            $count++;
        }

        //echo "</table>";

        $column = array("平均得点","正解率","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
        ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
        ,"区切り追加回数","DD回数","Uターン回数");//配列定義用


}else if($_POST["correl"] =="ques"){//問題用
/*
        $sql = "select a.WID,AVG(a.point),AVG(b.Time),AVG(a.Distance),AVG(a.Avespeed),AVG(a.MaxSpeed),AVG(a.MinSpeed),AVG(a.StartTime),
        AVG(a.DStartTime),AVG(a.MaxStopTime),AVG(a.MinStopTime),AVG(a.MaxDragDropTime),AVG(a.MinDragDropTime),AVG(a.MaxDropDragTime),
        AVG(a.MinDropDragTime),AVG(a.GroupCount),AVG(a.DragDropCount),AVG(a.UTurnCount) from trackdata as a, linedata as b 
        where a.WID=b.WID".$term_r." group by a.WID";
 */
         $sql = "select a.WID,AVG(a.point),AVG(b.TF),AVG(b.Time),AVG(a.Distance),AVG(a.Avespeed),AVG(a.MaxSpeed),AVG(a.MinSpeed),AVG(a.StartTime),
        AVG(a.DStartTime),AVG(a.MaxStopTime),AVG(a.MinStopTime),AVG(a.MaxDragDropTime),AVG(a.MinDragDropTime),AVG(a.MaxDropDragTime),
        AVG(a.MinDropDragTime),AVG(a.GroupCount),AVG(a.DragDropCount),AVG(a.UTurnCount) from trackdata as a, linedata as b, AnswerQues as c   
        where a.UID = b.UID and a.WID = b.WID and a.UID = c.UID and b.UID = c.UID and b.AID = c.AID ".$term_r. "group by a.WID 
        order by a.WID";
        //echo $sql;

        $res =  mysql_query($sql,$conn) or die("接続エラー");
        /*
        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>WID</td>";
        echo "<td>平均得点</td>";
        echo "<td>正解率</td>";
        echo "<td>平均解答時間</td>";
        echo "<td>総移動距離</td>";
        echo "<td>平均速度</td>";
        echo "<td>最大速度</td>";
        echo "<td>最小速度</td>";
        echo "<td>初動時間</td>";
        echo "<td>Drag開始時間</td>";
        echo "<td>最大静止時間</td>";
        echo "<td>最小静止時間</td>";
        echo "<td>最大Drag⇒Drop時間</td>";
        echo "<td>最小Drag⇒Drop時間</td>";
        echo "<td>最大Drop⇒Drag時間</td>";
        echo "<td>最小Drop⇒Drag時間</td>";
        echo "<td>区切り追加回数</td>";
        echo "<td>DD回数</td>";
        echo "<td>Uターン回数</td>";
        echo "<td>単語数</td>";
        echo "</tr>";
        */
        $count = 0;
        $sum_point_hokan = 0;
        while ($row = mysql_fetch_array($res)){
            //単語数取得用のsql文
            $sql2 ="select * from question_info where WID = ".$row["WID"];
            $res2 =  mysql_query($sql2,$conn) or die("接続エラー2");
            $row2 = mysql_fetch_array($res2);


            
            if($parameter[0] =="point"){
                $x_sub[$count] =$row["AVG(a.point)"];
            }else if($parameter[0] =="Time"){
                $x_sub[$count] =$row["AVG(b.Time)"];
            }else if($parameter[0] =="DragDropCount"){
                $x_sub[$count] =$row["AVG(a.DragDropCount)"];
            }else if($parameter[0] =="UTurnCount"){
                $x_sub[$count] =$row["AVG(a.UTurnCount)"];
            }else if($parameter[0] =="wordnum"){
                $x_sub[$count] =$row2["wordnum"];
            }

            if($parameter[1] =="point"){
                $y_sub[$count] =$row["AVG(a.point)"];
            }else if($parameter[1] =="Time"){
                $y_sub[$count] =$row["AVG(b.Time)"];
            }else if($parameter[1] =="DragDropCount"){
                $y_sub[$count] =$row["AVG(a.DragDropCount)"];
            }else if($parameter[1] =="UTurnCount"){
                $y_sub[$count] =$row["AVG(a.UTurnCount)"];
            }else if($parameter[1] =="wordnum"){
                $y_sub[$count] =$row2["wordnum"];
            }else{
                $y_sub[$count] =0;
            }
            

            $cluster_ID[$count] =$row["WID"];
            /*
            echo "<tr>";
            echo "<td>".$row["WID"]."</td>";
            echo "<td>".$row["AVG(a.point)"]."</td>";
            $row["AVG(b.TF)"] = round($row["AVG(b.TF)"] *100,1);
            echo "<td>".$row["AVG(a.point)"]."</td>";
            echo "<td>".$row["AVG(b.Time)"]."</td>";
            echo "<td>".$row["AVG(a.Distance)"]."</td>";
            echo "<td>".$row["AVG(a.Avespeed)"]."</td>";
            echo "<td>".$row["AVG(a.MaxSpeed)"]."</td>";
            echo "<td>".$row["AVG(a.MinSpeed)"]."</td>";
            echo "<td>".$row["AVG(a.StartTime)"]."</td>";
            echo "<td>".$row["AVG(a.DStartTime)"]."</td>";
            echo "<td>".$row["AVG(a.MaxStopTime)"]."</td>";
            echo "<td>".$row["AVG(a.MinStopTime)"]."</td>";
            echo "<td>".$row["AVG(a.MaxDragDropTime)"]."</td>";
            echo "<td>".$row["AVG(a.MinDragDropTime)"]."</td>";
            echo "<td>".$row["AVG(a.MaxDropDragTime)"]."</td>";
            echo "<td>".$row["AVG(a.MinDropDragTime)"]."</td>";
            echo "<td>".$row["AVG(a.GroupCount)"]."</td>";
            echo "<td>".$row["AVG(a.DragDropCount)"]."</td>";
            echo "<td>".$row["AVG(a.UTurnCount)"]."</td>";
            echo "<td>".$row2["wordnum"]."</td>";
            echo "</tr>";
            */
            $correl_array[0][$count] = $row["AVG(a.point)"]."</td>";
            $correl_array[1][$count] = $row["AVG(a.point)"]."</td>";
            $correl_array[2][$count] = $row["AVG(b.Time)"]."</td>";
            $correl_array[3][$count] = $row["AVG(a.Distance)"]."</td>";
            $correl_array[4][$count] = $row["AVG(a.Avespeed)"]."</td>";
            $correl_array[5][$count] = $row["AVG(a.MaxSpeed)"]."</td>";
            $correl_array[6][$count] = $row["AVG(a.MinSpeed)"]."</td>";
            $correl_array[7][$count] = $row["AVG(a.StartTime)"]."</td>";
            $correl_array[8][$count] = $row["AVG(a.DStartTime)"]."</td>";
            $correl_array[9][$count] = $row["AVG(a.MaxStopTime)"]."</td>";
            $correl_array[10][$count] = $row["AVG(a.MinStopTime)"]."</td>";
            $correl_array[11][$count] = $row["AVG(a.MaxDragDropTime)"]."</td>";
            $correl_array[12][$count] = $row["AVG(a.MinDragDropTime)"]."</td>";
            $correl_array[13][$count] = $row["AVG(a.MaxDropDragTime)"]."</td>";
            $correl_array[14][$count] = $row["AVG(a.MinDropDragTime)"]."</td>";
            $correl_array[15][$count] = $row["AVG(a.GroupCount)"]."</td>";
            $correl_array[16][$count] = $row["AVG(a.DragDropCount)"]."</td>";
            $correl_array[17][$count] = $row["AVG(a.UTurnCount)"]."</td>";
            $correl_array[18][$count] = $row2["wordnum"]."</td>";
            $count++;



        }
        //echo "</table>";

        $column = array("得点","正解率","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
        ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
        ,"区切り追加回数","DD回数","Uターン回数","単語数");//配列定義用
}


?>










<?php
     
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
        //echo $varp;
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
?>



<?php

   $x_sub =array();
   $y_sub =array();

   for($i=0;$i<50;$i++){
       $x_sub[$i]= mt_rand(1,1000);
       $x_sub[$i]= $x_sub[$i]/1000;
       $y_sub[$i]= mt_rand(1,1000);
       $y_sub[$i]= $y_sub[$i]/1000;
       $cluster_NO[$i] =$i+1;
   }
   echo "xの乱数<br>";
   for($i=0;$i<50;$i++){
       echo $x_sub[$i]."<br>";
   }
   echo "yの乱数<br>";
   for($i=0;$i<=50;$i++){
       echo $y_sub[$i]."<br>";
   }
/*   
   $x_sub =array(5,4,1,5,5);
   $y_sub =array(1,2,5,4,5); //テストデータ
   */

   $x_sub_ave =ave($x_sub);
   $y_sub_ave =ave($y_sub);
   $x_sub_sd = sd($x_sub);
   $y_sub_sd = sd($y_sub);
   /*
   echo "平均".$x_sub_ave."<br>";
   echo "標準偏差".$x_sub_sd."<br>";
   */
   /*
   for($i =0;$i<count($x_sub);$i++){
       $x[$i] = ($x_sub[$i] - $x_sub_ave) / $x_sub_sd;
       $y[$i] = ($y_sub[$i] - $y_sub_ave) / $y_sub_sd;
   }
   */
   
   $x = $x_sub;
   $y = $y_sub;//テスト用
   /*
   foreach($x as $tes){
    echo $tes."  ";
   }
   echo "<br>";
   foreach($y as $tes){
    echo $tes."  ";
   }
   echo "<br>";
   */
   /*
   $x = $x_sub;
   $y = $y_sub;
   */
   //$x=array(11,9,5,3);
   //$y=array(0,0,0,0);
            //echo $x[0]."<br>";
            //echo $x[1]."<br>";
            //echo "<br><br><br><br>";
   $combi_x =array();
   $combi_y =array();
   $ward = array();
$student_count = count($x);//クラスター数

for($i = 0; $i<$student_count; $i++){//配列をクラスターに入れる。
    $group_x[$i][0] =$x[$i];//標準化後の値
    $group_y[$i][0] =$y[$i];
    $value_x[$i][0] =$x_sub[$i];//標準化前の値
    $value_y[$i][0] =$y_sub[$i];
    $group_ID[$i][0] =$cluster_ID[$i];
    $test_NO[$i][0] = $cluster_NO[$i];//テスト用ＮＯ記録用
}



for($i = 0;$i<$student_count-1;$i++){
    for($j = $i+1; $j<$student_count;$j++){
       $combi_x = array();
       $combi_y = array();
        $combi_x = array_merge($group_x[$i],$group_x[$j]);//配列の結合
        $combi_y = array_merge($group_y[$i],$group_y[$j]);

    $center_x = ave($combi_x);
    $center_y = ave($combi_y);

$ward[$i][$j] = pow($group_x[$i][0] - $center_x,2) + pow($group_x[$j][0] - $center_x,2) + pow($group_y[$i][0] - $center_y,2)+ pow($group_y[$j][0] - $center_y,2);

//echo $ward[$i][$j]."<br>";
    
    }
}


    for($i = 0;$i<$student_count-1;$i++){
        for($j = $i+1; $j<$student_count;$j++){
            if($i==0 && $j==1){
                //echo "初回<br>";
                $mini =$i;
                $minj =$j;
                $minward =$ward[$i][$j];
            }else{
                //echo "行きりかえ<br>";
                if($ward[$i][$j] < $ward[$mini][$minj]){
                    //echo "小さくなりました<br>"; 
                    $mini =$i;
                    $minj =$j;
                    $minward =$ward[$i][$j];
                }else{
                    //echo "大きくなりました<br>";
                }
            }
        }
    }
        //echo "最小の配列[".$mini."],[".$minj."]<br>";
        //echo "最小の値".$minward."<br>";




//$student_count--;//クラスタ―数減少 

$group_x[$mini] = array_merge($group_x[$mini],$group_x[$minj]);
$group_y[$mini] = array_merge($group_y[$mini],$group_y[$minj]);//群の結合

$value_x[$mini] = array_merge($value_x[$mini],$value_x[$minj]);
$value_y[$mini] = array_merge($value_y[$mini],$value_y[$minj]);//群の結合

$group_ID[$mini] = array_merge($group_ID[$mini],$group_ID[$minj]);
$test_NO[$mini] = array_merge($test_NO[$mini],$test_NO[$minj]);//テスト用



$group_x[$minj] = array();
$group_y[$minj] = array();//結合前の配列の初期化
$value_x[$minj] = array();
$value_y[$minj] = array();//結合前の配列の初期化
$group_ID[$minj] = array();
$test_NO[$minj] = array();

$group_xdummy = $group_x;//配列の値の退避
$group_ydummy = $group_y;
$value_xdummy = $value_x;//配列の値の退避
$value_ydummy = $value_y;
$group_IDdummy = $group_ID;
$test_NOdummy = $test_NO;

$group_x = array();
$group_y = array();
$value_x = array();
$value_y = array();
$group_ID = array();
$test_NO = array();
$j = 0;
for($i = 0; $i<$student_count; $i++){//クラスタの再構成（これによってクラスタが1個減る）
    if($i < $minj){
        $group_x[$i] = $group_xdummy[$i];
        $group_y[$i] = $group_ydummy[$i];
        $value_x[$i] = $value_xdummy[$i];
        $value_y[$i] = $value_ydummy[$i];
        $group_ID[$i] = $group_IDdummy[$i];
        $test_NO[$i] = $test_NOdummy[$i];
        $j++;
    }else if($i == $minj){

    }else{
        $group_x[$j] = $group_xdummy[$i];
        $group_y[$j] = $group_ydummy[$i];
        $value_x[$j] = $value_xdummy[$i];
        $value_y[$j] = $value_ydummy[$i];
        $group_ID[$j] = $group_IDdummy[$i];
        $test_NO[$j] = $test_NOdummy[$i];
        $j++;
    }
}

$student_count--;

echo "---ID---<br>";
for($i=0;$i<$student_count;$i++){
    echo "クラスタ".$i.": ";
foreach($group_ID[$i] as $tes){
    echo $tes." ";
}
    echo "<br>";
}

echo "---ID---<br>";
for($i=0;$i<$student_count;$i++){
    echo "クラスタNO".$i.": ";
foreach($test_NO[$i] as $tes){
    echo $tes." ";
}
    echo "<br>";
}





echo "---パラメータ1---<br>";
for($i=0;$i<$student_count;$i++){
    echo "クラスタ".$i.": ";
foreach($value_x[$i] as $tes){
    echo $tes." ";
}
    echo "<br>";
}

echo "---パラメータ2---<br>";
for($i=0;$i<$student_count;$i++){
    echo "クラスタ".$i.": ";
foreach($value_y[$i] as $tes){
    echo $tes." ";
}
    echo "<br>";
}


//クラスタリング1週目終了
echo "--1週目終了--<br>";
echo "<br><br><br>";

//$student_count--;





while($student_count>2){//クラスタ数が指定した数になるまで
$ward = array();


for($i = 0;$i<$student_count-1;$i++){
    for($j = $i+1; $j<$student_count;$j++){
       $combi_x = array();
       $combi_y = array();
        $combi_x = array_merge($group_x[$i],$group_x[$j]);//配列の結合
        $combi_y = array_merge($group_y[$i],$group_y[$j]);

    $center_x = ave($combi_x);
    $center_y = ave($combi_y);
    
    $sub1_x = ave($group_x[$i]);
    $sub2_x = ave($group_x[$j]);
    $sub1_y = ave($group_y[$i]);
    $sub2_y = ave($group_y[$j]);
    /*
    if($i==0 && $j==3){
        echo "平均A:".$sub1_x."<br>";
        echo "平均B:".$sub2_x."<br>";
    }
    */
    $k =0;
    foreach($group_x[$i] as $value){//クラスターに所属している要素の数分ループする
        $ward[$i][$j] = $ward[$i][$j] + pow($group_x[$i][$k] - $center_x,2) + pow($group_y[$i][$k] - $center_y,2);
        $hokan1[$i][$j] = $hokan1[$i][$j]+pow($group_x[$i][$k] - $sub1_x,2) + pow($group_y[$i][$k] - $sub1_y,2);
        //echo"ddd<br>";
        //echo $group_x[$i][$k]."<br>";
        /*
        $ave1 = ave($group_x[$i]);
        if($i==0){
            echo "平ら".$ave1."<br>";
        echo "test".$ward[$i][$j]."<br>";
        }
        */
        $k++;
    }
    $k=0;
    foreach($group_x[$j] as $value){
        $ward[$i][$j] = $ward[$i][$j] + pow($group_x[$j][$k] - $center_x,2) + pow($group_y[$j][$k] - $center_y,2);
        $hokan2[$i][$j] = $hokan2[$i][$j]+pow($group_x[$j][$k] - $sub2_x,2) + pow($group_y[$j][$k] - $sub2_y,2);
        /*
                if($i==0 &&$j==3){
        echo "test".$ward[$i][$j]."<br>";
        }
        */
        $k++;
    }
    //$ward[$i][$j] = pow($group_x[$i][0] - $center_x,2) + pow($group_x[$j][0] - $center_x,2) + pow($group_y[$i][0] - $center_y,2)+ pow($group_y[$j][0] - $center_y,2);
    /*
    if($i==0 && $j==3){
        echo "reia".$hokan1[$i][$j]."<br>";
        echo "reib".$hokan2[$i][$j]."<br>";
    }
    */
    //echo "元".$ward[$i][$j]."<br>";
    //echo "補間1:".$hokan1[$i][$j]."<br>";
    //echo "補間2:".$hokan2[$i][$j]."<br>";
    $ward[$i][$j] = $ward[$i][$j] - $hokan1[$i][$j] - $hokan2[$i][$j];
    //echo $ward[$i][$j]."<br>";
    $hokan1 =array();
    $hokan2 =array();
    }
}

   for($i = 0;$i<$student_count-1;$i++){
        for($j = $i+1; $j<$student_count;$j++){
            if($i==0 && $j==1){
                //echo "初回<br>";
                $mini =$i;
                $minj =$j;
                $minward =$ward[$i][$j];
            }else{
                if($ward[$i][$j] < $ward[$mini][$minj]){
                    //echo "小さくなりました<br>"; 
                    $mini =$i;
                    $minj =$j;
                    $minward =$ward[$i][$j];
                }else{
                    //echo "大きくなりました<br>";
                }
            }
        }
    }
        //echo "最小の配列[".$mini."],[".$minj."]<br>";
        //echo "最小の値".$minward."<br>";

$group_x[$mini] = array_merge($group_x[$mini],$group_x[$minj]);
$group_y[$mini] = array_merge($group_y[$mini],$group_y[$minj]);//群の結合

$value_x[$mini] = array_merge($value_x[$mini],$value_x[$minj]);
$value_y[$mini] = array_merge($value_y[$mini],$value_y[$minj]);//群の結合

$group_ID[$mini] = array_merge($group_ID[$mini],$group_ID[$minj]);
$test_NO[$mini] = array_merge($test_NO[$mini],$test_NO[$minj]);

$group_x[$minj] = array();
$group_y[$minj] = array();
$value_x[$minj] = array();
$value_y[$minj] = array();
$group_ID[$minj] = array();
$test_NO[$minj] = array();

$group_xdummy = $group_x;//配列の値の退避
$group_ydummy = $group_y;
$value_xdummy = $value_x;//配列の値の退避
$value_ydummy = $value_y;
$group_IDdummy = $group_ID;
$test_NOdummy = $test_NO;
//echo $group_xdummy[3][0];
//echo $group_xdummy[3][1];
$group_x = array();
$group_y = array();
$value_x = array();
$value_y = array();
$group_ID = array();
$test_NO = array();
$j = 0;
for($i = 0; $i<$student_count; $i++){//クラスタの再構成（これによってクラスタが1個減る）
    if($i < $minj){
        $group_x[$i] = $group_xdummy[$i];
        $group_y[$i] = $group_ydummy[$i];
        $value_x[$i] = $value_xdummy[$i];
        $value_y[$i] = $value_ydummy[$i];
        $group_ID[$i] = $group_IDdummy[$i];
        $test_NO[$i] = $test_NOdummy[$i];
        $j++;
    }else if($i == $minj){

    }else{
        $group_x[$j] = $group_xdummy[$i];
        $group_y[$j] = $group_ydummy[$i];
        $value_x[$j] = $value_xdummy[$i];
        $value_y[$j] = $value_ydummy[$i];
        $group_ID[$j] = $group_IDdummy[$i];
        $test_NO[$j] = $test_NOdummy[$i];
        $j++;
    }
}

$student_count--;

//if($student_count ==$final){
/*
echo "---ID---<br>";
for($i=0;$i<$student_count;$i++){//出力(テスト)
    $z = count($group_ID[$i]);
    echo "クラスタ".$i."[".$z."]: ";
foreach($group_ID[$i] as $tes){
    echo $tes." ";
}

    echo "<br>";
}
*/

for($i=0;$i<$student_count;$i++){//出力(テスト)
    $z = count($group_ID[$i]);
    echo "クラスタNO".$i."[".$z."]: ";
foreach($test_NO[$i] as $tes){
    echo $tes." ";
}

    echo "<br>";
}



/*
echo "---パラメータ1---<br>";
for($i=0;$i<$student_count;$i++){//出力(テスト)
    $z = count($value_x[$i]);
    echo "クラスタ".$i."[".$z."]: ";
foreach($value_x[$i] as $tes){
    echo $tes." ";
}
    echo "<br>";
}

echo "---パラメータ2---<br>";
for($i=0;$i<$student_count;$i++){//出力(テスト)
    $z = count($value_y[$i]);
    echo "クラスタ".$i."[".$z."]: ";
foreach($value_y[$i] as $tes){
    echo $tes." ";
}
    echo "<br>";
}
*/
//クラスタリング1週目終了
echo "--週回終了--<br>";
echo "<br><br><br>";
//}
}
$group_IDdummy =$group_ID;
$test_NOdummy =$test_NO;

$group_xdummy = $group_x;
$group_ydummy = $group_y;
$value_xdummy =$value_x;
$value_yduumy = $value_y;
$ave = array();


for($i = 0;$i<$final;$i++){
    $ave[$i] = ave($group_x[$i]);
}

$_SESSION["group_ID"]=$group_ID;
$_SESSION["value_x"]=$value_x;
$_SESSION["value_y"]=$value_y;
$_SESSION["ave"] =$ave;
?>



<form name="navi">
<select name="contents" onchange="clus_result(this[this.selectedIndex].value)">
<?php
    for($i=1;$i<=$final;$i++){
?>
  <option value="<?php echo $i;?>"><?php echo $i;?></option>
<?php
    }
?>
</select>
</form>

<font size="1">
<table border="1">
<?php
 /*
    echo "<tr>";
    echo "<td>要素名</td>";
    $ff = 0;
    foreach($group_ID[0] as $tes){
        echo "<td>".$tes."</td>";
        $ff++;
    }
    echo "</tr>";
    echo "<tr>";
    echo "<td>パラメータ1</td>";
    foreach($value_x[0] as $tes){
        echo "<td>".$tes."</td>";
    }
    echo "</tr>";
    echo "<tr>";
    echo "<td>パラメータ2</td>";
    foreach($value_y[0] as $tes){
        echo "<td>".$tes."</td>";
    }
    echo "</tr>";
    */
?>
</table>
</font>
<?php
    $cc = 1;
    $correl = $_POST["correl"];
    if($correl == "student"){
        
        foreach($group_ID[0] as $tes){
            if($cc == 1){
                $term_r = $term_r." and (b.UID= ".$tes." ";
            }else if($cc == $ff){
                $term_r = $term_r." or b.UID= ".$tes.")";
            }else{
                $term_r = $term_r." or b.UID= ".$tes." ";
            }
            $cc++;
        }
        
    }else if($correl == "ques"){

         foreach($group_ID[0] as $tes){
            if($cc == 1){
                $term_r = $term_r." and (b.WID= ".$tes." ";
            }else if($cc == $ff){
                $term_r = $term_r." or b.WID= ".$tes.")";
            }else{
                $term_r = $term_r." or b.WID= ".$tes." ";
            }
            $cc++;
        }

    }

?>

<div id="test_test">
<font size="1">
<table border="1">
<?php
    echo "<tr>";
    echo "<td>要素名</td>";
    $ff = 0;
    foreach($group_ID[0] as $tes){
        echo "<td>".$tes."</td>";
        $ff++;
    }
    echo "</tr>";
    echo "<tr>";
    echo "<td>パラメータ1</td>";
    foreach($value_x[0] as $tes){
        echo "<td>".$tes."</td>";
    }
    echo "</tr>";
    echo "<tr>";
    echo "<td>パラメータ2</td>";
    foreach($value_y[0] as $tes){
        echo "<td>".$tes."</td>";
    }
    echo "</tr>";
?>
</table>
</font>
</div>

<?php
//$_SESSION["value_x"]="";
//$_SESSION["value_y"]="";
?>

<form action = "correl.php" method="post" target="_blank">
<input type="hidden" name="term_r" value="<?php echo $term_r;?>"> 
<input type="hidden" name="student_count" value="<?php echo $stu_count;?>">
<input type="hidden" name="question_count" value="<?php echo $question_count;?>">
<input type="hidden" name="data_count" value="<?php echo $data_count;?>">   
<input type="hidden" name="correl" value="<?php echo $correl;?>">
<input type="submit" name="Submit" value="相関分析">
</form>
</div>
</body>

</html>