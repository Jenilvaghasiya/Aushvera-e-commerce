<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    $email = $_POST["email"];
    $otp = rand(1000, 9999); // Generate 4-digit OTP

    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vadherharshil12@gmail.com';         // Your Gmail address
        $mail->Password = 'azta yyir qcsa qteg';            // Replace this with your real App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient
        $mail->setFrom('vadherharshil12@gmail.com', 'Ayushvera');
        $mail->addAddress($email); // Send to user

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body = "<h3>Your OTP is: <strong>$otp</strong></h3>";

        $mail->send();
        echo "OTP Sent: $otp"; // Send OTP back to client
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>
