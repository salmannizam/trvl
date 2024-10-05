<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Database connection
$host = 'localhost';
$db   = 'practice';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed.");
}

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agent_id'], $_POST['new_status'])) {
    $agentId = $_POST['agent_id'];
    $newStatus = $_POST['new_status'];

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'agent'");
    $stmt->execute([$newStatus, $agentId]);

    header("Location: ../views/admin-dashboard.php");
    exit();
}

// Fetch agent data
$stmt = $pdo->prepare("SELECT user_id, email, status FROM users WHERE role = 'agent'");
$stmt->execute();
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
