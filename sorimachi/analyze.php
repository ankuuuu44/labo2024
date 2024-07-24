<?php 
    $datalist = isset($_POST['datalist']) ? $_POST['datalist'] : '';
    $ID = array();
    $ID = explode(",",$datalist);
    //各データを変数に格納
    $uid = $ID[0];
    $wid = $ID[1];
    echo "uid = ".$uid." wid = ".$wid;

    //その問題の正答率を求めて，
?>