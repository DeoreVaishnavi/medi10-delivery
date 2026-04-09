<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_medicines.php");
    exit();
}

$medicine = null;
$result = mysqli_query($conn, "SELECT name FROM medicines WHERE id=$id");
if (mysqli_num_rows($result) > 0) {
    $medicine = mysqli_fetch_assoc($result);
} else {
    header("Location: manage_medicines.php");
    exit();
}

if (isset($_POST['confirm_delete'])) {
    mysqli_query($conn, "DELETE FROM medicines WHERE id=$id");
    header("Location: manage_medicines.php?deleted=1");
    exit();
}

include("../includes/header.php");
include("../includes/admin_navbar.php");
?>

<div class="d-flex flex-grow-1" style="min-height: calc(100vh - 56px);">
    <!-- Sidebar -->
    <?php include("../includes/admin_sidebar.php"); ?>

    <!-- Main Content -->
    <div class="admin-content w-100 p-4">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 text-center">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-4">
                                <i class="fa-solid fa-triangle-exclamation fa-4x text-danger"></i>
                            </div>
                        </div>
                        
                        <h3 class="fw-bold mb-3">Delete Medicine?</h3>
                        <p class="text-muted fs-5 mb-4">Are you sure you want to delete <strong class="text-dark">"<?php echo htmlspecialchars($medicine['name']); ?>"</strong> from your inventory? This action cannot be undone.</p>
                        
                        <form method="POST" action="delete_medicine.php?id=<?php echo $id; ?>">
                            <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                                <a href="manage_medicines.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Cancel</a>
                                <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg rounded-pill px-4">Yes, Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>