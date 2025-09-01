<!-- index.php - Homepage with introduction, sign up/login, book a meeting -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScheduleMeet - Easy Scheduling</title>
    <style>
        /* Internal CSS - Amazing, real-looking, responsive design */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; }
        header { background: #4a90e2; color: white; padding: 20px; text-align: center; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .welcome { text-align: center; margin-bottom: 40px; }
        .welcome h1 { font-size: 2.5em; color: #4a90e2; }
        .welcome p { font-size: 1.2em; line-height: 1.6; }
        .buttons { display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; }
        button { padding: 12px 24px; background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; transition: background 0.3s, transform 0.2s; }
        button:hover { background: #357abd; transform: translateY(-2px); }
        @media (max-width: 768px) { .container { padding: 10px; } .welcome h1 { font-size: 2em; } }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to ScheduleMeet</h1>
    </header>
    <div class="container">
        <div class="welcome">
            <h1>Effortless Meeting Scheduling</h1>
            <p>ScheduleMeet is your go-to platform for setting up availability, booking appointments, and managing meetings seamlessly. Sign up to create your personalized booking link, set your available times, and let others book meetings with you without the back-and-forth emails.</p>
            <p>How it works: 1. Sign up and log in. 2. Set your availability. 3. Share your booking link. 4. Manage everything from your dashboard.</p>
        </div>
        <div class="buttons">
            <button onclick="location.href='signup.php'">Sign Up</button>
            <button onclick="location.href='login.php'">Log In</button>
            <button onclick="promptBook()">Book a Meeting</button>
        </div>
    </div>
    <script>
        // Internal JS for redirection and prompt
        function promptBook() {
            let username = prompt("Enter the username to book with:");
            if (username) {
                location.href = `book.php?user=${username}`;
            }
        }
    </script>
</body>
</html>
