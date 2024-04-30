<?php
    session_start();
    if(isset($_POST["part"])){
        header('Location: ./create_part.php') ;
    }else if(isset($_POST["all"])){
        header('Location: ./part_all.php') ;
    }else if(isset($_POST["another"])){
        header('Location: ./part_another.php') ;
    }
?>