<?php
require "dbc.php";

$uid = $_GET['uid'];
$wid = $_GET['wid'];

$sql = "SELECT EndSentence, Understand, hesitate1, hesitate2 FROM linedata WHERE UID = ? AND WID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $uid, $wid);
$stmt->execute();
$result = $stmt->get_result();

$details = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($details);