<?php
session_start();
require_once "config.php";
require_once "products.php";

if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../login.php");
    exit();
}

// Ensure cart total is available
$totalCartPrice = isset($_SESSION['Total_Cart_Price']) ? $_SESSION['Total_Cart_Price'] : 0;

// Convert price to paise (Stripe requires amount in the smallest currency unit)
$totalAmount = $totalCartPrice * 100; 

if ($totalAmount <= 0) {
    die("Error: Invalid cart total. Please add items to your cart.");
}

try {
    // Create a dynamic product for checkout
    $product = \Stripe\Product::create([
        'name' => 'Cart Total Payment',
        'description' => 'Payment for items in shopping cart'
    ]);

    // Create a Stripe price object
    $price = \Stripe\Price::create([
        'product' => $product->id,
        'unit_amount' => $totalAmount, // Ensure it's in paise
        'currency' => 'inr',
    ]);

    // Create Stripe checkout session
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price' => $price->id,
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/Agricultural-portal-main/customer/cupdatedb.php',
        'cancel_url' => 'http://localhost/Agricultural-portal-main/customer/cbuy_crops.php',
    ]);

    // Redirect to Stripe payment page
    header("Location: " . $checkout_session->url);
    exit();
} catch (\Stripe\Exception\ApiErrorException $e) {
    die("Stripe API error: " . $e->getMessage());
}
?>
