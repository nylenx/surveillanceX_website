<?php
header('Content-Type: application/json'); // Indicate JSON response

// IMPORTANT: Configure these variables
$recipient_email = "your_email@yourdomain.com"; // WHERE TO SEND THE EMAIL
$subject_prefix = "[Website Lead - CCTV/Biometric]";

// Basic Security Headers (Optional but recommended)
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
// Consider adding Content Security Policy (CSP) headers for better security

$response = ['success' => false, 'message' => 'An unexpected error occurred.'];

// Only process POST requests.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Basic Honeypot (Optional - add a hidden field in your form) ---
    // if (!empty($_POST['honeypot_field_name'])) {
    //     // Likely a bot, exit quietly or log it
    //     echo json_encode(['success' => true, 'message' => 'Form submitted successfully.']); // Pretend success
    //     exit;
    // }

    // --- Sanitize Input Data ---
    // Use filter_var for better sanitization/validation
    $name = filter_var(trim($_POST["name"]), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_STRING); // Basic sanitize, more complex validation might be needed
    $service = filter_var(trim($_POST["service"]), FILTER_SANITIZE_STRING);
    $location = filter_var(trim($_POST["location"]), FILTER_SANITIZE_STRING);
    $message = filter_var(trim($_POST["message"]), FILTER_SANITIZE_STRING);

    // --- Validate Input Data ---
    if (empty($name) || empty($phone) || empty($message)) {
        $response['message'] = 'Name, Phone, and Message are required fields.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $response['message'] = 'Invalid email format.';
         echo json_encode($response);
         exit;
    }

    // --- Build the Email Content ---
    $email_content = "New Lead Details:\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Phone: $phone\n";
    $email_content .= "Service Interested In: $service\n";
    $email_content .= "Location: $location\n\n";
    $email_content .= "Message:\n$message\n";

    // --- Build the Email Headers ---
    $email_headers = "From: $name <$email>\r\n";
    $email_headers .= "Reply-To: $email\r\n";
    $email_headers .= "Content-Type: text/plain; charset=UTF-8\r\n"; // Ensure proper encoding
    $email_headers .= "X-Mailer: PHP/" . phpversion();

    // --- Send the Email ---
    // The mail() function requires a properly configured mail server (SMTP) on your hosting.
    // It's often unreliable on shared hosting. Consider using PHPMailer library for better results.
    $full_subject = "$subject_prefix $service"; // Add service to subject
    if (mail($recipient_email, $full_subject, $email_content, $email_headers)) {
        $response['success'] = true;
        $response['message'] = 'Thank you! Your message has been sent successfully. We will contact you soon.';
        // Optional: Redirect after success using JavaScript on the front-end based on this response
        // header('Location: ../thank-you.html'); // PHP redirect - less ideal with AJAX
    } else {
         $response['message'] = 'Oops! Something went wrong and we couldn\'t send your message. Please try calling us.';
         // Log the error for debugging on the server side if possible
         error_log("Mail function failed for $recipient_email from $email");
    }

} else {
    // Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
     $response['message'] = 'There was a problem with your submission, please try again.';
}

// Return JSON response
echo json_encode($response);
?>