<?php
// Enable detailed error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON response type
header("Content-Type: application/json");

// Autoload PHPMailer from Composer
require __DIR__ . '/vendor/autoload.php';

// Connect to your database
require 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Get JSON data from the frontend
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'] ?? '';
$email = $data['email'] ?? '';
$phone = $data['phone'] ?? '';
$clinic = $data['clinic'] ?? '';

if (!$username || !$email || !$phone || !$clinic) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit;
}

// Generate a secure 8-character password
$generatedPassword = bin2hex(random_bytes(4)); // e.g., a1b2c3d4

// Check if user/email already exists
$checkQuery = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$checkQuery->bind_param("ss", $username, $email);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Username or email already exists."]);
    exit;
}

// Insert user into database
$stmt = $conn->prepare("INSERT INTO users (username, email, phone, clinic_name, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $email, $phone, $clinic, $generatedPassword);

if ($stmt->execute()) {
    // Send email with PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tchoupachristian@gmail.com';         // Your Gmail
        $mail->Password   = 'dlxlzwdkdzzgkxwu';                    // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('tchoupachristian@gmail.com', 'Hessom Medical');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Hessom Medical Login Password';
        $mail->Body    = "
            Hello <strong>$username</strong>,<br><br>
            Your auto-generated password is: <strong>$generatedPassword</strong><br><br>
            Please use this password to log in to your account.<br><br>
            Regards,<br>
            <strong>Hessom Medical Team</strong>
        ";

        $mail->send();
        echo json_encode(["status" => "success"]);

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Email could not be sent. Mailer Error: " . $mail->ErrorInfo]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Signup failed. Please try again."]);
}
?>