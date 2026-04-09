<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'delivery') {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$id) {
    header("Location: assigned_orders.php");
    exit();
}

// Fetch delivery record
$delivery_res = mysqli_query($conn, "SELECT deliveries.*, orders.id AS order_id, orders.total_amount,
                                            users.name AS customer_name
                                     FROM deliveries
                                     JOIN orders ON deliveries.order_id = orders.id
                                     JOIN users  ON orders.user_id = users.id
                                     WHERE deliveries.id = $id");
if (mysqli_num_rows($delivery_res) == 0) {
    header("Location: assigned_orders.php");
    exit();
}
$delivery = mysqli_fetch_assoc($delivery_res);

$message      = '';
$message_type = '';

if (isset($_POST['update'])) {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE deliveries SET status='$status' WHERE id=$id");

    if ($status == 'Delivered') {
        mysqli_query($conn, "UPDATE orders SET status='Delivered'
                             WHERE id = (SELECT order_id FROM deliveries WHERE id=$id)");
    } elseif ($status == 'Out for Delivery') {
        mysqli_query($conn, "UPDATE orders SET status='Out for Delivery'
                             WHERE id = (SELECT order_id FROM deliveries WHERE id=$id)");
    }

    $message      = "Status updated to \"$status\" successfully!";
    $message_type = 'success';
    $delivery['status'] = $status;
}

// Fetch delivery boy name for sidebar
$user_res = mysqli_query($conn, "SELECT name FROM users WHERE id={$_SESSION['user_id']}");
$user     = mysqli_fetch_assoc($user_res);

$statuses = ['Assigned', 'Out for Delivery', 'Delivered'];
?>

<div class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->
    <div class="bg-dark text-white p-3 flex-shrink-0" style="min-width:240px;">
        <div class="text-center py-3 mb-3 border-bottom border-secondary">
            <div class="bg-primary bg-opacity-20 rounded-circle d-inline-flex p-3 mb-2">
                <i class="fa-solid fa-motorcycle fa-2x text-primary"></i>
            </div>
            <h6 class="fw-bold mb-0 text-white"><?php echo htmlspecialchars($user['name']); ?></h6>
            <small class="text-muted">Delivery Partner</small>
        </div>
        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link text-white rounded d-flex align-items-center" href="dashboard.php" style="transition:all 0.2s;">
                    <i class="fa-solid fa-chart-simple me-3"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white rounded d-flex align-items-center bg-primary bg-opacity-20" href="assigned_orders.php">
                    <i class="fa-solid fa-box me-3"></i> My Deliveries
                </a>
            </li>
        </ul>
        <div class="mt-auto pt-4 px-2">
            <a href="../logout.php" class="btn btn-outline-danger w-100 rounded-pill">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
            </a>
        </div>
        <style>.nav-link:hover { background: rgba(255,255,255,0.1); }</style>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4 d-flex align-items-start justify-content-center" style="background:#f8f9fa;">
        <div class="w-100" style="max-width:600px;">

            <!-- Back -->
            <a href="assigned_orders.php" class="btn btn-outline-secondary rounded-pill mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Deliveries
            </a>

            <!-- Alert -->
            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i><?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Order Info Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="fa-solid fa-box fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">#ORD-<?php echo str_pad($delivery['order_id'], 5, '0', STR_PAD_LEFT); ?></h5>
                            <small class="text-muted">
                                <i class="fa-solid fa-user me-1"></i><?php echo htmlspecialchars($delivery['customer_name']); ?>
                            </small>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="mb-0 text-muted small">Order Amount</p>
                            <p class="mb-0 fw-bold text-success fs-5">₹<?php echo number_format($delivery['total_amount'], 2); ?></p>
                        </div>
                        <div class="col-6">
                            <p class="mb-0 text-muted small">Current Status</p>
                            <?php
                            $b = 'bg-secondary';
                            if ($delivery['status'] == 'Assigned')        $b = 'bg-info text-dark';
                            if ($delivery['status'] == 'Out for Delivery') $b = 'bg-primary';
                            if ($delivery['status'] == 'Delivered')        $b = 'bg-success';
                            ?>
                            <span class="badge <?php echo $b; ?> rounded-pill px-3 py-2 mt-1"><?php echo $delivery['status']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Status Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fa-solid fa-pen-to-square fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Update Delivery Status</h5>
                        <p class="text-muted small mt-1">Select the current status of this delivery</p>
                    </div>

                    <?php if ($delivery['status'] == 'Delivered'): ?>
                    <div class="text-center py-3">
                        <div class="bg-success bg-opacity-10 rounded-4 p-4">
                            <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                            <h6 class="fw-bold text-success mb-1">Order Successfully Delivered!</h6>
                            <p class="text-muted small mb-0">This order has been marked as delivered. No further updates needed.</p>
                        </div>
                    </div>

                    <?php else: ?>
                    <!-- Status Progress -->
                    <div class="d-flex align-items-center justify-content-between mb-4 position-relative">
                        <div class="position-absolute" style="top:16px;left:16%;right:16%;height:4px;background:#e9ecef;z-index:0;">
                            <?php $progress = array_search($delivery['status'], $statuses); ?>
                            <div style="width:<?php echo ($progress / (count($statuses)-1)) * 100; ?>%;height:100%;background:#0d6efd;border-radius:9999px;"></div>
                        </div>
                        <?php foreach ($statuses as $i => $s):
                            $done = ($i <= array_search($delivery['status'], $statuses));
                            $icons_map = ['fa-motorcycle', 'fa-truck-fast', 'fa-circle-check'];
                        ?>
                        <div class="text-center flex-fill position-relative" style="z-index:1;">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-1 shadow-sm"
                                 style="width:36px;height:36px;background:<?php echo $done ? '#0d6efd' : '#dee2e6'; ?>;">
                                <i class="fa-solid <?php echo $icons_map[$i]; ?> <?php echo $done ? 'text-white' : 'text-muted'; ?> fa-xs"></i>
                            </div>
                            <small class="<?php echo $done ? 'fw-bold text-dark' : 'text-muted'; ?>" style="font-size:0.7rem;"><?php echo $s; ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-bold">New Status</label>
                            <select name="status" class="form-select form-select-lg rounded-pill" required>
                                <?php foreach ($statuses as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $delivery['status'] == $s ? 'selected' : ''; ?>>
                                    <?php echo $s; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="update" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="fa-solid fa-paper-plane me-2"></i>Update Status
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>