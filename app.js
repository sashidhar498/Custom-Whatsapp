document.addEventListener('DOMContentLoaded', () => {
    fetchMessages(); // Fetch messages when the DOM is fully loaded
    setInterval(fetchMessages, 1000); // Fetch messages every 1 second

    // Add event listener to the message input field
    const messageInput = document.getElementById('message');
    messageInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent the default action (e.g., a newline)
            sendMessage(); // Call the sendMessage function
        }
    });
});

let lastMessageTimestamp = 0; // Initialize variable to track the last message timestamp

async function sendMessage() {
    const message = document.getElementById('message').value;
    if (message.trim() !== '') {
        try {
            const response = await fetch('chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(message)}`
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            document.getElementById('message').value = ''; // Clear input field
            fetchMessages(); // Fetch messages after sending
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }
}

async function fetchMessages() {
    try {
        const response = await fetch('chat.php?action=getMessages');

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const messages = await response.json();
        const chatWindow = document.getElementById('chat-window');
        const isAtBottom = chatWindow.scrollHeight - chatWindow.clientHeight <= chatWindow.scrollTop + 5;

        chatWindow.innerHTML = ''; // Clear existing messages

        messages.forEach(msg => {
            // Render message
            const messageElement = document.createElement('div');
            messageElement.className = msg.username === currentUser ? 'message-sent' : 'message-received';
            messageElement.innerHTML = `<strong>${msg.username}</strong>: ${msg.message} <small>${new Date(msg.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</small>`;
            chatWindow.appendChild(messageElement);
        });

        // Scroll to the bottom if the user is currently at the bottom
        if (isAtBottom) {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}
