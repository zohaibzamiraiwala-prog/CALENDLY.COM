<?php
// availability.php - Set availability
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['day'];
    $start = $_POST['start'];
    $end = $_POST['end'];
 
    try {
        $stmt = $pdo->prepare("INSERT INTO availability (user_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $day, $start, $end]);
        echo "<script>alert('Availability set!'); location.href='dashboard.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
 
// Fetch current availability
$stmt = $pdo->prepare("SELECT * FROM availability WHERE user_id = ?");
$stmt->execute([$user_id]);
$avail = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Availability - ScheduleMeet</title>
    <style>
        /* Internal CSS - Form style, responsive */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #4a90e2; }
        form { display: grid; gap: 10px; }
        select, input { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 12px; background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #357abd; }
        ul { list-style: none; padding: 0; }
        li { background: #f0f0f0; padding: 10px; margin: 5px 0; border-radius: 5px; }
        @media (max-width: 768px) { .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Set Your Availability</h2>
        <form method="POST">
            <select name="day" required>
                <option value="">Select Day</option>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="time" name="start" required>
            <input type="time" name="end" required>
            <button type="submit">Add Availability</button>
        </form>
        <h3>Current Availability</h3>
        <ul>
            <?php foreach ($avail as $a): ?>
                <li><?php echo $a['day_of_week'] . ': ' . $a['start_time'] . ' - ' . $a['end_time']; ?></li>
            <?php endforeach; ?>
        </ul>
        <button onclick="location.href='dashboard.php'">Back to Dashboard</button>
    </div>
</body>
</html>
