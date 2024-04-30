<?php
    session_start();
    if(isset($_POST["part"])){
        header('Location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/create/create_part.php') ;
    }else if(isset($_POST["all"])){
        header('Location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/create/part_all.php') ;
    }else if(isset($_POST["another"])){
        header('Location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/create/part_another.php') ;
    }
?>