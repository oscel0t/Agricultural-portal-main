<?php
session_start();
require('../sql.php'); // Includes SQL connection script

if (isset($_POST['crops'])) {
    $crop = mysqli_real_escape_string($conn, $_POST['crops']); // Prevent SQL Injection

    // Fetch total estimated production quantity
    $query2 = "SELECT SUM(quantity) AS total_quantity FROM production_approx WHERE crop = ?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("s", $crop);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    $Cquantity = 0; // Default to zero in case no record is found
    if ($row2 = $result2->fetch_assoc()) {
        $Cquantity = $row2["total_quantity"] ?? 0;
    }
    $stmt2->close();

    // Fetch trade ID from farmer_crops_trade
    $query3 = "SELECT trade_id FROM farmer_crops_trade WHERE Trade_crop = ? LIMIT 1";
    $stmt3 = $conn->prepare($query3);
    $stmt3->bind_param("s", $crop);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    
    $TradeId = null; // Default to null in case no record is found
    if ($row3 = $result3->fetch_assoc()) {
        $TradeId = $row3["trade_id"];
    }
    $stmt3->close();

    // Prepare JSON response
    $result = array(
        "TradeIdR" => $TradeId,
        "quantityR" => $Cquantity
    );

    echo json_encode($result);
}
?>
