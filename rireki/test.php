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
$_POST["correl"] ="student";
?>

<html>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
	<title>クラスタリング</title>
    <STYLE type="text/css">
<!--
.scr {
  overflow: scroll;   /* スクロール表示 */
  width: 300px;
  height: 500px;
  background-color: white;
  position: relative;
  top: 10px;
  left: 20px;
}
.dataset {
  overflow: scroll;   /* スクロール表示 */
  width: 1200px;
  height: 300px;
  background-color: white;
  position: absolute;
  top: 600px;
  left: 20px;
}
-->
</STYLE>
</head>

<body>
<?php
     
	function sum($array1){
		
		// 対象配列の抽出
		$target = $array1;
		
		// ここから合計値の計算
		$result = 0.0; // 合計値
		
		for ( $i=0; $i<count($target); $i++ ){
			$result += $target[$i];
		}
		
		return $result;	// 合計値を返して終了
		
	}
	
	// 平均値・期待値を求めるメソッド ave()
	function ave($array1){
		
		// 対象配列の抽出
		$target = $array1;
		
		// 平均値の計算　配列の合計値を算出して、要素数で割る
		$sum = sum($target);
		if ( count($target)>0 ){
			$result = $sum / count($target);
		}else{
			$result = 0;
		}
		
		return $result;	
		
	}
	
	// 分散を求めるメソッド varp()
	function varp($array1){
		
		// 対象配列の抽出
		$target = $array1;
		
		// 分散 E{X-(E(X))^2}　により求められる
		$ave = ave($target);
		
		$tmp; // 作業用変数
		
		// X-(E(X))^2 の値を入れておく配列
		$tmparray = array();
		
		// 配列の1要素ずつ、 (X-E(X))^2 を計算
		for ( $i=0; $i<count($target); $i++ ){
			$tmp = $target[$i] - $ave;		// X-E(X)
			$tmparray[$i] = $tmp * $tmp; 	// (X-E(X))^2
		}
		
		// 最後に、その平均値をもとめて終わり
		$result = ave($tmparray);
		
		return $result;
		
	}
	
	// 標準偏差を求めるメソッド sd()
	function sd($array1){
		// 対象配列の抽出
		$target = $array1;
		
		// 標準偏差は分散の平方根により求められる
		$varp = varp($target);	// 分散の算出
		$result = sqrt($varp);			// その平方根をとる
		
		return $result;
		
	}
	
	// 共分散を求めるメソッド cov()
	function cov($array1,$array2){
		
		// 対象配列の抽出
		$target1 = $array1;
		$target2 = $array2;
		
		// これは作業用変数
		$targetx = array();
		$targety = array();		
		$target = array();
		
		// 共分散 E[(X-E(X))(Y-E(Y))]により求められる

		// X-E(X)の算出
		$avex = ave($target1);
		for ($i=0; $i<count($target1); $i++ ){
			$targetx[$i] = $target1[$i] - $avex;
		}
		
		// Y-E(Y)の算出
		$avey = ave($target2);
		for ($i=0; $i<count($target2); $i++ ){
			$targety[$i] = $target2[$i] - $avey;
		}
		
		// (X-E(X))(Y-E(Y)) の算出
		for ($i=0; $i<count($target1) || $i<count($target2); $i++ ){
			$target[$i] = $targetx[$i] * $targety[$i];
		}
		
		// (X-E(X))(Y-E(Y)) の平均値をとって終了
		$result = ave($target);
		
		return $result;
	}
?>



<?php
   $x =array();
   $y =array();
   //$x =array(5,4,1,5,5);
   //$y =array(1,2,5,4,5); //テストデータ
   $x=array(11,9,5,3);
   $y=array(0,0,0,0);

   $combi_x =array();
   $combi_y =array();
   $ward = array();
$student_count = count($x);//クラスター数

for($i = 0; $i<$student_count; $i++){//配列をクラスターに入れる。
    $group_x[$i][0] =$x[$i];
    $group_y[$i][0] =$y[$i];
}

for($i = 0;$i<$student_count-1;$i++){
    for($j = $i+1; $j<$student_count;$j++){
       $combi_x = array();
       $combi_y = array();
        $combi_x = array_merge($group_x[$i],$group_x[$j]);//配列の結合
        $combi_y = array_merge($group_y[$i],$group_y[$j]);

    $center_x = ave($combi_x);
    $center_y = ave($combi_y);

$ward[$i][$j] = pow($group_x[$i][0] - $center_x,2) + pow($group_x[$j][0] - $center_x,2) + pow($group_y[$i][0] - $center_y,2)+ pow($group_y[$j][0] - $center_y,2);

echo $ward[$i][$j]."<br>";
    
    }
}


    for($i = 0;$i<$student_count-1;$i++){
        for($j = $i+1; $j<$student_count;$j++){
            if($i==0 && $j==1){
                //echo "初回<br>";
                $mini =$i;
                $minj =$j;
                $minward =$ward[$i][$j];
            }else{
                //echo "行きりかえ<br>";
                if($ward[$i][$j] < $ward[$mini][$minj]){
                    //echo "小さくなりました<br>"; 
                    $mini =$i;
                    $minj =$j;
                    $minward =$ward[$i][$j];
                }else{
                    //echo "大きくなりました<br>";
                }
            }
        }
    }
        echo "最小の配列[".$mini."],[".$minj."]<br>";
        echo "最小の値".$minward."<br>";




//$student_count--;//クラスタ―数減少 

$group_x[$mini] = array_merge($group_x[$mini],$group_x[$minj]);
$group_y[$mini] = array_merge($group_y[$mini],$group_y[$minj]);//群の結合

$group_x[$minj] = array();


$group_xdummy = $group_x;//配列の値の退避
$group_ydummy = $group_y;
//echo $group_xdummy[3][0];
//echo $group_xdummy[3][1];
$group_x = array();
$group_y = array();

$j = 0;
for($i = 0; $i<$student_count; $i++){//クラスタの再構成（これによってクラスタが1個減る）
    if($i < $minj){
        $group_x[$i] = $group_xdummy[$i];
        $group_y[$i] = $group_ydummy[$i];
        $j++;
    }else if($i == $minj){

    }else{
        $group_x[$j] = $group_xdummy[$i];
        $group_y[$j] = $group_ydummy[$i];
        $j++;
    }
}

$student_count--;
for($i=0;$i<$student_count;$i++){
    echo "クラスタ".$i.": ";
foreach($group_x[$i] as $value){
    echo $value." ";
}
    echo "<br>";
}
//クラスタリング1週目終了
echo "--1週目終了--<br>";
//$student_count--;





while($student_count>2){//クラスタ数が○個になるまで
$ward = array();


for($i = 0;$i<$student_count-1;$i++){
    for($j = $i+1; $j<$student_count;$j++){
       $combi_x = array();
       $combi_y = array();
        $combi_x = array_merge($group_x[$i],$group_x[$j]);//配列の結合
        $combi_y = array_merge($group_y[$i],$group_y[$j]);

    $center_x = ave($combi_x);
    $center_y = ave($combi_y);
    
    $sub1_x = ave($group_x[$i]);
    $sub2_x = ave($group_x[$j]);
    $sub1_y = ave($group_y[$i]);
    $sub2_y = ave($group_y[$j]);
    /*
    if($i==0 && $j==3){
        echo "平均A:".$sub1_x."<br>";
        echo "平均B:".$sub2_x."<br>";
    }
    */
    $k =0;
    foreach($group_x[$i] as $value){//クラスターに所属している要素の数分ループする
        $ward[$i][$j] = $ward[$i][$j] + pow($group_x[$i][$k] - $center_x,2) + pow($group_y[$i][$k] - $center_y,2);
        $hokan1[$i][$j] = $hokan1[$i][$j]+pow($group_x[$i][$k] - $sub1_x,2) + pow($group_y[$i][$k] - $sub1_y,2);
        /*
        $ave1 = ave($group_x[$i]);
        if($i==0){
            echo "平ら".$ave1."<br>";
        echo "test".$ward[$i][$j]."<br>";
        }
        */
        $k++;
    }
    $k=0;
    foreach($group_x[$j] as $value){
        $ward[$i][$j] = $ward[$i][$j] + pow($group_x[$j][$k] - $center_x,2) + pow($group_y[$j][$k] - $center_y,2);
        $hokan2[$i][$j] = $hokan2[$i][$j]+pow($group_x[$j][$k] - $sub2_x,2) + pow($group_y[$j][$k] - $sub2_y,2);
        /*
                if($i==0 &&$j==3){
        echo "test".$ward[$i][$j]."<br>";
        }
        */
        $k++;
    }
    //$ward[$i][$j] = pow($group_x[$i][0] - $center_x,2) + pow($group_x[$j][0] - $center_x,2) + pow($group_y[$i][0] - $center_y,2)+ pow($group_y[$j][0] - $center_y,2);
    /*
    if($i==0 && $j==3){
        echo "reia".$hokan1[$i][$j]."<br>";
        echo "reib".$hokan2[$i][$j]."<br>";
    }
    */
    $ward[$i][$j] = $ward[$i][$j] - $hokan1[$i][$j] - $hokan2[$i][$j];
    echo $ward[$i][$j]."<br>";
    
    }
}

   for($i = 0;$i<$student_count-1;$i++){
        for($j = $i+1; $j<$student_count;$j++){
            if($i==0 && $j==1){
                //echo "初回<br>";
                $mini =$i;
                $minj =$j;
                $minward =$ward[$i][$j];
            }else{
                if($ward[$i][$j] < $ward[$mini][$minj]){
                    //echo "小さくなりました<br>"; 
                    $mini =$i;
                    $minj =$j;
                    $minward =$ward[$i][$j];
                }else{
                    //echo "大きくなりました<br>";
                }
            }
        }
    }
        echo "最小の配列[".$mini."],[".$minj."]<br>";
        echo "最小の値".$minward."<br>";

$group_x[$mini] = array_merge($group_x[$mini],$group_x[$minj]);
$group_y[$mini] = array_merge($group_y[$mini],$group_y[$minj]);//群の結合

$group_x[$minj] = array();


$group_xdummy = $group_x;//配列の値の退避
$group_ydummy = $group_y;
//echo $group_xdummy[3][0];
//echo $group_xdummy[3][1];
$group_x = array();
$group_y = array();
$j = 0;
for($i = 0; $i<$student_count; $i++){//クラスタの再構成（これによってクラスタが1個減る）
    if($i < $minj){
        $group_x[$i] = $group_xdummy[$i];
        $group_y[$i] = $group_ydummy[$i];
        $j++;
    }else if($i == $minj){

    }else{
        $group_x[$j] = $group_xdummy[$i];
        $group_y[$j] = $group_ydummy[$i];
        $j++;
    }
}

$student_count--;
for($i=0;$i<$student_count;$i++){//出力(テスト)
    echo "クラスタ".$i.": ";
foreach($group_x[$i] as $value){
    echo $value." ";
}
    echo "<br>";
}
//クラスタリング1週目終了
echo "--週回終了--<br>";

}



?>
</body>

</html>