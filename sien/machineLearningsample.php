<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style type="text/css">
        .cons-table{
            width: 50%;
            text-align: center;
            border-collapse: collapse;
            border-spacing: 0;
        }
        .cons-table th{
            padding: 10px;
            background: #e9e9e9;
            border: solid 1px #778ca3;
        }
        .cons-table td{
            padding: 10px;
            border: solid 1px #778ca3;
        }
    </style>
</head>
<body>
    ここは結果確認用のページです．<br>
    <?php
        require "dbc.php";
    ?>
    <?php
        if(isset($_POST['featureLabel'])){
            $featurevalue_sql = "SELECT UID,WID,Understand,";
            $selectcolumn = implode(",", $_POST['featureLabel']);
            $featurevalue_sql.= $selectcolumn." FROM featurevalue";
            $featurevalue_res = mysqli_query($conn, $featurevalue_sql);
            echo "選択した特徴量は".$selectcolumn."です<br>";
            echo "生成したSQLは".$featurevalue_sql."です<br>";
            if($featurevalue_res != false){
                $featurevalue_res_numrows = mysqli_num_rows($featurevalue_res);
                echo "抽出したデータ数は",$featurevalue_res_numrows;
            }

            //動的なテーブル生成
            echo "<table class='tableclassname' border='1'>";
            echo "<tr><th>UID</th><th>WID</th><th>Understand</th>";
            foreach($_POST['featureLabel'] as $addcolumnname){
                echo"<th>".$addcolumnname."</th>";
            }
            echo "</tr>";
            //ここまで動的なテーブル生成

            while($featurevalue_rows = $featurevalue_res -> fetch_assoc()){
                //ここに$POST[featureLabel]に応じてカラム名を動的に変化させる．
                echo "<tr><td>{$featurevalue_rows['UID']}</td>",
                    "<td>{$featurevalue_rows['WID']}</td>",
                    "<td>{$featurevalue_rows['Understand']}</td>";
                foreach($_POST['featureLabel'] as $addcolumnname){
                    echo "<td>{$featurevalue_rows[$addcolumnname]}</td>";
                }
                echo "</tr>";
            } 
            

        }else{
            echo "特徴量を指定してください．";
        }
        
    ?>




</body>
</html>