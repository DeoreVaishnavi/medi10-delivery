<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'delivery') {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");

$delivery_id = $_SESSION['user_id'];

$result = mysqli_query($conn,
    "SELECT deliveries.*, orders.id AS order_id, orders.total_amount, orders.status AS order_status,
            users.name AS customer_name
     FROM deliveries
     JOIN orders ON deliveries.order_id = orders.id
     JOIN users  ON orders.user_id = users.id
     WHERE delivery_boy_id = $delivery_id
     ORDER BY deliveries.id DESC"
);

// Fetch user for sidebar
$user_res = mysqli_query($conn, "SELECT name FROM users WHERE id=$delivery_id");
$user     = mysqli_fetch_assoc($user_res);
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
    <div class="flex-grow-1 p-4" style="background:#f8f9fa;">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-box me-2 text-primary"></i>My Deliveries</h2>
                <p class="text-muted mb-0">All orders assigned to you</p>
            </div>
            <div class="text-muted small"><i class="fa-regular fa-calendar me-1"></i><?php echo date('M d, Y'); ?></div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="py-3">Customer</th>
                                <th class="py-3">Amount</th>
                                <th class="py-3">Status</th>
                                <th class="px-4 py-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)):
                                $badge = 'bg-secondary';
                                if ($row['status'] == 'Assigned')         $badge = 'bg-info text-dark';
                                if ($row['status'] == 'Out for Delivery')  $badge = 'bg-primary';
                                if ($row['status'] == 'Delivered')         $badge = 'bg-success';
                            ?>
                            <tr>
                                <td class="px-4">
                                    <strong>#ORD-<?php echo str_pad($row['order_id'], 5, '0', STR_PAD_LEFT); ?></strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-light rounded-circle p-2 text-primary">
                                            <i class="fa-solid fa-user fa-sm"></i>
                                        </div>
                                        <?php echo htmlspecialchars($row['customer_name']); ?>
                                    </div>
                                </td>
                                <td class="text-success fw-bold">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2">
                                        <?php echo $row['status']; ?>
                                    </span>
                                </td>
                                <td class="px-4 text-end">
                                    <?php if ($row['status'] != 'Delivered'): ?>
                                    <a href="update_status.php?id=<?php echo $row['id']; ?>"
                                       class="btn btn-sm btn-primary rounded-pill px-3">
                                        <i class="fa-solid fa-pen me-1"></i>Update Status
                                    </a>
                                    <?php else: ?>
                                    <span class="text-muted small">
                                        <i class="fa-solid fa-circle-check text-success me-1"></i>Delivered
                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                        <i class="fa-solid fa-motorcycle fa-3x opacity-25"></i>
                    </div>
                    <p class="mb-0 fw-semibold">No deliveries assigned yet.</p>
                    <p class="small">Check back soon — orders will appear here once assigned.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>