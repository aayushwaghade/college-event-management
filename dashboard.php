<?php
// ============================================================
// DASHBOARD
// College Event Management System
// ============================================================
require_once 'header.php';

// ---- Fetch Dashboard Statistics using MySQL Queries ----

// Total Students (COUNT)
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$total_students = mysqli_fetch_assoc($result)['total'];

// Total Events (COUNT)
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events");
$total_events = mysqli_fetch_assoc($result)['total'];

// Total Registrations (COUNT)
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations");
$total_registrations = mysqli_fetch_assoc($result)['total'];

// Upcoming Events (COUNT + WHERE)
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM events WHERE status = 'Upcoming'");
$upcoming_events = mysqli_fetch_assoc($result)['total'];

// Approved Registrations (COUNT + WHERE)
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations WHERE status = 'Approved'");
$approved_registrations = mysqli_fetch_assoc($result)['total'];

// Cancelled Registrations (COUNT + WHERE)
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM registrations WHERE status = 'Cancelled'");
$cancelled_registrations = mysqli_fetch_assoc($result)['total'];

// Upcoming Events List with Registration Count (JOIN + COUNT + GROUP BY + WHERE + ORDER BY)
$upcoming_query = "SELECT e.event_id, e.event_name, e.event_date, e.event_time, e.venue, e.status,
                   COUNT(r.registration_id) AS reg_count
                   FROM events e
                   LEFT JOIN registrations r ON e.event_id = r.event_id
                   WHERE e.status IN ('Upcoming', 'Ongoing')
                   GROUP BY e.event_id, e.event_name, e.event_date, e.event_time, e.venue, e.status
                   ORDER BY e.event_date ASC";
$upcoming_result = mysqli_query($conn, $upcoming_query);
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>📊 Dashboard</h2>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon">👥</div>
        <div class="stat-value"><?php echo $total_students; ?></div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card secondary">
        <div class="stat-icon">📅</div>
        <div class="stat-value"><?php echo $total_events; ?></div>
        <div class="stat-label">Total Events</div>
    </div>
    <div class="stat-card info">
        <div class="stat-icon">📋</div>
        <div class="stat-value"><?php echo $total_registrations; ?></div>
        <div class="stat-label">Total Registrations</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon">⏳</div>
        <div class="stat-value"><?php echo $upcoming_events; ?></div>
        <div class="stat-label">Upcoming Events</div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">✅</div>
        <div class="stat-value"><?php echo $approved_registrations; ?></div>
        <div class="stat-label">Approved Registrations</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon">❌</div>
        <div class="stat-value"><?php echo $cancelled_registrations; ?></div>
        <div class="stat-label">Cancelled Registrations</div>
    </div>
</div>

<!-- Upcoming Events Table -->
<div class="card">
    <div class="card-header">📅 Upcoming & Ongoing Events</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Registrations</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($upcoming_result) > 0): ?>
                    <?php while ($event = mysqli_fetch_assoc($upcoming_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($event['event_name']); ?></strong></td>
                            <td><?php echo date('d M Y', strtotime($event['event_date'])); ?></td>
                            <td><?php echo date('h:i A', strtotime($event['event_time'])); ?></td>
                            <td><?php echo htmlspecialchars($event['venue']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($event['status']); ?>">
                                    <?php echo htmlspecialchars($event['status']); ?>
                                </span>
                            </td>
                            <td><strong><?php echo $event['reg_count']; ?></strong></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>No upcoming events found.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>
