<?php
// session_start();
// require('../sql.php'); // Includes Login Script

// // Include PHPMailer files
// require '../smtp/PHPMailer.php';
// require '../smtp/SMTP.php';
// require '../smtp/Exception.php';

// $email = $_SESSION['farmer_login_user'];
// $res = mysqli_query($conn, "select * from farmerlogin where email='$email'");
// $count = mysqli_num_rows($res);

// if ($count > 0) {
//     $otp = rand(11111, 99999);
//     mysqli_query($conn, "update farmerlogin set otp='$otp' where email ='$email'");
    
//     $html = "Your OTP verification code for Agriculture Portal is " . $otp;
    
//     // Send OTP email
//     if (smtp_mailer($email, 'OTP Verification', $html)) {
//         echo "yes";
//     } else {
//         echo "Failed to send OTP.";
//     }
// } else {
//     echo "not exist";
// }

// // Function to send email using PHPMailer
// function smtp_mailer($to, $subject, $msg) {
//     try {
//         $mail = new PHPMailer\PHPMailer\PHPMailer();  // Create instance of PHPMailer
//         $mail->isSMTP();                             // Set mailer to use SMTP
//         $mail->Host = 'smtp.gmail.com';               // Specify the SMTP server
//         $mail->SMTPAuth = true;                      // Enable SMTP authentication
//         $mail->Username = 'arnabbhowmik499@gmail.com'; // Your Gmail username
//         $mail->Password = 'glpg jbgp tusf bxms';      // Your Gmail app password
//         $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
//         $mail->Port = 587;                           // TCP port to connect to

//         $mail->setFrom('arnabbhowmik499@gmail.com', 'Agriculture Portal');
//         $mail->addAddress($to);                      // Add recipient's email address
//         $mail->Subject = $subject;                   // Set email subject
//         $mail->Body = $msg;                          // Set email body

//         // Send email and return result
//         if (!$mail->send()) {
//             return false;
//         } else {
//             return true;
//         }
//     } catch (Exception $e) {
//         // Error handling
//         echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
//         return false;
//     }
// }
?>



 
// function smtp_mailer($to,$subject, $msg){
// 	require_once("../smtp/class.phpmailer.php");
// 	$mail = new PHPMailer(); 
// 	$mail->IsSMTP(); 
// 	$mail->SMTPDebug = 2; 
// 	$mail->SMTPAuth = TRUE; 
// 	$mail->SMTPSecure = 'ssl'; 
// 	$mail->Host = "smtp.gmail.com";
// 	$mail->Port = 465; 
// 	$mail->IsHTML(true);
// 	$mail->CharSet = 'UTF-8';
// 	$mail->Username = "arnabbhowmik499@gmail.com";   
//     $mail->Password = "glpg jbgp tusf bxms"; 	
//     $mail->SetFrom("arnabbhowmik499@gmail.com");  
// 	$mail->Subject = $subject;
// 	$mail->Body =$msg;
// 	$mail->AddAddress($to);
// 	if(!$mail->Send()){
// 		return 0;
// 	}else{
// 		return 1;
// 	}
// }

