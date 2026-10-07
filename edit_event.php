<?php
// ============================================================
// EDIT EVENT
// College Event Management System
// ============================================================
require_once 'header.php';

$error = '';
$event = null;

// Validate event ID
$event_id = intval($_GET['id'] ?? 0);
if ($event_id <= 0) {
    header('Location: events.php');
    exit();
}

// Fetch event data (SELECT + WHERE)
$stmt = mysqli_prepare($conn, "SELECT * FROM events WHERE event_id = ?");
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$event) {
    header('Location: events.php');
    exit();
}

// Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = trim($_POST['event_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $event_time = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $capacity = intval($_POST['capacity'] ?? 0);
    $status = trim($_POST['status'] ?? 'Upcoming');

    if (empty($event_name) || empty($event_date) || empty($event_time) || empty($venue) || $capacity <= 0) {
        $error = 'Please fill in all required fields. Capacity must be greater than 0.';
    } else {
        $update_stmt = mysqli_prepare($conn, "UPDATE events SET event_name=?, description=?, event_date=?, event_time=?, venue=?, organizer=?, capacity=?, status=? WHERE event_id=?");
        mysqli_stmt_bind_param($update_stmt, "ssssssisi", $event_name, $description, $event_date, $event_time, $venue, $organizer, $capacity, $status, $event_id);

        if (mysqli_stmt_execute($update_stmt)) {
            header('Location: events.php?msg=updated');
            exit();
        } else {
            $error = 'Error updating event. Please try again.';
        }
        mysqli_stmt_close($update_stmt);

        // Update local variable for form
        $event['event_name'] = $event_name;
        $event['description'] = $description;
        $event['event_date'] = $event_date;
        $event['event_time'] = $event_time;
        $event['venue'] = $venue;
        $event['organizer'] = $organizer;
        $event['capacity'] = $capacity;
        $event['status'] = $status;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>✏️ Edit Event</h2>
        <p>Update event information</p>
    </div>
    <a href="events.php" class="btn btn-secondary">← Back to Events</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Edit Event Form -->
<div class="card">
    <div class="card-header">Event Information — ID: <?php echo $event_id; ?></div>
    <form method="POST" action="edit_event.php?id=<?php echo $event_id; ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="event_name">Event Name *</label>
                <input type="text" id="event_name" name="event_name" class="form-control" required
                    value="<?php echo htmlspecialchars($event['event_name']); ?>">
            </div>
            <div class="form-group">
                <label for="organizer">Organizer</label>
                <input type="text" id="organizer" name="organizer" class="form-control"
                    value="<?php echo htmlspecialchars($event['organizer']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="event_date">Event Date *</label>
                <input type="date" id="event_date" name="event_date" class="form-control" required
                    value="<?php echo htmlspecialchars($event['event_date']); ?>">
            </div>
            <div class="form-group">
                <label for="event_time">Event Time *</label>
                <input type="time" id="event_time" name="event_time" class="form-control" required
                    value="<?php echo htmlspecialchars($event['event_time']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="venue">Venue *</label>
                <input type="text" id="venue" name="venue" class="form-control" required
                    value="<?php echo htmlspecialchars($event['venue']); ?>">
            </div>
            <div class="form-group">
                <label for="capacity">Capacity *</label>
                <input type="number" id="capacity" name="capacity" class="form-control" required min="1"
                    value="<?php echo htmlspecialchars($event['capacity']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <?php
                $statuses = ['Upcoming', 'Ongoing', 'Completed', 'Cancelled'];
                foreach ($statuses as $s):
                ?>
                    <option value="<?php echo $s; ?>" <?php echo ($event['status'] === $s) ? 'selected' : ''; ?>>
                        <?php echo $s; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update Event</button>
            <a href="events.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
