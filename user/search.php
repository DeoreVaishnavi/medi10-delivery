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

while ($row = mysqli_fetch_assoc($result)) {
?>

<div style="border:1px solid black; padding:10px; margin:10px;">
    
    <h3><?php echo $row['name']; ?></h3>
    <p>Price: ₹<?php echo $row['price']; ?></p>
    <p>Stock: <?php echo $row['stock']; ?></p>

    <form method="POST" action="cart.php">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
        <input type="hidden" name="price" value="<?php echo $row['price']; ?>">

        <input type="number" name="quantity" value="1" min="1" max="<?php echo $row['stock']; ?>">

        <button type="submit" name="add_to_cart">Add to Cart</button>
    </form>

</div>

<?php } ?>