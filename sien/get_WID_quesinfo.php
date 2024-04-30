

<?php 
    if(!isset($_SESSION)){
        session_start();
    }

    /*
    このプログラムの要件定義
    1.取得したWIDを元にquestion_infoにアクセスする．
    2.question_infoから必要な物を取ってくる．
    */

    /*
    question_infoテーブル構成
    WID Japanese Sentence Fix level grammar start divide wordnum author
    WID:問題の識別番号
    Japanese:日本語訳
    Sentence:正解文
    Fix:よくわからん
    level:難易度（1:初級，2:中級，3:上級）
    grammar:文法項目（1~21まで，ここはgrammarテーブルとリンクさせるといいと思う．後述）
    start:初期配置
    divide:単語をどう分けるか
    wordnum:単語数
    author:よくわからないけど多分作成者

    grammarテーブルの構成
    GID Item
    1:仮定法，命令法
    2:It,There構文
    3:無生物主語
    4:接続詞
    5:倒置
    6:関係詞
    7:間接話法
    8:前置詞
    9:分詞
    10:動名詞
    11:不定詞
    12:受動態
    13:助動詞
    14:比較
    15:否定
    16:後置修飾
    17:完了形，時制
    18:句動詞
    19:挿入
    20:使役動詞
    21:補語/二重目的語

    */

    //dbc.phpはデータベース接続のプログラム
    require "dbc.php";

    if(isset($_GET['ques_id'])){
        $quesid = $_GET['ques_id'];

        $sqlques = "SELECT * FROM question_info WHERE WID = " . $quesid;
        $result = $conn -> query($sql);
        echo $result["grammar"];


        //$sqlgrammar = "SELECT * FROM grammar WHERE TID" . $

    }else{
        echo "問題IDが指定されていません";
    }





