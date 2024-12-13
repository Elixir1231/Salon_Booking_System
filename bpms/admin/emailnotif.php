<?php
// Include database connection
require_once 'includes/dbconnection.php';

require '/Users/Adrian Kyle/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



// Early exit if no admin session
if (!isset($_SESSION['bpmsaid'])) {
    exit('Unauthorized');
}

$adminId = $_SESSION['bpmsaid'];

// Fetch admin email with a single, prepared statement
$stmt = $con->prepare("SELECT Email FROM tbladmin WHERE ID = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if (empty($admin['Email'])) {
    exit('No admin email found');
}

// Variables to store the latest notifications
$newNotification = '';
$reschedNotification = '';

// New appointments - Get the most recent new appointment
$newStmt = $con->prepare("SELECT FirstName, LastName, AptNumber, BookingDate 
    FROM tblbook 
    JOIN tbluser ON tbluser.ID = tblbook.UserID 
    WHERE tblbook.Status IS NULL
    ORDER BY tblbook.BookingDate DESC LIMIT 1");  
$newStmt->execute();
$newResult = $newStmt->get_result();

if ($row = $newResult->fetch_assoc()) {
    $newNotification = sprintf(
        "New appointment received from %s %s (Appointment Number: %s).",
        htmlspecialchars($row['FirstName']),
        htmlspecialchars($row['LastName']),
        htmlspecialchars($row['AptNumber'])
    );
    $newBookingDate = $row['BookingDate']; 
}

// Rescheduled appointments - Get the most recent rescheduled appointment
$reschedStmt = $con->prepare("SELECT FirstName, LastName, AptNumber, BookingDate 
    FROM tblbook 
    JOIN tbluser ON tbluser.ID = tblbook.UserID 
    WHERE tblbook.Status = 'Rescheduled'
    ORDER BY tblbook.BookingDate DESC LIMIT 1");  
$reschedStmt->execute();
$reschedResult = $reschedStmt->get_result();

if ($row = $reschedResult->fetch_assoc()) {
    $reschedNotification = sprintf(
        "Appointment rescheduled by %s %s (Appointment Number: %s).",
        htmlspecialchars($row['FirstName']),
        htmlspecialchars($row['LastName']),
        htmlspecialchars($row['AptNumber'])
    );
    $reschedBookingDate = $row['BookingDate']; 
}

// Determine which notification to send (the latest one)
if (!empty($newNotification) && !empty($reschedNotification)) {
    // Compare the dates to determine the latest notification
    if (strtotime($newBookingDate) > strtotime($reschedBookingDate)) {
        $notification = $newNotification;  
    } else {
        $notification = $reschedNotification;  
    }
} elseif (!empty($newNotification)) {
    $notification = $newNotification;  
} elseif (!empty($reschedNotification)) {
    $notification = $reschedNotification;  
} else {
    // Do nothing if no notifications exist
    // Just return from the script or skip sending the email
    return;
}


// Send the email with the latest notification
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'adriankyleramirez4@gmail.com';
    $mail->Password = 'wvff aseq ubit vgoa';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('adriankyleramirez4@gmail.com', 'Edna Salon');
    $mail->addAddress($admin['Email']);
    $mail->isHTML(true);
    $mail->Subject = "New Notification from Edna Salon";
    $mail->Body = $notification;  

    $mail->send();
} catch (Exception $e) {
    error_log("Email notification error: " . $mail->ErrorInfo);
}
?>
