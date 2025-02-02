<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Total_Cart_Price'])) {
    $_SESSION['Total_Cart_Price'] = 1000; // Default ₹10.00 (1000 paise)
}

$Total_cart_price = $_SESSION['Total_Cart_Price'] * 100; // Convert to paise for Stripe

$products = [
    "product1" => [
        "title" => "Crops Payment",
        "price" => $Total_cart_price,
        "id" => "prod_Crops_Payment_" . uniqid() // Generate unique product ID dynamically
    ]
];
?>
