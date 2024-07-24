<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教師用ダッシュボード</title>
    <link rel="stylesheet" href="teachertrue_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>
<body>
    <?php
        require "dbc.php";
    ?>
    <header>
        <div class="logo">データ分析ページ</div>
        <nav>
            <ul>
                <li><a href="teachertrue.php">ホーム</a></li>
                <li><a href="#">学習履歴</a></li>
                <li><a href="machineLearning_sample.php">迷い推定・機械学習</a></li>
                <li><a href="#">苦手分野</a></li>
                <li><a href="#">設定</a></li>
            </ul>
        </nav>
        <div class="profile">プロフィール</div>
    </header>
    <div class="container">
        <aside>
            <ul>
                <li><a href="#">ダッシュボード</a></li>
                <li><a href="#">クラス管理</a></li>
                <li><a href="machineLearning_sample.php">迷い推定・機械学習</a></li>
                <li><a href="#">学習履歴</a></li>
                <li><a href="#">苦手分野</a></li>
                <li><a href="#">設定</a></li>
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
                        $conditions[] = "WID NOT IN (" . mysqli_real_escape_string($conn, $WIDsearch) . ")";
                    } else {
                        $conditions[] = "WID IN (" . mysqli_real_escape_string($conn, $WIDsearch) . ")";
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

                // 条件が一つでもあればWHERE句を追加
                if (!empty($conditions)) {
                    $sql .= " WHERE " . join(" AND ", $conditions);
                }
                echo $sql;

                // SQL実行  
                $result = mysqli_query($conn, $sql);
            ?>
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
                            <h3>平均解答時間
                                <?php
                                    // データベースから平均解答時間を取得
                                    //linedataのtimeを全て加算してデータ数で割る
                                    $StudentAll_Result_linedata = mysqli_query($conn, $sql);
                                    $sum = 0;
                                    while($row = mysqli_fetch_assoc($StudentAll_Result_linedata)){
                                        $sum = $sum + $row["Time"];
                                    }
                                    echo number_format(($sum / mysqli_num_rows($StudentAll_Result_linedata)) / 1000,2);
                                    
                                ?>
                                秒
                            </h3>
                        </div>
                    </div>
                </font>
            </section>

            <section class="progress-chart">
                <h2 onclick="openFilterModal()">検索フィルタ</h2>
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
                    <form action="machineLearning_sample.php" method="post">
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
            <section class="individual-details">
                <h2>履歴データ</h2>
                <!--表形式でデータ表示-->
                <div class = "scroll-table">
                    <table border="1" class = "table1">
                        <tr><th>UID</th><th>WID</th><th>解答日時</th><th>正誤</th><th>解答時間</th></tr>
                        <?php 
                            while($row = mysqli_fetch_assoc($result)){
                                if($row["TF"] == 1){
                                    $row["TF"] = "〇";
                                }else{
                                    $row["TF"] = "×";
                                }
                                echo "<tr><td>" . $row["UID"] . "</td><td>" . $row["WID"] . "</td><td>" . $row["Date"] . "</td><td>" . $row["TF"] . "</td><td>" . $row["Time"] . "</td></tr>";
                            }
                        ?>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
