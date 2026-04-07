<?php include("../includes/navbar.php"); ?>
<?php
session_start();
include("../config/db.php");

/* CREATE CART */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* =========================
   ADD TO CART (NO DUPLICATE)
========================= */
if (isset($_POST['add_to_cart'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $found = false;

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {

            /* GET STOCK FROM DB */
            $res = mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id");
            $data = mysqli_fetch_assoc($res);
            $stock = $data['stock'];

            /* CHECK STOCK */
            if ($_SESSION['cart'][$key]['quantity'] + $quantity <= $stock) {
                $_SESSION['cart'][$key]['quantity'] += $quantity;
            } else {
                echo "Cannot add more than available stock!";
            }

            $found = true;
            break;
        }
    }

    if (!$found) {

        /* CHECK STOCK BEFORE ADD */
        $res = mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id");
        $data = mysqli_fetch_assoc($res);

        if ($quantity <= $data['stock']) {
            $_SESSION['cart'][] = [
                "id" => $id,
                "name" => $name,
                "price" => $price,
                "quantity" => $quantity
            ];
        } else {
            echo "Stock not available!";
        }
    }

    header("Location: cart.php");
    exit();
}

/* =========================
   INCREASE QUANTITY
========================= */
if (isset($_GET['increase'])) {
    $id = $_GET['increase'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {

            $res = mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id");
            $data = mysqli_fetch_assoc($res);
            $stock = $data['stock'];

            if ($_SESSION['cart'][$key]['quantity'] < $stock) {
                $_SESSION['cart'][$key]['quantity']++;
            } else {
                echo "Stock limit reached!";
            }

            break;
        }
    }

    header("Location: cart.php");
    exit();
}

/* =========================
   DECREASE QUANTITY
========================= */
if (isset($_GET['decrease'])) {
    $id = $_GET['decrease'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {

            $_SESSION['cart'][$key]['quantity']--;

            if ($_SESSION['cart'][$key]['quantity'] <= 0) {
                unset($_SESSION['cart'][$key]);
            }

            break;
        }
    }

    header("Location: cart.php");
    exit();
}

/* =========================
   REMOVE ITEM
========================= */
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
</head>
<body>

<h2>Your Cart</h2>

<?php
$total = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $item) {

        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
?>

<div style="border:1px solid black; padding:10px; margin:10px;">

    <h3><?php echo $item['name']; ?></h3>
    <p>Price: ₹<?php echo $item['price']; ?></p>

    <!-- QUANTITY CONTROLS -->
    <a href="cart.php?decrease=<?php echo $item['id']; ?>">➖</a>

    <strong><?php echo $item['quantity']; ?></strong>

    <a href="cart.php?increase=<?php echo $item['id']; ?>">➕</a>

    <p>Subtotal: ₹<?php echo $subtotal; ?></p>

   
    
</div>

<?php } ?>

<h3>Total: ₹<?php echo $total; ?></h3>

<a href="checkout.php">Proceed to Checkout</a>

<?php
} else {
    echo "<p>Cart is empty!</p>";
}
?>

</body>
</html>