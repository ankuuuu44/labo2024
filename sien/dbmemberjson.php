<?php
if (!isset($_SESSION)) {
    session_start();
}

require "dbc.php"; // データベース接続の処理を書いたファイル

$sql = "SELECT uid, Name FROM member"; // ユーザー情報を取得するSQLクエリ

$result = $conn->query($sql); // クエリを実行

$users = array(); // 学習者の情報を格納する配列

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 各学習者の情報を連想配列に格納
        $user = array(
            "uid" => $row["uid"],
            "name" => $row["Name"]
        );

        // 学習者の情報を配列に追加
        $users[] = $user;
    }
}

// 配列をJSON形式に変換して出力
echo json_encode($users);

?>
