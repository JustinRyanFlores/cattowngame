<?php
$timeLimit = 30;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get That Kibble!</title>

    <!-- Cache busting by appending timestamp -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Add cache-busting query strings -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>"> <!-- Cache-bust CSS -->

</head>

<body>

    <div class="container text-center mt-4">
        <h1>Get That Kibble!</h1>

        <div class="character-selection-container" id="character-selection-container">
            <h2>Select a Villager:</h2>
            <div class="row justify-content-center">
                <div class="col-4 col-md-2 text-center">
                    <img src="/cattowngame/src/tinytony.png" alt="Cat Model 1" class="cat-option img-fluid" data-model="tinytony">
                    <p>Tiny Tony</p>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <img src="/cattowngame/src/bento.png" alt="Cat Model 2" class="cat-option img-fluid" data-model="bento">
                    <p>Bento</p>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <img src="/cattowngame/src/jimmy.png" alt="Cat Model 3" class="cat-option img-fluid" data-model="jimmy">
                    <p>Jimmy</p>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <img src="/cattowngame/src/isabela.png" alt="Cat Model 4" class="cat-option img-fluid" data-model="isabela">
                    <p>Isabela</p>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <img src="/cattowngame/src/skipper.png" alt="Cat Model 5" class="cat-option img-fluid" data-model="skipper">
                    <p>Skipper</p>
                </div>
                <div class="col-4 col-md-2 text-center">
                    <img src="/cattowngame/src/theodore.png" alt="Cat Model 6" class="cat-option img-fluid" data-model="theodore">
                    <p>Theodore</p>
                </div>
            </div>
            <input type="text" id="username-input" placeholder="Enter your username" class="form-control w-75 mx-auto my-3" required>
            <button id="play-button" class="btn btn-retro btn-primary w-100">Play</button>
            <button id="leaderboard-button" class="btn btn-retro btn-secondary w-100 mt-2">Leaderboard</button>
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
            <button id="retry-button" class="btn btn-retro btn-primary w-100">Retry</button>
            <button id="exit-button" class="btn btn-retro btn-danger w-100" onclick="location.reload();">Exit</button>

        </div>

        <div class="game-container hidden" id="game-container">
            <img src="/cattowngame/src/player.png" alt="Cat" class="cat img-fluid" id="cat">
            <img src="/cattowngame/src/kibble.png" alt="Coin" class="coin img-fluid" id="coin">
        </div>

        <!-- Joystick -->
        <div id="joystick-container" class="joystick-container">
            <div id="joystick" class="joystick"></div>
        </div>

        <div class="score-board hidden" id="score-board">
            Score: <span id="score">0</span> | Time Left: <span id="time-left"><?php echo $timeLimit; ?></span>s
        </div>

        <audio id="coin-sound" src="/cattowngame/src/coin-sound.mp3"></audio>
        <audio id="bg-music" src="/cattowngame/src/background-music.mp3" loop></audio>
        <audio id="select-sound" src="/cattowngame/src/select-sound.mp3"></audio>

    </div>

    <div id="resize-overlay" class="overlay hidden">
        <div class="overlay-content">
            <h2>Resize Detected</h2>
            <p>Please do not resize the window while playing.</p>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

    
            document.addEventListener('contextmenu', function(event) {
                event.preventDefault();
            });

            document.addEventListener('keydown', function(event) {
                // Check if the pressed key is F12
                if (event.key === 'F12') {
                    event.preventDefault(); // Prevent the default action
                }
            });

            const overlay = document.getElementById('resize-overlay');

            // Add this screen size check to disable gameplay and show the overlay
            function checkScreenSize() {
                // Get the width considering zoom
                const width = window.visualViewport ? window.visualViewport.width : window.innerWidth;

                if (width > 1920 || width < 300) { // Adjust for your required minimum width
                    overlay.classList.remove('hidden'); // Show the overlay
                    disableGame(); // Disable the game functionality
                    window.location.reload(); // Reload the page
                }
            }

            // Disable game function to hide the game container and prevent any gameplay
            function disableGame() {
                const gameContainer = document.getElementById('game-container');
                const scoreBoard = document.getElementById('score-board');
                gameContainer.classList.add('hidden');
                scoreBoard.classList.add('hidden');
            }

            // Add event listener for window resizing
            window.addEventListener('resize', checkScreenSize);

            // Call the function on page load to check initial screen size
            checkScreenSize();


            // Joystick control variables
            const joystickContainer = document.getElementById('joystick-container');
            const joystick = document.getElementById('joystick');
            let joystickActive = false;
            let joystickStartX = 0;
            let joystickStartY = 0;
            let joystickCurrentX = 0;
            let joystickCurrentY = 0;

            // Maximum distance joystick can move
            const joystickMaxDistance = 40;

            // Track touch or mouse events for joystick
            joystick.addEventListener('mousedown', startJoystick);
            joystick.addEventListener('touchstart', startJoystick, {
                passive: false
            });

            document.addEventListener('mousemove', moveJoystick);
            document.addEventListener('touchmove', moveJoystick, {
                passive: false
            });

            document.addEventListener('mouseup', endJoystick);
            document.addEventListener('touchend', endJoystick);

            function startJoystick(event) {
                joystickActive = true;
                const touch = event.touches ? event.touches[0] : event;
                joystickStartX = touch.clientX;
                joystickStartY = touch.clientY;
                event.preventDefault();
            }

            function moveJoystick(event) {
                if (!joystickActive) return;

                const touch = event.touches ? event.touches[0] : event;
                joystickCurrentX = touch.clientX - joystickStartX;
                joystickCurrentY = touch.clientY - joystickStartY;

                const distance = Math.sqrt(joystickCurrentX * joystickCurrentX + joystickCurrentY * joystickCurrentY);
                const angle = Math.atan2(joystickCurrentY, joystickCurrentX);

                if (distance > joystickMaxDistance) {
                    joystickCurrentX = joystickMaxDistance * Math.cos(angle);
                    joystickCurrentY = joystickMaxDistance * Math.sin(angle);
                }

                joystick.style.transform = `translate(${joystickCurrentX}px, ${joystickCurrentY}px)`;

                // Update cat movement based on joystick position
                keys.w = joystickCurrentY < -10;
                keys.s = joystickCurrentY > 10;
                keys.a = joystickCurrentX < -10;
                keys.d = joystickCurrentX > 10;
            }

            function endJoystick() {
                joystickActive = false;
                joystickCurrentX = 0;
                joystickCurrentY = 0;
                joystick.style.transform = `translate(0, 0)`;

                // Stop movement when joystick is released
                keys.w = keys.s = keys.a = keys.d = false;
            }

            document.addEventListener('keydown', (event) => {
                const key = event.key.toLowerCase(); // Convert the key to lowercase
                if (key === 'w') keys.w = true;
                if (key === 'a') keys.a = true;
                if (key === 's') keys.s = true;
                if (key === 'd') keys.d = true;
            });

            document.addEventListener('keyup', (event) => {
                const key = event.key.toLowerCase(); // Convert the key to lowercase
                if (key === 'w') keys.w = false;
                if (key === 'a') keys.a = false;
                if (key === 's') keys.s = false;
                if (key === 'd') keys.d = false;
            });

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

            let step;

            // Check if the user is on a mobile device
            const isMobile = /Mobi|Android/i.test(navigator.userAgent);

            // Set step value based on device type
            if (isMobile) {
                step = 5; // Slower movement for mobile
            } else {
                step = 10; // Normal movement for desktop
            }

            let score = 0;
            let timeLeft = <?php echo $timeLimit; ?>;
            let isGameOver = false;
            let selectedCatModel = 'tinytony'; // Default model

            let catX = 0,
                catY = 0; // Initialize these variables to track cat's position

            const keys = {
                w: false,
                a: false,
                s: false,
                d: false
            };

            function centerCat() {
                const catWidth = cat.width;
                const catHeight = cat.height;
                const gameWidth = gameContainer.clientWidth;
                const gameHeight = gameContainer.clientHeight;

                // Calculate the center position
                catX = (gameWidth - catWidth) / 2;
                catY = (gameHeight - catHeight) / 2;

                // Set the initial position of the cat
                cat.style.left = catX + 'px';
                cat.style.top = catY + 'px';
            }

            // Use window.onload to ensure everything is fully loaded, including images
            window.onload = centerCat;

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

            let timerInterval; // Move outside the function

            function startTimer() {
                clearInterval(timerInterval); // Clear any existing interval before starting a new one
                timerInterval = setInterval(() => {
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

                centerCat();
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
                restartGame();
            });

            // Handle character selection
            document.querySelectorAll('.cat-option').forEach(option => {
                option.addEventListener('click', () => {
                    // Play sound effect
                    const selectSound = document.getElementById('select-sound');
                    selectSound.play();

                    // Remove 'selected' class from all options and add to the clicked option
                    document.querySelectorAll('.cat-option').forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');

                    // Set the selected cat model
                    selectedCatModel = option.getAttribute('data-model');
                });
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

            // Attach event listener to leaderboard button
            leaderboardButton.addEventListener('click', fetchLeaderboard);
        });
    </script>

    <script src="game.js?v=<?php echo time(); ?>"></script> <!-- Cache-bust JS -->

</body>

</html>