<?php
// ============================================================
// ADD EVENT
// College Event Management System
// ============================================================
require_once 'header.php';

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = trim($_POST['event_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = trim($_POST['event_date'] ?? '');
    $event_time = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $capacity = intval($_POST['capacity'] ?? 0);
    $status = trim($_POST['status'] ?? 'Upcoming');

    // Validate required fields
    if (empty($event_name) || empty($event_date) || empty($event_time) || empty($venue) || $capacity <= 0) {
        $error = 'Please fill in all required fields. Capacity must be greater than 0.';
    } else {
        // INSERT event
        $stmt = mysqli_prepare($conn, "INSERT INTO events (event_name, description, event_date, event_time, venue, organizer, capacity, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssssis", $event_name, $description, $event_date, $event_time, $venue, $organizer, $capacity, $status);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: events.php?msg=added');
            exit();
        } else {
            $error = 'Error adding event. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>➕ Add New Event</h2>
        <p>Create a new college event</p>
    </div>
    <a href="events.php" class="btn btn-secondary">← Back to Events</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Add Event Form -->
<div class="card">
    <div class="card-header">Event Information</div>
    <form method="POST" action="add_event.php">
        <div class="form-row">
            <div class="form-group">
                <label for="event_name">Event Name *</label>
                <input type="text" id="event_name" name="event_name" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['event_name'] ?? ''); ?>"
                    placeholder="Enter event name">
            </div>
            <div class="form-group">
                <label for="organizer">Organizer</label>
                <input type="text" id="organizer" name="organizer" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['organizer'] ?? ''); ?>"
                    placeholder="e.g., CSE Department">
            </div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control"
                placeholder="Enter event description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="event_date">Event Date *</label>
                <input type="date" id="event_date" name="event_date" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="event_time">Event Time *</label>
                <input type="time" id="event_time" name="event_time" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="venue">Venue *</label>
                <input type="text" id="venue" name="venue" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['venue'] ?? ''); ?>"
                    placeholder="e.g., Main Auditorium">
            </div>
            <div class="form-group">
                <label for="capacity">Capacity *</label>
                <input type="number" id="capacity" name="capacity" class="form-control" required min="1"
                    value="<?php echo htmlspecialchars($_POST['capacity'] ?? ''); ?>"
                    placeholder="Maximum number of participants">
            </div>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="Upcoming" <?php echo (($_POST['status'] ?? '') === 'Upcoming' || !isset($_POST['status'])) ? 'selected' : ''; ?>>Upcoming</option>
                <option value="Ongoing" <?php echo (($_POST['status'] ?? '') === 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                <option value="Completed" <?php echo (($_POST['status'] ?? '') === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                <option value="Cancelled" <?php echo (($_POST['status'] ?? '') === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Add Event</button>
            <a href="events.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
