<?php
session_start();
include("config/db.php");

$error_message = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        /* ROLE BASED REDIRECT */
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }
    } else {
        $error_message = "Invalid email or password!";
    }
}
?>

<?php include("includes/header.php"); ?>
<?php include("includes/navbar.php"); ?>

<div class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="col-md-5">
        <div class="card glass-panel p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h2 class="font-weight-bold text-dark"><i class="fa-solid fa-right-to-bracket text-success me-2"></i>Welcome Back</h2>
                    <p class="text-muted">Login to order medicines securely.</p>
                </div>

                <?php if ($error_message != "") { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $error_message; ?>
                    </div>
                <?php } ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label text-dark">Email address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0" placeholder="Enter your email" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-dark">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                            <input type="password" name="password" class="form-control border-start-0" placeholder="Enter your password" required>
                        </div>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary-custom w-100 mb-3">Login</button>
                    
                    <div class="text-center">
                        <p class="mb-0 text-dark">Don't have an account? <a href="register.php" class="text-success text-decoration-none fw-bold">Register Here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>