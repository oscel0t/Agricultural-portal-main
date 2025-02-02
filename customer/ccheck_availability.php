<?php
session_start();
require('../sql.php'); // Includes SQL connection script

if (isset($_POST['crops']) && isset($_POST['quantity'])) {
    $flag = 0;
    $temp = 0;

    $crop = mysqli_real_escape_string($conn, $_POST['crops']); // Prevent SQL Injection
    $quantity = (int)$_POST['quantity']; // Ensure quantity is an integer

    // Check if the crop exists in farmer_crops_trade
    $query1 = "SELECT 1 FROM farmer_crops_trade WHERE Trade_crop = ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("s", $crop);
    $stmt1->execute();
    $stmt1->store_result();
    
    if ($stmt1->num_rows > 0) {
        $flag = 1;
    }
    $stmt1->close();

    // Get total available crop quantity
    $query2 = "SELECT SUM(Crop_quantity) as total_quantity FROM farmer_crops_trade WHERE Trade_crop = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("s", $crop);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    if ($row2 = $result2->fetch_assoc()) {
        $temp = $row2["total_quantity"];
    }
    $stmt2->close();

    // Subtract quantity already in the cart
    $query8 = "SELECT SUM(quantity) as cart_quantity FROM cart WHERE cropname = ?";
    $stmt8 = $conn->prepare($query8);
    $stmt8->bind_param("s", $crop);
    $stmt8->execute();
    $result8 = $stmt8->get_result();

    if ($row8 = $result8->fetch_assoc()) {
        $cartQuantity = $row8['cart_quantity'];
        $temp -= $cartQuantity; // Subtract cart quantity from available quantity
    }
    $stmt8->close();

    // If requested quantity is more than available, flag should be 0 (invalid)
    if ($quantity > $temp) {
        $flag = 0;
    }

    // Fetch Minimum Support Price (MSP)
    $query3 = "SELECT msp FROM farmer_crops_trade WHERE Trade_crop = ? LIMIT 1";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bind_param("s", $crop);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    
    $x = 0;
    if ($row3 = $result3->fetch_assoc()) {
        $x = $row3["msp"] * $quantity;
    }
    $stmt3->close();

    // Fetch Trade ID
    $query4 = "SELECT trade_id FROM farmer_crops_trade WHERE Trade_crop = ? LIMIT 1";
    $stmt4 = $conn->prepare($query4);
    $stmt4->bind_param("s", $crop);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    
    $TradeId = null;
    if ($row4 = $result4->fetch_assoc()) {
        $TradeId = $row4["trade_id"];
    }
    $stmt4->close();

    // Prepare response JSON
    $response = array(
        "flagR" => $flag,
        "xR" => $x,
        "TradeIdR" => $TradeId,
        "cropR" => $crop,
        "quantityR" => $quantity
    );

    echo json_encode($response);
}
?>
