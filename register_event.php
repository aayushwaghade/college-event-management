<?php
// ============================================================
// REGISTER STUDENT FOR EVENT
// College Event Management System
// ============================================================
require_once 'header.php';

$error = '';
$success = '';

// Fetch all students for dropdown (SELECT + ORDER BY)
$students_result = mysqli_query($conn, "SELECT student_id, student_name, roll_number FROM students ORDER BY student_name ASC");

// Fetch available events - exclude Cancelled (SELECT + WHERE + ORDER BY)
$events_result = mysqli_query($conn, "SELECT event_id, event_name, event_date, venue, capacity, status FROM events WHERE status != 'Cancelled' ORDER BY event_date ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = intval($_POST['student_id'] ?? 0);
    $event_id = intval($_POST['event_id'] ?? 0);

    if ($student_id <= 0 || $event_id <= 0) {
        $error = 'Please select both a student and an event.';
    } else {
        // Verify student exists
        $check_student = mysqli_prepare($conn, "SELECT student_id FROM students WHERE student_id = ?");
        mysqli_stmt_bind_param($check_student, "i", $student_id);
        mysqli_stmt_execute($check_student);
        $student_exists = mysqli_num_rows(mysqli_stmt_get_result($check_student)) > 0;
        mysqli_stmt_close($check_student);

        // Verify event exists and is not cancelled
        $check_event = mysqli_prepare($conn, "SELECT event_id, capacity, status FROM events WHERE event_id = ?");
        mysqli_stmt_bind_param($check_event, "i", $event_id);
        mysqli_stmt_execute($check_event);
        $event_data = mysqli_fetch_assoc(mysqli_stmt_get_result($check_event));
        mysqli_stmt_close($check_event);

        if (!$student_exists) {
            $error = 'Selected student does not exist.';
        } elseif (!$event_data) {
            $error = 'Selected event does not exist.';
        } elseif ($event_data['status'] === 'Cancelled') {
            $error = 'Cannot register for a cancelled event.';
        } else {
            // Check if event capacity has been reached (COUNT + WHERE)
            $cap_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS reg_count FROM registrations WHERE event_id = ? AND status != 'Cancelled'");
            mysqli_stmt_bind_param($cap_stmt, "i", $event_id);
            mysqli_stmt_execute($cap_stmt);
            $cap_result = mysqli_fetch_assoc(mysqli_stmt_get_result($cap_stmt));
            mysqli_stmt_close($cap_stmt);

            if ($cap_result['reg_count'] >= $event_data['capacity']) {
                $error = 'Event capacity has been reached. No more registrations allowed.';
            } else {
                // Check for duplicate registration (SELECT + WHERE + UNIQUE constraint)
                $dup_stmt = mysqli_prepare($conn, "SELECT registration_id FROM registrations WHERE student_id = ? AND event_id = ?");
                mysqli_stmt_bind_param($dup_stmt, "ii", $student_id, $event_id);
                mysqli_stmt_execute($dup_stmt);
                $dup_result = mysqli_stmt_get_result($dup_stmt);

                if (mysqli_num_rows($dup_result) > 0) {
                    $error = 'Student is already registered for this event.';
                } else {
                    // INSERT registration
                    $today = date('Y-m-d');
                    $reg_stmt = mysqli_prepare($conn, "INSERT INTO registrations (student_id, event_id, registration_date, status) VALUES (?, ?, ?, 'Registered')");
                    mysqli_stmt_bind_param($reg_stmt, "iis", $student_id, $event_id, $today);

                    if (mysqli_stmt_execute($reg_stmt)) {
                        $success = 'Student registered for event successfully.';
                    } else {
                        $error = 'Error registering student. Please try again.';
                    }
                    mysqli_stmt_close($reg_stmt);
                }
                mysqli_stmt_close($dup_stmt);
            }
        }

        // Re-fetch dropdowns after POST
        $students_result = mysqli_query($conn, "SELECT student_id, student_name, roll_number FROM students ORDER BY student_name ASC");
        $events_result = mysqli_query($conn, "SELECT event_id, event_name, event_date, venue, capacity, status FROM events WHERE status != 'Cancelled' ORDER BY event_date ASC");
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>➕ Register Student for Event</h2>
        <p>Register a student to an event</p>
    </div>
    <a href="registrations.php" class="btn btn-secondary">📋 View All Registrations</a>
</div>

<!-- Messages -->
<?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<!-- Registration Form -->
<div class="card">
    <div class="card-header">Event Registration Form</div>
    <form method="POST" action="register_event.php">
        <div class="form-row">
            <div class="form-group">
                <label for="student_id">Select Student *</label>
                <select id="student_id" name="student_id" class="form-control" required>
                    <option value="">-- Select Student --</option>
                    <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                        <option value="<?php echo $student['student_id']; ?>"
                            <?php echo (isset($_POST['student_id']) && $_POST['student_id'] == $student['student_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['student_name'] . ' (' . $student['roll_number'] . ')'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="event_id">Select Event *</label>
                <select id="event_id" name="event_id" class="form-control" required>
                    <option value="">-- Select Event --</option>
                    <?php while ($event = mysqli_fetch_assoc($events_result)): ?>
                        <option value="<?php echo $event['event_id']; ?>"
                            <?php echo (isset($_POST['event_id']) && $_POST['event_id'] == $event['event_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($event['event_name'] . ' — ' . date('d M Y', strtotime($event['event_date'])) . ' — ' . $event['venue']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Registration Date</label>
            <input type="text" class="form-control" value="<?php echo date('d M Y'); ?>" disabled>
            <small class="text-muted">Registration date is automatically set to today.</small>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">📝 Register Student</button>
            <a href="registrations.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Recent Registrations -->
<?php
// Show recent registrations using INNER JOIN + ORDER BY
$recent_query = "SELECT r.registration_id, s.student_name, s.roll_number, e.event_name, e.event_date, e.venue, r.registration_date, r.status
                 FROM registrations r
                 INNER JOIN students s ON r.student_id = s.student_id
                 INNER JOIN events e ON r.event_id = e.event_id
                 ORDER BY r.registration_id DESC
                 LIMIT 5";
$recent_result = mysqli_query($conn, $recent_query);
?>

<div class="card">
    <div class="card-header">📋 Recent Registrations</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Event</th>
                    <th>Event Date</th>
                    <th>Venue</th>
                    <th>Registered On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($recent_result) > 0): ?>
                    <?php while ($reg = mysqli_fetch_assoc($recent_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reg['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($reg['roll_number']); ?></td>
                            <td><strong><?php echo htmlspecialchars($reg['event_name']); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($reg['event_date'])); ?></td>
                            <td><?php echo htmlspecialchars($reg['venue']); ?></td>
                            <td><?php echo date('d M Y', strtotime($reg['registration_date'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($reg['status']); ?>">
                                    <?php echo htmlspecialchars($reg['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>No registrations yet.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
