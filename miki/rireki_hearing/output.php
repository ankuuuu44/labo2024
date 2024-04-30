<?php

//DB書き込み
/**
 * Error reporting level
 */
//error_reporting(E_ALL);   // デバッグ時
//error_reporting(0);   // 運用時

session_start();
require"dbc.php";
extract($_POST);
$group_ID =$_SESSION["group_ID"];
$value_x =$_SESSION["value_x"];
$value_y =$_SESSION["value_y"];
$ave =$_SESSION["ave"];


$id = $id-1;

//echo $id;
//echo "成功だっちゃ";
?>
<font size="1">
<table border="1">
<?php
    echo "<tr>";
    echo "<td>要素名</td>";
    $ff = 0;
    foreach($group_ID[$id] as $tes){
        echo "<td>".$tes."</td>";
        $ff++;
    }
    echo "</tr>";
    echo "<tr>";
    echo "<td>パラメータ1</td>";
    foreach($value_x[$id] as $tes){
        echo "<td>".$tes."</td>";
    }
    echo "</tr>";
    echo "<tr>";
    echo "<td>パラメータ2</td>";
    foreach($value_y[$id] as $tes){
        echo "<td>".$tes."</td>";
    }
    echo "</tr>";

    ?>
</table>
</font>
<?php
    echo "<br>";
    echo "平均".$ave[$id];
    echo "<br>";
    echo "<br>";
?>

