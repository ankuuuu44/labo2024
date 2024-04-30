<!DOCTYPE html PUBLIC "-//W3c//DTD HTML 4.01 Transitional//EN">

<?php
//ログイン関連
error_reporting(E_ALL);
session_start();
if(!isset($_SESSION["MemberName"])){
require"notlogin";
session_destroy();
exit;
}
if($_SESSION["examflag"] == 1){
	require"overlap.php";
	exit;
}else{
$_SESSION["examflag"] = 2;
$_SESSION["page"] = "ques";
}
?>

<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>並び替え問題プログラム</title>

<!--読み込み関連-->
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

</head>

<body>

<script type="text/javascript">
    //ストップウォッチ関数
    myButton = 0;            // [Start]/[Stop]のフラグ
    var myStart;
    var myStop;
    function myCheck(myFlg) {
        if (myButton == 0) {      // Startボタンを押した
            myStart = new Date();  // スタート時間を退避
            myButton = 1;
            myInterval = setInterval("myCheck(1)", 1);
        } else {                 // スタート実行中
            if (myFlg == 0) {       // Stopボタンを押した
                myButton = 0;
                clearInterval(myInterval);
            }
            myStop = new Date();  // 経過時間を退避
            myTime = myStop.getTime() - myStart.getTime(); // 通算ミリ秒計算
            myS = Math.floor(myTime / 1000);                 // '秒'取得
            myMS = myTime % 1000;                        // 'ミリ秒'取得
            document.getElementById("TextBox1").innerHTML = myS + ":" + myMS;
        }
    }
    //ストップウォッチ関数2(全体用)
    myButton2 = 0;            // [Start]/[Stop]のフラグ
    var myStart2;
    var myStop2;
    function myCheck2(myFlg2) {
        if (myButton2 == 0) {      // Startボタンを押した
            myStart2 = new Date();  // スタート時間を退避
            myButton2 = 1;
            myInterval2 = setInterval("myCheck2(1)", 1);
        } else {                 // スタート実行中
            if (myFlg2 == 0) {      // Stopボタンを押した
                myButton2 = 0;
                clearInterval(myInterval2);
            }
            myStop2 = new Date();  // 経過時間を退避
            myTime2 = myStop2.getTime() - myStart2.getTime(); // 通算ミリ秒計算
            myS2 = Math.floor(myTime2 / 1000);                 // '秒'取得
            myMS2 = myTime2 % 1000;                        // 'ミリ秒'取得
        }
    }
    //＃構造体の宣言
    var Mouse = new Object();
    Mouse["AID"] = 0;
    Mouse["Time"] = 0;
    Mouse["X"] = 0;
    Mouse["Y"] = 0;
    Mouse["DragDrop"] = 0;  //ドラッグ中か（0:MouseMove,1:MouseDown,2:MouseUp)
    Mouse["DropPos"] = 0;       //どこドロップされたか(0:元,1:レジスタ1,2:レジスタ2,3:レジスタ3)
    Mouse["hlabel"] = "";       //ドラッグしているラベル（マウスが当たっているラベル）
    Mouse["Label"] = "";        //どのラベルが対象か（複数ラベル)
    Mouse["addk"] = 0;
    //-------------------------
    var AnswerData = new Object();
    AnswerData["QN"] = 0;                       //問題番号
    AnswerData["ADate"] = new Date; //解答日時
    AnswerData["TF"] = 0;                       //正誤
    AnswerData["Time"] = 0;                 //解答時間
    AnswerData["FQues"] = "";               //問題
    AnswerData["AID"] = 0;
    //--------------------------
    p = new Array();
    Mouse_Flag = new Boolean(false);    //マウスの軌跡を保存するかどうか
    IsDragging = new Boolean(false);    //ドラッグ中の場合true
    function Point(_x, _y) { this.x = _x; this.y = _y; }
    //使う例 print(DiffPoint.x);
    var DiffPoint = new Point(0, 0);     //ドラッグ開始地点とドラッグ開始時のボタンの位置とのずれ
    var DLabel = "";
    var x = 0;                                      //挿入線を描画する位置
    var y1 = 0;
    var y2 = 0;
    var Mylabels = new Array();             //並び替えラベルの元
    var Mylabels_r1 = new Array();      //レジスタ用
    var Mylabels_r2 = new Array();
    var Mylabels_r3 = new Array();
    var Mylabels_ea = new Array();      //最終解答欄用
    var MyNums = new Array();               //番号リスト
    var DefaultX = 30;                      //ラベルの初期値
    var DefaultY = 100;
    var DefaultX_r1 = 30;                       //ラベルの初期値
    var DefaultY_r1 = 250;
    var DefaultX_r2 = 30;                       //ラベルの初期値
    var DefaultY_r2 = 330;
    var DefaultX_r3 = 30;                       //ラベルの初期値
    var DefaultY_r3 = 410;
    var DefaultX_ea = 30;                       //ラベルの初期値
    var DefaultY_ea = 170;
    var sPos = new Point(0, 0);
    var ePos = new Point(0, 0);
    var PorQ;                               //文末の.または?を格納するよう
    var Answer;                             //回答　（先頭大文字、文末つき）
    var Question;                       //問題文(先頭小文字、文末ぬき）
    var str1;                                   //Answerの補助
    var str2;
    var LabelNum;                           //ラベルの数
    var Answer;                             //正解
    var Answer1;                            //別解1
    var Answer2;                            //別解2
    var linedataFlg = false;                //linedataに書き込み中
    var Answertime = new Date;          //解答日時(datatime?)
    var $Ques_Num = 0;                          //問題番号
    var $Mouse_Data = Mouse;                //マウスの軌跡情報を保持
    var Mouse_Num;                                  //マウスの軌跡情報の数
    var StartQues = "";                           //始めの問題の状態
    var MyAnswer = "";                    //自分の答え
    var WriteAnswer = "";             //自分の答え保存用
    var $QAData = AnswerData; //問題データ保存用
    var NewQuesNum;                     //出題する問題番号
    var WriteAID = 0;                   //書き込み終了したAIDの数
    var MyControls = new Array();       //グループ化ラベルをまとめた配列
    var AllCorrectAnsRate = 0;          //全体の正解率
    var AllCorrectAns = 0;                  //全体の正解数
    var AllResAns = 0;                          //全体の解答数
    var CorrectAnsRate = 0;                 //今回の正解率
    var CorrectAns = 0;             //今回の正解数
    var ResAns = 0;                     //今回の解答数
    var ResRank = 0;                    //グループ全体の人数
    var CorrectRank = 0;            //自分の順位
    var printanswer = 0;            //問題表示用フラグ
    var QuesCount = 0;              //固定問題の個数
    var AID = 0;                            //解答番号、linedataとlinedatamouseを関連付けるキー
    var checkl = 0;                     //phpオリジナル、重さをなくすため
    var cx = 0;                             //キャンバスのギャップの修正用
    var cy = 0;
    var MV = new Boolean(false); //グループ化のためのドラッグ中か
    var loc = -1; //グループ化の線の位置　0:左上 1:左下 2:右上 3:右下
    var rx = 0; //再描画用（消すため)
    var rx = 0;
    var PreMouseTime = -1; //前回のマウス取得時間（※新しい問題が出るたびに初期化させている）
    var dd = new Array(); //ドラッグドロップ変数
    var $AccessDate; //ログイン日時
    //var kugiri_num = 0; //区切りラベルの数
    Mld = new Boolean(false); //mylabeldownイベント中か
    var FixLabels = new Array(); //固定ラベル
    var FixNum = new Array(); //固定ラベルの番号
    var FixText = new Array(); //固定ラベルのタグを含むテキスト
    MytoFo = new Boolean(false); //IEのバグ対応。MyLabels_MouseMove→Form1_onMouseMoveのため
    var DragL; //ドラッグ中のラベルの引渡し。
    var del; //デリートフラグがついた問題。
    var delwid;

    var array_flag = -1; //どこでイベントが起こったか判定する。(マウスダウン用)　0=問題提示欄 1=レジスタ1 2=レジスタ2 3=レジスタ3 4=最終解答欄
    var array_flag2 = -1; //どこでイベントが起こったか判定する。(マウスアップ用)　0=問題提示欄 1=レジスタ1 2=レジスタ2 3=レジスタ3 4=最終解答欄
    var d_flag = -1; //どこでイベントが起こったか判定する。(マウスアップ)　0=問題提示欄 1=レジスタ1 2=レジスタ2 3=レジスタ3 4=最終解答欄
    //再表示用だよ
    var Mylabels2 = new Array();
    var Mylabels_left = new Array();
    var region = 0;
    var URL = 'http://lmo.cs.inf.shizuoka.ac.jp/~miki/hearing/' //サーバー用
    //ランダムに配列を並び替えるソース
    //copyright(C) 2005 あう http://www5c.biglobe.ne.jp/~horoau/
    //ver1.0
    Array.prototype.random = function () {
        this.sort(function (a, b) {
            var i = Math.ceil(Math.random() * 100) % 2;
            if (i == 0)
            { return -1; }
            else
            { return 1; }
        });
    }
    //-------------------------------------------------------------
    //配列に指定した値があるかチェック
    if (!Array.prototype.contains) {
        /**
        * @access public
        * @param value mixed 検索するオブジェクト
        * @return boolean 対象配列に既にオブジェクトが存在していれば true, そうでなければ false
        * 配列の値の重複チェックなどに使用。
        */
        Array.prototype.contains = function (value) {
            for (var i in this) {
                if (this.hasOwnProperty(i) && this[i] === value) {
                    return true;
                }
            }
            return false;
        }
    }
    //-------------------------------------------------------------
    //ロードイベント
    function ques_Load() {
        new Ajax.Request(URL + 'swrite.php',
    {
        method: 'get',
        onSuccess: getA,
        onFailure: getE
    });
        //▲マウスデータの取得
        //ドラッグ開始地点の保存
        function getA(req) {
            alert(req.responseText);
        }
        function getE(req) {
            alert("書き込みに失敗しました");
        }
        AnswerT = new DateFormat("yyyy-MM-dd HH:mm:ss");
        $AccessDate = AnswerT.format(new Date());
        BPen = new jsGraphics("myCanvas");                     //ペン(グループ化用)
        BPen.setColor("black");
        //破線のスタイルを設定
        BPen.setStroke(-1);
        BPen2 = new jsGraphics("myCanvas");                    //ペン(挿入線用)
        BPen2.setColor("black");
        //スラッシュ入れる用
        BPen3 = new jsGraphics("myCanvas2");
        document.onmousemove = Form1_MouseMove;
        document.onselectstart = "return false";
        //-------------------------------------------------------------
        //DBから引用
        function getError(res) {
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
        function getm(res) {
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
        function getAID(res) {
            AID = res.responseText;
            if (AID == "AID抽出エラー（マウス）" || AID == "") {
                AID = 0;
            } else {
                AID -= 0; //数値化
                AID += 1;
            }
            WriteAID = AID;
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
        function getDelflg(res) {
            del = res.responseText;
            delwid = del.split("#");
            //alert(res.responseText);
            //alert(del);
        }
        //固定問題の個数取得======================================削除?
        var $count = "count";
        var $params = 'param2=' + encodeURIComponent($count);
        new Ajax.Request(URL + 'dbsyori.php',
        {
            method: 'get',
            onSuccess: getOID,
            onFailure: Error,
            parameters: $params
        });
        //関数開始-----------------------------------
        function getOID(res) {
            if (res.responseText == "エラー") {
                alert("固定問題個数取得エラー");
            } else {
                QuesCount = res.responseText - 0;
            }
        }
        function Error(res) {
            alert("問題取得失敗");
            window.close;
        }
        myCheck2(0);
    }

    //問題の出題関数
    function setques() {
        //OID=出題順
        Fixmsg.innerHTML = "-情報-";
        myCheck(0);
        //問題固定var------------
        var $Load = "load";
        var $OID = AID % QuesCount;
        $OID += 1;
        //alert($OID);
        var $params = 'param1=' + encodeURIComponent($OID)
                    + '&param2=' + encodeURIComponent($Load);
        new Ajax.Request(URL + 'dbsyori.php', //本番用
    {
    method: 'get',
    onSuccess: getOIDtoWID,
    onFailure: Error,
    parameters: $params
    });

        function Error(res) {
            alert("問題取得失敗");
            window.close;
        }
        function getOIDtoWID(res) {
            if (res.responseText == "エラー") {
                alert("固定問題番号取得エラー");
            } else {
                $Ques_Num = res.responseText - 0;
                //$Ques_Num = Math.floor( Math.random() * 201 ); //ランダムに問題を選出
                //$Ques_Num = 0; //問題を指定したいとき用

                for (i = 0; i <= delwid.length - 1; i++) {
                    //delwid[i] += 0;
                    //alert("変化前" + delwid[i]);
                    if (delwid[i] == $Ques_Num) {
                        //alert(delwid[i]);
                        //$Ques_Num = Math.floor( Math.random() * 201 );
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
                function getResponse(req) {

                    //---------------------------
                    PorQ = req.responseText.charAt(req.responseText.length - 1); //ピリオド、または？を抜き取る
                    /*Question = req.responseText.substring(0,req.responseText.length-1);*/ //ピリオド抜きの問題文
                    str1 = req.responseText.substr(0, 1);
                    str2 = req.responseText.substr(1);
                    Answer = str1.toUpperCase() + str2; //完全な答え
                    /*Mylabels = Question.split(" ");*/ //スペースで単語に区切る

                    $q = "q1";
                    $params = 'param1=' + encodeURIComponent($Ques_Num)
                        + '&param2=' + encodeURIComponent($q);
                    new Ajax.Request(URL + 'dbsyori.php', //本番用
            {
            method: 'get',
            onSuccess: getStart,
            onFailure: getError,
            parameters: $params
        });
                    function getStart(req1) {
                        Mylabels = req1.responseText.split("|");
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
                            if (Fix.responseText != "-1") {
                                FixNum = Fix.responseText.split("#"); //♯区切り
                                for (i = 0; i <= FixNum.length - 1; i++) {
                                    FixNum[i] -= 0; //数値化
                                    FixLabels[i] = Mylabels[FixNum[i]];
                                    FixNum[i] += 1;
                                    Fixmsg.innerHTML += "</br><font size='5' color='green'>" + FixLabels[i] + "</font>は<font size='5' color='red'>" + FixNum[i] + "</font>番目にきます";
                                    FixNum[i] -= 1;
                                }
                            } else {
                                FixNum = 0
                            }
                            //  Mylabels.random();
                            LabelNum = Mylabels.length;
                            //--------------------------------
                            //body要素を取得
                            var body = document.getElementsByTagName("body")[0];
                            var el;
                            //------------------------------
                            for (i = 0; i <= LabelNum - 1; i++) {
                                //p要素を作成
                                var p = document.createElement("div");
                                var n = document.createElement("div"); //そのラベルが何番目にくるのかを表示するためのdiv要素
                                //テキストノードを作成
                                p.setAttribute("id", i);
                                n.setAttribute("id", -i); //一応何かのために(削除用)
                                YAHOO.util.Dom.setStyle(p, "position", "absolute");
                                YAHOO.util.Dom.setStyle(n, "position", "absolute");
                                if (i < 1) {
                                    YAHOO.util.Dom.setStyle(p, "left", DefaultX);
                                    YAHOO.util.Dom.setStyle(p, "top", DefaultY);
                                    var LL = YAHOO.util.Dom.getRegion(p);
                                    YAHOO.util.Dom.setStyle(n, "top", DefaultY - 15);
                                }
                                else {
                                    YAHOO.util.Dom.setStyle(p, "left", el.right + 17);
                                    YAHOO.util.Dom.setStyle(p, "top", DefaultY);
                                    var LL = YAHOO.util.Dom.getRegion(p);
                                    YAHOO.util.Dom.setStyle(n, "top", DefaultY - 15);
                                }
                                YAHOO.util.Dom.setStyle(p, "width", "auto");
                                YAHOO.util.Dom.setStyle(n, "width", "auto");
                                YAHOO.util.Dom.setStyle(p, "font-family", "Arial");
                                //YAHOO.util.Dom.setStyle(Mylabels[FixNum[i]],"border","solid 1px orange");
                                YAHOO.util.Dom.setStyle(n, "font-size", "10px");
                                if (i == LabelNum - 1) {
                                    StartQues += Mylabels[i];
                                }
                                else {
                                    StartQues += Mylabels[i] + "|";
                                }
                                dd[i] = new YAHOO.util.DD(p);
                                var str = document.createTextNode(Mylabels[i]);
                                //テキストノードをp要素に追加
                                p.appendChild(str);
                                MyNums[i] = i + 1;
                                var str2 = document.createTextNode(MyNums[i]);
                                //テキストノードをn要素に追加
                                //n.appendChild(str2);

                                /*for(f=0;f<=FixNum.length-1;f++){
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
                                }*/

                                //p要素をbody要素に追加
                                Mylabels[i] = p;
                                body.appendChild(Mylabels[i]);
                                //p要素をbody要素に追加
                                MyNums[i] = n;
                                body.appendChild(MyNums[i]);

                                var LL = YAHOO.util.Dom.getRegion(p);
                                YAHOO.util.Dom.setStyle(n, "left", LL.left + (LL.right - LL.left) / 2 - 2);

                                el = YAHOO.util.Dom.getRegion(p);
                                //イベントハンドラの追加
                                dd[i].onMouseDown = function (e) { MyLabels_MouseDown(this.getDragEl()) }
                                dd[i].onMouseUp = function (e) { MyLabels_MouseUp(this.getDragEl()) }
                                dd[i].onDrag = function (e) { MyLabels_MouseMove(this.getDragEl()) }
                                YAHOO.util.Event.addListener(Mylabels[i], 'mouseover', MyLabels_MouseEnter);
                                YAHOO.util.Event.addListener(Mylabels[i], 'mouseout', MyLabels_MouseLeave);

                                region = YAHOO.util.Dom.getRegion(Mylabels[i]);
                                Mylabels_left[i] = region.left;
                                if (i != Mylabels.length - 1) {
                                    BPen3.setFont("arial", "15px", Font.ITALIC_BOLD);
                                    BPen3.drawString("/", region.right + 7, 100);
                                    BPen3.paint();
                                }
                            }
                            //Mylabels配列のコピー。Mylabelは今後動かさないので。
                            Mylabels2 = Mylabels.concat();
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
                            function getJapanese(res) {
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
                                
                                function getSentence1(res) {
                                    if (res.responseText != "") {//NULL以外だったら
                                        str1 = res.responseText.substr(0, 1);
                                        str2 = res.responseText.substr(1);
                                        Answer1 = str1.toUpperCase() + str2;
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
                                        function getSentence2(res) {
                                            if (res.responseText != "") {//NULL以外だったら
                                                str1 = res.responseText.substr(0, 1);
                                                str2 = res.responseText.substr(1);
                                                Answer2 = str1.toUpperCase() + str2;
                                                //alert(Answer2);

                                            } //ifres.responseText != ""ここまで------------------------------------

                                        } // getSentence1ここまで--------------------------------------------------------
                                    } else {
                                        alert("atainasi");
                                    } //ifres.responseText != ""ここまで------------------------------------

                                } // getSentence1ここまで--------------------------------------------------------

                            } // getJapaneseここまで--------------------------------------------------------
                            Mouse_Flag = true;
                        } //Fix関数ここまで--------------------------------------------------------
                    }
                } /*getStart関数ここまで*/
                //--関数getresponseここまで---------------------------------------
            }
        }
        function getError(req) {
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
        //alert(Answertime);
    }
    //問題の出題関数ここまで-------------------------------------------------------
    //範囲指定をするときのドラッグ開始処理------------------------------
    function Form1_MouseDown() {
        if (event.y <= 130)
        { d_flag = 0; }
        else if (event.y <= 215 && event.y > 130)
        { d_flag = 4; }
        else if (event.y <= 295 && event.y > 215)
        { d_flag = 1; }
        else if (event.y <= 375 && event.y > 295)
        { d_flag = 2; }
        else if (event.y > 375)
        { d_flag = 3; }
        if (Mouse_Flag == false) {
            return;
        }
        //マウスカーソルを十字に
        document.body.style.cursor = "crosshair";

        //グループ化されたラベルの初期化
        for (i = 0; i <= MyControls.length - 1; i++) {
            YAHOO.util.Dom.setStyle(MyControls[i], "color", "black");
        }
        MyControls = new Array();
        //開始点の取得
        sPos.x = event.x + cx;
        sPos.y = event.y + cy;
        ePos.x = event.x + cx;
        ePos.y = event.y + cy;
        //document.getElementById("msg").innerHTML = "Form1_MouseDown";
        MV = true;
    }
    //------------------------------------------------------------------
    //マウスアップ関数ここから(範囲選択を確定（ラベルをグループ化))---------------------------------------------------
    function Form1_MouseUp() {
        MV = false;
        if (Mouse_Flag == false || IsDragging == true) {
            return;
        }
        BPen.clear();
        //マウスカーソルを戻す
        document.body.style.cursor = "default";
        var g_array = new Array();
        if (d_flag == 0) { g_array = Mylabels.slice(0); }
        else if (d_flag == 1) { g_array = Mylabels_r1.slice(0); }
        else if (d_flag == 2) { g_array = Mylabels_r2.slice(0); }
        else if (d_flag == 3) { g_array = Mylabels_r3.slice(0); }
        else if (d_flag == 4) { g_array = Mylabels_ea.slice(0); }
        //選択範囲の中にラベルがあればグループ化する
        //青色への色変えも
        //左上,右上,左下,右下の４方向からのドラッグに対応------------------------------------------
        for (i = 0; i <= g_array.length; i++) {
            //一時退避・・・なくて良い
            MLi = YAHOO.util.Dom.getRegion(g_array[i]);
            if (sPos.x <= ePos.x && sPos.y <= ePos.y) {  //左上
                if ((sPos.x < MLi.right && sPos.y < MLi.bottom) && (ePos.x > MLi.left && ePos.y > MLi.top)) {
                    MyControls.push(g_array[i]);
                    //MyControls[i] = Mylabels[i];
                    //alert(MyControls[i].innerHTML);
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "blue");
                }
            }
            else if (sPos.x <= ePos.x && sPos.y >= ePos.y) {//左下
                if ((sPos.x < MLi.right && sPos.y > MLi.top) && (ePos.x > MLi.left && ePos.y < MLi.bottom)) {
                    MyControls.push(g_array[i]);
                    //MyControls[i] = Mylabels[i];
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "blue");
                }
            }
            else if (sPos.x > ePos.x && sPos.y < ePos.y) {//右上
                if ((sPos.x > MLi.left && sPos.y < MLi.bottom) && (ePos.x < MLi.right && ePos.y > MLi.top)) {
                    MyControls.push(g_array[i]);
                    //MyControls[i] = Mylabels[i];
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "blue");
                }
            }
            else if (sPos.x > ePos.x && sPos.y > ePos.y) {//右下
                if ((sPos.x > MLi.left && sPos.y > MLi.top) && (ePos.x < MLi.right && ePos.y < MLi.bottom)) {
                    MyControls.push(g_array[i]);
                    //MyControls[i] = Mylabels[i];
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "blue");
                }
            }
        } //----------------------------------------------------------------------------------------
        /*{     for(i=0;i<=g_array.length;i++){
        if(MyControls.indexOf(g_array[i]) == -1){
        YAHOO.util.Dom.setStyle(g_array[i],"color","black");
        }
        }*/

    }
    //-----------------------------------------------------------
    //ドラッグ中に範囲指定の線を描画など
    function Form1_MouseMove(sender) {
        /*  if(Mouse_Flag == false || Mld == true){
        document.getElementById("msg").innerHTML = "ラベルドラッグだからかえる";
        return;
        }
        if(MytoFo==false && IsDragging==true){
        }*/
        //問題提示欄では線を引かない
        //if(d_flag != 0){
        //ドラッグ中
        if (MV == true) {

            draw();
            ePos.x = event.x + cx;
            ePos.y = event.y + cy;
        }
        //}
        //--------------------別のマウスムーブの取り込み--------------------------------------
        var P = new Point(0, 0);

        if (Mouse_Flag == true) {
            //マウスの位置座標を取得
            P.x = event.x;
            P.y = event.y;
            var a;
            if (PreMouseTime != -1) { //データを間引く
                //経過時間取得-----
                myStop = new Date();
                mTime = myStop.getTime() - myStart.getTime();
                //alert(mTime);
                a = mTime - PreMouseTime;
                if (a < 100) {
                    return;
                }
            }

            //マウスデータの取得
            myStop = new Date();
            mTime = myStop.getTime() - myStart.getTime();
            $Mouse_Data["AID"] = AID;
            $Mouse_Data["Time"] = mTime;
            if (IsDragging == true) {
                var hLabel = sender;
                var hl = YAHOO.util.Dom.getRegion(DragL);
                $Mouse_Data["X"] = hl.left;
                $Mouse_Data["Y"] = hl.top;
            }
            else {
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

            //encodeURI = 変換してるだけだぴょん
            //paramっていうのに各変数を入れてる！(tmpfileで&で区切って送ってる)
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
            function getA(req) {
                document.getElementById("msg").innerHTML = req.responseText;
                MytoFo = false;
            }
            function getE(req) {
                alert("失敗");
            }
        }
        //--------------------別のマウスムーブここまで----------------------------------------------------------------
    }
    function draw() {
        BPen.clear();

        //レジスタ3をドラッグ中
        if (d_flag == 3) {
            //もし、範囲の線を超えてしまっていたら？
            if (ePos.y <= 375) { ePos.y = 375; }
            //もし、下の範囲を超えてしまっていたら？（バグ対策だぴょん） マウスアップの時になんかバグが起きてるかも！
            else if (ePos.y >= 480) { ePos.y = 480; }
        }
        //問題提示欄をドラッグ中
        else if (d_flag == 0) {
            //もし、範囲の線を超えてしまっていたら？
            if (ePos.y >= 130) { ePos.y = 130; }
        }
        //その他
        else {
            //最終解答欄だった場合
            if (d_flag == 4) {
                //上限を超えていた場合
                if (ePos.y <= 130) { ePos.y = 130; }
                //下限を超えていた場合
                else if (ePos.y >= 215) { ePos.y = 215; }
            }
            //レジスタ1だった場合
            if (d_flag == 1) {
                //上限を超えていた場合
                if (ePos.y <= 215) { ePos.y = 215; }
                //下限を超えていた場合
                else if (ePos.y >= 295) { ePos.y = 295; }
            }
            //レジスタ2だった場合
            if (d_flag == 2) {
                //上限を超えていた場合
                if (ePos.y <= 295) { ePos.y = 295; }
                //下限を超えていた場合
                else if (ePos.y >= 375) { ePos.y = 375; }
            }
        }
        //消える描画でドラッグ中の四角形を描く
        //左上、右上、左下、右下、の４方向からのドラッグに対応
        if (sPos.x <= ePos.x && sPos.y <= ePos.y) {  //左上
            BPen.drawRect(sPos.x, sPos.y, ePos.x - sPos.x, ePos.y - sPos.y)
            loc = 0;
        }
        else if (sPos.x <= ePos.x && sPos.y >= ePos.y) {//左下
            BPen.drawRect(sPos.x, ePos.y, ePos.x - sPos.x, sPos.y - ePos.y)
            loc = 1;
        }
        else if (sPos.x > ePos.x && sPos.y < ePos.y) {//右上
            BPen.drawRect(ePos.x, sPos.y, sPos.x - ePos.x, ePos.y - sPos.y)
            loc = 2;
        }
        else if (sPos.x > ePos.x && sPos.y > ePos.y) {//右下
            BPen.drawRect(ePos.x, ePos.y, sPos.x - ePos.x, sPos.y - ePos.y)
            loc = 3;
        }
        BPen.paint();
        //もし選択範囲にラベルがあれば赤色に色づけ
        //選択範囲が解除されたら黒色に戻る処理も実装
        //どの欄を対象にしているか、フラグにより判別
        var g_array = new Array();
        if (d_flag == 0) { g_array = Mylabels.slice(0); }
        else if (d_flag == 1) { g_array = Mylabels_r1.slice(0); }
        else if (d_flag == 2) { g_array = Mylabels_r2.slice(0); }
        else if (d_flag == 3) { g_array = Mylabels_r3.slice(0); }
        else if (d_flag == 4) { g_array = Mylabels_ea.slice(0); }

        //色付け。このへんはあまりいじってません。
        for (i = 0; i <= g_array.length - 1; i++) {
            //一時退避
            //退避ラベルならスキップ・・・必要なし
            //範囲選択をすべて抱合⇒一部抱合に変更
            MLi = YAHOO.util.Dom.getRegion(g_array[i]);
            if (sPos.x <= ePos.x && sPos.y <= ePos.y) { //左上---------------------------
                if ((sPos.x < MLi.right && sPos.y < MLi.bottom) && (ePos.x > MLi.left && ePos.y > MLi.top)) {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "red");
                }
                else {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "black");
                }
            } //左上ここまで--------------------------------------------------
            else if (sPos.x <= ePos.x && sPos.y >= ePos.y) {//左下
                if ((sPos.x < MLi.right && sPos.y > MLi.top) && (ePos.x > MLi.left && ePos.y < MLi.bottom)) {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "red");
                }
                else {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "black");
                }
            }
            else if (sPos.x > ePos.x && sPos.y < ePos.y) {//右上
                if ((sPos.x > MLi.left && sPos.y < MLi.bottom) && (ePos.x < MLi.right && ePos.y > MLi.top)) {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "red");
                }
                else {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "black");
                }
            }
            else if (sPos.x > ePos.x && sPos.y > ePos.y) {//右下
                if ((sPos.x > MLi.left && sPos.y > MLi.top) && (ePos.x < MLi.right && ePos.y < MLi.bottom)) {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "red");
                }
                else {
                    YAHOO.util.Dom.setStyle(g_array[i], "color", "black");
                }
            }
        } //forここまで-----------------------------------------
    }

    //------------------------------------------------------------------------------- 
    //ソート関数ここから----------------------------------------------------------
    function MyLabelSort(sender, ex, ey) {
        var mylabelarray3 = new Array();
        var X_p = 0;
        var Y_p = 0;
        if (array_flag2 == 0) {
            mylabelarray3 = Mylabels.slice(0);
            X_p = DefaultX;
            Y_p = DefaultY;
        }
        else if (array_flag2 == 1) {
            mylabelarray3 = Mylabels_r1.slice(0);
            X_p = DefaultX_r1;
            Y_p = DefaultY_r1;
        }
        else if (array_flag2 == 2) {
            mylabelarray3 = Mylabels_r2.slice(0);
            X_p = DefaultX_r2;
            Y_p = DefaultY_r2;
        }
        else if (array_flag2 == 3) {
            mylabelarray3 = Mylabels_r3.slice(0);
            X_p = DefaultX_r3;
            Y_p = DefaultY_r3;
        }
        else if (array_flag2 == 4) {
            mylabelarray3 = Mylabels_ea.slice(0);
            X_p = DefaultX_ea;
            Y_p = DefaultY_ea;
        }
        var i;
        var j;
        var k;

        var hLabel; // = document.createElement("div");
        hLabel = sender;
        var aNum = new Array(); //問題文のラベル番号を記憶
        var aCount = 0;
        for (i = 0; i <= mylabelarray3.length - 1; i++) {
            aNum.push(i);
            aCount++;
        }
        var iLabel = new Array(); //スワップ用配列
        var item = MyControls.indexOf(hLabel);
        //グループ化されているかの判定
        //if(item > -1){

        //グループ化されているかの判定
        if (MyControls.length > 0) {
            //問題提示欄にドロップされた場合のみ
            if (array_flag2 == 0) {

                for (j = 0; j < MyControls.length; j++) {
                    for (i = 0; i < Mylabels2.length; i++) {
                        if (Mylabels2[i].id == MyControls[j].id) { break; }
                    }
                    mylabelarray3.splice(i, 1, MyControls[j]);
                    Mylabels = mylabelarray3.slice(0);
                    YAHOO.util.Dom.setX(Mylabels[i], Mylabels_left[i]);
                    YAHOO.util.Dom.setY(Mylabels[i], 100);
                }
                return mylabelarray3;
            }
            var X1 = YAHOO.util.Dom.getRegion(MyControls[0]);
            //どこに挿入するか調べる。
            for (m = 0; m <= mylabelarray3.length; m++) {
                if (m == mylabelarray3.length) { break; }
                var X2 = YAHOO.util.Dom.getRegion(mylabelarray3[m]);
                if (X1.left <= X2.left) {
                    break;
                }
            }


            //配列に挿入
            for (k = 0; k < MyControls.length; k++) {
                mylabelarray3.splice(m + k, 0, MyControls[k])
            }
            //もとの問題文の位置のラベルを整形
            for (i = 0; i < mylabelarray3.length; i++) {
                if (i == 0) {
                    YAHOO.util.Dom.setX(mylabelarray3[0], X_p);
                    YAHOO.util.Dom.setY(mylabelarray3[0], Y_p);
                }
                else {
                    var X1 = YAHOO.util.Dom.getRegion(mylabelarray3[i - 1]);
                    YAHOO.util.Dom.setX(mylabelarray3[i], X1.right + 17);
                    YAHOO.util.Dom.setY(mylabelarray3[i], Y_p);
                }
            }
        }
        else {
            //問題提示欄にドロップされた場合のみ
            if (array_flag2 == 0) {
                for (i = 0; i < Mylabels2.length; i++) {
                    if (Mylabels2[i].id == hLabel.id) { break; }
                }
                mylabelarray3.splice(i, 1, hLabel);
                Mylabels = mylabelarray3.slice(0);
                YAHOO.util.Dom.setX(Mylabels[i], Mylabels_left[i]);
                YAHOO.util.Dom.setY(Mylabels[i], 100);
                return mylabelarray3;
            }
            var X1 = YAHOO.util.Dom.getRegion(hLabel);
            //var X1 = event.x;
            //どこに挿入するか調べる。グループ化も一緒にやってしまえたらやってしまおう。
            for (j = 0; j <= mylabelarray3.length; j++) {
                if (j == mylabelarray3.length) { break; }
                var X2 = YAHOO.util.Dom.getRegion(mylabelarray3[j]);
                if (X1.left <= X2.left) {
                    break;
                }
            }
            //配列に挿入
            mylabelarray3.splice(j, 0, hLabel)
            //もとの問題文の位置のラベルを整形
            for (i = 0; i < mylabelarray3.length; i++) {
                if (i == 0) {
                    YAHOO.util.Dom.setX(mylabelarray3[0], X_p);
                    YAHOO.util.Dom.setY(mylabelarray3[0], Y_p);
                }
                else {
                    var X1 = YAHOO.util.Dom.getRegion(mylabelarray3[i - 1]);
                    YAHOO.util.Dom.setX(mylabelarray3[i], X1.right + 17);
                    YAHOO.util.Dom.setY(mylabelarray3[i], Y_p);
                }
            }
        }
        //MyNumsSort();
        if (array_flag2 == 1) { Mylabels_r1 = mylabelarray3.slice(0); }
        else if (array_flag2 == 2) { Mylabels_r2 = mylabelarray3.slice(0); }
        else if (array_flag2 == 3) { Mylabels_r3 = mylabelarray3.slice(0); }
        else if (array_flag2 == 4) { Mylabels_ea = mylabelarray3.slice(0); }
        return mylabelarray3;

    }
    //マウスが上に来たらラベルの見た目を変えたり、グループ化やレジスタラベルの対応---------------
    function MyLabels_MouseEnter(e) {
        if (MV == true || IsDragging == true) {
            return;
        }
        //alert(this.id);
        //レジスタ内のグループ化・・・なくてよし
        var index = MyControls.indexOf(this);
        //グループ化されたラベルの初期化
        if (index == -1) {
            for (i = 0; i <= MyControls.length - 1; i++) {
                /*if(MyControls[i].innerHTML == "/"){
                YAHOO.util.Dom.setStyle(MyControls[i],"color","#ff6699");
                }else{
                YAHOO.util.Dom.setStyle(MyControls[i],"color","black");
                }*/
                YAHOO.util.Dom.setStyle(MyControls[i], "text-decoration", "none");
            }
            MyControls = new Array();
        }
        else {
            for (i = 0; i <= MyControls.length - 1; i++) {
                YAHOO.util.Dom.setStyle(MyControls[i], "text-decoration", "underline");
            }
        }
        //alert(MyControls.length);
        YAHOO.util.Dom.setStyle(this, "text-decoration", "underline");
    }
    function MyLabels_MouseLeave() {
        if (MV == true || IsDragging == true) {
            return;
        }
        for (i = 0; i <= MyControls.length - 1; i++) {
            YAHOO.util.Dom.setStyle(MyControls[i], "text-decoration", "none");
        }
        //alert(MyControls.length);
        YAHOO.util.Dom.setStyle(this, "text-decoration", "none");
    }

    //★★ラベルクリック時。引っこ抜くときの作業とかしてるよ
    function MyLabels_MouseDown(sender) {
        myStop = new Date();
        var mylabelarray = new Array();

        //どこのラベル郡にsenderが入ってるのか判定。ついでにどの位置に入っていたのかも。
        //idでやってます。頭悪いです。ごめんなさい。
        //もっといい簡潔な方法があったら書き換えてください。
        //単語何番目にあるんです？？グループ化してないとき
        var index_sender = 0;
        //単語何番目にあるんです？？グループ化してるとき
        var index_sender_g = 0;
        //もうグループ化してあるところは分かっているはず(d_flagで判定してるはず)なので
        //グループ化の先頭は何番目に入ってるのか？調べる
        //問題提示欄
        for (i = 0; i < Mylabels.length; i++) {
            if (Mylabels[i] == undefined) { continue; }
            if (Mylabels[i].id == sender.id) { array_flag = 0; index_sender = i; }
        }
        //レジスタ1
        for (i = 0; i < Mylabels_r1.length; i++) {
            if (Mylabels_r1[i].id == sender.id) { array_flag = 1; index_sender = i; }
        }
        //レジスタ2
        for (i = 0; i < Mylabels_r2.length; i++) {
            if (Mylabels_r2[i].id == sender.id) { array_flag = 2; index_sender = i; }
        }
        //レジスタ3
        for (i = 0; i < Mylabels_r3.length; i++) {
            if (Mylabels_r3[i].id == sender.id) { array_flag = 3; index_sender = i; }
        }
        //最終解答欄
        for (i = 0; i < Mylabels_ea.length; i++) {
            if (Mylabels_ea[i].id == sender.id) { array_flag = 4; index_sender = i; }
        }
        //もしグループ化されているなら
        if (MyControls.length > 0) {
            var g_array = new Array();
            if (array_flag == 1) { g_array = Mylabels_r1.slice(0); }
            else if (array_flag == 2) { g_array = Mylabels_r2.slice(0); }
            else if (array_flag == 3) { g_array = Mylabels_r3.slice(0); }
            else if (array_flag == 4) { g_array = Mylabels_ea.slice(0); }

            //グループ化の先頭が何番目に入っているか？
            for (i = 0; i < g_array.length; i++) {
                if (g_array[i] == undefined) { continue; }
                if (g_array[i].id == MyControls[0].id) { index_sender_g = i; }
            }
        }
        if (array_flag == 0) { mylabelarray = Mylabels.slice(0); }
        else if (array_flag == 1) { mylabelarray = Mylabels_r1.slice(0); }
        else if (array_flag == 2) { mylabelarray = Mylabels_r2.slice(0); }
        else if (array_flag == 3) { mylabelarray = Mylabels_r3.slice(0); }
        else if (array_flag == 4) { mylabelarray = Mylabels_ea.slice(0); }
        //グループ化されたラベルの初期化とか、hLabelに退避とか
        Mld = true;
        var hLabel = sender;
        DragL = sender; //IEのバグ対応
        IsDragging = true;
        //一時退避・・・レジスタなくすからよい？
        var DPos = 0;
        DLabel = "";
        //グループ化ラベルを#で連結する グループラベル
        if (MyControls.length != 0) {
            for (i = 0; i <= MyControls.length - 1; i++) {
                if (i == MyControls.length - 1) {
                    DLabel = DLabel + MyControls[i].id;
                }
                else {
                    DLabel = DLabel + MyControls[i].id + "#";
                }
            }
        }
        else {
            DLabel = DLabel + hLabel.id;
        }

        //Mylabelsで引っこ抜きがあったとき(array_flag==0だったとき)は、問題を詰める作業は行わない。
        if (array_flag == 0) {
            delete mylabelarray[index_sender];
        }
        else {
            //グループ化の場合
            if (MyControls.length > 0) {
                mylabelarray.splice(index_sender_g, MyControls.length);
            }
            else {
                mylabelarray.splice(index_sender, 1);
            }
            //各フラグに合わせて、デフォルトのYの値を変える。Xはついで。
            //array_flagは、ラベルのドラッグがどこで開始したかを示している。(問題提示欄か、レジスタか..？)
            //それによってデフォルトのY座標を変化させる。(ここでは、元あったラベルの並べ替えを行うので。)
            //ここで示すX,Y座標は、ドラッグ中のラベルがあったラベル群のX,Yだよ。
            var X_p = 0;
            var Y_p = 0;
            var DestX = 0;
            var DestY = 0;
            if (array_flag == 1) {
                X_p = DefaultX_r1;
                Y_p = DefaultY_r1;
            }
            else if (array_flag == 2) {
                X_p = DefaultX_r2;
                Y_p = DefaultY_r2;
            }
            else if (array_flag == 3) {
                X_p = DefaultX_r3;
                Y_p = DefaultY_r3;
            }
            else if (array_flag == 4) {
                X_p = DefaultX_ea;
                Y_p = DefaultY_ea;
            }
            //相対位置の計算
            var hl = YAHOO.util.Dom.getRegion(hLabel);
            DestX = hl.left + event.x - DiffPoint.x;
            DestY = hl.top + event.y - DiffPoint.y;

            //問題文のラベルをaLabelに格納
            /*var aLabel = new Array(LabelNum -1);//一時退避　問題文のラベル用ラベル配列
            var aNum = 0;
            for(i=0;i<=LabelNum-1;i++){
            if(MyControls.indexOf(mylabelarray[i]) != -1 || hLabel.id == mylabelarray[i].id){
            continue;
            }
            aLabel[aNum] = mylabelarray[i];
            aNum += 1;
            }*/
            //元の位置にあるラベルの位置を決定
            for (i = 0; i <= mylabelarray.length; i++) {
                if (i == 0) {
                    YAHOO.util.Dom.setX(mylabelarray[i], X_p);
                    YAHOO.util.Dom.setY(mylabelarray[i], Y_p);
                }
                else {
                    var al = YAHOO.util.Dom.getRegion(mylabelarray[i - 1]);
                    YAHOO.util.Dom.setX(mylabelarray[i], al.right + 17); //解像度がちがうため？
                    YAHOO.util.Dom.setY(mylabelarray[i], Y_p);
                }
            }
        }

        //経過時間取得-----
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
        function getA(req) {
            //alert(req.responseText);
            document.getElementById("msg").innerHTML = req.responseText;
            Mld = false;
        }
        function getE(req) {
            alert("失敗");
        }

        DiffPoint = new Point(event.x, event.y);

        var obj = document.getElementById("TermText");
        if (obj.style.display == 'block') {
            var lblText = "";
            var TextNum;
            //alert(hLabel.id);
            if (hLabel.id == LabelNum - 1) {
                lblText = hLabel.innerHTML;
                TextNum = document.Questions.TermText.value.indexOf(lblText.substring(0, lblText.length - 1));
                //alert(TextNum);
                if (TextNum == -1) {
                    lblText = lblText.substring(0, lblText.length - 1) + ".";
                } else {
                    lblText = "";
                }
            } else {
                TextNum = document.Questions.TermText.value.indexOf(hLabel.innerHTML);
                if (TextNum == -1) {
                    lblText = lblText + hLabel.innerHTML + ".";
                }
            }
            document.Questions.TermText.value += lblText;
            TermTextChange();
        }

        if (array_flag == 0) { Mylabels = mylabelarray.slice(0); }
        else if (array_flag == 1) { Mylabels_r1 = mylabelarray.slice(0); }
        else if (array_flag == 2) { Mylabels_r2 = mylabelarray.slice(0); }
        else if (array_flag == 3) { Mylabels_r3 = mylabelarray.slice(0); }
        else if (array_flag == 4) { Mylabels_ea = mylabelarray.slice(0); }
    }

    //★★ラベルを離した時の作業。問題文の形を変えたりいろいろ
    function MyLabels_MouseUp(sender) {
        //枠の色リセット
        document.getElementById("question").style.borderColor = "black";
        document.getElementById("register1").style.borderColor = "black";
        document.getElementById("register2").style.borderColor = "black";
        document.getElementById("register3").style.borderColor = "black";
        document.getElementById("answer").style.borderColor = "black";
        var mylabelarray2 = new Array();
        //イベントが起こったy座標の判定。それによって単語をどこに落とすか決める。
        if (event.y <= 150) { array_flag2 = 0; }
        else if (event.y <= 240 && event.y > 150) { array_flag2 = 4; }
        else if (event.y <= 320 && event.y > 240) { array_flag2 = 1; }
        else if (event.y <= 400 && event.y > 320) { array_flag2 = 2; }
        else if (event.y > 400) { array_flag2 = 3; }

        if (array_flag2 == 0) { mylabelarray2 = Mylabels.slice(0); }
        else if (array_flag2 == 1) { mylabelarray2 = Mylabels_r1.slice(0); }
        else if (array_flag2 == 2) { mylabelarray2 = Mylabels_r2.slice(0); }
        else if (array_flag2 == 3) { mylabelarray2 = Mylabels_r3.slice(0); }
        else if (array_flag2 == 4) { mylabelarray2 = Mylabels_ea.slice(0); }
        if (IsDragging != true) {
            return;
        }
        var hLabel = sender;

        //グループ化関係の処理
        for (i = 0; i <= MyControls.length - 1; i++) {
            YAHOO.util.Dom.setStyle(MyControls[i], "text-decoration", "none");
        }
        YAHOO.util.Dom.setStyle(hLabel, "text-decoration", "none");

        draw3();


        var Dpos = 0;
        var P = new Point(0, 0);
        var hl = YAHOO.util.Dom.getRegion(hLabel);
        P.x = hl.left;
        P.y = hl.top;
        mylabelarray2 = MyLabelSort(sender, event.x, event.y);
        /*  var Kcount = 0;
        for(i = 0;i <= FixNum.length - 1;i++){
        Kcount = 0;
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
        function getA(req) {
            document.getElementById("msg").innerHTML = req.responseText;
        }
        function getE(req) {
            alert("失敗");
        }

        if (array_flag2 == 0) { Mylabels = mylabelarray2.slice(0); }
        else if (array_flag2 == 1) { Mylabels_r1 = mylabelarray2.slice(0); }
        else if (array_flag2 == 2) { Mylabels_r2 = mylabelarray2.slice(0); }
        else if (array_flag2 == 3) { Mylabels_r3 = mylabelarray2.slice(0); }
        else if (array_flag2 == 4) { Mylabels_ea = mylabelarray2.slice(0); }

    }
    //★★マウスでラベルをドラッグ中。動かしてるときだからここで挿入線をアレしたりコレしたり
    function MyLabels_MouseMove(sender) {
        if (IsDragging != true) {
            return;
        }
        var hLabel = sender;
        //ラベルの番号の整理-------------
        //なくしていいとおもう
        /*var Ncount = 0;//Mylabelようの変数
        var MCflag = new Boolean(false);
        for(i=0;i <= mylabelarray.length - 1;i++){
        for(j=0;j<=MyControls.length -1;j++){
        //alert(MyControls[j].innerHTML);
        if(MyControls[j].innerHTML == mylabelarray[i].innerHTML){
        MCflag = true;
        break;
        }
        }
        /*if(hLabel.id == mylabelarray[i].id || mylabelarray[i].innerHTML == "/" || MCflag == true){
        MCflag = false;
        continue;
        }
        if(i<1){
        var LL = YAHOO.util.Dom.getRegion(mylabelarray[i]);
        YAHOO.util.Dom.setStyle(MyNums[i],"left",X_p + (LL.right - LL.left) / 2 - MyNums[i].innerHTML.length / 2 - 2);
        YAHOO.util.Dom.setStyle(MyNums[i],"top",Y_p - 15);
        }
        else{
        var LL = YAHOO.util.Dom.getRegion(mylabelarray[i]);
        YAHOO.util.Dom.setStyle(MyNums[Ncount],"left",LL.left + (LL.right - LL.left) / 2 - MyNums[Ncount].innerHTML.length / 2 - 2);
        YAHOO.util.Dom.setStyle(MyNums[Ncount],"top",Y_p - 15);
        }
        Ncount += 1;
        }
        Ncount =0;
        MCflag = false;*/

        //グループ化ラベルを動かすときの処理。何故動いているか不明。
        var GroupMem = 0;
        hl1 = YAHOO.util.Dom.getRegion(hLabel);
        for (i = 0; i <= MyControls.length - 1; i++) {
            var mcl = YAHOO.util.Dom.getRegion(MyControls[i]);
            if (hl1.left == mcl.left && hl1.top == mcl.top) {
                GroupMem = i //今どのラベルを動かしてるかを記憶（グループ化ラベル）
                break;
            }
        }
        //グループ化ラベルの位置を決定
        for (j = 0; j <= MyControls.length - 1; j++) {
            //ドラッグラベルの左側の位置を決定(hLabelの左側をはじめに決定、それ以降は減算により位置を決定していく)
            if (j < GroupMem) {
                var mcl1 = YAHOO.util.Dom.getRegion(MyControls[GroupMem - 1]);
                YAHOO.util.Dom.setX(MyControls[GroupMem - 1], hl1.left - (mcl1.right - mcl1.left) - 10); //解像度
                YAHOO.util.Dom.setY(MyControls[GroupMem - 1], hl1.top);
                for (k = GroupMem - 1; k >= 0; k--) {
                    var mcl2 = YAHOO.util.Dom.getRegion(MyControls[k + 1]);
                    var mcl3 = YAHOO.util.Dom.getRegion(MyControls[k]);
                    YAHOO.util.Dom.setX(MyControls[k], mcl2.left - (mcl3.right - mcl3.left) - 10); //解像度の影響本来は10
                    YAHOO.util.Dom.setY(MyControls[k], mcl2.top);
                }
                j = GroupMem;
            }
            else if (j == GroupMem) {
                YAHOO.util.Dom.setX(MyControls[j], hl1.left);
                YAHOO.util.Dom.setY(MyControls[j], hl1.top);
            }
            else if (j > GroupMem) {
                //ドラッグラベルの右側の位置を決定
                var mclj = YAHOO.util.Dom.getRegion(MyControls[j - 1]);
                YAHOO.util.Dom.setX(MyControls[j], mclj.right + 10); //
                YAHOO.util.Dom.setY(MyControls[j], mclj.top);
            }
        }

        var line_flag = -1;
        var line_array = new Array();
        var line_x = 0;
        var line_y = 0;
        var line_y2 = 0;
        var lstart_x = 0;
        var lstart_y = 0;
        //枠の色リセット
        document.getElementById("question").style.borderColor = "black";
        document.getElementById("register1").style.borderColor = "black";
        document.getElementById("register2").style.borderColor = "black";
        document.getElementById("register3").style.borderColor = "black";
        document.getElementById("answer").style.borderColor = "black";
        //挿入線関係。まずy座標でどこに挿入線を引くか判定
        if (event.y <= 150) { line_flag = 0; }
        else if (event.y <= 240 && event.y > 150) { line_flag = 4; }
        else if (event.y <= 320 && event.y > 240) { line_flag = 1; }
        else if (event.y <= 400 && event.y > 320) { line_flag = 2; }
        else if (event.y > 400) { line_flag = 3; }
        if (line_flag == 0) {
            line_array = Mylabels.slice(0);
            lstart_x = DefaultX;
            lstart_y = DefaultY;
            document.getElementById("question").style.borderColor = "red";
        }
        else if (line_flag == 1) {
            line_array = Mylabels_r1.slice(0);
            lstart_x = DefaultX_r1;
            lstart_y = DefaultY_r1;
            document.getElementById("register1").style.borderColor = "red";
        }
        else if (line_flag == 2) {
            line_array = Mylabels_r2.slice(0);
            lstart_x = DefaultX_r2;
            lstart_y = DefaultY_r2;
            document.getElementById("register2").style.borderColor = "red";
        }
        else if (line_flag == 3) {
            line_array = Mylabels_r3.slice(0);
            lstart_x = DefaultX_r3;
            lstart_y = DefaultY_r3;
            document.getElementById("register3").style.borderColor = "red";
        }
        else if (line_flag == 4) {
            line_array = Mylabels_ea.slice(0);
            lstart_x = DefaultX_ea;
            lstart_y = DefaultY_ea;
            document.getElementById("answer").style.borderColor = "red";
        }
        if (line_array.length == 0) {
            var line_x = lstart_x;
            var line_y = lstart_y;
            var line_y2 = lstart_y + 18;
        }
        else {
            for (i = 0; i < line_array.length; i++) {
                if (MyControls.length > 0) {
                    var send = YAHOO.util.Dom.getRegion(MyControls[0]);
                }
                else { var send = YAHOO.util.Dom.getRegion(sender); }
                var ali = YAHOO.util.Dom.getRegion(line_array[i]);
                var ali1 = YAHOO.util.Dom.getRegion(line_array[i + 1]);
                if (i == 0 && send.left < ali.left) {
                    //もし左端のラベルの左側に挿入しようとするなら
                    //左端のラベルから挿入位置を計算して表示
                    line_x = ali.left - 8;
                    line_y = ali.top;
                    line_y2 = line_y + (ali.bottom - ali.top);
                }
                else if (i == line_array.length - 1 && send.left >= ali.left) {
                    //もし右端に挿入しようとするなら
                    //右端のラベルから挿入位置を計算して表示
                    line_x = ali.right + 8;
                    line_y = ali.top;
                    line_y2 = line_y + (ali.bottom - ali.top);
                }
                else if (send.left >= ali.left && send.left < ali1.left) {
                    //ラベルに挟まれた位置に挿入するなら
                    //右のラベルから挿入位置を計算して表示
                    line_x = ali1.left - 8;
                    line_y = ali1.top;
                    line_y2 = line_y + (ali1.bottom - ali1.top);
                }
            }
        }
        //問題提示欄には表示させない
        if (line_flag != 0) {
            draw3();
            draw2(line_x, line_y, line_y2);
        }
        else { draw3(); }
        /*if(array_flag == 0){  Mylabels = mylabelarray.slice(0); }
        else if(array_flag == 1){ Mylabels_r1 = mylabelarray.slice(0); }
        else if(array_flag == 2){ Mylabels_r2 = mylabelarray.slice(0); }
        else if(array_flag == 3){ Mylabels_r3 = mylabelarray.slice(0); }
        else if(array_flag == 4){ Mylabels_ea = mylabelarray.slice(0); }*/

        MytoFo = true;
        Form1_MouseMove(sender);
    }
    //挿入線の描画
    function draw2(x, y1, y2) {
        BPen2.drawLine(x + cx, y1 + cy, x + cx, y2 + cy);
        BPen2.paint();
        checkl = x;
    }
    function draw3() {
        BPen2.clear();
    }

    //○○主に一時ファイルの書き込み処理
    function LineQuestioneForm_Closing() {
        var cmbQues;
        var lblTerm;
        var lblOrder;
        cmbQues = document.Questions.QuesLevel;
        cmbQues = cmbQues.selectedIndex;
        lblTerm = document.Questions.TermText.value;
        //alert(lblOrder);
        var $params = 'param1=' + encodeURIComponent(WriteAID)
                    + '&param2=' + encodeURIComponent(index)
                    + '&param3=' + encodeURIComponent(lblTerm)
                    + '&param5=' + encodeURIComponent(WriteAnswer);
        new Ajax.Request(URL + 'writeAns.php',
        {
            method: 'get',
            onSuccess: getWrite,
            onFailure: getError,
            parameters: $params
        });
        //▲マウスデータの取得
        function getWrite(req) {
            //alert(req.responseText);
            alert("お疲れ様です!OKを押してデータの書き込み開始です。");
            new Ajax.Request(URL + 'ewrite.php',
        {
            method: 'get',
            onSuccess: getA,
            onFailure: getE
        });
            //▲マウスデータの取得
            //ドラッグ開始地点の保存
            function getA(req) {
                mybutton = 0;
                alert(req.responseText);
                window.close();
            }
            function getE(req) {
                alert("失敗、何度試してもできなかったら右上の×ボタンで終了してください。\nそして佐藤にご連絡をお願いします。");
            }
        }
        function getError(req) {
            alert("失敗");
        }
    }

    //○○決定ボタン
    function Button1_Click() {
        //終了条件チェック
        if (Mylabels_ea.length != Mylabels2.length) {
            alert("まだ並べ替えが終了していません。");
            return;
        }
        //固定ラベルチェック
        for (i = 0; i < FixNum.length; i++) {
            var fixcheck = 0;
            fixcheck = Mylabels_ea[FixNum[i]].innerHTML;
            if (FixLabels[i] != fixcheck) {
                var fixnum2 = FixNum[i] + 1
                var fix_a = confirm("位置固定単語である" + FixLabels[i] + "が" + fixnum2 + "番目に来ていませんが、宜しいですか？");
                if (fix_a == true) {
                    continue;
                }
                else {
                    return;
                }
            }
        }

        BPen3.clear();
        YAHOO.util.Dom.setStyle("Button1", "display", "none");
        var P = new Point(0, 0);
        P.x = event.x;
        P.y = event.y;
        if (Mouse_Flag == false) {
            return;
        }
        Mouse_Flag = false;
        //区切りラベルを削除
        //if(kugiri_num > 0){
        //Kugiri_delete();
        //}
        myStop = new Date();
        mTime = myStop.getTime() - myStart.getTime();

        //解答したのでマウスの動きをとるのをやめる
        //Mouse_Flag = false;
        myCheck(0); //ストップウォッチを止める


        //グループ化されたコントロールの初期化
        for (i = 0; i <= MyControls.length - 1; i++) {
            YAHOO.util.Dom.setStyle(Mylabels_ea[i], "color", "black");
        }
        //削除
        MyControls.splice(0, MyControls.length - 1);

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
        function getA(req) {
            //alert(req.responseText);
        }
        function getE(req) {
            alert("失敗");
        }

        //先頭の文字列を大文字に変換
        str1 = Mylabels_ea[0].innerHTML.substr(0, 1);
        str2 = Mylabels_ea[0].innerHTML.substr(1);
        Mylabels_ea[0].innerHTML = str1.toUpperCase() + str2; //先頭の文字が大文字に

        //ピリオドまたはクエスチョンを最後につける
        Mylabels_ea[Mylabels2.length - 1].innerHTML += PorQ;

        //自分の解答を文字列に格納
        for (i = 0; i <= Mylabels2.length - 1; i++) {
            //区切りラベルは解答に入れない
            if (Mylabels_ea[i].innerHTML == "/") {
                continue;
            }
            MyAnswer += Mylabels_ea[i].innerHTML + " ";
        }
        MyAnswer = MyAnswer.replace(/^\s+|\s+$/g, ""); //前後の空白削除
        WriteAnswer = MyAnswer;

        ResAns += 1;
        AllResAns += 1;

        //YAHOO.util.Dom.setStyle("RichTextBox3","display","block");
        YAHOO.util.Dom.setStyle("TextBox1", "display", "block");
        //YAHOO.util.Dom.setStyle("Button2","display","block");
        YAHOO.util.Dom.setStyle("Label2", "display", "block");
        YAHOO.util.Dom.setStyle("QuesLevel", "display", "block");
        YAHOO.util.Dom.setStyle("QuesLabel", "display", "block");
        YAHOO.util.Dom.setStyle("TermText", "display", "block");
        YAHOO.util.Dom.setStyle("TermLabel", "display", "block");
        YAHOO.util.Dom.setStyle("OrderLabel", "display", "block");
        document.getElementById("Button2").disabled = true;
        document.getElementById("Buttonl").disabled = true;
        //document.getElementById("Button2").disabled = false;
    }
    //○○次の問題ボタン
    function Button2_Click() {

        //if (AID == 30) {
        //    alert("実験終了です。右下の終了ボタンを押して書き込みを行ってください。");
        //   return;
        //} else if (AID % 5 == 0) {
        //  alert("右下の終了ボタンを押して書き込みを行ってください。");
        // return;
        //}

        if (Mouse_Flag == true) {
            return;
        }
        var cmbQues;
        var lblTerm;
        var lblOrder;
        cmbQues = document.Questions.QuesLevel;
        cmbQues = cmbQues.selectedIndex;
        lblTerm = document.Questions.TermText.value;
        var $params = 'param1=' + encodeURIComponent(AID - 1)
                    + '&param2=' + encodeURIComponent(cmbQues)
                    + '&param3=' + encodeURIComponent(lblTerm)
                    + '&param5=' + encodeURIComponent(WriteAnswer);
        new Ajax.Request(URL + 'writeAns.php',
        {
            method: 'get',
            onSuccess: getWrite,
            onFailure: getE,
            parameters: $params
        });
        //▲マウスデータの取得
        function getWrite(req) {
            //alert(req.responseText);
            document.Questions.TermText.value = "";
            document.Questions.QuesLevel.options[0].selected = true;
            MyAnswer = "";
        }
        function getE(req) {
            alert("失敗");
        }
        document.getElementById("Button2").disabled = true;
        document.getElementById("Buttonl").disabled = true;
        YAHOO.util.Dom.setStyle("Button1", "display", "block");
        YAHOO.util.Dom.setStyle("RichTextBox2", "display", "none");
        YAHOO.util.Dom.setStyle("RichTextBox3", "display", "none");
        YAHOO.util.Dom.setStyle("Button2", "display", "none");
        YAHOO.util.Dom.setStyle("TextBox1", "display", "none");
        YAHOO.util.Dom.setStyle("Label2", "display", "block");
        YAHOO.util.Dom.setStyle("QuesLevel", "display", "none");
        YAHOO.util.Dom.setStyle("QuesLabel", "display", "none");
        YAHOO.util.Dom.setStyle("TermText", "display", "none");
        YAHOO.util.Dom.setStyle("TermLabel", "display", "none");
        YAHOO.util.Dom.setStyle("OrderLabel", "display", "none");
        for (i = 0; i <= LabelNum - 1; i++) {
            _delete_dom_obj(i);
        }
        for (i = 0; i >= -MyNums.length + 1; i--) {
            //alert(i); 
            _delete_dom_obj(i);
        }
        MyNums.splice(0, MyNums.length - 1);
        StartQues = "";

        FixLabels = new Array(); //固定ラベル
        FixNum = new Array(); //固定ラベルの番号
        FixText = new Array(); //固定ラベルのタグを含むテキスト
        //ここで初期化類やっちゃおう
        Mylabels_r1.length = 0;
        Mylabels_r2.length = 0;
        Mylabels_r3.length = 0;
        Mylabels_ea.length = 0;
        Mylabels2.length = 0;
        Mylabels_left.length = 0;

        setques();
        WriteAID = AID;
        linedataFlg = false;
    }
    //解答ラベルの削除-----------------------------
    function _delete_dom_obj(id_name) {
        var dom_obj = document.getElementById(id_name);
        var dom_obj_parent = dom_obj.parentNode;
        //alert('ID: '+dom_obj.getAttribute('id')+' を削除します');
        dom_obj_parent.removeChild(dom_obj);
    }
    //スタート
    function Button3_Click() {
        if (AID == 30) {
            alert("実験は終了しています。右上の×ボタンを押して終了してください。");
        }

        if (Mouse_Flag == true) {
            return;
        }
        setques();
        document.getElementById("Button3").disabled = true;
        document.getElementById("Buttonl").disabled = true;
        YAHOO.util.Dom.setStyle("Button3", "display", "none");
        YAHOO.util.Dom.setStyle("Button1", "display", "block");
    }
    //正誤表示
    function print_answer() {
        if (MyAnswer == Answer || MyAnswer == Answer1 || MyAnswer == Answer2) {
            document.getElementById("RichTextBox3").innerHTML = "正誤：○";
            YAHOO.util.Dom.setStyle("RichTextBox3", "color", "red");
            //DBに登録するときは１とするように変更が必要
            TF = 1;

            CorrectAns += 1;
            AllCorrectAns += 1;
        }
        else {
            document.getElementById("RichTextBox3").innerHTML = "正誤：×";
            YAHOO.util.Dom.setStyle("RichTextBox3", "color", "blue");
            //DBに登録するときは0とするように変更が必要
            TF = 0;
            document.getElementById("RichTextBox2").innerHTML = "回答</br>" + Answer;
            YAHOO.util.Dom.setStyle("RichTextBox2", "display", "block");
        }

        //CorrectAnsRate = CorrectAns / ResAns * 100 //今回の正解率の計算
        //AllCorrectAnsRate = AllCorrectAns / AllResAns * 100 //全体の正解率の計算

        //CorrectAnsRate = Math.round(CorrectAnsRate * 100) / 100; //小数第三位を四捨五入するため
        //AllCorrectAnsRate = Math.round(AllCorrectAnsRate * 100) / 100; //小数第三位を四捨五入するため

        //alert(AllCorrectAns);
        var $rank = "s";
        var $NowPer = AllCorrectAns / AllResAns;
        $params = 'param1=' + encodeURIComponent($rank)
                + '&param2=' + encodeURIComponent($NowPer);
        new Ajax.Request(URL + 'rank.php',
        {
            method: 'get',
            onSuccess: getrankt,
            onFailure: getError,
            parameters: $params
        });
        function getrankt(res) {
            //alert(res.responseText);
            res.responseText -= 0;
            CorrectRank = res.responseText;
            //順位の表示---------
            if (CorrectRank != -1) {
                CorrectRank = CorrectRank + 1;
            } else {
                CorrectRank = ResRank;
            }
            document.getElementById("ListBox1").innerHTML = "全体 ( " + AllCorrectAns + " / " + AllResAns + "  " + AllCorrectAnsRate + "% )</br>"
                    + "今回 ( " + CorrectAns + " / " + ResAns + "  " + CorrectAnsRate + "% ) </br>"
                    + "順位 ( " + CorrectRank + " / " + ResRank + " 位 )";
            //----------------------
        }
        function getError(req) {
            alert("順位取得失敗");
        }
        if (WriteAID == AID) {
            AID += 1;
            //alert(AID + "," + WriteAID);
            var myStoppers;
            var mTimers;
            myStoppers = new Date();
            mTimers = myStoppers.getTime() - myStart.getTime();

            $QAData["QN"] = $Ques_Num;
            $QAData["ADate"] = Answertime;
            $QAData["TF"] = TF;
            $QAData["Time"] = mTimers;
            $QAData["FQues"] = StartQues;
            $QAData["AID"] = AID - 1;

            var $params = 'param1=' + encodeURIComponent($QAData["QN"])
                        + '&param2=' + encodeURIComponent($QAData["ADate"])
                        + '&param3=' + encodeURIComponent($QAData["TF"])
                        + '&param4=' + encodeURIComponent($QAData["Time"])
                        + '&param5=' + encodeURIComponent($QAData["FQues"])
                        + '&param6=' + encodeURIComponent($QAData["AID"]);
            if (!(linedataFlg)) {
                linedataFlg = true;
                new Ajax.Request(URL + 'tmpfile2.php',
                {
                    method: 'get',
                    onSuccess: getA,
                    onFailure: getE,
                    parameters: $params
                });
                //▲マウスデータの取得
                //ドラッグ開始地点の保存
                function getA(req) {
                    //alert(req.responseText);
                }
                function getE(req) {
                    alert("失敗");
                }
            }
            var myStopper = new Date();
            var mTimer = myStopper.getTime() - myStart2.getTime();
            //alert($AccessDate);
            var $params = 'param1=' + encodeURIComponent(ResAns)
                        + '&param2=' + encodeURIComponent(mTimer)
                        + '&param3=' + encodeURIComponent($AccessDate);
            new Ajax.Request(URL + 'tmpfile3.php',
            {
                method: 'get',
                onSuccess: getA,
                onFailure: getE,
                parameters: $params
            });
            //▲マウスデータの取得
            //ドラッグ開始地点の保存
            function getA(req) {
                document.getElementById("msg").innerHTML = req.responseText;
                document.getElementById("Button2").disabled = false;
            }
            function getE(req) {
                alert("失敗");
            }

            Mouse_Num = 0;
        }
    }
    //自信度決定
    function ButtonM_Click() {
        print_answer();
        YAHOO.util.Dom.setStyle("QuesLabel", "display", "none");
        YAHOO.util.Dom.setStyle("QuesLevel", "display", "none");
        YAHOO.util.Dom.setStyle("RichTextBox3", "display", "block");
        YAHOO.util.Dom.setStyle("ButtonM", "display", "none");
        YAHOO.util.Dom.setStyle("Button2", "display", "block");
        document.getElementById("Buttonl").disabled = false;
    }
    //自信度変更
    function QuesLevelChange() {
        var obj;
        var DocStrL = document.getElementById('Buttonl');
        var DocStr2 = document.getElementById('Button2');
        obj = document.Questions.QuesLevel;

        index = obj.selectedIndex;
        if (index != 0) {
            DocStrL.disabled = false;
            DocStr2.disabled = false;
            YAHOO.util.Dom.setStyle("ButtonM", "display", "block");
        } else {
            DocStrL.disabled = true;
            //YAHOO.util.Dom.setStyle("Button2","display","none");
            DocStr2.disabled = true;
        }
        document.getElementById("Buttonl").disabled = true;
    }
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<body bgcolor = #efffef onLoad = "ques_Load()" onMouseDown = "Form1_MouseDown()" onMouseUp = "Form1_MouseUp()">

<!--スタートボタン-->
<input type = "button"
	id = "Button3"
	value="スタート"
	onclick="Button3_Click()"
	style="width:80px;height:36px;position:absolute;left:768px;top:27px"/>

<!--決定ボタン-->
<input type = "button"
	id = "Button1"
	value="決定"
	onclick="Button1_Click()"
	style="width:80px;height:36px;position:absolute;left:768px;top:27px;display:none"/>

<!--自信度決定-->
<input type = "button"
	id = "ButtonM"
	value="自信度決定"
	onclick="ButtonM_Click()"
	style="width:80px;height:30px;position:absolute;left:600px;top:300px;display:none"/>

<!--次の問題ボタン-->
<input type = "button"
	id = "Button2"
	value="次の問題"
	onclick="Button2_Click()"
	style="width:75px;height:33px;position:absolute;left:768px;top:600px;display:none"/>

<!--終了ボタン-->
<input type = "button"
	id = "Buttonl"
	value="終了"
	onclick="LineQuestioneForm_Closing()"
	style="width:75px;height:20px;position:absolute;left:768px;top:655px;background-color:pink;display:block"/>

<!--日本文-->
<div id = "RichTextBox1" style="background-color:#ccff99;position:absolute;
     left:12;top:27;width:731;height:36;border-style:inset">
                                   	   ここに日本文が表示されます</div>
<!--回答-->
<div id = "RichTextBox2" style="background-color:#a1ffa1;position:absolute;
	 left:12;top:602;width:650;height:67;border-style:inset;display:none">ここに回答を表示</div>

<!--正誤-->
<div id = "RichTextBox3" style="background-color:#a1ffa1;position:absolute;
	 left:670;top:635;width:80;height:34;border-style:inset;display:none">正誤を表示</div>

<!--解答時間-->
<div id = "TextBox1"  style="background-color:#a1ffa1;position:absolute;
	 left:670;top:602;width:80;height:23;border-style:inset;display:none">解答時間</div>

<!--機能説明-->
<div id = "Label2" style="position:absolute;
	 left:12;top:500;width:300;height:50;font-size:12;background-color:#a1ffa1;">
	 	 *操作説明</br>
		</br>
	 	 <b>単語の移動：ドラッグ＆ドロップ</b></br>
	 	 <b>グループ化：単語がないところでドラッグ</b></br></div>

<!--レジスタ-->
<!--<div id = "register" style="position:absolute;
	 left:40;top:200;font-size:13;display:block">
	 	* レジスタ1</br></br></br></br></br>
		* レジスタ2</br></br></br></br></br>
		* レジスタ3</br></div>-->

<div id = "register1" style="padding: 10px; border: 2px dotted #333333;position:absolute;
	left:12;top:240;width:500;height:15;font-size:12;"></div>

<div id = "register2" style="padding: 10px; border: 2px dotted #333333;position:absolute;
	left:12;top:320;width:500;height:15;font-size:12;"></div>

<div id = "register3" style="padding: 10px; border: 2px dotted #333333;position:absolute;
	left:12;top:400;width:500;height:15;font-size:12;"></div>

<!--解答欄-->
<div id = "answer" style="padding: 10px; border: 2px solid #333333;position:absolute;
	left:12;top:150;width:700;height:20;font-size:12;"></div>

<!--解答欄-->
<div id = "question" style="padding: 10px; border: 2px solid #333333;position:absolute;
	left:12;top:80;width:700;height:20;font-size:12;"></div>

<!--キャンバス-->
<div id="myCanvas" style="position:absolute;top:0;left:0;height:500px;width:500px;z-index:-1"></div>

<!--キャンバス2-->
<div id="myCanvas2" style="position:absolute;top:0;left:0;height:500px;width:500px;z-index:-1"></div>

<!--メモ-->
<div id="msg" style="position:absolute;
	 left:50;top:300;width:500;height:30;font-size:12;background-color:#a1ffa1;display:none"></div>

<!--固定情報-->
<div id="Fixmsg" style="position:absolute;
	 left:320;top:500;width:200;height:50;font-size:12;background-color:#a1ffa1;display:block">-情報-</div>
<form name="Questions">

<!--自信度--> 
<label for="QuesLevel" id="QuesLabel" style="position:absolute;left:600px;top:240px;display:none">
解答の自信度</label>
<select 
	id = "QuesLevel"
	size="1"
	onChange="QuesLevelChange()"
	style="position:absolute;left:600px;top:260px;display:none">
<option value="choose" selected="selected">選択してください</option>
<option value="level4">4:自信がある(75%以上)</option>
<option value="level3">3:完全には自信がない(50～75%程度)</option>
<option value="level2">2:あまり自信がない(25～50%程度)</option>
<option value="level1">1:自信がない(25%未満)</option>
<option value="level0">0:誤って決定ボタンを押した</option>
</select>

<!--ダミー-->
<input type="hidden" id="TermText" value="">

 </form>
</body>
</html>