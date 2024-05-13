<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
    session_start();
    if(isset($_SESSION["mark"]) && isset($_POST["mark"])){
        if($_POST["mark"] != $_SESSION["mark"]){//正誤表示、部分点表示の場合分け処理
            if(isset($_POST["mark"])){
                $_SESSION["mark"] = $_POST["mark"];
            }
        }
    }
    
    error_reporting(0);
?>

<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>


    <?php
    $_SESSION["student"]="";
    $_SESSION["question"]="";
    ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>履歴分析（仮）</title>


    <style type="text/css">
        /*modal-bgとmoda-visualは同じものなのでもう少し効率的に記述できるはず */
        #modal-bg {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        }
        #modal-visual {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        }
        #modal-sum {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        }


        #modal-main {
        position: relative;
        text-align: center;
        background: #fff;
        width: 80%;
        height: 80%;
        max-height: 800px;
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
        }

        #visual-main {
        position: relative;
        background: #fff;
        width: 80%;
        height: 80%;
        max-height: 800px;
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
        }
        #sum-main {
        position: relative;
        background: #fff;
        width: 80%;
        height: 80%;
        max-height: 800px;
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
        }
        /*
        .searchcontent {
            position: absolute;
            right: 0;
            bottom: 0;
        }
        */
        
        .hide{
            display: none;
        }
        
        #searchButton {
            position: absolute;
            right: 10;
            bottom: 10;
        }
        .closeButton {
            position: absolute;
            left:10;
            bottom: 10;
        }
        .checklabel{
            display: inline-block;
            text-align: left;
        }


    </style>

</head>
<body>

<div class="count">
    <font size="5">
    ■学習者：<?php echo $student_count;?>人　
    ■問題：<?php echo $question_count;?>問 
    ■履歴データ数：<?php echo $data_count;?>問
    </font>
    <button type = "button" id = "filterbutton">フィルタ</button>
    <button type = "button" id = "sumbutton">集計</button>
    <button type = "button" id = "visualbutton">ビジュアライゼーション</button>
</div>

<!-- モーダルウィンドウの背景 -->
<div id="modal-bg">
        <!-- モーダルウィンドウの本体 -->
        <div id="modal-main">
            <h2>検索ウィンドウ</h2>
            <form action="stusample.php" method="post">
                <b>UID</b> 
                    <select name="UIDrange">
                        <option value = "include">含む</option>
                        <option value = "not">以外</option>
                    </select>
                    <input type="text" name="UIDsearch" ><br>

                <b>WID</b> 
                    <select name="WIDrange">
                        <option value = "include">含む</option>
                        <option value = "not">以外</option>
                    </select>
                    <input type="text" name="WIDsearch"><br>

                <b>正誤</b> <input type="radio" name = "TFsearch" value="1">正解　<input type="radio" name="TFsearch" value="0">不正解<br>
                <b>解答時間</b>
                    <select name="TimeRange" id = "TimeRangeid">
                        <option value = "above">以上</option>
                        <option value = "below">以下</option>
                        <option value = "range">範囲</option>
                    </select>
                    <input type="text" name = "Timesearch" id = "Timesearchid"><br>
                    <div id ="Timesearch_minmax" class = "hide">
                        <input type="text" name = "Timesearch-min">～<input type="text" name="Timesearch-max">
                    </div>
                <b>迷い度</b><br>
                <label class ="checklabel">
                    <input type = "checkbox" name = "Understandlabel[]" value = "4">ほとんど迷わなかった<br>
                    <input type = "checkbox" name = "Understandlabel[]" value = "3">少し迷った<br>
                    <input type = "checkbox" name = "Understandlabel[]" value = "2">かなり迷った<br>
                    <input type = "checkbox" name = "Understandlabel[]" value = "1">誤って決定ボタンを押した<br>
                </label>
                <br>
                <b>単語の迷い</b>
                <input type="radio" name = "hesitateword" value = "1">あり <input type="radio" name = "hesitateword" value = "0">なし<br>
                <b>全体的な迷い</b>
                <input type = "radio" name ="Check-all" value = "1">あり <input type="radio" name = "Check-all" value = "0">なし<br>


                <input type="submit" id = "searchButton" value="検索">
                <button class="closeButton">閉じる</button>
            </form>
        </div>
    </div>
    
    <!-- モーダルウィンドウの背景 -->
    <div id="modal-visual">
    <!-- モーダルウィンドウの本体 -->
        <div id="visual-main">
            <h2>ビジュアライゼーション</h2>
            <button id="bargraph">棒グラフ</button>
            <button class="closeButton">閉じる</button>
        </div>
    </div>

    <!-- モーダルウィンドウの背景 -->
    <div id="modal-sum">
    <!-- モーダルウィンドウの本体 -->
        <div id="sum-main">
            <h2>集計</h2>
            <!--ほんまはここで合計or平均がクリックされたら以下のモノを表示したい．-->
            <!--
            <div class = "hide"　style = "display:none">
            -->
                集約キー
                <select name="keycolumn">
                    <option value="UID">UID</option>
                    <option value="WID">WID</option>
                </select>
                集約データ
                <select name="datacolumn">
                    <option value="Time">解答時間</option>
                    <option value="Understand">迷い度</option>
                    <option value="hesitate">単語単位の迷い</option>
                    <option value="check">全体的に迷った</option>
                </select>
                <br>
                <input type="button" id="sumbuttoncons" value="合計">
                <button id="avgbuttoncons">平均</button>

            <!--
            </div>

            -->
            <button class="closeButton">閉じる</button>
        </div>
    </div>    


    <div id = "consequence">
        <table border="1">
            <tr>
                <th>UID</th>
                <th>WID</th>
                <th>解答日時</th>
                <th>正誤</th>
                <th>解答時間</th>
                <th>迷い度</th>
                <th>単語単位の迷い</th>
                <th>全体的に迷った</th>
            </tr>
            
        
        <?php 
        //もしPOST通信が行われたら検索結果表示．なければデータベース一覧が表示
        $sqlsearch = 'SELECT * FROM linedata WHERE 1';
        $sql_rownum = "SELECT count(*) FROM linedata WHERE 1";
        //UID条件
        if(!empty($_POST["UIDsearch"])){
            if(isset($_POST["UIDrange"])){
                if($_POST["UIDrange"] == "not"){
                    $tmpsearrch = " AND UID NOT in(";
                }else if($_POST["UIDrange"] == "include"){
                    $tmpsearrch = " AND UID in(";
                }
            }
            //SQLにuidを追加したものを生成
            $searchuid = $_POST["UIDsearch"];
            $tmpsearrch.= $searchuid;   
            $tmpsearrch.= ")";
            $sqlsearch.= $tmpsearrch;
            //SQL実行
        }else{
            //SQLに何も追加しない
        }
        //WID条件
        if(!empty($_POST["WIDsearch"])){
            if(isset($_POST["WIDrange"])){
                if($_POST["WIDrange"] == "not"){
                    $tmpsearchWID = " AND WID NOT in(";
                }else if($_POST["WIDrange"] == "include"){
                    $tmpsearchWID = " AND WID in(";
                }
            }
            $searchwid = $_POST["WIDsearch"];
            $tmpsearchWID.= $searchwid;
            $tmpsearchWID.= ")";
            $sqlsearch.= $tmpsearchWID;
        }
        //正誤条件
        if(isset($_POST["TFsearch"])){
            $searchTF = $_POST["TFsearch"];
            $tmpsearchTF = " AND TF =";
            $tmpsearchTF.= $searchTF;
            $sqlsearch.= $tmpsearchTF;
        }else{
            //SQLに何も追加しない
        }
        //解答時間条件
        //もし，Timesearchが設定されているなら，以上か以下かで条件分岐して検索を行う．
        //もし,Timesearch_minとTimesearch_maxが設定されているなら，範囲検索を行う
        if(!empty($_POST["Timesearch"])){
            $searchTime = $_POST["Timesearch"];
            $tmpsearchTime = " AND Time ";
            if(isset($_POST["TimeRange"])){
                if($_POST["TimeRange"] == "above"){
                    $tmpsearchTime.= ">=";
                }else if($_POST["TimeRange"] == "below"){
                    $tmpsearchTime.= "<=";
                }
            }
            $tmpsearchTime.= $searchTime;
            $sqlsearch.= $tmpsearchTime;
        }else if(!empty($_POST["Timesearch-min"]) && !empty($_POST["Timesearch-max"])){
            $searchTime_min = $_POST["Timesearch-min"];
            $searchTime_max = $_POST["Timesearch-max"];
            $tmpsearchTime = " AND Time >=";
            $tmpsearchTime.= $searchTime_min;
            $tmpsearchTime.= " AND Time <=";
            $tmpsearchTime.= $searchTime_max;
            $sqlsearch.= $tmpsearchTime;
        }else if(!empty($_POST["Timesearch-min"]) && empty($_POST["Timesearch-max"])){
            $alert = "<script>alert('時間の上限を設定してください')</script>";
            echo $alert;
        }else if(empty($_POST["Timesearch-min"]) && !empty($_POST["Timesearch-max"])){
            $alert = "<script>alert('時間の下限を設定してください')</script>";
            echo $alert;
        }else{
            //SQLに何も追加しない
            //この条件に入るときはTimesearchが空でかつTimesearch-minとTimesearch-maxも空であるときのみ
            //つまり時間検索が行われていない時なので，なにもしなくていい
        }
        //迷い度検索
        if(isset($_POST["Understandlabel"])){
            $searchUnderstandarray = $_POST["Understandlabel"];
            $searchUnderstand = implode(",",$searchUnderstandarray);
            $tmpsearchUnderstand = " AND Understand in(";
            $tmpsearchUnderstand.= $searchUnderstand;
            $tmpsearchUnderstand.= ")";
            $sqlsearch.= $tmpsearchUnderstand;
        }else{
            //SQLに何も追加しない
        }
        //単語単位の迷い検索
        if(isset($_POST["hesitateword"])){
            if($_POST["hesitateword"] == "1"){
                $tmpsearchhesitate = " AND linedata.hesitate !=''";
            }else{
                $tmpsearchhesitate = " AND linedata.hesitate =''";
            }
            $sqlsearch.= $tmpsearchhesitate;
        }else{
            //SQLに何も追加しない
        }
        //全体的に迷ったの検索
        if(isset($_POST["Check-all"])){
            $searchCheckall = $_POST["Check-all"];
            $tmpsearchCheckall = " AND linedata.check =";
            $tmpsearchCheckall.= $searchCheckall;
            $sqlsearch.= $tmpsearchCheckall;
        }else{
            //SQLに何も追加しない
        }

        echo "<br>";
        echo $sqlsearch;
    ?>
    <?php
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //検索条件のSQL取得
        //SQL実行
        $res_search = mysqli_query($conn,$sqlsearch);
    }else{
        $post_not_sql = "SELECT * FROM linedata";
        $res_search = mysqli_query($conn,$post_not_sql);
    }
    if($res_search != false){
        $databasearray1 = [];   //グラフ化のための連装配列
        $search_numrows = mysqli_num_rows($res_search);
        echo "<br>";
        echo "データベースの行数:",$search_numrows;

        while($row_search = $res_search -> fetch_assoc()){
            if($row_search['TF'] == '1'){
                $echoTF = '〇';
            }else{
                $echoTF = '×';
            }
            echo "<tr><td>{$row_search['UID']}</td>",
                "<td>{$row_search['WID']}</td>",
                "<td>{$row_search['Date']}</td>",
                "<td>{$echoTF}</td>",
                "<td>{$row_search['Time']}</td>",
                "<td>{$row_search['Understand']}</td>",
                "<td>{$row_search['hesitate']}</td>",
                "<td>{$row_search['check']}</td>","</tr>";

            $databasearray1[]= [
                'UID' => $row_search['UID'],
                'WID' => $row_search['WID'],
                'Date' => $row_search['Date'],
                'TF' => $row_search['TF'],
                'Time' => $row_search['Time'],
                'Understand' => $row_search['Understand'],
                'hesitate' => $row_search['hesitate'],
                'check' => $row_search['check']
            ];

            
        }

        $_SESSION["databasearray1"] = $databasearray1;
        //print_r($databasearray1);
    }
    
    ?>

    
    </table>
    </div>


<canvas id="lineChart"></canvas>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>

<script type="text/javascript" src="./prototype.js"></script>
<script type="text/javascript" src="./d3.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>-->

<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>

<script type="text/javascript" src="./filter.js"></script>
</body>
</html>