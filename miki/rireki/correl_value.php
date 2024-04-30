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

if($_POST["mark"] != $_SESSION["mark"]){//正誤表示、部分点表示の場合分け処理
    if(isset($_POST["mark"])){
        $_SESSION["mark"] = $_POST["mark"];
    }
}

?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>相関分析</title>
</head>


<body>
  <div align ="center">
    <font size="6">
    データ一覧
    </font>
    <?php
        require "dbc.php";
       
        $sql = "select UID,AVG(point),AVG(Distance),AVG(Avespeed),AVG(MaxSpeed),AVG(MinSpeed),AVG(StartTime),
        AVG(DStartTime),AVG(MaxStopTime),AVG(MinStopTime),AVG(MaxDragDropTime),AVG(MinDragDropTime),AVG(MaxDropDragTime),
        AVG(MinDropDragTime),AVG(GroupCount),AVG(DragDropCount),AVG(UTurnCount) from trackdata where UID>5000 group by UID ";
        
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
        echo "<td>得点</td>";
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
            echo "<td>".$row["AVG(point)"]."</td>";
            echo "<td></td>";
            echo "<td></td>";
            echo "<td>".$row["AVG(Distance)"]."</td>";
            echo "<td>".$row["AVG(Avespeed)"]."</td>";
            echo "<td>".$row["AVG(MaxSpeed)"]."</td>";
            echo "<td>".$row["AVG(MinSpeed)"]."</td>";
            echo "<td>".$row["AVG(StartTime)"]."</td>";
            echo "<td>".$row["AVG(DStartTime)"]."</td>";
            echo "<td>".$row["AVG(MaxStopTime)"]."</td>";
            echo "<td>".$row["AVG(MinStopTime)"]."</td>";
            echo "<td>".$row["AVG(MaxDragDropTime)"]."</td>";
            echo "<td>".$row["AVG(MinDragDropTime)"]."</td>";
            echo "<td>".$row["AVG(MaxDropDragTime)"]."</td>";
            echo "<td>".$row["AVG(MinDropDragTime)"]."</td>";
            echo "<td>".$row["AVG(GroupCount)"]."</td>";
            echo "<td>".$row["AVG(DragDropCount)"]."</td>";
            echo "<td>".$row["AVG(UTurnCount)"]."</td>";
            echo "</tr>";
            //$sum_point = $sum_point + $row["AVG(point)"];
            //$sum_UTurn = $sum_UTurn + $row["AVG(UTurnCount)"];
            /*
            $point[$count] = $row["AVG(point)"];
            $UTurn[$count] = $row["AVG(UTurnCount)"];
            $DD[$count] = $row["AVG(DragDropCount)"];
            $StartTime[$count] = $row["AVG(StartTime)"];
            */
            $correl_array[0][$count] = $row["AVG(point)"]."</td>";
            $correl_array[1][$count] = $row["AVG(point)"]."</td>";
            $correl_array[2][$count] = $row["AVG(point)"]."</td>";
            $correl_array[3][$count] = $row["AVG(Distance)"]."</td>";
            $correl_array[4][$count] = $row["AVG(Avespeed)"]."</td>";
            $correl_array[5][$count] = $row["AVG(MaxSpeed)"]."</td>";
            $correl_array[6][$count] = $row["AVG(MinSpeed)"]."</td>";
            $correl_array[7][$count] = $row["AVG(StartTime)"]."</td>";
            $correl_array[8][$count] = $row["AVG(DStartTime)"]."</td>";
            $correl_array[9][$count] = $row["AVG(MaxStopTime)"]."</td>";
            $correl_array[10][$count] = $row["AVG(MinStopTime)"]."</td>";
            $correl_array[11][$count] = $row["AVG(MaxDragDropTime)"]."</td>";
            $correl_array[12][$count] = $row["AVG(MinDragDropTime)"]."</td>";
            $correl_array[13][$count] = $row["AVG(MaxDropDragTime)"]."</td>";
            $correl_array[14][$count] = $row["AVG(MinDropDragTime)"]."</td>";
            $correl_array[15][$count] = $row["AVG(GroupCount)"]."</td>";
            $correl_array[16][$count] = $row["AVG(DragDropCount)"]."</td>";
            $correl_array[17][$count] = $row["AVG(UTurnCount)"]."</td>";
            $count++;
        }
        echo "</table>";
        /*
        $column = array("得点","正解率","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
        ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
        ,"区切り追加回数","DD回数","Uターン回数");//配列定義用
        */

?>
   </div>
</body>
</html>