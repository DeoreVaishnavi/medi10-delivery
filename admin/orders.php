<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");
include("../includes/header.php");
include("../includes/admin_navbar.php");

$query = "SELECT orders.*, users.name 
          FROM orders 
          JOIN users ON orders.user_id = users.id 
          ORDER BY orders.id DESC";

$result = mysqli_query($conn, $query);
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <!-- Sidebar -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- Main Content -->
    <div class="admin-content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold text-dark mb-0"><i class="fa-solid fa-clipboard-list text-primary me-2"></i>All Orders</h2>
        </div>

        <div class="card border-0 shadow-sm glass-panel">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Order ID</th>
                                <th class="py-3">Customer</th>
                                <th class="py-3">Total Amount</th>
                                <th class="py-3">Status</th>
                                <th class="px-4 py-3 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { 
                                    $badge_color = 'bg-secondary';
                                    if(strtolower($row['status']) == 'pending') $badge_color = 'bg-warning text-dark';
                                    if(strtolower($row['status']) == 'delivered') $badge_color = 'bg-success';
                                    if(strtolower($row['status']) == 'assigned') $badge_color = 'bg-info text-dark';
                            ?>
                                <tr>
                                    <td class="px-4"><strong>#ORD-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <?php echo $row['name']; ?>
                                        </div>
                                    </td>
                                    <td><span class="text-success fw-bold">₹<?php echo number_format($row['total_amount'], 2); ?></span></td>
                                    <td><span class="badge <?php echo $badge_color; ?> rounded-pill px-3 py-2"><?php echo $row['status']; ?></span></td>
                                    <td class="px-4 text-end">
                                        <a href="order_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="5" class="text-center py-4 text-muted">No orders found.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>