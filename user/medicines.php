<?php 
session_start();
include("../config/db.php"); 
include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Medicines</title>
    <style>
        #imageModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background: rgba(0,0,0,0.85);
            text-align: center;
        }

        #imageModal img {
            margin-top: 4%;
            max-width: 80%;
            max-height: 75%;
            border-radius: 8px;
            box-shadow: 0 0 10px #000;
        }

        #closeBtn {
            position: absolute;
            top: 20px;
            right: 35px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        #prevBtn, #nextBtn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            padding: 10px 15px;
            background: rgba(0,0,0,0.4);
            border-radius: 6px;
            user-select: none;
        }

        #prevBtn {
            left: 20px;
        }

        #nextBtn {
            right: 20px;
        }

        #imageCaption {
            color: white;
            margin-top: 15px;
            font-size: 18px;
        }

        .search-box {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<h2>Available Medicines</h2>

<div class="search-box">
    <input type="text" id="search" placeholder="Search medicine..." onkeyup="searchMedicine()">
</div>

<div id="result"></div>

<!-- IMAGE MODAL -->
<div id="imageModal">
    <span id="closeBtn" onclick="closeImage()">✖</span>
    <span id="prevBtn" onclick="changeImage(-1)">❮</span>
    <img id="modalImg" src="" alt="Medicine Image">
    <span id="nextBtn" onclick="changeImage(1)">❯</span>
    <div id="imageCaption"></div>
</div>

<script>
let currentImages = [];
let currentIndex = 0;

function searchMedicine() {
    var search = document.getElementById("search").value;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "search.php?search=" + encodeURIComponent(search), true);

    xhr.onload = function() {
        document.getElementById("result").innerHTML = this.responseText;
    };

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

</body>
</html>