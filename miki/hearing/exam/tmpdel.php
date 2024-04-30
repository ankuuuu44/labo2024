<?php
/**
 * Error reporting level
 */
error_reporting(E_ALL);   // fobO
//error_reporting(0);   // ^p
//linedatamouseւ̏
session_start();
$FName2 = "elinedatamouse_".$_SESSION["MemberName"];
$MemberID = $_SESSION["MemberID"];


$TempFileName = sys_get_temp_dir()."/tem".$MemberID.".tmp";
echo file_get_contents($TempFileName);
unlink($TempFileName);

?>