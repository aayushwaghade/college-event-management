<?php
// ============================================================
// STUDENTS - List All Students with Search
// College Event Management System
// ============================================================
require_once 'header.php';

// Handle search/filter (GET parameters)
$search = trim($_GET['search'] ?? '');
$dept_filter = trim($_GET['department'] ?? '');

// Build query with WHERE conditions
$query = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (student_name LIKE ? OR roll_number LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sss';
}

if (!empty($dept_filter)) {
    $query .= " AND department = ?";
    $params[] = $dept_filter;
    $types .= 's';
}

$query .= " ORDER BY student_id DESC";

// Execute prepared statement
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get unique departments for filter dropdown
$dept_result = mysqli_query($conn, "SELECT DISTINCT department FROM students ORDER BY department");
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>👥 Student Management</h2>
        <p>Manage student records</p>
    </div>
    <a href="add_student.php" class="btn btn-primary">➕ Add New Student</a>
</div>

<!-- Display Messages -->
<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'added'): ?>
        <div class="alert alert-success">✅ Student added successfully.</div>
    <?php elseif ($_GET['msg'] === 'updated'): ?>
        <div class="alert alert-success">✅ Student updated successfully.</div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">✅ Student deleted successfully.</div>
    <?php elseif ($_GET['msg'] === 'delete_error'): ?>
        <div class="alert alert-danger">⚠️ Error deleting student. Student may have active registrations.</div>
    <?php endif; ?>
<?php endif; ?>

<!-- Search and Filter Bar -->
<div class="card">
    <form method="GET" action="students.php">
        <div class="filter-bar">
            <div class="form-group">
                <label>Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, roll number, or email..."
                    value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department" class="form-control">
                    <option value="">All Departments</option>
                    <?php while ($dept = mysqli_fetch_assoc($dept_result)): ?>
                        <option value="<?php echo htmlspecialchars($dept['department']); ?>"
                            <?php echo ($dept_filter === $dept['department']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['department']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
            <a href="students.php" class="btn btn-secondary">↻ Reset</a>
        </div>
    </form>
</div>

<!-- Students Table -->
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Roll Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($student = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $student['student_id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($student['roll_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['phone']); ?></td>
                            <td><?php echo htmlspecialchars($student['department']); ?></td>
                            <td><?php echo htmlspecialchars($student['year']); ?></td>
                            <td>
                                <a href="edit_student.php?id=<?php echo $student['student_id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                                <a href="delete_student.php?id=<?php echo $student['student_id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirmDelete('Are you sure you want to delete this student?');">
                                    🗑️ Delete
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-icon">📭</div>
                            <p>No students found.</p>
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
