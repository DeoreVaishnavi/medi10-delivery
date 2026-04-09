<?php 
session_start();
include("../config/db.php"); 
include("../includes/header.php");
include("../includes/navbar.php");
?>

<div class="container py-5 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="font-weight-bold text-dark"><i class="fa-solid fa-capsules text-primary me-2"></i>Available Medicines</h2>
    </div>

    <!-- 🔍 LIVE SEARCH -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-8">
            <div class="input-group shadow-sm border-0 rounded-pill p-1 bg-white">
                <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" id="search" class="form-control border-0 bg-transparent ps-2" placeholder="Search medicines by name or description..." onkeyup="searchMedicine()">
            </div>
        </div>
    </div>

    <!-- 🔽 RESULTS WILL LOAD HERE -->
    <div id="result" class="row g-4">
        <!-- Loader during ajax -->
        <div class="text-center w-100 placeholder-wave" id="loader" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<script>
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
        if(this.status == 200) {
            resultDiv.innerHTML = this.responseText;
        }
    }

    xhr.send();
}

/* 🔥 LOAD ALL MEDICINES ON PAGE LOAD */
window.onload = function() {
    searchMedicine();
}
</script>

<?php include("../includes/footer.php"); ?>