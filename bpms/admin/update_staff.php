<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

// Check if user is logged in
if (strlen($_SESSION['bpmsaid'] == 0)) {
    header('location:logout.php');
    exit();
}

// Check if staff ID is provided
if (!isset($_GET['staff_id']) || empty($_GET['staff_id'])) {
    echo "<script>alert('Invalid Staff Member'); window.location.href='staff_list.php';</script>";
    exit();
}

$staff_id = $_GET['staff_id'];

// Fetch current staff details
$stmt = $con->prepare("SELECT * FROM staff_profiles WHERE staff_id = ?");
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

if (!$staff) {
    echo "<script>alert('Staff Member Not Found'); window.location.href='staff_list.php';</script>";
    exit();
}

// Update staff member
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $specialization = $_POST['specialization'];
    
    // Process working days
    $working_days = isset($_POST['working_days']) ? implode(', ', $_POST['working_days']) : '';

    // Process working hours
    $start_time = date('g:ia', strtotime($_POST['start_time']));
    $end_time = date('g:ia', strtotime($_POST['end_time']));
    $working_hours = $start_time . ' - ' . $end_time;

    // File upload handling
    $profile_picture = $staff['profile_picture'];
    $target_dir = "uploads/staff/";

    if(!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["profile_picture"]["name"];
        $filetype = $_FILES["profile_picture"]["type"];
        $filesize = $_FILES["profile_picture"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            echo "<script>alert('Error: Please select a valid file format.');</script>";
            exit();
        }
    
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            echo "<script>alert('Error: File size is larger than the allowed limit.');</script>";
            exit();
        }
    
        // Verify MIME type of the file
        if(in_array($filetype, $allowed)) {
            $unique_name = date('YmdHis') . '_' . uniqid() . '.' . $ext;
            $target_file = $target_dir . $unique_name;
            
            if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Delete old profile picture if a new one is uploaded
                if (!empty($staff['profile_picture']) && file_exists($staff['profile_picture'])) {
                    unlink($staff['profile_picture']);
                }
                $profile_picture = $target_file;
            } else {
                echo "<script>alert('Error: There was an error uploading your file.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error: There was a problem with the file type.');</script>";
            exit();
        }
    }

    $stmt = $con->prepare("UPDATE staff_profiles SET profile_picture = ?, name = ?, role = ?, specialization = ?, working_days = ?, working_hours = ? WHERE staff_id = ?");
    $stmt->bind_param("ssssssi", $profile_picture, $name, $role, $specialization, $working_days, $working_hours, $staff_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Staff member has been updated successfully.'); window.location.href='staff_list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>BPMS | Update Staff Member</title>
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <!-- Custom CSS -->
    <link href="css/style.css" rel='stylesheet' type='text/css' />
    <!-- font-awesome icons -->
    <link href="css/font-awesome.css" rel="stylesheet"> 
    
    <!-- Web Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
    
    <!-- Animate CSS -->
    <link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
    
    <!-- Metis Menu -->
    <link href="css/custom.css" rel="stylesheet">
    
    <!-- Jquery -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/modernizr.custom.js"></script>
    
    <!-- Wow Animation -->
    <script src="js/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>
</head>
<body class="cbp-spmenu-push">
    <div class="main-content">
        <!-- Side Navigation -->
        <?php include_once('includes/sidebar.php');?>
        
        <!-- Header -->
        <?php include_once('includes/header.php');?>
        
        <!-- Main Content -->
        <div id="page-wrapper">
            <div class="main-page">
                <div class="forms">
                    <h3 class="title1">Update Staff Member</h3>
                    <div class="form-grids row widget-shadow" data-example-id="basic-forms"> 
                        <div class="form-title">
                            <h4>Edit Staff Member Details:</h4>
                        </div>
                        <div class="form-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group"> 
                                    <label for="profile_picture">Profile Picture</label> 
                                    <?php if(!empty($staff['profile_picture'])): ?>
                                        <div class="mb-3">
                                            <img src="<?php echo $staff['profile_picture']; ?>" alt="Current Profile Picture" width="150" height="150">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/*">
                                    <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF (Max size: 5MB)</small>
                                </div>
                                <div class="form-group"> 
                                    <label for="name">Name</label> 
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Staff Name" value="<?php echo htmlspecialchars($staff['name']); ?>" required="true"> 
                                </div>
                                <div class="form-group"> 
                                    <label for="role">Role</label> 
                                    <input type="text" id="role" name="role" class="form-control" placeholder="Enter Role" value="<?php echo htmlspecialchars($staff['role']); ?>" required="true"> 
                                </div>
                                <div class="form-group"> 
                                    <label for="specialization">Specialization</label> 
                                    <input type="text" id="specialization" name="specialization" class="form-control" placeholder="Enter Specialization" value="<?php echo htmlspecialchars($staff['specialization']); ?>" required="true"> 
                                </div>
                                <div class="form-group">
                                    <label>Working Days</label>
                                    <div class="checkbox">
                                        <?php 
                                        $selected_days = explode(', ', $staff['working_days']);
                                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                        foreach ($days as $day): 
                                        ?>
                                        <label>
                                            <input type="checkbox" name="working_days[]" value="<?php echo $day; ?>" 
                                                <?php echo in_array($day, $selected_days) ? 'checked' : ''; ?>>
                                            <?php echo $day; ?>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Working Hours</label>
                                    <?php 
                                    // Parse existing working hours
                                    $hours = explode(' - ', $staff['working_hours']);
                                    $start_time = date('H:i', strtotime($hours[0]));
                                    $end_time = date('H:i', strtotime($hours[1]));
                                    ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="start_time">Start Time</label>
                                            <input type="time" id="start_time" name="start_time" class="form-control" value="<?php echo $start_time; ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_time">End Time</label>
                                            <input type="time" id="end_time" name="end_time" class="form-control" value="<?php echo $end_time; ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="update" class="btn btn-default">Update</button> 
                                <a href="staff_list.php" class="btn btn-secondary">Cancel</a>
                            </form> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <?php include_once('includes/footer.php');?>
    </div>

    <!-- Classie -->
    <script src="js/classie.js"></script>
    <script>
        var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),
            showLeftPush = document.getElementById( 'showLeftPush' ),
            body = document.body;
            
        showLeftPush.onclick = function() {
            classie.toggle( this, 'active' );
            classie.toggle( body, 'cbp-spmenu-push-toright' );
            classie.toggle( menuLeft, 'cbp-spmenu-open' );
            disableOther( 'showLeftPush' );
        };

        function disableOther( button ) {
            if( button !== 'showLeftPush' ) {
                classie.toggle( showLeftPush, 'disabled' );
            }
        }
    </script>

    <!-- Scrolling JS -->
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/scripts.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.js"></script>
</body>
</html>