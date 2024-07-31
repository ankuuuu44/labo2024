<?php
require "dbc.php";

$uid = $_GET['uid'];
$sql = "SELECT DISTINCT WID FROM linedata WHERE UID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = ['WID' => $row['WID']];
}

header('Content-Type: application/json');
echo json_encode(['questions' => $questions]);