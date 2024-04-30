<!DOCTYPE html PUBLIC "-//W3c//DTD HTML 4.01 Transitional//EN">
<?php
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
error_reporting(0);   // 運用時
session_start();
if(!isset($_SESSION["MemberName"])){ //ログインしていない場合
require"notlogin";
session_destroy();
exit;
}
/*if($_SESSION["examflag"] == 1){
	require"overlap.php";
	exit;
}else{
$_SESSION["examflag"] = 2;
$_SESSION["page"] = "ques";
}*/
//echo $_SESSION["MemberName"];
/*
echo "AAA";
mysql_connect("localhost","maintainer","789514");
mysql_select_db("niyon_kdb");
$ss = "set names utf8";
mysql_query($ss);
$ttt = "select Japanese from lquestion where wid = 100";
$yyy = mysql_query($ttt);
$ggg = mysql_fetch_array($yyy);
echo "BBB";

	echo mb_detect_encoding($ggg['Japanese']);
	echo $ggg['Japanese'];	
	//echo mb_convert_encoding($row['Japanese'],"UTF-8","EUC-JP");
	//echo mb_convert_encoding($row['Japanese'],"EUC-JP","auto");
	//echo $row['Japanese'];
echo "CCC";

exit();
*/




?>
<html>
<head>
<!--<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.6.0/build/reset-fonts-grids/reset-fonts-grids.css">-->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>並び替え問題プログラム</title>


<script type="text/javascript"
	    src="yui/build/yahoo/yahoo-min.js"></script>
<script type="text/javascript"
		src="yui/build/event/event-min.js"></script>
<script type="text/javascript"
		src="yui/build/dom/dom-min.js"></script>
<script type="text/javascript"
		src="prototype.js"></script>
<script type="text/javascript"
		src="dateformat.js"></script>
<script type="text/javascript"
		src="wz_jsgraphics.js"></script>
<script type="text/javascript"
		src="yui/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript"
		src="yui/build/animation/animation-min.js"></script>
<script type="text/javascript">
//ストップウォッチ関数------------------------------------
//宣言部分
myButton = 0;            // [Start]/[Stop]のフラグ
var myStart;
var myStop;
function myCheck(myFlg){
  if (myButton==0){      // Startボタンを押した
    myStart=new Date();  // スタート時間を退避
    myButton = 1;
    //document.myForm.myFormButton.value = "Stop!";
    myInterval=setInterval("myCheck(1)",1);
  }else{                 // スタート実行中
    if (myFlg==0){       // Stopボタンを押した
      myButton = 0;
      //document.myForm.myFormButton.value = "Start";
      clearInterval( myInterval ); 
    }
    myStop=new Date();  // 経過時間を退避
    myTime = myStop.getTime() - myStart.getTime(); // 通算ミリ秒計算
    /*myH = Math.floor(myTime/(60*60*1000));         // '時間'取得
    myTime = myTime-(myH*60*60*1000);
    myM = Math.floor(myTime/(60*1000));            // '分'取得
    myTime = myTime-(myM*60*1000);*/
    myS = Math.floor(myTime/1000);                 // '秒'取得
    myMS = myTime%1000;                        // 'ミリ秒'取得
    //document.getElementById("TextBox1").innerHTML = myH+":"+myM+":"+myS+":"+myMS;
    document.getElementById("TextBox1").innerHTML = myS+":"+myMS;
  }
}
//-------------------------------------------------------------------------------------
//ストップウォッチ関数2(全体用)------------------------------------
//宣言部分
myButton2 = 0;            // [Start]/[Stop]のフラグ
var myStart2;
var myStop2;
function myCheck2(myFlg2){
  if (myButton2==0){      // Startボタンを押した
    myStart2=new Date();  // スタート時間を退避
    myButton2 = 1;
    //document.myForm.myFormButton.value = "Stop!";
    myInterval2=setInterval("myCheck2(1)",1);
  }else{                 // スタート実行中
    if (myFlg2==0){       // Stopボタンを押した
      myButton2 = 0;
      //document.myForm.myFormButton.value = "Start";
      clearInterval( myInterval2 ); 
    }
    myStop2=new Date();  // 経過時間を退避
    myTime2 = myStop2.getTime() - myStart2.getTime(); // 通算ミリ秒計算
    /*myH = Math.floor(myTime/(60*60*1000));         // '時間'取得
    myTime = myTime-(myH*60*60*1000);
    myM = Math.floor(myTime/(60*1000));            // '分'取得
    myTime = myTime-(myM*60*1000);*/
    myS2 = Math.floor(myTime2/1000);                 // '秒'取得
    myMS2 = myTime2%1000;                        // 'ミリ秒'取得
    //document.getElementById("TextBox1").innerHTML = myH+":"+myM+":"+myS+":"+myMS;
    //document.getElementById("TextBox1").innerHTML = myS+":"+myMS;
  }
}
//-------------------------------------------------------------------------------------
//＃構造体の宣言
//-----------------------
var Mouse = new Object();
	Mouse["AID"] = 0;
	Mouse["Time"] = 0;
	Mouse["X"] = 0;
	Mouse["Y"] = 0;
	Mouse["DragDrop"] = 0;//ドラッグ中か（0:MouseMove,1:MouseDown,2:MouseUp)
	Mouse["DropPos"] = 0;//どこドロップされたか(0:元,1:レジスタ1,2:レジスタ2,3:レジスタ3)
	Mouse["hlabel"] = "";//ドラッグしているラベル（マウスが当たっているラベル）
	Mouse["Label"] = "";//どのラベルが対象か（複数ラベル)
	Mouse["addk"] = 0;
//-------------------------
//-----------------------
var AnswerData = new Object();
	AnswerData["QN"] = 0;//問題番号
	AnswerData["ADate"] = new Date;//解答日時
	AnswerData["TF"] = 0;//正誤
	AnswerData["Time"] = 0;//解答時間
	AnswerData["FQues"] = "";//問題
	AnswerData["AID"] = 0;
//--------------------------
p = new Array();
Mouse_Flag = new Boolean(false);//マウスの軌跡を保存するかどうか
IsDragging = new Boolean(false);//ドラッグ中の場合true
function Point(_x,_y){ this.x=_x; this.y=_y;}
//使う例 print(DiffPoint.x);
var DiffPoint = new Point(0,0); //ドラッグ開始地点とドラッグ開始時のボタンの位置とのずれ
var DLabel = "";

var x = 0; //挿入線を描画する位置
var y1 = 0;
var y2 = 0;

Mylabels = new Array(); //並び替えラベルの元
MyNums = new Array(); //番号リスト
var DefaultX = 30; //ラベルの初期値
var DefaultY = 100;

var sPos = new Point(0,0);
var ePos = new Point(0,0);

var PorQ; //文末の.または?を格納するよう
var Answer; //回答　（先頭大文字、文末つき）
var Question; //問題文(先頭小文字、文末ぬき）
var str1;//Answerの補助
var str2;
var LabelNum;//ラベルの数
var Answer; //正解
var Answer1; //別解1
var Answer2; //別解2

var Answertime = new Date;//解答日時(datatime?)
var $Ques_Num = 0;//問題番号
var $Mouse_Data = Mouse; //マウスの軌跡情報を保持
var Mouse_Num; //マウスの軌跡情報の数
var StartQues=""; //始めの問題の状態
var MyAnswer=""; //自分の答え
var $QAData = AnswerData; //問題データ保存用
var NewQuesNum; //出題する問題番号

var MyControls = new Array();//グループ化ラベルをまとめた配列

var AllCorrectAnsRate = 0;//全体の正解率
var AllCorrectAns = 0; //全体の正解数
var AllResAns = 0; //全体の解答数

var CorrectAnsRate = 0; //今回の正解率
var CorrectAns = 0; //今回の正解数
var ResAns = 0;//今回の解答数

var AID = 0; //解答番号、linedataとlinedatamouseを関連付けるキー
var checkl = 0;//phpオリジナル、重さをなくすため
var cx = 0;//キャンバスのギャップの修正用
var cy = 0;

var MV = new Boolean(false);//グループ化のためのドラッグ中か
var loc = -1; //グループ化の線の位置　0:左上 1:左下 2:右上 3:右下
var rx = 0; //再描画用（消すため)
var rx = 0;
var PreMouseTime = -1; //前回のマウス取得時間（※新しい問題が出るたびに初期化させている）
var dd = new Array();//ドラッグドロップ変数
var $AccessDate; //ログイン日時
var kugiri_num = 0; //区切りラベルの数
Mld = new Boolean(false);//mylabeldownイベント中か

var FixLabels = new Array(); //固定ラベル
var FixNum = new Array(); //固定ラベルの番号
var FixText = new Array(); //固定ラベルのタグを含むテキスト
MytoFo = new Boolean(false); //IEのバグ対応。MyLabels_MouseMove→Form1_onMouseMoveのため

var DragL; //ドラッグ中のラベルの引渡し。
var del; //デリートフラグがついた問題。
var delwid; 

var URL = 'http://lmo.cs.inf.shizuoka.ac.jp/~sato/test/' //サーバー用
//var URL = 'http://localhost/' //ローカル用

//var Kdelid = new Array();
//jg = new jsGraphics("myCanvas"); //キャンバス
/*if(typeof Form1 == "undefined"){
		var Form1 = {};
	}
	Form1.isMousedown = false;
	Event.observe(document, "mousedown", Form1.mousedown);
	Event.observe(document, "mouseup", Form1.mouseup);
	//Event.observe(document, "mouseover", Form1.mouseover);*/
//----------ランダムに配列を並び替えるソース--------------------
//copyright(C) 2005 あう http://www5c.biglobe.ne.jp/~horoau/
//ver1.0
Array.prototype.random = function ()
{this.sort(function (a,b)
 {var i = Math.ceil(Math.random()*100)%2;
  if(i == 0)
  {return -1;}
  else
  {return 1;}});}
 //-------------------------------------------------------------
 //----------配列に指定した値があるかチェック-----------------
 if( ! Array.prototype.contains ){
        /**
        * @access public
        * @param value mixed 検索するオブジェクト
        * @return boolean 対象配列に既にオブジェクトが存在していれば true, そうでなければ false
        * 配列の値の重複チェックなどに使用。
        */
        Array.prototype.contains = function( value ){
            for(var i in this){
                if( this.hasOwnProperty(i) && this[i] === value){
                    return true;
                }
            }
            return false;
        }
}
//-------------------------------------------------------------
//ロードイベント//----------------------------------------------
function ques_Load(){
	new Ajax.Request(URL + 'swrite.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	alert(req.responseText);
	}
	function getE(req){
		alert("書き込みに失敗しました");
	}
	AnswerT = new DateFormat("yyyy-MM-dd HH:mm:ss");
	$AccessDate = AnswerT.format(new Date());
	 BPen = new jsGraphics("myCanvas"); //ペン(グループ化用)
	 BPen.setColor("black");
	 //破線のスタイルを設定
	 BPen.setStroke(-1);
	 BPen2 = new jsGraphics("myCanvas"); //ペン(挿入線用)
	 BPen2.setColor("black");
	document.onmousemove = Form1_MouseMove;
	//document.onmousemove = Form_MouseMove;
	document.onselectstart = "return false";
	//document.unselectable = on;
//DBから引用--------------------------------------------------
function getError(res)
{
	alert("失敗");
	window.close();
}
//=============linedatamouseがなかったら作成============
new Ajax.Request(URL + 'linemouse.php',
{
	method: 'get',
		onSuccess: getm,
		onFailure: getError
});
	function getm(res){
		//alert(res.responseText);
	}
//======================================================
	var $a = "a";
	$params = 'param1=' + encodeURIComponent($a);
	 new Ajax.Request(URL + 'load.php',
{
	method: 'get',
		onSuccess: getAID,
		onFailure: getError,
		parameters: $params
});
	function getAID(res){
		AID = res.responseText;
		//alert(res.responseText);
		//alert(AID);
		if(AID == "AID抽出エラー（マウス）" || AID == ""){
			AID = 0;
			//alert(AID);
		}else{
			AID -= 0;//数値化
			AID += 1;
		}
	}
//======================================================
//======================================================
//デリートフラグが1の問題を抽出
	var $del = "del";
	$params = 'param1=' + encodeURIComponent($del);
	 new Ajax.Request(URL + 'load.php',
{
	method: 'get',
		onSuccess: getDelflg,
		onFailure: getError,
		parameters: $params
});
	function getDelflg(res){
		del = res.responseText;
		delwid = del.split("#");
		//alert(res.responseText);
		//alert(del);
	}
//======================================================正解率
var $aca = "aca";
	$params = 'param1=' + encodeURIComponent($aca);
new Ajax.Request(URL + 'correct.php',
{
	method: 'get',
		onSuccess: getaca,
		onFailure: getError,
		parameters: $params
});
	function getaca(res){
		res.responseText -= 0;
		AllCorrectAns = res.responseText;
		//======================================================
		var $ara = "ara";
			$params = 'param1=' + encodeURIComponent($ara);
		new Ajax.Request(URL + 'correct.php',
		{
			method: 'get',
			onSuccess: getara,
			onFailure: getError,
			parameters: $params
		});
			function getara(res){
				//alert(res.responseText);
				res.responseText -= 0;
				AllResAns = res.responseText;
				//正解率の表示---------
				if(AllResAns != 0){
					AllCorrectAnsRate = AllCorrectAns / AllResAns * 100;
				}
				AllCorrectAnsRate = Math.round(AllCorrectAnsRate * 100) / 100; //小数第三位を四捨五入するため
				document.getElementById("ListBox1").innerHTML = "全体 ( " + AllCorrectAns + " / " + AllResAns + "  " + AllCorrectAnsRate + "% )</br>"
						+ "今回 ( " + CorrectAns + " / " + ResAns + "  " + CorrectAnsRate + "% )";
				//----------------------
			}
		//======================================================
	}
//======================================================
//--------------------------------------------------------------------
	myCheck2(0);
}
//--------------------------------------------------------------------
//スタート 問題の出題関数------------------------------------------------------------
function setques(){
	Fixmsg.innerHTML = "-情報-";
	myCheck(0);
	$Ques_Num = Math.floor( Math.random() * 201 ); //ランダムに問題を選出
	//$Ques_Num = 0; //問題を指定したいとき用
	
	for(i=0;i <= delwid.length - 1;i++){
		//delwid[i] += 0;
		//alert("変化前" + delwid[i]);
		if(delwid[i] == $Ques_Num){
			//alert(delwid[i]);
			$Ques_Num = Math.floor( Math.random() * 201 );
			//alert("変化後" +  $Ques_Num);
			i = -1;
		}
	}
	$q = "q";
	//alert($Ques_Num);
	var $params = 'param1=' + encodeURIComponent($Ques_Num)
				+ '&param2=' + encodeURIComponent($q);
	//var $params = 'param1=' + encodeURIComponent($Ques_Num);
	//document.getElementById("test1").innerHTML = "I";
	new Ajax.Request(URL + 'dbsyori.php', //本番用
{
	method: 'get',
		onSuccess: getResponse,
		onFailure: getError,
		parameters: $params
});
//関数開始-----------------------------------
function getResponse(req)
{
	//alert("ok");
	//NewQuesNum = $_SESSION["WID"];
	//---------------------------
	PorQ = req.responseText.charAt(req.responseText.length-1); //ピリオド、または？を抜き取る
	Question = req.responseText.substring(0,req.responseText.length-1); //ピリオド抜きの問題文
	str1 = req.responseText.substr(0,1);
	str2 = req.responseText.substr(1);
	Answer = str1.toUpperCase()+str2; //完全な答え
	Mylabels = Question.split(" "); //スペースで単語に区切る
			
			$f = "f";
			$params = 'param1=' + encodeURIComponent($Ques_Num)
					+ '&param2=' + encodeURIComponent($f);
		new Ajax.Request(URL + 'dbsyori.php', //本番用
		{
			method: 'get',
			onSuccess: getFix,
			onFailure: getError,
				parameters: $params
		});
		function getFix(Fix) //固定情報の表示
		{
			msg.innerHTML = Fix.responseText;
			//alert(Fix.responseText);
			if(Fix.responseText != "-1"){
				FixNum = Fix.responseText.split("#");//♯区切り
				for(i = 0;i <= FixNum.length - 1;i++){
					FixNum[i] -= 0; //数値化
					FixLabels[i] = Mylabels[FixNum[i]];
					FixNum[i] += 1;
					Fixmsg.innerHTML += "</br><font size='5' color='green'>" + FixLabels[i] + "</font>は<font size='5' color='red'>" + FixNum[i] +"</font>番目にきます";
					FixNum[i] -= 1;
				}
			}
		//alert("完了");
			Mylabels.random();
	LabelNum = Mylabels.length;
	//--------------------------------
	//alert(Mylabels[LabelNum-1]);
	//sortArray(Mylabels);
	//body要素を取得
	var body = document.getElementsByTagName("body")[0];
	var el;
	//------------------------------
	for(i=0;i<=LabelNum-1;i++){
		//p要素を作成
		var p = document.createElement("div");
		var n = document.createElement("div"); //そのラベルが何番目にくるのかを表示するためのdiv要素
		//テキストノードを作成
		p.setAttribute("id",i);
		n.setAttribute("id",-i);//一応何かのために(削除用)
		//YAHOO.util.Dom.setStyle(p,"background-color","orange");
		YAHOO.util.Dom.setStyle(p,"position","absolute");
		YAHOO.util.Dom.setStyle(n,"position","absolute");
		if(i<1){
		YAHOO.util.Dom.setStyle(p,"left",DefaultX);
		YAHOO.util.Dom.setStyle(p,"top",DefaultY);
		var LL = YAHOO.util.Dom.getRegion(p);
		//YAHOO.util.Dom.setStyle(n,"left",DefaultX + (LL.right - LL.left) / 2);
		YAHOO.util.Dom.setStyle(n,"top",DefaultY - 15);
		}
		else{
		YAHOO.util.Dom.setStyle(p,"left",el.right + 17);
		YAHOO.util.Dom.setStyle(p,"top",DefaultY);
		var LL = YAHOO.util.Dom.getRegion(p);
		//YAHOO.util.Dom.setStyle(n,"left",LL.left + (LL.right - LL.left) / 2);
		YAHOO.util.Dom.setStyle(n,"top",DefaultY - 15);
		}
		YAHOO.util.Dom.setStyle(p,"width","auto");
		YAHOO.util.Dom.setStyle(n,"width","auto");
		YAHOO.util.Dom.setStyle(p,"font-family","Arial");
		//YAHOO.util.Dom.setStyle(p,"cursor","w-resize");

		YAHOO.util.Dom.setStyle(Mylabels[FixNum[i]],"border","solid 1px orange");
		//YAHOO.util.Dom.setStyle(p,"font-size","12px");
		YAHOO.util.Dom.setStyle(n,"font-size","10px");
		
		if(i==LabelNum-1){
			StartQues += Mylabels[i];
		}
		else{
			StartQues += Mylabels[i] + " ";
		}
		dd[i] = new YAHOO.util.DD(p);
		var str = document.createTextNode(Mylabels[i]);
		//テキストノードをp要素に追加
		p.appendChild(str);
		MyNums[i] = i+1;
		var str2 = document.createTextNode(MyNums[i]);
		//テキストノードをn要素に追加
		n.appendChild(str2);
		
		for(f=0;f<=FixNum.length-1;f++){
			if(p.innerHTML == FixLabels[f]){
				if(p.id == FixNum[f]){
					YAHOO.util.Dom.setStyle(p,"border","solid 1px orange");
				}else{
				YAHOO.util.Dom.setStyle(p,"border","solid 1px blue");
				}
				FixNum[f] += 1;
				//p.innerHTML += "<sub><font size='1'>" + FixNum[f] + "</font></sub>";
				FixText[f] = p.innerHTML;
				FixNum[f] -= 1;
			}
			if(n.id * -1 == FixNum[f]){
				//n.innerHTML = "<font size='2' color='red'>" + FixLabels[f] + "</font>";
				YAHOO.util.Dom.setStyle(n,"font-size","11px");
				YAHOO.util.Dom.setStyle(n,"color","red");
				n.innerHTML = FixLabels[f];
			}
		}
		
		//p要素をbody要素に追加
		Mylabels[i] = p;
	    body.appendChild(Mylabels[i]);
	    //p要素をbody要素に追加
		MyNums[i] = n;
	    body.appendChild(MyNums[i]);
	    
	    var LL = YAHOO.util.Dom.getRegion(p);
		YAHOO.util.Dom.setStyle(n,"left",LL.left + (LL.right - LL.left) / 2 - 2);
	    
	    el = YAHOO.util.Dom.getRegion(p);
	    	//alert( "x" + x + "r" + r +"右"+el.right);
	    //イベントハンドラの追加
	    dd[i].onMouseDown = function(e){MyLabels_MouseDown(this.getDragEl())}
	    dd[i].onMouseUp = function(e){MyLabels_MouseUp(this.getDragEl())}
	    dd[i].onDrag = function(e){MyLabels_MouseMove(this.getDragEl())}
	    
	    YAHOO.util.Event.addListener(Mylabels[i],'mouseover',MyLabels_MouseEnter);
	    YAHOO.util.Event.addListener(Mylabels[i],'mouseout',MyLabels_MouseLeave);
	}
	//-------------------------------------
	var $j = "j";
	$params = 'param1=' + encodeURIComponent($Ques_Num)
				+ '&param2=' + encodeURIComponent($j);
	 new Ajax.Request(URL + 'dbsyori.php',
{
	method: 'get',
		onSuccess: getJapanese,
		onFailure: getError,
		//onFailure: getJpanese,
		parameters: $params
});
	function getJapanese(res){
		document.getElementById("RichTextBox1").innerHTML = res.responseText;
		
		
		//-------------------------------------
		var $s1 = "s1";
		$params = 'param1=' + encodeURIComponent($Ques_Num)
					+ '&param2=' + encodeURIComponent($s1);
		 new Ajax.Request(URL + 'dbsyori.php',
	{
		method: 'get',
			onSuccess: getSentence1,
			onFailure: getError,
			parameters: $params
	});
		function getSentence1(res){
			if(res.responseText != ""){//NULL以外だったら
				str1 = res.responseText.substr(0,1);
				str2 = res.responseText.substr(1);
				Answer1 = str1.toUpperCase()+str2;
				//alert(Answer1);
				
				//-------------------------------------
				var $s2 = "s2";
				$params = 'param1=' + encodeURIComponent($Ques_Num)
							+ '&param2=' + encodeURIComponent($s2);
		 		new Ajax.Request(URL + 'dbsyori.php',
			{
				method: 'get',
					onSuccess: getSentence2,
					onFailure: getError,
					parameters: $params
			});
				function getSentence2(res){
					if(res.responseText != ""){//NULL以外だったら
						str1 = res.responseText.substr(0,1);
						str2 = res.responseText.substr(1);
						Answer2 = str1.toUpperCase()+str2;
						//alert(Answer2);
				
			}//ifres.responseText != ""ここまで------------------------------------
		
		}// getSentence1ここまで--------------------------------------------------------
			}//ifres.responseText != ""ここまで------------------------------------
		
		}// getSentence1ここまで--------------------------------------------------------
		
	}// getJapaneseここまで--------------------------------------------------------
			Mouse_Flag = true;
		}//Fix関数ここまで--------------------------------------------------------
}
//--関数getresponseここまで---------------------------------------
function getError(req)
{
	alert("失敗");
	window.close;
}
//alert("saki");
//マウス取得スタート
//Mouse_Flag = true;
PreMouseTime = -1;

//時刻を取得
AnswerT = new DateFormat("yyyy-MM-dd HH:mm:ss");
Answertime = AnswerT.format(new Date());
}
//問題の出題関数ここまで-------------------------------------------------------
//範囲指定をするときのドラッグ開始処理------------------------------
function Form1_MouseDown(){
	if(Mouse_Flag == false){
		return;
	}
		//グループ化されたラベルの初期化
	for(i=0;i <= MyControls.length -1;i++){
		if(MyControls[i].innerHTML == "/"){
			YAHOO.util.Dom.setStyle(MyControls[i],"color","#ff6699");
		}else{
		YAHOO.util.Dom.setStyle(MyControls[i],"color","black");
		}
	}
	MyControls = new Array();//初期化
	//開始点の取得
	sPos.x = event.x + cx;
	sPos.y = event.y + cy;
	ePos.x = event.x + cx;
	ePos.y = event.y + cy;
	document.getElementById("msg").innerHTML = "Form1_MouseDown";
	MV = true;
}
//------------------------------------------------------------------

//マウスアップ関数ここから(範囲選択を確定（ラベルをグループ化))---------------------------------------------------
function Form1_MouseUp(){
		MV = false;
	if(Mouse_Flag == false || IsDragging == true){
		return;
	}
	BPen.clear();
	/*GPen = new jsGraphics("myCanvas");
	GPen.setColor("#dddddd");/*
		//alert("マウスが離れた!");
	//me.refresh//画面を再描画(vb)
	/*if(loc==0){
		GPen.drawRect(sPos.x,sPos.y,rx - sPos.x,ry - sPos.y)
	}
	else if(loc==1){
		GPen.drawRect(sPos.x,ry,rx - sPos.x,sPos.y - ry)
	}
	else if(loc==2){
		GPen.drawRect(rx,sPos.y,sPos.x - rx,ry - sPos.y)
	}
	else if (loc==3){
		GPen.drawRect(rx,ry,sPos.x - rx,sPos.y - ry)
	}
	GPen.paint();*/
	//選択範囲の中にラベルがあればグループ化する
	//青色への色変えも
	//左上,右上,左下,右下の４方向からのドラッグに対応------------------------------------------
	for(i=0;i<=LabelNum-1;i++){
		//一時退避・・・なくて良い
		MLi = YAHOO.util.Dom.getRegion(Mylabels[i]);
		if(sPos.x <= ePos.x && sPos.y <= ePos.y){  //左上
			if((sPos.x < MLi.right && sPos.y < MLi.bottom) && (ePos.x > MLi.left && ePos.y > MLi.top)){
				MyControls.push(Mylabels[i]);
				//MyControls[i] = Mylabels[i];
				//alert(MyControls[i].innerHTML);
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
		else if(sPos.x <= ePos.x && sPos.y >= ePos.y){//左下
			if((sPos.x < MLi.right && sPos.y > MLi.top) && (ePos.x > MLi.left && ePos.y < MLi.bottom)){
				MyControls.push(Mylabels[i]);
				//MyControls[i] = Mylabels[i];
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
		else if(sPos.x > ePos.x && sPos.y < ePos.y){//右上
	 		if((sPos.x > MLi.left && sPos.y < MLi.bottom) && (ePos.x < MLi.right && ePos.y > MLi.top)){
	 			MyControls.push(Mylabels[i]);
	 			//MyControls[i] = Mylabels[i];
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
		else if(sPos.x > ePos.x && sPos.y > ePos.y){//右下
	 	 	if((sPos.x > MLi.left && sPos.y > MLi.top) && (ePos.x < MLi.right && ePos.y < MLi.bottom)){
	 	 		MyControls.push(Mylabels[i]);
	 	 		//MyControls[i] = Mylabels[i];
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
	}//----------------------------------------------------------------------------------------
			//alert(MyControls.length);
		for(i=0;i<=LabelNum-1;i++){//--------------
			if(MyControls.indexOf(Mylabels[i]) == -1){
				if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
			}
		}//----------------------------------------
}
//-----------------------------------------------------------
//ドラッグ中に範囲指定の線を描画など
function Form1_MouseMove(sender){
	if(Mouse_Flag == false || Mld == true){
		document.getElementById("msg").innerHTML = "ラベルドラッグだからかえる";
		return;
	}
	if(MytoFo==false && IsDragging==true){
		//document.getElementById("msg").innerHTML = "先にきた";
		//MyLabels_MouseMove(this.getDragEl());
		//return;
	}
	/*if (typeof sender == 'undefined') && IsDragging == true) {
        document.getElementById("msg").innerHTML = "空だよ" + sender;
        return;
    }*/
	if(MV==true){
	/* GPen = new jsGraphics("myCanvas"); //ペン
	 GPen.setColor("#dddddd");
	 //破線のスタイルを設定
	 GPen.setStroke(-1);*/
	 
	 draw();
	 //描いたのいったん消す・・・未完
	
	 /*rx = ePos.x;
	 ry = ePos.y;*/
	 
	 ePos.x = event.x + cx;
	 ePos.y = event.y + cy;
	}
	//--------------------別のマウスムーブの取り込み--------------------------------------
	var P = new Point(0,0);
	
	if(Mouse_Flag==true){
		//マウスの位置座標を取得
		P.x = event.x;
		P.y = event.y;
		// ****************************************
		//位置座標を相対値に変換
		//alert(event.screenX);
		/*P.x -= event.screenX;
		P.y -= event.screenY;
		alert(event.x);
		alert(event.screenX);*/
		// ***************************************
		var a;
		if(PreMouseTime != -1){ //データを間引く
			//経過時間取得-----
			myStop = new Date();
			mTime = myStop.getTime() - myStart.getTime();
			//alert(mTime);
			a = mTime - PreMouseTime;
			if(a < 100){
				return;
			}
		}
			
			//マウスデータの取得
			myStop = new Date();
			mTime = myStop.getTime() - myStart.getTime();
			//var $Mouse_Data = Mouse;
			$Mouse_Data["AID"] = AID;
			//if( mTime == undefined ) {
			//	return;
			//}
			//alert(mTime);
			$Mouse_Data["Time"] = mTime;
			if(IsDragging == true){
				var hLabel = sender;
				//document.getElementById("msg").innerHTML = hLabel.innerHTML;
				var hl = YAHOO.util.Dom.getRegion(DragL);
				$Mouse_Data["X"] = hl.left;
				$Mouse_Data["Y"] = hl.top;
			}
			else{
				$Mouse_Data["X"] = P.x;
				$Mouse_Data["Y"] = P.y;
			}
			$Mouse_Data["DragDrop"] = 0;
			$Mouse_Data["DropPos"] = -1;
			$Mouse_Data["hlabel"] = "";
			$Mouse_Data["Label"] = "";
			$Mouse_Data["addk"] = 0;
			Mouse_Num += 1;
			
			PreMouseTime = $Mouse_Data["Time"];	
			
			var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
				+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
		new Ajax.Request(URL + 'tmpfile.php',
	{
			method: 'get',
			onSuccess: getA,
			onFailure: getE,
			parameters: $params
	});
		//▲マウスデータの取得
		//ドラッグ開始地点の保存
		function getA(req){
		document.getElementById("msg").innerHTML = req.responseText;
		MytoFo = false;
		}
		function getE(req){
			alert("失敗");
		}
	}
	//--------------------別のマウスムーブここまで----------------------------------------------------------------
}

 function draw(){
 	 BPen.clear();
	 /*if(loc==0){
		GPen.drawRect(sPos.x,sPos.y,rx - sPos.x,ry - sPos.y)
	}
	else if(loc==1){
		GPen.drawRect(sPos.x,ry,rx - sPos.x,sPos.y - ry)
	}
	else if(loc==2){
		GPen.drawRect(rx,sPos.y,sPos.x - rx,ry - sPos.y)
	}
	else if (loc==3){
		GPen.drawRect(rx,ry,sPos.x - rx,sPos.y - ry)
	}
	GPen.paint();*/
	 //消える描画でドラッグ中の四角形を描く
	 
	 //左上、右上、左下、右下、の４方向からのドラッグに対応
	 if(sPos.x <= ePos.x && sPos.y <= ePos.y){  //左上
	 	 BPen.drawRect(sPos.x,sPos.y,ePos.x - sPos.x,ePos.y - sPos.y)
	 	 	 //alert("左上");
	 	 	 loc=0;
	 }
	 else if(sPos.x <= ePos.x && sPos.y >= ePos.y){//左下
	 	 BPen.drawRect(sPos.x,ePos.y,ePos.x - sPos.x,sPos.y - ePos.y)
	 	 	 loc=1;
	 }
	 else if(sPos.x > ePos.x && sPos.y < ePos.y){//右上
	 	 BPen.drawRect(ePos.x,sPos.y,sPos.x - ePos.x,ePos.y - sPos.y)
	 	 	 //alert("右上");
	 	 	 loc=2;
	 }
	 else if(sPos.x > ePos.x && sPos.y > ePos.y){//右下
	 	 BPen.drawRect(ePos.x,ePos.y,sPos.x - ePos.x,sPos.y - ePos.y)
	 	 	 //alert("右下");
	 	 	 loc=3;
	 }
	 BPen.paint();
	 
	 //もし選択範囲にラベルがあれば赤色に色づけ
	 //選択範囲が解除されたら黒色に戻る処理も実装
	 for(i=0;i <= LabelNum -1;i++){
	 	 //一時退避
	 	 //退避ラベルならスキップ・・・必要なし
	 	 //範囲選択をすべて抱合⇒一部抱合に変更
	 	 MLi = YAHOO.util.Dom.getRegion(Mylabels[i]);
	 	if(sPos.x <= ePos.x && sPos.y <= ePos.y){ //左上---------------------------
	 		if((sPos.x < MLi.right && sPos.y < MLi.bottom) && (ePos.x > MLi.left && ePos.y > MLi.top)){
	 			YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}
	 		else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}//左上ここまで--------------------------------------------------
	 	else if(sPos.x <= ePos.x && sPos.y >= ePos.y){//左下
	 		if((sPos.x < MLi.right && sPos.y > MLi.top) && (ePos.x > MLi.left && ePos.y < MLi.bottom)){
	 			YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}
	 		else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}
	 	else if(sPos.x > ePos.x && sPos.y < ePos.y){//右上
	 		if((sPos.x > MLi.left && sPos.y < MLi.bottom) && (ePos.x < MLi.right && ePos.y > MLi.top)){
	 			YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}
	 		else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}
	 	 else if(sPos.x > ePos.x && sPos.y > ePos.y){//右下
	 	 	if((sPos.x > MLi.left && sPos.y > MLi.top) && (ePos.x < MLi.right && ePos.y < MLi.bottom)){
	 	 	 	YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}
	 		else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}
	 }//forここまで-----------------------------------------
 }
 
 //マウスが動いたときにイベントが発生するので
 //時間を取得してとまっていた時間を時刻の差分から求める。------------------------
/*function Form_MouseMove(sender){
	var P = new Point(0,0);
	
	if(Mouse_Flag==true){
		//マウスの位置座標を取得
		P.x = event.x;
		P.y = event.y;
		//位置座標を相対値に変換
		//alert(event.screenX);
		P.x -= event.screenX;
		P.y -= event.screenY;
		
		var a;
		if(PreMouseTime != -1){ //データを間引く
			//経過時間取得-----
			myStop = new Date();
			var mTime = myStop.getTime() - myStart.getTime();
			a = mTime - PreMouseTime;
			if(a < 100){
				return;
			}
		}
			
			//マウスデータの取得
			
			var $Mouse_Data = Mouse;
			$Mouse_Data["AID"] = AID;
			$Mouse_Data["Time"] = mTime;
			if(IsDragging == true){
				var hLabel = sender;
				var hl = YAHOO.util.Dom.getRegion(hLabel);
				$Mouse_Data["X"] = hl.left;
				$Mouse_Data["Y"] = hl.top;
			}
			else{
				$Mouse_Data["X"] = P.x;
				$Mouse_Data["Y"] = P.y;
			}
			$Mouse_Data["DragDrop"] = 0;
			$Mouse_Data["DropPos"] = -1:
			$Mouse_Data["hlabel"] = "";
			$Mouse_Data["Label"] = "";
			Mouse_Num += 1;
			
			PreMouseTime = $Mouse_Data["Time"];	
			
			var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"]);
		new Ajax.Request(URL + 'tmpfile.php',
	{
			method: 'get',
			onSuccess: getA,
			onFailure: getE,
			parameters: $params
	});
		//▲マウスデータの取得
		//ドラッグ開始地点の保存
		function getA(req){
		//alert(req.responseText);
		}
		function getE(req){
			alert("失敗");
		}
	}
}*/
//------------------------------------------------------------------------------- 
//ソート関数ここから----------------------------------------------------------
function MyLabelSort(sender,ex,ey)
{
	//alert("haita");
	var i;
	var j;
	var k;
	var hLabel;// = document.createElement("div");
	hLabel = sender;
	//alert(hlabel.innerHTML);
	var aNum = new Array(); //問題文のラベル番号を記憶
	var aCount = 0;
	for(i=0;i<=LabelNum-1;i++){
		aNum.push(i);
		aCount++;
	}
	var iLabel = new Array();//スワップ用配列
	var item = MyControls.indexOf(hLabel);
if(item > -1){//-------------------------------------
	j = 0;
	for(i=0;i<=LabelNum-1;i++){
		var item2 = MyControls.indexOf(Mylabels[i]);
		//グループ化ラベルを除外--------------
		if(item2 > -1){
			continue;
		}
		//------------------------------------
		iLabel[j] = Mylabels[i];//iLabelには元の配列に現在あるラベル
		j++;
	}
	//---------------挿入-------------------
	//挿入箇所が見つかるまでデータをずらす
    //Mylabelsをずれすことでグループ化ラベルが入る位置を確保する
    var X1 = YAHOO.util.Dom.getRegion(hLabel);
	for(j=(aCount-1)-MyControls.length;j>=0;j--){
		var X2 = YAHOO.util.Dom.getRegion(iLabel[j]);
		iLabel[j + MyControls.length] = iLabel[j];
		if(X1.left >= X2.left){
			break;
		}
	}
	
	//退避してあったグループ化ラベル(MyGroupLabel)を挿入する
	var m = 0;
	for(k=j+1;k<=j+MyControls.length;k++){
		iLabel[k] = MyControls[m];
		m++;
	}
	//------------挿入完了-------------------
}
else{//それ以外のラベル整形処理
	j=0;
	for(i=0;i<=LabelNum-1;i++){
			if(hLabel.id == Mylabels[i].id){
				//alert("次へ");
				continue;
			}
			iLabel[j] = Mylabels[i]
				j++;
	}
	//挿入-----------------------------------------
	var X1 = YAHOO.util.Dom.getRegion(hLabel);
	for(j=(aCount-1)-1;j>=0;j--){
		var X2 = YAHOO.util.Dom.getRegion(iLabel[j]);
		iLabel[j+1] = iLabel[j];
		if(X1.left >= X2.left){
			break;
		}
	}
	//退避してあったデータを挿入する
	iLabel[j+1] = hLabel;
	//挿入完了-------------------------------------
}
//もとの問題文の位置のラベルを整形
for(i=0;i<=aCount;i++){
//	alert(i);
	if(i==0){
		YAHOO.util.Dom.setX(iLabel[0],DefaultX);
		YAHOO.util.Dom.setY(iLabel[0],DefaultY);
	}
	else{
		var X1 = YAHOO.util.Dom.getRegion(iLabel[i-1]);
		YAHOO.util.Dom.setX(iLabel[i],X1.right + 17);//VBとはピクセルの幅が違う？PHPでは２倍にする
		YAHOO.util.Dom.setY(iLabel[i],DefaultY);
	}
	Mylabels[aNum[i]] = iLabel[i];
}
MyNumsSort();
return Mylabels;
}

//ソート関数ここまで------------------------------------------------------
function MyNumsSort(){
	//alert("きた");
var Ncount = 0;//Mylabelようの変数
	for(i=0;i <= Mylabels.length - 1;i++){
		if(Mylabels[i].innerHTML == "/"){
				//Hflag = true;
				//Scount += 1;
				continue;
			}
			//alert(MyNums[i].innerHTML.length);
		if(i<1){
			var LL = YAHOO.util.Dom.getRegion(Mylabels[i]);
			YAHOO.util.Dom.setStyle(MyNums[i],"left",DefaultX + (LL.right - LL.left) / 2 - MyNums[i].innerHTML.length / 2 - 2);
			//YAHOO.util.Dom.setStyle(MyNums[i],"left",DefaultX + (LL.left + LL2.right - LL.right));
			YAHOO.util.Dom.setStyle(MyNums[i],"top",DefaultY - 15);
			}
		else{
			/*if(Hflag == true){
				var LL = YAHOO.util.Dom.getRegion(Mylabels[i+1]);
			}
			else{*/
			//	alert("iは"+i);
			var LL = YAHOO.util.Dom.getRegion(Mylabels[i]);
			//}
			YAHOO.util.Dom.setStyle(MyNums[Ncount],"left",LL.left + (LL.right - LL.left) / 2 - MyNums[Ncount].innerHTML.length / 2 - 2);
			//YAHOO.util.Dom.setStyle(MyNums[Ncount],"left",LL.left + LL2.right - LL.right);
			YAHOO.util.Dom.setStyle(MyNums[Ncount],"top",DefaultY - 15);
		}
		Ncount += 1;
	}//------------------------------
}
//マウスが上に来たらラベルの見た目を変えたり、グループ化やレジスタラベルの対応---------------
function MyLabels_MouseEnter(e){
	if(MV==true || IsDragging==true){
		return;
	}
	//alert(this.id);
	//レジスタ内のグループ化・・・なくてよし
	var index = MyControls.indexOf(this);
	//グループ化されたラベルの初期化
	if(index == -1){
	for(i=0;i<=MyControls.length-1;i++){
		/*if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}*/
				if(MyControls[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(MyControls[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(MyControls[i],"color","black");
				}
				YAHOO.util.Dom.setStyle(MyControls[i],"text-decoration","none");
	}
	MyControls = new Array();
	}
	else{
		for(i=0;i<=MyControls.length-1;i++){
			YAHOO.util.Dom.setStyle(MyControls[i],"text-decoration","underline");
		}
	}
	//alert(MyControls.length);
	YAHOO.util.Dom.setStyle(this,"text-decoration","underline");
}
//-------------------------------------------------------------------------------------------
function MyLabels_MouseLeave(){
	if(MV==true || IsDragging==true){
		return;
	}
	for(i=0;i<=MyControls.length-1;i++){
				YAHOO.util.Dom.setStyle(MyControls[i],"text-decoration","none");
	}
	//alert(MyControls.length);
	YAHOO.util.Dom.setStyle(this,"text-decoration","none");
}
//----------------------------------------------------
/*function sortArray(Ques)
{
var fKey = new Array(LabelNum - 1);//キーとなる配列（乱数を入れる）
var iData = new Array(LabelNum - 1);//データ（最初は0～9を入れる）
var SortQues = new Array();
var k;
for(k=0;k<LabelNum;k++){
	fKey[k] = Math.random();//乱数を入れる
	iData[k] = k;//iを入れる
}
	fKey.sort();
	iData.sort();
for(k=0;k<LabelNum;k++){
	SortQues[k] = Ques[iData[k]];
}
alert(SortQues);
return SortQues;
}*/
//-----------------------------------------------------
function MyLabels_MouseDown(sender){
	// 左クリックじゃなかったら終了を作るつもり　未完
		//グループ化されたラベルの初期化
	Mld = true;
	var hLabel = sender;
	DragL = sender;//IEのバグ対応
	IsDragging = true;
	//alert("IsDraggingがtrueに");
	
	//一時退避・・・レジスタなくすからよい？
	var DPos = 0;
	DLabel = "";
	//グループ化ラベルを#で連結する グループラベル
	if(MyControls.length != 0){
		for(i=0;i <= MyControls.length - 1;i++){
			if(i == MyControls.length - 1){
				DLabel = DLabel + MyControls[i].id;
			}
			else{
				DLabel = DLabel + MyControls[i].id + "#";
			}
		}
	}
	else{
		DLabel = DLabel + hLabel.id;
	}
	//経過時間取得-----
	myStop = new Date();
	mTime = myStop.getTime() - myStart.getTime();
	//----------------
	var X = YAHOO.util.Dom.getRegion(hLabel);
	//▼マウスデータの取得
	//var $Mouse_Data = Mouse;
	$Mouse_Data["AID"] = AID;
	$Mouse_Data["Time"] = mTime;
	$Mouse_Data["X"] = X.left;
	$Mouse_Data["Y"] = X.top;
	$Mouse_Data["DragDrop"] = 2;
	$Mouse_Data["DropPos"] = DPos;
	$Mouse_Data["hlabel"] = hLabel.id;
	$Mouse_Data["Label"] = DLabel;
	$Mouse_Data["addk"] = 0;
	Mouse_Num += 1;
	
	//Tmp_Write();
		var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
				+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
	new Ajax.Request(URL + 'tmpfile.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	//alert(req.responseText);
	document.getElementById("msg").innerHTML = req.responseText;
	Mld = false;
	}
	function getE(req){
		alert("失敗");
	}
	
	DiffPoint = new Point(event.x,event.y);
}
//--------ラベルクリック時ここまで---------------------

//ラベルのMouseup-----------------------------------------
function MyLabels_MouseUp(sender){	
	if(IsDragging != true){
		//alert("true");
		return;
	}
	var hLabel = sender;
	
	for(i=0;i<=MyControls.length-1;i++){
				YAHOO.util.Dom.setStyle(MyControls[i],"text-decoration","none");
	}
	YAHOO.util.Dom.setStyle(hLabel,"text-decoration","none");
	
	
	//区切りラベルだったら、ボタン５のロックを解除 未完
	//if(hLabel.innerHTML=="/" && kugiri_num<10){
	//}
	/*jg = new jsGraphics("myCanvas");
	//挿入位置の線を初期化するため
	jg.setColor("#dddddd");
	//alert("消すよ");
	jg.drawLine(checkl+cx,y1+cy,checkl+cx,y2+cy); 
	jg.paint();*/
	draw3();
	
	
	var Dpos = 0;
	var P = new Point(0,0);
	var hl = YAHOO.util.Dom.getRegion(hLabel);
	P.x = hl.left;
	P.y = hl.top;
	//alert(P.x);
	
	
	//ラベル削除ゾーン
	/*if(P.x > 650 && P.x < 700 && P.y > 155 && P.y < 205 && hLabel.innerHTML == "/"){
		MyLabelSort(sender,event.x,event.y);
		//alert(hLabel.id);
		for(j=0;j <= LabelNum - 1;j++){
			if(Mylabels[j].id == hLabel.id){
				//alert("まずはけす");
				var id = hLabel.id;
				id -= 0;
				arr2 = Mylabels.splice(j,1);
				arr1 = dd.pop();
				arr1 = new Array();
				_delete_dom_obj(hLabel.id);
				//区切りラベルがないときのラベルの数にする
				//区切りラベルを取り除く
				kugiri_num -= 1;
				LabelNum -= 1;
				MyLabelSort(Mylabels[0],DefaultX,DefaultY);
				for(i=0;i <= LabelNum - 1;i++){
					Mylabels[i].id -= 0;
					if(Mylabels[i].id > id){
				//		alert("haita");
						var ida = Mylabels[i].id;
						ida -= 1;
						//Mylabels[i].setAttribute("id",ida);
						Mylabels[i].id = ida;
						dd[ida] = new YAHOO.util.DD(Mylabels[i]);
						dd[ida].onMouseDown = function(e){MyLabels_MouseDown(this.getDragEl())}
	    				dd[ida].onMouseUp = function(e){MyLabels_MouseUp(this.getDragEl())}
	    				dd[ida].onDrag = function(e){MyLabels_MouseMove(this.getDragEl())}
	    	   			//YAHOO.util.Event.addListener(Mylabels[i],'mouseover',MyLabels_MouseEnter);
					} 
				//document.getElementById("msg").innerHTML = 	alert(Mylabels[i].innerHTML);
				//alert(Mylabels[i].innerHTML);
				//alert(Mylabels[i].id);
				}
				//alert(dd.length);
				break;
			}
		}
		DPos = 0;
		IsDragging = false;
		
		//▼マウスデータの取得
		myStop = new Date();
		mTime = myStop.getTime() - myStart.getTime();
		//var $Mouse_Data = Mouse;
		$Mouse_Data["AID"] = AID;
		$Mouse_Data["Time"] = mTime;
		$Mouse_Data["X"] = P.x;
		$Mouse_Data["Y"] = P.y;
		$Mouse_Data["DragDrop"] = 1;
		$Mouse_Data["DropPos"] = DPos;
		$Mouse_Data["hlabel"] = id;
		$Mouse_Data["Label"] = "";
		$Mouse_Data["addk"] = 3;
		Mouse_Num += 1;
	
		var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
					+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
					+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
					+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
					+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
					+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
					+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
					+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
					+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
		new Ajax.Request(URL + 'tmpfile.php',
	{
			method: 'get',
			onSuccess: getDe,\
			onFailure: getE,
			parameters: $params
	});
		//ドラッグ開始地点の保存
	function getDe(req){
	//alert(req.responseText);
	//MyLabelSort(Mylabels[LabelNum-1],event.x,event.y);
	}
		return;

	}*/
	
	//ラベルの一時退避（レジスタないからやらなくてよし)
	
	//
	Mylabels = MyLabelSort(sender,event.x,event.y);
	var Kcount = 0;
	for(i = 0;i <= FixNum.length - 1;i++){
		Kcount = 0;
		/*if(Mylabels[FixNum[i]].innerHTML == FixLabels[i]){
			YAHOO.util.Dom.setStyle(Mylabels[FixNum[i]],"border","solid 1px orange");
		}
		else{*/
			for(j = 0;j <= LabelNum - 1;j++){
				if(Mylabels[j].innerHTML == FixText[i] && j == FixNum[i] + Kcount){
					YAHOO.util.Dom.setStyle(Mylabels[j],"border","solid 1px orange");
				}
				else if(Mylabels[j].innerHTML == FixText[i]){
					YAHOO.util.Dom.setStyle(Mylabels[j],"border","solid 1px blue");
				}
				else if(Mylabels[j].innerHTML == "/"){
					Kcount += 1;
				}
			}
		//}
	}
	//Kcount = 0;
	/*for(i = 0;i <= LabelNum -1;i++){
		for(j = 0;j <= FixNum.length - 1;j++){
		if(Mylabels[i].id == FixNum[j] && Mylabels[i + Kcount]
	}*/
	
	
	DPos = 0;
	IsDragging = false;
	
	//▼マウスデータの取得
	myStop = new Date();
	mTime = myStop.getTime() - myStart.getTime();
	//var $Mouse_Data = Mouse;
	$Mouse_Data["AID"] = AID;
	$Mouse_Data["Time"] = mTime;
	$Mouse_Data["X"] = P.x;
	$Mouse_Data["Y"] = P.y;
	$Mouse_Data["DragDrop"] = 1;
	$Mouse_Data["DropPos"] = DPos;
	$Mouse_Data["hlabel"] = "";
	$Mouse_Data["Label"] = "";
	$Mouse_Data["addk"] = 0;
	Mouse_Num += 1;
	
	var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
				+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
	new Ajax.Request(URL + 'tmpfile.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	document.getElementById("msg").innerHTML = req.responseText;
	}
	function getE(req){
		alert("失敗");
	}
}
//--------------------------------------------------------
//マウスでラベルをドラッグ中のイベント
function MyLabels_MouseMove(sender){
	if(IsDragging != true){
		return;
	}
	var hLabel = sender;
	var DestX = 0;
	var DestY = 0;
	
	
		//先に、背景色でなんか書いてる
	//ペンの作成-------
	//jg = new jsGraphics("myCanvas"); //キャンバス
	//------------------
	
	
	//jg.setStroke(-1);
	//jg.drawLine(5,18,100,100);
	

	
	//相対位置の計算
	
	var hl = YAHOO.util.Dom.getRegion(hLabel);
	DestX = hl.left + event.x - DiffPoint.x;
	DestY = hl.top + event.y - DiffPoint.y;
	
	//現在ドラッグしているラベルの位置を決定
	/*YAHOO.util.Dom.setX(hLabel,DestX);
	YAHOO.util.Dom.setY(hLabel,DestY);*/
	
	
	
	//問題文のラベルをaLabelに格納
	var aLabel = new Array(LabelNum -1);//一時退避　問題文のラベル用ラベル配列
	var aNum = 0;
	for(i=0;i<=LabelNum-1;i++){
		if(MyControls.indexOf(Mylabels[i]) != -1 || hLabel.id == Mylabels[i].id){
			continue;
		}
		aLabel[aNum] = Mylabels[i];
		aNum += 1;
	}
	
	//元の位置にあるラベルの位置を決定
	for(i=0;i<=aNum-1;i++){
		if(i==0){
			YAHOO.util.Dom.setX(aLabel[i],DefaultX);
			YAHOO.util.Dom.setY(aLabel[i],DefaultY);
		}
		else{
			var al = YAHOO.util.Dom.getRegion(aLabel[i-1]);
			YAHOO.util.Dom.setX(aLabel[i],al.right + 17); //解像度がちがうため？
			YAHOO.util.Dom.setY(aLabel[i],DefaultY);
		}
	}
	//ラベルの番号の整理-------------
	var Ncount = 0;//Mylabelようの変数
	var MCflag = new Boolean(false);
	for(i=0;i <= Mylabels.length - 1;i++){
		for(j=0;j<=MyControls.length -1;j++){
				//alert(MyControls[j].innerHTML);
				if(MyControls[j].innerHTML == Mylabels[i].innerHTML){
					MCflag = true;
					break;
				}
			}
		if(hLabel.id == Mylabels[i].id || Mylabels[i].innerHTML == "/" || MCflag == true){
			MCflag = false;
				continue;
			}
		if(i<1){
			var LL = YAHOO.util.Dom.getRegion(Mylabels[i]);
			YAHOO.util.Dom.setStyle(MyNums[i],"left",DefaultX + (LL.right - LL.left) / 2 - MyNums[i].innerHTML.length / 2 - 2);
			YAHOO.util.Dom.setStyle(MyNums[i],"top",DefaultY - 15);
			}
		else{
			var LL = YAHOO.util.Dom.getRegion(Mylabels[i]);
			YAHOO.util.Dom.setStyle(MyNums[Ncount],"left",LL.left + (LL.right - LL.left) / 2 - MyNums[Ncount].innerHTML.length / 2 - 2);
			YAHOO.util.Dom.setStyle(MyNums[Ncount],"top",DefaultY - 15);
		}
		Ncount += 1;
	}//------------------------------
	Ncount =0;
	MCflag = false;
	//一時退避
	var GroupMem = 0;
	hl1 = YAHOO.util.Dom.getRegion(hLabel);
	for(i=0;i <= MyControls.length - 1;i++){
		var mcl = YAHOO.util.Dom.getRegion(MyControls[i]);
		if(hl1.left==mcl.left && hl1.top==mcl.top){
			GroupMem = i //今どのラベルを動かしてるかを記憶（グループ化ラベル）
			//alert("記憶");
			break;
		}
	}
	//一時退避2
	//グループ化ラベルの位置を決定
	//forここから-----------------------------------------
	for(j=0;j<=MyControls.length -1;j++){
		//ドラッグラベルの左側の位置を決定(hLabelの左側をはじめに決定、それ以降は減算により位置を決定していく)
		if(j < GroupMem){
			var mcl1 = YAHOO.util.Dom.getRegion(MyControls[GroupMem-1]);
			YAHOO.util.Dom.setX(MyControls[GroupMem - 1],hl1.left - (mcl1.right - mcl1.left) -10);//解像度
			YAHOO.util.Dom.setY(MyControls[GroupMem - 1],hl1.top);
			for(k=GroupMem-1;k>=0;k--){
				var mcl2 = YAHOO.util.Dom.getRegion(MyControls[k+1]);
				var mcl3 = YAHOO.util.Dom.getRegion(MyControls[k]);
				YAHOO.util.Dom.setX(MyControls[k],mcl2.left - (mcl3.right - mcl3.left) -10);//解像度んお影響本来は10
				YAHOO.util.Dom.setY(MyControls[k],mcl2.top);
			}
			j = GroupMem;
		}
		else if(j==GroupMem){
			YAHOO.util.Dom.setX(MyControls[j],hl1.left);
			YAHOO.util.Dom.setY(MyControls[j],hl1.top);
		}
		else if(j>GroupMem){
			//ドラッグラベルの右側の位置を決定
			var mclj = YAHOO.util.Dom.getRegion(MyControls[j-1]);
			YAHOO.util.Dom.setX(MyControls[j],mclj.right +10); //
			YAHOO.util.Dom.setY(MyControls[j],mclj.top);
		}
	}//forここまで------------------------------------------------
		//ラベルの一時退避
		//ドラッグラベルがレジスタ１～３にEnterしたら(なくて良い)
	
	//今のマウスポインタ位置から挿入すべき位置にカーソル表示
	if(aNum == 0){
		x = DefaultX;
		y1 = DefaultY;
		y2 = y1 + 18;
		//jg.drawLine(x+cx,y1+cy,x+cx,y2+cy);
	}
	//-------------------------------------
	for(i=0;i<=aNum-1;i++){
		var ali = YAHOO.util.Dom.getRegion(aLabel[i]);
		var ali1 = YAHOO.util.Dom.getRegion(aLabel[i+1]);
		if(i==0 && hl1.left < ali.left){
			//もし左端のラベルの左側に挿入しようとするなら
			//左端のラベルから挿入位置を計算して表示
			x = ali.left - 8;
			y1 = ali.top;
			y2 = y1 + (ali.bottom - ali.top);
			//jg.drawLine(x,y1,x,y2);
		}
		else if(i == aNum -1 && hl1.left >= ali.left){
			//もし右端に挿入しようとするなら
			//右端のラベルから挿入位置を計算して表示
			x = ali.right + 8;
			y1 = ali.top;
			y2 = y1 + (ali.bottom - ali.top);
			//jg.drawLine(x,y1,x,y2);
		}
		else if(hl.left >= ali.left && hl.left < ali1.left){
			//ラベルに挟まれた位置に挿入するなら
			//右のラベルから挿入位置を計算して表示
			x = ali1.left - 8;
			y1 = ali1.top;
			y2 = y1 + (ali1.bottom - ali1.top);
			//jg.drawLine(x,y1,x,y2);
		}
	}
	
	if(checkl != x){
		//alert("消して書くよ");
		draw2(x,y1,y2);
	}
	//**************************************************不要
	//------------------------------
	//Form_MouseMove(sender,e);
	//alert(checkl);
/*	if(checkl != x){
	jg.setColor("#dddddd");
	//alert("消すよ");
	jg.drawLine(checkl+cx,y1+cy,checkl+cx,y2+cy); //良く分からない
	jg.paint();
	jg.setColor("black");
	//alert("描くよ");
	jg.drawLine(x+cx,y1+cy,x+cx,y2+cy);
	jg.paint();
	checkl = x;
	}*/
	//alert(sender.innerHTML + "を送るよ");
	//*********************************************
	MytoFo = true;
	Form1_MouseMove(sender);
}

function draw2(x,y1,y2){
	BPen2.clear();
	BPen2.drawLine(x+cx,y1+cy,x+cx,y2+cy);
	BPen2.paint();
	checkl = x;
}
function draw3(){
	BPen2.clear();
}
//mousemoveここまで-----------------------------------------------------

//プログラムが閉じるときの処理
//主に一時ファイルの書き込み処理---------------------------------------------
function LineQuestioneForm_Closing(){
	alert("お疲れ様です!OKを押してデータの書き込み開始です。");
	new Ajax.Request(URL + 'ewrite.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	alert(req.responseText);
	window.close();
	}
	function getE(req){
		alert("失敗、何度試してもできなかったら右上の×ボタンで終了してください。そして佃にご連絡をお願いします。");
	}
}
//function exit(){
	
//}
//---------------------------------------------------------------------------
function Kugiri_add(){ //区切り追加イベント
	if(Mouse_Flag==false){
		return;
	}
	var x1 = event.x - 25; //マウスの周り25pxを削除ゾーンに
	var y1 = event.y - 25;
	var x2 = event.x + 25;
	var y2 = event.y + 25;
	for(j=0;j <= LabelNum - 1;j++){
		var Mlr = YAHOO.util.Dom.getRegion(Mylabels[j]); //付近に/があったら削除
		var Mx1 = Mlr.left;
		var Mx2 = Mlr.right;
		var My1 = Mlr.top;
		var My2 = Mlr.bottom;
		var Mx = Mx1 + (Mx2 - Mx1)/2;
		var My = My1 + (My2 - My1)/2;
		if(((x1 < Mx)&&(Mx < x2))&&((y1 < My)&&(My < y2)) && Mylabels[j].innerHTML == "/"){
		Kugiri_onedelete(Mylabels[j]);
		return;
		}
	}
	if(kugiri_num > 9){
		return;
	}
	
	//alert("ダブルクリック！");
	var body = document.getElementsByTagName("body")[0];
	LabelNum += 1;
	var k = LabelNum - 1;
		Mylabels[k] = document.createElement("div");
		//テキストノードを作成
		Mylabels[k].setAttribute("id",k);
		//YAHOO.util.Dom.setStyle(p,"background-color","orange");
		YAHOO.util.Dom.setStyle(Mylabels[k],"position","absolute");
		
		YAHOO.util.Dom.setStyle(Mylabels[k],"left",event.x);
		YAHOO.util.Dom.setStyle(Mylabels[k],"top",DefaultY);
		
		YAHOO.util.Dom.setStyle(Mylabels[k],"width","auto");
		YAHOO.util.Dom.setStyle(Mylabels[k],"font-family","Arial");
		//YAHOO.util.Dom.setStyle(Mylabels[k],"font-size",20);
		YAHOO.util.Dom.setStyle(Mylabels[k],"cursor","w-resize");
		YAHOO.util.Dom.setStyle(Mylabels[k],"color","#ff6699");
		YAHOO.util.Dom.setStyle(Mylabels[k],"font-weight","bold");
		
		dd[k] = new YAHOO.util.DD(Mylabels[k]);
		var str = document.createTextNode("/");
		//テキストノードをp要素に追加
		Mylabels[k].appendChild(str);
	    body.appendChild(Mylabels[k]);
	    	//alert( "x" + x + "r" + r +"右"+el.right);
	    //イベントハンドラの追加
	    dd[k].onMouseDown = function(e){MyLabels_MouseDown(this.getDragEl())}
	    dd[k].onMouseUp = function(e){MyLabels_MouseUp(this.getDragEl())}
	    dd[k].onDrag = function(e){MyLabels_MouseMove(this.getDragEl())}
	    
	    YAHOO.util.Event.addListener(Mylabels[i],'mouseover',MyLabels_MouseEnter);
	    
	    
	    //削除のためのイベント
	    /*dd[k].onDragDrop = function(e,id){
	    	alert("aaa");
	    	var ddEl = this.getDragEl();
	    	var xy = YAHOO.util.Dom.getXY(ddEl);
	    	if(id == "DustBox"){
	    		arr2 = Mylabels.splice(j,1);
				_delete_dom_obj(i);
	    	}
	    };*/
	    
	    YAHOO.util.Event.addListener(Mylabels[k],'mouseover',MyLabels_MouseEnter);
	    YAHOO.util.Event.addListener(Mylabels[k],'mouseout',MyLabels_MouseLeave);
	    
	    MyLabelSort(Mylabels[k],event.x,event.y);
	    
	    kugiri_num += 1;
	    
	    //相対値
	    /*var P = new Point(event.x,event.y);
	    P.x -= event.x;
	    P.Y -= event.y;*/
	    //▼マウスデータの取得
		myStop = new Date();
		mTime = myStop.getTime() - myStart.getTime();
		//var $Mouse_Data = Mouse;
		$Mouse_Data["AID"] = AID;
		$Mouse_Data["Time"] = mTime;
		$Mouse_Data["X"] = event.x;
		$Mouse_Data["Y"] = event.y;
		$Mouse_Data["DragDrop"] = -1;
		$Mouse_Data["DropPos"] = -1;
		$Mouse_Data["hlabel"] = "";
		$Mouse_Data["Label"] = "";
		$Mouse_Data["addk"] = 1;
		Mouse_Num += 1;
	
		//Tmp_Write();
		var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
				+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
	new Ajax.Request(URL + 'tmpfile.php',
	{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
	});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	document.getElementById("msg").innerHTML = req.responseText;
	}
	function getE(req){
		alert("失敗");
	}
}
//---------------------------区切り個別削除----------------------------------------------------------
function Kugiri_onedelete(hLabel){
	YAHOO.util.Dom.setStyle(hLabel,"left",10000);
			MyLabelSort(hLabel,10000,50);
		var id = hLabel.id;
		
		id -= 0;
		arr2 = Mylabels.splice(LabelNum-1,1);
		
		arr1 = dd.pop();
		arr1 = new Array();
		_delete_dom_obj(hLabel.id);
		kugiri_num -= 1;
		LabelNum -= 1;
		MyLabelSort(Mylabels[0],DefaultX,DefaultY);
		for(i=0;i <= LabelNum - 1;i++){
			Mylabels[i].id -= 0;
			if(Mylabels[i].id > id){
				var ida = Mylabels[i].id;
				ida -= 1;
				Mylabels[i].id = ida;
				dd[ida] = new YAHOO.util.DD(Mylabels[i]);
				dd[ida].onMouseDown = function(e){MyLabels_MouseDown(this.getDragEl())}
	    		dd[ida].onMouseUp = function(e){MyLabels_MouseUp(this.getDragEl())}
	    		dd[ida].onDrag = function(e){MyLabels_MouseMove(this.getDragEl())}
			} 
			//alert(Mylabels[i].innerHTML + Mylabels[i].id);
		}
		DPos = 0;
		IsDragging = false;
		//▼マウスデータの取得
		myStop = new Date();
		mTime = myStop.getTime() - myStart.getTime();
		//var $Mouse_Data = Mouse;
		$Mouse_Data["AID"] = AID;
		$Mouse_Data["Time"] = mTime;
		$Mouse_Data["X"] = event.x;
		$Mouse_Data["Y"] = event.y;
		$Mouse_Data["DragDrop"] = 0;
		$Mouse_Data["DropPos"] = DPos;
		$Mouse_Data["hlabel"] = id;
		$Mouse_Data["Label"] = "";
		$Mouse_Data["addk"] = 3;
		Mouse_Num += 1;
		var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
					+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
					+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
					+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
					+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
					+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
					+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
					+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
					+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
		
		new Ajax.Request(URL + 'tmpfile.php',
	{
			method: 'get',
			onSuccess: getDe,
			onFailure: getE,
			parameters: $params
	});
		//ドラッグ開始地点の保存
	function getDe(req){
		
	//alert(req.responseText);
	//MyLabelSort(Mylabels[LabelNum-1],event.x,event.y);
	}
	function getE(req){
		alert("era-");
	}
		return;
}
//区切り個別削除ここまで--------------------------------------
//-----------区切り全削除----------------------------------------------------------------------------
function Kugiri_delete(){
	/*for(i=0;i<=LabelNum-1;i++){
		if(Mylabels[i].innerHTML == "/"){
			var count = 0;
			var r = i;
			while(Mylabels[r].innerHTML=="/" && r < LabelNum){
				_delete_dom_obj(Mylabels[r].id);
				count += 1;
				r += 1;
				//if(r >= LabelNum -1){
				//	break;
				//}
			}
			if(i==LabelNum -1){
				break;
			}
			for(k=i;k <= LabelNum -1 - count;k++){
				Mylabels[k] = Mylabels[k+count]//区切りラベルの部分の配列を詰める
			}
		}
	}*/
	for(i=LabelNum-kugiri_num;i<=LabelNum-1;i++){
		//alert(i);
		//alert(LabelNum);
		for(j=0;j <= LabelNum - 1;j++){
			if(Mylabels[j].id == i){
				//_delete_dom_obj(Mylabels[j].id);
				//if(j==LabelNum-1){
				//	break;
				//}
				//Mylabels[j] = Mylabels[j+1]//区切りラベルの部分の配列を詰める
				arr2 = Mylabels.splice(j,1);
				arr1 = dd.splice(j,1);
				arr2 = new Array();
				arr1 = new Array();
				_delete_dom_obj(i);
				//LabelNum -= 1;
				break;
			}
		}
	}
	
	 //相対値
	    /*var P = new Point(event.x,event.y);
	    P.x -= event.x;
	    P.y -= event.y;*/
	    //▼マウスデータの取得
		myStop = new Date();
		mTime = myStop.getTime() - myStart.getTime();
		//var $Mouse_Data = Mouse;
		$Mouse_Data["AID"] = AID;
		$Mouse_Data["Time"] = mTime;
		$Mouse_Data["X"] = event.x;
		$Mouse_Data["Y"] = event.y;
		$Mouse_Data["DragDrop"] = -1;
		$Mouse_Data["DropPos"] = -1;
		$Mouse_Data["hlabel"] = "";
		$Mouse_Data["Label"] = "";
		$Mouse_Data["addk"] = 2;
		Mouse_Num += 1;
	
		//Tmp_Write();
		var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
				+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
	new Ajax.Request(URL + 'tmpfile.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	//alert(req.responseText);
	}
	function getE(req){
		alert("失敗");
	}
	
	LabelNum -= kugiri_num;//区切りラベルがないときのラベルの数にする
	//区切りラベルを取り除く
	kugiri_num = 0;
	//alert("ラベルの数は"+ Mylabels.length);
	MyLabelSort(Mylabels[0],DefaultX,DefaultY);
}
//データの書き込み-----------------------------------------------------------------------
//一時ファイルによる処理
function Data_Write(){
	myStop2 = new Date();
	mTime2 = myStop2.getTime() - myStart2.getTime();
	//alert(mTime2);
	var $params = 'param1=' + encodeURIComponent(ResAns)
				+ '&param2=' + encodeURIComponent(mTime2);
	new Ajax.Request(URL + 'tmpfile3.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	document.getElementById("msg").innerHTML = req.responseText;
	document.getElementById("Button2").disabled = false;
	}
	function getE(req){
		alert("失敗");
	}
	
	Mouse_Num = 0;
}
//解答情報(linedata)の保存----------------------------------------------
function Save_data(){
	myStop = new Date();
	mTime = myStop.getTime() - myStart.getTime();
	
	$QAData["QN"] = $Ques_Num;
	$QAData["ADate"] = Answertime;
	$QAData["TF"] = TF;
	$QAData["Time"] = mTime;
	$QAData["FQues"] = StartQues;
	$QAData["AID"] = AID;
	AID += 1;
	
	var $params = 'param1=' + encodeURIComponent($QAData["QN"])
				+ '&param2=' + encodeURIComponent($QAData["ADate"])
				+ '&param3=' + encodeURIComponent($QAData["TF"])
				+ '&param4=' + encodeURIComponent($QAData["Time"])
				+ '&param5=' + encodeURIComponent($QAData["FQues"])
				+ '&param6=' + encodeURIComponent($QAData["AID"]);
	new Ajax.Request(URL + 'tmpfile2.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	//alert(req.responseText);
	}
	function getE(req){
		alert("失敗");
	}
	
	Data_Write();
}
//----------------------------------------------------------------
/*function Tmp_Write(){
	var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"]);
	new Ajax.Request(URL + 'tmpfile.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	//ドラッグ開始地点の保存
	function getA(req){
	alert(req.responseText);
	}
	function getE(req){
		alert("失敗");
	}
}*/
//決定--------------------------------------------------------------------
function Button1_Click()
{
	if(Mouse_Flag == false){
		return;
	}
	Mouse_Flag = false;
	//区切りラベルを削除
	if(kugiri_num > 0){
	Kugiri_delete();
	}
	var P = new Point(0,0);
	
	myStop = new Date();
	mTime = myStop.getTime() - myStart.getTime();
	
	//解答したのでマウスの動きをとるのをやめる
	//Mouse_Flag = false;
	myCheck(0);//ストップウォッチを止める
	
	//ラベルを移動できないようにする
	/*for(i=0;i<=LabelNum-1;i++){
		dd[i].lock();
	}*/
	
	//グループ化されたコントロールの初期化
	for(i=0;i<=MyControls.length-1;i++){
		if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","#ff6699");
				}else{
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	}
	//削除
	MyControls.splice(0,MyControls.length-1);
	
	P.x = event.x;
	P.y = event.y;
	
	/*P.x -= event.screen.X;
	P.x -= event.screen.Y;*/
	
	var $Mouse_Data = Mouse;
	$Mouse_Data["AID"] = AID;
	$Mouse_Data["Time"] = mTime;
	$Mouse_Data["X"] = P.x;
	$Mouse_Data["Y"] = P.y;
	$Mouse_Data["DragDrop"] = -1;
	$Mouse_Data["DropPos"] = -1;
	$Mouse_Data["hlabel"] = "";
	$Mouse_Data["Label"] = "";
	$Mouse_Data["addk"] = 0;
	Mouse_Num += 1;
	
	var $params = 'param1=' + encodeURIComponent($Mouse_Data["AID"])
				+ '&param2=' + encodeURIComponent($Mouse_Data["Time"])
				+ '&param3=' + encodeURIComponent($Mouse_Data["X"])
				+ '&param4=' + encodeURIComponent($Mouse_Data["Y"])
				+ '&param5=' + encodeURIComponent($Mouse_Data["DragDrop"])
				+ '&param6=' + encodeURIComponent($Mouse_Data["DropPos"])
				+ '&param7=' + encodeURIComponent($Mouse_Data["hlabel"])
				+ '&param8=' + encodeURIComponent($Mouse_Data["Label"])
				+ '&param9=' + encodeURIComponent($Mouse_Data["addk"]);
	new Ajax.Request(URL + 'tmpfile.php',
{
		method: 'get',
		onSuccess: getA,
		onFailure: getE,
		parameters: $params
});
	//▲マウスデータの取得
	function getA(req){
	//alert(req.responseText);
	}
	function getE(req){
		alert("失敗");
	}
	
	//先頭の文字列を大文字に変換
	str1 = Mylabels[0].innerHTML.substr(0,1);
	str2 = Mylabels[0].innerHTML.substr(1);
	Mylabels[0].innerHTML = str1.toUpperCase() + str2; //先頭の文字が大文字に
	
	//ピリオドまたはクエスチョンを最後につける
	Mylabels[LabelNum-1].innerHTML += PorQ;
	
	//自分の解答を文字列に格納
	for(i=0;i<=LabelNum-1;i++){
		//区切りラベルは解答に入れない
		if(Mylabels[i].innerHTML == "/"){
			continue;
		}
		MyAnswer += Mylabels[i].innerHTML + " ";
	}
	MyAnswer = MyAnswer.replace(/^\s+|\s+$/g, ""); //前後の空白削除
	
	ResAns += 1;
	AllResAns += 1;
	
	if(MyAnswer == Answer || MyAnswer == Answer1 || MyAnswer == Answer2){
		document.getElementById("RichTextBox3").innerHTML = "正誤：○";
		YAHOO.util.Dom.setStyle("RichTextBox3","color","red");
		//DBに登録するときは１とするように変更が必要
		TF = 1;
		
		CorrectAns += 1;
		AllCorrectAns += 1;
	}
	else{
		document.getElementById("RichTextBox3").innerHTML = "正誤：×";
		YAHOO.util.Dom.setStyle("RichTextBox3","color","blue");
		//DBに登録するときは0とするように変更が必要
		TF = 0;
		document.getElementById("RichTextBox2").innerHTML = "回答</br>"+Answer;
		YAHOO.util.Dom.setStyle("RichTextBox2","display","block");
	}

	CorrectAnsRate = CorrectAns / ResAns * 100 //今回の正解率の計算
	AllCorrectAnsRate = AllCorrectAns / AllResAns * 100 //全体の正解率の計算
	
	CorrectAnsRate = Math.round(CorrectAnsRate * 100) / 100; //小数第三位を四捨五入するため
	AllCorrectAnsRate = Math.round(AllCorrectAnsRate * 100) / 100; //小数第三位を四捨五入するため
	
	document.getElementById("ListBox1").innerHTML = "全体 ( " + AllCorrectAns + " / " + AllResAns + "  " + AllCorrectAnsRate + "% )</br>"
		+ "今回 ( " + CorrectAns + " / " + ResAns + "  " + CorrectAnsRate + "% )";
	
	Save_data();
	
	YAHOO.util.Dom.setStyle("Button1","display","none");
	YAHOO.util.Dom.setStyle("RichTextBox3","display","block");
	YAHOO.util.Dom.setStyle("Button2","display","block");
	YAHOO.util.Dom.setStyle("TextBox1","display","block");
	YAHOO.util.Dom.setStyle("Label2","display","block");
	document.getElementById("Buttonl").disabled = false;
	//document.getElementById("Button2").disabled = false;
}
//-----------------------------------------------------------------------------------
//次の問題
function Button2_Click()
{
	if(Mouse_Flag == true){
		return;
	}
	document.getElementById("Button2").disabled = true;
	document.getElementById("Buttonl").disabled = true;
	YAHOO.util.Dom.setStyle("Button1","display","block");
	YAHOO.util.Dom.setStyle("RichTextBox2","display","none");
	YAHOO.util.Dom.setStyle("RichTextBox3","display","none");
	YAHOO.util.Dom.setStyle("Button2","display","none");
	YAHOO.util.Dom.setStyle("TextBox1","display","none");
	YAHOO.util.Dom.setStyle("Label2","display","none");
	for(i=0;i<=LabelNum-1;i++){
	_delete_dom_obj(i);
	}
	for(i=0;i>=-MyNums.length+1;i--){
	//alert(i);	
	_delete_dom_obj(i);
	}
	MyNums.splice(0,MyNums.length-1);
	MyAnswer = "";
	StartQues = "";
	
	FixLabels = new Array(); //固定ラベル
	FixNum = new Array(); //固定ラベルの番号
    FixText = new Array(); //固定ラベルのタグを含むテキスト

	
	setques();
}
//解答ラベルの削除-----------------------------
function _delete_dom_obj( id_name ){

	var dom_obj=document.getElementById(id_name);
	var dom_obj_parent=dom_obj.parentNode;

	//alert('ID: '+dom_obj.getAttribute('id')+' を削除します');
	dom_obj_parent.removeChild(dom_obj);
}
//---------------------------------------------
//スタートボタン-------------------------
function Button3_Click(){
	//alert("スタート!");
	//NewQuesNum = //setquesでやろう
	
	//Redim Mouse_Data(0)
	if(Mouse_Flag == true){
		return;
	}
	setques();
	document.getElementById("Button3").disabled = true;
	document.getElementById("Buttonl").disabled = true;
	YAHOO.util.Dom.setStyle("Button3","display","none");
	YAHOO.util.Dom.setStyle("Button1","display","block");
	//YAHOO.util.Dom.setStyle("Button5","display","block"); //区切りボタン
}
//----------------------------------------


</script>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<body bgcolor = #efffef onLoad = "ques_Load()" onMouseDown = "Form1_MouseDown()" onMouseUp = "Form1_MouseUp()" ondblclick = "Kugiri_add()">
<!--スタートボタン-->
<input type = "button"
	id = "Button3"
	value="スタート"
	onclick="Button3_Click()"
	style="width:80px;height:36px;position:absolute;left:749px;top:27px"/>
<!--決定ボタン-->
<input type = "button"
	id = "Button1"
	value="決定"
	onclick="Button1_Click()"
	style="width:80px;height:36px;position:absolute;left:749px;top:27px;display:none"/>
<!--次の問題ボタン-->
<input type = "button"
	id = "Button2"
	value="次の問題"
	onclick="Button2_Click()"
	style="width:75px;height:33px;position:absolute;left:749px;top:27px;display:none"/>
<!--終了ボタン-->
<input type = "button"
	id = "Buttonl"
	value="終了"
	onclick="LineQuestioneForm_Closing()"
	style="width:75px;height:20px;position:absolute;left:768px;top:480px;display:block"/>
<!--区切り削除ボタン-->
<input type = "button"
	id = "Button5"
	value="区切り全削除"
	onclick="Kugiri_delete()"
	style="width:90px;height:32px;position:absolute;left:10px;top:162px;"/>
<!--ごみ箱
<div id = "DustBox" style="background-color:#dfffdf;position:absolute;
     left:650px;top:155px;width:50;height:50;border-style:inset">
                                   	   削除ゾーン</div>-->
<!--日本文-->
<div id = "RichTextBox1" style="background-color:#ccff99;position:absolute;
     left:12;top:27;width:731;height:36;border-style:inset">
                                   	   ここに日本文が表示されます</div>
<!--回答-->
<div id = "RichTextBox2" style="background-color:#a1ffa1;position:absolute;
	 left:12;top:402;width:650;height:67;border-style:inset;display:none">ここに回答を表示</div>
<!--正誤-->
<div id = "RichTextBox3" style="background-color:#a1ffa1;position:absolute;
	 left:668;top:402;width:80;height:34;border-style:inset;display:none">正誤を表示</div>
<!--解答時間-->
<div id = "TextBox1"  style="background-color:#a1ffa1;position:absolute;
	 left:778;top:446;width:65;height:23;border-style:inset;display:none">解答時間</div>
<!--正解率-->
<div id = "ListBox1" style="background-color:#a1ffa1;position:absolute;
	 left:668;top:356;width:175;height:36;border-style:inset">正解率</div>
<div id = "Label2" style="position:absolute;
	 left:700;top:345;width:175;height:36;font-size:10;">(正解数/解答数 正解率)</div>
<!--解答時間-->
<div id = "Label2" style="position:absolute;
	 left:670;top:456;width:77;height:12;font-size:12;display:none">解答時間(秒)</div>
<!--機能説明-->
<div id = "Label2" style="position:absolute;
	 left:50;top:200;width:300;height:50;font-size:12;background-color:#a1ffa1;">
	 	 操作説明</br>
	 	 ラベルの移動：ドラッグ＆ドロップ</br>
	 	 区切りの追加：挿入したい場所にダブルクリック</br>
	 	 グループ化：ラベルがないところでドラッグ</br></div>
<!--キャンバス-->
<div id="myCanvas" style="position:absolute;top:0;left:0;height:513px;width:861px;z-index:-1"></div>
<!--メモ-->
<div id="msg" style="position:absolute;
	 left:50;top:300;width:500;height:30;font-size:12;background-color:#a1ffa1;display:none"></div>
<!--固定情報-->
<div id="Fixmsg" style="position:absolute;
	 left:360;top:200;width:200;height:30;font-size:12;background-color:#a1ffa1;display:block">-情報-</div>
</body>
</html>