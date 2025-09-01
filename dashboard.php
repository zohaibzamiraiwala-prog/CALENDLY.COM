<?php
// dashboard.php - User dashboard to manage meetings
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
 
// Fetch upcoming and past appointments
$upcoming = $pdo->prepare("SELECT * FROM appointments WHERE host_user_id = ? AND appointment_date >= CURDATE() ORDER BY appointment_date, start_time");
$upcoming->execute([$user_id]);
$upcoming_appts = $upcoming->fetchAll();
 
$past = $pdo->prepare("SELECT * FROM appointments WHERE host_user_id = ? AND appointment_date < CURDATE() ORDER BY appointment_date DESC, start_time");
$past->execute([$user_id]);
$past_appts = $past->fetchAll();
 
// Handle cancel/reschedule
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel'])) {
        $appt_id = $_POST['appt_id'];
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND host_user_id = ?");
        $stmt->execute([$appt_id, $user_id]);
        echo "<script>alert('Appointment cancelled'); location.reload();</script>";
    } elseif (isset($_POST['reschedule'])) {
        // Simple reschedule: update date/time (in real, more logic needed)
        $appt_id = $_POST['appt_id'];
        $new_date = $_POST['new_date'];
        $new_start = $_POST['new_start'];
        $new_end = date('H:i:s', strtotime($new_start) + 1800); // Assume 30 min
        $stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, start_time = ?, end_time = ?, status = 'rescheduled' WHERE id = ? AND host_user_id = ?");
        $stmt->execute([$new_date, $new_start, $new_end, $appt_id, $user_id]);
        echo "<script>alert('Appointment rescheduled'); location.reload();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ScheduleMeet</title>
    <style>
        /* Internal CSS - Dashboard style, responsive tables */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #4a90e2; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #4a90e2; color: white; }
        button { padding: 8px 16px; background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 5px; }
        button:hover { background: #357abd; }
        .buttons { display: flex; justify-content: space-between; margin-bottom: 20px; }
        form { display: inline; }
        @media (max-width: 768px) { table { font-size: 0.9em; } .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>! Your Dashboard</h2>
        <div class="buttons">
            <button onclick="location.href='availability.php'">Set Availability</button>
            <button onclick="location.href='index.php'">Home</button>
            <button onclick="location.href='logout.php'">Log Out</button>
        </div>
        <h3>Upcoming Appointments</h3>
        <table>
            <tr><th>Date</th><th>Time</th><th>Booker</th><th>Email</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($upcoming_appts as $appt): ?>
                <tr>
                    <td><?php echo $appt['appointment_date']; ?></td>
                    <td><?php echo $appt['start_time'] . ' - ' . $appt['end_time']; ?></td>
                    <td><?php echo $appt['booker_name']; ?></td>
                    <td><?php echo $appt['booker_email']; ?></td>
                    <td><?php echo $appt['status']; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="appt_id" value="<?php echo $appt['id']; ?>">
                            <button type="submit" name="cancel">Cancel</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="appt_id" value="<?php echo $appt['id']; ?>">
                            <input type="date" name="new_date" required>
                            <input type="time" name="new_start" required>
                            <button type="submit" name="reschedule">Reschedule</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <h3>Past Appointments</h3>
        <table>
            <tr><th>Date</th><th>Time</th><th>Booker</th><th>Email</th><th>Status</th></tr>
            <?php foreach ($past_appts as $appt): ?>
                <tr>
                    <td><?php echo $appt['appointment_date']; ?></td>
                    <td><?php echo $appt['start_time'] . ' - ' . $appt['end_time']; ?></td>
                    <td><?php echo $appt['booker_name']; ?></td>
                    <td><?php echo $appt['booker_email']; ?></td>
                    <td><?php echo $appt['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script>
        // JS for notifications (simple alert for upcoming)
        alert('Check your upcoming meetings!');
    </script>
</body>
</html>
