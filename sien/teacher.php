<?php
session_start();
?>
<!DOCTYPE html>

<html>
    <head>
    <link rel="stylesheet" href="../StyleSheet.css" type="text/css" />  
    <link rel = "stylesheet" href="style.css">
        <meta charset="UTF-8">
        <!-- 画面上に仮想のwidthのモニターを作り，そこに1.0の倍率で表示するよという意味-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!---Chart.jsの読み込み-->
        <!--なんかコメントアウトしているものだと円グラフが表示されない．．．調査求-->
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js" type="text/javascript"></script>-->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
        
        <title>BI analytics</title>

        
        
    </head>

    <body>
    <span style="line-height:20px">         <!--行間隔指定-->
    <div align="center">                    <!--</div>まで文字を中央に指定-->
    <img src="../mondai/image/logo.png">    <!-- 背景-->
    <script src = "bi.js" defer></script>
    <h2 class="titleframe">ここは学習分析ページです．</h2>


    </div>
    <div class = "container">
        <div class = "user-container">
        <?php
        
            require "dbmember.php";
            //データベースの値を表示
            while($row = $result -> fetch_assoc()){
                //ユーザー名をクリックできるように表示
                //<a href = "#"はページ遷移したくないけどリンクタグを埋め込みたいときに使用する;
                //class属性はダブルクォートで囲む必要あり．注意ね！
                //ダブルクォートを文字列で使用したいときはシングルクォートで挟む
                $userLink = '<a href = "#" class = "user-link" data-users-id ="' . $row["uid"]. '">';
                $userLink.= 'UID:' . $row["uid"] . 'Name:' . $row["Name"]. '</a><br>';
                echo $userLink;


            }

            
        ?>
        
        <?php
        /*
            require "dbques.php";
            while ($rowques = $resultques -> fetch_assoc()){
                //問題もユーザー名同様にクリックできるように表示
                $quesLink = '<a href = "#" class = "ques-link" data-ques-id = "' . $row['WID']. '">';
                $quesLink.='WID:'. $row["WID"] . 'Sentence:' . $row["Sentence"] . '</a><br>';
                echo $quesLink;
            }
            */
        ?>
    
        </div>
        

        <!-- 履歴データを表示する要素 -->
        <div id="history-container"></div>

        <!---グラフ描画エリア-->
        
        <canvas id="canvasTF">
        ※表示にはcanvas要素を解釈可能なブラウザが必要です。
        </canvas>

        <script>
    var canvasTF = document.getElementById('canvasTF').getContext('2d');
    var myDoughnutChart;    // チャートの変数を定義

    // 既存のチャートを破棄
    
    if (myDoughnutChart) {
        myDoughnutChart.destroy();
    }
    /*

    var chartData = {
        labels: ['正解', '不正解'],
        datasets: [{
        data: [15, 20],
        backgroundColor: ['green', 'red']
        }]
    };
    */

    myDoughnutChart = new Chart(canvasTF, {
        type: 'pie',
        data: {
            labels:["正解","不正解"],
            datasets:[{
                data:[3,9],
                backgroundColor:['red','green']
            }]
        }
    });
    
</script>
        <canvas id = "canvasBar">
        
        </canvas>
            

        
        
    </div>

    <div class="search-container">

        <a href="#" id = "studenttree-menu">学習者検索</a>
            <div class="student_search">
                <div class = "student-all">
                <ul>
                    <li><a href="#" id = "studentall-menu">学習者一覧</a></li>
                        <ul class="user-list">
                            <li><label><input type="checkbox">UID:60910014</label></li>
                        </ul>
                    <li><a href="#" id = "tfacuracy">正解率</a></li>
                </ul>
            </div>


    
        <a href="#" id = "questree-menu">問題検索</a>

            <div class = "ques_search">
                <ul>
                    <li><a href="#" id = "quesall-menu">問題一覧</a></li>
                    <li><a href="#" id = "grammar-menu">文法項目</a></li>
                    <li><a href="#" id = "level-menu">難易度</a></li>
                    

                </ul>

            </div>
    </div>

        
        

    

    <script src = "search.js"></script>    
    </body>
</html>