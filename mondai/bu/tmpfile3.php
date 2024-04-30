<?php
/**
 * Error reporting level
 */
error_reporting(E_ALL);   // デバッグ時
//error_reporting(0);   // 運用時

//lastdayへの書き込み
session_start();
$FName3 = "lastday";
$MemberID = $_SESSION["MemberID"];
$AccessDate = $_SESSION["AccessDate"];

if($_GET['param1'] == 1){
$str = "INSERT INTO ".$FName3." VALUES(".$MemberID.",\"".$AccessDate."\",".$_GET['param2'].")";
}
else{
$str = "UPDATE ".$FName3." SET Time = ".$_GET['param2']." WHERE Date = \"".$AccessDate."\"";
}
// ******************************************************
//テンポラリファイルの名前
/*$Tempdir = sys_get_temp_dir();
$TempFileName = "/temp".$MemberID.".tmp"; //一時ファイルのパス。php版がわからない。これでいいのかな？
$tfn = tempnam($Tempdir,$TempFileName);
$handle = fopen($tfn,"a+");
fwrite($handle,$str);*/
// ******************************************************

//ファイル書き込みコード
$TempFileName = sys_get_temp_dir()."/tem".$MemberID.".tmp";
file_put_contents($TempFileName,$str."\n",FILE_APPEND);
echo file_get_contents($TempFileName);

// ***************************************
//unlink($TempFileName);
//echo fread($TempFileName,filesize($TempFileName));
/*$temp = tmpfile();
fopen($temp,"a+");
fwrite($temp,$str);
fseek($temp,0);
echo fread($temp,filesize($temp));*/
//echo fread($handle,filesize($tfn));
//echo $tfn;
// ***************************************
?>