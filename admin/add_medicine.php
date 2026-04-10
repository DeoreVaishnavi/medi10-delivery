<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");
include("../includes/header.php");
include("../includes/admin_navbar.php");

$message = "";

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $query = "INSERT INTO medicines (name, description, price, stock)
              VALUES ('$name', '$desc', '$price', '$stock')";

    if (mysqli_query($conn, $query)) {
        $message = "Medicine Added Successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <!-- Sidebar -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- Main Content -->
    <div class="admin-content w-100 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold text-dark mb-0"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Add Medicine</h2>
            <a href="manage_medicines.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Back to Inventory</a>
        </div>

        <div class="card border-0 shadow-sm glass-panel col-lg-8">
            <div class="card-body p-4">
                <?php if ($message) { ?>
                    <div class="alert alert-<?php echo strpos($message, 'Error') !== false ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Medicine Name</label>
                        <input type="text" name="name" class="form-control" placeholder="E.g., Paracetamol" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Brief description of the medicine..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Price (₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted">₹</span>
                                <input type="number" name="price" step="0.01" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark fw-bold">Stock Quantity</label>
                            <input type="number" name="stock" class="form-control" placeholder="100" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="add" class="btn btn-primary-custom w-100 mt-3">
                        <i class="fa-solid fa-save me-2"></i>Save Medicine
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
