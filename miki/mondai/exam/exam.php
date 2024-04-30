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
if($_SESSION["examflag"] == 2){
	require"overlap.php";
	exit;
}else{
 $_SESSION["examflag"] = 1;
 $_SESSION["page"] = "exam";
}
//echo $_SESSION["MemberName"];
?>
<html>
<head>
<title>マウス検査</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script type="text/javascript"
	    src="../yui/build/yahoo/yahoo-min.js"></script>
<script type="text/javascript"
		src="../yui/build/event/event-min.js"></script>
<script type="text/javascript"
		src="../yui/build/dom/dom-min.js"></script>
<script type="text/javascript"
		src="../prototype.js"></script>
<script type="text/javascript"
		src="../dateformat.js"></script>
<script type="text/javascript"
		src="../wz_jsgraphics.js"></script>
<script type="text/javascript"
		src="../yui/build/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript"
		src="../yui/build/animation/animation-min.js"></script>
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
    myS2 = Math.floor(myTime2/1000);                 // '秒'取得
    myMS2 = myTime2%1000;                        // 'ミリ秒'取得
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
MytoFo = new Boolean(false); //IEのバグ対応。MyLabels_MouseMove→Form1_onMouseMoveのため
var DragL; //ドラッグ中のラベルの引渡し。
var URL = 'http://lmo.cs.inf.shizuoka.ac.jp/~miki/mondai/exam/' //サーバー用
//var URL = 'http://localhost/test/exam/' //ローカル用

var Count = 0; //1～10回目まで，現在の回数目．

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
	alert(req.responseText + "\nここでは、あなたのマウスの挙動を検査します。\nランダムに表示された数字をドラッグ＆ドロップで指示された順に並び変えてください。");
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
	document.onselectstart = "return false";
	//document.unselectable = on;
//DBから引用--------------------------------------------------
function getError(res)
{
	alert("失敗");
	window.close();
}
//=============elinedatamouseがなかったら作成============
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
		if(AID == "AID抽出エラー（マウス）" || AID == "" || AID == "AID抽出エラー"){	
			AID = 0;
		}else{
			AID -= 0;//数値化
			AID += 1;
		}
		//alert(AID);
		if(AID!=0){
			document.getElementById("Button3").disabled = true;
			document.getElementById("Buttonl").disabled = false;
			alert("すでに検査を終了しています。\n終了ボタンを押して終了してください");
		}
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
				AllCorrectAnsRate = Math.round(AllCorrectAnsRate * 100) / 100; //小数第四位を四捨五入するため
				document.getElementById("ListBox1").innerHTML = "全体 ( " + AllCorrectAns + " / " + AllResAns + "  " + parseInt(AllCorrectAnsRate) + "% )</br>"
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

	//$Ques_Num = 0; //問題を指定したいとき用
	//$Ques_Num =AID - (Math.floor(AID/10) * 10);		//AIDの下一けたを抽出→NID
	$Ques_Num = Count;	//同じ問題は1度だけ　間違えたらもう1度
	$q = "q";
	//alert($Ques_Num);
	var $params = 'param1=' + encodeURIComponent($Ques_Num)
				+ '&param2=' + encodeURIComponent($q);
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
	//PorQ = req.responseText.charAt(req.responseText.length-1); //ピリオド、または？を抜き取る
	Question = req.responseText;//.substring(0,req.responseText.length-1); //ピリオド抜きの問題文
	//str1 = req.responseText.substr(0,1);
	//str2 = req.responseText.substr(1);
	//Answer = str1.toUpperCase()+str2; //完全な答え
	Mylabels = Question.split(" "); //スペースで単語に区切る
			
			$ans = "ans";
			$params = 'param1=' + encodeURIComponent($Ques_Num)
					+ '&param2=' + encodeURIComponent($ans);
		new Ajax.Request(URL + 'dbsyori.php', //本番用
	{
		method: 'get',
			onSuccess: getAns,
			onFailure: getError,
			parameters: $params
		});
		function getAns(Ans) //答えの取得
		{
			msg.innerHTML = Ans.responseText;
			//alert(Ans.responseText);
			Answer = Ans.responseText; //答え
		//alert("完了");
	//Mylabels.random();			//マウス検査だから同じ並びに対する動き保存。つまりランダムにしない
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
		//テキストノードを作成
		p.setAttribute("id",i);
		//YAHOO.util.Dom.setStyle(p,"background-color","orange");
		YAHOO.util.Dom.setStyle(p,"position","absolute");
		if(i<1){
		YAHOO.util.Dom.setStyle(p,"left",DefaultX);
		YAHOO.util.Dom.setStyle(p,"top",DefaultY);
		}
		else{
		YAHOO.util.Dom.setStyle(p,"left",el.right + 17);
		YAHOO.util.Dom.setStyle(p,"top",DefaultY);
		}
		YAHOO.util.Dom.setStyle(p,"width","auto");
		YAHOO.util.Dom.setStyle(p,"font-family","Arial");
		YAHOO.util.Dom.setStyle(p,"cursor","w-resize");
		//YAHOO.util.Dom.setStyle(p,"font-size","12px");
		
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
		
		/*for(f=0;f<=FixNum.length-1;f++){
			if(p.innerHTML == FixLabels[f]){
				YAHOO.util.Dom.setStyle(p,"border","solid 1px blue");
			}
		}*/
		
		//p要素をbody要素に追加
		Mylabels[i] = p;
	    body.appendChild(Mylabels[i]);
		//body.appendChild(p);
	    el = YAHOO.util.Dom.getRegion(p);
	    	//alert( "x" + x + "r" + r +"右"+el.right);
	    //イベントハンドラの追加
	    dd[i].onMouseDown = function(e){MyLabels_MouseDown(this.getDragEl())}
	    dd[i].onMouseUp = function(e){MyLabels_MouseUp(this.getDragEl())}
	    dd[i].onDrag = function(e){MyLabels_MouseMove(this.getDragEl())}
	    
	    YAHOO.util.Event.addListener(Mylabels[i],'mouseover',MyLabels_MouseEnter);
	    YAHOO.util.Event.addListener(Mylabels[i],'mouseout',MyLabels_MouseLeave);
	}
	//-------------------------------------条件抽出
			var $ter = "term";
	$params = 'param1=' + encodeURIComponent($Ques_Num)
				+ '&param2=' + encodeURIComponent($ter);
	 new Ajax.Request(URL + 'dbsyori.php',
{
	method: 'get',
		onSuccess: getterm,
		onFailure: getError,
		parameters: $params
});
	function getterm(term){
		var TM = term.responseText.split("#");
		if(TM[0] == 'small'){
			var TText = '小さい順';
		}
		else if(TM[0] == 'big'){
			var TText = '大きい順';
		}
		else if(TM[0] == 'near'){
			var TText = TM[1] + 'に近い順';
		}
		else if(TM[0] == 'far'){
			var TText = TM[1] + 'に遠い順';
		}
		document.getElementById("RichTextBox1").innerHTML = "条件:" +　TText;
		Mouse_Flag = true;

	}
		}//Ans関数ここまで--------------------------------------------------------
}
//--関数getresponseここまで---------------------------------------
function getError(req)
{
	alert("失敗");
	window.close;
}
//alert("saki");
//マウス取得スタート
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
			YAHOO.util.Dom.setStyle(MyControls[i],"color","yellow");
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
	//選択範囲の中にラベルがあればグループ化する
	//青色への色変えも
	//左上,右上,左下,右下の４方向からのドラッグに対応------------------------------------------
	for(i=0;i<=LabelNum-1;i++){
		//一時退避・・・なくて良い
		MLi = YAHOO.util.Dom.getRegion(Mylabels[i]);
		if(sPos.x <= ePos.x && sPos.y <= ePos.y){  //左上
			if((sPos.x < MLi.right && sPos.y < MLi.bottom) && (ePos.x > MLi.left && ePos.y > MLi.top)){
				MyControls.push(Mylabels[i]);
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
		else if(sPos.x <= ePos.x && sPos.y >= ePos.y){//左下
			if((sPos.x < MLi.right && sPos.y > MLi.top) && (ePos.x > MLi.left && ePos.y < MLi.bottom)){
				MyControls.push(Mylabels[i]);
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
		else if(sPos.x > ePos.x && sPos.y < ePos.y){//右上
	 		if((sPos.x > MLi.left && sPos.y < MLi.bottom) && (ePos.x < MLi.right && ePos.y > MLi.top)){
	 			MyControls.push(Mylabels[i]);
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
		else if(sPos.x > ePos.x && sPos.y > ePos.y){//右下
	 	 	if((sPos.x > MLi.left && sPos.y > MLi.top) && (ePos.x < MLi.right && ePos.y < MLi.bottom)){
	 	 		MyControls.push(Mylabels[i]);
				YAHOO.util.Dom.setStyle(Mylabels[i],"color","blue");
			}
		}
	}//----------------------------------------------------------------------------------------
			//alert(MyControls.length);
		for(i=0;i<=LabelNum-1;i++){//--------------
			if(MyControls.indexOf(Mylabels[i]) == -1){
				if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","yellow");
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
	}
	if(MV==true){
	 draw();	 
	 ePos.x = event.x + cx;
	 ePos.y = event.y + cy;
	}
	//--------------------別のマウスムーブの取り込み--------------------------------------
	var P = new Point(0,0);
	
	if(Mouse_Flag==true){
		//マウスの位置座標を取得
		P.x = event.x;
		P.y = event.y;
		//位置座標を相対値に変換
		//alert(event.screenX);
		/*P.x -= event.screenX;
		P.y -= event.screenY;
		alert(event.x);
		alert(event.screenX);*/
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
	 		}else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","yellow");
				}else{
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}//左上ここまで--------------------------------------------------
	 	else if(sPos.x <= ePos.x && sPos.y >= ePos.y){//左下
	 		if((sPos.x < MLi.right && sPos.y > MLi.top) && (ePos.x > MLi.left && ePos.y < MLi.bottom)){
	 			YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","yellow");
				}else{
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	} else if(sPos.x > ePos.x && sPos.y < ePos.y){//右上
	 		if((sPos.x > MLi.left && sPos.y < MLi.bottom) && (ePos.x < MLi.right && ePos.y > MLi.top)){
	 			YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","yellow");
				}else{
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}else if(sPos.x > ePos.x && sPos.y > ePos.y){//右下
	 	 	if((sPos.x > MLi.left && sPos.y > MLi.top) && (ePos.x < MLi.right && ePos.y < MLi.bottom)){
	 	 	 	YAHOO.util.Dom.setStyle(Mylabels[i],"color","red");
	 		}else{
	 			if(Mylabels[i].innerHTML == "/"){
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","yellow");
				}else{
					YAHOO.util.Dom.setStyle(Mylabels[i],"color","black");
				}
	 		}
	 	}
	 }//forここまで-----------------------------------------
 }
 
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
return Mylabels;
}

//ソート関数ここまで------------------------------------------------------

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
			if(Mylabels[i].innerHTML == "/"){
				YAHOO.util.Dom.setStyle(MyControls[i],"color","yellow");
			}else{
				YAHOO.util.Dom.setStyle(MyControls[i],"color","black");
			}
			YAHOO.util.Dom.setStyle(MyControls[i],"text-decoration","none");
		}
		MyControls = new Array();
	}else{
		for(i=0;i<=MyControls.length-1;i++){
			YAHOO.util.Dom.setStyle(MyControls[i],"text-decoration","underline");
		}
	}
	YAHOO.util.Dom.setStyle(this,"text-decoration","underline");
	//alert(MyControls.length);
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

	draw3();
	
	
	var Dpos = 0;
	var P = new Point(0,0);
	var hl = YAHOO.util.Dom.getRegion(hLabel);
	P.x = hl.left;
	P.y = hl.top;
	
	Mylabels = MyLabelSort(sender,event.x,event.y);
	var Kcount = 0;
	for(i = 0;i <= FixNum.length - 1;i++){
		Kcount = 0;
			for(j = 0;j <= LabelNum - 1;j++){
				/*if(Mylabels[j].innerHTML == FixLabels[i] && j == FixNum[i] + Kcount){
					YAHOO.util.Dom.setStyle(Mylabels[j],"border","solid 1px orange");
				}else if(Mylabels[j].innerHTML == FixLabels[i]){
					YAHOO.util.Dom.setStyle(Mylabels[j],"border","solid 1px blue");
				}else*/ if(Mylabels[j].innerHTML == "/"){
					Kcount += 1;
				}
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
		
	//相対位置の計算
	
	var hl = YAHOO.util.Dom.getRegion(hLabel);
	DestX = hl.left + event.x - DiffPoint.x;
	DestY = hl.top + event.y - DiffPoint.y;
	
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
		}
		else if(i == aNum -1 && hl1.left >= ali.left){
			//もし右端に挿入しようとするなら
			//右端のラベルから挿入位置を計算して表示
			x = ali.right + 8;
			y1 = ali.top;
			y2 = y1 + (ali.bottom - ali.top);
		}
		else if(hl.left >= ali.left && hl.left < ali1.left){
			//ラベルに挟まれた位置に挿入するなら
			//右のラベルから挿入位置を計算して表示
			x = ali1.left - 8;
			y1 = ali1.top;
			y2 = y1 + (ali1.bottom - ali1.top);
		}
	}
	
	if(checkl != x){
		//alert("消して書くよ");
		draw2(x,y1,y2);
	}
	//------------------------------
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
	alert("お疲れ様です!データを書き込んでいます、しばらくお待ちください。");
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
		YAHOO.util.Dom.setStyle(Mylabels[k],"color","yellow");
		YAHOO.util.Dom.setStyle(Mylabels[k],"font-weight","bold");
		
		dd[k] = new YAHOO.util.DD(Mylabels[k]);
		//alert(k);
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
	    
	    YAHOO.util.Event.addListener(Mylabels[k],'mouseover',MyLabels_MouseEnter);
	    
	    MyLabelSort(Mylabels[k],event.x,event.y);
	    
	    kugiri_num += 1;
	    
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
	for(i=LabelNum-kugiri_num;i<=LabelNum-1;i++){
		for(j=0;j <= LabelNum - 1;j++){
			if(Mylabels[j].id == i){
				arr2 = Mylabels.splice(j,1);
				arr1 = dd.splice(j,1);
				arr2 = new Array();
				arr1 = new Array();
				_delete_dom_obj(i);
				break;
			}
		}
	}
	
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
	Mouse_Num = 0;
}
//解答情報(elinedata)の保存----------------------------------------------
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

//決定--------------------------------------------------------------------
function Button1_Click()
{
	if(Mouse_Flag == false){
		return;
	}
	//区切りラベルを削除
	if(kugiri_num > 0){
	Kugiri_delete();
	}
	var P = new Point(0,0);
	
	myStop = new Date();
	mTime = myStop.getTime() - myStart.getTime();
	
	//解答したのでマウスの動きをとるのをやめる
	Mouse_Flag = false;
	myCheck(0);//ストップウォッチを止める
	
	//ラベルを移動できないようにする
	/*for(i=0;i<=LabelNum-1;i++){
		dd[i].lock();
	}*/
	
	//グループ化されたコントロールの初期化
	for(i=0;i<=MyControls.length-1;i++){
		if(Mylabels[i].innerHTML == "/"){
			YAHOO.util.Dom.setStyle(Mylabels[i],"color","yellow");
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
	//Mylabels[LabelNum-1].innerHTML += PorQ;
	
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
	Count += 1;
	
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
		Count -= 1;
		alert("間違えたのでもう１度同じ問題を解いてもらいます");
		//alert(document.getElementById("RichTextBox2").innerHTML);
		document.getElementById("RichTextBox2").innerHTML = "回答</br>"+Answer;
		YAHOO.util.Dom.setStyle("RichTextBox2","display","block");
	}

	CorrectAnsRate = CorrectAns / ResAns * 100 //今回の正解率の計算
	AllCorrectAnsRate = AllCorrectAns / AllResAns * 100 //全体の正解率の計算
	
	//document.getElementById("ListBox1").innerHTML = "全体 ( " + AllCorrectAns + " / " + AllResAns + "  " + parseInt(AllCorrectAnsRate) + "% )</br>"
	//	+ "今回 ( " + CorrectAns + " / " + ResAns + "  " + parseInt(CorrectAnsRate) + "% )";
	
	document.getElementById("ListBox1").innerHTML = "全体 ( " + AllCorrectAns + " / " + AllResAns + "  回 )</br>"
		+ "今回 (  " + CorrectAns + " / " + ResAns + "  回 )";
	
	Save_data();
	
	YAHOO.util.Dom.setStyle("Button1","display","none");
	YAHOO.util.Dom.setStyle("RichTextBox3","display","block");
	YAHOO.util.Dom.setStyle("Button2","display","block");
	//YAHOO.util.Dom.setStyle("TextBox1","display","block");
	YAHOO.util.Dom.setStyle("Label2","display","block");
	//document.getElementById("Buttonl").disabled = false;
	if(Count < 10){
		document.getElementById("Button2").disabled = false;
	}else{
		document.getElementById("Buttonl").disabled = false;
		alert("お疲れさまでした。\n全問解答が終了したので左下の終了ボタンをクリックしてください。");
	}
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
	
	MyAnswer = "";
	StartQues = "";
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
	if(Mouse_Flag == true){
		return;
	}
	setques();
	document.getElementById("Button3").disabled = true;
	document.getElementById("Buttonl").disabled = true;
	YAHOO.util.Dom.setStyle("Button3","display","none");
	YAHOO.util.Dom.setStyle("Button1","display","block");
}
//----------------------------------------
//区切り追加----------------
function Button5_Click(){
	alert("追加!");
}
//--------------------------

</script>
</head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<body bgcolor = #ccffff onLoad = "ques_Load()" onMouseDown = "Form1_MouseDown()" onMouseUp = "Form1_MouseUp()" ondblclick = "Kugiri_add()">
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
	value="区切り削除"
	onclick="Kugiri_delete()"
	style="width:90px;height:32px;position:absolute;left:10px;top:162px;"/>
<!--日本文-->
<div id = "RichTextBox1" style="background-color:#cccccc;position:absolute;
     left:12;top:27;width:731;height:36;border-style:inset;display:block">
                                   	   指定された条件に合うように数字を並べ替えてください</div>
<!--回答-->
<div id = "RichTextBox2" style="background-color:#66ffff;position:absolute;
	 left:12;top:402;width:650;height:67;border-style:inset;display:none">ここに回答を表示</div>
<!--正誤-->
<div id = "RichTextBox3" style="background-color:#66ffff;position:absolute;
	 left:668;top:402;width:80;height:34;border-style:inset;display:none">正誤を表示</div>
<!--解答時間-->
<div id = "TextBox1"  style="background-color:#66ffff;position:absolute;
	 left:778;top:446;width:65;height:23;border-style:inset;display:none">解答時間</div>
<!--正解率-->
<div id = "ListBox1" style="background-color:#66ffff;position:absolute;
	 left:668;top:356;width:175;height:36;border-style:inset">正解率</div>
<div id = "Label2" style="position:absolute;
	 left:700;top:345;width:175;height:36;font-size:10;">(正解数/解答数)</div>
<!--解答時間-->
<div id = "Label2" style="position:absolute;
	 left:670;top:456;width:77;height:12;font-size:12;display:none">解答時間(秒)</div>
<!--機能説明-->
<div id = "Label2" style="position:absolute;
	 left:50;top:200;width:300;height:50;font-size:12;background-color:#66ffff;">
	 	 操作説明</br>
	 	 ラベルの移動：ドラッグ＆ドロップ</br>
	 	 区切りの追加：挿入したい場所にダブルクリック</br>
	 	 グループ化：ラベルがないところでドラッグ</br></div>
<!--キャンバス-->
<div id="myCanvas" style="position:absolute;top:0;left:0;height:513px;width:861px;z-index:-1"></div>
<!--メモ-->
<div id="msg" style="position:absolute;
	 left:50;top:300;width:500;height:30;font-size:12;background-color:#66ffff;display:none"></div>
<!--固定情報-->
<div id="Fixmsg" style="position:absolute;
	 left:360;top:200;width:200;height:30;font-size:12;background-color:#66ffff;display:block">-情報-</div>
</body>
</html>