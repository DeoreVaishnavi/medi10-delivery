<?php
session_start();
include("../config/db.php");

/* 🔒 PROTECT PAGE (ONLY ADMIN) */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";

/* ✅ ADD ADMIN LOGIC */
if (isset($_POST['add'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* 🔥 BASIC VALIDATION */
    if (empty($name) || empty($email) || empty($password)) {
        $message = "❌ All fields are required!";
    }
    elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters!";
    }
    else {

        /* 🔥 CHECK DUPLICATE EMAIL */
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $message = "❌ Email already exists!";
        } 
        else {
            /* ✅ INSERT NEW ADMIN */
            $query = "INSERT INTO users (name, email, password, role)
                      VALUES ('$name', '$email', '$password', 'admin')";

            if (mysqli_query($conn, $query)) {
                $message = "✅ New Admin Added Successfully!";
            } else {
                $message = "❌ Error: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Admin</title>
</head>
<body>

<h2>Add New Admin</h2>

<!-- 🔥 MESSAGE DISPLAY -->
<?php if ($message != "") { ?>
    <p><?php echo $message; ?></p>
<?php } ?>

<form method="POST">

    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="add">Add Admin</button>

</form>

<br>
<a href="orders.php">⬅ Back to Orders</a>

</body>
</html>