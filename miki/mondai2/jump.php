<?php
session_start();


//idとpassの検証
//テキストボックスに入力されたデータを受け取る
	for($i=1;$i<=10;$i++){
		if($check[@$_POST["qtxt".$i]]==@$_POST["qtxt".$i]){	//同じ数字が2回登録
			echo ("同じ数字は入力しないでください</br>");
			//header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/select.php");//本当のサーバー用
			require "select.php";//ローカル用
		}else if(@$_POST["qtxt".$i]!=""){ //空白のまま
			$_SESSION["qtxt".$i]=@$_POST["qtxt".$i];
			$check[@$_POST["qtxt".$i]] = @$_POST["qtxt".$i];
						
		}else{
			echo("空欄があります<br>");
			//header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/select.php");//本当のサーバー用
			require "select.php";//ローカル用
		}
	}
	
	//echo "入力id：{$id}<br>";
	//echo "パス：{$pass}<br>";
	//print $_SESSION["qtxt2"];
	//現在時刻の取得
	$AccessDate = date('Y-m-d H:i:s');
	$_SESSION["AccessDate"] = $AccessDate;
	//echo "<p>".$_SESSION["MemberName"]."さん ようこそ";
	//header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/quiz.php");//本当のサーバー用
	require "quiz.php";//ローカル用
?>
