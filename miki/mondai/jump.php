<?php
session_start();


//id��pass�̌���
//�e�L�X�g�{�b�N�X�ɓ��͂��ꂽ�f�[�^���󂯎��
	for($i=1;$i<=10;$i++){
		if($check[@$_POST["qtxt".$i]]==@$_POST["qtxt".$i]){	//����������2��o�^
			echo ("���������͓��͂��Ȃ��ł�������</br>");
			//header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/select.php");//�{���̃T�[�o�[�p
			require "select.php";//���[�J���p
		}else if(@$_POST["qtxt".$i]!=""){ //�󔒂̂܂�
			$_SESSION["qtxt".$i]=@$_POST["qtxt".$i];
			$check[@$_POST["qtxt".$i]] = @$_POST["qtxt".$i];
						
		}else{
			echo("�󗓂�����܂�<br>");
			//header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/select.php");//�{���̃T�[�o�[�p
			require "select.php";//���[�J���p
		}
	}
	
	//echo "����id�F{$id}<br>";
	//echo "�p�X�F{$pass}<br>";
	//print $_SESSION["qtxt2"];
	//���ݎ����̎擾
	$AccessDate = date('Y-m-d H:i:s');
	$_SESSION["AccessDate"] = $AccessDate;
	//echo "<p>".$_SESSION["MemberName"]."���� �悤����";
	//header("location: http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/quiz.php");//�{���̃T�[�o�[�p
	require "quiz.php";//���[�J���p
?>
