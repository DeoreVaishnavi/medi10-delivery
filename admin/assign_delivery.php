<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}
include("../config/db.php");

$order_id     = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_POST['order_id']) ? (int)$_POST['order_id'] : null);
$message      = "";
$message_type = "";

if (!$order_id) { header("Location: orders.php"); exit(); }

if (isset($_POST['assign'])) {
    $delivery_boy_id = (int)$_POST['delivery_boy'];
    $check = mysqli_query($conn, "SELECT * FROM deliveries WHERE order_id=$order_id");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE deliveries SET delivery_boy_id=$delivery_boy_id, status='Assigned' WHERE order_id=$order_id");
    } else {
        mysqli_query($conn, "INSERT INTO deliveries (order_id, delivery_boy_id, status) VALUES ($order_id, $delivery_boy_id, 'Assigned')");
    }
    $res = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'delivery_id'");
    if (mysqli_num_rows($res) > 0) {
        mysqli_query($conn, "UPDATE orders SET delivery_id=$delivery_boy_id, status='Assigned' WHERE id=$order_id");
    } else {
        mysqli_query($conn, "UPDATE orders SET status='Assigned' WHERE id=$order_id");
    }
    $message = "Delivery partner assigned successfully!";
    $message_type = "success";
}

$order_res = mysqli_query($conn, "SELECT orders.*, users.name as customer_name, users.email as customer_email
                                   FROM orders JOIN users ON orders.user_id = users.id WHERE orders.id=$order_id");
$order = ($order_res && mysqli_num_rows($order_res) > 0) ? mysqli_fetch_assoc($order_res) : null;
if (!$order) { header("Location: orders.php"); exit(); }

$items_res = mysqli_query($conn, "SELECT order_items.quantity, medicines.name as medicine_name
                                   FROM order_items JOIN medicines ON order_items.medicine_id = medicines.id
                                   WHERE order_id=$order_id");

$res = mysqli_query($conn, "SHOW TABLES LIKE 'delivery_boys'");
$delivery_boys = (mysqli_num_rows($res) > 0)
    ? mysqli_query($conn, "SELECT * FROM delivery_boys")
    : mysqli_query($conn, "SELECT id, name FROM users WHERE role='delivery'");

$current_assign   = mysqli_query($conn, "SELECT deliveries.*, users.name as boy_name
                                          FROM deliveries JOIN users ON deliveries.delivery_boy_id = users.id
                                          WHERE order_id=$order_id");
$current_assigned = ($current_assign && mysqli_num_rows($current_assign) > 0) ? mysqli_fetch_assoc($current_assign) : null;
$current_boy_id   = $current_assigned['delivery_boy_id'] ?? '';

include("../includes/header.php");
include("../includes/admin_navbar.php");
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <?php include("../includes/admin_sidebar.php"); ?>

    <div class="admin-content w-100 p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-motorcycle me-2 text-primary"></i>Assign Delivery</h2>
                <p class="text-muted mb-0">Order #ORD-<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?></p>
            </div>
            <a href="orders.php" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show rounded-4 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-5">
                <!-- Order Summary -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-muted text-uppercase small"><i class="fa-solid fa-file-invoice me-2 text-primary"></i>Order Summary</h6>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:50px;height:50px;">
                                <i class="fa-solid fa-user text-primary fa-lg"></i>
                            </div>
                            <div>
                                <p class="fw-bold mb-0"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                            </div>
                        </div>
                        <div class="row g-3 text-center">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <p class="mb-0 text-muted small">Amount</p>
                                    <p class="fw-bold text-success mb-0">₹<?php echo number_format($order['total_amount'], 2); ?></p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3">
                                    <p class="mb-0 text-muted small">Status</p>
                                    <?php
                                    $b = 'bg-secondary';
                                    if ($order['status']=='Pending')  $b='bg-warning text-dark';
                                    if ($order['status']=='Assigned') $b='bg-info text-dark';
                                    if ($order['status']=='Delivered')$b='bg-success';
                                    ?>
                                    <span class="badge <?php echo $b; ?> rounded-pill px-3 py-1"><?php echo $order['status']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <?php if ($items_res && mysqli_num_rows($items_res) > 0): ?>
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-muted text-uppercase small"><i class="fa-solid fa-box me-2 text-primary"></i>Items to Deliver</h6>
                        <div class="d-flex flex-column gap-2">
                            <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                            <div class="d-flex align-items-center gap-3 bg-light rounded-3 p-2">
                                <div class="bg-primary bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                                    <i class="fa-solid fa-pills text-primary fa-sm"></i>
                                </div>
                                <p class="fw-semibold mb-0 flex-grow-1"><?php echo htmlspecialchars($item['medicine_name']); ?></p>
                                <span class="badge bg-white text-dark border rounded-pill px-2">×<?php echo $item['quantity']; ?></span>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Current Assignment -->
                <?php if ($current_assigned): ?>
                <div class="card border-0 rounded-4" style="background:linear-gradient(135deg,#10b981,#059669);">
                    <div class="card-body p-4 text-white">
                        <h6 class="fw-bold mb-3 text-uppercase small opacity-75"><i class="fa-solid fa-check-circle me-2"></i>Currently Assigned</h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                <i class="fa-solid fa-motorcycle text-white fa-lg"></i>
                            </div>
                            <div>
                                <p class="fw-bold mb-0"><?php echo htmlspecialchars($current_assigned['boy_name']); ?></p>
                                <small class="text-white-50">Status: <?php echo $current_assigned['status']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-5">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                                <i class="fa-solid fa-motorcycle fa-3x text-primary"></i>
                            </div>
                            <h4 class="fw-bold mb-1"><?php echo $current_assigned ? 'Reassign Delivery Partner' : 'Assign Delivery Partner'; ?></h4>
                            <p class="text-muted">Select a delivery partner for this order</p>
                        </div>

                        <?php if ($delivery_boys && mysqli_num_rows($delivery_boys) > 0): ?>
                        <form method="POST" action="assign_delivery.php?order_id=<?php echo $order_id; ?>">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="fa-solid fa-person-biking me-2 text-muted"></i>Available Partners</label>
                                <div class="d-flex flex-column gap-3">
                                    <?php
                                    mysqli_data_seek($delivery_boys, 0);
                                    while ($row = mysqli_fetch_assoc($delivery_boys)):
                                        $is_current = ($current_boy_id == $row['id']);
                                    ?>
                                    <label class="partner-card d-flex align-items-center gap-3 p-3 rounded-3 border"
                                           style="cursor:pointer;transition:all 0.2s;" for="partner_<?php echo $row['id']; ?>">
                                        <input type="radio" name="delivery_boy" id="partner_<?php echo $row['id']; ?>"
                                               value="<?php echo $row['id']; ?>"
                                               <?php echo $is_current ? 'checked' : ''; ?>
                                               class="form-check-input flex-shrink-0 mt-0" required>
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                                            <i class="fa-solid fa-motorcycle text-primary fa-sm"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="fw-bold mb-0"><?php echo htmlspecialchars($row['name']); ?></p>
                                            <small class="text-muted">Delivery Partner #<?php echo $row['id']; ?></small>
                                        </div>
                                        <?php if ($is_current): ?>
                                        <span class="badge bg-success rounded-pill px-3">Current</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <div class="d-grid gap-3">
                                <button type="submit" name="assign" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Confirm Assignment
                                </button>
                                <a href="order_details.php?id=<?php echo $order_id; ?>" class="btn btn-outline-secondary rounded-pill">
                                    <i class="fa-solid fa-file-invoice me-2"></i>View Order Details
                                </a>
                            </div>
                        </form>

                        <?php else: ?>
                        <div class="text-center py-4">
                            <div class="bg-warning bg-opacity-10 rounded-4 p-5">
                                <i class="fa-solid fa-triangle-exclamation fa-3x text-warning mb-3"></i>
                                <h5 class="fw-bold mb-2">No Delivery Partners Available</h5>
                                <p class="text-muted mb-4">Add users with the <strong>delivery</strong> role first.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.partner-card:has(input:checked) { border-color:#0d6efd!important; background:rgba(13,110,253,0.05); }
.partner-card:hover { border-color:#0d6efd!important; background:rgba(13,110,253,0.03); }
</style>

<?php include("../includes/footer.php"); ?>