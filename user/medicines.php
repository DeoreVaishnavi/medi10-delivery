<?php 
session_start();
include("../config/db.php"); 
include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicines</title>
</head>
<body>

<h2>Available Medicines</h2>

<!-- 🔍 LIVE SEARCH -->
<input type="text" id="search" placeholder="Search medicine..." onkeyup="searchMedicine()">

<br><br>

<!-- 🔽 RESULTS WILL LOAD HERE -->
<div id="result"></div>

<script>
function searchMedicine() {
    var search = document.getElementById("search").value;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "search.php?search=" + search, true);

    xhr.onload = function() {
        document.getElementById("result").innerHTML = this.responseText;
    }

    xhr.send();
}

/* 🔥 LOAD ALL MEDICINES ON PAGE LOAD */
window.onload = function() {
    searchMedicine();
}
</script>

</body>
</html>