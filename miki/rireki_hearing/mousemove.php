<html>
<head>
<title>マウス軌跡再現</title>

<script type="text/javascript">
//ここから
var nativeSetInterval = window.setInterval;

_setInterval = {};

window.setInterval = function(process, delay) {
	var entry;

	if(typeof process == 'string') {
		entry = new _setInterval.Entry(function(){eval(process);}, delay);
	}
	else if(typeof process == 'function') {
		entry = new _setInterval.Entry(process, delay);
	}
	else {
		throw Error('第一引数が不正です。');
	}

	var id = _setInterval.queue.length;
	_setInterval.queue[id] = entry
	return id;
};

window.clearInterval = function(id) {
	_setInterval.queue[id].loop = function(){};
};

_setInterval.queue = [];

_setInterval.Entry = function(process, delay) {
	this.process = process;
	this.delay   = delay;
	this.time    = 0;
};

_setInterval.Entry.prototype.loop = function(time) {
	this.time += time;
	while(this.time >= this.delay) {
		this.process();
		this.time -= this.delay
	}
};

_setInterval.lastTime = new Date().getTime();

nativeSetInterval(function() {
	
	var time = new Date().getTime();
	
	var subTime =  time - _setInterval.lastTime;

	_setInterval.lastTime = time;

	for(var i = 0; i < _setInterval.queue.length; i++) {
		_setInterval.queue[i].loop(subTime);
	}

}, 10);

</script>

<?php
	require("dbc.php");
?>
<style type="text/css">
 <!--
 th,td{
 font-size:11pt;
 }
 -->
 </style>
 
<style type="text/css"><!--
div#myCanvasb
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas2
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas2_1
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas2_2
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas2_3
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas2_4
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas3
{
  position:absolute;  
  left:50px;
  top:150px;
}

div#myCanvas3_1
{
  position:absolute;  
  left:50px;
  top:150px;
}

div#myCanvas3_2
{
  position:absolute;  
  left:50px;
  top:150px;
}

div#myCanvas3_3
{
  position:absolute;  
  left:50px;
  top:150px;
}

div#myCanvas3_4
{
  position:absolute;  
  left:50px;
  top:150px;
}

div#myCanvas4
{
  position:absolute;  
  left:50px;
  top:150px;
}

div#myCanvas5
{
  position:absolute; 
  left:50px;
  top:150px;
}

div#myCanvas6
{
  position:absolute; 
  left:50px;
  top:150px;
}

#jquery-ui-slider-value {
    border: 0;
    color: red !important;
    font-weight: bold;
    background-color: transparent;
    margin: 5px;
    width: 100px;
}
#jquery-ui-slider {
    margin: 0 10px;
    width: 300px;
}

-->

</style>

<?php

//uid,wid,aidを受け取る
$data_list = array();
$data_list = $_POST["datalist"];

//受け取ったものをコンマで区切る
$ID = array();
$ID = explode(",",$data_list);

//各データを変数に格納
$uid = $ID[0];
$wid = $ID[1];
$aid = $ID[2];

// データベースから値を取り出す
$query = "select distinct(Time),X,Y,DD,DPos,hLabel,Label,addk from linedatamouse where uid = $uid and AID = $aid order by Time";
$res = mysql_query($query,$conn);

$query2 = "select EndSentence,Understand from AnswerQues where uid = $uid and AID = $aid";
$res2 = mysql_query($query2,$conn);

$query3 = "select Japanese,Sentence,grammar,level,start from question_info where WID = $wid";
$res3 = mysql_query($query3,$conn);

$query4 = "select Distance,AveSpeed,MaxStopTime,point,GroupCount,UTurnCount,DragDropCount from trackdata where uid = $uid and AID = $aid";
$res4 = mysql_query($query4,$conn);

$query5 = "select Time from linedata where uid = $uid and AID = $aid";
$res5 = mysql_query($query5,$conn);

$row = mysql_fetch_array($res2);
$es = $row['EndSentence'];
$us = $row['Understand'];

$row2 = mysql_fetch_array($res3);
$js = $row2['Japanese'];
$se = $row2['Sentence'];
$grammar = $row2['grammar'];
$level = $row2['level'];
$start = $row2['start'];

$row3 = mysql_fetch_array($res4);
$point = $row3['point'];
$avespeed = $row3['AveSpeed'];
$distance = $row3['Distance'];
$groupcount = $row3['GroupCount'];
$uturncount = $row3['UTurnCount'];
$maxstoptime = $row3['MaxStopTime'];
$dragdropcount = $row3['DragDropCount'];

$row4 = mysql_fetch_array($res5);
$a_time = $row4['Time']/1000;

$grammar_split = array();
$grammar_split = explode("#",$grammar);

array_pop($grammar_split);
array_shift($grammar_split);

$grammar_print = array();

for($i = 0; $i<count($grammar_split); $i++)
{

$query6 = "select Item from grammar where PID = $grammar_split[$i]";
$res6 = mysql_query($query6,$conn);
$row5 = mysql_fetch_array($res6);

$grammar_print[$i] = $row5['Item'];
}

$time = array();
$x = array();
$y = array();
$DD = array();
$DPos = array();
$hLabel = array();
$Label = array();
$addk = array();

// 切り取って配列へ
while ( $Column = mysql_fetch_array($res,MYSQL_ASSOC) ){
	$time[] = $Column['Time'];
	$x[] = $Column['X'];
	$y[] = $Column['Y'];
	$DD[] = $Column['DD'];
	$DPos[] = $Column['DPos'];
	$hLabel[] = $Column['hLabel'];
	$Label[] = $Column['Label'];
	$addk[] = $Column['addk'];
}

$timestring = "";
$xstring = "";
$ystring = "";
$DDstring = "";
$DPosstring = "";
$hLabelstring = "";
$Labelstring = "";
$addkstring = "";

// 繋げて配列へ格納(Javascriptへ値を渡すため)
for ( $i=0; $i<count($time); $i++ ){
	if ( $i > 0 ){
		$timestring .= "###";
		$xstring .= "###";
		$ystring .= "###";
		$DDstring .= "###";
		$DPosstring .= "###";
		$hLabelstring .= "###";
		$Labelstring .= "###";
		$addkstring .= "###";
	}
	$timestring .= $time[$i];
	$xstring .= $x[$i];
	$ystring .= $y[$i];
	$DDstring .= $DD[$i];
	$DPosstring .= $DPos[$i];
	$hLabelstring .= $hLabel[$i];
	$Labelstring .= $Label[$i];
	$addkstring .= $addk[$i];
}

?>


<script type="text/javascript" src="wz_jsgraphics.js"></script>

<script type="text/javascript">






var t = 0;
var x = 0;
var y = 0;

// linedatamouse内の各情報
var t_point = new Array();
var x_point = new Array();
var y_point = new Array();
var DD_point = new Array();
var DPos_point = new Array();
var hLabel_point = new Array();
var Label_point = new Array();
var addk_point = new Array();

// 初期の英単語の並び情報
var start_point = new Array();

var tstring = "<?php echo $timestring; ?>";
var xstring = "<?php echo $xstring; ?>";
var ystring = "<?php echo $ystring; ?>";
var DDstring = "<?php echo $DDstring; ?>";
var DPosstring = "<?php echo $DPosstring; ?>";
var hLabelstring = "<?php echo $hLabelstring; ?>";
var Labelstring = "<?php echo $Labelstring; ?>";
var addkstring = "<?php echo $addkstring; ?>";

var startstring = "<?php echo $start; ?>";

t_point = tstring.split("###");
x_point = xstring.split("###");
y_point = ystring.split("###");
DD_point = DDstring.split("###");
DPos_point = DPosstring.split("###");
hLabel_point = hLabelstring.split("###");
Label_point = Labelstring.split("###");
addk_point = addkstring.split("###");

UTurnFlag = 0;
UTurnCount = 0;
</script>

</head>
<body>
<!--各フォーム-->
<form name="myForm" action="#">
   <div>
    <input type="text" size="20" name="time" disabled>
    <select NAME = "speed" SIZE = 1>
    <OPTION value = 5>等倍</option>
    <OPTION value = 2.5>0.5倍</option>
    <OPTION value = 10>2倍</option>
	<OPTION value = 15>3倍</option>
	<OPTION value = 25>5倍</option>
	</select>
    <input type="button" value="軌跡再現" name="start" id = "start_b" 
      onclick="interval(); DrawAline();">
    <input type="button" value="一時停止" name="stop" 
      onclick="stop_interval()">
    <input type="button" value="リセット" name="reset"
      onclick="reset_c()">
   </div>
</form>

<link rel="stylesheet" href="themes/base/jquery.ui.all.css" />
<script type="text/javascript" src="jquery-1.8.3.js"></script>
<script type="text/javascript" src="ui/jquery.ui.core.js"></script>
<script type="text/javascript" src="ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="ui/jquery.ui.mouse.js"></script>
<script type="text/javascript" src="ui/jquery.ui.slider.js"></script>

<p>途中から開始：<input type="text" id="jquery-ui-slider-value" /> / <script type="text/javascript">document.write(t_point[t_point.length-4]);</script>ms</p>
<div id="jquery-ui-slider"></div><br>

<!--文字の幅を計測するために使用-->
<script type="text/javascript" src="excanvas.js"></script>
<canvas id="canvas" width="0" height="0" style="visibility:hidden;position:absolute;"></canvas>

<!--描画用キャンバス・各パラメータ表示用テーブルなど-->
<table border = "1" cellspacing = "1" width = "1000" height = "500">
<tr><td>
<div id="myCanvasb">
</div>
<div id="myCanvas">
</div>
<div id="myCanvas2">
</div>
<div id="myCanvas2_1">
</div>
<div id="myCanvas2_2">
</div>
<div id="myCanvas2_3">
</div>
<div id="myCanvas2_4">
</div>
<div id="myCanvas3">
</div>
<div id="myCanvas3_1">
</div>
<div id="myCanvas3_2">
</div>
<div id="myCanvas3_3">
</div>
<div id="myCanvas3_4">
</div>
<div id="myCanvas4">
</div>
<div id="myCanvas5">
</div>
<div id="myCanvas6">
</div>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<table border = "0" width = "995" height = "200">
<tr>
<td>
<table border = "1" cellspacing = "1" width = "600" height = "200">
<tr><td>
<b><u>問題番号</u>：<?php
echo $wid;
?>　　　　<u>学籍番号</u>：<?php
echo $uid;
?></b><br>
<br>
<b><u>最終解答文</u>：<?php echo $es; ?>
</script></b><br>
<br>
<b><u>日本語文</u>：<?php echo $js; ?></b><br>
<b><u>正解文</u>：<?php echo $se; ?></b><br>
<br>
<b><u>自信度</u>：
<?php
if ($us == 0){print("間違って終了ボタンを押した");}
elseif ( $us == 1 ){print("4(自信がある)");}
elseif ( $us == 2 ){print("3(やや自信がある)");}
elseif ( $us == 3 ){print("2(やや自信がない)");}
elseif ( $us == 4 ){print("1(自信がない)");}
?>
</b><br>
<b><u>正誤</u>：
<?php
if ( $point == 10 ){print("○");}
else{print("×");}
?>
</b><br>
</td></tr></table>
</td>
<td>
<table border = "1" cellspacing = "1" width = "380" height = "200">
<tr><td>
文法項目：<?php
for($i = 0; $i<count($grammar_print); $i++)
{print($grammar_print[$i]);
print(" ");}
?>

　　
難易度：<?php
if ( $level == 1 ){print("初級");}
else if ( $level == 2 ){print("中級");}
else if ( $level == 3 ){print("上級");}
?>
<br>
<br>
得点：
<?php
echo $point;
?>点<br>
解答時間：<?php
echo $a_time;
?>秒<br>
Drag＆Drop回数：<?php
echo $dragdropcount;
?>回<br>
Uターン回数：<?php
echo $uturncount;
?>回<br>
マウス移動距離：<?php
echo $distance;
?>pixel<br>
平均マウス速度：<?php
echo $avespeed;
?>pixel/ms<br>
最大静止時間：<?php
echo $maxstoptime;
?>ms<br>
グループ化回数：<?php
echo $groupcount;
?>回<br>
</td></tr></table>
</td>
</tr></table>
</td></tr></table>



<script type="text/javascript">

    // 初期の英単語の並び情報を|で区切って入れる
    start_point = startstring.split("|");

    start_point_x = new Array();
    start_point_w = new Array();
    df_x = 30;
    wd = 0;

    for (l = 0; l < start_point.length; l++) {

        //単語の位置を記録
        start_point_x[l] = df_x;

        //単語の長さ取得
        wd = strWidth(start_point[l]);
        start_point_w[l] = wd;

        df_x = df_x + wd + 18;

    }

    var m = 0;
    var timer1;

    //単語のそれぞれの位置を記録しておく。単語をドロップする際に使用
    var Label_x = new Array();

    //グループ化の場合分け
    var grouptest = new Array();

    //現在の単語の並び順
    var now_list = new Array();

    //各配列・各座標用
    //問題提示欄
    var list_q = new Array();
    var Label_q = new Array();

    //最終解答欄
    var list_a = new Array();
    var Label_a = new Array();

    //レジスタ1
    var list_r1 = new Array();
    var Label_r1 = new Array();

    //レジスタ1
    var list_r2 = new Array();
    var Label_r2 = new Array();

    //レジスタ1
    var list_r3 = new Array();
    var Label_r3 = new Array();

    //キャンバス受け渡し用
    var jg_canvas = 0;

    //キャンバス受け渡し用
    var jg_canvas2 = 0;

    //イベントがどこで起こっているか？　0:問題提示欄 1:レジスタ1 2:レジスタ2 3:レジスタ3 4:最終解答欄
    var md_flag = -1;

    //最初だけリスト全表示したいのでそのためのフラグ
    var start_st = 0;

    //ここをまず問題提示欄に変える？？
    //ロードイベントと同時にできないかな。最初(ロードした時)だけフラグ立てておいて
    //文字出力するときにif文で分ける。出力したらフラグを戻す。

    for (l = 0; l < start_point.length; l++) {
        //単語の数だけ取得
        list_q[l] = l;
    }

    /*for (l = 0; l < start_point.length; l++){
    //単語の数だけ取得
    now_list[l] = l;
    }*/

    //初期配置記録用
    var start_t = start_point.concat();

    //Labelで指定された単語は現在何番目にあるのか記録
    var t_word = 0;

    //変換用
    var parse_t = 0;

    //単語（群）一時退避用
    var word = new Array();

    //単語移動フラグ
    var wordmove = 0;

    //スライダー用
    var slider = 0;

    //単語の長さを測る。IEだと上手く動いてくれない。
    function strWidth(str) {
        var canvas = document.getElementById('canvas');
        if (canvas.getContext) {
            var context = canvas.getContext('2d');
            context.font = "16px 'arial'";

            var metrics = context.measureText(str);
            return metrics.width;
        }
        return -1;
    }

    //キャンバスに単語を配置する。
    function DrawString() {

        //フラグによって各配列の中身をnow_listにぶち込み
        if (md_flag == 0) {
            now_list = list_q.slice(0);
            Label_x = Label_q.slice(0);
            var string_x = 30;
            var string_y = 100;
            jg_canvas = jg3
            jg_canvas2 = jg2
        }
        else if (md_flag == 1) {
            now_list = list_r1.slice(0);
            Label_x = Label_r1.slice(0);
            var string_x = 30;
            var string_y = 250;
            jg_canvas = jg3_1
            jg_canvas2 = jg2_1
        }
        else if (md_flag == 2) {
            now_list = list_r2.slice(0);
            Label_x = Label_r2.slice(0);
            var string_x = 30;
            var string_y = 330;
            jg_canvas = jg3_2
            jg_canvas2 = jg2_2
        }
        else if (md_flag == 3) {
            now_list = list_r3.slice(0);
            Label_x = Label_r3.slice(0);
            var string_x = 30;
            var string_y = 410;
            jg_canvas = jg3_3
            jg_canvas2 = jg2_3
        }
        else if (md_flag == 4) {
            now_list = list_a.slice(0);
            Label_x = Label_a.slice(0);
            var string_x = 30;
            var string_y = 170;
            jg_canvas = jg3_4
            jg_canvas2 = jg2_4
        }

        jg_canvas2.clear();
        jg_canvas.clear();




        if (md_flag != 0) {
            var l = 0;

            //単語初期位置 あとでけす
            //var string_x = 30;
            //var string_y = 100;

            //単語の長さ。x座標に足していく。
            var w_width = 0;

            for (l = 0; l < now_list.length; l++) {

                //単語のフォント設定
                jg_canvas.setFont("arial", "16px", Font.Plain);

                //単語の出力
                jg_canvas.drawString(start_point[now_list[l]], string_x, string_y);
                jg_canvas.paint();


                //単語の位置を記録
                Label_x[l] = string_x;

                //単語の長さ取得
                w_width = strWidth(start_point[now_list[l]]);

                //背景を付ける
                jg_canvas2.setColor("white");
                jg_canvas2.fillRect(string_x, string_y, w_width, 20);
                jg_canvas2.paint();

                string_x = string_x + w_width + 18;
            }
        }
        else {

            for (l = 0; l < now_list.length; l++) {

                //単語のフォント設定
                jg_canvas.setFont("arial", "16px", Font.Plain);

                //単語の出力
                jg_canvas.drawString(start_point[now_list[l]], start_point_x[now_list[l]], string_y);
                jg_canvas.paint();

                jg_canvas2.setColor("white");
                jg_canvas2.fillRect(start_point_x[now_list[l]], string_y, start_point_w[now_list[l]], 20);
                jg_canvas2.paint();
            }

        }

        //フラグによってnow_listをぶち込み
        if (md_flag == 0) {
            list_q = now_list.slice(0);
            Label_q = Label_x.slice(0);
        }
        else if (md_flag == 1) {
            list_r1 = now_list.slice(0);
            Label_r1 = Label_x.slice(0);
        }
        else if (md_flag == 2) {
            list_r2 = now_list.slice(0);
            Label_r2 = Label_x.slice(0);
        }
        else if (md_flag == 3) {
            list_r3 = now_list.slice(0);
            Label_r3 = Label_x.slice(0);
        }
        else if (md_flag == 4) {
            list_a = now_list.slice(0);
            Label_a = Label_x.slice(0);
        }


    }

    //リセット
    function reset_c() {

        t = 0;
        m = 0;
        document.myForm.time.value = "";

        stop_interval();

        jg_b.clear();
        jg.clear();
        jg2.clear();
        jg2_1.clear();
        jg2_2.clear();
        jg2_3.clear();
        jg2_4.clear();
        jg3.clear();
        jg3_1.clear();
        jg3_2.clear();
        jg3_3.clear();
        jg3_4.clear();
        jg4.clear();
        jg5.clear();
        jg6.clear();

        start_point.length = 0;
        now_list.length = 0;

        start_point = startstring.split("|");
        word.length = 0;

        list_q.length = 0;
        list_a.length = 0;
        list_r1.length = 0;
        list_r2.length = 0;
        list_r3.length = 0;

        Label_q.length = 0;
        Label_a.length = 0;
        Label_r1.length = 0;
        Label_r2.length = 0;
        Label_r3.length = 0;

        for (l = 0; l < start_point.length; l++) {
            list_q[l] = l;
        }


        document.getElementById("start_b").style.visibility = "visible";
        document.getElementById("jquery-ui-slider").style.visibility = "visible";

    }

    //インターバル開始
    function interval() {

        document.getElementById("start_b").style.visibility = "hidden";
        document.getElementById("jquery-ui-slider").style.visibility = "hidden";

        //ここで文字表示？(最初だけ！)
        //start_st = 0;

        md_flag = 0;
        DrawString();
        //String内で場合分けするか、もしくはイベントフラグを0にしてぶちこむ。←この場合はstart_stはいらないヨ

        for (n = t; n < slider; n++) {

            t = t + 1;

            if (t == t_point[m + 1]) {

                grouptest = Label_point[m + 1].split("#");

                //グループ化されていない場合
                if (DD_point[m + 1] == 2 && grouptest[1] == undefined) {

                    //ここでイベントが起こった場所によって場合分け
                    if (parseInt(y_point[m + 1]) <= 130)
                    { md_flag = 0; }
                    else if (parseInt(y_point[m + 1]) <= 215 && parseInt(y_point[m + 1]) > 130)
                    { md_flag = 4; }
                    else if (parseInt(y_point[m + 1]) <= 295 && parseInt(y_point[m + 1]) > 215)
                    { md_flag = 1; }
                    else if (parseInt(y_point[m + 1]) <= 375 && parseInt(y_point[m + 1]) > 295)
                    { md_flag = 2; }
                    else if (parseInt(y_point[m + 1]) > 375)
                    { md_flag = 3; }

                    WordDrag();
                    wordmove = 1;
                }

                //グループ化された場合(複数選択されている場合)
                if (DD_point[m + 1] == 2 && grouptest[1] != undefined) {

                    //ここでイベントが起こった場所によって場合分け
                    if (parseInt(y_point[m + 1]) <= 130)
                    { md_flag = 0; }
                    else if (parseInt(y_point[m + 1]) <= 215 && parseInt(y_point[m + 1]) > 130)
                    { md_flag = 4; }
                    else if (parseInt(y_point[m + 1]) <= 295 && parseInt(y_point[m + 1]) > 215)
                    { md_flag = 1; }
                    else if (parseInt(y_point[m + 1]) <= 375 && parseInt(y_point[m + 1]) > 295)
                    { md_flag = 2; }
                    else if (parseInt(y_point[m + 1]) > 375)
                    { md_flag = 3; }

                    WordGroup();
                    wordmove = 1;
                }

                //ドラッグ＆ドロップが行われた場合に、単語の並び順等を変更
                if (DD_point[m + 1] == 1) {

                    //ここでイベントが起こった場所によって場合分け
                    if (parseInt(y_point[m + 1]) <= 130)
                    { md_flag = 0; }
                    else if (parseInt(y_point[m + 1]) <= 215 && parseInt(y_point[m + 1]) > 130)
                    { md_flag = 4; }
                    else if (parseInt(y_point[m + 1]) <= 295 && parseInt(y_point[m + 1]) > 215)
                    { md_flag = 1; }
                    else if (parseInt(y_point[m + 1]) <= 375 && parseInt(y_point[m + 1]) > 295)
                    { md_flag = 2; }
                    else if (parseInt(y_point[m + 1]) > 375)
                    { md_flag = 3; }

                    WordDrop();
                    wordmove = 0;
                    jg4.clear();
                    jg5.clear();

                }

                /*
                //区切りラベル追加時
                if(addk_point[m+1] == 1){
                Add_k();
                }

                //区切りラベル全削除時
                //スラッシュ以外のものをdummy内に格納。最終的にnow_list=dummyに。
                if(addk_point[m+1] == 2){

                dummy.length = 0;

                for(j = 0; j < now_list.length; j++){
                if(start_point[now_list[j]] != "/"){
                dummy.push(now_list[j])
                }
                }

                splice_num = now_list.length - dummy.length;
                start_point.splice(dummy.length,splice_num)

                now_list.length = 0;
                now_list = dummy.concat();

                DrawString();

                }

                //区切りラベル個別削除時
                if(addk_point[m+1] == 3){

                indexof = 0;

                indexof = now_list.indexOf(parseInt(hLabel_point[m+1]));
                now_list.splice(indexof,1);
                start_point.splice(parseInt(hLabel_point[m+1]),1);

                DrawString();
                }*/

                m = m + 1;
				//バグ対策
				if(parseInt(t_point[m]) == parseInt(t_point[m+1])){m = m+1;}
            }
        }

        timer1 = setInterval(timer, 5);

    }

    //インターバル中止
    function stop_interval() {

        clearInterval(timer1);


        document.getElementById("start_b").style.visibility = "visible";

    }

    //タイマーに合わせて描画
    function timer() {

        var indexof = 0;
        var dummy = new Array();
        var splice_num = 0;

        var j = 0;
        var k = document.myForm.speed.value;
        for (j = 0; j < k; j++) {

            t = t + 1;
            document.myForm.time.value = t;

            if (t == t_point[m + 1]) {
                grouptest = Label_point[m + 1].split("#");

              //グループ化されていない場合
                if (DD_point[m + 1] == 2 && grouptest[1] == undefined) {

                    //ここでイベントが起こった場所によって場合分け
                    if (parseInt(y_point[m + 1]) <= 130)
                    { md_flag = 0; }
                    else if (parseInt(y_point[m + 1]) <= 215 && parseInt(y_point[m + 1]) > 130)
                    { md_flag = 4; }
                    else if (parseInt(y_point[m + 1]) <= 295 && parseInt(y_point[m + 1]) > 215)
                    { md_flag = 1; }
                    else if (parseInt(y_point[m + 1]) <= 375 && parseInt(y_point[m + 1]) > 295)
                    { md_flag = 2; }
                    else if (parseInt(y_point[m + 1]) > 375)
                    { md_flag = 3; }

                    WordDrag();
                    wordmove = 1;
                }

                //グループ化された場合(複数選択されている場合)
                if (DD_point[m + 1] == 2 && grouptest[1] != undefined) {

                    //ここでイベントが起こった場所によって場合分け
                    if (parseInt(y_point[m + 1]) <= 130)
                    { md_flag = 0; }
                    else if (parseInt(y_point[m + 1]) <= 215 && parseInt(y_point[m + 1]) > 130)
                    { md_flag = 4; }
                    else if (parseInt(y_point[m + 1]) <= 295 && parseInt(y_point[m + 1]) > 215)
                    { md_flag = 1; }
                    else if (parseInt(y_point[m + 1]) <= 375 && parseInt(y_point[m + 1]) > 295)
                    { md_flag = 2; }
                    else if (parseInt(y_point[m + 1]) > 375)
                    { md_flag = 3; }

                    WordGroup();
                    wordmove = 1;
                }

                //ドラッグ＆ドロップが行われた場合に、単語の並び順等を変更
                if (DD_point[m + 1] == 1) {

                    //ここでイベントが起こった場所によって場合分け
                    if (parseInt(y_point[m + 1]) <= 130)
                    { md_flag = 0; }
                    else if (parseInt(y_point[m + 1]) <= 215 && parseInt(y_point[m + 1]) > 130)
                    { md_flag = 4; }
                    else if (parseInt(y_point[m + 1]) <= 295 && parseInt(y_point[m + 1]) > 215)
                    { md_flag = 1; }
                    else if (parseInt(y_point[m + 1]) <= 375 && parseInt(y_point[m + 1]) > 295)
                    { md_flag = 2; }
                    else if (parseInt(y_point[m + 1]) > 375)
                    { md_flag = 3; }

                    WordDrop();
                    wordmove = 0;
                    jg4.clear();
                    jg5.clear();

                }

                //区切りラベル追加時
                /*if(addk_point[m+1] == 1){
                Add_k();
                }*/

                //区切りラベル全削除時
                //スラッシュ以外のものをdummy内に格納。最終的にnow_list=dummyに。
                /*if(addk_point[m+1] == 2){
                dummy.length = 0;

                for(j = 0; j < now_list.length; j++){
                if(start_point[now_list[j]] != "/"){
                dummy.push(now_list[j])
                }
                }

                splice_num = now_list.length - dummy.length;
                start_point.splice(dummy.length,splice_num)

                now_list.length = 0;
                now_list = dummy.concat();

                DrawString();

                }*/

                /*
                //区切りラベル個別削除時
                if(addk_point[m+1] == 3){

                indexof = 0;

                indexof = now_list.indexOf(parseInt(hLabel_point[m+1]));
                now_list.splice(indexof,1);
                start_point.splice(parseInt(hLabel_point[m+1]),1);

                DrawString();
                }*/
							
                DrawLine();
            }
        }
        //再現終了
        //if(DD_point[m+1] == -1){
        //alert("再現終了");
        //reset_c();

        if (t_point[t_point.length] == t_point[m + 1]) {
            alert("再現終了");
            UTurnCount = 0;
            reset_c();

        }

    }

    //区切りラベル追加処理
    /*function Add_k(){

    //最初のリストに新たにスラッシュを追加
    start_point.push("/");

    // 作業用配列
    var tmplist2 = new Array();
    tmplist2 = now_list;        //　一時的に作業用へ退避　この配列を処理に使用する

    // 1番左端だったときの検査
    if (parseInt(x_point[m+1]) <= Label_x[0]){
    tmplist2.splice(0, 0, tmplist2.length);
    }

    // 2番目～最後から2番目までの検査
    for(i = 0; i < (tmplist2.length-1); i++){
    if (parseInt(x_point[m+1]) > Label_x[i] && parseInt(x_point[m+1]) <= Label_x[i+1]){
    tmplist2.splice(i+1, 0, tmplist2.length);
    }
    }

    // 右端の検査
    if (parseInt(x_point[m+1]) > Label_x[tmplist2.length-1]){
    tmplist2.splice(tmplist2.length, 0, tmplist2.length);
    }


    now_list = tmplist2;
    DrawString();



    }*/



    //ドラッグ処理
    function WordDrag() {

		//三木さんすいません
		if(list_q.indexOf(parseInt(hLabel_point[m+1])) != -1){md_flag = 0;}
		if(list_r1.indexOf(parseInt(hLabel_point[m+1])) != -1){md_flag = 1;}
		if(list_r2.indexOf(parseInt(hLabel_point[m+1])) != -1){md_flag = 2;}
		if(list_r3.indexOf(parseInt(hLabel_point[m+1])) != -1){md_flag = 3;}
		if(list_a.indexOf(parseInt(hLabel_point[m+1])) != -1){md_flag = 4;}

        //フラグによって各配列の中身をnow_listにぶち込み
        if (md_flag == 0) { now_list = list_q.slice(0); }
        else if (md_flag == 1) { now_list = list_r1.slice(0); }
        else if (md_flag == 2) { now_list = list_r2.slice(0); }
        else if (md_flag == 3) { now_list = list_r3.slice(0); }
        else if (md_flag == 4) { now_list = list_a.slice(0); }

        //now_list(現在の並び順)の中で、hLabel_point[m+1]の値は何番目にあるのか調べて、t_wordに入れる
        t_word = now_list.indexOf(parseInt(hLabel_point[m + 1]));

        //ドラッグ中の単語を退避させておく
        parse_t = parseInt(t_word);
        word[0] = now_list[parse_t];

        //ドラッグ中の単語の消去
        now_list.splice(parse_t, 1);

        //フラグによってnow_listにぶち込み
        if (md_flag == 0) { list_q = now_list.slice(0); }
        else if (md_flag == 1) { list_r1 = now_list.slice(0); }
        else if (md_flag == 2) { list_r2 = now_list.slice(0); }
        else if (md_flag == 3) { list_r3 = now_list.slice(0); }
        else if (md_flag == 4) { list_a = now_list.slice(0); }

        DrawString();

    }

    //グループ化処理
    function WordGroup() {

        //フラグによって各配列の中身をnow_listにぶち込み
        if (md_flag == 0) { now_list = list_q.slice(0); }
        else if (md_flag == 1) { now_list = list_r1.slice(0); }
        else if (md_flag == 2) { now_list = list_r2.slice(0); }
        else if (md_flag == 3) { now_list = list_r3.slice(0); }
        else if (md_flag == 4) { now_list = list_a.slice(0); }

        word = Label_point[m + 1].split("#");

        //ドラッグ処理を単語数分繰り返すだけ
        for (i = 0; i < word.length; i++) {

            t_word = now_list.indexOf(parseInt(grouptest[i]));
            parse_t = parseInt(t_word);
            word[i] = now_list[parse_t];

            now_list.splice(parse_t, 1);

        }

        //フラグによってnow_listにぶち込み
        if (md_flag == 0) { list_q = now_list.slice(0); }
        else if (md_flag == 1) { list_r1 = now_list.slice(0); }
        else if (md_flag == 2) { list_r2 = now_list.slice(0); }
        else if (md_flag == 3) { list_r3 = now_list.slice(0); }
        else if (md_flag == 4) { list_a = now_list.slice(0); }

        DrawString();

    }

    //単語をどこにドロップするか見る
    function WordDrop() {

        //フラグによって各配列の中身をnow_listにぶち込み
        if (md_flag == 0) {
            now_list = list_q.slice(0);
            Label_x = Label_q.slice(0);
        }
        else if (md_flag == 1) {
            now_list = list_r1.slice(0);
            Label_x = Label_r1.slice(0);
        }
        else if (md_flag == 2) {
            now_list = list_r2.slice(0);
            Label_x = Label_r2.slice(0);
        }
        else if (md_flag == 3) {
            now_list = list_r3.slice(0);
            Label_x = Label_r3.slice(0);
        }
        else if (md_flag == 4) {
            now_list = list_a.slice(0);
            Label_x = Label_a.slice(0);
        }

        // 各変数のやくわりこーなー
        // x_point[m+1]:今まさにドロップが行われた場所
        // now_list[]:現時点での(単語持ってかれてるから最初よりは少なくﾅｯﾃｲﾙﾖ)単語の並び順
        // word[]:今退避させてある単語(群)(ドラッグ中の単語)
        // Label_x[]:現時点での単語の並び順における、各単語のx座標

        // 作業用配列
        var tmplist = new Array();
        tmplist = now_list;     //　一時的に作業用へ退避　この配列を処理に使用する

        if (now_list[0] == undefined || md_flag == 0) {
            for (j = 0; j < word.length; j++) {
                tmplist.splice(0 + j, 0, word[j]);
            }
        }
        else {
            // 1番左端だったときの検査
            if (parseInt(x_point[m + 1]) <= Label_x[0]) {
                for (j = 0; j < word.length; j++) {
                    tmplist.splice(0 + j, 0, word[j]);
                }
            }

            // 2番目～最後から2番目までの検査
            for (i = 0; i < (tmplist.length - 1); i++) {
                if (parseInt(x_point[m + 1]) > Label_x[i] && parseInt(x_point[m + 1]) <= Label_x[i + 1]) {
                    for (j = 0; j < word.length; j++) {
                        tmplist.splice(i + 1 + j, 0, word[j]);
                    }
                }
            }

            // 右端の検査
            if (parseInt(x_point[m + 1]) > Label_x[tmplist.length - 1]) {
                for (j = 0; j < word.length; j++) {
                    tmplist.splice(tmplist.length + j, 0, word[j]);
                }

                //デバッグ用だよ
                /*
                var printstring = "";
                for ( var p=0; p<tmplist.length; p++ ){
                printstring += "|" + tmplist[p];
                }
                alert(printstring);
                */
            }
        }
        now_list = tmplist;

        //フラグによってnow_listにぶち込み
        if (md_flag == 0) { list_q = now_list.slice(0); }
        else if (md_flag == 1) { list_r1 = now_list.slice(0); }
        else if (md_flag == 2) { list_r2 = now_list.slice(0); }
        else if (md_flag == 3) { list_r3 = now_list.slice(0); }
        else if (md_flag == 4) { list_a = now_list.slice(0); }

        DrawString();
        word.length = 0;

    }


    //線を描画
    function DrawLine() {
        jg6.clear();

        if (t <= 10000) { jg.setColor("pink"); }
        else if (t > 10000 && t <= 20000) { jg.setColor("blue"); }
        else if (t > 20000 && t <= 30000) { jg.setColor("orange"); }
        else if (t > 30000 && t <= 40000) { jg.setColor("green"); }
        else if (t > 40000 && t <= 50000) { jg.setColor("red"); }
        else if (t > 50000) { jg.setColor("black"); }

        //int型に変換
        var x1 = parseInt(x_point[m]);
        var y1 = parseInt(y_point[m]);
        var x2 = parseInt(x_point[m + 1]);
        var y2 = parseInt(y_point[m + 1]);

        jg.drawLine(x1, y1, x2, y2);


 /*       if (UTurnFlag == 0) {
            if ((x2 - x1) == 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
            } else if ((x2 - x1) > 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
                UTurnFlag = 1;
            } else if ((x2 - x1) < 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
                UTurnFlag = -1;
            }
        } else if (UTurnFlag == 1) {
            if ((x2 - x1) == 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
            } else if ((x2 - x1) > 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
            } else if ((x2 - x1) < 0) {
                UTurnFlag = -1;
                //alert("Uターン");
                jg.fillRect(x1, y1, 10, 10);
                UTurnCount++;
            }
        } else if (UTurnFlag == -1) {
            if ((x2 - x1) == 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
            } else if ((x2 - x1) > 0) {
                UTurnFlag = 1;
                //alert("Uターン");
                jg.fillRect(x1, y1, 10, 10);
                UTurnCount++;
            } else if ((x2 - x1) < 0) {
                jg.drawEllipse(x1, y1 - 2, 4, 4);
            }
        }
*/

        jg.drawEllipse(x2, y2 - 2, 4, 4);
        jg.paint();

        //マウスポインター
        jg6.drawImage("pointer001.png", x2, y2, 10, 18);
        jg6.paint();

/*
        //決定ボタン
        jg_b.drawImage("kettei.png", 760, 30, 78, 35);
        //区切りボタン
        //jg_b.drawImage("kugiri.png", 15, 162, 88, 28);
       //問題提示欄
        jg_b.drawRect(12, 80, 700, 40);
        //最終解答欄
        jg_b.drawRect(12, 150, 700, 40);
        //レジスタ3つ
        jg_b.drawRect(12, 240, 500, 30);
        jg_b.drawRect(12, 320, 500, 30);
        jg_b.drawRect(12, 400, 500, 30);

        jg_b.paint();
        */

        if (wordmove == 1) { WordMove(); }



        m = m + 1;
//バグ対策
if(parseInt(t_point[m]) == parseInt(t_point[m+1])){m = m+1;}


    }


function DrawAline(){

        //決定ボタン
        jg_b.drawImage("kettei.png", 760, 30, 78, 35);
        //問題提示欄
        jg_b.drawRect(12, 80, 700, 40);
        //最終解答欄
        jg_b.drawRect(12, 150, 700, 40);
        //レジスタ3つ
        jg_b.drawRect(12, 240, 500, 30);
        jg_b.drawRect(12, 320, 500, 30);
        jg_b.drawRect(12, 400, 500, 30);

 jg_b.paint();

}

    function WordMove() {
        jg4.clear();
        jg5.clear();

        var n = 0;

        //単語初期位置
        var w_string_x = parseInt(x_point[m + 1]);
        var w_string_y = parseInt(y_point[m + 1]) - 10;

        //単語の長さ。x座標に足していく。
        var w_width_2 = 0;

        for (n = 0; n < word.length; n++) {

            //単語のフォント設定
            jg5.setFont("arial", "16px", Font.Plain);

            //単語の出力
            jg5.drawString(start_point[word[n]], w_string_x, w_string_y);
            jg5.paint();

            //単語の長さ取得。足す。
            w_width_2 = strWidth(start_point[word[n]]);

            jg4.setColor("yellow");
            jg4.fillRect(w_string_x, w_string_y, w_width_2, 20);
            jg4.paint();

            w_string_x = w_string_x + w_width_2 + 17;
        }
    }

    jQuery(function () {
        jQuery('#jquery-ui-slider').slider({
            range: 'min',
            value: 0,
            min: 0,
            max: t_point[t_point.length - 4],
            step: 100,
            slide: function (event, ui) {
                jQuery('#jquery-ui-slider-value').val(ui.value + 'ms');
                slider = ui.value;
            }
        });
        jQuery('#jquery-ui-slider-value').val(jQuery('#jquery-ui-slider').slider('value') + 'ms');
    });

    //ボタン用
    var jg_b = new jsGraphics("myCanvasb");
    //線を引く用
    var jg = new jsGraphics("myCanvas");
    //並び単語の背景用
    var jg2 = new jsGraphics("myCanvas2");
    //並び単語の背景用(レジスタ1)
    var jg2_1 = new jsGraphics("myCanvas2_1");
    //並び単語の背景用(レジスタ2)
    var jg2_2 = new jsGraphics("myCanvas2_2");
    //並び単語の背景用(レジスタ3)
    var jg2_3 = new jsGraphics("myCanvas2_3");
    //並び単語の背景用(最終解答欄)
    var jg2_4 = new jsGraphics("myCanvas2_4");

    //単語表示用(問題提示欄)
    var jg3 = new jsGraphics("myCanvas3");
    //単語表示用(レジスタ1)
    var jg3_1 = new jsGraphics("myCanvas3_1");
    //単語表示用(レジスタ2)
    var jg3_2 = new jsGraphics("myCanvas3_2");
    //単語表示用(レジスタ3)
    var jg3_3 = new jsGraphics("myCanvas3_3");
    //単語表示用(最終解答欄)
    var jg3_4 = new jsGraphics("myCanvas3_4");
    //移動単語背景用
    var jg4 = new jsGraphics("myCanvas4");
    //移動単語表示用
    var jg5 = new jsGraphics("myCanvas5");
    //マウスカーソル表示用
    var jg6 = new jsGraphics("myCanvas6");

</script>   

</body>

</html>