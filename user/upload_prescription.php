<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include("../config/db.php");
include("../includes/header.php");
include("../includes/navbar.php");

$message      = '';
$message_type = '';

if (isset($_POST['upload_prescription'])) {
    if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['prescription']['name'];
        $ext      = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $message      = 'Only JPG, PNG, and PDF files are allowed.';
            $message_type = 'danger';
        } elseif ($_FILES['prescription']['size'] > 5 * 1024 * 1024) {
            $message      = 'File size must be under 5MB.';
            $message_type = 'danger';
        } else {
            $upload_dir = '../uploads/prescriptions/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $new_name = 'prescription_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['prescription']['tmp_name'], $upload_dir . $new_name)) {
                $message      = 'Prescription uploaded successfully! Our team will review it shortly.';
                $message_type = 'success';
            } else {
                $message      = 'Upload failed. Please try again.';
                $message_type = 'danger';
            }
        }
    } else {
        $message      = 'Please select a file to upload.';
        $message_type = 'danger';
    }
}
?>

<div class="container py-5" style="min-height: calc(100vh - 200px);">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <!-- Page Header -->
            <div class="text-center mb-5">
                <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                    <i class="fa-solid fa-file-medical fa-3x text-primary"></i>
                </div>
                <h2 class="fw-bold mb-2">Upload Prescription</h2>
                <p class="text-muted">Upload your doctor's prescription and we'll prepare your order.</p>
            </div>

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fa-solid <?php echo $message_type == 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?> me-2"></i>
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Upload Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" enctype="multipart/form-data" id="prescriptionForm">

                        <!-- Drag & Drop Zone -->
                        <div id="dropZone" class="border-2 border-dashed rounded-4 p-5 text-center mb-4"
                             style="border: 2px dashed #dee2e6; cursor: pointer; transition: all 0.3s; background: #f8f9fa;">
                            <div id="dropContent">
                                <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted mb-3"></i>
                                <h5 class="fw-bold text-dark mb-1">Drag & Drop your prescription</h5>
                                <p class="text-muted small mb-3">or click to browse files</p>
                                <span class="badge bg-light text-muted border px-3 py-2">JPG · PNG · PDF</span>
                            </div>
                            <div id="previewContent" style="display:none;">
                                <i class="fa-solid fa-file-circle-check fa-3x text-success mb-3"></i>
                                <h6 class="fw-bold text-dark mb-1" id="fileName">—</h6>
                                <small class="text-muted" id="fileSize">—</small>
                            </div>
                            <input type="file" name="prescription" id="fileInput" accept=".jpg,.jpeg,.png,.pdf"
                                   class="position-absolute opacity-0" style="top:0;left:0;width:100%;height:100%;cursor:pointer;">
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Additional Notes (Optional)</label>
                            <textarea name="notes" class="form-control rounded-4" rows="3"
                                      placeholder="Any special instructions for pharmacist..."></textarea>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-info bg-opacity-10 border border-info border-opacity-25 rounded-4 p-3 mb-4">
                            <div class="d-flex gap-3 align-items-start">
                                <i class="fa-solid fa-circle-info text-info mt-1"></i>
                                <div class="small text-muted">
                                    <strong class="text-dark">How it works:</strong> After uploading, our pharmacist team will review your prescription
                                    and prepare your medicine order. You'll be notified within 10 minutes.
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="upload_prescription" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                                <i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Prescription
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back Link -->
            <div class="text-center mt-4">
                <a href="dashboard.php" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
const dropZone  = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const dropContent   = document.getElementById('dropContent');
const previewContent = document.getElementById('previewContent');
const fileNameEl = document.getElementById('fileName');
const fileSizeEl = document.getElementById('fileSize');

function updatePreview(file) {
    if (!file) return;
    dropContent.style.display  = 'none';
    previewContent.style.display = 'block';
    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = (file.size / 1024).toFixed(1) + ' KB';
    dropZone.style.borderColor = '#198754';
    dropZone.style.background  = '#f0fdf4';
}

fileInput.addEventListener('change', () => updatePreview(fileInput.files[0]));

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.borderColor = '#0d6efd';
    dropZone.style.background  = '#f0f4ff';
});
dropZone.addEventListener('dragleave', () => {
    dropZone.style.borderColor = '#dee2e6';
    dropZone.style.background  = '#f8f9fa';
});
dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    const file = e.dataTransfer.files[0];
    if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        updatePreview(file);
    }
});
</script>

<?php include("../includes/footer.php"); ?>
