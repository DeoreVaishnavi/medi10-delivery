<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php"); exit();
}
include("../config/db.php");

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$order_id) { header("Location: orders.php"); exit(); }

if (isset($_POST['update_status'])) {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
    header("Location: order_details.php?id=$order_id&updated=1"); exit();
}

$order_res = mysqli_query($conn, "SELECT orders.*, users.name as customer_name, users.email as customer_email
                                   FROM orders JOIN users ON orders.user_id = users.id
                                   WHERE orders.id=$order_id");
$order = mysqli_fetch_assoc($order_res);
if (!$order) { header("Location: orders.php"); exit(); }

$items_res = mysqli_query($conn, "SELECT order_items.*, medicines.name as medicine_name, medicines.price
                                   FROM order_items JOIN medicines ON order_items.medicine_id = medicines.id
                                   WHERE order_id=$order_id");

$delivery_res = mysqli_query($conn, "SELECT deliveries.*, users.name as delivery_name
                                      FROM deliveries JOIN users ON deliveries.delivery_boy_id = users.id
                                      WHERE deliveries.order_id=$order_id");
$delivery = ($delivery_res && mysqli_num_rows($delivery_res) > 0) ? mysqli_fetch_assoc($delivery_res) : null;

include("../includes/header.php");
include("../includes/admin_navbar.php");

$status_steps = ['Pending', 'Assigned', 'Out for Delivery', 'Delivered'];
$current_step = array_search($order['status'], $status_steps);
if ($current_step === false) $current_step = 0;

function od_badge($s) {
    $m = ['pending'=>'bg-warning text-dark','assigned'=>'bg-info text-dark','out for delivery'=>'bg-primary','delivered'=>'bg-success','cancelled'=>'bg-danger'];
    return $m[strtolower($s)] ?? 'bg-secondary';
}
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <?php include("../includes/admin_sidebar.php"); ?>

    <div class="admin-content w-100 p-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">
                    <i class="fa-solid fa-file-invoice me-2 text-primary"></i>
                    Order #ORD-<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?>
                </h2>
                <p class="text-muted mb-0">
                    <i class="fa-regular fa-calendar me-1"></i>
                    <?php echo isset($order['order_time']) ? date('M d, Y · h:i A', strtotime($order['order_time'])) : 'N/A'; ?>
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge <?php echo od_badge($order['status']); ?> rounded-pill px-4 py-2 fs-6"><?php echo $order['status']; ?></span>
                <a href="orders.php" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i>Order status updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Progress Timeline -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4 text-muted text-uppercase small"><i class="fa-solid fa-map-pin me-2"></i>Order Progress</h6>
                <div class="d-flex align-items-start justify-content-between position-relative">
                    <div class="position-absolute" style="top:20px;left:calc(12.5%);right:calc(12.5%);height:4px;background:#e9ecef;z-index:0;">
                        <div style="width:<?php echo $current_step > 0 ? ($current_step / (count($status_steps)-1))*100 : 0; ?>%;height:100%;background:linear-gradient(90deg,#0d6efd,#198754);border-radius:9999px;"></div>
                    </div>
                    <?php
                    $step_icons = ['fa-clock','fa-motorcycle','fa-truck-fast','fa-circle-check'];
                    foreach ($status_steps as $i => $s):
                        $done = ($i <= $current_step);
                    ?>
                    <div class="text-center flex-fill position-relative" style="z-index:1;">
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm"
                             style="width:42px;height:42px;background:<?php echo $done ? '#0d6efd' : '#dee2e6'; ?>;">
                            <i class="fa-solid <?php echo $step_icons[$i]; ?> <?php echo $done ? 'text-white' : 'text-muted'; ?> fa-sm"></i>
                        </div>
                        <small class="<?php echo $done ? 'fw-bold text-dark' : 'text-muted'; ?> d-block" style="font-size:0.73rem;"><?php echo $s; ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <!-- Customer -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-muted text-uppercase small"><i class="fa-solid fa-user me-2 text-primary"></i>Customer</h6>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="fa-solid fa-user text-primary fa-lg"></i>
                            </div>
                            <div>
                                <p class="fw-bold mb-0"><?php echo htmlspecialchars($order['customer_name'] ?? 'N/A'); ?></p>
                                <small class="text-muted"><?php echo htmlspecialchars($order['customer_email'] ?? 'N/A'); ?></small>
                            </div>
                        </div>
                        <div class="bg-light rounded-3 p-3 text-center">
                            <p class="mb-0 text-muted small">Order Amount</p>
                            <p class="fw-bold fs-4 text-success mb-0">₹<?php echo number_format($order['total_amount'], 2); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Delivery Partner -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-muted text-uppercase small"><i class="fa-solid fa-motorcycle me-2 text-primary"></i>Delivery Partner</h6>
                        <?php if ($delivery): ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                                <i class="fa-solid fa-motorcycle text-success fa-lg"></i>
                            </div>
                            <div>
                                <p class="fw-bold mb-0"><?php echo htmlspecialchars($delivery['delivery_name']); ?></p>
                                <span class="badge <?php echo od_badge($delivery['status']); ?> rounded-pill px-2 py-1 small"><?php echo $delivery['status']; ?></span>
                            </div>
                        </div>
                        <a href="assign_delivery.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-primary w-100 rounded-pill btn-sm">
                            <i class="fa-solid fa-pen me-2"></i>Reassign Partner
                        </a>
                        <?php else: ?>
                        <div class="text-center py-2">
                            <i class="fa-solid fa-circle-question fa-2x text-muted opacity-50 mb-2"></i>
                            <p class="text-muted small mb-3">No delivery partner assigned yet.</p>
                            <a href="assign_delivery.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary w-100 rounded-pill">
                                <i class="fa-solid fa-motorcycle me-2"></i>Assign Delivery
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Update Status -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 text-muted text-uppercase small"><i class="fa-solid fa-rotate me-2 text-primary"></i>Update Status</h6>
                        <form method="POST">
                            <div class="mb-3">
                                <select name="status" class="form-select rounded-pill">
                                    <?php foreach (['Pending','Accepted','Assigned','Out for Delivery','Delivered','Cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $order['status']==$s?'selected':''; ?>><?php echo $s; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="update_status" class="btn btn-primary w-100 rounded-pill fw-bold">
                                <i class="fa-solid fa-check me-2"></i>Apply Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-muted text-uppercase small"><i class="fa-solid fa-box me-2 text-primary"></i>Order Items</h6>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 ps-3">Medicine</th>
                                        <th class="py-3 text-center">Qty</th>
                                        <th class="py-3 text-end">Unit Price</th>
                                        <th class="py-3 text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $order_total = 0; $has_items = false;
                                    while ($row = mysqli_fetch_assoc($items_res)):
                                        $has_items = true;
                                        $sub = ($row['price'] ?? 0) * $row['quantity'];
                                        $order_total += $sub;
                                    ?>
                                    <tr>
                                        <td class="py-3 ps-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-primary bg-opacity-10 rounded-3 p-2 flex-shrink-0">
                                                    <i class="fa-solid fa-pills text-primary"></i>
                                                </div>
                                                <span class="fw-bold"><?php echo htmlspecialchars($row['medicine_name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-light text-dark border rounded-pill px-3 py-2"><?php echo $row['quantity']; ?></span>
                                        </td>
                                        <td class="py-3 text-end text-muted">₹<?php echo number_format($row['price'] ?? 0, 2); ?></td>
                                        <td class="py-3 text-end fw-bold pe-3">₹<?php echo number_format($sub, 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if (!$has_items): ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">No items found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-semibold py-3 text-muted">Delivery Fee</td>
                                        <td class="text-end py-3 pe-3">₹40.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold fs-5 py-3">Grand Total</td>
                                        <td class="text-end fw-bold fs-5 text-primary py-3 pe-3">
                                            ₹<?php echo number_format($order['total_amount'] ?? ($order_total + 40), 2); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="border-top pt-4 mt-2 d-flex gap-3 flex-wrap">
                            <a href="assign_delivery.php?order_id=<?php echo $order_id; ?>" class="btn btn-outline-primary rounded-pill px-4">
                                <i class="fa-solid fa-motorcycle me-2"></i><?php echo $delivery ? 'Reassign' : 'Assign Delivery'; ?>
                            </a>
                            <a href="orders.php" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa-solid fa-list me-2"></i>All Orders
                            </a>
                            <?php if ($order['status'] != 'Cancelled'): ?>
                            <form method="POST" class="ms-auto">
                                <input type="hidden" name="status" value="Cancelled">
                                <button type="submit" name="update_status" class="btn btn-outline-danger rounded-pill px-4"
                                        onclick="return confirm('Cancel this order?');">
                                    <i class="fa-solid fa-ban me-2"></i>Cancel Order
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>