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

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $query = "INSERT INTO medicines (name, description, price, stock)
              VALUES ('$name', '$desc', '$price', '$stock')";

    if (mysqli_query($conn, $query)) {
        echo "Medicine Added Successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<h2>Add Medicine</h2>

<form method="POST">
    <input type="text" name="name" placeholder="Medicine Name" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <input type="number" name="price" placeholder="Price" required><br><br>
    <input type="number" name="stock" placeholder="Stock Quantity" required><br><br>
    <button type="submit" name="add">Add Medicine</button>
</form>

