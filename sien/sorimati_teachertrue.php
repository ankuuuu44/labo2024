<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>教師用ダッシュボード</title>
    <link rel="stylesheet" href="sorimati_teachertrue_styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
</head>

<body>
    <?php
    session_start();
    require "dbc.php";
    ?>
    <header>
        <div class="logo">データ分析ページ</div>
        <nav>
            <ul>
                <li><a href="#">ホーム</a></li>
                <li><a href="#">学習履歴</a></li>
                <li><a href="machineLearning_sample.php">迷い推定・機械学習</a></li>
                <li><a href="#">苦手分野</a></li>
                <li><a href="#">設定</a></li>
            </ul>
        </nav>
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
            $max_accuracy = 0;
            $min_accuracy = 100;
            while ($row = mysqli_fetch_assoc($result)) {
                if (!array_key_exists($row['UID'], $user_accuracy)) {
                    //キーが存在せんかったら新しいの追加
                    $user_accuracy[$row['UID']] = ['correct' => 0, 'total' => 0, 'accuracy' => 0];
                }
                //解答問題数を追加
                $user_accuracy[$row['UID']]['total']++;
                if ($row['TF'] == 1) {
                    //正解数を追加
                    $user_accuracy[$row['UID']]['correct']++;
                }
            }

            // 各学習者ごとの正解率を計算
            foreach ($user_accuracy as $key => $value) {
                $user_accuracy[$key]['accuracy'] = round($value['correct'] / $value['total'] * 100, 2);
                //echo "user_accuracy[$key]['accuracy'] =" ,$user_accuracy[$key]['accuracy'];
                if ($user_accuracy[$key]['accuracy'] > $max_accuracy) {
                    $max_accuracy = $user_accuracy[$key]['accuracy'];
                }
                if ($user_accuracy[$key]['accuracy'] < $min_accuracy) {
                    $min_accuracy = $user_accuracy[$key]['accuracy'];
                }
            }
            // $user_accuracyをJSONにエンコード
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



            //難易度ごとの正解数記録
            $sql_dif_TF = "SELECT UID,linedata.WID,TF,Understand,question_info.level,question_info.grammar FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID";
            if (!empty($conditions)) {
                $sql_dif_TF .= " WHERE " . join(" AND ", $conditions);
            }
            $result_dif_TF = mysqli_query($conn, $sql_dif_TF);
            $level_true = [1 => 0, 2 => 0, 3 => 0];
            $level_false = [1 => 0, 2 => 0, 3 => 0];
            $countgrammar = [];
            $grammarCountTrue = [];
            $grammarCountFalse = [];
            $grammarCounthesitateT = [];
            $grammarCounthesitateF = [];

            for ($i = -1; $i <= 22; $i++) {
                $countgrammar[(string) $i] = 0;
                $grammarCountTrue[(string) $i] = 0;
                $grammarCountFalse[(string) $i] = 0;
                $grammarCounthesitateT[(string) $i] = 0;
                $grammarCounthesitateF[(string) $i] = 0;
            }
            while ($row = $result_dif_TF->fetch_assoc()) {
                $grammarItems = array_filter(explode("#", $row["grammar"]), function ($value) {
                    return $value !== '';
                });
                // デバッグ用メッセージ
                error_log("Grammar items: " . implode(",", $grammarItems));

                foreach ($grammarItems as $value) {
                    $countgrammar[$value]++;
                    if ($row["TF"] == 1) {
                        $grammarCountTrue[$value]++;
                        if ($row['Understand'] == 2)
                            $grammarCounthesitateT[$value]++;
                    } else {
                        $grammarCountFalse[$value]++;
                        if ($row['Understand'] == 4)
                            $grammarCounthesitateF[$value]++;
                    }
                }

                $level = $row["level"];
                if ($row["TF"] == 1) {
                    $level_true[$level]++;
                } else {
                    $level_false[$level]++;
                }

            }
            // 正解率を計算
            $dif1_accuracy = calculateAccuracy($level_true[1], $level_false[1]);
            $dif2_accuracy = calculateAccuracy($level_true[2], $level_false[2]);
            $dif3_accuracy = calculateAccuracy($level_true[3], $level_false[3]);

            function calculateAccuracy($trueCount, $falseCount)
            {
                $total = $trueCount + $falseCount;
                return $total > 0 ? round($trueCount / $total * 100, 2) : 0;
            }


            for ($i = -1; $i <= 22; $i++) {
                //ゼロ除算を防ぐ
                if ($countgrammar[$i] != 0) {
                    $accuracy_grammar[$i] = round($grammarCountTrue[$i] / $countgrammar[$i] * 100, 2);
                    $hesitate_grammar[$i] = round($grammarCounthesitateT[$i] / $countgrammar[$i] * 100, 2);
                } else {
                    $accuracy_grammar[$i] = 0;
                    $hesitate_grammar[$i] = 0;
                }
            }

            // $user_accuracyをJSONにエンコード
            $json_data_allacu = json_encode($accuracy_grammar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $json_data_allhesi = json_encode($hesitate_grammar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            // JSONデータをファイルに保存
            $file_path_allacu = 'json/file_all_acu.json'; // JSONファイルを保存するパス
            $file_path_allhesi = 'json/file_all_hesi.json'; // JSONファイルを保存するパス
            $json_keep_result_allacu = file_put_contents($file_path_allacu, $json_data_allacu);
            $json_keep_result_alllhesi = file_put_contents($file_path_allhesi, $json_data_allhesi);
            if ($json_keep_result_allacu === false) {
                echo "エラーが発生しました。ファイルに書き込めませんでした。";
            } else {
                echo "ファイルに書き込みが成功しました。";
            }

            ?>

            <section class="overview">
                <section id="head">
                    <h2>クラス全体の概要</h2>
                    <div class="dropdown" id="first_dropdown">
                        <button class="dropbtn" id="dropdownButton2">
                            <span id="dropdownText2">日付一覧</span>
                            <div class="arrow"></div>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" onclick="setDateRange('全期間', 'dropdownText2')">全期間</a>
                            <a href="#" onclick="setDateRange('5月', 'dropdownText2')">5月</a>
                            <a href="#" onclick="setDateRange('6月', 'dropdownText2')">6月</a>
                            <a href="#" onclick="setDateRange('7月', 'dropdownText2')">7月</a>
                        </div>
                    </div>

                    <div class="dropdown" id="second_dropdown">
                        <button class="dropbtn" id="dropdownButton1">
                            <span id="dropdownText1">問題一覧</span>
                            <div class="arrow"></div>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" onclick="setDateRange('全ての問題', 'dropdownText1')">全ての問題</a>
                            <a href="#" onclick="setDateRange('問題1', 'dropdownText1')">問題1</a>
                            <a href="#" onclick="setDateRange('問題2', 'dropdownText1')">問題2</a>
                            <a href="#" onclick="setDateRange('問題3', 'dropdownText1')">問題3</a>
                            <a href="#" onclick="setDateRange('問題4', 'dropdownText1')">問題4</a>
                        </div>
                    </div>
                    <script>
                        function setDateRange(selectedOption, textId) {
                            document.getElementById(textId).textContent = selectedOption;
                        }
                    </script>
                </section>
                <font size="5">
                    <div class="overview-contents">
                        <div id="allstu-info">
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
                        <div id="allques-info">
                            <h3>■全データ数:
                                <?php
                                // データベースからデータ数を取得
                                echo mysqli_num_rows($result);
                                ?>
                                件
                            </h3> <!-- データベースからデータ数を取得-->
                        </div>
                        <div class="average-accuracy">
                            <h3>■平均正答率
                                <?php
                                $StudentAll_Result_linedata = mysqli_query($conn, $_SESSION["sql"]);
                                $sum_TF = 0;
                                while ($row = mysqli_fetch_assoc($StudentAll_Result_linedata)) {
                                    if ($row["TF"] == 1) {
                                        $sum_TF = $sum_TF + 1;
                                    }
                                }
                                echo number_format(($sum_TF / mysqli_num_rows($StudentAll_Result_linedata)) * 100, 2);
                                ?>
                                %
                            </h3>
                        </div>
                        <div class="average-hesitation">
                            <h3>■平均迷い度
                                <?php
                                echo "30.5";
                                ?>
                            </h3>
                        </div>
                    </div>
                    <div class="overview-contents">
                        <div class="max-accuracy">
                            <h3>■最高正答率
                                <?php
                                echo $max_accuracy;
                                ?>
                                %
                            </h3>
                        </div>
                        <div class="min-accuracy">
                            <h3>■最低正答率
                                <?php
                                echo $min_accuracy;
                                ?>
                                %
                            </h3>
                        </div>
                        <div class="max-hesitation">
                            <h3>■最高迷い度
                                <?php
                                echo "80";
                                ?>
                            </h3>
                        </div>
                        <div class="min-hesitation">
                            <h3>■最低迷い度
                                <?php
                                echo "10.2";
                                ?>
                            </h3>
                        </div>
                    </div>

                </font>
                <section class="sori">
                    <div class="row-content">
                        <div class="content">
                            <h4>ボリュームゾーンのグラフ</h4>
                            <div class="img-chart" id="comparison_data1">
                                <img src="images/accuracy_histogram_1.png" alt="Accuracy Histogram">
                            </div>
                        </div>
                        <div class="content">
                            <h4>全員が苦手と予測される文法項目</h4>
                            <table border="1" class="table4">
                                <thead>
                                    <tr>
                                        <th id=first></th>
                                        <th id=second>関連分野</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th id=first>ワースト1</th>
                                        <td><?php echo "関係詞" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト2</th>
                                        <td><?php echo "代名詞" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト3</th>
                                        <td><?php echo "動名詞" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト4</th>
                                        <td><?php echo "" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト5</th>
                                        <td><?php echo "" ?></td>
                                    </tr>
                                    <!-- テーブルにユーザーデータを追加-->
                                </tbody>
                            </table>
                        </div>
                        <div class="content">
                            <h4>全員が正答率の低い文法項目</h4>
                            <table border="1" class="table4">
                                <thead>
                                    <tr>
                                        <th id=first></th>
                                        <th id=second>関連分野</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th id=first>ワースト1</th>
                                        <td><?php echo "動詞" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト2</th>
                                        <td><?php echo "名詞" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト3</th>
                                        <td><?php echo "革命せよ" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト4</th>
                                        <td><?php echo "" ?></td>
                                    </tr>
                                    <tr>
                                        <th id=first>ワースト5</th>
                                        <td><?php echo "" ?></td>
                                    </tr>
                                    <!-- テーブルにユーザーデータを追加-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </section>

            <script>
                function openFilterModal() {
                    document.getElementById("filter-modal").style.display = "block";
                }

                function closeFilterModal() {
                    document.getElementById("filter-modal").style.display = "none";
                }
            </script>
            <div id="filter-modal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeFilterModal()">&times;</span>
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
                    <form action="teachertrue.php" method="post">
                        <div class="center">
                            <table border="1" class="table2">
                                <tr>
                                    <th>UID</th>
                                    <td>
                                        <select name="UIDrange">
                                            <option value="include">含む</option>
                                            <option value="not">以外</option>
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
                                            <option value="include">含む</option>
                                            <option value="not">以外</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="WIDsearch">
                                    </td>
                                </tr>
                                <tr>
                                    <th>正誤</th>
                                    <td colspan="2"><input type="radio" name="TFsearch" value="1">正解　<input type="radio"
                                            name="TFsearch" value="0">不正解</td>
                                </tr>
                                <tr>
                                    <th>解答時間</th>
                                    <td>
                                        <select name="TimeRange" id="TimeRangeid" onchange="toggleMinMaxInputs()">
                                            <option value="above">以上</option>
                                            <option value="below">以下</option>
                                            <option value="range">範囲</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="Timesearch" id="Timesearchid">
                                        <div id="Timesearch_minmax" class="hide">
                                            <input type="text" name="Timesearch-min">～<input type="text"
                                                name="Timesearch-max">
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
                <div id="head">
                    <h2>個別学習者の詳細</h2>
                    <div class="dropdown" id="first_dropdown">
                        <button class="dropbtn" id="dropdownButton3">
                            <span id="dropdownText3">学習者一覧</span>
                            <div class="arrow"></div>
                        </button>
                        <div class="dropdown-content" id="studentDropdownContent">
                            <!-- 学習者一覧がここに動的に追加される -->
                        </div>
                    </div>
                    <div class="dropdown" id="second_dropdown">
                        <button class="dropbtn" id="dropdownButton4">
                            <span id="dropdownText4">問題一覧</span>
                            <div class="arrow"></div>
                        </button>
                        <div class="dropdown-content" id="questionDropdownContent">
                            <!-- 問題一覧がここに動的に追加される -->
                        </div>
                    </div>
                    <script>
                        function setDateRange(selectedOption, textId) {
                            document.getElementById(textId).textContent = selectedOption;
                            if (textId === 'dropdownText3') {
                                document.getElementById('selectedUID').textContent = selectedOption;
                            } else if (textId === 'dropdownText4') {
                                document.getElementById('selectedWID').textContent = selectedOption;
                            }
                        }

                        document.addEventListener('DOMContentLoaded', function () {
                            fetchStudentsList();
                        });

                        function fetchStudentsList() {
                            fetch('fetch_students_list.php')
                                .then(response => response.json())
                                .then(data => {
                                    const studentDropdown = document.getElementById('studentDropdownContent');
                                    studentDropdown.innerHTML = '';
                                    data.students.forEach(student => {
                                        const a = document.createElement('a');
                                        a.href = '#';
                                        a.textContent = student.UID;
                                        a.onclick = () => {
                                            setDateRange(student.UID, 'dropdownText3');
                                            fetchQuestions(student.UID);
                                        };
                                        studentDropdown.appendChild(a);
                                    });
                                })
                                .catch(error => console.error('Error fetching students:', error));
                        }

                        function fetchQuestions(uid) {
                            fetch('fetch_questions.php?uid=' + uid)
                                .then(response => response.json())
                                .then(data => {
                                    const questionDropdown = document.getElementById('questionDropdownContent');
                                    questionDropdown.innerHTML = '';
                                    data.questions.forEach(question => {
                                        const a = document.createElement('a');
                                        a.href = '#';
                                        a.textContent = question.WID;
                                        a.onclick = () => setDateRange(question.WID, 'dropdownText4');
                                        questionDropdown.appendChild(a);
                                    });
                                })
                                .catch(error => console.error('Error fetching questions:', error));
                        }
                    </script>
                </div>
                <div id="selectedDetails" class="split-container">
                    <div class="left-panel">
                        <div class="header-container">
                            <h2>学習者</h2>
                            <h2 id="selectedUID">未選択</h2>
                            <h2>のデータ詳細</h2>
                        </div>
                        <div class="text-container">
                            <h3>■理解度: ?????</h3>
                        </div>
                        <div class="text-container">
                            <h3>■全データ数: 30 件</h3>
                            <h3>■正答率 53.04 %</h3>
                            <h3>■平均迷い度 30.5</h3>
                        </div>
                        <div class="text-container">
                            <div class="content">
                                <h4>文法項目ごとの正答率</h4>
                                <div class="img-chart" id="comparison_data1">
                                    <img src="images/comparison_data_3.png" alt="comparison_bargraph">
                                </div>
                            </div>
                        </div>
                        <div class="text-container">
                            <div class="content">
                                <h4>苦手と予測される文法項目</h4>
                                <table border="1" class="table5">
                                    <thead>
                                        <tr>
                                            <th id=first></th>
                                            <th id=second>関連分野</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th id=first>ワースト1</th>
                                            <td><?php echo "関係詞" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト2</th>
                                            <td><?php echo "代名詞" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト3</th>
                                            <td><?php echo "動名詞" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト4</th>
                                            <td><?php echo "" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト5</th>
                                            <td><?php echo "" ?></td>
                                        </tr>
                                        <!-- テーブルにユーザーデータを追加-->
                                    </tbody>
                                </table>
                            </div>
                            <div class="content">
                                <h4>正答率の低い文法項目</h4>
                                <table border="1" class="table5">
                                    <thead>
                                        <tr>
                                            <th id=first></th>
                                            <th id=second>関連分野</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th id=first>ワースト1</th>
                                            <td><?php echo "動詞" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト2</th>
                                            <td><?php echo "名詞" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト3</th>
                                            <td><?php echo "革命せよ" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト4</th>
                                            <td><?php echo "" ?></td>
                                        </tr>
                                        <tr>
                                            <th id=first>ワースト5</th>
                                            <td><?php echo "" ?></td>
                                        </tr>
                                        <!-- テーブルにユーザーデータを追加-->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="right-panel">
                        <div class="header-container">
                            <h2>問題</h2>
                            <h2 id="selectedWID">未選択</h2>
                            <h2>のデータ詳細</h2>
                        </div>
                        <div class="text-container">
                            <h3>問題文:</h3>
                        </div>
                        <div class="text-container">
                            <h3>There are many ways to solve this problem.</h3>
                        </div>
                        <div class="text-container">
                            <h3>■文法項目: There・it</h3>
                            <h3>■難易度: 53.04 %</h3>
                            <h3>■単語数: 8</h3>
                        </div>
                        <div class="text-container">
                            <h4></h4>
                        </div>
                        <div class="text-container">
                            <div class="content">
                                <h4>迷った単語群:</h4>
                                <div class="img-chart" id="comparison_data1">
                                    <img src="images/example_1.png" alt="example">
                                </div>
                            </div>
                        </div>
                        <div class="text-container">
                            <h3>■入力した迷い度: 2</h3>
                            <h3>■正誤: 不正解</h3>
                        </div>
                    </div>
                </div>
                <div class="row-content">
                    <h4></h4>
                </div>
                <div class="row-content">
                    <h4></h4>
                </div>
                <div class="header-container">
                    <h2>詳細</h2>
                </div>
                <div class="row-content">
                    <div class="content">
                        <h4>難易度ごとの正答率</h4>
                        <table border="1" class="table3">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>初級</th>
                                    <th>中級</th>
                                    <th>上級</th>
                                    <th>削除</th>
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
                        <div class="img-chart" id="comparison_data1">
                            <img src="images/comparison_grammar_accuracy.jpg" alt="Accuracy Histogram">
                        </div>
                    </div>
                    <div class="content">
                        <h4>文法項目ごとの迷い率</h4>
                        <div class="img-chart">
                            <img src="images/comparison_data2.png" alt="Accuracy Histogram">
                        </div>
                    </div>
                </div>
        </main>
    </div>
    <div class="row-content">
        <div class="content">
            苦手だと考えられる問題
        </div>
    </div>

    </section>
    </main>
    </div>
</body>

</html>