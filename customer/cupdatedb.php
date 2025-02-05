<?php
session_start();
require_once "../sql.php";

if (!isset($_SESSION['customer_login_user'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the shopping cart exists and has items
if (!isset($_SESSION['shopping_cart']) || empty($_SESSION['shopping_cart'])) {
    header("Location: cbuy_crops.php?error=cart_empty");
    exit();
}

if (!isset($_SESSION['customer_id'])) {
    header("Location: cbuy_crops.php?error=customer_not_found");
    exit();
}

$conn->begin_transaction();

try {
    foreach ($_SESSION['shopping_cart'] as $item) {
        // Ensure quantity is not negative
        $stmt = $conn->prepare("SELECT quantity FROM production_approx WHERE crop = ?");
        $stmt->bind_param("s", $item['item_name']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row || $row['quantity'] < $item['item_quantity']) {
            throw new Exception("Insufficient stock for " . $item['item_name']);
        }

        // Update inventory
        $stmt = $conn->prepare("UPDATE production_approx SET quantity = quantity - ? WHERE crop = ?");
        $stmt->bind_param("is", $item['item_quantity'], $item['item_name']);
        if (!$stmt->execute()) {
            throw new Exception("Error updating inventory for " . $item['item_name']);
        }

        // Record transaction
        $stmt = $conn->prepare("INSERT INTO transactions (customer_id, crop_name, quantity, amount, transaction_date) 
                                VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isid", $_SESSION['customer_id'], $item['item_name'], 
                          $item['item_quantity'], $item['item_price']);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting transaction for " . $item['item_name']);
        }
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
    header("Location: cbuy_crops.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>
