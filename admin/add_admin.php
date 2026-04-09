<?php
session_start();
include("../config/db.php");

/* 🔒 PROTECT PAGE (ONLY ADMIN) */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$message      = "";
$message_type = "";

/* ✅ ADD ADMIN LOGIC */
if (isset($_POST['add'])) {
    $name     = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        $message      = "All fields are required!";
        $message_type = "danger";
    } elseif (strlen($password) < 6) {
        $message      = "Password must be at least 6 characters!";
        $message_type = "danger";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $message      = "This email is already registered!";
            $message_type = "danger";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query  = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed', 'admin')";
            if (mysqli_query($conn, $query)) {
                $message      = "New Admin Added Successfully!";
                $message_type = "success";
            } else {
                $message      = "Error: " . mysqli_error($conn);
                $message_type = "danger";
            }
        }
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
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1"><i class="fa-solid fa-user-shield me-2 text-primary"></i>Add New Admin</h2>
                <p class="text-muted mb-0">Create an administrator account with full platform access.</p>
            </div>
            <a href="orders.php" class="btn btn-outline-secondary rounded-pill">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">

                <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="fa-solid <?php echo $message_type == 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?> me-2"></i>
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-5">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                                <i class="fa-solid fa-user-plus fa-3x text-primary"></i>
                            </div>
                            <h5 class="fw-bold mb-1">Admin Account Details</h5>
                            <p class="text-muted small">Fill in the details below to create a new admin user.</p>
                        </div>

                        <form method="POST" id="addAdminForm">

                            <!-- Name -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Full Name</label>
                                <div class="input-group rounded-pill overflow-hidden border" style="border-color:#dee2e6!important;">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fa-solid fa-user text-muted"></i>
                                    </span>
                                    <input type="text" name="name" class="form-control border-0 ps-2"
                                           placeholder="e.g. John Doe"
                                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                           required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Email Address</label>
                                <div class="input-group rounded-pill overflow-hidden border" style="border-color:#dee2e6!important;">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fa-solid fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control border-0 ps-2"
                                           placeholder="admin@medi10.com"
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                           required>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Password</label>
                                <div class="input-group rounded-pill overflow-hidden border" style="border-color:#dee2e6!important;">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fa-solid fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" name="password" id="passwordInput" class="form-control border-0 ps-2"
                                           placeholder="Min. 6 characters" required>
                                    <button type="button" class="btn btn-light border-0 px-3" onclick="togglePassword()" id="toggleBtn">
                                        <i class="fa-solid fa-eye text-muted" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="progress rounded-pill" style="height:4px;">
                                        <div class="progress-bar" id="strengthBar" role="progressbar" style="width:0%;transition:width 0.3s;"></div>
                                    </div>
                                    <small id="strengthText" class="text-muted"></small>
                                </div>
                            </div>

                            <!-- Security notice -->
                            <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 p-3 mb-4">
                                <div class="d-flex gap-2 align-items-start">
                                    <i class="fa-solid fa-shield-halved text-warning mt-1"></i>
                                    <small class="text-muted">
                                        <strong class="text-dark">Security Notice:</strong>
                                        Admin accounts have full platform access including orders, medicines, and user management.
                                        Only create accounts for trusted personnel.
                                    </small>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="add" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                                    <i class="fa-solid fa-user-plus me-2"></i>Create Admin Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('passwordInput').addEventListener('input', function () {
    const val = this.value;
    const bar = document.getElementById('strengthBar');
    const txt = document.getElementById('strengthText');
    let strength = 0;
    if (val.length >= 6) strength++;
    if (val.length >= 10) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const colors = ['', 'bg-danger', 'bg-warning', 'bg-info', 'bg-success', 'bg-success'];
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    bar.style.width = (strength * 20) + '%';
    bar.className   = 'progress-bar ' + (colors[strength] || '');
    txt.textContent = val.length > 0 ? labels[strength] : '';
});
</script>

<?php include("../includes/footer.php"); ?>