<?php
// ============================================================
// EVENTS - List All Events with Search/Filter
// College Event Management System
// ============================================================
require_once 'header.php';

// Handle search/filter (GET parameters)
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Build query with LEFT JOIN to count registrations (JOIN + COUNT + GROUP BY)
$query = "SELECT e.event_id, e.event_name, e.description, e.event_date, e.event_time, e.venue, e.organizer, e.capacity, e.status, COUNT(r.registration_id) AS reg_count
          FROM events e
          LEFT JOIN registrations r ON e.event_id = r.event_id
          WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (e.event_name LIKE ? OR e.venue LIKE ? OR e.organizer LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if (!empty($status_filter)) {
    $query .= " AND e.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$query .= " GROUP BY e.event_id, e.event_name, e.description, e.event_date, e.event_time, e.venue, e.organizer, e.capacity, e.status ORDER BY e.event_date DESC";

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>📅 Event Management</h2>
        <p>Manage college events</p>
    </div>
    <a href="add_event.php" class="btn btn-primary">➕ Add New Event</a>
</div>

<!-- Display Messages -->
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'added'): ?>
        <div class="alert alert-success">✅ Event added successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success">✅ Event updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">✅ Event deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_error'): ?>
        <div class="alert alert-danger">⚠️ Error deleting event.</div>
    <?php endif; ?>
<?php endif; ?>

<!-- Search and Filter Bar -->
<div class="card">
    <form method="GET" action="events.php">
        <div class="filter-bar">
            <div class="form-group">
                <label>Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by event name, venue, or organizer..."
                    value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Upcoming" <?php echo ($status_filter === 'Upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="Ongoing" <?php echo ($status_filter === 'Ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="Completed" <?php echo ($status_filter === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo ($status_filter === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
            <a href="events.php" class="btn btn-secondary">↻ Reset</a>
        </div>
    </form>
</div>

<!-- Events Table -->
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Organizer</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Registrations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($event = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $event['event_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($event['event_name']); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($event['event_time'])); ?></td>
                            <td><?php echo htmlspecialchars($event['venue']); ?></td>
                            <td><?php echo htmlspecialchars($event['organizer']); ?></td>
                            <td><?php echo $event['capacity']; ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($event['status']); ?>">
                                    <?php echo htmlspecialchars($event['status']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo $event['reg_count']; ?></strong></td>
                            <td>
                                <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                <a href="delete_event.php?id=<?php echo $event['event_id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirmDelete('Are you sure you want to delete this event?');">
                                    🗑️ Delete
                                </a>
                                <a href="registrations.php?event=<?php echo $event['event_id']; ?>" class="btn btn-primary btn-sm">👁️ View Regs</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>No events found.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
mysqli_stmt_close($stmt);
require_once 'footer.php';
?>
