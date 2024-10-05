<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}
?>
<h1>Welcome to Admin Dashboard</h1>
<p>Hello, <?php echo $_SESSION['email']; ?>!</p>
