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
        session_start();
        require "dbc.php";
        // セッション変数をクリアする（必要に応じて）
        unset($_SESSION['conditions']);
    ?>
    <header>
        <div class="logo">データ分析ページ</div>
        <nav>
            <ul>
                <li><a href="#">ホーム</a></li>
                <li><a href="#">学習履歴</a></li>

            </ul>
        </nav>
    </header>
    <div class="container">
        <aside>
            <ul>
                <li><a href="#">ダッシュボード</a></li>
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
                    //echo "!emptyの条件を満たしています．<br>";
                }else{
                    //echo "emptyの条件を満たしていません。<br>";
                }
                // $_SESSION['conditions']が設定されているかどうかを確認します
                if (isset($_SESSION['conditions']) && !empty($_SESSION['conditions'])) {
                    //echo '$_SESSION["conditions"]が設定されています．<br>';
                    // ここに$_SESSION['conditions']を使用するコードを追加します
                } else {
                    //echo '$_SESSION["conditions"]は設定されていません．<br>';
                }
                $_SESSION['sql'] = $sql;
                //echo $_SESSION['sql'];
                
                

                // SQL実行  
                $result = mysqli_query($conn, $_SESSION['sql']);

                // 各学習者ごとの正解率を計算
                $user_accuracy = array();
                while ($row = mysqli_fetch_assoc($result)) {
                    if (!array_key_exists($row['UID'], $user_accuracy)) {
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
                $json_data = json_encode($user_accuracy, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                // JSONデータをファイルに保存
                $file_path = 'json/file.json'; // JSONファイルを保存するパス
                $json_keep_result = file_put_contents($file_path, $json_data);
                // 結果を確認
                if ($json_keep_result === false) {
                    echo "エラーが発生しました。ファイルに書き込めませんでした。";
                } else {
                    //echo "ファイルに書き込みが成功しました。<br>";
                }
                $command = "py .\graph_plot.py";
                $output = shell_exec($command);



                //難易度ごとの正解数記録
                $sql_dif_TF = "SELECT UID,linedata.WID,TF,Understand,question_info.level,question_info.grammar,question_info.Sentence FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID";
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
                        $stu_TF[$UID] = ['correct' => 0, 'incorrect' => 0];
                    }
        
                    foreach ($grammarItems as $value) {
                        if (!isset($grammarCount[$value])) {
                            // キーが存在しない場合、新しい配列を作成
                            $grammarCount[$value] = ['total' => 0, 'correct' => 0, 'incorrect' => 0, 'accuracy' => 0];
                        }
                        $grammarCount[$value]['total']++;
                        $countgrammar[$value]++;
                        if ($row["TF"] == 1) {
                            $grammarCountTrue[$value]++;
                            $grammarCount[$value]['correct']++;
                        } else {
                            $grammarCountFalse[$value]++;
                            $grammarCount[$value]['incorrect']++;
                        }
                        if ($row['Understand'] == 2) $grammarCounthesitateT[$value]++;
                        if ($row['Understand'] == 4) $grammarCounthesitateF[$value]++;
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

                foreach($grammarCount as $key => $value){
                    $grammarCount[$key]['accuracy'] = round($value['correct'] / $value['total'] * 100,2);
                }
                foreach($grammarCount as $key => $value){
                    //echo "key = $key, total = $value[total], correct = $value[correct], incorrect = $value[incorrect], accuracy = $value[accuracy]<br>";
                }
                // 正解率の低い順（文法項目）に並べ替えるための配列を作成
                $grammar_TF_with_grammar = [];
                foreach ($grammarCount as $grammar => $counts) {
                    $grammar_TF_with_grammar[] = ['grammar' => $grammar] + $counts;
                }

                // accuracyの昇順でソート
                usort($grammar_TF_with_grammar, function($a, $b) {
                    if($a['accuracy'] == $b['accuracy']) {
                        return 0;
                    }
                    return ($a['accuracy'] < $b['accuracy']) ? -1 : 1;
                });

                // 下位5件を抽出
                $lowest_accuracy_grammar = array_slice($grammar_TF_with_grammar, 0, 5);

                $key_label_map = [
                    -1 => "その他",
                    1 => "仮定法，命令法",
                    2 => "It,There",
                    3 => "無生物主語",
                    4 => "接続詞",
                    5 => "倒置",
                    6 => "関係詞",
                    7 => "間接話法",
                    8 => "前置詞",
                    9 => "分詞",
                    10 => "動名詞",
                    11 => "不定詞",
                    12 => "受動態",
                    13 => "助動詞",
                    14 => "比較",
                    15 => "否定",
                    16 => "後置修飾",
                    17 => "完了形，時制",
                    18 => "句動詞",
                    19 => "挿入",
                    20 => "使役",
                    21 => "補語/二重目的語",
                    22 => "不明",
                ];

                // デバッグ用: 抽出した下位5件を出力
                foreach ($lowest_accuracy_grammar as $key => $value) {
                    $grammar_label = isset($key_label_map[$value['grammar']]) ? $key_label_map[$value['grammar']] : 'Unknown';
                    //echo "key = $key, grammar = $grammar_label, total = {$value['total']}, correct = {$value['correct']}, incorrect = {$value['incorrect']}, accuracy = {$value['accuracy']}<br>";
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
                    //echo "エラーが発生しました。ファイルに書き込めませんでした。";
                } else {
                    //echo "ファイルに書き込みが成功しました。<br>";
                }
                $studentAccuracyJson = json_encode($stu_TF);
                
            ?>
            <script>
                let chartInstance = null;
                document.addEventListener('DOMContentLoaded', function() {
                    fetchStudentslist();
                })

                function fetchStudentslist() {
                    fetch('fetch_students_list_only.php')
                        .then(response => response.json())
                        .then(data => {
                            const students = data.students;
                            const selectElement = document.getElementById('learner-list');
                            const selectElementQues = document.getElementById('ques-list');
                            students.forEach(student => {
                                const option = document.createElement('option');
                                option.value = student.UID;
                                option.textContent = student.UID;
                                selectElement.appendChild(option);
                            });
                            // 学習者が選択されたときのイベントリスナーを追加
                            selectElement.addEventListener('change', function() {
                                if (selectElement.value) {
                                    console.log("学習者が選択されました。");
                                    const selectedUID = selectElement.value;
                                    console.log(selectedUID);
                                    fetchQueslist(selectedUID);
                                    displayLearnerDetails(selectedUID); // 選択された学習者を表示
                                    //これに加え，学習者が解答した問題数をカウントするものと，正答率を表示するものを追加

                                }
                            });
                            //問題が選択されたときのイベントリスナーを追加
                            selectElementQues.addEventListener('change', function() {
                                if (selectElementQues.value) {
                                    console.log("問題が選択されました。");
                                    const selectElement1 = document.getElementById('learner-list');
                                    const selectedUID = selectElement.value;
                                    const selectedWID = selectElementQues.value;
                                    console.log(selectedUID);
                                    console.log(selectedWID);
                                    displayQuesDetails(selectedWID);
                                    fetchQuesinfo(selectedWID, selectedUID);
                                }
                            });
                        })
                        .catch(error => console.error(error));
                }

                function fetchQueslist(selectedUID) {
                    fetch('fetch_ques_list.php?uid=' + selectedUID)
                    .then(response => response.json())
                    .then(data => {
                        const answers = data.answers;
                        const ques_count = data.datacount;
                        const accuracy = data.accuracy;
                        const grammarinfo = data.grammarinfo;


                        const answerListElement = document.getElementById('ques-list');
                        answerListElement.innerHTML = ''; // 既存の内容をクリア
                        answers.forEach(answer => {
                            const option = document.createElement('option');
                            option.value = answer.WID;
                            option.textContent = answer.WID;
                            if(answer.TF == '1'){
                                option.textContent = option.textContent + ' : ' + '〇';
                            }else{
                                option.textContent = option.textContent + ' : ' + '×';
                            }
                            option.textContent = option.textContent + ' : ' + answer.Sentence
                            const grammarItems = Object.values(answer.grammarJapanese).join(', ');
                            option.textContent = option.textContent + ' : ' + grammarItems
                            answerListElement.appendChild(option);
                        });
                        displayQuescount(ques_count);
                        displayAccuracy(accuracy);
                        missGrammarElement(grammarinfo);
                    })
                    .catch(error => console.error(error));
                }
                function displayLearnerDetails(selectedUID) {
                    const studentname = document.getElementById('student-name');
                    studentname.innerHTML = `選択された学習者は: <span class="text-highlight">${selectedUID}</span> です。`;
                }
                function displayQuescount(ques_count){
                    const student_ques_count = document.getElementById('student-ques-count');
                    student_ques_count.textContent = `■問題数: ${ques_count} 件`
                }
                function displayAccuracy(accuracy){
                    const student_accuracy = document.getElementById('student-accuracy');
                    student_accuracy.textContent = `■正解率: ${accuracy} %`
                }
                function missGrammarElement(grammarinfo){
                    const miss_grammar = document.querySelector('#miss-grammar-table tbody');
                    miss_grammar.innerHTML = ''; // 既存の内容をクリア
                    // オブジェクトを配列に変換
                    const grammarArray = Object.keys(grammarinfo).map(key => {
                        return { grammar: key, ...grammarinfo[key] };
                    });
                    //console.log(grammarArray);
                    // grammaraccuracyで昇順に並べ替え
                    grammarArray.sort((a, b) => a.grammaraccuracy - b.grammaraccuracy);
                    //水平棒グラフ作成のために配列を関数に送る
                    createhorizonBarChart(grammarArray);

                    // 上位5件を取り出す
                    const top5 = grammarArray.slice(0, 5);
                    // 行を追加
                    top5.forEach((info, index) => {
                        const row = document.createElement('tr');
                        row.dataset.grammar = info.grammar; // データ属性として文法項目を保存

                        const cellRank = document.createElement('td');
                        cellRank.textContent = index + 1;
                        row.appendChild(cellRank);

                        const cellGrammar = document.createElement('td');
                        cellGrammar.textContent = info.grammarjapanese;
                        row.appendChild(cellGrammar);

                        const cellAccuracy = document.createElement('td');
                        cellAccuracy.textContent = `${info.grammaraccuracy}%`;
                        row.appendChild(cellAccuracy);

                        row.addEventListener('click', function() {
                            updateSelectQues(this.dataset.grammar); // クリック時に文法項目を引数として関数を実行
                            //console.log(this.dataset.grammar);
                        });

                        miss_grammar.appendChild(row);
                    });
                }

                function updateSelectQues(selectedGrammar) {
                    const selectElement = document.getElementById('learner-list');
                    const selectedUID = selectElement.value;
                    
                    const select = document.getElementById('ques-list');
                    select.innerHTML = ''; // 既存の内容をクリア

                    
                    fetch('fetch_ques_list.php?uid=' + selectedUID + '&grammar=' + selectedGrammar)
                    .then(response => response.json())
                    .then(data => {
                        const answers = data.answers;
                        const ques_count = data.datacount;
                        const accuracy = data.accuracy;
                        const grammarinfo = data.grammarinfo;
                        const grammarnumber = data.grammarnumber;
                        console.log(grammarnumber);

                        const answerListElement = document.getElementById('ques-list');
                        answerListElement.innerHTML = ''; // 既存の内容をクリア
                        answers.forEach(answer => {
                            const option = document.createElement('option');
                            option.value = answer.WID;
                            option.textContent = answer.WID
                            if(answer.TF == '1'){
                                option.textContent = option.textContent + ' : ' + '〇';
                            }else{
                                option.textContent = option.textContent + ' : ' + '×';
                            }
                            option.textContent = option.textContent + ' : ' + answer.Sentence
                            const grammarItems = Object.values(answer.grammarJapanese).join(', ');
                            option.textContent = option.textContent + ' : ' + grammarItems
                            answerListElement.appendChild(option);
                        });
                        displayQuescount(ques_count);
                        displayAccuracy(accuracy);
                        missGrammarElement(grammarinfo);
                    })
                    .catch(error => console.error(error));
                    
                }
                
                function fetchQuesinfo(selectedWID, selectedUID) {
                    fetch('fetch_ques_info.php?wid=' + selectedWID + '&uid=' + selectedUID)
                    .then(response => response.json())
                    .then(data => {
                        const quesinfo = data.quesinfo;
                        console.log(quesinfo);
                        //const questionname = document.getElementById('ques-name');
                        displayQuesSentence(quesinfo);
                        displayQuesgrammar(quesinfo[0].grammarJapanese);
                        displayQueslevel(quesinfo[0].level);
                        //displayQuesEndsentence(quesinfo[0].EndSentence);
                        displayQuesTF(quesinfo[0].TF);
                        displayQueswordnum(quesinfo[0].wordnum);
                        console.log(quesinfo[0].hesitate2);
                        displayQueshesitateWord(quesinfo[0].hesitate2);
                        relativeSentence(quesinfo[0].Sentence, quesinfo[0].EndSentence);
                    })
                    .catch(error => console.error(error));
                }
                function createhorizonBarChart(grammarArray) {
                    const ctx = document.getElementById('stu-accuracy-grammar').getContext('2d');
                    const labels = grammarArray.map(item => item.grammarjapanese);
                    const data = grammarArray.map(item => item.grammaraccuracy);
                    //console.log("labels: " + labels);
                    //console.log("data: " + data);
                    // 既存のチャートがある場合は破棄する
                    if (chartInstance) {
                        chartInstance.destroy();
                    }
                    chartInstance = new Chart(ctx, {
                        type:'bar',  //水平棒グラフ
                        data:{
                            labels: labels,
                            datasets: [{
                                label: '文法項目別正解率',
                                data: data,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)', // 同じ色
                                borderColor: 'rgba(75, 192, 192, 1)', // 同じ色
                                borderWidth: 1
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            scales: {
                                x:{
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
                function displayQuesDetails(selectedWID) {
                    const questionname = document.getElementById('ques-name');
                    questionname.innerHTML = `選択された問題は: <span class="text-highlight">${selectedWID}</span> です。`;
                }
                function displayQuesSentence(quesinfo){
                    const question_sentence = document.getElementById('ques-sentence');
                    //console.log(quesinfo);
                    question_sentence.innerHTML = `■日本語文: ${quesinfo[0].Japanese}<br>■問題文: ${quesinfo[0].Sentence}`;
                }
                function displayQuesgrammar(grammarJapanese){
                    const question_grammar = document.getElementById('ques-grammar');
                    console.log(typeof(grammarJapanese));
                    // オブジェクトの値を取得してカンマ区切りの文字列に変換
                    const grammarItems = Object.values(grammarJapanese).join(', ');

                    // カンマ区切りの文字列を表示
                    question_grammar.textContent = `■文法項目: ${grammarItems}`;
                }
                function relativeSentence(Sentence, EndSentence){
                    const sentenceWords = Sentence.split(' ');
                    const endSentenceWords = EndSentence.split(' ');

                    //比較結果を格納する配列
                    const highlightSenteneceWords = [];
                    const highlightEndSentenceWords = [];
                    //長さ取得
                    const maxlength = Math.max(sentenceWords.length, endSentenceWords.length);
                    for(let i = 0; i < maxlength; i++){
                        if(sentenceWords[i] !== endSentenceWords[i]){
                            highlightEndSentenceWords.push(`<span class="text-highlight">${endSentenceWords[i] || ''}</span>`);
                        }else{
                            highlightEndSentenceWords.push(endSentenceWords[i] || '');
                        }
                    }

                    document.getElementById('ques-endsentence').innerHTML = `■最終解答文: ${highlightEndSentenceWords.join(' ')}`;
                }
                function displayQueslevel(level){
                    const question_level = document.getElementById('ques-level');
                    if(level == 1){
                        level = "初級";
                    }else if(level == 2){
                        level = "中級";
                    }else if(level == 3){
                        level = "上級";
                    }
                    question_level.textContent = `■難易度: ${level}`;
                }
                function displayQueswordnum(wordnum){
                    const question_wordnum = document.getElementById('ques-wordnum');
                    question_wordnum.textContent = `■単語数: ${wordnum}`;
                }
                function displayQuesEndsentence(EndSentence){
                    const question_endsentence = document.getElementById('ques-endsentence');
                    question_endsentence.textContent = `■最終解答文: ${EndSentence}`;
                }
                function displayQuesTF(TF){
                    const question_TF = document.getElementById('ques-TF');
                    if(TF == 1){
                        TF = "○";
                    }else if(TF == 0){
                        TF = "×";
                    }
                    question_TF.textContent = `■正解: ${TF}`;
                    
                }
                function displayQueshesitateWord(hesitate2Word){
                    console.log(hesitate2Word);
                    const question_hesitateWord = document.getElementById('ques-hesitateword');
                    if(hesitate2Word == ""){
                        question_hesitateWord.textContent = `■迷いの可能性のある単語: なし`;
                    }else{
                        question_hesitateWord.textContent = `■迷いの可能性のある単語: ${hesitate2Word}`;
                    }
                }

                

            </script>
            <!--
            <div class = "search" align = "center">
                <h2 onclick="openFilterModal()">検索フィルタ</h2>
            </div>
            -->
            <section class="overview">
                <div align ="center">
                    <h2>クラス全体の概要</h2>
                    <div class = "overview-contents">
                    <div class="average-study-time" >
                        <!--フォントサイズを5にする-->
                        <font size = "5">
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
                        </font>
                    </div>
                    <div class="average-accuracy">
                        <!--フォントサイズを5にする-->
                        <font size = "5">
                        <h3>■正解率
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
                        </font>
                    </div>
                    </div>
                </div>
                <font size = "5">
                    <div class="overview-contents">
                        
                    </div>
                </font>
                    <div class = "overview-contents">
                        <div align ="center" id = "accuracy-chart">
                            <h3>正解率分布</h3>
                            <div class = "img-chart">
                                <canvas id="accuracy-histogram" width="500" height="500"></canvas>
                            </div>
                        </div>
                        <div id = "miss-ques">
                            <h3>正解率が低い文法項目</h3>
                            <table border="1" id="tablemiss-ques" class = "table5">
                                <thead>
                                    <tr>
                                        <th>順位</th>
                                        <th>文法項目</th>
                                        <th>正解率</th>
                                    </tr>
                                <thead>
                                <tbody>
                                    <!-- ここに検索結果で誤答率が高いものを表示 -->
                                    <?php foreach ($lowest_accuracy_grammar as $index => $counts) { 
                                        $grammar_label = isset($key_label_map[$counts['grammar']]) ? $key_label_map[$counts['grammar']] : 'Unknown';
                                    ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($grammar_label); ?></td>
                                            <td><?php echo htmlspecialchars($counts['accuracy']); ?>%</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div id = "hesitate-grammar">
                            <h3>正解率が低い問題</h3>
                            <table border="1" id="tablemiss-ques" class = "table5">
                                <thead>
                                    <tr>
                                        <th>順位</th>
                                        <th>WID</th>
                                        <th>問題</th>
                                        <th>正解率</th>
                                    </tr>
                                <thead>
                                <tbody>
                                    <!-- ここに検索結果で誤答率が高いものを表示 -->
                                    <?php foreach ($lowest_accuracy_questions as $index => $counts) { ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($counts['WID']); ?></td>
                                            <td><?php echo htmlspecialchars($counts['Sentence']); ?></td>
                                            <td><?php echo htmlspecialchars($counts['accuracy']); ?>%</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class = "overview-contents">
                        <!--<h1>ここはサンプル置き場</h1>-->
                    </div>
                
            </section>

            <section class="progress-chart">
                <div class = "head-content">
                    <div class = "title">
                        <h2>学習者分析</h2>
                    </div>
                    <div class="select-container" id = "select-learner">
                        <select id="learner-list">
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
                      document.addEventListener('DOMContentLoaded', function() {
                            // PHPからJSONデータを受け取る
                            var studentAccuracy = <?php echo $studentAccuracyJson; ?>;
                            
                            // ヒストグラム用にデータを集計
                            var accuracyValues = Object.values(studentAccuracy).map(function(student) {
                                return student.accuracy;
                            });

                            // ヒストグラム用にデータのバケットを作成
                            var buckets = Array(10).fill(0); // 0-10, 10-20, ..., 90-100
                            accuracyValues.forEach(function(accuracy) {
                                var bucketIndex = Math.floor(accuracy / 10);
                                if (bucketIndex === 10) bucketIndex = 9; // 100%のケース
                                buckets[bucketIndex]++;
                            });

                            var ctx = document.getElementById('accuracy-histogram').getContext('2d');
                            var histogramChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: ['0-10%', '10-20%', '20-30%', '30-40%', '40-50%', '50-60%', '60-70%', '70-80%', '80-90%', '90-100%'],
                                    datasets: [{
                                        label: '学習者の正答率分布',
                                        data: buckets,
                                        backgroundColor: 'rgba(0, 0, 255, 0.8)',
                                        borderColor: 'rgba(0, 0, 255, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    maintainAspectRatio: false,  // これを追加
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        },
                                        x: {
                                            title: {
                                                display: true,
                                                text: '正答率の範囲'
                                            }
                                        }
                                    }
                                }
                            });
                        });
                    /*
                    document.addEventListener('DOMContentLoaded', function() {
                        //PHPのデータをパース
                        var studentAccuracy = <?php //echo $studentAccuracyJson; ?>;
                        //データをChart.js用に変換
                        var labels = [];
                        var data = [];

                        for (var UID in studentAccuracy) {
                            labels.push(UID);
                            data.push(studentAccuracy[UID].accuracy);
                        }
                        var avg = <?php //echo $aveaccuracy; ?>;
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
                                },
                                plugins: {
                                    averageLine: true // カスタムプラグインを有効にする
                                }
                            },
                            plugins: [averageLinePlugin]
                            
                        });

                    })
                    */
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
                    <form action="teachertrue.php" method="post">
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
            <!--いったんここメインページにはいらない-->
            <!--
            <section class="individual-details">
                <h2>個別学習者の詳細</h2>
                <div class="student-list-details-container">
                    <div class="student-list">
                        <h3>学習者リスト</h3>
                        <form action="teachertrue.php" method="post" id = "stu_form">
                            <select name="studentlist" id="studentlist" size="10">
                                <?php
                                    /*
                                    // データベースから学習者リストを取得
                                    $StudentAll_SQL = "SELECT uid,Name FROM member";
                                    if(isset($_POST["UIDrange"]) && $_POST["UIDrange"] == "not"){
                                        $StudentAll_SQL .= " WHERE uid NOT IN (".$_POST["UIDsearch"].")";
                                    }else if(isset($_POST["UIDrange"]) && $_POST["UIDrange"] == "include"){ 
                                        $StudentAll_SQL .= " WHERE uid IN (".$_POST["UIDsearch"].")";
                                    }

                                    $StudentAll_Result = mysqli_query($conn, $StudentAll_SQL);
                                    while($row = mysqli_fetch_assoc($StudentAll_Result)){
                                        echo '<option value="'.$row["uid"].'">'.$row["Name"].'</option>';
                                    }
                                    */

                                ?>
                            </select>
                            <input type="button" value="表示" id = "stu_info">
                        </form>
                    </div>
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

                                fetch('fetch_student_details.php', {
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
                            const centerTextPlugin = {
                                id: 'centerText',
                                afterDraw: function(chart) {
                                    var correctCount = chart.config.data.datasets[0].data[0];
                                    var totalCount = chart.config.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    var correctPercentage = (correctCount * 100 / totalCount).toFixed(2);

                                    var width = chart.width,
                                        height = chart.height,
                                        ctx = chart.ctx;

                                    ctx.save(); // 状態を保存

                                    var fontSize = (height / 350).toFixed(2);
                                    ctx.font = fontSize + "em sans-serif";
                                    ctx.textBaseline = "middle";

                                    var text = "正解率 " + correctPercentage + "%",
                                        textX = Math.round((width - ctx.measureText(text).width) / 2),
                                        textY = height / 2;
                                    ctx.fillText(text, textX, textY);

                                    ctx.restore(); // 状態を復元
                                }
                            };

                            Chart.register(centerTextPlugin);

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
                    
                    <div class="student-details">
                        <div align="center">
                            <h3>
                                学習者詳細
                                <div id = "studentName"></div>
                            </h3>
                        </div>
                        <div class="student-progress">
                            <div id = "student-solve-ques" class = "content">

                            </div>
                            <div class = "content">
                                <div class = "hide" id = "student-accuracy">
                                <h4>正解率</h4>
                                <canvas id="Stu-Accuracy" class="doughnutgraph"></canvas>
                                </div>
                            </div>
                            <div class = "content">
                                <div class = "hide" id = "studentQuesTime">
                                    <h4>平均解答時間<br> <span id="quesTimeValue" class="highlight"></span></h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="student-weak-areas">
                            <h4>苦手分野</h4>
                            グラフ表示 
                        </div>
                                
                    </div>
                </div>
            </section>
                    -->

        </main>
    </div>
</body>
</html>
