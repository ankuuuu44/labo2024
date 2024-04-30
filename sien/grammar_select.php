<?php

session_start();
require"dbc.php";
extract($_POST);
error_reporting(0);


$sqlgrammar = "select distinct question_info.WID , question_info.Sentence FROM question_info INNER JOIN linedata ON question_info.WID = linedata.WID WHERE question_info.grammar LIKE '%" . $id . "%' order by WID asc" ;
$res_grammarques = mysqli_query($conn,$sqlgrammar);
$grammarquesid = array();
$grammarquessentence = array();

while($row = $res_grammarques -> fetch_assoc()){
    $grammarquesid[] = $row['WID'];
    $grammarquessentence[] = $row['Sentence'];
 }

$responsecountgrammar = array(
    "grammarquesid" => $grammarquesid,
    "grammarquessentence" => $grammarquessentence
);



echo json_encode($responsecountgrammar);




?>



