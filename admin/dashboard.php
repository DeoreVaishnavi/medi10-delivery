<?php include("../includes/admin_navbar.php"); ?>
<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<?php
include("../includes/auth.php");
?>

<h2>Welcome <?php echo $_SESSION['user_name']; ?> 👋</h2>

<a href="../logout.php">Logout</a>