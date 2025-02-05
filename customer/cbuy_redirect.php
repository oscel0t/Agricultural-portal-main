<?php
session_start();
require('../sql.php');

if (isset($_POST['add_to_cart'])) {
    $crop = $_POST['crops'];
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];

    // Fetch trade_id from database
    $stmt = $conn->prepare("SELECT trade_id FROM farmer_crops_trade WHERE Trade_crop = ?");
    $stmt->bind_param("s", $crop);
    $stmt->execute();
    $stmt->bind_result($trade_id);
    $stmt->fetch();
    $stmt->close();

    if (empty($trade_id)) {
        echo "<script>alert('Invalid Crop Selection'); window.location='cbuy_crops.php';</script>";
        exit();
    }

    // Check if the item already exists in the cart session
    $item_exists = false;
    if (isset($_SESSION["shopping_cart"])) {
        foreach ($_SESSION["shopping_cart"] as $keys => $values) {
            if ($values["item_name"] == $crop) {
                $item_exists = true;
                break;
            }
        }
    }
    // After adding the item to the cart in cbuy_redirect.php
if (!$item_exists) {
    $item_array = array(
        'item_name' => $crop,
        'item_quantity' => $quantity,
        'item_price' => $price
    );
    $_SESSION["shopping_cart"][] = $item_array;
}

// Recalculate total after adding item
$_SESSION['Total_Cart_Price'] = array_sum(array_column($_SESSION["shopping_cart"], 'item_price'));

    // Check if cart table has 'trade_id' column
    $result = $conn->query("SHOW COLUMNS FROM cart LIKE 'trade_id'");
    $has_trade_id = $result->num_rows > 0;

    // Check if the crop already exists in the cart table
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE cropname = ?");
    $stmt->bind_param("s", $crop);
    $stmt->execute();
    $stmt->bind_result($existing_quantity);
    $stmt->fetch();
    $stmt->close();

    if ($existing_quantity !== null) {
        // Update existing entry
        $new_quantity = $existing_quantity + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, price = ? WHERE cropname = ?");
        $stmt->bind_param("ids", $new_quantity, $price, $crop);
    } else {
        // Insert new entry based on trade_id availability
        if ($has_trade_id) {
            $stmt = $conn->prepare("INSERT INTO cart (trade_id, cropname, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isid", $trade_id, $crop, $quantity, $price);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (cropname, quantity, price) VALUES (?, ?, ?)");
            $stmt->bind_param("sid", $crop, $quantity, $price);
        }
    }

    $stmt->execute();
    $stmt->close();

    header("Location: cbuy_crops.php");
    exit();
}

// Handle item removal
// if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id'])) {
//     $id = $_GET['id'];  // No (int) casting to prevent 0 issues

//     if (isset($_SESSION["shopping_cart"])) {
//         foreach ($_SESSION["shopping_cart"] as $keys => $values) {
//             if (isset($values["item_id"]) && $values["item_id"] == $id) {
//                 unset($_SESSION["shopping_cart"][$keys]);
//                 $_SESSION["shopping_cart"] = array_values($_SESSION["shopping_cart"]); // Reset array index
//                 break;
//             }
//         }
//     }
// Handle item removal
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id'])) {
    $id = $_GET['id'];  // No (int) casting to prevent 0 issues

    if (isset($_SESSION["shopping_cart"])) {
        foreach ($_SESSION["shopping_cart"] as $keys => $values) {
            if (isset($values["item_name"]) && $values["item_name"] == $id) {
                unset($_SESSION["shopping_cart"][$keys]);
                $_SESSION["shopping_cart"] = array_values($_SESSION["shopping_cart"]); // Reset array index
                break;
            }
        }
    }

    // Remove from database cart table
    $stmt = $conn->prepare("DELETE FROM cart WHERE cropname = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: cbuy_crops.php");
    exit();
} // <-- Closing bracket was missing here



    // Remove from database cart table if trade_id exists
    // $stmt = $conn->prepare("DELETE FROM cart WHERE trade_id = ? OR cropname = ?");
    // $stmt->bind_param("is", $id, $id);
//     $stmt = $conn->prepare("DELETE FROM cart WHERE cropname = ?");
//     $stmt->bind_param("s", $id);


//     $stmt->execute();
//     $stmt->close();

//     header("Location: cbuy_crops.php");
//     exit();
// }


?>
