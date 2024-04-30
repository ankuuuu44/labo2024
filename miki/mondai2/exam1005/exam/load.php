<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

require"dbc.php";

$FName4 = "elinedata";
$FName2 = "elinedatamouse_".$_SESSION["MemberName"];
$MemberID = $_SESSION["MemberID"];

if($_GET['param1']=="a"){


$Question = "SELECT MAX(AID) FROM ".$FName4." WHERE (UID= ".$MemberID.")";


$res = mysql_query($Question, $conn) or die("AID抽出エラー");

$count = mysql_num_rows($res);


//データが抽出できたとき
if(mysql_num_rows($res) > 0){
	$row = mysql_fetch_array($res);
	echo $row['MAX(AID)'];
	$LineA = $row['MAX(AID)'];
	//mysql_free_result($res)
	
	$Question = "SELECT MAX(AID) FROM ".$FName2;
    $res = mysql_query($Question, $conn) or die("AID抽出エラー（マウス）");
    if($res){
    $row = mysql_fetch_array($res);
    $MouseA = $row['MAX(AID)'];
    	if($LineA != $MouseA){
    		$Question = "DELETE FROM ".$FName2." WHERE (AID= ".$MouseA.")";
    		$res = mysql_query($Question, $conn) or die("AID削除エラー");
    	}
    	mysql_free_result($res);
    }
}
else{
	echo "AIDエラー";
	}
}
?>