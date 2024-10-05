<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.html");
    exit();
}
?>
<h1>Welcome to User Dashboard</h1>
<p>Hello, <?php echo $_SESSION['email']; ?>!</p>
