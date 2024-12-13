<?php
// Save this as update_payment_status.php
session_start();
include('includes/dbconnection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['bpmsaid'])) {
    $invoice_id = mysqli_real_escape_string($con, $_POST['invoice_id']);
    $payment_status = mysqli_real_escape_string($con, $_POST['payment_status']);
    $payment_option = mysqli_real_escape_string($con, $_POST['payment_option']);
    
    // Update the payment status and payment option
    $query = mysqli_query($con, "UPDATE tblinvoice SET 
        payment_status='$payment_status',
        payment_option_id='$payment_option',
        payment_date=NOW()
        WHERE BillingId='$invoice_id'");
    
    if ($query) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>