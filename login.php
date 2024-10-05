<?php
session_start();

// Database connection (XAMPP default credentials)
$host = 'localhost';
$db   = 'practice';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Get JSON input data
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

// Check if the user exists
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
    exit();
}

// Check if the user is active
if ($user['status'] !== 'active') {
    echo json_encode(['success' => false, 'message' => 'User is not active.']);
    exit();
}

// Verify the password
if (!password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
    exit();
}

// Set session variables
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

// Return response based on role
if ($user['role'] === 'admin') {
    echo json_encode(['success' => true, 'role' => 'admin']);
} elseif ($user['role'] === 'agent') {
    echo json_encode(['success' => true, 'role' => 'agent']);
} else {
    echo json_encode(['success' => true, 'role' => 'user']);
}
?>
