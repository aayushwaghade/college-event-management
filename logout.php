<?php
// ============================================================
// LOGOUT
// College Event Management System
// ============================================================
require_once 'db.php';

// Destroy session and redirect to login
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>
