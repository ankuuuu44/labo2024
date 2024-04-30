<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
//linedatamouseへの書き込み
session_start();
$FName2 = "elinedatamouse_".$_SESSION["MemberName"];
$MemberID = $_SESSION["MemberID"];

$str = "INSERT INTO ".$FName2." VALUES(".$_GET['param1'].",".$_GET['param2'].",".$_GET['param3'].
	",".$_GET['param4'].",".$_GET['param5'].",".$_GET['param6'].",\"".$_GET['param7']."\",\"".
	$_GET['param8']."\",".$_GET['param9'].")";
//テンポラリファイルの名前
/*$Tempdir = sys_get_temp_dir();
$TempFileName = "/temp".$MemberID.".tmp"; //一時ファイルのパス。php版がわからない。これでいいのかな？
$tfn = tempnam($Tempdir,$TempFileName);
$handle = fopen($tfn,"a+");
fwrite($handle,$str);*/
//ファイル書き込みコード
$TempFileName = sys_get_temp_dir()."/tem".$MemberID.".tmp";
file_put_contents($TempFileName,$str."\n",FILE_APPEND);
echo file_get_contents($TempFileName);
//unlink($TempFileName);
//echo fread($TempFileName,filesize($TempFileName));
/*$temp = tmpfile();
fopen($temp,"a+");
fwrite($temp,$str);
fseek($temp,0);
echo fread($temp,filesize($temp));*/
//echo fread($handle,filesize($tfn));
//echo $tfn;
?>