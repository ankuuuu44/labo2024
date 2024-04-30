<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();
require"dbc.php";
//解答系文のの参照
if($_GET['param1']=="start"){
    //$divide = $_SESSION["divide2"];
    $divide = $_SESSION["divide2"];
    echo $divide;
}else if($_GET['param1']=="japanese"){
    echo $_SESSION["Japanese"];
}
?>