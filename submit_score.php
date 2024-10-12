<?php

// Database connection settings
$servername = "sql107.infinityfree.com";  // Use 'localhost' for local development
$username = "if0_37500831";         // Your database username
$password = "justinMF16";             // Your database password (leave blank if none)
$dbname = "if0_37500831_cat_collecting_game";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if score and username were passed in the request
if (isset($_POST['score']) && isset($_POST['username'])) {
    $score = $_POST['score'];
    $username = $_POST['username'];

    // Insert the score into the database
    $stmt = $conn->prepare("INSERT INTO scores (username, score) VALUES (?, ?)");
    $stmt->bind_param("si", $username, $score);  // 'si' stands for string and integer
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Score saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error saving score.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Missing score or username.']);
}

$conn->close();
