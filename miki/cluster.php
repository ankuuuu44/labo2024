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
$_POST["correl"] ="student";
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>クラスタリング</title>
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
}

$mode = 0;//どのパラメータでクラスタリングを行うか。
for($i =0; $i < $student_count; $i++){

}
for ($i = 0 ; $i < 10 ; $i++){
    print(mt_rand(1, 6).'<br>');
}
?>
</div>
<br><br>
</div>
</body>
</html>