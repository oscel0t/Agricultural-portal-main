<?php
session_start();
include('csession.php');

// Verify payment success
if (!isset($_SESSION['payment_success'])) {
    header("Location: cbuy_crops.php");
    exit();
}

unset($_SESSION['payment_success']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Successful</title>
</head>
<body>
    <h2>Payment Successful!</h2>
    <p>Thank you for your purchase. Your order is being processed.</p>
    <a href="cprofile.php" class="btn btn-primary">Return to Profile</a>
</body>
</html>
<!--                               <//?php
session_start();
echo "<h2>Payment Successful!</h2>";
echo "<p>Thank you for your purchase.</p>";
?> -->