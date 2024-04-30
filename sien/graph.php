<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    ここはグラフ描画用のページです．
    <canvas id = "barChart"></canvas>

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