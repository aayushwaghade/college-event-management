<?php
// ============================================================
// ADD STUDENT
// College Event Management System
// ============================================================
require_once 'header.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $roll_number = trim($_POST['roll_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $year = trim($_POST['year'] ?? '');

    // Validate required fields
    if (empty($student_name) || empty($roll_number) || empty($email) || empty($department) || empty($year)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check for duplicate roll number
        $check_stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE roll_number = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $roll_number);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'A student with this roll number already exists.';
        } else {
            // INSERT student
            $stmt = mysqli_prepare($conn, "INSERT INTO students (student_name, roll_number, email, phone, department, year) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $student_name, $roll_number, $email, $phone, $department, $year);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: students.php?msg=added');
                exit();
            } else {
                $error = 'Error adding student. Please try again.';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>➕ Add New Student</h2>
        <p>Add a new student record</p>
    </div>
    <a href="students.php" class="btn btn-secondary">← Back to Students</a>
</div>

<!-- Messages -->
<?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Add Student Form -->
<div class="card">
    <div class="card-header">Student Information</div>
    <form method="POST" action="add_student.php">
        <div class="form-row">
            <div class="form-group">
                <label for="student_name">Student Name *</label>
                <input type="text" id="student_name" name="student_name" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['student_name'] ?? ''); ?>"
                    placeholder="Enter student full name">
            </div>
            <div class="form-group">
                <label for="roll_number">Roll Number *</label>
                <input type="text" id="roll_number" name="roll_number" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['roll_number'] ?? ''); ?>"
                    placeholder="e.g., CSE2024001">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    placeholder="student@college.edu">
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control"
                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                    placeholder="10-digit phone number">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="department">Department *</label>
                <select id="department" name="department" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <option value="Computer Science & Engineering" <?php echo (($_POST['department'] ?? '') === 'Computer Science & Engineering') ? 'selected' : ''; ?>>Computer Science & Engineering</option>
                    <option value="Information Technology" <?php echo (($_POST['department'] ?? '') === 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                    <option value="Mechanical Engineering" <?php echo (($_POST['department'] ?? '') === 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                    <option value="Civil Engineering" <?php echo (($_POST['department'] ?? '') === 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
                    <option value="Electronics & Telecommunication" <?php echo (($_POST['department'] ?? '') === 'Electronics & Telecommunication') ? 'selected' : ''; ?>>Electronics & Telecommunication</option>
                </select>
            </div>
            <div class="form-group">
                <label for="year">Year *</label>
                <select id="year" name="year" class="form-control" required>
                    <option value="">-- Select Year --</option>
                    <option value="First Year" <?php echo (($_POST['year'] ?? '') === 'First Year') ? 'selected' : ''; ?>>First Year</option>
                    <option value="Second Year" <?php echo (($_POST['year'] ?? '') === 'Second Year') ? 'selected' : ''; ?>>Second Year</option>
                    <option value="Third Year" <?php echo (($_POST['year'] ?? '') === 'Third Year') ? 'selected' : ''; ?>>Third Year</option>
                    <option value="Fourth Year" <?php echo (($_POST['year'] ?? '') === 'Fourth Year') ? 'selected' : ''; ?>>Fourth Year</option>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Add Student</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
