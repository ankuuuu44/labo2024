<?php
if(!isset($_SESSION)){ session_start(); }
?>

<?php

    require "dbc.php";
    if(isset($_GET['user_id'])) {
        // ユーザーIDを取得
        $userId = $_GET['user_id'];
    
        // データベースからユーザーデータを取得するクエリを実行
        $sql = "SELECT * FROM linedata WHERE UID =".$userId;
        $result = $conn-> query($sql);
        $countT = 0;    //Tの数をcount 
        $countF = 0;    //Fの数をcount
        // ここでデータベースから必要なデータを取得する処理を行う

        while($row = $result -> fetch_assoc()){
            //ここからlinedata内の値の処理

            //正誤判定
            if ($row["TF"] === '1'){
                $tfcon = '正解';
                $countT+=1;
            }else{
                $tfcon = '不正解';
                $countF+=1;
            }
            //迷い度判定
            if($row["Understand"] === '1'){
                $Understandcon = '誤って決定ボタンを押した';
            }else if($row["Understand"] === '2'){
                $Understandcon = 'かなり迷った';
            }else if($row["Understand"] === '3'){
                $Understandcon = '少し迷った';
            }else if($row["Understand"] === '4'){
                $Understandcon = 'ほとんど迷わなかった';
            }
            $strWID = "解答した問題:" . $row["WID"];
            $strTF = "正誤:". $tfcon;
            $strUnderstand = "迷い度:" . $Understandcon;


            //ここからWIDとquestion_infoの処理
            // レスポンス配列にデータを追加
            /*
            $response[] = array(
            'WID' => $strWID,
            'TF' => $strTF,
            'Understand' => $strUnderstand
            );
            */

        }

        //countTとcountFの連想配列作成
        $response[] = array();
        $response['countT'] = $countT;
        $response['countF'] = $countF;

        echo json_encode($response);
    
        // 仮のデータを返す例
        $userData = "ユーザーID: $userId のデータを取得しました。";
        
        
    
        // 結果を返す
        //echo $userData;
        
    } else {

        echo "ユーザーIDが指定されていません。";
    }
    ?>



