<?php

session_start();
require"dbc.php";
extract($_POST);
$Question = "SELECT count(*) as cnt FROM linedata WHERE WID = '".$id."'";//ＤＢから英文を得る
$res = mysqli_query($conn,$Question) or die("英文抽出エラー");
$row = $res -> fetch_assoc();
echo "解答数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$row['cnt']."問</br>";
$Correct = "SELECT count(*) as cnt FROM trackdata WHERE WID = '".$id."' 
			and Point = 10";//ＤＢから英文を得る

$res_correct = mysqli_query($conn,$Correct) or die("英文抽出エラー");
$row_correct = $res_correct -> fetch_assoc();

$correct_per = sprintf("%.1f",$row_correct['cnt'] / $row['cnt'] * 100);
echo "正解:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp    ".$correct_per."%<br>";
echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp(".$row_correct['cnt']."/".$row['cnt'].")<br>";

$avgtime = "SELECT avg(Time) FROM linedata WHERE WID = '".$id."'";//ＤＢから英文を得る
$res_time = mysqli_query($conn,$avgtime) or die("英文抽出エラー");
$row_time = $res_time -> fetch_assoc();
$row_time['avg(Time)'] = sprintf("%.2f",$row_time['avg(Time)']/ 1000);
echo "平均解答時間: ".$row_time['avg(Time)']."秒<br>";
$word_num = "SELECT * FROM question_info WHERE WID = '".$id."'";//ＤＢから英文を得る
$res_word = mysqli_query($conn,$word_num) or die("英文抽出エラー");
$row_word = $res_word -> fetch_assoc();
echo "単語数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp".$row_word['wordnum']."語<br>";

if($row_word['level'] == 1){
    echo "難易度:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp初級<br>";
}else if($row_word['level'] == 2){
    echo "難易度:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp中級<br>";
}else{
    echo "難易度:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp上級<br>";    
}
$grammar_sql ="SELECT * from grammar";
$res_grammar = mysqli_query($conn,$grammar_sql) or die("英文抽出エラー");

$grammar = $row_word['grammar'];
$grammar_split = array();
$grammar_split = explode("#",$grammar);

array_pop($grammar_split);
array_shift($grammar_split);

$grammar_print = array();

for($i = 0; $i<count($grammar_split); $i++)
{

$query6 = "select Item from grammar where GID = $grammar_split[$i]";
$res6 = mysqli_query($conn,$query6);
$row5 = $res6 -> fetch_assoc();

$grammar_print[$i] = $row5['Item'];
}



echo "文法項目:";
for($i = 0; $i<count($grammar_print); $i++){
    echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
    if($i>0){echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";}
    $userLink = '<a href = "#" class = "grammar-link" data-grammar-link-id ="' . $grammar_print[$i]. '">';
    $userLink.= $grammar_print[$i]. '</a><br>';
    echo $userLink;
    
    echo "<br>";
}

$Truestu = array();
$Falsestu = array();

$TFstu = "SELECT UID,TF,Understand FROM linedata WHERE WID = '".$id."'";//ＤＢから英文を得る
$resTFstu = mysqli_query($conn, $TFstu);

while($row = $resTFstu -> fetch_assoc()){
    if($row['TF'] === ' 1'){
        
    }
}

?>