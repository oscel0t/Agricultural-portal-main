<?php
session_start();
require_once "../sql.php";

if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../login.php");
    exit();
}

// Process database updates after successful payment
if(isset($_SESSION['shopping_cart'])) {
    $conn->begin_transaction();
    
    try {
        foreach($_SESSION['shopping_cart'] as $item) {
            // Update inventory
            $stmt = $conn->prepare("UPDATE production_approx SET quantity = quantity - ? WHERE crop = ?");
            $stmt->bind_param("is", $item['item_quantity'], $item['item_name']);
            $stmt->execute();
            
            // Record transaction
            $stmt = $conn->prepare("INSERT INTO transactions (customer_id, crop_name, quantity, amount, transaction_date) 
                                   VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isid", $_SESSION['customer_id'], $item['item_name'], 
                            $item['item_quantity'], $item['item_price']);
            $stmt->execute();
        }
        
        // Clear cart
        unset($_SESSION['shopping_cart']);
        unset($_SESSION['Total_Cart_Price']);
        
        $conn->commit();
        header("Location: cmoney_transfered.php");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        header("Location: cbuy_crops.php?error=payment_failed");
        exit();
    }
} else {
    header("Location: cbuy_crops.php");
    exit();
}
?>