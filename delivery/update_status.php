<?php
include("../config/db.php");

$id = $_GET['id'];

if (isset($_POST['update'])) {
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE deliveries SET status='$status' WHERE id=$id");

    /* 🔥 UPDATE MAIN ORDER */
    if ($status == "Delivered") {
        mysqli_query($conn, "UPDATE orders 
                             SET status='Delivered' 
                             WHERE id = (SELECT order_id FROM deliveries WHERE id=$id)");
    }

    echo "✅ Status Updated!";
}
?>

<h2>Update Delivery Status</h2>

<form method="POST">
    <select name="status">
        <option>Assigned</option>
        <option>Out for Delivery</option>
        <option>Delivered</option>
    </select>

    <button name="update">Update</button>
</form>