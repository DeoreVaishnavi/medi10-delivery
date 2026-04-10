<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$basePath = (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? '../' : './';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand font-weight-bold text-success" href="<?php echo $basePath; ?>admin/dashboard.php">
            <i class="fa-solid fa-shield-halved me-2"></i>Medi-10 Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo $basePath; ?>index.php" target="_blank">
                        <i class="fa-solid fa-globe me-1"></i> View Site
                    </a>
                </li>
                <?php if(isset($_SESSION['admin_id'])) { ?>
                    <li class="nav-item dropdown ms-3">
                        <a class="nav-link dropdown-toggle btn btn-outline-success text-white px-3 rounded-pill" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user-shield me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="<?php echo $basePath; ?>admin/dashboard.php">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $basePath; ?>logout.php"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>