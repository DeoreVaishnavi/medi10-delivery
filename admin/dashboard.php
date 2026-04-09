<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");
include("../includes/admin_navbar.php");
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <!-- Sidebar -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- Main Content -->
    <div class="admin-content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold text-dark mb-0">Dashboard Overview</h2>
            <div class="text-muted"><i class="fa-regular fa-calendar me-2"></i><?php echo date('l, F j, Y'); ?></div>
        </div>

        <?php
        // Fetch real stats
        $users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role='user'"))['count'];
        $medicines_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM medicines"))['count'];
        $orders_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
        $pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status='Pending'"))['count'];
        ?>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card stat-card stat-card-1 h-100 p-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title text-white-50 text-uppercase fw-bold mb-0">Total Orders</h6>
                            <i class="fa-solid fa-box fa-2x text-white-50"></i>
                        </div>
                        <h2 class="display-5 text-white fw-bold mt-3 mb-0"><?php echo $orders_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card stat-card-2 h-100 p-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title text-white-50 text-uppercase fw-bold mb-0">Pending Orders</h6>
                            <i class="fa-solid fa-clock fa-2x text-white-50"></i>
                        </div>
                        <h2 class="display-5 text-white fw-bold mt-3 mb-0"><?php echo $pending_orders; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card stat-card-3 h-100 p-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title text-white-50 text-uppercase fw-bold mb-0">Medicines</h6>
                            <i class="fa-solid fa-pills fa-2x text-white-50"></i>
                        </div>
                        <h2 class="display-5 text-white fw-bold mt-3 mb-0"><?php echo $medicines_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card stat-card-4 h-100 p-3 shadow-sm">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-title text-white-50 text-uppercase fw-bold mb-0">Registered Users</h6>
                            <i class="fa-solid fa-users fa-2x text-white-50"></i>
                        </div>
                        <h2 class="display-5 text-white fw-bold mt-3 mb-0"><?php echo $users_count; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm glass-panel">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">Quick Links</h5>
                        <div class="d-flex gap-3">
                            <a href="orders.php" class="btn btn-outline-primary"><i class="fa-solid fa-box-open me-2"></i>View All Orders</a>
                            <a href="manage_medicines.php" class="btn btn-outline-success"><i class="fa-solid fa-boxes-stacked me-2"></i>Manage Inventory</a>
                            <a href="add_medicine.php" class="btn btn-primary"><i class="fa-solid fa-plus me-2"></i>Add New Product</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>