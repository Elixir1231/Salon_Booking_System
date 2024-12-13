<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session and include database connection
session_start();
include('includes/dbconnection.php');

// Set JSON header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'status' => 'error',
    'message' => 'Unknown error occurred',
    'details' => []
];

try {
    // Log incoming request data
    error_log('Appointment check request: ' . print_r($_POST, true));

    // Validate input parameters
    $staff_id = isset($_POST['staff_id']) ? intval($_POST['staff_id']) : null;
    $apt_date = isset($_POST['apt_date']) ? trim($_POST['apt_date']) : null;
    $apt_time = isset($_POST['apt_time']) ? trim($_POST['apt_time']) : null;

    // Log processed input
    error_log("Processed input - Staff ID: $staff_id, Date: $apt_date, Time: $apt_time");

    // Comprehensive input validation
    $errors = [];
    
    if (empty($staff_id)) {
        $errors[] = 'Staff ID is required';
    }
    
    if (empty($apt_date)) {
        $errors[] = 'Appointment date is required';
    } elseif (!strtotime($apt_date)) {
        $errors[] = 'Invalid date format';
    }
    
    if (empty($apt_time)) {
        $errors[] = 'Appointment time is required';
    } elseif (!preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $apt_time)) {
        $errors[] = 'Invalid time format';
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $response['message'] = 'Validation failed';
        $response['details'] = $errors;
        echo json_encode($response);
        exit;
    }

    // Check database connection
    if (!$con) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute staff profile query
    $query = "SELECT 
                staff_id, 
                name, 
                working_days, 
                working_hours, 
                max_appointments_per_day 
              FROM staff_profiles 
              WHERE staff_id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        throw new Exception("Prepare statement failed: " . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "i", $staff_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Statement execution failed: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $staff = mysqli_fetch_assoc($result);

    // Check if staff exists
    if (!$staff) {
        $response['message'] = 'Staff member not found';
        echo json_encode($response);
        exit;
    }

    // Log staff data
    error_log("Staff data found: " . print_r($staff, true));

    // Convert time format if needed (assuming working_hours are in 12-hour format)
    list($start_time, $end_time) = explode(' - ', $staff['working_hours']);
    
    // Convert to 24-hour format for comparison
    $start_time = date("H:i", strtotime($start_time));
    $end_time = date("H:i", strtotime($end_time));
    $apt_time_24 = date("H:i", strtotime($apt_time));

    // Validate working days
    $dayOfWeek = date('l', strtotime($apt_date));
    $working_days = explode(', ', $staff['working_days']);

    if (!in_array($dayOfWeek, $working_days)) {
        $response['message'] = "Staff not available on {$dayOfWeek}";
        $response['details'] = [
            'available_days' => $staff['working_days']
        ];
        echo json_encode($response);
        exit;
    }

    // Validate working hours using 24-hour format
    if ($apt_time_24 < $start_time || $apt_time_24 > $end_time) {
        $response['message'] = "Time outside working hours";
        $response['details'] = [
            'working_hours' => $staff['working_hours']
        ];
        echo json_encode($response);
        exit;
    }

    // If all checks pass, return success
    $response = [
        'status' => 'success',
        'message' => 'Appointment slot is available',
        'details' => [
            'staff_name' => $staff['name'],
            'date' => $apt_date,
            'time' => $apt_time
        ]
    ];

} catch (Exception $e) {
    // Log the full error
    error_log('Appointment Availability Check Error: ' . $e->getMessage());
    
    $response['message'] = 'System error occurred';
    $response['details'] = ['error' => $e->getMessage()];
} finally {
    // Always close the database connection
    if (isset($con)) {
        mysqli_close($con);
    }
}

// Ensure proper JSON encoding
echo json_encode($response);
exit;
?>