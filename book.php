<?php
// book.php - Appointment booking system
include 'db.php';
 
if (!isset($_GET['user'])) {
    echo "<script>alert('No user specified'); location.href='index.php';</script>";
    exit;
}
 
$username = $_GET['user'];
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();
if (!$user) {
    echo "<script>alert('User not found'); location.href='index.php';</script>";
    exit;
}
$host_id = $user['id'];
 
// Fetch availability
$avail_stmt = $pdo->prepare("SELECT * FROM availability WHERE user_id = ?");
$avail_stmt->execute([$host_id]);
$availability = $avail_stmt->fetchAll();
 
// For simplicity, generate a calendar in JS, show slots based on availability
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $date = $_POST['date'];
    $start = $_POST['start'];
    $end = date('H:i:s', strtotime($start) + 1800); // 30 min slot
 
    // Check if slot available (simple check, no overlap with existing)
    $check = $pdo->prepare("SELECT * FROM appointments WHERE host_user_id = ? AND appointment_date = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?))");
    $check->execute([$host_id, $date, $start, $start, $end, $end]);
    if ($check->rowCount() > 0) {
        echo "<script>alert('Slot taken');</script>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO appointments (host_user_id, booker_name, booker_email, appointment_date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
        $stmt->execute([$host_id, $name, $email, $date, $start, $end]);
 
        // Send email
        $to = $email;
        $subject = "Appointment Confirmed";
        $message = "Your appointment with $username on $date at $start is confirmed.";
        $headers = "From: no-reply@schedulemeet.com";
        mail($to, $subject, $message, $headers);
 
        echo "<script>alert('Booked! Confirmation email sent.'); location.href='index.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book with <?php echo $username; ?> - ScheduleMeet</title>
    <style>
        /* Internal CSS - Calendar style, responsive */
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2 { color: #4a90e2; }
        #calendar { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin-bottom: 20px; }
        .day { padding: 10px; background: #f0f0f0; text-align: center; border-radius: 5px; cursor: pointer; }
        .day:hover { background: #ddd; }
        .selected { background: #4a90e2; color: white; }
        #slots { display: flex; flex-wrap: wrap; gap: 10px; }
        .slot { padding: 10px; background: #4a90e2; color: white; border-radius: 5px; cursor: pointer; }
        .slot:hover { background: #357abd; }
        form { display: grid; gap: 10px; }
        input { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 12px; background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #357abd; }
        @media (max-width: 768px) { #calendar { grid-template-columns: repeat(3, 1fr); } .container { padding: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book a Meeting with <?php echo $username; ?></h2>
        <div id="calendar"></div>
        <h3>Available Slots for <span id="selected-date"></span></h3>
        <div id="slots"></div>
        <form method="POST" id="book-form" style="display:none;">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <input type="hidden" name="date" id="book-date">
            <input type="hidden" name="start" id="book-start">
            <button type="submit">Book Now</button>
        </form>
    </div>
    <script>
        // Internal JS for calendar and slots
        const availability = <?php echo json_encode($availability); ?>;
        const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
 
        function generateCalendar() {
            const calendar = document.getElementById('calendar');
            calendar.innerHTML = '';
            const today = new Date();
            for (let i = 0; i < 30; i++) { // Next 30 days
                const date = new Date(today);
                date.setDate(today.getDate() + i);
                const dayName = daysOfWeek[date.getDay()];
                const dayDiv = document.createElement('div');
                dayDiv.classList.add('day');
                dayDiv.textContent = date.toDateString();
                dayDiv.onclick = () => selectDate(date, dayName);
                calendar.appendChild(dayDiv);
            }
        }
 
        function selectDate(date, dayName) {
            document.querySelectorAll('.day').forEach(d => d.classList.remove('selected'));
            event.target.classList.add('selected');
            document.getElementById('selected-date').textContent = date.toDateString();
            showSlots(date, dayName);
        }
 
        function showSlots(date, dayName) {
            const slotsDiv = document.getElementById('slots');
            slotsDiv.innerHTML = '';
            const availForDay = availability.filter(a => a.day_of_week === dayName);
            availForDay.forEach(avail => {
                let start = new Date(`2000-01-01T${avail.start_time}`);
                const end = new Date(`2000-01-01T${avail.end_time}`);
                while (start < end) {
                    const slotTime = start.toTimeString().slice(0,5);
                    const slotDiv = document.createElement('div');
                    slotDiv.classList.add('slot');
                    slotDiv.textContent = slotTime;
                    slotDiv.onclick = () => selectSlot(date.toISOString().slice(0,10), slotTime);
                    slotsDiv.appendChild(slotDiv);
                    start.setMinutes(start.getMinutes() + 30); // 30 min slots
                }
            });
        }
 
        function selectSlot(date, start) {
            document.getElementById('book-date').value = date;
            document.getElementById('book-start').value = start + ':00';
            document.getElementById('book-form').style.display = 'block';
        }
 
        generateCalendar();
    </script>
</body>
</html>
