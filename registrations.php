<?php
// ============================================================
// REGISTRATIONS - View All Registrations with Filters
// College Event Management System
// ============================================================
require_once 'header.php';

// Handle filters (GET parameters)
$event_filter = intval($_GET['event'] ?? 0);
$status_filter = trim($_GET['status'] ?? '');
$dept_filter = trim($_GET['department'] ?? '');

// Build query with INNER JOIN between students, registrations, events
$query = "SELECT r.registration_id, r.registration_date, r.status AS reg_status,
                 s.student_name, s.roll_number, s.department,
                 e.event_name, e.event_date, e.venue
          FROM registrations r
          INNER JOIN students s ON r.student_id = s.student_id
          INNER JOIN events e ON r.event_id = e.event_id
          WHERE 1=1";
$params = [];
$types = '';

if ($event_filter > 0) {
    $query .= " AND r.event_id = ?";
    $params[] = $event_filter;
    $types .= 'i';
}

if (!empty($status_filter)) {
    $query .= " AND r.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($dept_filter)) {
    $query .= " AND s.department = ?";
    $params[] = $dept_filter;
    $types .= 's';
}

$query .= " ORDER BY r.registration_id DESC";

$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get events for filter dropdown
$events_list = mysqli_query($conn, "SELECT event_id, event_name FROM events ORDER BY event_name");

// Get departments for filter dropdown
$dept_list = mysqli_query($conn, "SELECT DISTINCT department FROM students ORDER BY department");
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>📋 Registration Management</h2>
        <p>View and manage event registrations</p>
    </div>
    <a href="register_event.php" class="btn btn-primary">➕ New Registration</a>
</div>

<!-- Display Messages -->
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success">✅ Registration status updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'error'): ?>
        <div class="alert alert-danger">⚠️ Error updating registration status.</div>
    <?php endif; ?>
<?php endif; ?>

<!-- Filter Bar -->
<div class="card">
    <form method="GET" action="registrations.php">
        <div class="filter-bar">
            <div class="form-group">
                <label>Event</label>
                <select name="event" class="form-control">
                    <option value="0">All Events</option>
                    <?php while ($ev = mysqli_fetch_assoc($events_list)): ?>
                        <option value="<?php echo $ev['event_id']; ?>"
                            <?php echo ($event_filter == $ev['event_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ev['event_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Registered" <?php echo ($status_filter === 'Registered') ? 'selected' : ''; ?>>Registered</option>
                    <option value="Approved" <?php echo ($status_filter === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="Cancelled" <?php echo ($status_filter === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department" class="form-control">
                    <option value="">All Departments</option>
                    <?php while ($dept = mysqli_fetch_assoc($dept_list)): ?>
                        <option value="<?php echo htmlspecialchars($dept['department']); ?>"
                            <?php echo ($dept_filter === $dept['department']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['department']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="registrations.php" class="btn btn-secondary">↻ Reset</a>
        </div>
    </form>
</div>

<!-- Registrations Table -->
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Reg ID</th>
                    <th>Student Name</th>
                    <th>Roll No</th>
                    <th>Event Name</th>
                    <th>Event Date</th>
                    <th>Venue</th>
                    <th>Registered On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($reg = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $reg['registration_id']; ?></td>
                            <td><?php echo htmlspecialchars($reg['student_name']); ?></td>
                            <td><strong><?php echo htmlspecialchars($reg['roll_number']); ?></strong></td>
                            <td><strong><?php echo htmlspecialchars($reg['event_name']); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($reg['event_date'])); ?></td>
                            <td><?php echo htmlspecialchars($reg['venue']); ?></td>
                            <td><?php echo date('d M Y', strtotime($reg['registration_date'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($reg['reg_status']); ?>">
                                    <?php echo htmlspecialchars($reg['reg_status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="update_registration.php" style="display:inline-flex;gap:5px;align-items:center;">
                                    <input type="hidden" name="registration_id" value="<?php echo $reg['registration_id']; ?>">
                                    <select name="status" class="form-control" style="width:auto;padding:4px 8px;font-size:0.8rem;">
                                        <option value="Registered" <?php echo ($reg['reg_status'] === 'Registered') ? 'selected' : ''; ?>>Registered</option>
                                        <option value="Approved" <?php echo ($reg['reg_status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Cancelled" <?php echo ($reg['reg_status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>No registrations found.</p>
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
