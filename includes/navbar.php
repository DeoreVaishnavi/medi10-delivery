<?php
session_start();
?>

<div style="background:black; color:white; padding:10px;">

    <a href="../user/medicines.php" style="color:white; margin-right:15px;">Medicines</a>

    <a href="../user/cart.php" style="color:white; margin-right:15px;">Cart</a>

    <a href="../user/order_tracking.php" style="color:white; margin-right:15px;">My Orders</a>

    <?php if(isset($_SESSION['user_id'])) { ?>
        <a href="../logout.php" style="color:red;">Logout</a>
    <?php } ?>

</div>