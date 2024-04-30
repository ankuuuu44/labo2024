<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
session_start();
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>外れ値判定</title>
    <STYLE type="text/css">
        <!--
        .dataset {
          overflow: scroll;   /* スクロール表示 */
          width: 900px;
          height: 800px;
          background-color: white;
          position: absolute;
          top: 30px;
          left: 20px;
        }
        -->
    </STYLE>
</head>
<body>
    <?php
	    // 合計値を求めるメソッド sum()
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
		    $result = sqrt($varp);	// その平方根をとる
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
	
	    // 相関係数を求めるメソッド cc()
	    function cc($array1,$array2){
		    // 対象配列の抽出
		    $target1 = $array1;
		    $target2 = $array2;
		    // 相関係数の求め方 (X,Yの共分散)/((Xの標準偏差)(Yの標準偏差))
		    $cov = cov($target1,$target2);
		    // 2つの配列のそれぞれの標準偏差を求める
		    $sdx = sd($target1);
		    $sdy = sd($target2);
		    // 相関係数を求める際の分母を算出
		    $tmp = $sdx * $sdy;
		    if($tmp == 0){
			    // 分母が0のときは、相関係数を算出できない
			    $result = "Undefined";
		    }else{
			    // 分母はそれ以外の時は、相関係数の求め方に従って算出
			    $result = $cov / $tmp;
		    }
		    return $result;
	    }
        function array_isunique($array){
	        if(!is_array($array)){
		        return false;
	        }
	        $arrayValue = array_count_values($array);	//配列の値の数をカウントする
	        $arraykey = array_keys($arrayValue,1);	//重複していない値のキーを取り出す
	        for($i=0;$i<count($arraykey);$i++){
		        unset($arrayValue[$arraykey[$i]]);	//重複していない要素を削除
                echo "d";
	        }
	        if(count($arrayValue)!=0){
                echo $arrayValue;
		        return $arrayValue;
	        }else{
		        return false;
	        }
        }
    ?>
<DIV class="dataset">
    <?php
        require "dbc.php";
        $DS_array =array();//DDStart保存用配列
        $WID_array =array(); //WID保存用配列
        $Time_array = array();//解答時間保存用配列
        $Answer_array = array();//解答文保存用配列
        $Check_array = array();//確認時間保存用配列
        $Label_array = array();
        $Time2_array = array();
        $point_array = array();//得点保存用配列
        $Understand_array =array();//自信度保存用
        $AveSpeed = array();
        $Stime = array();
        $Etime = array();
        $point_X = array();
        $point_Y = array();
        $Time = array();
        $data_count =0;
        $sql_ques ="select count(*) as cnt from question_info;";
        $res_ques = mysql_query($sql_ques,$conn) or die("接続エラー");
        $row_ques = mysql_fetch_array($res_ques,MYSQL_ASSOC);
        $ques_count = $row_ques["cnt"];
        $DD_flag = 0;//DD中かどうかの判定用フラグ
        echo "<table border=\"1\">";
        echo "<td>WID</td>";
        echo "<td>開始</td>";
        echo "<td>終了</td>";
        echo "<td>X</td>";
        echo "<td>Y</td>";
        echo "</tr>";
 
        for($i=0;$i<$ques_count;$i++){
            $sql_DD ="select linedatamouse.X,linedatamouse.Y,linedatamouse.Time from linedatamouse,quesorder where linedatamouse.WID=quesorder.WID and linedatamouse.UID = ".$_SESSION["studentlist"]." and quesorder.OID = ".$i." order by linedatamouse.Time;";
            $res_DD = mysql_query($sql_DD,$conn) or die("接続エラー");
            $UTurnCount=0;
            $UTurnCount_XinDD=0;
            $UTurnCount_Y=0;
            $UTurnCount_YinDD=0;
            $UTurnFlag_X = 0;
            $UTurnFlag_Y = 0;
            $mouse_count = 0;
            while($row_DD = mysql_fetch_array($res_DD)){
                $before_X = $row_DD["X"];
                $before_Y = $row_DD["Y"];
                $mouse_count++;
                echo "<tr>";
                echo "<td>WID</td>";
                echo "<td>開始</td>";
                echo "<td>終了</td>";
                echo "<td>X</td>";
                echo "<td>Y</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    ?>

    <?php
            $word_sub = array();
            $word_output = array();
            $word_num = array();
            $j = 0;
            $WID_record = array();
            $rec_count = 0;
            $word_array = array();
            $word_record = array();
            $record_num = 0;
            $memo = 0;

            for($i=0;$i<$data_count;$i++){
                $sql_word ="select * from quesorder where OID= ".($WID_array[$i]);
                if($i !=0 and $WID_array[$i] != $WID_array[$i-1]){
                    echo "<tr>";
                    $WID_record[$memo+1] = $WID_array[$i];
                    $record_num = 0;
                    $memo++;
                    $j = 0;
                }else if ($i ==0){
                    $WID_record[0] = $WID_array[$i];
                }
                $word_DD = $Label_array[$i];
                $Time_DD = $Time2_array[$i];
                $j++;
                $res_word = mysql_query($sql_word,$conn) or die("接続エラーe");
                while($row_word = mysql_fetch_array($res_word)){
                    $ques_num = $row_word["WID"];
                    $OID_num = $row_word["OID"];
                }
                $sql_sentence ="select * from question_info where WID= ".$ques_num;
                $res_sentence = mysql_query($sql_sentence,$conn) or die("接続エラーf");
                while($row_sentence = mysql_fetch_array($res_sentence)){
                    $divide = $row_sentence["start"];
                }
                $word_array[$WID_array[$i]] = explode("|",$divide);
                $word_DD2 = $word_DD;
                for($k =20;$k>=0;$k--){
                    $word_DD = str_replace($k,$word_array[$WID_array[$i]][$k],$word_DD);
                }
                $Time_interval[$i] = $Stime[$i]- $Etime[$i-1];
                if($Time_interval[$i] <0){
                    $Time_interval[$i] = $Stime[$i];
                }
            }
            echo "</table>";
            echo "<br><br>";
        }
    ?>
</div>
<?php
$_SESSION["cmd"]="";
$_SESSION["studentlist"]="";    
?>
</body>
</html>