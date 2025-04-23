<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Replace with your email address
$receiving_email_address = 'mostafa.farag3392@gmail.com';

// Initialize response array
$response = array(
    'status' => 'error',
    'message' => 'Form submission failed, please try again.'
);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate required fields
    $required_fields = array('name', 'email', 'subject', 'message');
    $error = false;
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $response['message'] = ucfirst($field) . ' is required.';
            $error = true;
            break;
        }
    }
    
    // Validate email format
    if (!$error && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Please enter a valid email address.';
        $error = true;
    }
    
    // If validation passed, process the form
    if (!$error) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        $phone = isset($_POST['phone']) ? $_POST['phone'] : 'Not provided';
        
        // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com';      // Set the SMTP server
            $mail->SMTPAuth   = true;                    // Enable SMTP authentication
            $mail->Username   = 'your-email@example.com'; // SMTP username
            $mail->Password   = 'your-password';         // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port       = 587;                     // TCP port to connect to
            
            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress($receiving_email_address);
            $mail->addReplyTo($email, $name);
            
            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
            
            $mail->send();
            $response = array(
                'status' => 'success',
                'message' => 'Your message has been sent. Thank you!'
            );
        } catch (Exception $e) {
            $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

// Return JSON response for AJAX requests
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>