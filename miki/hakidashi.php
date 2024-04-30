<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>掃出し法</title>
</head>
<body>
    <?php
        $a = array();
        $a[0] =array(2,1,1);
        $a[1] =array(3,2,1);
        $a[2] =array(1,2,3);

        $b = array(5,8,10);
        
        /*
        2x + 1y + 1z = 5
        3x + 2y + 1z = 8
        1x + 2y + 3z = 10
        の連立方程式を解く
        */
        $n = count($b);//変数の数

        for($k=1;$k<=$n-1;$k++){
            $w =1/$a[$k][$k];
            for($i=$k+1;$i<=$n;$i++){
                $m = $a[$i][$k]*$w;
                $for($j=1;$j<=$n;$j++){
                    $a[$i][$j] -= $m*$a[$k][$j];
                }
                $b[$i] -= $m*$b[$k];
            }
            
        }

        $x[$n] =$b[$n]/$a[$n][$n];
        for($k =$n-1;$k>=1;$k--){
            $s =0;
            for($j=$k+1;$j<=$n;$j++){
                $s += $a[$k][$j]*$x[$j];
            }
            $x[$k] =($b[$k]-$s)-$a[$k][$k];
        }

        for($i=1;$i<=$n;$i++){
            echo $x[$i]."<br>";
        }
        ?>
</body>
</html>