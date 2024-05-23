<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="visualstyle.css" type="text/css" />  
</head>
<body>
    ここは結果確認用のページです．<br>
    <?php
        require "dbc.php";
    ?>
    <?php
        if(isset($_POST['featureLabel'])){
            $allresult = array();
            $featurevalue_sql = "SELECT ";

            //作業用
            $column_name = "UID,WID,Understand,";
            $selectcolumn = implode(",", $_POST['featureLabel']);
            $column_name.= $selectcolumn;   //データベースの列名が入っている．
            $featurevalue_sql.= $column_name." FROM featurevalue";
            $featurevalue_res = mysqli_query($conn, $featurevalue_sql);


            //グラフ表示用のSQL
            $graphvisual_sql = $featurevalue_sql." ORDER BY UID ASC limit 10";
            $graphvisual_res = mysqli_query($conn, $graphvisual_sql);
            echo "選択した特徴量は".$selectcolumn."です<br>";
            echo "生成したSQLは".$featurevalue_sql."です<br>";

            
            if($featurevalue_res != false){
                $featurevalue_res_numrows = mysqli_num_rows($featurevalue_res);
                echo "抽出したデータ数は",$featurevalue_res_numrows;
            }
            //カラム名のみ先にcsvに記述
            $fp = fopen('./pydata/test.csv', 'w');
            fputcsv($fp, explode(',', $column_name));

            //動的なテーブル生成
            //すべてのデータ表示
            echo "<div class='container'>";
            echo "<div class = 'content-a'>";
            echo "<table class='cons-table' border='1'>";
            echo "<tr><th>UID</th><th>WID</th><th>Understand</th>";
            foreach($_POST['featureLabel'] as $addcolumnname){
                echo"<th>".$addcolumnname."</th>";
            }
            echo "</tr>";
            //ここまで動的なテーブル生成

            while($featurevalue_rows = $featurevalue_res -> fetch_assoc()){
                $allresult[] = $featurevalue_rows;  //全部のデータをallresultに入れる．後々引数で使う．
                //ここに$POST[featureLabel]に応じてカラム名を動的に変化させる．
                echo "<tr><td>{$featurevalue_rows['UID']}</td>",
                    "<td>{$featurevalue_rows['WID']}</td>",
                    "<td>{$featurevalue_rows['Understand']}</td>";

                foreach($_POST['featureLabel'] as $addcolumnname){
                    echo "<td>{$featurevalue_rows[$addcolumnname]}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";

            //上位10件のみ表示
            /*
            echo "<table class='cons-table' border='1'>";
            echo "<tr><th>UID</th><th>WID</th><th>Understand</th>";
            foreach($_POST['featureLabel'] as $addcolumnname){
                echo"<th>".$addcolumnname."</th>";
            }
            echo "</tr>";
            while($graphvisual_rows = $graphvisual_res -> fetch_assoc()){
                echo "<tr><td>{$graphvisual_rows['UID']}</td>",
                    "<td>{$graphvisual_rows['WID']}</td>",
                    "<td>{$graphvisual_rows['Understand']}</td>";
                foreach($_POST['featureLabel'] as $addcolumnname){
                    echo "<td>{$graphvisual_rows[$addcolumnname]}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            */

            

            //print_r($allresult);
            

        }else{
            echo "特徴量を指定してください．";
        }
        //結果をcsvファイルに記述
        $fp1 = fopen('./pydata/test.csv', 'a');
        foreach ($allresult as $fields) {
            fputcsv($fp1, $fields);
        }
        fclose($fp);
        
    ?>

    <?php
        //Pythonに渡すプログラム
        
        $pyscript = "./a/php_machineLearning.py";
        exec("py ".$pyscript, $output, $status);
        echo "<div class = 'content-b'>";
        echo "Python実行結果<br>";
        for ($i = 0; $i < count($output); $i++) {
            if($i != count($output)-1){
                echo ($i+1)."回目:".round($output[$i],2)."%<br>";
            }else{
                echo "10分割交差検定の結果".round($output[$i],2)."%<br>";
            }
        }
        if($status != 0){
            echo "実行エラー";
        }else{
            echo "正常終了";
        }
        echo "</div>";
        echo "</div>";

        
        
        //exec("py", $output);

    ?>




</body>
</html>