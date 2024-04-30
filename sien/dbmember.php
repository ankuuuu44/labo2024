<?php
if(!isset($_SESSION)){ session_start(); }
?>
<?php

    require "dbc.php";
    //ユーザ情報クエリ
    $sqlmember = "SELECT uid,Name FROM member";
    $result = $conn-> query($sqlmember);
    
    if($result === false){
        echo "memberに登録されているメンバーがいません";
    }

    
 
?>