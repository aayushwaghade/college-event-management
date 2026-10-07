<?php
// ============================================================
// INDEX - Redirect to Login or Dashboard
// College Event Management System
// ============================================================
require_once 'db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit();
?>
