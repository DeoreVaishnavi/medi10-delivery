<?php
include("config/db.php");

$message = "";
$messageType = "";

if (isset($_POST['register'])) {

    /* 🔹 GET & CLEAN INPUT */
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    /* 🔥 VALIDATIONS */

    // Empty check
    if (empty($name) || empty($email) || empty($password)) {
        $message = "Name, Email, and Password are required!";
        $messageType = "danger";
    }

    // Password length
    elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters!";
        $messageType = "warning";
    }

    // Email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        $messageType = "warning";
    }

    else {
        /* 🔥 CHECK DUPLICATE EMAIL */
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $message = "Email already registered!";
            $messageType = "danger";
        } 
        else {
            /* 🔐 (OPTIONAL BUT GOOD) HASH PASSWORD */
            // $password = password_hash($password, PASSWORD_DEFAULT);

            /* ✅ INSERT USER (DEFAULT ROLE = user) */
            $query = "INSERT INTO users (name, email, password, phone, address, role)
                      VALUES ('$name', '$email', '$password', '$phone', '$address', 'user')";

            if (mysqli_query($conn, $query)) {
                $message = "Registration Successful! You can now login.";
                $messageType = "success";
            } else {
                $message = "Error: " . mysqli_error($conn);
                $messageType = "danger";
            }
        }
    }
}
?>
<?php include("includes/header.php"); ?>
<?php include("includes/navbar.php"); ?>

<div class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="col-md-6">
        <div class="card glass-panel p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="font-weight-bold text-dark"><i class="fa-solid fa-user-plus text-success me-2"></i>Create Account</h2>
                    <p class="text-muted">Join Medi-10 for fast medicine delivery.</p>
                </div>

                <!-- 🔥 MESSAGE DISPLAY -->
                <?php if ($message != "") { ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fa-solid <?php echo $messageType == 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation'; ?> me-2"></i>
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-dark">Full Name *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                            <input type="text" name="name" class="form-control border-start-0" placeholder="John Doe" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-dark">Email Address *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="your@email.com" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Password *</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Minimum 6 characters" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-phone text-muted"></i></span>
                            <input type="text" name="phone" class="form-control border-start-0" placeholder="10-digit number">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-dark">Delivery Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-house-chimney text-muted"></i></span>
                            <textarea name="address" class="form-control border-start-0" placeholder="Complete address for delivery" rows="2"></textarea>
                        </div>
                    </div>

                    <button type="submit" name="register" class="btn btn-primary-custom w-100 mb-3">Register Now</button>
                    
                    <div class="text-center">
                        <p class="mb-0 text-dark">Already have an account? <a href="login.php" class="text-success text-decoration-none fw-bold">Login Here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>