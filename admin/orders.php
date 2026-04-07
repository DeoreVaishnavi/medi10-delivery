<?php include("../includes/admin_navbar.php"); ?>
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<?php
include("../config/db.php");

$query = "SELECT orders.*, users.name 
          FROM orders 
          JOIN users ON orders.user_id = users.id 
          ORDER BY orders.id DESC";

$result = mysqli_query($conn, $query);
?>

<h2>All Orders</h2>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <div style="border:1px solid black; padding:10px; margin:10px;">
        <h3>Order ID: <?php echo $row['id']; ?></h3>
        <p>User: <?php echo $row['name']; ?></p>
        <p>Total: ₹<?php echo $row['total_amount']; ?></p>
        <p>Status: <?php echo $row['status']; ?></p>

        <a href="order_details.php?id=<?php echo $row['id']; ?>">View Details</a>
    </div>
<?php } ?>