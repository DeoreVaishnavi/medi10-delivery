<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");
include("../includes/navbar.php");

$user_id = $_SESSION['user_id'];

// Fetch user info
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user = mysqli_fetch_assoc($user_res);

// Fetch order stats
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE user_id=$user_id"))['c'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE user_id=$user_id AND status='Pending'"))['c'];
$delivered_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders WHERE user_id=$user_id AND status='Delivered'"))['c'];

// Recent orders
$recent = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC LIMIT 5");
?>

<div class="container py-5" style="min-height: calc(100vh - 200px);">

    <!-- Welcome Banner -->
    <div class="card border-0 rounded-4 shadow-sm mb-5 overflow-hidden" style="background: linear-gradient(135deg, #0d6efd 0%, #0a3d9e 100%);">
        <div class="card-body p-4 p-md-5 text-white d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
            <div>
                <p class="text-white-50 mb-1 fw-semibold"><i class="fa-solid fa-hand-wave me-2"></i>Welcome back,</p>
                <h2 class="fw-bold mb-2 display-6"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="text-white-75 mb-0"><i class="fa-solid fa-envelope me-2 opacity-75"></i><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            <div class="text-center">
                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                    <i class="fa-solid fa-user-circle fa-4x text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #6366f1, #4338ca);">
                <div class="card-body p-4 text-white d-flex align-items-center gap-4">
                    <div class="bg-white bg-opacity-20 rounded-3 p-3">
                        <i class="fa-solid fa-box fa-2x"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-white-50 small fw-semibold text-uppercase">Total Orders</p>
                        <h2 class="fw-bold mb-0"><?php echo $total_orders; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <div class="card-body p-4 text-white d-flex align-items-center gap-4">
                    <div class="bg-white bg-opacity-20 rounded-3 p-3">
                        <i class="fa-solid fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-white-50 small fw-semibold text-uppercase">Pending</p>
                        <h2 class="fw-bold mb-0"><?php echo $pending_orders; ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg, #10b981, #059669);">
                <div class="card-body p-4 text-white d-flex align-items-center gap-4">
                    <div class="bg-white bg-opacity-20 rounded-3 p-3">
                        <i class="fa-solid fa-circle-check fa-2x"></i>
                    </div>
                    <div>
                        <p class="mb-0 text-white-50 small fw-semibold text-uppercase">Delivered</p>
                        <h2 class="fw-bold mb-0"><?php echo $delivered_orders; ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-rocket me-2 text-primary"></i>Quick Actions</h5>
                    <div class="d-grid gap-3">
                        <a href="medicines.php" class="btn btn-primary rounded-pill fw-semibold py-2">
                            <i class="fa-solid fa-capsules me-2"></i>Browse Medicines
                        </a>
                        <a href="cart.php" class="btn btn-outline-primary rounded-pill fw-semibold py-2">
                            <i class="fa-solid fa-cart-shopping me-2"></i>View Cart
                        </a>
                        <a href="my_orders.php" class="btn btn-outline-secondary rounded-pill fw-semibold py-2">
                            <i class="fa-solid fa-box-open me-2"></i>My Orders
                        </a>
                        <a href="upload_prescription.php" class="btn btn-outline-success rounded-pill fw-semibold py-2">
                            <i class="fa-solid fa-file-medical me-2"></i>Upload Prescription
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Recent Orders</h5>
                        <a href="my_orders.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                    </div>

                    <?php if (mysqli_num_rows($recent) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="rounded-start py-3 ps-3">Order ID</th>
                                        <th class="py-3">Amount</th>
                                        <th class="py-3">Status</th>
                                        <th class="py-3 rounded-end pe-3 text-end">Track</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($recent)): 
                                        $badge = 'bg-secondary';
                                        if (strtolower($row['status']) == 'pending')   $badge = 'bg-warning text-dark';
                                        if (strtolower($row['status']) == 'delivered') $badge = 'bg-success';
                                        if (strtolower($row['status']) == 'assigned')  $badge = 'bg-info text-dark';
                                    ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">#ORD-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                        <td class="text-success fw-semibold">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td><span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2"><?php echo $row['status']; ?></span></td>
                                        <td class="text-end pe-3">
                                            <a href="order_tracking.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fa-solid fa-location-dot me-1"></i>Track
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-open fa-3x mb-3 opacity-25"></i>
                            <p class="mb-3">You haven't placed any orders yet.</p>
                            <a href="medicines.php" class="btn btn-primary rounded-pill px-4">Shop Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
