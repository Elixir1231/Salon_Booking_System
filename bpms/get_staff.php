<?php
header('Content-Type: application/json');
require_once('includes/dbconnection.php');

// Initialize response array
$response = [
    'status' => 'error',
    'message' => '',
    'data' => []
];

try {
    // Get and sanitize search term
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // Validate search term length
    if (strlen($searchTerm) < 1) {
        $response['message'] = 'Please enter a search term';
        echo json_encode($response);
        exit;
    }
    
    // Prepare the search query - modified to match your table structure
    $query = "SELECT staff_id, profile_picture, name, role, specialization, working_days, working_hours, max_appointments_per_day 
              FROM staff_profiles 
              WHERE name LIKE ? OR role LIKE ? OR specialization LIKE ? 
              LIMIT 10";
    
    $stmt = mysqli_prepare($con, $query);
    
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($con));
    }
    
    // Add wildcards to search term
    $likeTerm = "%{$searchTerm}%";
    mysqli_stmt_bind_param($stmt, "sss", $likeTerm, $likeTerm, $likeTerm);
    
    // Execute the query
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
    }
    
    // Get the result
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result === false) {
        throw new Exception('Failed to get result: ' . mysqli_error($con));
    }
    
    // Fetch and format results
    $staff = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $staff[] = [
            'staff_id' => intval($row['staff_id']),
            'profile_picture' => htmlspecialchars($row['profile_picture'], ENT_QUOTES, 'UTF-8'),
            'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
            'role' => htmlspecialchars($row['role'], ENT_QUOTES, 'UTF-8'),
            'specialization' => htmlspecialchars($row['specialization'], ENT_QUOTES, 'UTF-8'),
            'working_days' => htmlspecialchars($row['working_days'], ENT_QUOTES, 'UTF-8'),
            'working_hours' => htmlspecialchars($row['working_hours'], ENT_QUOTES, 'UTF-8'),
            'max_appointments_per_day' => intval($row['max_appointments_per_day'])
        ];
    }
    
    // Set response based on results
    if (empty($staff)) {
        $response['status'] = 'no_results';
        $response['message'] = 'No staff members found matching your search';
    } else {
        $response['status'] = 'success';
        $response['message'] = count($staff) . ' staff member(s) found';
        $response['data'] = $staff;
    }

} catch (Exception $e) {
    // Log the error and return a generic message
    error_log('Staff Search Error: ' . $e->getMessage());
    $response['message'] = 'An unexpected error occurred. Please try again.';
} finally {
    // Clean up
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    if (isset($con)) {
        mysqli_close($con);
    }
}

// Return the response
echo json_encode($response);
exit;
?>