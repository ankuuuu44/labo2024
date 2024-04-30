<?php
//DB書き込み
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時

session_start();
//$_SESSION = array();

//$_SESSION["examflag"] = 0;
require"dbc.php";

$MemberID = 5;

$file_name = "./special.tmp";

if(is_file($file_name)){ 
	$text = fopen($file_name,'r'); 
	for($line = 1; !feof($text); $line++){ 
		$lines = fgets($text);
		if($lines){
			//print $lines;
			$res = mysql_query($lines, $conn) or die(mysql_errno().": ".mysql_error());
			
			if($res){
				echo $lines;
			}
			mysql_free_result($res);
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
<html>
	<head></head>
	<title>特別仕様</title>
	<body>
		<?= $res ?><br />
	</body>
</html>
