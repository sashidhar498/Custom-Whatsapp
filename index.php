<?php
session_start();
$host = 'sql103.infinityfree.com';
$db = 'if0_37575819_chat_app';
$user = 'if0_37575819';
$pass = 'mous498498';
$conn = new mysqli($host, $user, $pass, $db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Static password check (123)
    if ($password == '123456') {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // New user, register them
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
        }

        // Login successful
        $_SESSION['username'] = $username;
        header("Location: chat.php");
    } else {
        // Incorrect password, redirect back to login
        header("Location: login.html");
    }
}
?>