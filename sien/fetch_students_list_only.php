<?php
    require "dbc.php";

    $sql = "SELECT DISTINCT UID FROM linedata";

    if(isset($_SESSION["MemberID"])){
        $sql .= " WHERE UID = " . $_SESSION["MemberID"];
    }
    $result = $conn->query($sql);

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = ['UID' => $row['UID']];
    }

    header('Content-Type: application/json');
    echo json_encode(['students' => $students]);