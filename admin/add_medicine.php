<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$message = "";

if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);

    if (empty($name) || empty($price) || empty($stock)) {
        $message = "❌ Name, price and stock are required!";
    } else {
        $query = "INSERT INTO medicines (name, description, price, stock)
                  VALUES ('$name', '$desc', '$price', '$stock')";

        if (mysqli_query($conn, $query)) {
            $medicine_id = mysqli_insert_id($conn);

            if (!empty($_FILES['images']['name'][0])) {
                $totalFiles = count($_FILES['images']['name']);

                for ($i = 0; $i < $totalFiles; $i++) {
                    $imageName = $_FILES['images']['name'][$i];
                    $tmpName = $_FILES['images']['tmp_name'][$i];

                    if ($imageName != "") {
                        $newImageName = time() . "_" . $i . "_" . $imageName;
                        $folder = "../uploads/medicines/" . $newImageName;

                        if (move_uploaded_file($tmpName, $folder)) {
                            mysqli_query($conn, "INSERT INTO medicine_images (medicine_id, image_name)
                                                 VALUES ('$medicine_id', '$newImageName')");
                        }
                    }
                }
            }

            $message = "✅ Medicine with images added successfully!";
        } else {
            $message = "❌ Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Medicine</title>
</head>
<body>

<h2>Add Medicine</h2>

<?php if ($message != "") { ?>
    <p><?php echo $message; ?></p>
<?php } ?>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Medicine Name" required><br><br>

    <textarea name="description" placeholder="Description"></textarea><br><br>

    <input type="number" step="0.01" name="price" placeholder="Price" required><br><br>

    <input type="number" name="stock" placeholder="Stock Quantity" required><br><br>

    <label>Upload 2 to 3 Images:</label><br>
    <input type="file" name="images[]" multiple><br><br>

    <button type="submit" name="add">Add Medicine</button>
</form>

</body>
</html>