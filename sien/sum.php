<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    ここは結果確認用のページです．

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
            echo "UID: $user ,合計時間: {$info['total_time']} ,回数: {$info['row_count']}, 平均時間: {$info['average_time']} <br>";
        }

    ?>


    <script>
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
            console.log("UID:" + user + " ,合計時間:" + totals[user]['total_time'] + " ,回数:" + totals[user]['row_count'] + " ,平均時間:" + totals[user]['average_time'] + " <br>");
        });
    </script>

</body>
</html>