<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Reliably detect subdirectory depth using PHP (not JS)
$script = $_SERVER['PHP_SELF'] ?? '';
if (
    strpos($script, '/user/')     !== false ||
    strpos($script, '/admin/')    !== false ||
    strpos($script, '/delivery/') !== false
) {
    $css_base = '../';
} else {
    $css_base = './';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medi-10 | Medicine Delivery in 10 Minutes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS (PHP-resolved path — no JS required) -->
    <link rel="stylesheet" href="<?php echo $css_base; ?>assets/css/style.css">
</head>
<body class="bg-light">

