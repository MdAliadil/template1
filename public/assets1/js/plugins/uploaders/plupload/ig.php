<?php
// Handle image upload if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Define the directory where you want to save the uploaded files
    $targetDir = "uploads/";
    
    // Get the uploaded file's information
    $fileName = basename($_FILES['image']['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image or a fake one
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        echo "File is not an image.<br>";
    } else {
        // Check file size (optional: here it's limited to 5MB)
        if ($_FILES['image']['size'] > 5000000) {
            echo "Sorry, your file is too large.<br>";
        } else {
            // Allow only specific file formats (e.g., JPG, JPEG, PNG, GIF)
            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedFormats)) {
                echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.<br>";
            } else {
                // Move the uploaded file to the target directory
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    echo "The file " . htmlspecialchars($fileName) . " has been uploaded.<br>";
                } else {
                    echo "Sorry, there was an error uploading your file.<br>";
                }
            }
        }
    }
}
?>

<!-- HTML Form to upload image -->
<form action="ig.php" method="POST" enctype="multipart/form-data">
    <label>Select Image to Upload:</label>
    <input type="file" name="image" required>
    <input type="submit" value="Upload Image">
</form>
