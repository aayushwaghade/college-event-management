<?php
// ============================================================
// EDIT STUDENT
// College Event Management System
// ============================================================
require_once 'header.php';

$error = '';
$student = null;

// Validate student ID
$student_id = intval($_GET['id'] ?? 0);
if ($student_id <= 0) {
    header('Location: students.php');
    exit();
}

// Fetch student data (SELECT + WHERE)
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE student_id = ?");
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$student) {
    header('Location: students.php');
    exit();
}

// Handle form submission (UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $roll_number = trim($_POST['roll_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $year = trim($_POST['year'] ?? '');

    if (empty($student_name) || empty($roll_number) || empty($email) || empty($department) || empty($year)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Check for duplicate roll number (exclude current student)
        $check_stmt = mysqli_prepare($conn, "SELECT student_id FROM students WHERE roll_number = ? AND student_id != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $roll_number, $student_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Another student with this roll number already exists.';
        } else {
            // UPDATE student
            $update_stmt = mysqli_prepare($conn, "UPDATE students SET student_name=?, roll_number=?, email=?, phone=?, department=?, year=? WHERE student_id=?");
            mysqli_stmt_bind_param($update_stmt, "ssssssi", $student_name, $roll_number, $email, $phone, $department, $year, $student_id);

            if (mysqli_stmt_execute($update_stmt)) {
                header('Location: students.php?msg=updated');
                exit();
            } else {
                $error = 'Error updating student. Please try again.';
            }
            mysqli_stmt_close($update_stmt);
        }
        mysqli_stmt_close($check_stmt);

        // Update local variable for form display
        $student['student_name'] = $student_name;
        $student['roll_number'] = $roll_number;
        $student['email'] = $email;
        $student['phone'] = $phone;
        $student['department'] = $department;
        $student['year'] = $year;
    }
}
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h2>✏️ Edit Student</h2>
        <p>Update student information</p>
    </div>
    <a href="students.php" class="btn btn-secondary">← Back to Students</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Edit Student Form -->
<div class="card">
    <div class="card-header">Student Information — ID: <?php echo $student_id; ?></div>
    <form method="POST" action="edit_student.php?id=<?php echo $student_id; ?>">
        <div class="form-row">
            <div class="form-group">
                <label for="student_name">Student Name *</label>
                <input type="text" id="student_name" name="student_name" class="form-control" required
                    value="<?php echo htmlspecialchars($student['student_name']); ?>">
            </div>
            <div class="form-group">
                <label for="roll_number">Roll Number *</label>
                <input type="text" id="roll_number" name="roll_number" class="form-control" required
                    value="<?php echo htmlspecialchars($student['roll_number']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required
                    value="<?php echo htmlspecialchars($student['email']); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control"
                    value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="department">Department *</label>
                <select id="department" name="department" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php
                    $departments = ['Computer Science & Engineering', 'Information Technology', 'Mechanical Engineering', 'Civil Engineering', 'Electronics & Telecommunication'];
                    foreach ($departments as $dept):
                    ?>
                        <option value="<?php echo $dept; ?>" <?php echo ($student['department'] === $dept) ? 'selected' : ''; ?>>
                            <?php echo $dept; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="year">Year *</label>
                <select id="year" name="year" class="form-control" required>
                    <option value="">-- Select Year --</option>
                    <?php
                    $years = ['First Year', 'Second Year', 'Third Year', 'Fourth Year'];
                    foreach ($years as $yr):
                    ?>
                        <option value="<?php echo $yr; ?>" <?php echo ($student['year'] === $yr) ? 'selected' : ''; ?>>
                            <?php echo $yr; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update Student</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
