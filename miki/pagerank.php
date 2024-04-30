<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
/*
if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
	require"notlogin.html";
	session_destroy();
	exit;
}
*/
/*
if($_POST["mark"] != $_SESSION["mark"]){//正誤表示、部分点表示の場合分け処理
    if(isset($_POST["mark"])){
        $_SESSION["mark"] = $_POST["mark"];
    }
}
*/
$_POST["correl"] =$_REQUEST["correl"];

?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>pagerankプログラム</title>
</head>


<body>
    <?php
      $b = array(1,0,0,0,0,0,0);//初期ベクトル（適当）


      $Rink_array =array();
      $Rink_array[0] =array(0,1,1,1,1,0,1);
      $Rink_array[1] =array(1,0,0,0,0,0,0);
      $Rink_array[2] =array(1,1,0,0,0,0,0);
      $Rink_array[3] =array(0,1,1,0,1,0,0);
      $Rink_array[4] =array(1,0,1,1,0,1,0);
      $Rink_array[5] =array(1,0,0,0,1,0,0);
      $Rink_array[6] =array(0,0,0,0,1,0,0);
      $count = 0;
      

      for($i=0;$i<=6;$i++){
          for($j=0;$j<=6;$j++){
              if($Rink_array[$i][$j] ==1){
                $count++;  
              }
          }
          for($j=0;$j<=6;$j++){
              if($Rink_array[$i][$j] ==1){
                $Rink_array[$i][$j] = $Rink_array[$i][$j]/$count;
              }
          }
          $count = 0;
      }

      for ($k=0;$k<=100;$k++){
        for($i=0;$i<=6;$i++){
            for($j=0;$j<=6;$j++){
                  $c[$i] = $c[$i] + $Rink_array[$i][$j] * $b[$j];
              }
              echo $c[$i]."<br>";
         }
         echo "<br>";
         $b = $c;
         $c =array();
      }
      //echo $Rink_array[4][2];
      
    ?>
</body>
</html>