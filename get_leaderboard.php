<?php
header('Content-Type: application/json');

// Database connection settings
$servername = "sql107.infinityfree.com";  // Use 'localhost' for local development
$username = "if0_37500831";         // Your database username
$password = "justinMF16";             // Your database password (leave blank if none)
$dbname = "if0_37500831_cat_collecting_game";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Fetch top 10 scores ordered by score descending and timestamp ascending (for tie-breakers)
$sql = "SELECT username, score FROM scores ORDER BY score DESC, id ASC LIMIT 10";
$result = $conn->query($sql);

if ($result) {
    $leaderboard = [];
    while ($row = $result->fetch_assoc()) {
        $leaderboard[] = [
            'username' => htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'),
            'score' => (int)$row['score']
        ];
    }
    echo json_encode(['success' => true, 'leaderboard' => $leaderboard]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error fetching leaderboard.']);
}

$conn->close();
