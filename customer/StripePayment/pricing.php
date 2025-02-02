v<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Page</title>
    <link rel="stylesheet" href="bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <style>
        .container { margin-top: 100px; }
        .card { width: 300px; }
        .card:hover { transform: scale(1.05); transition: all .3s ease-in-out; }
        .list-group-item { border: 0px; padding: 5px; }
        .price { font-size: 72px; }
        .currency { position: relative; font-size: 25px; top: -31px; }
    </style>
</head>
<body>
<div class="container">
    <?php
        require_once "config.php";
        require_once "products.php";

        foreach ($products as $productID => $attributes) {
            echo '
                <br>
                <form action="stripeIPN.php?id='.$productID.'" method="POST">
                    <input type="hidden" name="product_id" value="'.$productID.'">
                    <button type="submit" class="btn btn-primary">Buy Now - ₹'.($attributes["price"]/100).'</button>
                </form>
            ';
        }
    ?>
</div>
</body>
</html>
