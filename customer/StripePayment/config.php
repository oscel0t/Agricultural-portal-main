<?php
require_once "stripe-php-master/init.php";
require_once "products.php";

$stripeDetails = [
    "secretKey" => "sk_test_51QnRMf2fOrBn3yViEAcQMZcPhX0BTiazQzCKSEdBbdJ1YuTHhMif3ET5mqXoJRs0nzi0Rzl2LMMCJo3msWrdHNpX00siG4fm3e", // Replace with your Stripe Secret key
    "publishableKey" => "pk_test_51QnRMf2fOrBn3yViXnXuOxDcwEZeDQVCXm1lJBnm71WGJWeqy1pjez5WohuxmBRb3QnhPHCYmhq8iZHagqyhtBtY00tEuNf26V" // Replace with your Stripe Publishable key
];

\Stripe\Stripe::setApiKey($stripeDetails['secretKey']);
?>