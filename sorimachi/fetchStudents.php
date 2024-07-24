<?php
    session_start();
    require"dbc.php";
    error_reporting(0);

    $grammarinanswer = "SELECT linedata.WID,TF,Understand,grammar,question_info.level,question_info.Sentence FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID WHERE linedata.UID = ".$id;
    $res_grammarinanswer = mysqli_query($conn,$grammarinanswer) or die ("文法項目抽出エラー");
    while($row = $res_grammarinanswer -> fetch_assoc()){
        $grammarstr = explode("#", $row["grammar"]);
        $Allques[] = $row['WID'];
        $AllquesSentence[] = $row['Sentence'];
        if($row['TF'] === '1'){
            $countT_user += 1;
            $Trueques[] = $row['WID'];
            if($row['Understand'] =='4'){
                $countT_hesitateF += 1;
                $TruehesitateF[] = $row['WID'];
            }elseif($row['Understand'] == '2'){
                $countT_hesitateT += 1;
                $TruehesitateT[] = $row['WID'];
            }else{
                $countT_other += 1;
                $Trueothere[] =$row['WID'];
            }
        }else{
            $countF_user += 1;
            $Falseques[] = $row['WID'];
            if(isset($row['Understand'])){
                if($row['Understand' == '4']){
                    $countF_hesitateF += 1;
                    $FalsehesitateF[] = $row['WID'];
                }elseif($row['Understand'] == '2'){
                    $countF_hesitateT += 1;
                    $FalsehesitateT = $row['WID'];
                }else{
                    $countF_other += 1;
                    $Falseother[] = $row['WID'];
                }
            }
        }
    
        if($row['level'] == '1'){
            $level1count += 1;
        }elseif($row['level'] == '2'){
            $level2count += 1;
        }elseif($row['level'] == '3'){
            $level3count += 1;
        }
    
    
    
        foreach($grammarstr as $value){
            if(array_key_exists($value,$countgrammar)){
                $countgrammar[$value] += 1;
            }
            
            //各文法項目の正解，不正解数を入れる．
            if(array_key_exists($value,$grammarCountTrue) && $row['TF'] == 1){
                $grammarCountTrue[$value] += 1;
            
            }else if(array_key_exists($value,$grammarCountFalse) && $row['TF'] == 0){
                $grammarCountFalse[$value] += 1;
            }
    
            if (array_key_exists($value, $grammarCounthesitateT) && $row['Understand'] == 2){
                $grammarCounthesitateT[$value] += 1;
            }else if (array_key_exists($value, $grammarCounthesitateF) && $row['Understand'] == 4){
                $grammarCounthesitateF[$value] += 1;
            }
        }
    
    
        
        #echo "<br>";
        
    }