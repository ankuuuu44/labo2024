<?php
require "../../dbc.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentId = $_POST['studentlist'];

    // 解答問題一覧を取得
    $StudentsolveQues_SQL = "SELECT linedata.WID, linedata.TF, question_info.Sentence FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID WHERE linedata.UID = ?";
    $stmt = $conn->prepare($StudentsolveQues_SQL);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $response['questions'] = '';
    $correctCount = 0;
    $incorrectCount = 0;
    if ($result->num_rows > 0) {
        $response['questions'] .= '<h4>解答問題一覧</h4><form action="mousemove.php" method="post" target="_blank"><select name="datalist" id="stu-solve-ques" size="10">';
        while($row = $result->fetch_assoc()) {
            $optionvalue = $studentId . "," . $row["WID"];
            if ($row["TF"] == 1) {
                $correctCount++;
                $response['questions'] .= '<option value="'.$optionvalue.'">〇'.$row["Sentence"].'</option>';
            } else {
                $incorrectCount++;
                $response['questions'] .= '<option value="'.$optionvalue.'">×'.$row["Sentence"].'</option>';
            }
        }
        $response['questions'] .= '</select><input type="submit" value="軌跡再現"><input type="button" value="詳細分析"></form>';
    } else {
        $response['questions'] = '学習者は選択されていません。';
    }
    $stmt->close();

    $response['correctCount'] = $correctCount;
    $response['incorrectCount'] = $incorrectCount;
    $response['totalCount'] = $correctCount + $incorrectCount;

    // 学習者名を取得
    $Name_student_SQL = "SELECT Name FROM member WHERE UID = ?";
    $stmt = $conn->prepare($Name_student_SQL);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $Name_student_Result = $stmt->get_result();
    if($Name_student_Result->num_rows > 0){
        $response['studentName'] = mysqli_fetch_assoc($Name_student_Result)["Name"];
    } else {
        $response['studentName'] = "学習者は選択されていません。";
    }

    $stmt->close();
    //平均回答時間を取得
    $StudentQuesTime_SQL = "SELECT AVG(linedata.Time) FROM linedata WHERE linedata.UID = ?";
    $stmt = $conn->prepare($StudentQuesTime_SQL);
    $stmt->bind_param("s", $studentId);
    $stmt->execute();
    $StudentsolveQuesTime_Result = $stmt->get_result();
    if($StudentsolveQuesTime_Result->num_rows > 0){
        $row = mysqli_fetch_assoc($StudentsolveQuesTime_Result); 
        $response['studentQuesTime'] =  number_format($row["AVG(linedata.Time)"] / 1000,2) . "秒";
    } else {
        $response['studentQuesTime'] = "学習者は解答した問題はありません。";
    }
    $stmt->close();


    // grammarを追加するために配列を初期化
    $response["grammar"] = array();
    $StudentDetail_SQL = "SELECT UID, linedata.WID, TF, Understand, question_info.level, question_info.grammar 
                      FROM linedata 
                      INNER JOIN question_info ON linedata.WID = question_info.WID 
                      WHERE UID = ?";
    $stmt = $conn->prepare($StudentDetail_SQL);
    $stmt->bind_param("s", $_POST["studentlist"]);
    $stmt->execute();
    $StudentDetail_Result = $stmt->get_result();
    
    

    
    if($StudentDetail_Result->num_rows > 0){
        $level_true = [1 => 0, 2 => 0, 3 => 0];
        $level_false = [1 => 0, 2 => 0, 3 => 0];
        $stu_countgrammar = [];
        $stu_grammarCountTrue = [];
        $stu_grammarCountFalse = [];
        $stu_grammarCounthesitateT = [];
        $stu_grammarCounthesitateF = [];
        
        for ($i = -1; $i <= 22; $i++) {
            $stu_countgrammar[(string)$i] = 0;
            $stu_grammarCountTrue[(string)$i] = 0;
            $stu_grammarCountFalse[(string)$i] = 0;
            $stu_grammarCounthesitateT[(string)$i] = 0;
            $stu_grammarCounthesitateF[(string)$i] = 0;
        }
        

        
        while ($row = $StudentDetail_Result->fetch_assoc()) {
            $grammarItems = array_filter(explode("#", $row["grammar"]), function($value) {
                return $value !== '';
            });

            foreach ($grammarItems as $value) {
                $stu_countgrammar[$value]++;
                if ($row["TF"] == 1) {
                    $stu_grammarCountTrue[$value]++;
                } else {
                    $stu_grammarCountFalse[$value]++;
                }
                if ($row['Understand'] == 2) {
                    $stu_grammarCounthesitateT[$value]++;
                }else if ($row['Understand'] == 4){
                    $stu_grammarCounthesitateF[$value]++;
                }
            }
            
            $level = $row["level"];
            if ($row["TF"] == 1) {
                $level_true[$level]++;
            } else {
                $level_false[$level]++;
            }
            
        }
        
    }
    $stmt->close();
    // 正解率を計算
    $response['Stu_accuracy_dif1'] = calculateAccuracy($level_true[1], $level_false[1]);
    $response['Stu_accuracy_dif2'] = calculateAccuracy($level_true[2], $level_false[2]);
    $response['Stu_accuracy_dif3'] = calculateAccuracy($level_true[3], $level_false[3]);

    for ($i = -1; $i <= 22; $i++) {
        if ($stu_countgrammar[$i] != 0) {
            $response['stu_accuracy_grammar'][$i] = round($stu_grammarCountTrue[$i] / $stu_countgrammar[$i] * 100, 2);
            $response['stu_hesitate_grammar'][$i] = round($stu_grammarCounthesitateT[$i] / $stu_countgrammar[$i] * 100, 2);
        } else {
            $response['stu_accuracy_grammar'][$i] = 0;
            $response['stu_hesitate_grammar'][$i] = 0;
        }
    }

    $json_data_allacu_stu = json_encode($response['stu_accuracy_grammar'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $json_data_allhesi_stu = json_encode($response['stu_hesitate_grammar'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    // JSONデータをファイルに保存
    $file_path_allacu_stu = 'json/file_all_acu_stu.json'; // JSONファイルを保存するパス
    $file_path_allhesi_stu = 'json/file_all_hesi_stu.json'; // JSONファイルを保存するパス
    $json_keep_result_allacu_stu = file_put_contents($file_path_allacu_stu, $json_data_allacu_stu);
    $json_keep_result_allhesi_stu = file_put_contents($file_path_allhesi_stu, $json_data_allhesi_stu);

    Makegraph();
    //生成された画像のパス
    $imagePath = "images/comparison_grammar_accuracy.jpg";
    $response["image_path"] = $imagePath;


    header('Content-Type: application/json');
    echo json_encode($response);
}

function calculateAccuracy($trueCount, $falseCount) {
    $total = $trueCount + $falseCount;
    return $total > 0 ? round($trueCount / $total * 100, 2) : 0;
}

function Makegraph(){
    $command = "py .\graph_python\horizonBarChart.py";
    $output = shell_exec($command);
}
