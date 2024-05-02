<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "bvn";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $photo = $_FILES['photo'];

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $response = array(
            'success' => false,
            'message' => "Email already exists. BVN cannot be generated."
        );
        echo json_encode($response);
    } else {
        $bvn = generateBVN();

        // Check if the generated BVN already exists
        $sql = "SELECT * FROM users WHERE bvn = '$bvn'";
        $result = $conn->query($sql);

        while ($result->num_rows > 0) {
            // Regenerate BVN if it already exists
            $bvn = generateBVN();
            $sql = "SELECT * FROM users WHERE bvn = '$bvn'";
            $result = $conn->query($sql);
        }

        // Upload the photo
        $photoPath = uploadPhoto($photo, $bvn);

        // Insert user data into the database
        $sql = "INSERT INTO users (username, email, phone, bvn, photo) VALUES ('$name', '$email', '$phone', '$bvn', '$photoPath')";

        if ($conn->query($sql) === TRUE) {
            $response = array(
                'success' => true,
                'bvn' => $bvn,
                'photoPath' => $photoPath
            );
            echo json_encode($response);
        } else {
            $response = array(
                'success' => false,
                'message' => "Error: " . $sql . "<br>" . $conn->error
            );
            echo json_encode($response);
        }
    }
}

// Function to generate a unique BVN
function generateBVN() {
    // Implement your logic to generate a unique BVN
    // Example: Concatenate random numbers and characters
    $bvn = substr(str_shuffle("0123456789"), 0, 10);
    return $bvn;
}

// Function to upload the photo
function uploadPhoto($photo, $bvn) {
    $target_dir = "uploads/";
    $target_file = $target_dir . $bvn . "_" . basename($photo["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is an image
    if (isset($_POST["submit"])) {
        $check = getimagesize($photo["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($photo["tmp_name"], $target_file)) {
            return $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>