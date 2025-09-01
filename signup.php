<?php
// signup.php - Sign up form
session_start();
include 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
 
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        echo "<script>alert('Sign up successful!'); location.href='login.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - ScheduleMeet</title>
    <style>
        /* Internal CSS - Professional, responsive */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); }
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #4a90e2; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { width: 100%; padding: 12px; background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #357abd; }
        @media (max-width: 768px) { .form-container { margin: 20px; padding: 15px; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p style="text-align:center;">Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>
