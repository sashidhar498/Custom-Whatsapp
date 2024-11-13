<?php
session_start();
$host = 'sql103.infinityfree.com';
$db = 'if0_37575819_chat_app';
$user = 'if0_37575819';
$pass = 'mous498498';

$conn = new mysqli($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = $_POST['message'];
    $username = $_SESSION['username'];

    $stmt = $conn->prepare("INSERT INTO messages (username, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $message);
    $stmt->execute();
    $stmt->close();
} elseif ($_GET['action'] == 'getMessages') {
    $result = $conn->query("SELECT * FROM messages ORDER BY timestamp ASC");

    $messages = [];
    $last_date = '';
    while ($row = $result->fetch_assoc()) {
        $current_date = date('Y-m-d', strtotime($row['timestamp']));
        if ($current_date != $last_date) {
            // Add a date entry
            $messages[] = ['type' => 'date', 'date' => date('F j, Y', strtotime($row['timestamp']))];
            $last_date = $current_date;
        }
        // Add the message entry
        $messages[] = ['type' => 'message', 'username' => $row['username'], 'message' => $row['message'], 'timestamp' => $row['timestamp']];
    }
    echo json_encode($messages);
}

$conn->close();
?>
