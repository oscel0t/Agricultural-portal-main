<?php
session_start();
require('../sql.php'); // Includes Login Script

// Include PHPMailer files
require '../smtp/PHPMailer.php';
require '../smtp/SMTP.php';
require '../smtp/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_SESSION['customer_login_user'];
$res = mysqli_query($conn, "SELECT * FROM custlogin WHERE email='$email'");
$count = mysqli_num_rows($res);

if ($count > 0) {
    $otp = rand(11111, 99999);
    mysqli_query($conn, "UPDATE custlogin SET otp='$otp' WHERE email ='$email'");

    $html = "Your OTP verification code for Agriculture Portal is " . $otp;

    // Send OTP email
    if (smtp_mailer($email, 'OTP Verification', $html)) {
        echo "yes";
    } else {
        echo "Failed to send OTP.";
    }
} else {
    echo "not exist";
}

// Function to send email using PHPMailer
function smtp_mailer($to, $subject, $msg) {
    try {
        $mail = new PHPMailer(true);  // Enable exceptions

        $mail->isSMTP();                // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com'; // Specify the SMTP server
        $mail->SMTPAuth = true;         // Enable SMTP authentication
	    //CHANGE THE USERNAME AND PASSWORD
        $mail->Username = '1bi21cs014@bit-bangalore.edu.in'; // Your Gmail username
        $mail->Password = 'zsed ugme seuk ttho';   // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
        $mail->Port = 587;              // TCP port to connect to
		//CHANGE MAIL
        $mail->setFrom('1bi21cs014@bit-bangalore.edu.in', 'Agriculture Portal');
        $mail->addAddress($to);         // Add recipient's email address
        $mail->Subject = $subject;      // Set email subject
        $mail->Body = $msg;             // Set email body

        // Send email
        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}
?>