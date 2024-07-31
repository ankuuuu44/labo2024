<?php
    require "dbc.php";
    $uid = $_GET['uid'];

    $sql = "SELECT linedata.TF,linedata.Understand,question_info.WID,question_info.Sentence,question_info.grammar FROM linedata 
            INNER JOIN question_info ON linedata.WID = question_info.WID 
            WHERE linedata.UID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $stmt->bind_result($TF,$Understand,$WID, $Sentence, $grammar);
    $answers = [];
    $datacount = 0; // $datacountを初期化
    $accuracy = 0;
    $countT = 0;
    $countF = 0;
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
    while ($stmt->fetch()) {
        //問題ごとの情報を取得
        $datacount += 1;
        $answers[] = [
            'WID' => $WID,
            'TF' => $TF,
            'Understand' => $Understand,
            'grammar' => $grammar,
            'Sentence' => $Sentence
        ];
        if($TF == 1){
            $countT += 1;
        }else{
            $countF += 1;
        }

        //文法項目ごとの情報を取得
        $grammarItems = array_filter(explode("#", $grammar), function($value) {
            return $value !== '';
        });
        foreach ($grammarItems as $value) {
            if (!isset($grammarCount[$value])) {
                // キーが存在しない場合、新しい配列を作成
                $grammarCount[$value] = ['total' => 0, 'correct' => 0, 'incorrect' => 0, 'grammaraccuracy' => 0,'hesitateT' => 0,'hesitateF' => 0];
            }
            $grammarCount[$value]['total']++;
            if ($TF == 1) {
                $grammarCount[$value]['correct']++;
            } else {
                $grammarCount[$value]['incorrect']++;
            }
            if ($Understand == 2) {
                $grammarCount[$value]['hesitateT']++;
            }else if($Understand == 4){
                $grammarCount[$value]['hesitateF']++;
            } 
        }
    }
    // 対象学習者の全体の正答率を計算
    $accuracy = round($countT / ($countT + $countF) * 100, 2);
    //　対象学習者の文法項目ごとの正答率を計算
    //文法項目に日本語をマッピング
    foreach ($grammarCount as $key => $value) {
        $grammarCount[$key]['grammaraccuracy'] = round($value['correct'] / $value['total'] * 100, 2);
        $grammarCount[$key]['grammarjapanese'] = isset($key_label_map[$key]) ? $key_label_map[$key] : "不明";
    }

    $stmt->close();



    header('Content-Type: application/json');
    echo json_encode([
        'answers' => $answers,
        'datacount' => $datacount,
        'accuracy' => $accuracy,
        'grammarinfo' => $grammarCount
    ]);