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

// Filter by status
$filter = $_GET['status'] ?? 'all';
$where  = "WHERE orders.user_id=$user_id";
if ($filter !== 'all') {
    $filter_safe = mysqli_real_escape_string($conn, $filter);
    $where .= " AND orders.status='$filter_safe'";
}

$result = mysqli_query($conn, "SELECT orders.* FROM orders $where ORDER BY orders.id DESC");
?>

<div class="container py-5" style="min-height: calc(100vh - 200px);">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-box-open me-2 text-primary"></i>My Orders</h2>
            <p class="text-muted mb-0">Track and manage all your medicine orders</p>
        </div>
        <a href="medicines.php" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i>New Order
        </a>
    </div>

    <!-- Status Filter Tabs -->
    <div class="d-flex gap-2 flex-wrap mb-4">
        <?php
        $statuses = ['all' => 'All Orders', 'Pending' => 'Pending', 'Assigned' => 'Assigned', 'Delivered' => 'Delivered'];
        foreach ($statuses as $key => $label):
            $active = ($filter === $key) ? 'btn-primary' : 'btn-outline-secondary';
        ?>
        <a href="my_orders.php?status=<?php echo $key; ?>" class="btn <?php echo $active; ?> rounded-pill px-4"><?php echo $label; ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Orders List -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="d-flex flex-column gap-3">
            <?php while ($row = mysqli_fetch_assoc($result)):
                $badge = 'bg-secondary';
                $icon  = 'fa-box';
                if (strtolower($row['status']) == 'pending')   { $badge = 'bg-warning text-dark'; $icon = 'fa-clock'; }
                if (strtolower($row['status']) == 'delivered') { $badge = 'bg-success'; $icon = 'fa-circle-check'; }
                if (strtolower($row['status']) == 'assigned')  { $badge = 'bg-info text-dark'; $icon = 'fa-motorcycle'; }

                // Get item count
                $item_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM order_items WHERE order_id={$row['id']}"))['c'] ?? 0;
            ?>
            <div class="card border-0 shadow-sm rounded-4 hover-lift" style="transition: transform 0.2s, box-shadow 0.2s;">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-md-1 text-center">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3 d-inline-flex">
                                <i class="fa-solid <?php echo $icon; ?> fa-lg text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold mb-1">#ORD-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></h6>
                            <small class="text-muted">
                                <i class="fa-regular fa-calendar me-1"></i>
                                <?php echo isset($row['order_time']) ? date('M d, Y · h:i A', strtotime($row['order_time'])) : '—'; ?>
                            </small>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-0 text-muted small">Items</p>
                            <p class="mb-0 fw-semibold"><?php echo $item_count; ?> item<?php echo $item_count != 1 ? 's' : ''; ?></p>
                        </div>
                        <div class="col-md-2">
                            <p class="mb-0 text-muted small">Total</p>
                            <p class="mb-0 fw-bold text-success">₹<?php echo number_format($row['total_amount'], 2); ?></p>
                        </div>
                        <div class="col-md-2">
                            <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 fs-7">
                                <?php echo $row['status']; ?>
                            </span>
                        </div>
                        <div class="col-md-1 text-end">
                            <a href="order_tracking.php" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="Track Order">
                                <i class="fa-solid fa-location-dot"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                    <i class="fa-solid fa-box-open fa-3x text-muted opacity-50"></i>
                </div>
                <h5 class="fw-bold mb-2">No orders found</h5>
                <p class="text-muted mb-4">
                    <?php echo $filter !== 'all' ? "No $filter orders yet." : "You haven't placed any orders yet."; ?>
                </p>
                <a href="medicines.php" class="btn btn-primary rounded-pill px-5">
                    <i class="fa-solid fa-capsules me-2"></i>Browse Medicines
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.hover-lift:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important; }
</style>

<?php include("../includes/footer.php"); ?>
