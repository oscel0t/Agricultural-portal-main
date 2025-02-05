<?php
include ('csession.php');
include ('../sql.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
    exit();
}

// Handle item removal
// In cbuy.php, handle removal by item name
if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $item_name = $_GET["id"];
    
    if (!empty($_SESSION["shopping_cart"])) {
        foreach ($_SESSION["shopping_cart"] as $keys => $values) {
            if ($values["item_name"] == $item_name) {
                unset($_SESSION["shopping_cart"][$keys]);
                $_SESSION["shopping_cart"] = array_values($_SESSION["shopping_cart"]);
                break;
            }
        }
        // Recalculate total
        $_SESSION['Total_Cart_Price'] = array_sum(array_column($_SESSION["shopping_cart"], 'item_price'));
    }
    
    // Delete from database
    $delete_query = "DELETE FROM cart WHERE cropname = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $item_name);
    $stmt->execute();
    
    header("Location: cbuy_crops.php");
    exit();
}

// Fetch user details
$user_check = $_SESSION['customer_login_user'];
$query4 = "SELECT * FROM custlogin WHERE email=?";
$stmt4 = $conn->prepare($query4);
$stmt4->bind_param("s", $user_check);
$stmt4->execute();
$result4 = $stmt4->get_result();
$row4 = $result4->fetch_assoc();
$para1 = $row4['cust_id'];
$para2 = $row4['cust_name'];
?>
<!DOCTYPE html>
<html>
<?php include ('cheader.php'); ?>
<body class="bg-white" id="top">
<?php include ('cnav.php'); ?>

<section class="section section-shaped section-lg">
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto text-center">
                <span class="badge badge-danger badge-pill mb-3">Buy Crops</span>
            </div>
        </div>

        <!-- Add to Cart Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="cbuy_redirect.php">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Crop Name</th>
                                <th>Quantity (KG)</th>
                                <th>Price (₹)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="crops" id="crops" class="form-control" required>
                                        <option value="">Select Crop</option>
                                        <?php
                                        $sql = "SELECT crop FROM production_approx WHERE quantity > 0";
                                        $result = $conn->query($sql);
                                        while($row = $result->fetch_assoc()) {
                                            echo "<option value='".$row["crop"]."'>".$row["crop"]."</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantity" id="quantity" 
                                           class="form-control" min="1" required 
                                           placeholder="Enter Quantity">
                                </td>
                                <td>
                                    <input type="text" name="price" id="price" 
                                           class="form-control" readonly>
                                </td>
                                <td>
                                    <button type="submit" name="add_to_cart" 
                                            class="btn btn-success btn-block" disabled>
                                        Add to Cart
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" name="tradeid" id="tradeid">
                </form>
            </div>
        </div>

        <!-- Cart Display -->
        <div class="card">
            <div class="card-body">
                <h3 class="mb-4">Your Cart</h3>
                <div class="table-responsive">
                    <table class="table table-striped cart-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            if(!empty($_SESSION["shopping_cart"])) {
                                foreach($_SESSION["shopping_cart"] as $keys => $values) {
                            ?>
                            <tr class="cart-item">
                                <td><?php echo ucfirst($values["item_name"]); ?></td>
                                <td><?php echo $values["item_quantity"]; ?> KG</td>
                                <td>₹<?php echo number_format($values["item_price"], 2); ?></td>
                                <td>
                                <a href="cbuy_redirect.php?action=delete&id=<?php echo !empty($values['item_name']) ? urlencode($values['item_name']) : ''; ?>" 
class="btn btn-warning btn-sm remove-btn" 
onclick="return confirm('Remove this item from cart?');">
Remove
</a>


                                </td>
                            </tr>
                            <?php
                                    $total += $values["item_price"];
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                <td colspan="2">₹<?php echo number_format($total, 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <form action="StripePayment/stripeIPN.php" method="POST">
                                        <button class="btn btn-primary btn-lg" 
                                            <?php echo ($total <= 0) ? 'disabled' : ''; ?>>
                                            Proceed to Payment (₹<?php echo number_format($total, 2); ?>)
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require("footer.php");?>

<!-- Scripts -->
<script src="https://js.stripe.com/v3/"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Quantity Validation
document.getElementById("quantity").addEventListener("input", function() {
    const max = parseInt(this.placeholder);
    if (this.value > max) {
        alert(`Maximum available quantity: ${max}`);
        this.value = max;
    }
    if (this.value < 1) this.value = 1;
});

// Price Calculation
$('#crops').change(function() {
    $.post('ccheck_quantity.php', { crops: $(this).val() }, function(response) {
        const data = JSON.parse(response);
        $('#quantity').attr('placeholder', data.quantityR);
        $('#tradeid').val(data.TradeIdR);
    });
});

$('#quantity').keyup(function() {
    const crop = $('#crops').val();
    const qty = $(this).val();
    
    if(crop && qty > 0) {
        $.post('ccheck_price.php', { crops: crop, quantity: qty }, function(price) {
            $('#price').val(price);
            $('[name="add_to_cart"]').prop('disabled', false);
        });
    }
});
</script>

<style>
.cart-table { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
.cart-item:hover { background-color: #f8f9fa; transition: 0.3s; }
.remove-btn:hover { transform: scale(1.05); }
</style>

</body>
</html>
