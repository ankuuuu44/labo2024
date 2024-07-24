<?php

    session_start();
    require "dbc.php";
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['studentId']) && !empty($_POST['studentId'])) {
            $id = $_POST['studentId'];

            // データベースクエリの準備と実行
            $Question = "SELECT count(*) as cnt FROM linedata WHERE UID = '".$id."'"; // DBからデータを得る
            $res = mysqli_query($conn, $Question);

            if (!$res) {
                die(json_encode(["error" => "データ抽出エラー: " . mysqli_error($conn)]));
            }

            $row = $res->fetch_assoc();

            // 必要なデータを返す
            echo json_encode([
                'count' => $row['cnt']
            ]);
        } else {
            echo json_encode(["error" => "studentIdが設定されていないか空です"]);
        }
    } else {
        echo json_encode(["error" => "無効なリクエストメソッド"]);
    }

?>
