<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "ddrss";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get form data
$studentName = $_POST['studentName'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$course = $_POST['course'];
$parentName = $_POST['parentName'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$address = $_POST['address'];
$message = $_POST['message'];

// File upload
$targetDir = "uploads/";
if (!is_dir($targetDir)) {
  mkdir($targetDir, 0777, true);
}

$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . time() . "_" . $fileName;
$fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

// Check if file is allowed
$allowedTypes = array("pdf", "jpg", "jpeg", "png");
if (in_array($fileType, $allowedTypes)) {
  if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
    
    // Insert into database
    $sql = "INSERT INTO admission_data (studentName, dob, gender, course, parentName, phone, email, address, message, file_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $studentName, $dob, $gender, $course, $parentName, $phone, $email, $address, $message, $targetFilePath);

    if ($stmt->execute()) {
      echo "✅ Thank you! Your application has been submitted successfully.";
    } else {
      echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
  } else {
    echo "❌ Failed to upload file.";
  }
} else {
  echo "❌ Invalid file type. Only PDF, JPG, JPEG, PNG allowed.";
}

$conn->close();
?>
