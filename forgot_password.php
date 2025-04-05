<?php
require 'db.php'; // Make sure this contains your DB connection

// Include Composer's autoload file
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//  Get JSON input from frontend
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$phone = $data['phone'] ?? '';

// Prepare and run the DB query
$stmt = $conn->prepare("SELECT email, password FROM users WHERE username = ? AND phone = ?");
$stmt->bind_param("ss", $username, $phone);
$stmt->execute();
$result = $stmt->get_result();

// If user is found, send password via email
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $email = $user['email'];
    $password = $user['password'];

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tchoupachristian@gmail.com'; // Replace with your Gmail
        $mail->Password   = 'dlxlzwdkdzzgkxwu'; // Use Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email content
        $mail->setFrom('tchoupachristian@gmail.com', 'Hessom Medical');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = 'Password Recovery - Hessom Medical';
        $mail->Body    = "Hello <strong>$username</strong>,<br><br>Your password is: <strong>$password</strong><br><br>Thanks,<br>Hessom Medical Team";

        $mail->send();
        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Email send failed: {$mail->ErrorInfo}"]);
    }

} else {
    echo json_encode(["status" => "not_found"]);
}