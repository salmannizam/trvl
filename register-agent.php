<?php
// Enable CORS if necessary (for development, remove in production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Set content type to JSON
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$db   = 'practice'; // Your database name
$user = 'root'; // Default MySQL user for XAMPP
$pass = ''; // Default password for root (empty in XAMPP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$address = $data['address'] ?? '';
$aadhaar = $data['aadhaar'] ?? '';
$password = $data['password'] ?? '';

// Validate input
if (empty($name) || empty($email) || empty($address) || empty($aadhaar) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit();
}

// Check if agent already exists in users table by email
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([$email]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
    exit();
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Insert into users table
    $stmt = $pdo->prepare('INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)');
    $stmt->execute([$email, $hashedPassword, 'agent', 'inactive']);

    // Get the last inserted user ID
    $userId = $pdo->lastInsertId();

    // Insert into users_profile table
    $stmt = $pdo->prepare('INSERT INTO users_profile (user_id, name, address, aadhaar) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $name, $address, $aadhaar]);

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Agent registered successfully.']);

} catch (Exception $e) {
    // Rollback the transaction if something goes wrong
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Failed to register agent.']);
}

?>
