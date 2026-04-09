<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");
include("../includes/navbar.php");

$user_id = $_SESSION['user_id'];
$result  = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC");
$statuses = ['Pending', 'Assigned', 'Out for Delivery', 'Delivered'];
?>

<div class="container py-5" style="min-height: calc(100vh - 200px);">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5 gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-location-dot me-2 text-primary"></i>Order Tracking</h2>
            <p class="text-muted mb-0">Real-time status of your medicine deliveries</p>
        </div>
        <a href="medicines.php" class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-plus me-2"></i>New Order
        </a>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="d-flex flex-column gap-4">
            <?php while ($row = mysqli_fetch_assoc($result)):
                $status = $row['status'];
                $step   = array_search($status, $statuses);
                if ($step === false) $step = 0;
            ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                        <div>
                            <h6 class="fw-bold mb-1">
                                <i class="fa-solid fa-box me-2 text-primary"></i>
                                Order #ORD-<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?>
                            </h6>
                            <small class="text-muted">
                                <?php echo isset($row['order_time']) ? date('M d, Y · h:i A', strtotime($row['order_time'])) : 'Placed recently'; ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <p class="fw-bold text-success mb-0">₹<?php echo number_format($row['total_amount'], 2); ?></p>
                            <?php
                            $badge = 'bg-secondary';
                            if ($status == 'Pending')          $badge = 'bg-warning text-dark';
                            if ($status == 'Delivered')        $badge = 'bg-success';
                            if ($status == 'Assigned')         $badge = 'bg-info text-dark';
                            if ($status == 'Out for Delivery') $badge = 'bg-primary';
                            ?>
                            <span class="badge <?php echo $badge; ?> rounded-pill px-3 py-2 mt-1"><?php echo $status; ?></span>
                        </div>
                    </div>
                </div>

                <div class="card-body px-4 pt-4 pb-4">
                    <div class="d-flex align-items-start justify-content-between position-relative">
                        <div class="position-absolute" style="top:18px;left:calc(12.5%);right:calc(12.5%);height:4px;background:#e9ecef;z-index:0;">
                            <div style="width:<?php echo ($step / (count($statuses) - 1)) * 100; ?>%;height:100%;background:linear-gradient(90deg,#0d6efd,#198754);border-radius:9999px;"></div>
                        </div>
                        <?php
                        $icons_map = ['fa-clock', 'fa-motorcycle', 'fa-truck-fast', 'fa-circle-check'];
                        foreach ($statuses as $i => $s):
                            $done = ($i <= $step);
                        ?>
                        <div class="text-center flex-fill position-relative" style="z-index:1;">
                            <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-2 shadow-sm"
                                 style="width:40px;height:40px;background:<?php echo $done ? '#0d6efd' : '#dee2e6'; ?>;">
                                <i class="fa-solid <?php echo $icons_map[$i]; ?> <?php echo $done ? 'text-white' : 'text-muted'; ?> fa-sm"></i>
                            </div>
                            <small class="<?php echo $done ? 'fw-bold text-dark' : 'text-muted'; ?> d-block" style="font-size:0.73rem;"><?php echo $s; ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($status == 'Pending'): ?>
                    <div class="alert alert-warning bg-warning bg-opacity-10 border-0 rounded-4 mt-4 mb-0 small">
                        <i class="fa-solid fa-clock me-2"></i>Your order is being processed. A delivery partner will be assigned shortly.
                    </div>
                    <?php elseif ($status == 'Assigned' || $status == 'Out for Delivery'): ?>
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-4 mt-4 mb-0 small">
                        <i class="fa-solid fa-motorcycle me-2"></i>Your order is on the way! Expected delivery in 10 minutes.
                    </div>
                    <?php elseif ($status == 'Delivered'): ?>
                    <div class="alert alert-success bg-success bg-opacity-10 border-0 rounded-4 mt-4 mb-0 small">
                        <i class="fa-solid fa-circle-check me-2"></i>Order delivered successfully! Thank you for choosing Medi-10.
                    </div>
                    <?php endif; ?>
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
                <h5 class="fw-bold mb-2">No Orders Yet</h5>
                <p class="text-muted mb-4">Place your first order to track it here.</p>
                <a href="medicines.php" class="btn btn-primary rounded-pill px-5">
                    <i class="fa-solid fa-capsules me-2"></i>Browse Medicines
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include("../includes/footer.php"); ?>