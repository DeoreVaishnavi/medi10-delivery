<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'delivery') {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");

$delivery_id = $_SESSION['user_id'];

// Fetch delivery boy info
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id=$delivery_id");
$user     = mysqli_fetch_assoc($user_res);

// Stats
$total_assigned  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM deliveries WHERE delivery_boy_id=$delivery_id"))['c'];
$delivered_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM deliveries WHERE delivery_boy_id=$delivery_id AND status='Delivered'"))['c'];
$pending_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM deliveries WHERE delivery_boy_id=$delivery_id AND status!='Delivered'"))['c'];

// Recent deliveries
$recent = mysqli_query($conn,
    "SELECT deliveries.*, orders.id AS order_id, orders.total_amount
     FROM deliveries
     JOIN orders ON deliveries.order_id = orders.id
     WHERE delivery_boy_id = $delivery_id
     ORDER BY deliveries.id DESC LIMIT 5"
);
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
        <h5 class="text-uppercase text-muted fw-bold mb-3 px-2 small">Navigation</h5>
        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link text-white rounded d-flex align-items-center bg-primary bg-opacity-20" href="dashboard.php">
                    <i class="fa-solid fa-chart-simple me-3"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white rounded d-flex align-items-center" href="assigned_orders.php" style="transition:all 0.2s;">
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
    <div class="flex-grow-1 p-4" style="background:#f8f9fa;">

        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Delivery Dashboard</h2>
                <p class="text-muted mb-0"><i class="fa-regular fa-calendar me-2"></i><?php echo date('l, F j, Y'); ?></p>
            </div>
            <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded-pill px-4 py-2">
                <i class="fa-solid fa-circle text-success me-2" style="font-size:10px;"></i>
                <small class="fw-semibold text-success">Online</small>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#6366f1,#4338ca);">
                    <div class="card-body p-4 text-white d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3">
                            <i class="fa-solid fa-box fa-xl"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-white-50 small fw-semibold text-uppercase">Total Assigned</p>
                            <h2 class="fw-bold mb-0"><?php echo $total_assigned; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
                    <div class="card-body p-4 text-white d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3">
                            <i class="fa-solid fa-clock fa-xl"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-white-50 small fw-semibold text-uppercase">Pending</p>
                            <h2 class="fw-bold mb-0"><?php echo $pending_count; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100" style="background: linear-gradient(135deg,#10b981,#059669);">
                    <div class="card-body p-4 text-white d-flex align-items-center gap-3">
                        <div class="bg-white bg-opacity-20 rounded-3 p-3">
                            <i class="fa-solid fa-circle-check fa-xl"></i>
                        </div>
                        <div>
                            <p class="mb-0 text-white-50 small fw-semibold text-uppercase">Delivered</p>
                            <h2 class="fw-bold mb-0"><?php echo $delivered_count; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Deliveries -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-check me-2 text-primary"></i>Recent Deliveries</h5>
                    <a href="assigned_orders.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All</a>
                </div>

                <?php if ($recent && mysqli_num_rows($recent) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3">Order ID</th>
                                <th class="py-3">Amount</th>
                                <th class="py-3">Status</th>
                                <th class="py-3 text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($recent)):
                                $badge = 'bg-secondary';
                                if ($row['status'] == 'Assigned')         $badge = 'bg-info text-dark';
                                if ($row['status'] == 'Out for Delivery')  $badge = 'bg-primary';
                                if ($row['status'] == 'Delivered')         $badge = 'bg-success';
                            ?>
                            <tr>
                                <td class="ps-3 fw-bold">#ORD-<?php echo str_pad($row['order_id'], 5, '0', STR_PAD_LEFT); ?></td>
                                <td class="text-success fw-semibold">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td><span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2"><?php echo $row['status']; ?></span></td>
                                <td class="text-end pe-3">
                                    <?php if ($row['status'] != 'Delivered'): ?>
                                    <a href="update_status.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="fa-solid fa-pen me-1"></i>Update
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted small"><i class="fa-solid fa-check me-1"></i>Done</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-motorcycle fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0">No deliveries assigned yet. Check back soon!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
