<?php
// Enable CORS for development (remove or limit in production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Set content type to JSON
header('Content-Type: application/json');

// Database connection
$host = 'localhost'; // Database host (usually localhost)
$db   = 'practice';  // Your database name
$user = 'root';      // Default MySQL user for XAMPP
$pass = '';          // Default password for root (empty in XAMPP)

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // You may want to log this error and show a generic message instead for production
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Get the JSON input data
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

// Check if the user already exists in the database
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
    exit();
}

// Hash the password using bcrypt
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user into the users table with role 'user' and status 'active'
$stmt = $pdo->prepare('INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)');
if ($stmt->execute([$email, $hashedPassword, 'user', 'active'])) {
    echo json_encode(['success' => true, 'message' => 'User created successfully.']);
} else {
    // It's always good to return more information for debugging (e.g., error code in development)
    echo json_encode(['success' => false, 'message' => 'Failed to create user.']);
}
?>
