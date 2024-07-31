<?php
    require "dbc.php";

    $sql = "SELECT DISTINCT UID FROM linedata";

    if (isset($_SESSION['conditions']) && !empty($_SESSION['conditions'])) {
        $sql .= " WHERE " . join(" AND ", $_SESSION['conditions']);
    } else{
        $sql .= " WHERE 1";
    }
    $result = $conn->query($sql);

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = ['UID' => $row['UID']];
    }

    header('Content-Type: application/json');
    echo json_encode(['students' => $students]);