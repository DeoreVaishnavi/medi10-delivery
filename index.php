<?php
session_start();
include("config/db.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<!-- Hero Section -->
<section class="hero-section text-center d-flex align-items-center">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1 class="display-3 font-weight-bold mb-4">Fastest Medicine Delivery in <span class="text-success">10 Minutes</span></h1>
        <p class="lead mb-5">Order your prescribed medicines and daily healthcare needs from the comfort of your home. Real-time tracking and 24/7 support.</p>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="user/medicines.php" method="GET" class="d-flex bg-white rounded-pill p-1 shadow">
                    <input type="text" name="q" class="form-control border-0 rounded-pill ps-4" placeholder="Search for 'Paracetamol'..." aria-label="Search">
                    <button class="btn btn-success rounded-pill px-4" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                </form>
            </div>
        </div>
        
        <div class="mt-5">
            <div class="d-inline-flex gap-3">
                <span class="badge bg-light text-dark shadow-sm px-3 py-2 rounded-pill"><i class="fa-solid fa-truck-fast text-success me-1"></i> 10-Min Delivery</span>
                <span class="badge bg-light text-dark shadow-sm px-3 py-2 rounded-pill"><i class="fa-solid fa-shield-halved text-success me-1"></i> 100% Genuine</span>
                <span class="badge bg-light text-dark shadow-sm px-3 py-2 rounded-pill"><i class="fa-solid fa-headset text-success me-1"></i> 24/7 Support</span>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories / Info -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 py-4 glass-panel">
                    <div class="card-body">
                        <i class="fa-solid fa-prescription-bottle-medical fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Prescription Meds</h5>
                        <p class="card-text text-muted">Upload your prescription and we will handle the rest.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 py-4 glass-panel">
                    <div class="card-body">
                        <i class="fa-solid fa-kit-medical fa-3x text-danger mb-3"></i>
                        <h5 class="card-title">First Aid & OTC</h5>
                        <p class="card-text text-muted">Daily essentials and counter medicines delivered fast.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 py-4 glass-panel">
                    <div class="card-body">
                        <i class="fa-solid fa-heart-pulse fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Health Checkups</h5>
                        <p class="card-text text-muted">Book lab tests and get results directly on your phone.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 py-4 glass-panel">
                    <div class="card-body">
                        <i class="fa-solid fa-baby fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Mom & Baby Care</h5>
                        <p class="card-text text-muted">A dedicated section for our precious little ones.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Medicines Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="font-weight-bold"><i class="fa-solid fa-star text-warning me-2"></i>Featured Medicines</h2>
            <a href="user/medicines.php" class="btn btn-outline-primary rounded-pill px-4">View All</a>
        </div>
        
        <div class="row g-4">
            <?php
            $query = "SELECT * FROM medicines ORDER BY id DESC LIMIT 4";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
            ?>
                <div class="col-md-3">
                    <div class="card h-100 product-card">
                        <div class="product-image-container">
                            <?php if ($row['image']) { ?>
                                <img src="uploads/<?php echo $row['image']; ?>" class="img-fluid" style="object-fit: cover; height:100%;" alt="<?php echo $row['name']; ?>">
                            <?php } else { ?>
                                <i class="fa-solid fa-pills text-muted"></i>
                            <?php } ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-dark"><?php echo $row['name']; ?></h5>
                            <p class="card-text text-muted small flex-grow-1"><?php echo substr($row['description'], 0, 50); ?>...</p>
                            <h4 class="text-success fw-bold mb-3">₹<?php echo $row['price']; ?></h4>
                            <a href="user/cart.php?add=<?php echo $row['id']; ?>" class="btn btn-primary-custom mt-auto w-100">
                                <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                            </a>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p class='text-muted'>No medicines available currently.</p>";
            }
            ?>
        </div>
    </div>
</section>

<?php include("includes/footer.php"); ?>
