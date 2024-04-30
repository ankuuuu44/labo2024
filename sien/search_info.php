<?php

session_start();
require"dbc.php";
extract($_POST);
error_reporting(0);



$Question = "SELECT count(*) as cnt FROM linedata WHERE UID = '".$id."'";//ＤＢから英文を得る
$res = mysqli_query($conn,$Question) or die("英文抽出エラー");
$row = $res -> fetch_assoc();

$countques= "解答数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$row['cnt']."問</br>";
$countques_echo = $row['cnt'];
$Correct =  "SELECT count(*) as cnt FROM linedata WHERE UID = '".$id."' and TF = 1";//ＤＢから英文を得る
$res_correct = mysqli_query($conn,$Correct) or die("英文抽出エラー");
$row_correct = $res_correct -> fetch_assoc();

$correct_per_conq = $row_correct['cnt'] / $row['cnt'] * 100 ;
$correct_per = sprintf("%.1f",$correct_per_conq);
//echo "正解:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp    ".$correct_per."% (".$row_correct['cnt']."/".$row['cnt'].")<br>";
$correct_per_echo = $correct_per_conq;

$avgtime = "SELECT avg(Time) FROM linedata WHERE UID = '".$id."'";//ＤＢから英文を得る
$res_time = mysqli_query($conn,$avgtime) or die("英文抽出エラー");
$row_time = $res_time -> fetch_assoc();
$row_time_conq = $row_time['avg(Time)']/ 1000;
$row_time['avg(Time)'] = sprintf("%.2f",$row_time_conq);
//echo "平均解答時間: ".$row_time['avg(Time)']."秒<br>";
$avgtime_echo = $row_time_conq;




//2023/10/21山川追記
$grammarinanswer = "SELECT linedata.WID,TF,Understand,grammar,question_info.level,question_info.Sentence FROM linedata INNER JOIN question_info ON linedata.WID = question_info.WID WHERE linedata.UID = ".$id;
$res_grammarinanswer = mysqli_query($conn,$grammarinanswer) or die ("文法項目抽出エラー");

/*
$countgrammarnull = 0;		//文法項目が定義されていないもののカウント変数
$countgrammar1 = 0;			//仮定法
$countgrammar2 = 0;			//It,There
$countgrammar3 = 0;			//無生物主語
$countgrammar4 = 0;			//接続詞
$countgrammar5 = 0;			//倒置
$countgrammar6 = 0;			//関係詞
$countgrammar7 = 0;			//間接話法
$countgrammar8 = 0;			//前置詞
$countgrammar9 = 0;			//分詞
$countgrammar10 = 0;		//動名詞
$countgrammar11 = 0;		//不定詞
$countgrammar12 = 0;		//受動態
$countgrammar13 = 0;		//助動詞
$countgrammar14 = 0;		//比較
$countgrammar15 = 0;		//否定
$countgrammar16 = 0;		//後置修飾
$countgrammar17 = 0;		//完了形
$countgrammar18 = 0;		//句動詞
$countgrammar19 = 0;		//挿入
$countgrammar20 = 0;		//使役動詞
$countgrammar21 = 0;		//補語/二重目的語
*/

$countT_user = 0;			//正解数カウント
$countF_user = 0;			//不正解数カウント
$countT_hesitateT = 0;		//正解かつ迷い有り
$countT_hesitateF = 0;		//正解かつ迷い無し
$countF_hesitateT = 0;		//不正解かつ迷い有り
$countF_hesitateF = 0;		//不正解かつ迷い無し
$countT_other = 0;
$countF_other = 0;
//levelは配列とかにまとめた方がいいかもね
$level1count = 0;
$level2count = 0;
$level3count = 0;
$Allques = array();
$AllquesSentence = array();
$Trueques = array();
$Falseques = array();
$TruehesitateT = array();
$TruehesitateF = array();
$Trueothere = array();
$FalsehesitateT = array();
$FalsehesitateF = array();
$Falseother = array();
$countgrammar = array(
	'-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);

$grammarCountTrue = array(
    '-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);

$grammarCountFalse = array(
	'-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);

$accuracy_grammar = array(
	'-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);

$hesitate_grammar = array(
	'-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);

$grammarCounthesitateT = array(
    '-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);

$grammarCounthesitateF = array(
    '-1' => 0,
    '1' => 0,
    '2' => 0,
    '3' => 0,
    '4' => 0,
    '5' => 0,
    '6' => 0,
    '7' => 0,
    '8' => 0,
    '9' => 0,
    '10' => 0,
    '11' => 0,
    '12' => 0,
    '13' => 0,
    '14' => 0,
    '15' => 0,
    '16' => 0,
    '17' => 0,
    '18' => 0,
    '19' => 0,
    '20' => 0,
    '21' => 0
);





//echo "grammar:<br>";
while($row = $res_grammarinanswer -> fetch_assoc()){

	/*echo $row["WID"];*/
	$grammarstr = explode("#", $row["grammar"]);
    $Allques[] = $row['WID'];
    $AllquesSentence[] = $row['Sentence'];

	#print_r($grammarstr);

	if($row['TF'] === '1'){
		$countT_user += 1;
		$Trueques[] = $row['WID'];
		if($row['Understand'] =='4'){
			$countT_hesitateF += 1;
			$TruehesitateF[] = $row['WID'];
		}elseif($row['Understand'] == '2'){
			$countT_hesitateT += 1;
			$TruehesitateT[] = $row['WID'];
		}else{
			$countT_other += 1;
			$Trueothere[] =$row['WID'];
		}
	}else{
		$countF_user += 1;
		$Falseques[] = $row['WID'];
		if(isset($row['Understand'])){
			if($row['Understand' == '4']){
				$countF_hesitateF += 1;
				$FalsehesitateF[] = $row['WID'];
			}elseif($row['Understand'] == '2'){
				$countF_hesitateT += 1;
				$FalsehesitateT = $row['WID'];
			}else{
				$countF_other += 1;
				$Falseother[] = $row['WID'];
			}
		}
	}

	if($row['level'] == '1'){
		$level1count += 1;
	}elseif($row['level'] == '2'){
		$level2count += 1;
	}elseif($row['level'] == '3'){
		$level3count += 1;
	}



	foreach($grammarstr as $value){
		if(array_key_exists($value,$countgrammar)){
			$countgrammar[$value] += 1;
		}
		
		//各文法項目の正解，不正解数を入れる．
		if(array_key_exists($value,$grammarCountTrue) && $row['TF'] == 1){
			$grammarCountTrue[$value] += 1;
		
		}else if(array_key_exists($value,$grammarCountFalse) && $row['TF'] == 0){
			$grammarCountFalse[$value] += 1;
		}

		if (array_key_exists($value, $grammarCounthesitateT) && $row['Understand'] == 2){
			$grammarCounthesitateT[$value] += 1;
		}else if (array_key_exists($value, $grammarCounthesitateF) && $row['Understand'] == 4){
			$grammarCounthesitateF[$value] += 1;
		}
	}


	
	#echo "<br>";
	
}


	for($i=-1; $i<=21; $i++){
		if($i != 0){
			//ゼロ除算を防ぐ
			if($countgrammar[$i] != 0){
				$accuracy_grammar[$i] = $grammarCountTrue[$i] / $countgrammar[$i];
				$hesitate_grammar[$i] = $grammarCounthesitateT[$i] / $countgrammar[$i];			
			}else{
				$accuracy_grammar[$i] = 0;
				$hesitate_grammar[$i] = 0;
			}

		}
	}





	$levelAll = array(
		"level1count" => $level1count,
		"level2count" => $level2count,
		"level3count" => $level3count
	);


	//JSON形式のデータで返す
	
	$responsecountgrammar = array(

		"countgrammar" => $countgrammar,
		"accuracy_grammar" => $accuracy_grammar,
		"countgrammarTrue" => $grammarCountTrue,
		"countgrammarFalse" => $grammarCountFalse,
		"grammarcounthesitateT" => $grammarCounthesitateT,
		"grammarcounthesitateF" => $grammarCounthesitateF,
		"hesitate_grammar" => $hesitate_grammar,
		"countques_echo" => $countques_echo,
		"correct_per_echo" => $correct_per_echo,
		"avgtime_echo" => $avgtime_echo,
		"countT_echo" => $countT_user,
		"countF_echo" => $countF_user,
		"Trueques" => $Trueques,
		"Falseques" => $Falseques,
		"levelAll" => $levelAll,
        "Allques" => $Allques,
        "AllquesSentence" => $AllquesSentence,
		"TruehesitateTques" => $TruehesitateT,
		"TruehesitateFques" => $TruehesitateF,
		"Truehesitateotherques" => $Trueothere,
		"FalsehesitateTques" => $FalsehesitateT,
		"FalsehesitateFques" => $FalsehesitateF,
		"Falsehesitateotherques" => $Falseother
	);
	
//echo "aaa";


echo json_encode($responsecountgrammar);




?>



