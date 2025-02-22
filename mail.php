<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration_db"; // Change this to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Database Connection Failed!'); window.location.href='index.html';</script>");
}

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = isset($_POST['first-name']) ? htmlspecialchars(trim($_POST['first-name'])) : '';
    $last_name = isset($_POST['last-name']) ? htmlspecialchars(trim($_POST['last-name'])) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $website_url = isset($_POST['url']) ? filter_var($_POST['url'], FILTER_SANITIZE_URL) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($message)) {
        echo "<script>alert('All fields except website URL are required.'); window.history.back();</script>";
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.history.back();</script>";
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO messages (first_name, last_name, email, website_url, message) VALUES (?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        echo "<script>alert('Database Error: " . $conn->error . "'); window.history.back();</script>";
        exit();
    }

    $stmt->bind_param("sssss", $first_name, $last_name, $email, $website_url, $message);

    if ($stmt->execute()) {
        // Send confirmation email
        $to = $email;
        $subject = "Message Received - Thank You!";
        $body = "Hello $first_name,\n\nThank you for reaching out! We have received your message and will respond soon.\n\nBest Regards,\nSupport Team";
        $headers = "From: support@example.com\r\nReply-To: support@example.com";

        // Check if mail function is available
        if (function_exists('mail')) {
            mail($to, $subject, $body, $headers);
        }

        // Show success popup and redirect
        echo "<script>
            alert('Message sent successfully!');
            window.location.href = 'index.html';
        </script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.history.back();</script>";
}

$conn->close();
?>
