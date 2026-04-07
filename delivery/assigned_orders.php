<?php
include("../config/db.php");

/* TEMP: hardcoded delivery boy id */
$delivery_boy_id = 1;

$query = "SELECT deliveries.*, orders.id AS order_id 
          FROM deliveries 
          JOIN orders ON deliveries.order_id = orders.id
          WHERE delivery_boy_id = $delivery_boy_id";

$result = mysqli_query($conn, $query);
?>

<h2>My Deliveries</h2>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <div style="border:1px solid black; padding:10px; margin:10px;">
        <p>Order ID: <?php echo $row['order_id']; ?></p>
        <p>Status: <?php echo $row['status']; ?></p>

        <a href="update_status.php?id=<?php echo $row['id']; ?>">Update Status</a>
    </div>
<?php } ?>