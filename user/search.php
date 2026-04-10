<?php
include("../config/db.php");

$search = "";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
}

$query = "SELECT * FROM medicines
          WHERE stock > 0
          AND (
                name LIKE '%$search%'
                OR description LIKE '%$search%'
              )
          ORDER BY id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
}

if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {
        $medicine_id = $row['id'];

        $images = [];
        $imgQuery = "SELECT * FROM medicine_images WHERE medicine_id = '$medicine_id'";
        $imgResult = mysqli_query($conn, $imgQuery);

        if ($imgResult && mysqli_num_rows($imgResult) > 0) {
            while ($img = mysqli_fetch_assoc($imgResult)) {
                $images[] = "../uploads/medicines/" . $img['image_name'];
            }
        }
        ?>
        
        <div style="border:1px solid #ccc; padding:15px; margin:15px 0; border-radius:8px;">

            <h3><?php echo $row['name']; ?></h3>

            <p>
                <strong>Description:</strong>
                <?php echo $row['description']; ?>
            </p>

            <p>
                <strong>Price:</strong> ₹<?php echo $row['price']; ?>
            </p>

            <p>
                <strong>Stock:</strong> <?php echo $row['stock']; ?>
            </p>

            <!-- MULTIPLE IMAGES -->
            <div style="margin-bottom:10px;">
                <?php
                if (!empty($images)) {
                    foreach ($images as $singleImage) {
                        $jsImageList = htmlspecialchars(json_encode($images), ENT_QUOTES, 'UTF-8');
                        $jsImageSrc = htmlspecialchars($singleImage, ENT_QUOTES, 'UTF-8');
                        $jsMedicineName = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                        ?>
                        <img 
                            src="<?php echo $singleImage; ?>"
                            alt="Medicine Image"
                            width="100"
                            height="100"
                            style="margin:5px; border:1px solid #ddd; padding:3px; object-fit:cover; cursor:pointer;"
                            onclick='openImage("<?php echo $jsImageSrc; ?>", "<?php echo $jsMedicineName; ?>", <?php echo $jsImageList; ?>)'
                        >
                        <?php
                    }
                } else {
                    echo "<p>No image available</p>";
                }
                ?>
            </div>

            <!-- ADD TO CART -->
            <form method="POST" action="cart.php">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                <input type="hidden" name="price" value="<?php echo $row['price']; ?>">

                <input 
                    type="number" 
                    name="quantity" 
                    value="1" 
                    min="1" 
                    max="<?php echo $row['stock']; ?>"
                    required
                >

                <button type="submit" name="add_to_cart">Add to Cart</button>
            </form>

        </div>

        <?php
    }

} else {
    echo "<p>No medicines found!</p>";
}
?>