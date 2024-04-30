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
	<title>相関分析</title>
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
   $student_count = $_POST["student_count"];
   $question_count = $_POST["question_count"];
   $data_count = $_POST["data_count"];
   $termr1 =  $_POST["term_r"];
   $termr2 = str_replace("linedata","b",$termr1);
   $termr3 = str_replace("trackdata","a",$termr2);
   $term_r = str_replace("AnswerQues","c",$termr3);
   echo $term_r;
   //echo $term_s."<br>";
   //echo $term_q."<br>";
   //echo "ddd:".$term_r."<br>";
?>
<font size="6">
<?php
    if($_POST["correl"] =="student"){
?>
相関分析(生徒ごと)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
標本数:<?php echo $student_count;?>
<?php
    }else if($_POST["correl"] =="ques"){
?>
相関分析(問題ごと)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
標本数:<?php echo $question_count;?>
<?php
    }else{
?>
相関分析(履歴データごと)
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
標本数:<?php echo $data_count;?>
<?php
    }
?>

</font>
<div class="dataset">
<?php
 //echo $_SESSION["correl_mode"];
        require "dbc.php";
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


        $count = 0;
        $sum_point_hokan = 0;
        while ($row = mysql_fetch_array($res)){
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
        echo "</table>";

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
        $count = 0;
        $sum_point_hokan = 0;
        while ($row = mysql_fetch_array($res)){
            //単語数取得用のsql文
            $sql2 ="select * from question_info where WID = ".$row["WID"];
            $res2 =  mysql_query($sql2,$conn) or die("接続エラー2");
            $row2 = mysql_fetch_array($res2);

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
        echo "</table>";

        $column = array("得点","正解率","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
        ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
        ,"区切り追加回数","DD回数","Uターン回数","単語数");//配列定義用
}else{
        $sql = "select a.UID,a.WID,a.point,b.Time,a.Distance,a.Avespeed,a.MaxSpeed,a.MinSpeed,a.StartTime,
        a.DStartTime,a.MaxStopTime,a.MinStopTime,a.MaxDragDropTime,a.MinDragDropTime,a.MaxDropDragTime,
        a.MinDropDragTime,a.GroupCount,a.DragDropCount,a.UTurnCount from trackdata as a , linedata as b, AnswerQues as c  
        where a.UID = b.UID and a.WID = b.WID and a.UID = c.UID and b.UID = c.UID and b.AID = c.AID ".$term_r.
        " order by a.UID,a.WID";
        //echo $sql;
        /*
        $sql = "select a.UID,a.AVG(point),a.AVG(StartTime),a.AVG(DragDropCount),a.AVG(UTurnCount) from trackdata as a
          group by a.UID";
          */
        //$sql =  "select UID,AVG(point),AVG(DragDropCount),AVG(UTurnCount) from trackdata group by UID";
       // echo $sql;
        $res =  mysql_query($sql,$conn) or die("接続エラー");

        echo "<table border=\"1\">";
        echo "<tr>";
        echo "<td>UID</td>";
        echo "<td>WID</td>";
        echo "<td>得点</td>";
        echo "<td>正誤</td>";
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


        $count = 0;
        $sum_point_hokan = 0;
        while ($row = mysql_fetch_array($res)){

            echo "<tr>";
            echo "<td>".$row["UID"]."</td>";
            echo "<td>".$row["WID"]."</td>";
            echo "<td>".$row["point"]."</td>";
            if($row["point"] == 10){
                echo "<td>○</td>";
            }else{
                echo "<td>×</td>";
            }
            echo "<td>".$row["Time"]."</td>";
            echo "<td>".$row["Distance"]."</td>";
            echo "<td>".$row["Avespeed"]."</td>";
            echo "<td>".$row["MaxSpeed"]."</td>";
            echo "<td>".$row["MinSpeed"]."</td>";
            echo "<td>".$row["StartTime"]."</td>";
            echo "<td>".$row["DStartTime"]."</td>";
            echo "<td>".$row["MaxStopTime"]."</td>";
            echo "<td>".$row["MinStopTime"]."</td>";
            echo "<td>".$row["MaxDragDropTime"]."</td>";
            echo "<td>".$row["MinDragDropTime"]."</td>";
            echo "<td>".$row["MaxDropDragTime"]."</td>";
            echo "<td>".$row["MinDropDragTime"]."</td>";
            echo "<td>".$row["GroupCount"]."</td>";
            echo "<td>".$row["DragDropCount"]."</td>";
            echo "<td>".$row["UTurnCount"]."</td>";
            echo "</tr>";


            $correl_array[0][$count] = $row["point"]."</td>";
            if($row["point"] == "10"){
                $correl_array[1][$count] = "10</td>";
            }else{
                $correl_array[1][$count] = "0</td>";
            }
            $correl_array[2][$count] = $row["Time"]."</td>";
            $correl_array[3][$count] = $row["Distance"]."</td>";
            $correl_array[4][$count] = $row["Avespeed"]."</td>";
            $correl_array[5][$count] = $row["MaxSpeed"]."</td>";
            $correl_array[6][$count] = $row["MinSpeed"]."</td>";
            $correl_array[7][$count] = $row["StartTime"]."</td>";
            $correl_array[8][$count] = $row["DStartTime"]."</td>";
            $correl_array[9][$count] = $row["MaxStopTime"]."</td>";
            $correl_array[10][$count] = $row["MinStopTime"]."</td>";
            $correl_array[11][$count] = $row["MaxDragDropTime"]."</td>";
            $correl_array[12][$count] = $row["MinDragDropTime"]."</td>";
            $correl_array[13][$count] = $row["MaxDropDragTime"]."</td>";
            $correl_array[14][$count] = $row["MinDropDragTime"]."</td>";
            $correl_array[15][$count] = $row["GroupCount"]."</td>";
            $correl_array[16][$count] = $row["DragDropCount"]."</td>";
            $correl_array[17][$count] = $row["UTurnCount"]."</td>";
            $count++;
        }
        echo "</table>";

        $column = array("得点","正誤","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
        ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
        ,"区切り追加回数","DD回数","Uターン回数");//配列定義用
}
?>
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
	

    //    echo "テスト".$point."<br>";
     //   echo "テスト".$UTurn."<br>";
       // $test33 = cc($point,$UTurn);
       // $test11 = cc($point,$DD);
       // $test15 = cc($point,$StartTime);
       ?>

<br><br>
      <div align ="left">
          <DIV class="scr">
  <?php
   echo "<table border=\"1\">";
   echo "<tr>";
   echo "<td>相関パラメータ</td>";
   echo "<td>相関係数</td>";
   echo"</tr>";

   if($_POST["correl"] == "ques"){
       $correl_num = 18;
   }else{
       $correl_num = 17;
   }
   /*
       for($i = 0; $i <= $correl_num -1; $i++){
           for($j = $i+1 ; $j <= $correl_num; $j++){
                $correl_value = cc($correl_array[$i],$correl_array[$j]);
                $correl_value = round($correl_value,3);
                echo "<tr>";
                echo "<td>".$column[$i]." - ".$column[$j]."</td>";
                echo "<td>".$correl_value."</td>";
                echo "</tr>";
                //echo $column[$i]." - ".$column[$j]." : ".$correl_value."<br>";
            }
        }
        */
       for($i = 0; $i <= $correl_num; $i++){
           for($j = 0 ; $j <= $correl_num; $j++){
               if( $i != $j){
                $correl_value = cc($correl_array[$i],$correl_array[$j]);
                $correl_value = round($correl_value,3);
                echo "<tr>";
                echo "<td>".$column[$i]." - ".$column[$j]."</td>";
                echo "<td>".$correl_value."</td>";
                echo "</tr>";
               }
                //echo $column[$i]." - ".$column[$j]." : ".$correl_value."<br>";
            }
        }
        echo "</table>";
?>
   </div>
</div>

   </div>
</body>
</html>