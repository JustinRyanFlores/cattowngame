<?php
$initialX = 300;
$initialY = 300;
$timeLimit = 30;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get That Kibble!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <h1>Get That Kibble!</h1>

    <div class="character-selection-container" id="character-selection-container">
        <h2>Select a Villager:</h2>
        <div class="character-options">
            <div class="character">
                <img src="/cattowngame/src/tinytony.png" alt="Cat Model 1" class="cat-option" data-model="tinytony">
                <p>Tiny Tony</p>
            </div>
            <div class="character">
                <img src="/cattowngame/src/bento.png" alt="Cat Model 2" class="cat-option" data-model="bento">
                <p>Bento</p>
            </div>
            <div class="character">
                <img src="/cattowngame/src/jimmy.png" alt="Cat Model 3" class="cat-option" data-model="jimmy">
                <p>Jimmy</p>
            </div>
            <div class="character">
                <img src="/cattowngame/src/isabela.png" alt="Cat Model 4" class="cat-option" data-model="isabela">
                <p>Isabela</p>
            </div>
            <div class="character">
                <img src="/cattowngame/src/skipper.png" alt="Cat Model 5" class="cat-option" data-model="skipper">
                <p>Skipper</p>
            </div>
            <div class="character">
                <img src="/cattowngame/src/theodore.png" alt="Cat Model 6" class="cat-option" data-model="theodore">
                <p>Theodore</p>
            </div>
        </div>
        <input type="text" id="username-input" placeholder="Enter your username" class="form-control w-50 mx-auto my-3" required>
        <button id="play-button" class="btn btn-retro">Play</button>
        <button id="leaderboard-button" class="btn btn-retro">Leaderboard</button>
    </div>

    <!-- Leaderboard Modal -->
    <div class="modal fade" id="leaderboardModal" tabindex="-1" role="dialog" aria-labelledby="leaderboardModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Leaderboard</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Username</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-body">
                            <!-- Leaderboard entries will be inserted here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="game-over" class="hidden">
        <h2>Game Over</h2>
        <p>Your Score: <span id="final-score">0</span></p>
        <button id="retry-button" class="btn btn-retro">Retry</button>
    </div>

    <div class="game-container hidden" id="game-container">
        <img src="/cattowngame/src/player.png" alt="Cat" class="cat" id="cat">
        <img src="/cattowngame/src/kibble.png" alt="Coin" class="coin" id="coin">

    </div>
    <div class="score-board hidden" id="score-board">
        Score: <span id="score">0</span> | Time Left: <span id="time-left"><?php echo $timeLimit; ?></span>s
    </div>

    <audio id="coin-sound" src="/cattowngame/src/coin-sound.mp3"></audio>
    <audio id="bg-music" src="/cattowngame/src/background-music.mp3" loop></audio>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cat = document.getElementById('cat');
            const coin = document.getElementById('coin');
            const scoreBoard = document.getElementById('score');
            const timeLeftBoard = document.getElementById('time-left');
            const gameContainer = document.getElementById('game-container');
            const playButton = document.getElementById('play-button');
            const retryButton = document.getElementById('retry-button');
            const gameOverScreen = document.getElementById('game-over');
            const finalScore = document.getElementById('final-score');
            const scoreBoardContainer = document.getElementById('score-board');
            const usernameInput = document.getElementById('username-input');
            const coinSound = document.getElementById('coin-sound');
            const bgMusic = document.getElementById('bg-music');

            let catX = <?php echo $initialX; ?>;
            let catY = <?php echo $initialY; ?>;
            let step = 20;
            let score = 0;
            let timeLeft = <?php echo $timeLimit; ?>;
            let isGameOver = false;
            let selectedCatModel = 'tinytony'; // Default model

            const keys = {
                w: false,
                a: false,
                s: false,
                d: false
            };

            // Randomly place the coin within bounds
            function placeCoin() {
                const coinX = Math.floor(Math.random() * (gameContainer.clientWidth - coin.width));
                const coinY = Math.floor(Math.random() * (gameContainer.clientHeight - coin.height));
                coin.style.left = coinX + 'px';
                coin.style.top = coinY + 'px';
            }

            // Update cat position based on key state
            function updatePosition() {
                if (isGameOver) return;

                if (keys.w && catY > 0) catY -= step;
                if (keys.s && catY < gameContainer.clientHeight - cat.height) catY += step;
                if (keys.a && catX > 0) catX -= step;
                if (keys.d && catX < gameContainer.clientWidth - cat.width) catX += step;

                cat.style.top = catY + 'px';
                cat.style.left = catX + 'px';

                checkCollision(); // Check if the cat collides with the coin
            }

            // Check if the cat has collected the coin
            function checkCollision() {
                const catRect = cat.getBoundingClientRect();
                const coinRect = coin.getBoundingClientRect();

                if (
                    catRect.left < coinRect.left + coinRect.width &&
                    catRect.left + catRect.width > coinRect.left &&
                    catRect.top < coinRect.top + coinRect.height &&
                    catRect.height + catRect.top > coinRect.top
                ) {
                    score++;
                    scoreBoard.textContent = score;
                    coinSound.play();
                    placeCoin();
                }
            }

            // Timer function
            function startTimer() {
                const timerInterval = setInterval(() => {
                    if (timeLeft > 0) {
                        timeLeft--;
                        timeLeftBoard.textContent = timeLeft;
                    } else {
                        clearInterval(timerInterval);
                        endGame();
                    }
                }, 1000);
            }

            // End the game
            function endGame() {
                isGameOver = true;
                finalScore.textContent = score;
                gameOverScreen.classList.remove('hidden');
                gameContainer.classList.add('hidden');
                scoreBoardContainer.classList.add('hidden');
                bgMusic.pause();

                const username = usernameInput.value.trim();

                if (username) {
                    fetch('submit_score.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `score=${score}&username=${encodeURIComponent(username)}`,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Score recorded successfully.');
                            } else {
                                alert('Failed to record score. Please try again.');
                                console.error('Failed to record score:', data.message);
                            }
                        })
                        .catch(error => {
                            alert('Error submitting score. Please check your connection.');
                            console.error('Error submitting score:', error);
                        });
                }
            }

            // Restart the game
            function restartGame() {
                score = 0;
                timeLeft = <?php echo $timeLimit; ?>;
                isGameOver = false;

                gameOverScreen.classList.add('hidden');
                gameContainer.classList.remove('hidden');
                scoreBoardContainer.classList.remove('hidden');
                scoreBoard.textContent = score;
                timeLeftBoard.textContent = timeLeft;

                catX = <?php echo $initialX; ?>;
                catY = <?php echo $initialY; ?>;
                cat.style.top = catY + 'px';
                cat.style.left = catX + 'px';

                placeCoin();
                startTimer();
                bgMusic.play();
            }

            // Handle retry button click
            retryButton.addEventListener('click', restartGame);

            // Handle play button click
            playButton.addEventListener('click', () => {
                const username = usernameInput.value.trim();

                if (!username) {
                    alert('Please enter a username to start the game!');
                    return;
                }

                // Set the cat image to the selected model
                cat.src = `/cattowngame/src/${selectedCatModel}.png`;

                gameContainer.classList.remove('hidden');
                scoreBoardContainer.classList.remove('hidden');
                playButton.classList.add('hidden');
                document.getElementById('character-selection-container').classList.add('hidden');

                bgMusic.play(); // Start playing background music
                placeCoin();
                startTimer();
            });

            // Handle character selection
            document.querySelectorAll('.cat-option').forEach(option => {
                option.addEventListener('click', () => {
                    document.querySelectorAll('.cat-option').forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    selectedCatModel = option.getAttribute('data-model');
                });
            });



            // Movement event listeners
            window.addEventListener('keydown', (e) => {
                keys[e.key] = true;
            });

            window.addEventListener('keyup', (e) => {
                keys[e.key] = false;
            });

            // Update position based on key inputs at regular intervals
            setInterval(updatePosition, 1000 / 60);


            const leaderboardButton = document.getElementById('leaderboard-button');
            const leaderboardModal = $('#leaderboardModal');
            const leaderboardBody = document.getElementById('leaderboard-body');

            // Function to fetch and display the leaderboard
            function fetchLeaderboard() {
                fetch('get_leaderboard.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear existing leaderboard entries
                            leaderboardBody.innerHTML = '';

                            // Populate leaderboard
                            data.leaderboard.forEach((entry, index) => {
                                const row = document.createElement('tr');

                                const rankCell = document.createElement('td');
                                rankCell.textContent = index + 1;
                                row.appendChild(rankCell);

                                const usernameCell = document.createElement('td');
                                usernameCell.textContent = entry.username;
                                row.appendChild(usernameCell);

                                const scoreCell = document.createElement('td');
                                scoreCell.textContent = entry.score;
                                row.appendChild(scoreCell);

                                leaderboardBody.appendChild(row);
                            });

                            // Show the modal
                            leaderboardModal.modal('show');
                        } else {
                            alert('Failed to fetch leaderboard: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error fetching leaderboard. Please try again later.');
                        console.error('Leaderboard fetch error:', error);
                    });
            }

            // **Add this line to attach the event listener**
            leaderboardButton.addEventListener('click', fetchLeaderboard);
        });
    </script>

</body>

</html>