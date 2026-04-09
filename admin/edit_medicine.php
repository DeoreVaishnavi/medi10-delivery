<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");

$message = "";
$message_type = "";
$medicine = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM medicines WHERE id=$id");
    if (mysqli_num_rows($result) > 0) {
        $medicine = mysqli_fetch_assoc($result);
    } else {
        header("Location: manage_medicines.php");
        exit();
    }
} else {
    header("Location: manage_medicines.php");
    exit();
}

if (isset($_POST['update_medicine'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // If an image is uploaded, we'd process it here. For now we just update text fields.
    $query = "UPDATE medicines SET name='$name', description='$description', price='$price', stock='$stock' WHERE id=$id";
    
    if (mysqli_query($conn, $query)) {
        $message = "Medicine updated successfully!";
        $message_type = "success";
        // Refresh data
        $medicine['name'] = $name;
        $medicine['description'] = $description;
        $medicine['price'] = $price;
        $medicine['stock'] = $stock;
    } else {
        $message = "Error updating medicine: " . mysqli_error($conn);
        $message_type = "danger";
    }
}

include("../includes/header.php");
include("../includes/admin_navbar.php");
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <!-- Sidebar -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- Main Content -->
    <div class="admin-content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Edit Medicine</h2>
            <a href="manage_medicines.php" class="btn btn-outline-secondary rounded-pill"><i class="fa-solid fa-arrow-left me-2"></i>Back to Inventory</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show rounded-4" role="alert">
                        <i class="fa-solid <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation'; ?> me-2"></i>
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm glass-panel rounded-4">
                    <div class="card-body p-5">
                        <form method="POST" action="edit_medicine.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Medicine Name</label>
                                    <input type="text" name="name" class="form-control rounded-pill" value="<?php echo htmlspecialchars($medicine['name']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Price (₹)</label>
                                    <input type="number" step="0.01" name="price" class="form-control rounded-pill" value="<?php echo htmlspecialchars($medicine['price']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Stock Quantity</label>
                                    <input type="number" name="stock" class="form-control rounded-pill" value="<?php echo htmlspecialchars($medicine['stock']); ?>" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Description / Usage</label>
                                <textarea name="description" class="form-control rounded-4" rows="4" required><?php echo htmlspecialchars($medicine['description']); ?></textarea>
                            </div>

                            <!-- Image Upload stub for UI purposes -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Update Image (Optional)</label>
                                <input type="file" name="image" class="form-control rounded-pill" accept="image/*">
                                <small class="text-muted">Leave empty to keep the current image.</small>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="update_medicine" class="btn btn-primary btn-lg rounded-pill fw-bold">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>