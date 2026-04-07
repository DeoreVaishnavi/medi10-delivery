<?php include("../includes/navbar.php"); ?>
<?php
session_start();
include("../config/db.php");

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<h2>My Orders</h2>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <div style="border:1px solid black; padding:10px; margin:10px;">
        <p>Order ID: <?php echo $row['id']; ?></p>
        <p>Status: <?php echo $row['status']; ?></p>
    </div>
<?php } ?>