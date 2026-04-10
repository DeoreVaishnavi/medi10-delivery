<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_POST['add_to_cart'])) {
    $id = $_POST['id']; $name = $_POST['name']; $price = $_POST['price']; $quantity = (int)$_POST['quantity'];
    $found = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            $stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id"))['stock'];
            if ($_SESSION['cart'][$key]['quantity'] + $quantity <= $stock)
                $_SESSION['cart'][$key]['quantity'] += $quantity;
            $found = true; break;
        }
    }
    if (!$found) {
        $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id"));
        if ($quantity <= $data['stock'])
            $_SESSION['cart'][] = ['id' => $id, 'name' => $name, 'price' => $price, 'quantity' => $quantity];
    }
    header("Location: cart.php"); exit();
}
if (isset($_GET['increase'])) {
    $id = $_GET['increase'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            $stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM medicines WHERE id=$id"))['stock'];
            if ($_SESSION['cart'][$key]['quantity'] < $stock) $_SESSION['cart'][$key]['quantity']++;
            break;
        }
    }
    header("Location: cart.php"); exit();
}
if (isset($_GET['decrease'])) {
    $id = $_GET['decrease'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            $_SESSION['cart'][$key]['quantity']--;
            if ($_SESSION['cart'][$key]['quantity'] <= 0) unset($_SESSION['cart'][$key]);
            break;
        }
    }
    header("Location: cart.php"); exit();
}
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) { unset($_SESSION['cart'][$key]); break; }
    }
    header("Location: cart.php"); exit();
}

include("../includes/header.php");
include("../includes/navbar.php");

$total = 0;
foreach ($_SESSION['cart'] as $item) $total += $item['price'] * $item['quantity'];
$delivery_fee = 40;
$grand_total  = $total + $delivery_fee;
$cart_count   = count($_SESSION['cart']);
?>

<div class="container py-5" style="min-height: calc(100vh - 200px);">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-cart-shopping me-2 text-primary"></i>Your Cart</h2>
            <p class="text-muted mb-0">
                <?php echo $cart_count === 0 ? 'Your cart is empty' : "$cart_count item" . ($cart_count > 1 ? 's' : '') . " in your cart"; ?>
            </p>
        </div>
        <a href="medicines.php" class="btn btn-outline-primary rounded-pill px-4">
            <i class="fa-solid fa-capsules me-2"></i>Continue Shopping
        </a>
    </div>

    <?php if (!empty($_SESSION['cart'])): ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-3">
                <?php foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['price'] * $item['quantity']; ?>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-4 flex-wrap">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 flex-shrink-0">
                                <i class="fa-solid fa-pills fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <span class="text-success fw-semibold">₹<?php echo number_format($item['price'], 2); ?> / unit</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="cart.php?decrease=<?php echo $item['id']; ?>"
                                   class="btn btn-outline-secondary btn-sm rounded-circle" style="width:36px;height:36px;line-height:1.5;">
                                    <i class="fa-solid fa-minus fa-xs"></i>
                                </a>
                                <span class="fw-bold fs-5 px-2"><?php echo $item['quantity']; ?></span>
                                <a href="cart.php?increase=<?php echo $item['id']; ?>"
                                   class="btn btn-outline-primary btn-sm rounded-circle" style="width:36px;height:36px;line-height:1.5;">
                                    <i class="fa-solid fa-plus fa-xs"></i>
                                </a>
                            </div>
                            <div class="text-end" style="min-width:100px;">
                                <p class="mb-0 text-muted small">Subtotal</p>
                                <p class="fw-bold text-dark mb-0">₹<?php echo number_format($subtotal, 2); ?></p>
                            </div>
                            <a href="cart.php?remove=<?php echo $item['id']; ?>"
                               class="btn btn-light text-danger rounded-circle flex-shrink-0"
                               style="width:38px;height:38px;"
                               onclick="return confirm('Remove this item?');">
                                <i class="fa-solid fa-trash-can fa-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top:80px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-receipt me-2 text-primary"></i>Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal</span>
                        <span class="fw-semibold text-dark">₹<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted">
                        <span>Delivery Fee</span>
                        <span class="fw-semibold text-dark">₹<?php echo number_format($delivery_fee, 2); ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold fs-5">Total</span>
                        <span class="fw-bold fs-5 text-success">₹<?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-4 p-3 mb-4 text-center">
                        <i class="fa-solid fa-bolt text-success me-2"></i>
                        <small class="fw-semibold text-success">10-Minute Delivery Guaranteed</small>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">
                        <i class="fa-solid fa-bag-shopping me-2"></i>Proceed to Checkout
                    </a>
                    <p class="small text-muted text-center mt-3 mb-0">
                        <i class="fa-solid fa-shield-halved me-1"></i>Safe &amp; secure checkout
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                <i class="fa-solid fa-cart-shopping fa-3x text-muted opacity-50"></i>
            </div>
            <h5 class="fw-bold mb-2">Your cart is empty</h5>
            <p class="text-muted mb-4">Add medicines to your cart to get started.</p>
            <a href="medicines.php" class="btn btn-primary rounded-pill px-5">
                <i class="fa-solid fa-capsules me-2"></i>Browse Medicines
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>