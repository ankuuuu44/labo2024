<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>生徒_履歴分析</title>
    <link rel="stylesheet" href="stylestu.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<?php
    session_start();
?>
<body>
    <header class="header">
        <div class="welcome">
            <font size="5">
            <?php   
                require "dbc.php";
                $_SESSION["MemberID"] = "9131114";//デバッグのため．！！！！！ここは消す．
                echo "ようこそ",$_SESSION["MemberName"],"さん！<br>";
                $linedataSQL = "SELECT * FROM linedata WHERE UID = {$_SESSION['MemberID']}";
                $linedataRes = mysqli_query($conn, $linedataSQL);
                $linedataSQL_countrow = mysqli_num_rows($linedataRes);
                echo "解答問題数",$linedataSQL_countrow,"問<br>";
            ?>
            </font>
        </div>
        <button class="logout-button">ログアウト</button>
    </header>
    <main class="main-content">
        <div class="box">
            解答問題一覧<br>
            <select name="answer_ques" id="answer_ques" size="15" >
            <?php 
                $answer_info_sql = "SELECT linedata.WID, linedata.TF,question_info.Sentence FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID WHERE linedata.UID = {$_SESSION['MemberID']}";
                $answer_info_res = mysqli_query($conn, $answer_info_sql);
                
                while($linedataRows = $answer_info_res->fetch_assoc()){
                    if($linedataRows['TF'] == 1){
                        $echoTrue = "〇";
                    }else{
                        $echoTrue = "×";
                    }
                    echo "<option value='",$linedataRows['WID'],"'>",$linedataRows['WID']," ",$linedataRows['Sentence']," ",$echoTrue,"</option>";
                }
                
            ?>
            </select>
        </div>
        <div class="box">
            <div class = "content_column_two">
                <div id = "a">
                解答状況一覧<br>
                <div class = "center">
                <div id = "canvasA">
                    <canvas id = "pieChart" max-width="300px",max-height = "300px"></canvas>
                </div>
                </div>
                </div>
                <?php
                    $answer_count_TF_sql = "SELECT TF,COUNT(TF) FROM linedata WHERE UID = {$_SESSION['MemberID']} GROUP BY TF";
                    $answer_count_TF_res = mysqli_query($conn, $answer_count_TF_sql);
                    while($answer_count_TF_rows = $answer_count_TF_res->fetch_assoc()){
                        $TF_count[] = $answer_count_TF_rows['COUNT(TF)']; 
                    }  
                    $allques_count_sql = "SELECT COUNT(*) FROM question_info";
                    $allques_count_res = mysqli_query($conn, $allques_count_sql);
                    $allques_count_rows = $allques_count_res->fetch_assoc();
                    $yet_answer_count = intval($allques_count_rows["COUNT(*)"]) - intval($linedataSQL_countrow);



                ?>
                <script>
                    var Fcount = <?php echo $TF_count[0];?>;
                    var Tcount = <?php echo $TF_count[1];?>;
                    var yetcount = <?php echo $yet_answer_count;?>;
                    var allquescount = <?php echo $allques_count_rows["COUNT(*)"]?>;
                    var answer_ratio = (Tcount+Fcount) / allquescount;

                    console.log("Fcount:" + Fcount + "Tcount:" + Tcount + "yetcount:" + yetcount);
                    var ctx = document.getElementById('pieChart').getContext('2d');
                    const counter = {
                        id: 'counter',
                        beforeDraw(chart, args, options) {
                            const { ctx, chartArea: { top, right , bottom, left, width, height } } = chart;
                            ctx.save();
                            ctx.fillStyle = 'black';
                            ctx.fillRect(width / 2, top + (height / 2), 0, 0);
                            ctx.font = '32px sans-serif';
                            ctx.textAlign = 'center';

                            // 位置調整
                            console.log("width", width);
                            console.log("height", height);
                            console.log("top", top);
                            console.log("width / 2, top + (height / 2)", width / 2, top + (height / 2));
                            ctx.fillText(((answer_ratio*100).toPrecision(3))+'%', width / 2, top + (height / 2));
                        }
                    };
                    const myPieChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['不正解', '正解','未回答'],
                            datasets: [{
                                data: [Fcount, Tcount, yetcount],
                                backgroundColor: ['blue', 'red', 'gray']
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                            legend: {
                                display: false,
                                position: 'left',
                            },
                            title: {
                                display: true,
                                text: '解答状況',
                                position: 'top',
                                align: 'center',
                                fontSize: '50px',
                            },
                            counter: {
                                fontColor: 'red',
                                fontSize: '50px',
                                fontFamily: 'sans-serif',
                            },
                            },
                        },
                        plugins: [counter]
                });
                </script>
                <div id = "b">
                    正答率
                    <div id = "canvasA">
                        <canvas id = "pieChart_acuracy" max-width="300px",max-height = "300px"></canvas>
                    </div>
                    <script>
                        /*
                        var Fcount = <?php //echo $TF_count[0];?>;
                        var Tcount = <?php //echo $TF_count[1];?>;
                        var answer_ratio = (Tcount+Fcount) / allquescount;

                        console.log("Fcount:" + Fcount + "Tcount:" + Tcount + "yetcount:" + yetcount);
                        var ctx = document.getElementById('pieChart').getContext('2d');
                        const counter1 = {
                            id: 'counter',
                            beforeDraw(chart, args, options) {
                                const { ctx, chartArea: { top, right , bottom, left, width, height } } = chart;
                                ctx.save();
                                ctx.fillStyle = 'black';
                                ctx.fillRect(width / 2, top + (height / 2), 0, 0);
                                ctx.font = '32px sans-serif';
                                ctx.textAlign = 'center';

                                // 位置調整
                                console.log("width", width);
                                console.log("height", height);
                                console.log("top", top);
                                console.log("width / 2, top + (height / 2)", width / 2, top + (height / 2));
                                ctx.fillText(((answer_ratio*100).toPrecision(3))+'%', width / 2, top + (height / 2));
                            }
                        };
                        const myPieChart1 = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['不正解', '正解','未回答'],
                                datasets: [{
                                    data: [Fcount, Tcount, yetcount],
                                    backgroundColor: ['blue', 'red', 'gray']
                                }],
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                legend: {
                                    display: false,
                                    position: 'left',
                                },
                                title: {
                                    display: true,
                                    text: '解答状況',
                                    position: 'top',
                                    align: 'center',
                                    fontSize: '50px',
                                },
                                counter: {
                                    fontColor: 'red',
                                    fontSize: '50px',
                                    fontFamily: 'sans-serif',
                                },
                                },
                            },
                            plugins: [counter]
                    });
                    */
                </script>

                </div>
            </div>
        </div>
        <div class="box">登録問題一覧<br>
            <select name="answer_ques" id="answer_ques" size="15" >
                <?php 
                    $allques_sql = "SELECT * FROM question_info";
                    $allques_res = mysqli_query($conn, $allques_sql);
                    
                    while($allquesRows = $allques_res->fetch_assoc()){
                        echo "<option value='",$allquesRows['WID'],"'>",$allquesRows['WID']," ",$allquesRows['Sentence'],"</option>";
                    }
                    
                ?>
                </select>
        </div>
        <div class="box">
            迷いの有無をもとにレーダーチャートで各文法項目の理解度を示す．
        </div>
    </main>
</body>
</html>
