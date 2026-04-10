<?php 
session_start();
include("../config/db.php"); 
include("../includes/header.php");
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
let currentImages = [];
let currentIndex = 0;

function searchMedicine() {
    var search = document.getElementById("search").value;
    var resultDiv = document.getElementById("result");
    
    // Check if query was passed via URL (from index.php)
    const urlParams = new URLSearchParams(window.location.search);
    const q = urlParams.get('q');
    if (q && search === "") {
        search = q;
        document.getElementById("search").value = q;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "search.php?search=" + encodeURIComponent(search), true);

    xhr.onload = function() {
        document.getElementById("result").innerHTML = this.responseText;
    }

    xhr.send();
}

function openImage(clickedSrc, medicineName, imageList) {
    currentImages = imageList;
    currentIndex = currentImages.indexOf(clickedSrc);

    if (currentIndex === -1) {
        currentIndex = 0;
    }

    document.getElementById("imageModal").style.display = "block";
    document.getElementById("modalImg").src = currentImages[currentIndex];
    document.getElementById("imageCaption").innerText = medicineName;
}

function closeImage() {
    document.getElementById("imageModal").style.display = "none";
}

function changeImage(step) {
    if (currentImages.length === 0) return;

    currentIndex += step;

    if (currentIndex < 0) {
        currentIndex = currentImages.length - 1;
    }

    if (currentIndex >= currentImages.length) {
        currentIndex = 0;
    }

    document.getElementById("modalImg").src = currentImages[currentIndex];
}

document.getElementById("imageModal").onclick = function(e) {
    if (e.target.id === "imageModal") {
        closeImage();
    }
};

document.addEventListener("keydown", function(e) {
    if (document.getElementById("imageModal").style.display === "block") {
        if (e.key === "Escape") {
            closeImage();
        }
        if (e.key === "ArrowLeft") {
            changeImage(-1);
        }
        if (e.key === "ArrowRight") {
            changeImage(1);
        }
    }
});

window.onload = function() {
    searchMedicine();
};
</script>

<?php include("../includes/footer.php"); ?>