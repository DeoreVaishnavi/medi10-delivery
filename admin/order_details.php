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

$order_id = $_GET['id'];

/* UPDATE STATUS */
if (isset($_POST['update_status'])) {
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
}

/* GET ORDER ITEMS */
$query = "SELECT order_items.*, medicines.name 
          FROM order_items 
          JOIN medicines ON order_items.medicine_id = medicines.id
          WHERE order_id=$order_id";

$result = mysqli_query($conn, $query);
?>

<h2>Order Details</h2>

<form method="POST">
    <label>Status:</label>
 <select name="status">
    <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>

    <option value="Accepted" <?php if($order['status']=='Accepted') echo 'selected'; ?>>Accepted</option>

    <option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
</select>
    <button name="update_status">Update</button>
</form>

<h3>Items:</h3>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <p><?php echo $row['name']; ?> - Qty: <?php echo $row['quantity']; ?></p>
<?php } ?>