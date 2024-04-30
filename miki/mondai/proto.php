<?php
/**
 * Error reporting level
 */
error_reporting(E_ALL);   // デバッグ時
//error_reporting(0);   // 運用時
session_start();
$MemberID = $_SESSION["MemberID"];
require"dbc.php";

$Ques = "SELECT GID FROM member WHERE UID = ".$MemberID;
$res = mysql_query($Ques, $conn) or die("グループID抽出エラー");
$row = mysql_fetch_array($res);
$Prop = $row['GID'];
$Prop -= 0;
mysql_free_result($res);
if($_GET['param1']=="b"){
	$Ques = "SELECT count(*) FROM member WHERE GID = ".$Prop;
	$res = mysql_query($Ques, $conn) or die("グループ抽出エラー");
	$rew = mysql_fetch_array($res);
	echo $rew['count(*)'];
	mysql_free_result($res);
}else if($_GET['param1']=="s"){
	$pst = $_GET['param2'];
	if($pst <> 0){
		$Ques = "SELECT count(*) from linedata A, member B WHERE B.GID = ".$Prop." AND A.UID = B.UID";
		$res = mysql_query($Ques, $conn) or die("自己回答数抽出エラー");
		$row = mysql_fetch_array($res);	
		if($row['count(*)']<>0){
			$Ques = "SELECT uid,count(*) FROM linedata GROUP BY uid";
			$res = mysql_query($Ques, $conn) or die("全回答数抽出エラー");
			$row = mysql_fetch_array($res);
			if($res){
				mysql_free_result($res);
				$Ques = "SELECT count(*) FROM (SELECT A.uid ,A.GID,(C.Good / B.Answer)AS Per FROM member A, 
				(SELECT uid , count(*) AS Answer FROM linedata GROUP BY uid) B, 
				(SELECT uid, count(*) AS Good FROM linedata WHERE TF=1 GROUP BY uid) C 
				WHERE A.uid= B.uid AND B.uid = C.uid AND GID = ".$Prop." ORDER BY 3 desc) D 
				WHERE D.Per > ".$pst." AND D.uid <> ".$MemberID;
				$res = mysql_query($Ques, $conn) or die("グループ順位抽出エラー");
				$rank = mysql_fetch_array($res);
				echo $rank['count(*)'];
				mysql_free_result($res);
			}else{
				echo 0;
				mysql_free_result($res);
			//$rank
			}
		}else{
			echo -1;
		}
	}else{
		echo -1;
	}
}
?>