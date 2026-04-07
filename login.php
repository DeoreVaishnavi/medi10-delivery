<?php

// abc
session_start();
include("config/db.php");

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users 
              WHERE email='$email' AND password='$password'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        /* 🔥 ROLE BASED REDIRECT */
        if ($user['role'] == 'admin') {
            header("Location: admin/orders.php");
        } else {
            header("Location: user/medicines.php");
        }

    } else {
        echo "Invalid Login!";
    }
}
?>

<form method="POST">
    <h2>Login</h2>

    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Password"><br><br>

    <button name="login">Login</button>
</form>

<!-- 🔥 ADD THIS -->
<p>Don't have an account? 
    <a href="register.php">Register Here</a>
</p>