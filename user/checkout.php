<?php include("../includes/navbar.php"); ?>
<?php
session_start();
include("../config/db.php");

/* CHECK LOGIN */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* CHECK CART */
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Cart is empty!";
    exit();
}

if (isset($_POST['place_order'])) {

    $user_id = $_SESSION['user_id'];
    $total = 0;

    /* CALCULATE TOTAL */
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $order_time = date("Y-m-d H:i:s");

    /* INSERT ORDER */
    mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, status, order_time)
                         VALUES ($user_id, $total, 'Pending', '$order_time')");

    $order_id = mysqli_insert_id($conn);

    /* INSERT ORDER ITEMS + UPDATE STOCK */
    foreach ($_SESSION['cart'] as $item) {

        $id = $item['id'];
        $qty = $item['quantity'];

        /* 🔥 CHECK STOCK AGAIN (IMPORTANT) */
        $res = mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id");
        $data = mysqli_fetch_assoc($res);

        if ($data['stock'] < $qty) {
            echo "Not enough stock for item ID: $id";
            exit();
        }

        /* INSERT ITEM */
        mysqli_query($conn, "INSERT INTO order_items (order_id, medicine_id, quantity)
                             VALUES ($order_id, $id, $qty)");

        /* 🔥 UPDATE STOCK */
        mysqli_query($conn, "UPDATE medicines 
                             SET stock = stock - $qty 
                             WHERE id = $id");
    }

    /* CLEAR CART */
    unset($_SESSION['cart']);

    echo "<h2>✅ Order Placed Successfully!</h2>";
    echo "<p>Your Order ID: $order_id</p>";
    echo "<a href='medicines.php'>Continue Shopping</a>";
}
?>

<h2>Checkout</h2>

<form method="POST">
    <button type="submit" name="place_order">Place Order</button>
</form>