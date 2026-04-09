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
    header("Location: cart.php");
    exit();
}

$order_placed = false;
$new_order_id = null;
$error = '';

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

    $new_order_id = mysqli_insert_id($conn);

    /* INSERT ORDER ITEMS + UPDATE STOCK */
    $stock_error = false;
    foreach ($_SESSION['cart'] as $item) {
        $id = $item['id'];
        $qty = $item['quantity'];

        $res = mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id");
        $data = mysqli_fetch_assoc($res);

        if ($data['stock'] < $qty) {
            $stock_error = true;
            $error = "Not enough stock for one or more items. Please review your cart.";
            break;
        }

        mysqli_query($conn, "INSERT INTO order_items (order_id, medicine_id, quantity)
                             VALUES ($new_order_id, $id, $qty)");

        mysqli_query($conn, "UPDATE medicines SET stock = stock - $qty WHERE id = $id");
    }

    if (!$stock_error) {
        unset($_SESSION['cart']);
        $order_placed = true;
    }
}

include("../includes/header.php");
include("../includes/navbar.php");

/* Calculate totals for display */
$subtotal = 0;
$cart_items = $_SESSION['cart'] ?? [];
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$delivery_fee = 40;
$grand_total = $subtotal + $delivery_fee;
?>

<div class="container my-5" style="min-height: calc(100vh - 200px);">

<?php if ($order_placed): ?>
    <!-- Order Success -->
    <div class="row justify-content-center">
        <div class="col-md-6 text-center py-5">
            <div class="mb-4">
                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4">
                    <i class="fa-solid fa-circle-check fa-5x text-success"></i>
                </div>
            </div>
            <h2 class="fw-bold mb-3">Order Placed Successfully!</h2>
            <p class="text-muted fs-5 mb-1">Thank you for your order.</p>
            <p class="text-muted mb-4">Your Order ID: <strong class="text-dark">#ORD-<?php echo str_pad($new_order_id, 5, '0', STR_PAD_LEFT); ?></strong></p>
            <p class="text-muted mb-4">We'll deliver your medicines within <strong class="text-primary">10 minutes</strong>! 🚀</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="order_tracking.php" class="btn btn-primary rounded-pill px-4">Track Order</a>
                <a href="medicines.php" class="btn btn-outline-secondary rounded-pill px-4">Continue Shopping</a>
            </div>
        </div>
    </div>

<?php else: ?>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h2 class="fw-bold mb-0"><i class="fa-solid fa-bag-shopping me-2 text-primary"></i>Checkout</h2>
        </div>
        <div class="col-auto">
            <a href="cart.php" class="btn btn-outline-secondary rounded-pill"><i class="fa-solid fa-arrow-left me-2"></i>Back to Cart</a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger rounded-4 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-7 mb-4">
            <div class="card border-0 shadow-sm glass-panel rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt me-2 text-primary"></i>Order Summary</h5>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Medicine</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                                                <i class="fa-solid fa-pills text-primary"></i>
                                            </div>
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </div>
                                    </td>
                                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                                    <td class="text-end">₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td class="text-end fw-bold">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Panel -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-primary text-white rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Payment Details</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="fw-bold">₹<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee</span>
                        <span class="fw-bold">₹<?php echo number_format($delivery_fee, 2); ?></span>
                    </div>
                    <hr class="border-light opacity-50">
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-5 fw-bold">₹<?php echo number_format($grand_total, 2); ?></span>
                    </div>

                    <div class="mb-4">
                        <label class="fw-bold mb-2">Delivery Address</label>
                        <input type="text" class="form-control rounded-pill bg-white text-dark border-0" placeholder="Enter your delivery address" id="delivery_address">
                    </div>

                    <form method="POST">
                        <button type="submit" name="place_order" class="btn btn-light btn-lg w-100 rounded-pill fw-bold text-primary">
                            <i class="fa-solid fa-lock me-2"></i>Place Order
                        </button>
                    </form>

                    <p class="small text-white-50 text-center mt-3 mb-0">
                        <i class="fa-solid fa-shield-halved me-1"></i>Secure & fast delivery in 10 minutes
                    </p>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>