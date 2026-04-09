<?php
include("../config/db.php");

$search = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$query = "SELECT * FROM medicines 
          WHERE stock > 0 
          AND (name LIKE '%$search%' 
          OR description LIKE '%$search%')";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
?>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card h-100 product-card border-0 shadow-sm">
            <div class="product-image-container rounded-top" style="height: 180px;">
                <?php if (!empty($row['image'])) { ?>
                    <img src="../uploads/<?php echo $row['image']; ?>" class="img-fluid rounded-top" style="object-fit: cover; height:100%; width:100%;" alt="<?php echo $row['name']; ?>">
                <?php } else { ?>
                    <i class="fa-solid fa-pills text-muted fa-2x"></i>
                <?php } ?>
            </div>
            
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title text-dark fw-bold mb-0 text-truncate" title="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">In Stock: <?php echo $row['stock']; ?></span>
                </div>
                
                <p class="card-text text-muted small flex-grow-1"><?php echo (strlen($row['description']) > 60) ? substr($row['description'],0,60).'...' : $row['description']; ?></p>
                
                <h4 class="text-success fw-bold mb-3">₹<?php echo number_format($row['price'], 2); ?></h4>

                <form method="POST" action="cart.php" class="mt-auto">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">

                    <div class="input-group mb-3">
                        <span class="input-group-text bg-light text-muted border-end-0">Qty</span>
                        <input type="number" name="quantity" class="form-control border-start-0" value="1" min="1" max="<?php echo $row['stock']; ?>">
                    </div>

                    <button type="submit" name="add_to_cart" class="btn btn-primary-custom w-100">
                        <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php 
    } 
} else {
    echo '<div class="col-12 text-center py-5">
            <i class="fa-solid fa-magnifying-glass ms-muted fa-3x mb-3 text-muted"></i>
            <h4 class="text-muted">No medicines found</h4>
            <p class="text-muted">Try a different search term or browse our categories.</p>
          </div>';
}
?>