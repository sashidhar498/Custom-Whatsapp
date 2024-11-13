<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

// Database connection
$servername = "sql103.infinityfree.com"; // Your database server name
$username = "if0_37575819"; // Your database username
$password = "mous498498"; // Your database password
$dbname = "if0_37575819_chat_app"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handling the sending of a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $username = $_SESSION['username']; // Use logged-in username
    $stmt = $conn->prepare("INSERT INTO messages (username, message, timestamp) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $username, $message);
    $stmt->execute();
    $stmt->close();
    exit; // Stop further processing after sending a message
}

// Handling the retrieval of messages
if (isset($_GET['action']) && $_GET['action'] === 'getMessages') {
    $result = $conn->query("SELECT username, message, timestamp FROM messages ORDER BY timestamp ASC");
    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'username' => $row['username'],
            'message' => $row['message'],
            'timestamp' => $row['timestamp'],
            'type' => 'message' // Type for rendering
        ];
    }

    echo json_encode($messages);
    exit; // Stop further processing after sending the messages
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
<div class="bubble-background"></div>
    <div class="chat-container">
        <div class="top">
        <h1>What's new - <?php echo htmlspecialchars($_SESSION['username']); ?></h1> <!-- Escape username -->
        <button id="toggle-chat" onclick="toggleChat()">
            <i class="fas fa-eye"></i>
        </button>
        </div>
        <div id="chat-window"></div>
        <div class="message-input">
            <input type="text" id="message" placeholder="Type a message..." required>
            <button onclick="sendMessage()">Send</button>
            <div class="emoji-picker" id="emoji-picker" style="display: none;">
                <!-- Emoji buttons -->
                <?php 
                $emojis = ['😀', '😂', '😍', '😎', '😢', '😡', '👍', '👎', '🎉', '❤️', '🌟', '✨', '🔥', '🐶', '🐱', '🌈', '😁', '🥺', '🤔', '👻', '🤪', '🚀', '🎈', '💻', '🎸', '🧡', '💚', '💙', '💜', '🏖️', '🕶️', '🍀', '⚽', '🎳', '🎮', '🍆'];
                foreach ($emojis as $emoji) {
                    echo "<span class='emoji' onclick=\"insertEmoji('$emoji')\">$emoji</span>";
                }
                ?>
            </div>
            <button id='emoji-btn' onclick="toggleEmojiPicker()">😀</button> <!-- Emoji button -->
        </div>
    </div>

    <script>
        const currentUser = '<?php echo htmlspecialchars($_SESSION['username']); ?>'; // Escape username

        function toggleChat() {
            const chatWindow = document.getElementById('chat-window');
            const toggleButton = document.getElementById('toggle-chat');

            if (chatWindow.style.display === 'none') {
                chatWindow.style.display = 'block';
                toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i>'; // Change to eye-slash icon
            } else {
                chatWindow.style.display = 'none';
                toggleButton.innerHTML = '<i class="fas fa-eye"></i>'; // Change to eye icon
            }
        }

        function toggleEmojiPicker() {
            const emojiPicker = document.getElementById('emoji-picker');
            emojiPicker.style.display = emojiPicker.style.display === 'block' ? 'none' : 'block';
        }

        function insertEmoji(emoji) {
            const messageInput = document.getElementById('message');
            messageInput.value += emoji; // Insert emoji at the end of the input
            toggleEmojiPicker(); // Close the emoji picker after selecting an emoji
            messageInput.focus(); // Keep focus on the input
        }

        function createBubble() {
            const bubble = document.createElement('div');
            bubble.className = 'bubble';
            const size = Math.random() * 50 + 20; // Random size between 20px and 70px
            bubble.style.width = `${size}px`;
            bubble.style.height = `${size}px`;
            bubble.style.left = `${Math.random() * 100}vw`; // Random horizontal position

            // Set the initial position of the bubble below the viewport
            document.querySelector('.bubble-background').appendChild(bubble);

            // Remove the bubble after animation completes to prevent overflow
            bubble.addEventListener('animationend', () => {
                bubble.remove();
            });
        }

        // Example usage: creating a bubble every second
        setInterval(createBubble, 1000);


        // Generate bubbles every 1.5 seconds
        setInterval(createBubble, 500);
        let inactivityTime = 0;
        const timeoutDuration = 300000; // 2 minutes

        // Reset inactivity timer on user activity
        const resetInactivityTimer = () => {
            inactivityTime = 0;
        };

        const checkInactivity = () => {
            inactivityTime += 1000; // Increase by 1 second
            if (inactivityTime >= timeoutDuration) {
                // Redirect to the login page if the user is inactive
                window.location.href = 'login.html'; // Change to your login page
            }
        };

        // Set up event listeners for user interaction
        window.onload = () => {
            // Check for activity
            setInterval(checkInactivity, 1000); // Check every second

            // Listen for various user events to reset the inactivity timer
            document.addEventListener('mousemove', resetInactivityTimer);
            document.addEventListener('keydown', resetInactivityTimer);
            document.addEventListener('click', resetInactivityTimer);
            document.addEventListener('scroll', resetInactivityTimer);
            document.addEventListener('touchstart', resetInactivityTimer);
        };
    </script>

    <script src="app.js"></script>
</body>
</html>
