<?php
// Database connection (replace with your credentials)
$host = 'localhost';
$db = 'your_database';
$user = 'your_db_user';
$pass = 'your_db_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initialize'])) {
    // Sanitize and get form data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_SPECIAL_CHARS);  // Updated sanitization
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if any users exist in the database
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();

    if ($userCount == 0) {
        // No users exist, proceed to create the admin
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role) VALUES ('admin', :email, :phone, :password, 'admin')");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            echo "<p>Admin user created successfully.</p>";
        } else {
            echo "<p>Failed to create admin user.</p>";
        }
    } else {
        echo "<p>An admin user has already been initialized.</p>";
    }
}
?>
