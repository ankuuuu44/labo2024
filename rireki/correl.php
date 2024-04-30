<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
?>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>相関分析</title>
    <STYLE type="text/css">
        <!--
        .scr {
          overflow: scroll;   /* スクロール表示 */
          width: 400px;
          height: 500px;
          background-color: white;
          position: relative;
          top: 10px;
          left: 50px;
        }
        .chart {
          overflow: hidden;   /* スクロール表示 */
          width: 600px;
          height: 600px;
          background-color: white;
          position: absolute;
          top: 70px;
          left: 530px;
        }
        .dataset {
          overflow: scroll;   /* スクロール表示 */
          width: 1000px;
          height: 300px;
          background-color: white;
          position: absolute;
          top: 600px;
          left: 50px;
        }
        -->
    </STYLE>
    <script language="javascript" type="text/javascript" src="jquery-2.0.3.min.js"></script>
    <script language="javascript" type="text/javascript" src="jquery.jqplot.min.js"></script>
    <script language="javascript" type="text/javascript" src="plugins/jqplot.pointLabels.min.js"></script>
    <link rel="stylesheet" type="text/css" href="jquery.jqplot.min.css" />
    <script language="JavaScript">
	    function sentaku(pos){
		    switch(pos)
		    {
		    case	1:
			    document.getElementById("1").style.backgroundColor="yellow";
			    document.getElementById("2").style.backgroundColor="white";
			    break;
		    case	2:
			    document.getElementById("2").style.backgroundColor="yellow";
			    document.getElementById("1").style.backgroundColor="white";
			    break;
		    }
	    }

    jQuery(function () {
        jQuery.jqplot(
            'jqPlot-sample',
            [
                [[7, 36.345], [5, 58.933], [5.8333, 56.364], [5.333, 72.055], [6.667, 40.344], [7, 42.469],
                  [5.167, 57.891], [8.5, 33.74], [6.667, 36.153], [4.833, 50.081], [6.167, 46.574], [6.8, 65.181],
                  [6, 61.725], [4.667, 62.449], [4.167, 89.046], [5.333, 71.394], [5.333, 46.51], [6.667, 42.323],
                  [5.333, 57.725], [4.8333, 60.031], [4.8333, 43.454], [6.333, 77.535], [6.379, 50.498], [7.667, 55.128],
                  [5.5, 49.781], [4.5, 99.62], [6.5, 41.208], [4.667, 56.814], [7.667, 49.297], [5.5, 52.131],
                  [7.5, 60.988], [4.167, 71.582], [5, 46.108], [6.333, 43.815], [6, 67.174], [6.167, 40.779],
                  [6, 34.059], [5, 47.876], [7.5, 51.527], [4.167, 53.663]
                 ]
            ],
            {

                grid: {
                drawBorder: true,
                shadow: false,
                background: "#ffffff",
            },

                axes: {
                    xaxis: {
                        label: '平均得点（点）',
                        labelOptions: {
                        
                        fontSize: '14pt',
                                          
                    }
                    },
                    yaxis: 
                    {
                    
                        label: '平均解答時間（秒）',
                        labelOptions: {
                        
                        fontSize: '14pt',
                        
                    }

                    }
                },

                seriesDefaults: 
                {
                    color: '#ff0000',
                    showLine: false,
                    markerOptions: 
                    {
                        size: 13
                    },
    
                }
                }
        );
     });
    </script>
</head>
<body>
<?php
   $student_count = $_POST["student_count"];
   $question_count = $_POST["question_count"];
   $data_count = $_POST["data_count"];
   $termr1 =  $_POST["term_r"];
   $termr2 = str_replace("linedata","b",$termr1);
   $termr3 = str_replace("trackdata","a",$termr2);
   $term_r = str_replace("AnswerQues","c",$termr3);
?>
<div align ="center">
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
</div>
<div class= "chart">
    <div id="jqPlot-sample" style="height: 520px; width: 520px;"></div>    
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
            echo "<td>DD回数</td>";
            echo "<td>Uターン回数(X軸)</td>";
            echo "<td>Uターン回数(Y軸)</td>";
            echo "<td>A→A回数</td>";
            echo "</tr>";

            $count = 0;
            $sum_point_hokan = 0;
            while ($row = mysql_fetch_array($res)){
                $point = number_format($row["AVG(a.point)"],3);
                $TF = number_format($row["AVG(b.TF)"]*100,3);
                $Time = number_format($row["AVG(b.Time)"]/1000,3);
                $Distance = number_format($row["AVG(a.Distance)"],3);
                $AveSpeed = number_format($row["AVG(a.Avespeed)"]*1000,3);
                $MaxSpeed = number_format($row["AVG(a.MaxSpeed)"]*1000,3);
                $MinSpeed = number_format($row["AVG(a.MinSpeed)"]*1000,3);
                $StartTime = number_format($row["AVG(a.StartTime)"]/1000,3);
                $DStartTime = number_format($row["AVG(a.DStartTime)"]/1000,3);
                $MaxStopTime = number_format($row["AVG(a.MaxStopTime)"]/1000,3);
                $MinStopTime = number_format($row["AVG(a.MinStopTime)"]/1000,3);
                $MaxDragDropTime = number_format($row["AVG(a.MaxDragDropTime)"]/1000,3);
                $MinDragDropTime = number_format($row["AVG(a.MinDragDropTime)"]/1000,3);
                $MaxDropDragTime = number_format($row["AVG(a.MaxDropDragTime)"]/1000,3);
                $MinDropDragTime = number_format($row["AVG(a.MinDropDragTime)"]/1000,3);
                $DragDropCount = number_format($row["AVG(a.DragDropCount)"],3);
                $UTurnCountX = number_format($row["AVG(a.UTurnCount_X)"]+$row["AVG(a.UTurnCount_XinDD)"],3);
                $UTurnCountY = number_format($row["AVG(a.UTurnCount_Y)"]+$row["AVG(a.UTurnCount_YinDD)"],3);
                $DD_AA_Count = number_format($row["AVG(a.DD_AA_Count)"],3);

                echo "<tr>";
                echo "<td>".$row["UID"]."</td>";
                echo "<td>".$point."</td>";
                echo "<td>".$TF."</td>";
                echo "<td>".$Time."</td>";
                echo "<td>".$Distance."</td>";
                echo "<td>".$AveSpeed."</td>";
                echo "<td>".$MaxSpeed."</td>";
                echo "<td>".$MinSpeed."</td>";
                echo "<td>".$StartTime."</td>";
                echo "<td>".$DStartTime."</td>";
                echo "<td>".$MaxStopTime."</td>";
                echo "<td>".$MinStopTime."</td>";
                echo "<td>".$MaxDragDropTime."</td>";
                echo "<td>".$MinDragDropTime."</td>";
                echo "<td>".$MaxDropDragTime."</td>";
                echo "<td>".$MinDropDragTime."</td>";
                echo "<td>".$DragDropCount."</td>";
                echo "<td>".$UTurnCountX."</td>";
                echo "<td>".$UTurnCountY."</td>";
                echo "<td>".$DD_AA_Count."</td>";
                echo "</tr>";

                $correl_array[0][$count] = $point."</td>";
                $correl_array[1][$count] = $TF."</td>";
                $correl_array[2][$count] = $Time."</td>";
                $correl_array[3][$count] = $Distance."</td>";
                $correl_array[4][$count] = $AveSpeed."</td>";
                $correl_array[5][$count] = $MaxSpeed."</td>";
                $correl_array[6][$count] = $MinSpeed."</td>";
                $correl_array[7][$count] = $StartTime."</td>";
                $correl_array[8][$count] = $DStartTime."</td>";
                $correl_array[9][$count] = $MaxStopTime."</td>";
                $correl_array[10][$count] = $MinStopTime."</td>";
                $correl_array[11][$count] = $MaxDragDropTime."</td>";
                $correl_array[12][$count] = $MinDragDropTime."</td>";
                $correl_array[13][$count] = $MaxDropDragTime."</td>";
                $correl_array[14][$count] = $MinDropDragTime."</td>";
                $correl_array[15][$count] = $DragDropCount."</td>";
                $correl_array[16][$count] = $UTurnCountX."</td>";
                $correl_array[17][$count] = $UTurnCountY."</td>";
                $correl_array[18][$count] = $DD_AA_Count."</td>";
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
            echo "<td>DD回数</td>";
            echo "<td>Uターン回数(X軸)</td>";
            echo "<td>Uターン回数(Y軸)</td>";
            echo "<td>A→A回数</td>";
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
            $res =  mysql_query($sql,$conn) or die("接続エラー");

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
            
                $Time = round($row["Time"],3);
                $Distance = round($row["Distance"],3);
                $AveSpeed = round($row["Avespeed"],3);
                $MaxSpeed = round($row["MaxSpeed"],3);
                $MinSpeed = round($row["MinSpeed"],3);
                $StartTime = round($row["StartTime"],3);
                $DStartTime = round($row["DStartTime"],3);
                $MaxStopTime = round($row["MaxStopTime"],3);
                $MinStopTime = round($row["MinStopTime"],3);
                $MaxDragDropTime = round($row["MaxDragDropTime"],3);
                $MinDragDropTime = round($row["MinDragDropTime"],3);
                $MaxDropDragTime = round($row["MaxDropDragTime"],3);
                $MinDropDragTime = round($row["MinDropDragTime"],3);
                $DragDropCount = round($row["DragDropCount"],3);
                $UTurnCountX =round($row["UTurnCount_X"]+$row["UTurnCount_XinDD"],3);
                $UTurnCountY =round($row["UTurnCount_Y"]+$row["UTurnCount_YinDD"],3);
                $DD_AA_Count = round($row["DD_AA_Count"],3);
            
                echo "<td>".$Time."</td>";
                echo "<td>".$Distance."</td>";
                echo "<td>".$AveSpeed."</td>";
                echo "<td>".$MaxSpeed."</td>";
                echo "<td>".$MinSpeed."</td>";
                echo "<td>".$StartTime."</td>";
                echo "<td>".$DStartTime."</td>";
                echo "<td>".$MaxStopTime."</td>";
                echo "<td>".$MinStopTime."</td>";
                echo "<td>".$MaxDragDropTime."</td>";
                echo "<td>".$MinDragDropTime."</td>";
                echo "<td>".$MaxDropDragTime."</td>";
                echo "<td>".$MinDropDragTime."</td>";
                echo "<td>".$DragDropCount."</td>";
                echo "<td>".$UTurnCountX."</td>";
                echo "<td>".$UTurnCountY."</td>";
                echo "<td>".$DD_AA_Count."</td>";
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
<br><br>
<DIV class="scr">
    <div align ="left">
    <table border="1">
    <?php
        echo "<tr>";
        echo "<td>相関パラメータ</td>";
        echo "<td>相関係数</td>";
        echo"</tr>";

       if($_POST["correl"] == "ques"){
           $correl_num = 19;
       }else{
           $correl_num = 18;
       }
       for($i = 0; $i <= 1; $i++){
           for($j = 0 ; $j <= $correl_num; $j++){
               if( $i != $j){
                $correl_value = cc($correl_array[$i],$correl_array[$j]);
                $correl_value = round($correl_value,3);
                echo "<tr id='".$j."' onclick='sentaku(".$j.")'>";
                echo "<td>".$column[$i]." - ".$column[$j]."</td>";
                echo "<td>".$correl_value."</td>";
                echo "</tr>";
               }
            }
        } 
?>
   </table>
   </div>
</div>
</body>
</html>