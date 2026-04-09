<div class="bg-dark text-white p-3" style="min-width: 250px;">
    <h5 class="text-uppercase text-muted fw-bold mb-4 mt-2 px-3 small">Admin Navigation</h5>
    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a class="nav-link text-white rounded d-flex align-items-center" href="dashboard.php" style="transition:all 0.2s;">
                <i class="fa-solid fa-chart-pie me-3 w-10"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white rounded d-flex align-items-center" href="orders.php" style="transition:all 0.2s;">
                <i class="fa-solid fa-clipboard-list me-3 w-10"></i> Orders
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white rounded d-flex align-items-center" href="manage_medicines.php" style="transition:all 0.2s;">
                <i class="fa-solid fa-pills me-3 w-10"></i> Medicines Inventory
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white rounded d-flex align-items-center" href="add_medicine.php" style="transition:all 0.2s;">
                <i class="fa-solid fa-plus-circle me-3 w-10"></i> Add Medicine
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white rounded d-flex align-items-center" href="assign_delivery.php" style="transition:all 0.2s;">
                <i class="fa-solid fa-motorcycle me-3 w-10"></i> Deliveries
            </a>
        </li>
    </ul>
    
    <div class="mt-auto pt-5 px-3">
        <a href="../logout.php" class="btn btn-outline-danger w-100 rounded-pill"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
    </div>
</div>
<style>
    .nav-link:hover { background: rgba(255,255,255,0.1); }
</style>
