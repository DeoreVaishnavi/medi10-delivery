<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = (strpos($_SERVER['REQUEST_URI'], '/user/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../' : './';
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand font-weight-bold" href="<?php echo $basePath; ?>index.php">
            <i class="fa-solid fa-pills me-2"></i>Medi-10
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNavbar" aria-controls="userNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="userNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>index.php"><i class="fa-solid fa-house me-1"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $basePath; ?>user/medicines.php"><i class="fa-solid fa-capsules me-1"></i> Medicines</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <?php if(isset($_SESSION['user_id'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>user/order_tracking.php"><i class="fa-solid fa-box-open me-1"></i> My Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>user/cart.php">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Cart
                            <span class="badge bg-warning text-dark rounded-pill">0</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle btn btn-outline-light text-white px-3 rounded-pill" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user me-1"></i> Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo $basePath; ?>user/dashboard.php">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $basePath; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>login.php"><i class="fa-solid fa-right-to-bracket me-1"></i> Login</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-light rounded-pill px-4" href="<?php echo $basePath; ?>register.php">Sign Up</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>