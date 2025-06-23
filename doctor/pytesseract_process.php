<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log errors
function logError($message) {
    error_log("[PyTesseract OCR] " . date('Y-m-d H:i:s') . " - " . $message . "\n", 3, "pytesseract_errors.log");
}

// Function to send JSON response
function sendResponse($success, $data = null, $error = null) {
    $response = array('success' => $success);
    if ($data !== null) {
        $response['text'] = $data;
    }
    if ($error !== null) {
        $response['error'] = $error;
    }
    echo json_encode($response);
    exit;
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, null, 'Only POST method is allowed');
    }

    // Check if file was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error_msg = 'No file uploaded or upload error';
        if (isset($_FILES['image']['error'])) {
            switch ($_FILES['image']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_msg = 'File too large';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_msg = 'File upload incomplete';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_msg = 'No file selected';
                    break;
                default:
                    $error_msg = 'File upload error';
            }
        }
        sendResponse(false, null, $error_msg);
    }

    $uploadedFile = $_FILES['image'];
    $fileName = $uploadedFile['name'];
    $fileTmpName = $uploadedFile['tmp_name'];
    $fileSize = $uploadedFile['size'];
    $fileType = $uploadedFile['type'];

    // Validate file type
    $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/tiff', 'image/tif');
    if (!in_array($fileType, $allowedTypes)) {
        sendResponse(false, null, 'Invalid file type. Only image files are allowed.');
    }

    // Validate file size (max 10MB)
    $maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($fileSize > $maxFileSize) {
        sendResponse(false, null, 'File size too large. Maximum allowed size is 10MB.');
    }

    // Create temp directory if it doesn't exist
    $tempDir = 'temp_ocr/';
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    // Generate unique filename
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    $uniqueFileName = 'ocr_' . uniqid() . '.' . $fileExtension;
    $tempFilePath = $tempDir . $uniqueFileName;

    // Move uploaded file to temp directory
    if (!move_uploaded_file($fileTmpName, $tempFilePath)) {
        sendResponse(false, null, 'Failed to save uploaded file');
    }

    // Check if Python is available
    $pythonCommand = 'python3'; // or 'python' depending on your system
    $pythonCheck = shell_exec("which $pythonCommand 2>/dev/null");
    if (empty($pythonCheck)) {
        $pythonCommand = 'python';
        $pythonCheck = shell_exec("which $pythonCommand 2>/dev/null");
        if (empty($pythonCheck)) {
            // Clean up temp file
            unlink($tempFilePath);
            sendResponse(false, null, 'Python is not installed or not in PATH');
        }
    }

    // Create Python script for PyTesseract processing
    $pythonScript = <<<PYTHON
import sys
import json
import os
try:
    import pytesseract
    from PIL import Image
    import cv2
    import numpy as np
except ImportError as e:
    print(json.dumps({"success": False, "error": f"Missing required Python package: {str(e)}"}))
    sys.exit(1)

def preprocess_image(image_path):
    """Preprocess image for better OCR results"""
    try:
        # Read image with OpenCV
        img = cv2.imread(image_path)
        
        # Convert to grayscale
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
        
        # Apply denoising
        denoised = cv2.fastNlMeansDenoising(gray)
        
        # Apply threshold to get image with only black and white
        _, thresh = cv2.threshold(denoised, 0, 255, cv2.THRESH_BINARY + cv2.THRESH_OTSU)
        
        # Save preprocessed image
        preprocessed_path = image_path.replace('.', '_preprocessed.')
        cv2.imwrite(preprocessed_path, thresh)
        
        return preprocessed_path
    except Exception as e:
        # If preprocessing fails, return original path
        return image_path

def process_image(image_path):
    try:
        # Preprocess image for better OCR
        processed_image_path = preprocess_image(image_path)
        
        # Open image with PIL
        image = Image.open(processed_image_path)
        
        # Configure Tesseract options for better accuracy
        custom_config = r'--oem 3 --psm 6 -c tessedit_char_whitelist=0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz.,!?@#$%^&*()_+-=[]{}|;:\'\"<>/\\~` '
        
        # Extract text using pytesseract
        extracted_text = pytesseract.image_to_string(image, config=custom_config)
        
        # Get confidence scores for each word
        data = pytesseract.image_to_data(image, output_type=pytesseract.Output.DICT, config=custom_config)
        
        # Filter out low confidence text (confidence < 30)
        filtered_text = []
        confidences = []
        
        for i, conf in enumerate(data['conf']):
            if int(conf) > 30:  # Only include text with confidence > 30
                text = data['text'][i].strip()
                if text:
                    filtered_text.append(text)
                    confidences.append(int(conf))
        
        # If filtered text is available, use it; otherwise use raw extraction
        if filtered_text:
            final_text = ' '.join(filtered_text)
            avg_confidence = sum(confidences) / len(confidences) if confidences else 0
        else:
            final_text = extracted_text.strip()
            avg_confidence = 0
        
        # Clean up preprocessed image if it was created
        if processed_image_path != image_path and os.path.exists(processed_image_path):
            os.remove(processed_image_path)
        
        return {
            "success": True,
            "text": final_text,
            "confidence_info": f"Average confidence: {avg_confidence:.1f}%, Words detected: {len(filtered_text)}"
        }
        
    except Exception as e:
        return {
            "success": False,
            "error": f"OCR processing failed: {str(e)}"
        }

def process_image_simple(image_path):
    """Simple processing without OpenCV preprocessing (fallback)"""
    try:
        # Open image with PIL
        image = Image.open(image_path)
        
        # Simple Tesseract configuration
        custom_config = r'--oem 3 --psm 6'
        
        # Extract text using pytesseract
        extracted_text = pytesseract.image_to_string(image, config=custom_config)
        
        return {
            "success": True,
            "text": extracted_text.strip(),
            "confidence_info": "Simple extraction mode"
        }
        
    except Exception as e:
        return {
            "success": False,
            "error": f"Simple OCR processing failed: {str(e)}"
        }

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"success": False, "error": "Usage: python script.py <image_path>"}))
        sys.exit(1)
    
    image_path = sys.argv[1]
    
    if not os.path.exists(image_path):
        print(json.dumps({"success": False, "error": "Image file not found"}))
        sys.exit(1)
    
    # Try advanced processing first, fall back to simple if it fails
    result = process_image(image_path)
    if not result['success']:
        result = process_image_simple(image_path)
    
    print(json.dumps(result))
PYTHON;

    // Save Python script to temp file
    $pythonScriptPath = $tempDir . 'pytesseract_script.py';
    file_put_contents($pythonScriptPath, $pythonScript);

    // Execute Python script
    $command = "$pythonCommand " . escapeshellarg($pythonScriptPath) . " " . escapeshellarg($tempFilePath) . " 2>&1";
    $output = shell_exec($command);

    // Clean up temp files
    unlink($tempFilePath);
    unlink($pythonScriptPath);

    // Parse output
    if (empty($output)) {
        sendResponse(false, null, 'No output from OCR processing');
    }

    $result = json_decode($output, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        logError("JSON decode error: " . json_last_error_msg() . " | Output: " . $output);
        sendResponse(false, null, 'Failed to parse OCR results');
    }

    if (!isset($result['success'])) {
        logError("Invalid result format: " . $output);
        sendResponse(false, null, 'Invalid OCR result format');
    }

    if ($result['success']) {
        $extractedText = isset($result['text']) ? $result['text'] : '';
        sendResponse(true, $extractedText);
    } else {
        $error = isset($result['error']) ? $result['error'] : 'Unknown OCR error';
        logError("OCR failed: " . $error);
        sendResponse(false, null, $error);
    }

} catch (Exception $e) {
    logError("Exception: " . $e->getMessage());
    sendResponse(false, null, 'Server error: ' . $e->getMessage());
}

// Direct Tesseract implementation (alternative approach without Python)
function processWithTesseractDirect($imagePath) {
    try {
        // Check if tesseract is installed
        $tesseractCheck = shell_exec("which tesseract 2>/dev/null");
        if (empty($tesseractCheck)) {
            return array('success' => false, 'error' => 'Tesseract is not installed');
        }

        // Run tesseract OCR with configuration
        $outputFile = $imagePath . '_output';
        $configOptions = '-c tessedit_char_whitelist=0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz.,!?@#$%^&*()_+-=[]{}|;:\'\"<>/\\~` --oem 3 --psm 6';
        $command = "tesseract " . escapeshellarg($imagePath) . " " . escapeshellarg($outputFile) . " $configOptions 2>&1";
        $output = shell_exec($command);

        $textFile = $outputFile . '.txt';
        if (file_exists($textFile)) {
            $extractedText = file_get_contents($textFile);
            unlink($textFile); // Clean up
            return array('success' => true, 'text' => trim($extractedText));
        } else {
            return array('success' => false, 'error' => 'Tesseract processing failed: ' . $output);
        }
    } catch (Exception $e) {
        return array('success' => false, 'error' => 'Tesseract error: ' . $e->getMessage());
    }
}

// Installation check endpoint
if (isset($_GET['check'])) {
    $checks = array();
    
    // Check Python
    $pythonCheck = shell_exec("python3 --version 2>&1") ?: shell_exec("python --version 2>&1");
    $checks['python'] = !empty($pythonCheck);
    
    // Check PyTesseract (this requires Python to be available)
    if ($checks['python']) {
        $pytesseractCheck = shell_exec("python3 -c 'import pytesseract; print(\"OK\")' 2>&1") ?: 
                           shell_exec("python -c 'import pytesseract; print(\"OK\")' 2>&1");
        $checks['pytesseract'] = strpos($pytesseractCheck, 'OK') !== false;
        
        // Check PIL
        $pilCheck = shell_exec("python3 -c 'from PIL import Image; print(\"OK\")' 2>&1") ?: 
                   shell_exec("python -c 'from PIL import Image; print(\"OK\")' 2>&1");
        $checks['pillow'] = strpos($pilCheck, 'OK') !== false;
        
        // Check OpenCV (optional for preprocessing)
        $cvCheck = shell_exec("python3 -c 'import cv2; print(\"OK\")' 2>&1") ?: 
                  shell_exec("python -c 'import cv2; print(\"OK\")' 2>&1");
        $checks['opencv'] = strpos($cvCheck, 'OK') !== false;
    } else {
        $checks['pytesseract'] = false;
        $checks['pillow'] = false;
        $checks['opencv'] = false;
    }
    
    // Check Tesseract binary
    $tesseractCheck = shell_exec("tesseract --version 2>&1");
    $checks['tesseract_binary'] = !empty($tesseractCheck);
    
    echo json_encode(array(
        'success' => true,
        'checks' => $checks,
        'message' => 'System check completed',
        'installation_notes' => array(
            'python_packages' => 'pip install pytesseract pillow opencv-python',
            'tesseract_binary' => 'Install Tesseract OCR binary from https://github.com/tesseract-ocr/tesseract',
            'ubuntu_install' => 'sudo apt-get install tesseract-ocr',
            'macos_install' => 'brew install tesseract'
        )
    ));
    exit;
}
?>