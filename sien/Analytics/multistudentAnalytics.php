<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教師用ダッシュボード</title>
    <link rel="stylesheet" href="../teachertrue_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script src="../multimove.js"></script>
</head>
<body>
    <?php
        session_start();
        require "../dbc.php";
    ?>
    <header>
        <div class="logo">データ分析ページ</div>
        <nav>
            <ul>
                <li><a href="../teachertrue.php">ホーム</a></li>
                <li><a href="#">学習履歴</a></li>
                <li><a href="machineLearning_sample.php">迷い推定・機械学習</a></li>
                <li><a href="studentAnalytics.php">学生分析</a></li>
                <li><a href="multistudentAnalytics.php">複数学習者分析</a></li>
                <li><a href="questionAnalytics.php">問題分析</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <aside>
            <ul>
                <li><a href="#">ダッシュボード</a></li>
                <li><a href="#">クラス管理</a></li>
                <li><a href="machineLearning_sample.php">迷い推定・機械学習</a></li>
                <li><a href="studentAnalytics.php">学生分析</a></li>
                <li><a href="multistudentAnalytics.php">複数学習者分析</a></li>
                <li><a href="questionAnalytics.php">問題分析</a></li>
            </ul>
        </aside>
        <main>
        <?php
                // フォームからの入力を受け取る
                $UIDrange = isset($_POST['UIDrange']) ? $_POST['UIDrange'] : null;
                $WIDrange = isset($_POST['WIDrange']) ? $_POST['WIDrange'] : null;
                $UIDsearch = isset($_POST['UIDsearch']) ? $_POST['UIDsearch'] : null;
                $WIDsearch = isset($_POST['WIDsearch']) ? $_POST['WIDsearch'] : null;
                $TFsearch = isset($_POST['TFsearch']) ? $_POST['TFsearch'] : null;
                $TimeRange = isset($_POST['TimeRange']) ? $_POST['TimeRange'] : null;
                $Timesearch = isset($_POST['Timesearch']) ? $_POST['Timesearch'] : null;
                $TimesearchMin = isset($_POST['Timesearch-min']) ? $_POST['Timesearch-min'] : null;
                $TimesearchMax = isset($_POST['Timesearch-max']) ? $_POST['Timesearch-max'] : null;


                $sql = "SELECT * FROM linedata";
                // WHERE 句の条件を保持する配列
                $conditions = [];
                // UIDの条件を追加
                if (!empty($UIDsearch)) {
                    if ($UIDrange === 'not') {
                        $conditions[] = "UID NOT IN (" . mysqli_real_escape_string($conn, $UIDsearch) . ")";
                    } else {
                        $conditions[] = "UID IN (" . mysqli_real_escape_string($conn, $UIDsearch) . ")";
                    }
                }

                // WIDの条件を追加
                if (!empty($WIDsearch)) {
                    if ($WIDrange === 'not') {
                        $conditions[] = "linedata.WID NOT IN (" . mysqli_real_escape_string($conn, $WIDsearch) . ")";
                    } else {
                        $conditions[] = "linedata.WID IN (" . mysqli_real_escape_string($conn, $WIDsearch) . ")";
                    }
                }
                // 正誤の条件を追加
                if (!empty($TFsearch)) {
                    $conditions[] = "TF = '" . mysqli_real_escape_string($conn, $TFsearch) . "'";
                }
                // 解答時間の条件を追加
                if (!empty($TimeRange) && !empty($Timesearch)) {
                    switch ($TimeRange) {
                        case 'above':
                            $conditions[] = "Time >= '" . mysqli_real_escape_string($conn, $Timesearch) . "'";
                            break;
                        case 'below':
                            $conditions[] = "Time <= '" . mysqli_real_escape_string($conn, $Timesearch) . "'";
                            break;
                        case 'range':
                            if (!empty($TimesearchMin) && !empty($TimesearchMax)) {
                                $conditions[] = "Time BETWEEN '" . mysqli_real_escape_string($conn, $TimesearchMin) . "' AND '" . mysqli_real_escape_string($conn, $TimesearchMax) . "'";
                            }
                            break;
                    }
                }


                // 条件が一つでもあればWHERE句を追加&SQLと条件をsessionに保存
                if (!empty($conditions)) {
                    $sql .= " WHERE " . join(" AND ", $conditions);
                    $_SESSION['conditions'] = $conditions;
                }
                $_SESSION['sql'] = $sql;
                echo $_SESSION['sql'];
                
                

                // SQL実行  
                $result = mysqli_query($conn, $_SESSION['sql']);

                // 各学習者ごとの正解率を計算
                $user_accuracy = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    if(!array_key_exists($row['UID'], $user_accuracy)){
                        //キーが存在せんかったら新しいの追加
                        $user_accuracy[$row['UID']] = ['correct' => 0, 'total' => 0, 'accuracy' => 0];
                    }
                    //解答問題数を追加
                    $user_accuracy[$row['UID']]['total']++;
                    if($row['TF'] == 1){
                        //正解数を追加
                        $user_accuracy[$row['UID']]['correct']++;
                    }
                }

                // 各学習者ごとの正解率を計算
                foreach ($user_accuracy as $key => $value) {
                    $user_accuracy[$key]['accuracy'] = round($value['correct'] / $value['total']*100,2);
                    //echo "user_accuracy[$key]['accuracy'] =" ,$user_accuracy[$key]['accuracy'];
                }
                // $user_accuracyをJSONにエンコード
                /*
                $json_data = json_encode($user_accuracy, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // JSONデータをファイルに保存
                $file_path = 'json/file.json'; // JSONファイルを保存するパス
                $json_keep_result = file_put_contents($file_path, $json_data);
                // 結果を確認
                if ($json_keep_result === false) {
                    echo "エラーが発生しました。ファイルに書き込めませんでした。";
                } else {
                    echo "ファイルに書き込みが成功しました。";
                }
                $command = "py .\graph_plot.py";
                $output = shell_exec($command);
                */



                //難易度ごとの正解数記録
                $sql_dif_TF = "SELECT UID,linedata.WID,TF,Understand,question_info.level,question_info.grammar,question_info.Sentence FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID";
                $sql_stu_Time = "SELECT UID,AVG(Time) FROM linedata";
                if (!empty($conditions)) {
                    $sql_dif_TF .= " WHERE " . join(" AND ", $conditions);
                    $sql_stu_Time .= " WHERE " . join(" AND ", $conditions);
                }
                $sql_stu_Time .= " GROUP BY UID";
                $result_dif_TF = mysqli_query($conn, $sql_dif_TF);
                $result_stu_Time = mysqli_query($conn, $sql_stu_Time);
                $level_true = [1 => 0, 2 => 0, 3 => 0];
                $level_false = [1 => 0, 2 => 0, 3 => 0];
                $countgrammar = [];
                $grammarCountTrue = [];
                $grammarCountFalse = [];
                $grammarCounthesitateT = [];
                $grammarCounthesitateF = [];
                $ques_TF = [];
                $stu_TF = [];
                
                for ($i = -1; $i <= 22; $i++) {
                    $countgrammar[(string)$i] = 0;
                    $grammarCountTrue[(string)$i] = 0;
                    $grammarCountFalse[(string)$i] = 0;
                    $grammarCounthesitateT[(string)$i] = 0;
                    $grammarCounthesitateF[(string)$i] = 0;
                }
                while ($row = $result_dif_TF->fetch_assoc()) {
                    $grammarItems = array_filter(explode("#", $row["grammar"]), function($value) {
                        return $value !== '';
                    });
                    // デバッグ用メッセージ
                    error_log("Grammar items: " . implode(",", $grammarItems));
                    //WIDごとの正答率をもとめる
                    $WID = $row['WID'];
                    if (!isset($ques_TF[$WID])) {
                        $ques_TF[$WID] = ['correct' => 0, 'incorrect' => 0 ,'Sentence' => $row['Sentence']];
                    }
                    //UIDごとの正答率をもとめる
                    $UID = $row['UID'];
                    if (!isset($stu_TF[$UID])) {
                        $stu_TF[$UID] = ['correct' => 0, 'incorrect' => 0 ,'Time' => 0];
                    }
        
                    foreach ($grammarItems as $value) {
                        $countgrammar[$value]++;
                        if ($row["TF"] == 1) {
                            $grammarCountTrue[$value]++;
                        } else {
                            $grammarCountFalse[$value]++;
                        }
                        if ($row['Understand'] == 2) {
                            $grammarCounthesitateF[$value]++;
                        }else if ($row['Understand'] == 2) {
                            $grammarCounthesitateT[$value]++;
                        }
                    }
                    
                    $level = $row["level"];
                    if ($row["TF"] == 1) {
                        $level_true[$level]++;
                        $ques_TF[$WID]['correct']++;
                        $stu_TF[$UID]['correct']++;
                    } else {
                        $level_false[$level]++;
                        $ques_TF[$WID]['incorrect']++;
                        $stu_TF[$UID]['incorrect']++;
                    }
                    
                }
                while ($row = $result_stu_Time->fetch_assoc()) {
                    $UID = $row['UID'];
                    $stu_TF[$UID]['Time'] = $row['AVG(Time)'];
                }
                //問題ごとの正答率を計算
                foreach ($ques_TF as $WID => $counts) {
                    $total = $counts['correct'] + $counts['incorrect'];
                    $accuracy = $total > 0 ? ($counts['correct'] / $total) * 100 : 0;
                    $ques_TF[$WID]['accuracy'] = round($accuracy, 2); // 正解率を小数点以下2桁に丸める
                }
                //UIDごとの正答率を計算
                foreach ($stu_TF as $UID => $counts) {
                    $total = $counts['correct'] + $counts['incorrect'];
                    $accuracy = $total > 0 ? ($counts['correct'] / $total) * 100 : 0;
                    $stu_TF[$UID]['accuracy'] = round($accuracy, 2); // 正解率を小数点以下2桁に丸める
                }
                
                
                // 正解率の低い順にソート
                //PHP 7 以降の場合は 以下の通り 関数を使用する
                /*
                usort($ques_TF, function($a, $b) {
                    return $a['accuracy'] <=> $b['accuracy'];
                });
                */
                // 正解率の低い順に並べ替えるための配列を作成
                $ques_TF_with_WID = [];
                foreach ($ques_TF as $WID => $counts) {
                    $ques_TF_with_WID[] = ['WID' => $WID] + $counts;
                }

                usort($ques_TF_with_WID, function($a, $b) {
                    if ($a['accuracy'] == $b['accuracy']) {
                        return 0;
                    }
                    return ($a['accuracy'] < $b['accuracy']) ? -1 : 1;
                });

                // 正解率の低い順に上位5件を取得
                $lowest_accuracy_questions = array_slice($ques_TF_with_WID, 0, 5);
                
                // 正解率を計算
                $dif1_accuracy = calculateAccuracy($level_true[1], $level_false[1]);
                $dif2_accuracy = calculateAccuracy($level_true[2], $level_false[2]);
                $dif3_accuracy = calculateAccuracy($level_true[3], $level_false[3]);
                
                function calculateAccuracy($trueCount, $falseCount) {
                    $total = $trueCount + $falseCount;
                    return $total > 0 ? round($trueCount / $total * 100, 2) : 0;
                }
                

                for($i=-1; $i<=22; $i++){
                    //ゼロ除算を防ぐ
                    if($countgrammar[$i] != 0){
                        $accuracy_grammar[$i] = round($grammarCountTrue[$i] / $countgrammar[$i] * 100,2);
                        $hesitate_grammar[$i] = round($grammarCounthesitateT[$i] / $countgrammar[$i] * 100,2);
                    }else{
                        $accuracy_grammar[$i] = 0;
                        $hesitate_grammar[$i] = 0;
                    }
                }
                
                // $user_accuracyをJSONにエンコード
                /*
                $json_data_allacu = json_encode($accuracy_grammar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $json_data_allhesi = json_encode($hesitate_grammar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // JSONデータをファイルに保存
                $file_path_allacu = 'json/file_all_acu.json'; // JSONファイルを保存するパス
                $file_path_allhesi = 'json/file_all_hesi.json'; // JSONファイルを保存するパス
                $json_keep_result_allacu = file_put_contents($file_path_allacu, $json_data_allacu);
                $json_keep_result_alllhesi = file_put_contents($file_path_allhesi, $json_data_allhesi);
                if ($json_keep_result_allacu === false) {
                    //echo "エラーが発生しました。ファイルに書き込めませんでした。";
                } else {
                    echo "ファイルに書き込みが成功しました。";
                }
                */
                $studentAccuracyJson = json_encode($stu_TF);
                
            ?>
            
            <div class = "search" align = "center">
                <h2 onclick="openFilterModal()">検索フィルタ</h2>
            </div>
            <section class="overview">
                <div align ="center">
                    <h2>クラス全体の概要</h2>
                </div>
                <font size = "5">
                    <div class="overview-contents">
                        <div id = "allstu-info">
                            <h3>■全学生数:
                                <?php
                                    // データベースから学生数を取得
                                    $Studentconut = "SELECT count(distinct UID) FROM linedata";
                                    if (!empty($conditions)) {
                                        $Studentconut .= " WHERE " . join(" AND ", $conditions);
                                    }
                                    $StudentResult = mysqli_query($conn, $Studentconut);
                                    echo $StudentResult->fetch_row()[0];
                                ?>
                                人
                            </h3>
                        </div>
                        <div id = "allques-info">
                            <h3>■全データ数:
                                <?php
                                    // データベースからデータ数を取得
                                    echo mysqli_num_rows($result);
                                ?>
                                件
                            </h3>  <!-- データベースからデータ数を取得-->
                        </div>
                        <div class="average-study-time">
                            <h3>■平均解答時間
                                <?php
                                    // データベースから平均解答時間を取得
                                    //linedataのtimeを全て加算してデータ数で割る
                                    $StudentAll_Result_linedata = mysqli_query($conn, $_SESSION["sql"]);
                                    $sum = 0;
                                    while($row = mysqli_fetch_assoc($StudentAll_Result_linedata)){
                                        $sum = $sum + $row["Time"];
                                    }
                                    echo number_format(($sum / mysqli_num_rows($StudentAll_Result_linedata)) / 1000,2);
                                ?>
                                秒
                            </h3>
                        </div>
                        <div class="average-accuracy">
                            <h3>■正答率
                                <?php
                                    $StudentAll_Result_linedata = mysqli_query($conn, $_SESSION["sql"]);
                                    $sum_TF = 0;
                                    while($row = mysqli_fetch_assoc($StudentAll_Result_linedata)){
                                        if($row["TF"] == 1){
                                            $sum_TF = $sum_TF + 1;
                                        }
                                    }
                                    $aveaccuracy = number_format(($sum_TF / mysqli_num_rows($StudentAll_Result_linedata)) * 100,2);
                                    echo $aveaccuracy;
                                ?>
                            %
                            </h3>
                        </div>
                    </div>
                </font>
            </section>

            <section class="progress-chart">
                <h2>学習者情報</h2>
                <div class = "container">
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            var myChart;

                            document.getElementById("stu_info").addEventListener("click", function(event) {
                                event.preventDefault();
                                fetchStudents();
                            });

                            function fetchStudents() {
                                var form = document.getElementById('stu_form');
                                var formData = new FormData(form);
                                // FormDataの内容を確認
                                for (var pair of formData.entries()) {
                                    console.log(pair[0] + ': ' + pair[1]);
                                }

                                fetch('../fetch_student_details.php', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok ' + response.statusText);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Fetch successful');
                                    console.log(data);
                                    if (data.error) {
                                        console.error('Server error: ' + data.error);
                                    } else {
                                        document.getElementById('student-solve-ques').innerHTML = data.questions;
                                        document.getElementById('studentName').innerHTML = data.studentName;
                                        document.getElementById('quesTimeValue').innerHTML = data.studentQuesTime;
                                        console.log(data.stu_accuracy_grammar);
                                        document.getElementById('student-accuracy').style.display = "block";
                                        document.getElementById('studentQuesTime').style.display = "block";

                                        // テーブルにユーザーデータを追加
                                        //いったん消す
                                        var tableBody = document.querySelector('.table3 tbody');
                                        var userRow = document.createElement('tr');
                                        userRow.innerHTML = `
                                            <th>${data.studentName}</th>
                                            <td id="user-dif1">${data.Stu_accuracy_dif1}</td>
                                            <td id="user-dif2">${data.Stu_accuracy_dif2}</td>
                                            <td id="user-dif3">${data.Stu_accuracy_dif3}</td>
                                            <td><button class="delete-row">削除</button></td>
                                        `;
                                        tableBody.appendChild(userRow);
                                        // 削除ボタンにイベントリスナーを追加
                                        userRow.querySelector('.delete-row').addEventListener('click', function() {
                                            tableBody.removeChild(userRow);
                                        });
                                        //画像を更新
                                        var imgContainer = document.getElementById("comparison_data1");
                                        imgContainer.innerHTML = "";
                                        var plotImg = document.createElement("img");
                                        plotImg.src = data.image_path + "?t=" + new Date().getTime(); // 一意のクエリパラメータを追加
                                        plotImg.alt = "Accuracy grammar";
                                        imgContainer.appendChild(plotImg);
                                        

                                        createChart(data.correctCount, data.incorrectCount, data.totalCount);
                                    }
                                })
                                .catch(error => {
                                    console.error('Fetch error:', error);
                                });
                            }

                            function createChart(correctCount, incorrectCount, totalCount) {
                                if (totalCount === 0) {
                                    console.error('Total count is zero, cannot calculate percentages.');
                                    return;
                                }

                                var ctx = document.getElementById('Stu-Accuracy').getContext('2d');

                                if (myChart) {
                                    myChart.data.datasets[0].data = [correctCount, incorrectCount];
                                    myChart.update();
                                } else {
                                    myChart = new Chart(ctx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: ['正解', '不正解'],
                                            datasets: [{
                                                data: [correctCount, incorrectCount],
                                                backgroundColor: ['#FF6384', '#36A2EB'],
                                                hoverBackgroundColor: ['#FF6384', '#36A2EB']
                                            }]
                                        },
                                        options: {
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                title: {
                                                    display: false,
                                                }
                                            }
                                        }
                                    });
                                }
                            }
                        });

                    </script>
                </div>
                
                <div class = "container">
                    <div id = "studentdata-info">
                        <div align ="center">
                            <h3>正答率分布</h3>
                            <canvas id = "student-accuracy-chart" width="600" height="400"></canvas>
                        </div>
                        <div class = "canvascontent" align = "center">
                            <h3>解答時間分布</h3>
                            <canvas id = "student-time-chart" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>


                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        //PHPのデータをパース
                        var studentAccuracy = <?php echo $studentAccuracyJson; ?>;
                        //データをChart.js用に変換
                        var labels = [];
                        var data = [];
                        var timedata = [];

                        for (var UID in studentAccuracy) {
                            labels.push(UID);
                            data.push(studentAccuracy[UID].accuracy);
                            timedata.push(studentAccuracy[UID].Time);
                        }
                        console.log(labels);
                        console.log(data);
                        console.log(timedata);
                        var avg = <?php echo $aveaccuracy; ?>;
                        console.log(avg);
                        var averageLinePlugin = {
                            id: 'averageLine',
                            beforeDraw: function(chart) {
                                var ctx = chart.ctx; // グラフの描画コンテキストを取得
                                var yScale = chart.scales['y']; // y軸のスケールを取得
                                var yPos = yScale.getPixelForValue(avg); // 平均値に対応するy座標を取得

                                // 水平線を描画
                                ctx.save(); // 現在の描画状態を保存
                                ctx.beginPath(); // 新しいパスを開始
                                ctx.moveTo(chart.chartArea.left, yPos); // 水平線の開始点
                                ctx.lineTo(chart.chartArea.right, yPos); // 水平線の終了点
                                ctx.strokeStyle = 'red'; // 線の色を赤に設定
                                ctx.lineWidth = 2; // 線の幅を2に設定
                                ctx.stroke(); // 線を描画
                                ctx.restore(); // 保存した描画状態を復元

                                // 平均値のテキストを描画
                                ctx.fillStyle = 'red'; // テキストの色を赤に設定
                                ctx.fillText('平均値: ' + avg.toFixed(2) + '%', chart.chartArea.right - 100, yPos - 10); // テキストを描画
                            }
                        };

                        //Chart.jsの棒グラフ描画
                        var ctx = document.getElementById('student-accuracy-chart').getContext('2d');
                        var accuracychart = new Chart(ctx, {
                            type: 'bar',
                            data:{
                                labels:labels,
                                datasets:[{
                                    label:"正解率(%)",
                                    data:data,
                                    backgroundColor: 'rgba(0, 0, 255, 0.8)',
                                    borderColor: 'rgba(0, 0, 255, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options:{
                                scales:{
                                    y: {
                                        beginAtZero:true,
                                        max: 100
                                    }
                                }/*
                                plugins: {
                                    averageLine: true // カスタムプラグインを有効にする
                                }
                                    */
                            },
                            //plugins: [averageLinePlugin]
                            
                        });
                        //解答時間の平均のグラフ描画
                        var ctx1 = document.getElementById('student-time-chart').getContext('2d');
                        var timechart = new Chart(ctx1, {
                            type: 'bar',
                            data:{
                                labels: labels,
                                datasets: [{
                                    label: "解答時間(ms)",
                                    data: timedata,
                                    backgroundColor: 'rgba(0, 0, 255, 0.8)',
                                    borderColor: 'rgba(0, 0, 255, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options:{
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                },
                                
                            }
                        });

                        // 平均値を計算する関数
                        function getAverage(data) {
                            var sum = data.reduce(function(acc, val) { return acc + val; }, 0);
                            return sum / data.length;
                        }
                    })
                </script>
            </section>
            <section class="progress-chart" id = "studentanalytics">
                <div class = "head-content">
                    <div class = "title">
                        <h2>学習者分析</h2>
                    </div>
                    <div class="select-container" id = "select-learner">
                        <select id="multi-learner-list" multiple>
                            <option value=""  disabled selected>学習者一覧</option>
                            <!-- ここに学習者のリストを動的に追加 -->
                        </select>
                    </div>
                    <div class ="select-container" id = "select-ques">
                        <select id="ques-list">
                            <option value="" disabled selected>学習者を選択してください</option>
                            <!-- ここに問題のリストを動的に追加 -->
                        </select>
                    </div>
                </div>

                <div class = "container">
                    <div class = "subcontent50">
                        <h1 id = "student-name">学習者は選択されていません．</h1>
                        <div class="container">
                            <div class = "subcontent">
                                <h2 id = "student-ques-count">
                                    <!--ここに問題数が入る-->
                                </h2>
                            </div>
                            <div class = "subcontent">
                                <h2 id = "student-accuracy">
                                    <!--ここに正答率が入る-->
                                </h2>
                            </div>
                        </div>
                        <div class = "container">
                            <div class = "subcontent">
                                <h4>正解率が低い文法項目</h4>
                                <table border="1" id="miss-grammar-table" class = "table5">
                                    <thead>
                                        <tr>
                                            <th>順位</th>
                                            <th>文法項目</th>
                                            <th>正解率</th>
                                        </tr>
                                    <thead>
                                    <tbody>
                                        <!-- ここに検索結果で誤答率が高いものを表示 -->
                                    </tbody>
                                </table>
                            </div>
                            <div class = "subcontent">
                                <h4>苦手な文法項目</h4>
                                <table border="1" id="weak-grammar-table" class = "table5">
                                    <thead>
                                        <tr>
                                            <th>順位</th>
                                            <th>文法項目</th>
                                        </tr>
                                    <thead>
                                    <tbody>
                                        <!-- ここに検索結果で誤答率が高いものを表示 -->
                                        <tr>
                                            <td>1</td>
                                            <td>名詞</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>動詞</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>形容詞</td>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class = "container">
                            <div class = "subcontent">
                                <canvas id = "stu-accuracy-grammar"></canvas>
                            </div>
                        </div>
                        <div class = "container">
                            <div class = "subcontent">
                                <canvas id = "stu-time-chart"></canvas>
                            </div>
                        </div>
                        <select id="filtered-questions">
                            <option value = "">問題を選択してください</option>
                        </select>
                        <!--ユーザーが選択されたらここを動的に変化-->
                    </div>
                    <div class = "subcontent50">
                        <h1 id = "ques-name">問題は選択されていません！</h1>
                        <h1>■基本情報</h1>
                        <div id = "ques-info-data">
                            <div class = "container">
                                <div class = "textcontent-row">
                                </div>
                                <div class = "subcontent">
                                    <h2 id = "ques-sentence">
                                        mondaibun<!--ここに問題文が入る-->
                                    </h2>
                                </div>
                            </div>
                            <div class = "container">
                                <div class = "subcontent">
                                    <h3 id = "ques-grammar">
                                        <!--ここに文法項目が入る-->
                                    </h3>
                                </div>
                                <div class = "subcontent">
                                    <h3 id = "ques-level">
                                        <!--ここに難易度が入る-->
                                    </h3>
                                </div>
                                <div class = "subcontent">
                                    <h3 id = "ques-wordnum">
                                        <!--ここに単語数が入る-->
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class = "container">
                            <div class = "subcontent">
                                <h2 id = "ques-endsentence">
                                    <!--ここに最終並べ替え文が入る-->
                                </h2>
                                注:赤色の並べ替えで間違いが起きています
                            </div>
                        </div>
                        <div class = "container">
                            <div class = "subcontent">
                                <h2 id = "ques-TF">
                                    <!--ここにTFが入る-->
                                </h2>
                            </div>
                            <div class = "subcontent">
                                <h2 id = "ques-hesitateword">
                                    <!--ここに答えが入る-->
                                </h2>
                            </div>
                        </div>
                        <!--ユーザーが選択されたらここを動的に変化-->
                    </div>
                </div>
                

                <script>
                </script>
            </section>
            <script>
                function openFilterModal(){
                    document.getElementById("filter-modal").style.display = "block";
                }

                function closeFilterModal(){
                    document.getElementById("filter-modal").style.display = "none";
                }
            </script>
            <div id = "filter-modal" class = "modal">
                <div class = "modal-content">
                    <span class = "close" onclick="closeFilterModal()">&times;</span>
                    <h3>フィルタ条件を選択して下さい</h3>
                    <script>
                        function toggleMinMaxInputs() {
                            var selection = document.getElementById('TimeRangeid').value;
                            var minMaxDiv = document.getElementById('Timesearch_minmax');
                            var timeSearchInput = document.getElementById('Timesearchid');
                            if (selection === 'range') {
                                minMaxDiv.classList.remove('hide');  // 範囲が選択された場合、要素を表示
                                timeSearchInput.classList.add('hide');  // 範囲が選択された場合、要素を隠す
                            } else {
                                minMaxDiv.classList.add('hide');     // それ以外の場合は隠す
                                timeSearchInput.classList.remove('hide');   // それ以外の場合は表示
                            }
                        }
                    </script>
                    <form action="" method="post">
                        <div class = "center">
                            <table border="1" class = "table2">
                                <tr>
                                    <th>UID</th>
                                    <td>
                                        <select name="UIDrange">
                                            <option value = "include">含む</option>
                                            <option value = "not">以外</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="UIDsearch">
                                    </td>
                                </tr>
                                <tr>
                                    <th>WID</th>
                                    <td>
                                        <select name="WIDrange">
                                            <option value = "include">含む</option>
                                            <option value = "not">以外</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="WIDsearch">
                                    </td>
                                </tr>
                                <tr>
                                    <th>正誤</th>
                                    <td colspan="2"><input type="radio" name = "TFsearch" value="1">正解　<input type="radio" name="TFsearch" value="0">不正解</td>
                                </tr>
                                <tr>
                                    <th>解答時間</th>
                                    <td>
                                        <select name="TimeRange" id = "TimeRangeid" onchange="toggleMinMaxInputs()">
                                            <option value = "above">以上</option>
                                            <option value = "below">以下</option>
                                            <option value = "range">範囲</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name = "Timesearch" id = "Timesearchid">
                                        <div id ="Timesearch_minmax" class = "hide">
                                            <input type="text" name = "Timesearch-min">～<input type="text" name="Timesearch-max">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        <input type="submit" value="検索">   
                        </div>
                    </form>
                </div>
            </div>
            <section class="weak-areas">
                <h2>苦手分野</h2>
                <div class = "row-content">
                    <div class = "content">
                        <h4>難易度ごとの正答率</h4>
                        <table border="1" class = "table3">
                            <thead>
                                <tr>
                                    <th></th><th>初級</th><th>中級</th><th>上級</th><th>削除</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>平均</th>
                                    <td><?php echo $dif1_accuracy; ?></td>
                                    <td><?php echo $dif2_accuracy; ?></td>
                                    <td><?php echo $dif3_accuracy; ?></td>
                                    <td></td>
                                </tr>
                            <!-- テーブルにユーザーデータを追加-->
                            </tbody>
                        </table>
                    </div>
                    <div class="content">
                        <h4>文法項目ごとの正答率</h4>
                        <div class = "img-chart" id ="comparison_data1">
                            <img src="images/comparison_grammar_accuracy.jpg" alt="Accuracy Histogram">
                        </div>
                    </div>
                    <div class="content">
                        <h4>文法項目ごとの迷い率</h4>
                        <div class = "img-chart">
                            <img src="images/comparison_data2.png" alt="Accuracy Histogram">
                        </div>
                    </div>
                </div>
                </div>
                <div class = "row-content">
                    <div class = "content">
                        苦手だと考えられる問題
                    </div>
                </div>

            </section>
        </main>
    </div>
</body>
</html>
