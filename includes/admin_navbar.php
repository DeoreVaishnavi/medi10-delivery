<?php
session_start();
?>

<div style="background:darkblue; color:white; padding:10px;">

    <a href="orders.php" style="color:white; margin-right:15px;">Orders</a>

    <a href="add_medicine.php" style="color:white; margin-right:15px;">Add Medicine</a>

    <a href="assign_delivery.php" style="color:white; margin-right:15px;">Assign Delivery</a>

    <?php if(isset($_SESSION['admin_id'])) { ?>
        <a href="../logout.php" style="color:red;">Logout</a>
    <?php } ?>

</div>