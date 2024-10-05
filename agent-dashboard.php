<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'agent') {
    header("Location: login.html");
    exit();
}
?>
<h1>Welcome to Agent Dashboard</h1>
<p>Hello, <?php echo $_SESSION['email']; ?>!</p>
