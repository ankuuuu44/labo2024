<?php
    require "dbc.php";
    $wid = $_GET['wid'];

    $sql = "SELECT linedata.EndSentence,linedata.hesitate1,linedata.hesitate2, linedata.Time, linedata.TF,linedata.Understand,
            question_info.WID,question_info.divide, question_info.level,question_info.Japanese, question_info.Sentence,question_info.grammar,question_info.wordnum 
            FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID 
            WHERE linedata.WID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $wid);
    $stmt->execute();
    $stmt->bind_result($EndSentence, $hesitate1, $hesitate2, $Time, $TF, $Understand, $WID, $divide, $level, $Japanese, $Sentence, $grammar, $wordnum);
    $quesinfo = [];
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
        //文法項目のマッピング
        $grammarItems = array_filter(explode("#", $grammar), function($value) {
            return $value !== '';
        });
        $mappedGrammarItems = array_map(function($item) use ($key_label_map) {
            return isset($key_label_map[$item]) ? $key_label_map[$item] : "不明";
        }, $grammarItems);

        //問題ごとの情報を取得
        $quesinfo[] = array(
            'EndSentence' => $EndSentence,
            'hesitate1' => $hesitate1,
            'hesitate2' => $hesitate2,
            'Time' => $Time,
            'TF' => $TF,
            'Understand' => $Understand,
            'WID' => $WID,
            'divide' => $divide,
            'level' => $level,
            'Japanese' => $Japanese,
            'Sentence' => $Sentence,
            'grammar' => $grammar,
            'wordnum' => $wordnum,
            'grammarJapanese' => $mappedGrammarItems
        );
        
    }

    

    $stmt->close();



    header('Content-Type: application/json');
    echo json_encode([
        'quesinfo' => $quesinfo
    ]);