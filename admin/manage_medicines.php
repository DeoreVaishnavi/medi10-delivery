<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");
include("../includes/header.php");
include("../includes/admin_navbar.php");

$query = "SELECT * FROM medicines ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM medicines WHERE id='$id'");
    header("Location: manage_medicines.php");
    exit();
}
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <!-- Sidebar -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- Main Content -->
    <div class="admin-content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold text-dark mb-0"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i>Manage Medicines</h2>
            <a href="add_medicine.php" class="btn btn-primary-custom"><i class="fa-solid fa-plus me-2"></i>Add New Medicine</a>
        </div>

        <div class="card border-0 shadow-sm glass-panel">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Price</th>
                                <th class="py-3">Stock</th>
                                <th class="py-3">Added On</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) { 
                            ?>
                                <tr>
                                    <td class="px-4 text-muted"><strong>#<?php echo $row['id']; ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-muted">
                                                <i class="fa-solid fa-pills"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?php echo $row['name']; ?></h6>
                                                <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;"><?php echo $row['description']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-success fw-bold">₹<?php echo number_format($row['price'], 2); ?></span></td>
                                    <td>
                                        <?php if($row['stock'] > 10) { ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">In Stock (<?php echo $row['stock']; ?>)</span>
                                        <?php } else if($row['stock'] > 0) { ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Low Stock (<?php echo $row['stock']; ?>)</span>
                                        <?php } else { ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Out of Stock</span>
                                        <?php } ?>
                                    </td>
                                    <td class="text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                    <td class="px-4 text-end">
                                        <!-- Note: Edit functionality exists as edit_medicine.php -->
                                        <a href="edit_medicine.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-circle me-2" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="manage_medicines.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this medicine?');" class="btn btn-sm btn-outline-danger rounded-circle" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                } 
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa-solid fa-box-open fa-3x mb-3 text-light"></i><br>No medicines in inventory. <a href="add_medicine.php">Add one now</a>.</td></tr>';
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