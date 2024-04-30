<?php
if(!isset($_SESSION)){ session_start(); }
?>
<?php

    require "dbc.php";
    //ユーザ情報クエリ
    $sqlques = "SELECT * FROM question_info WHERE WID in (SELECT WID FROM linedata)";
    $resultques = $conn-> query($sqlques);
    
    if($result === false){
        echo "question_infoに登録されている問題がありません　又は解答されている問題がありません";
    }
    

    
?>