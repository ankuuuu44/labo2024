<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();

require "dbc.php";

$linedataTableName = "linedata";
$linedatamouseTableName = "linedatamouse";
$MemberID = $_SESSION["MemberID"];


if($_GET['param1']=="a")
{
    //ユーザが解答した問題のOIDの最大値を算出 linedata
	$SQLForMaxOIDFromLineData = "SELECT MAX(quesorder.OID) as MAX "
								."FROM ".$linedataTableName.",quesorder "
								."WHERE ".$linedataTableName.".UID= ".$MemberID." and ".$linedataTableName.".WID=quesorder.WID";
	$tableMaxOIDFromLineData = mysql_query($SQLForMaxOIDFromLineData, $conn) or die("OID抽出エラー");
	//データが抽出できたとき
	if(mysql_num_rows($tableMaxOIDFromLineData) > 0)
	{
		$maxOIDInLineData = mysql_fetch_array($tableMaxOIDFromLineData)['MAX'];
		//mysql_free_result($tableMaxOID)
	}
	else
	{
		echo "OIDエラー";
	}
	//ユーザが解答した問題のOIDの最大値を算出 linedatamouse
	$SQLForMaxOIDFromLineDataMouse = "SELECT MAX(quesorder.OID) as MAX "
									."FROM ".$linedatamouseTableName.",quesorder "
									."WHERE ".$linedatamouseTableName.".UID= ".$MemberID." and ".$linedatamouseTableName.".WID=quesorder.WID";
	$tableMaxOIDFromLineDataMouse = mysql_query($SQLForMaxOIDFromLineDataMouse, $conn) or die("OID抽出エラー（マウス）");
	if(mysql_num_rows($tableMaxOIDFromLineDataMouse) > 0)
	{
	    $row = mysql_fetch_array($tableMaxOIDFromLineDataMouse);
	    $maxOIDInLineDataMouse = $row['MAX'];
		// mysql_free_result($tableMaxOIDFromLineDataMouse);
	}
	else
	{
		echo "OIDエラー";
	}

	if($maxOIDInLineData < $maxOIDInLineDataMouse)
	{
		echo $maxOIDInLineDataMouse;
	}
	else
	{
		echo $maxOIDInLineDataMouse;
    }
}
else if($_GET['param1']=="w")
{
    $sql_wid="select WID from quesorder where oid=".$_GET['param2']."";
    $res_wid = mysql_query($sql_wid, $conn) or die("WID抽出エラー");
    $cnt_wid = mysql_num_rows($res_wid);
    if($cnt_wid ==1)
    {
        $row_wid = mysql_fetch_array($res_wid);
		$WID = $row_wid['WID'];
        echo $WID;
    }
    else 
    {
        echo "WID抽出エラー";
    }
}
?>
