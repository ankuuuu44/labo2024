<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>課題用</title>
</head>
<body>

<font size="3">
ニュートン・ラプソン法<br>
</font>
<?php
    $rad =M_PI/2;
    $rad2 = 0;
    do{
        $rad2 = $rad;//ループの前回の結果保存
        $f1 = $rad - cos($rad);
        $df1 = 1 + sin($rad);
        $rad = $rad - ($f1 / $df1);
        echo $rad."</br>";
    }while(abs($rad-$rad2)>0);
?>
</br>
<font size="3">
二分法<br>
<?php
    $b =M_PI/2;
    $a =0;
    $c=0;
    do{
        $c2 = $c;//ループの前回の結果保存
        $c = ($a+$b)/2;
        $f2 = $c -cos($c);
        if($f2 >0){
            $b =$c;
        }else{
            $a =$c;
        }
        echo $c."</br>";
    }while(abs($c-$c2) > 0);
?>
</font>
</br>

<font size="3">
はさみ打ち法<br>
</font>
<?php
    $b =M_PI/2;
    $a =0;
    $c = 0;
    do{
        $c2 = $c;//ループの前回の結果保存
        $fa = $a -cos($a);
        $fb = $b -cos($b);

        $c = ($a*$fb - $b*$fa)/($fb - $fa);
        $f3 = $c -cos($c);
        if($f3 >0){
            $b =$c;
        }else{
            $a =$c;
        }
        echo $c."</br>";
    }while(abs($c-$c2) > 0);
?>
</body>
</html>