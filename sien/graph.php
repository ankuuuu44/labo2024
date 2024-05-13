<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style type="text/css">
        body{
          box-sizing: border-box;
          display: inline-block;
        }
        body *{
          box-sizing: inherit; /* box-sizingの値は継承されないので明示的に設定 */
        }
        .cons-table{
            width: 50%;
            text-align: center;
            border-collapse: collapse;
            border-spacing: 0;
        }
        .cons-table th{
            padding: 10px;
            background: #e9e9e9;
            border: solid 1px #778ca3;
        }
        .cons-table td{
            padding: 10px;
            border: solid 1px #778ca3;
        }
        #barChart{
          width: 50%;
        }
        
    </style>
</head>
<body>
    ここはグラフ描画用のページです．
    <canvas id = "barChart"></canvas>

    <table class = "cons-table" border="1">
        <tr>
            <th>UID</th>
            <th>合計時間</th>
            <th>解答問題数</th>
            <th>平均時間</th>
        </tr>


    <?php
        session_start();
        $databasearray1 = $_SESSION['databasearray1'];
        //print_r($databasearray1);

        //合計を求めるプログラム
        $totals = array();
        foreach($databasearray1 as $data){
            $user = $data['UID'];
            if(!isset($totals[$user])){
                $totals[$user] = [
                    'total_time' => 0,
                    'row_count' => 0
                ];
            }
            $totals[$user]['total_time'] += $data['Time'];
            $totals[$user]['row_count'] += 1;
        }

        foreach($totals as $user => $info){
            $info['average_time'] = $info['total_time'] / $info['row_count'];
            //echo "UID: $user ,合計時間: {$info['total_time']} ,回数: {$info['row_count']}, 平均時間: {$info['average_time']} <br>";
            echo "<tr><td>{$user}</td><td>{$info['total_time']}</td><td>{$info['row_count']}</td><td>{$info['average_time']}</td></tr>";
        }
    ?>
    </table>

    <?php
        session_start();
    ?>

    <script>
      var lineChartlabels = [];
      var lineChartdata = [];
      //合計と平均の計算
      var data_array = <?php echo json_encode($_SESSION['databasearray1']);?>;
      


      var totals = {};
      var n = 0;

      data_array.forEach(function(data){
          var user = data["UID"];
          var timeoriginal = data["Time"];

          var time = parseInt(timeoriginal,10);
          if(!totals[user]){
              totals[user] = {
                  total_time : 0,
                  row_count : 0
              };
          }
          totals[user]['total_time'] += time;
          totals[user]['row_count'] += 1;

      })

      Object.keys(totals).forEach(function(user) {
        totals[user]['average_time'] = totals[user]['total_time'] / totals[user]['row_count'];
        lineChartlabels.push(user);
        lineChartdata.push(totals[user]['average_time']);
        console.log("UID:" + user + " ,合計時間:" + totals[user]['total_time'] + " ,回数:" + totals[user]['row_count'] + " ,平均時間:" + totals[user]['average_time']);
      })
      //console.log(linelabels);

    let barCharttag = document.getElementById("barChart");
    
    

      let lineConfig = new Chart (barCharttag,{
        type: "bar",  //typeでグラフの種類指定
        data:{
          labels: lineChartlabels,
          datasets:[
            {
              label: "時間",
              data: lineChartdata
            }
          ]
        }
      })
      


    </script>
</body>
</html>