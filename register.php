<?php
include("config/db.php");

$message = "";

if (isset($_POST['register'])) {

    /* 🔹 GET & CLEAN INPUT */
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    /* 🔥 VALIDATIONS */

    // Empty check
    if (empty($name) || empty($email) || empty($password)) {
        $message = "❌ Name, Email, and Password are required!";
    }

    // Password length
    elseif (strlen($password) < 6) {
        $message = "❌ Password must be at least 6 characters!";
    }

    // Email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email format!";
    }

    else {

        /* 🔥 CHECK DUPLICATE EMAIL */
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

        if (mysqli_num_rows($check) > 0) {
            $message = "❌ Email already registered!";
        } 
        else {

            /* 🔐 (OPTIONAL BUT GOOD) HASH PASSWORD */
            // $password = password_hash($password, PASSWORD_DEFAULT);

            /* ✅ INSERT USER (DEFAULT ROLE = user) */
            $query = "INSERT INTO users (name, email, password, phone, address, role)
                      VALUES ('$name', '$email', '$password', '$phone', '$address', 'user')";

            if (mysqli_query($conn, $query)) {
                $message = "✅ Registration Successful!";
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
    <title>User Registration</title>
</head>
<body>

<h2>Register</h2>

<!-- 🔥 MESSAGE DISPLAY -->
<?php if ($message != "") { ?>
    <p><?php echo $message; ?></p>
<?php } ?>

<form method="POST">

    <input type="text" name="name" placeholder="Name" required><br><br>

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <input type="text" name="phone" placeholder="Phone"><br><br>

    <textarea name="address" placeholder="Address"></textarea><br><br>

    <button type="submit" name="register">Register</button>

</form>

<br>
<a href="login.php">Already have an account? Login</a>

</body>
</html>