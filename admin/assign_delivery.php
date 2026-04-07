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

$order_id = $_GET['order_id'];

if (isset($_POST['assign'])) {
    $delivery_boy_id = $_POST['delivery_boy'];

    mysqli_query($conn, "INSERT INTO deliveries (order_id, delivery_boy_id, status)
                         VALUES ($order_id, $delivery_boy_id, 'Assigned')");

    echo "✅ Delivery Assigned!";
}

/* Get delivery boys */
$result = mysqli_query($conn, "SELECT * FROM delivery_boys");
?>

<h2>Assign Delivery</h2>

<form method="POST">
    <select name="delivery_boy">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <option value="<?php echo $row['id']; ?>">
                <?php echo $row['name']; ?>
            </option>
        <?php } ?>
    </select>

    <button name="assign">Assign</button>
</form>