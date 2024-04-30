<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
$_POST["correl"] =$_REQUEST["correl"];
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>クラスタリング選択画面</title>
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

    ?>
    <font size="6">
    <?php
        if($_POST["correl"] =="student"){
    ?>
            クラスタリング(生徒ごと)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            標本数:<?php echo $student_count;?>
    <?php
        }else if($_POST["correl"] =="ques"){
    ?>
            クラスタリング(問題ごと)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            標本数:<?php echo $question_count;?>
    <?php
        }else{
    ?>
            クラスタリング(履歴データごと)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            標本数:<?php echo $data_count;?>
    <?php
        }
    ?>
    <br>
    </font>
    <br>
    <form action = "cluster_result.php" method="post" target="_blank">
    <?php
        if($_POST["correl"] =="student"){
            echo "パラメータ選択（２つまで）<br>";
    ?>
            <p style="width:15%; margin-left:auto;margin-right:auto;text-align:left;">
            <input type="checkbox" name="parameter[]" value="point">平均得点<br>
            <input type="checkbox" name="parameter[]" value="Time">平均解答時間<br>
            <input type="checkbox" name="parameter[]" value="DragDropCount">DD回数<br>
            <input type="checkbox" name="parameter[]" value="UTurnCount">Uターン回数<br>
            </p>
    <?php
        }else if($_POST["correl"] =="ques"){
            echo "パラメータ選択（２つまで）<br>";
    ?>
            <p style="width:15%; margin-left:auto;margin-right:auto;text-align:left;">
            <input type="checkbox" name="parameter[]" value="point">平均得点<br>
            <input type="checkbox" name="parameter[]" value="Time">平均解答時間<br>
            <input type="checkbox" name="parameter[]" value="DragDropCount">DD回数<br>
            <input type="checkbox" name="parameter[]" value="UTurnCount">Uターン回数<br>
            <input type="checkbox" name="parameter[]" value="wordnum">単語数<br>
            </p>
    <?php
        }
        $correl = $_POST["correl"];
    ?>
    <input type="hidden" name="term_r" value="<?php echo $term_r;?>">
    <input type="hidden" name="student_count" value="<?php echo $student_count;?>">
    <input type="hidden" name="question_count" value="<?php echo $question_count;?>">
    <input type="hidden" name="data_count" value="<?php echo $data_count;?>">
    <input type="hidden" name="correl" value="<?php echo $correl;?>">
    <br>
    <b>クラスタ数</b><input type="text" size="3" name="cluster_num" >
    <input type="submit" value="決定" />
</div>
<div class="dataset">
    <?php
        require "dbc.php";
        if($_POST["correl"] == "student"){
            $sql = "select a.UID,AVG(a.point),AVG(b.TF),AVG(b.Time),AVG(a.Distance),AVG(a.Avespeed),AVG(a.MaxSpeed),AVG(a.MinSpeed),AVG(a.StartTime),
                AVG(a.DStartTime),AVG(a.MaxStopTime),AVG(a.MinStopTime),AVG(a.MaxDragDropTime),AVG(a.MinDragDropTime),AVG(a.MaxDropDragTime),
                AVG(a.MinDropDragTime),AVG(a.DragDropCount),AVG(a.UTurnCount_X),AVG(a.UTurnCount_Y),AVG(a.UTurnCount_XinDD),AVG(a.UTurnCount_YinDD),AVG(a.DD_AA_Count) from trackdata as a, linedata as b
                where a.UID = b.UID and a.WID = b.WID ".$term_r. "group by a.UID 
                order by a.UID";
            echo $sql."<br>";
            $res =  mysqli_query($conn,$sql) or die("接続エラー1");
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
            echo "<td>DD回数</td>";
            echo "<td>Uターン回数(X軸)</td>";
            echo "<td>Uターン回数(Y軸)</td>";
            echo "<td>A→A回数</td>";
            echo "</tr>";

            $count = 0;
            $sum_point_hokan = 0;
            while ($row = mysqli_fetch_array($res)){
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
                echo "<td>".$row["AVG(a.DragDropCount)"]."</td>";
                echo "<td>".($row["AVG(a.UTurnCount_X)"]+$row["AVG(a.UTurnCount_XinDD)"])."</td>";
                echo "<td>".($row["AVG(a.UTurnCount_Y)"]+$row["AVG(a.UTurnCount_YinDD)"])."</td>";
                echo "<td>".$row["AVG(a.DD_AA_Count)"]."</td>";          
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
                $correl_array[15][$count] = $row["AVG(a.DragDropCount)"]."</td>";
                $correl_array[16][$count] = $row["AVG(a.UTurnCount_X)"]+$row["AVG(a.UTurnCount_XinDD)"]."</td>";
                $correl_array[17][$count] = $row["AVG(a.UTurnCount_Y)"]+$row["AVG(a.UTurnCount_YinDD)"]."</td>";
                $correl_array[18][$count] = $row["AVG(a.DD_AA_Count)"]."</td>";
                $count++;
            }
            echo "</table>";
            $column = array("平均得点","正解率","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
                ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
                ,"DD回数","Uターン回数(X軸)","Uターン回数(Y軸)","A→A回数");//配列定義用
        }else if($_POST["correl"] =="ques"){//問題用
            $sql = "select a.WID,AVG(a.point),AVG(b.TF),AVG(b.Time),AVG(a.Distance),AVG(a.Avespeed),AVG(a.MaxSpeed),AVG(a.MinSpeed),AVG(a.StartTime),
                AVG(a.DStartTime),AVG(a.MaxStopTime),AVG(a.MinStopTime),AVG(a.MaxDragDropTime),AVG(a.MinDragDropTime),AVG(a.MaxDropDragTime),
                AVG(a.MinDropDragTime),AVG(a.DragDropCount),AVG(a.GroupCount),AVG(a.UTurnCount_X),AVG(a.UTurnCount_Y),AVG(a.UTurnCount_XinDD),AVG(a.UTurnCount_YinDD),AVG(a.DD_AA_Count) from trackdata as a, linedata as b
                where a.UID = b.UID and a.WID = b.WID ".$term_r. "group by a.WID 
                order by a.WID";
            $res =  mysqli_query($conn,$sql) or die("接続エラー2");

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
            echo "<td>DD回数</td>";
            echo "<td>Uターン回数(X軸)</td>";
            echo "<td>Uターン回数(Y軸)</td>";
            echo "<td>A→A回数</td>";
            echo "<td>単語数</td>";
            echo "</tr>";
            $count = 0;
            $sum_point_hokan = 0;
            while ($row = mysqli_fetch_array($res)){
                //単語数取得用のsql文
                $sql2 ="select * from question_info where WID = ".$row["WID"];
                $res2 =  mysqli_query($conn,$sql2) or die("接続エラー2");
                $row2 = mysqli_fetch_array($res2);

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
                echo "<td>".$row["AVG(a.DragDropCount)"]."</td>";
                echo "<td>".($row["AVG(a.UTurnCount_X)"]+$row["AVG(a.UTurnCount_XinDD)"])."</td>";
                echo "<td>".($row["AVG(a.UTurnCount_Y)"]+$row["AVG(a.UTurnCount_YinDD)"])."</td>";
                echo "<td>".$row["AVG(a.DD_AA_Count)"]."</td>";
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
                $correl_array[15][$count] = $row["AVG(a.DragDropCount)"]."</td>";
                $correl_array[16][$count] = $row["AVG(a.UTurnCount_X)"]+$row["AVG(a.UTurnCount_XinDD)"]."</td>";
                $correl_array[17][$count] = $row["AVG(a.UTurnCount_Y)"]+$row["AVG(a.UTurnCount_YinDD)"]."</td>";
                $correl_array[18][$count] = $row["AVG(a.DD_AA_Count)"]."</td>";
                $correl_array[19][$count] = $row2["wordnum"]."</td>";
                $count++;
            }
            echo "</table>";
            $column = array("得点","正解率","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
                ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
                ,"DD回数","Uターン回数(X軸)","Uターン回数(Y軸)","A→A回数","単語数");//配列定義用
        }else{
            $sql = "select a.UID,a.WID,a.point,b.Time,a.Distance,a.Avespeed,a.MaxSpeed,a.MinSpeed,a.StartTime,
                a.DStartTime,a.MaxStopTime,a.MinStopTime,a.MaxDragDropTime,a.MinDragDropTime,a.MaxDropDragTime,
                a.MinDropDragTime,a.DragDropCount,a.UTurnCount_X,a.UTurnCount_Y,a.UTurnCount_XinDD,a.UTurnCount_YinDD,a.DD_AA_Count from trackdata as a , linedata as b
                where a.UID = b.UID and a.WID = b.WID ".$term_r.
                " order by a.UID,a.WID";
            $res =  mysqli_query($conn,$sql) or die("接続エラー3");

            echo "<table border=\"1\">";
            echo "<tr>";
            echo "<td>UID</td>";
            echo "<td>WID</td>";
            echo "<td>得点</td>";
            echo "<td>正誤</td>";
            echo "<td>解答時間</td>";
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
            echo "<td>DD回数</td>";
            echo "<td>Uターン回数(X軸)</td>";
            echo "<td>Uターン回数(Y軸)</td>";
            echo "<td>A→A回数</td>";
            echo "</tr>";

            $count = 0;
            $sum_point_hokan = 0;
            while ($row = mysqli_fetch_array($res)){
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
                echo "<td>".$row["DragDropCount"]."</td>";
                echo "<td>".($row["UTurnCount_X"]+$row["UTurnCount_XinDD"])."</td>";
                echo "<td>".($row["UTurnCount_Y"]+$row["UTurnCount_YinDD"])."</td>";
                echo "<td>".$row["DD_AA_Count"]."</td>";
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
                $correl_array[15][$count] = $row["DragDropCount"]."</td>";
                $correl_array[16][$count] = $row["UTurnCount_X"]+$row["UTurnCount_XinDD"]."</td>";
                $correl_array[17][$count] = $row["UTurnCount_Y"]+$row["UTurnCount_YinDD"]."</td>";
                $correl_array[18][$count] = $row["DD_AA_Count"]."</td>";
                $count++;
            }
            echo "</table>";
            $column = array("得点","正誤","解答時間","総移動距離","平均速度","最大速度","最小速度","初動時間","Drag開始時間"
                ,"最大静止時間","最小静止時間","最大Drag⇒Drop時間","最小Drag⇒Drop時間","最大Drop⇒Drag時間","最小Drop⇒Drag時間"
                ,"DD回数","Uターン回数(X軸)","Uターン回数(Y軸)","A→A回数");//配列定義用
        }
    ?>
</div>
</body>
</html>