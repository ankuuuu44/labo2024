<?php
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時

session_start();
$_SESSION["examflag"] = 0;
require"dbc.php";

$MemberID = $_SESSION["MemberID"];

$file_name = sys_get_temp_dir()."/tem".$MemberID.".tmp";

if(is_file($file_name)){ 
	$text = fopen($file_name,'r'); 
	for($line = 1; !feof($text); $line++){ 
		$lines = fgets($text);
		if($lines){
//print $lines;
			$res = mysql_query($lines, $conn) or die("データ追加エラー".$lines);
			if($res){
				//echo $lines;
			}
		}
	}
	echo "正常にデータを書き終えました";
	fclose($text);
}else{
	print 'データがありませんでした';
	exit;
}
unlink($file_name);
?>