<?php
  include('../include/doctor_session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <?php include('../include/title.php'); ?>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="../assets/modules/jqvmap/dist/jqvmap.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons.min.css">
  <link rel="stylesheet" href="../assets/modules/weather-icon/css/weather-icons-wind.min.css">
  <link rel="stylesheet" href="../assets/modules/summernote/summernote-bs4.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">
  
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  
  <style>
    /* Enhanced styles for bigger extract box */
    .extract-text-container {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .extract-text-container label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 15px;
        display: block;
        font-size: 16px;
    }
    
    #extractedText {
        min-height: 400px !important;
        height: 400px;
        resize: vertical;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        line-height: 1.6;
        border: 2px solid #ced4da;
        border-radius: 6px;
        padding: 15px;
        background: #ffffff;
        transition: border-color 0.3s ease;
    }
    
    #extractedText:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }
    
    .extract-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .text-stats {
        color: #6c757d;
        font-size: 14px;
    }
    
    .resize-buttons {
        display: flex;
        gap: 5px;
    }
    
    .resize-btn {
        padding: 5px 10px;
        font-size: 12px;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .resize-btn:hover {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    
    .ocr-processing {
        display: none;
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 15px 0;
    }
    
    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }

    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: #f8f9fa;
    }

    .file-upload-area:hover {
        border-color: #007bff;
        background: #e7f3ff;
    }

    .file-upload-area.drag-over {
        border-color: #007bff;
        background: #e7f3ff;
    }

    .file-upload-area.file-selected {
        border-color: #28a745;
        background: #d4edda;
    }

    .file-info {
        display: none;
        margin-top: 20px;
        padding: 15px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .image-preview {
        max-width: 100%;
        max-height: 400px;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 768px) {
        #extractedText {
            min-height: 300px !important;
            height: 300px;
            font-size: 13px;
        }
        
        .extract-controls {
            flex-direction: column;
            align-items: flex-start;
        }
    }
  </style>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>
      <?php include('../include/header.php'); ?>
      <?php include('../include/sidebar.php'); ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Laboratory Result Upload</h1>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Pet Selection -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="pet_id" class="col-form-label">Select Pet</label>
                        <select name="pet_id" class="form-control" id="pet_id" required>
                            <option value="" disabled selected>Choose Pet...</option>
                            <?php 
                                $doctor_id = $_SESSION['doctor_id'];
                                $query = mysqli_query($con, "SELECT p.id, p.pet_name, p.species, p.breed, p.age, p.weight, p.sex, 
                                                            CONCAT(pt.firstname, ' ', pt.lastname) as owner_name
                                                            FROM pet p
                                                            JOIN patient pt ON p.patient_id = pt.id
                                                            WHERE p.active = 1 AND pt.active = 1
                                                            ORDER BY p.pet_name ASC");
                                while($row = mysqli_fetch_array($query)){
                            ?>
                            <option value="<?php echo $row['id']; ?>" 
                                    data-species="<?php echo $row['species']; ?>" 
                                    data-breed="<?php echo $row['breed']; ?>" 
                                    data-age="<?php echo $row['age']; ?>" 
                                    data-weight="<?php echo $row['weight']; ?>" 
                                    data-sex="<?php echo $row['sex']; ?>" 
                                    data-owner="<?php echo $row['owner_name']; ?>">
                                <?php echo $row['pet_name']; ?> - <?php echo $row['owner_name']; ?>
                            </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                
                <!-- Test Type -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="test_type" class="col-form-label">Test Type</label>
                        <select name="test_type" class="form-control" id="test_type" required>
                            <option value="" disabled selected>Choose Test Type...</option>
                            <option value="blood_work">Blood Work</option>
                            <option value="urine_test">Urine Test</option>
                            <option value="chemistry_panel">Chemistry Panel</option>
                            <option value="complete_blood_count">Complete Blood Count</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                
                <!-- Test Date -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="test_date" class="col-form-label">Test Date</label>
                        <input type="date" name="test_date" class="form-control" id="test_date" required>
                    </div>
                </div>
                
                <!-- Pet Details Display -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-form-label">Pet Details</label>
                        <div class="card" style="background: #f8f9fa; border: 1px solid #dee2e6;">
                            <div class="card-body p-3">
                                <div id="petDetails" class="text-muted">
                                    <small>Select a pet to view details</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- File Upload -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="col-form-label">Upload Lab Result</label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                            <h5 class="mb-3">Click to upload or drag and drop</h5>
                            <p class="text-muted mb-4">JPG, PNG, PDF (Max: 10MB)</p>
                            <button type="button" class="btn btn-primary" id="browseBtn">
                                <i class="fas fa-folder-open"></i> Browse Files
                            </button>
                            <input type="file" class="d-none" name="lab_file" id="labFile" accept=".jpg,.jpeg,.png,.pdf" required>
                            
                            <div class="file-info" id="fileInfo">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-check fa-2x text-success mb-2"></i>
                                        <h6 class="text-success mb-1" id="fileName">File Selected</h6>
                                        <p class="text-muted small mb-0" id="fileSize">File size</p>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" id="removeFileBtn">
                                        <i class="fas fa-times"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Image Preview and OCR -->
                <div class="col-md-12" id="ocrSection" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h4>Image Preview & Text Recognition (PyTesseract)</h4>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img id="imagePreview" class="image-preview" style="display: none;" />
                                <div class="mt-3" id="ocrButtonContainer" style="display: none;">
                                    <button type="button" class="btn btn-info" id="startOcrBtn">
                                        <i class="fas fa-magic"></i> Extract Text with PyTesseract
                                    </button>
                                </div>
                            </div>
                            
                            <!-- OCR Processing Status -->
                            <div class="ocr-processing" id="ocrProcessing">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="sr-only">Processing...</span>
                                </div>
                                <h6 class="mb-2">Processing with PyTesseract...</h6>
                                <p class="text-muted mb-0">Advanced image preprocessing and OCR analysis in progress</p>
                            </div>
                            
                            <!-- Enhanced Extract Text Container -->
                            <div class="extract-text-container">
                                <div class="extract-controls">
                                    <label for="extractedText">
                                        <i class="fas fa-edit"></i> Extracted Text (Editable)
                                    </label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-stats">
                                            <span id="charCount">0</span> characters | 
                                            <span id="wordCount">0</span> words | 
                                            <span id="lineCount">0</span> lines
                                        </div>
                                        <div class="resize-buttons">
                                            <button type="button" class="resize-btn" onclick="resizeTextarea('smaller')">
                                                <i class="fas fa-compress-alt"></i> Smaller
                                            </button>
                                            <button type="button" class="resize-btn" onclick="resizeTextarea('bigger')">
                                                <i class="fas fa-expand-alt"></i> Bigger
                                            </button>
                                            <button type="button" class="resize-btn" onclick="resizeTextarea('reset')">
                                                <i class="fas fa-undo"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <textarea class="form-control" id="extractedText" name="extracted_text" 
                                        rows="20" placeholder="Extracted text will appear here...&#10;&#10;PyTesseract provides reliable OCR text extraction.&#10;&#10;Features:&#10;• Advanced image preprocessing&#10;• Multiple OCR configurations tested&#10;• Character whitelist filtering&#10;• Enhanced accuracy with OpenCV processing"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Notes -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="additional_notes" class="col-form-label">Additional Notes</label>
                        <textarea class="form-control" name="additional_notes" id="additional_notes" 
                                rows="3" placeholder="Enter any additional notes about the test..."></textarea>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Back</button>
                <button type="submit" name="submit" class="btn btn-success" id="saveBtn">
                    <i class="fas fa-save"></i> Save Lab Result
                </button>
            </div>
            </form>
        </section>
      </div>
      
      <?php
        if(isset($_POST['submit'])) {
            $pet_id = $_POST['pet_id'];
            $test_type = $_POST['test_type'];
            $test_date = $_POST['test_date'];
            $extracted_text = $_POST['extracted_text'];
            $additional_notes = $_POST['additional_notes'];
            $doctor_id = $_SESSION['doctor_id'];

            $file = $_FILES['lab_file'];
            $allowed_types = array('jpg', 'jpeg', 'png', 'pdf');
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_types) && $file['size'] < 10000000) {
                $file_destination = 'uploads/lab_results/' . uniqid('lab_', true) . '.' . $file_ext;
                
                if (!file_exists('uploads/lab_results/')) {
                    mkdir('uploads/lab_results/', 0777, true);
                }
                
                if (move_uploaded_file($file['tmp_name'], $file_destination)) {
                    $reference = uniqid();
                    
                    // Insert lab result record
                    $insert = mysqli_query($con, "INSERT INTO `lab_analysis` (`reference`, `pet_id`, `doctor_id`, `test_type`, `test_date`, `file_path`, `extracted_text`, `additional_notes`, `created_at`)
                    VALUES ('$reference', '$pet_id', '$doctor_id', '$test_type', '$test_date', '$file_destination', '$extracted_text', '$additional_notes', NOW())");

                    if($insert) {
                        echo "<script>
                        alert('Lab result saved successfully!');
                        window.location.href = window.location.href;
                        </script>";
                    } else {
                        echo "<script>alert('Failed to save lab result.');</script>";
                    }
                } else {
                    echo "<script>alert('Failed to upload file.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type or size. Please upload JPG, PNG, or PDF files under 10MB.');</script>";
            }
        }
      ?>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="../assets/modules/popper.js"></script>
  <script src="../assets/modules/tooltip.js"></script>
  <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
  <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
  <script src="../assets/modules/moment.min.js"></script>
  <script src="../assets/js/stisla.js"></script>
  <script src="../assets/modules/simple-weather/jquery.simpleWeather.min.js"></script>
  <script src="../assets/modules/chart.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/jquery.vmap.min.js"></script>
  <script src="../assets/modules/jqvmap/dist/maps/jquery.vmap.world.js"></script>
  <script src="../assets/modules/summernote/summernote-bs4.js"></script>
  <script src="../assets/modules/chocolat/dist/js/jquery.chocolat.min.js"></script>
  <script src="../assets/js/page/index-0.js"></script>
  <script src="../assets/js/scripts.js"></script>
  <script src="../assets/js/custom.js"></script>

  <script>
    // Enhanced functionality for the bigger extract box
    document.addEventListener('DOMContentLoaded', function() {
        const extractedText = document.getElementById('extractedText');
        const charCount = document.getElementById('charCount');
        const wordCount = document.getElementById('wordCount');
        const lineCount = document.getElementById('lineCount');
        
        // Update statistics in real-time
        function updateStats() {
            const text = extractedText.value;
            const chars = text.length;
            const words = text.trim() === '' ? 0 : text.trim().split(/\s+/).length;
            const lines = text.split('\n').length;
            
            charCount.textContent = chars.toLocaleString();
            wordCount.textContent = words.toLocaleString();
            lineCount.textContent = lines.toLocaleString();
        }
        
        // Update stats on input
        extractedText.addEventListener('input', updateStats);
        extractedText.addEventListener('paste', function() {
            setTimeout(updateStats, 10);
        });
        
        // Initial update
        updateStats();
        
        // Pet selection change handler
        document.getElementById('pet_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const petDetails = document.getElementById('petDetails');
            
            if (selectedOption.value) {
                const species = selectedOption.getAttribute('data-species');
                const breed = selectedOption.getAttribute('data-breed');
                const age = selectedOption.getAttribute('data-age');
                const weight = selectedOption.getAttribute('data-weight');
                const sex = selectedOption.getAttribute('data-sex');
                const owner = selectedOption.getAttribute('data-owner');
                
                petDetails.innerHTML = `
                    <strong>Species:</strong> ${species}<br>
                    <strong>Breed:</strong> ${breed}<br>
                    <strong>Age:</strong> ${age}<br>
                    <strong>Weight:</strong> ${weight}<br>
                    <strong>Sex:</strong> ${sex}<br>
                    <strong>Owner:</strong> ${owner}
                `;
            } else {
                petDetails.innerHTML = '<small class="text-muted">Select a pet to view details</small>';
            }
        });

        // File upload handling
        const fileUploadArea = document.getElementById('fileUploadArea');
        const browseBtn = document.getElementById('browseBtn');
        const labFile = document.getElementById('labFile');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFileBtn = document.getElementById('removeFileBtn');
        const ocrSection = document.getElementById('ocrSection');
        const imagePreview = document.getElementById('imagePreview');
        const ocrButtonContainer = document.getElementById('ocrButtonContainer');
        const startOcrBtn = document.getElementById('startOcrBtn');
        const ocrProcessing = document.getElementById('ocrProcessing');

        // FIXED: Browse button click - prevent event bubbling
        browseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            labFile.click();
        });

        // FIXED: File upload area click - prevent double trigger
        fileUploadArea.addEventListener('click', function(e) {
            // Don't trigger if clicking the browse button or its children
            if (e.target === browseBtn || e.target.closest('#browseBtn')) {
                return;
            }
            
            // Don't trigger if clicking inside file info area
            if (e.target.closest('.file-info')) {
                return;
            }
            
            // Only trigger if clicking the upload area itself
            if (e.target === fileUploadArea || e.target.closest('.file-upload-area')) {
                labFile.click();
            }
        });

        // File selection handling
        labFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                
                // Show file info
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileInfo.style.display = 'block';
                fileUploadArea.classList.add('file-selected');
                
                // Show image preview for images
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                        ocrButtonContainer.style.display = 'block';
                        ocrSection.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // Remove file button
        removeFileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            labFile.value = '';
            fileInfo.style.display = 'none';
            fileUploadArea.classList.remove('file-selected');
            ocrSection.style.display = 'none';
            imagePreview.style.display = 'none';
            ocrButtonContainer.style.display = 'none';
        });

        // Start OCR with PyTesseract
        startOcrBtn.addEventListener('click', function() {
            const file = labFile.files[0];
            if (!file) {
                alert('Please select an image file first.');
                return;
            }

            // Check if file is an image
            if (!file.type.startsWith('image/')) {
                alert('OCR is only available for image files (JPG, PNG, etc.).');
                return;
            }

            // Show processing status
            ocrProcessing.style.display = 'block';
            startOcrBtn.disabled = true;
            startOcrBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Create FormData to send file to server
            const formData = new FormData();
            formData.append('image', file);

            // Send to PyTesseract endpoint (using the PHP file you provided)
            fetch('pytesseract_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                ocrProcessing.style.display = 'none';
                startOcrBtn.disabled = false;
                startOcrBtn.innerHTML = '<i class="fas fa-magic"></i> Extract Text with PyTesseract';

                if (data.success) {
                    extractedText.value = data.text;
                    updateStats();
                    
                    // Auto-resize textarea
                    extractedText.style.height = 'auto';
                    extractedText.style.height = Math.max(400, extractedText.scrollHeight) + 'px';
                    
                    // Show success message with processing info
                    let message = 'Text extraction completed successfully!';
                    if (data.processing_info) {
                        message += '\n\nProcessing details: ' + data.processing_info;
                    }
                    alert(message);
                } else {
                    alert('OCR processing failed: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                ocrProcessing.style.display = 'none';
                startOcrBtn.disabled = false;
                startOcrBtn.innerHTML = '<i class="fas fa-magic"></i> Extract Text with PyTesseract';
                console.error('Error:', error);
                alert('OCR processing failed. Please check if PyTesseract is properly configured and try again.');
            });
        });

        // Drag and drop functionality
        fileUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        fileUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });

        fileUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                labFile.files = files;
                labFile.dispatchEvent(new Event('change'));
            }
        });

        // Helper function to format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
    
    // Resize textarea functions
    function resizeTextarea(action) {
        const textarea = document.getElementById('extractedText');
        const currentHeight = parseInt(window.getComputedStyle(textarea).height);
        
        switch(action) {
            case 'bigger':
                textarea.style.height = (currentHeight + 100) + 'px';
                break;
            case 'smaller':
                if(currentHeight > 200) {
                    textarea.style.height = (currentHeight - 100) + 'px';
                }
                break;
            case 'reset':
                textarea.style.height = '400px';
                break;
        }
    }
    
    // Auto-expand textarea based on content
    document.getElementById('extractedText').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.max(400, this.scrollHeight) + 'px';
    });
  </script>
</body>
</html>