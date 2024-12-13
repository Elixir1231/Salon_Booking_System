<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

$msg = ""; // Initialize the message variable

if (strlen($_SESSION['bpmsaid'] == 0)) {
    header('location:logout.php');
} else {

  // Add new staff member
  if (isset($_POST['submit'])) {
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
    $target_dir = "uploads/staff/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $profile_picture = "";
    if(isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["profile_picture"]["name"];
        $filetype = $_FILES["profile_picture"]["type"];
        $filesize = $_FILES["profile_picture"]["size"];
    
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            echo "<script>alert('Error: Please select a valid file format.');</script>";
            header("Location: staff_list.php");
            return;
        }
    
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            echo "<script>alert('Error: File size is larger than the allowed limit.');</script>";
            header("Location: staff_list.php");
            return;
        }
    
        // Verify MYME type of the file
        if(in_array($filetype, $allowed)) {
            $unique_name = date('YmdHis') . '_' . uniqid() . '.' . $ext;
            $target_file = $target_dir . $unique_name;
            
            if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                $profile_picture = $target_file;
            } else {
                echo "<script>alert('Error: There was an error uploading your file.');</script>";
                return;
            }
        } else {
            echo "<script>alert('Error: There was a problem with the file type.');</script>";
            return;
        }
    }

    $stmt = $con->prepare("INSERT INTO staff_profiles (profile_picture, name, role, specialization, working_days, working_hours) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $profile_picture, $name, $role, $specialization, $working_days, $working_hours);
    
    if ($stmt->execute()) {
        echo "<script>alert('Staff member has been added.');</script>";
        echo "<script>window.location.href = 'staff_list.php'</script>";
    } else {
        echo "<script>alert('Something Went Wrong. Please try again.');</script>";
    }
    $stmt->close();
    }

    // Delete staff member
    if (isset($_GET['delid'])) {
        $id = $_GET['delid'];
        $stmt = $con->prepare("DELETE FROM staff_profiles WHERE staff_id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Staff member has been deleted.');</script>";
            echo "<script>window.location.href = 'staff_list.php'</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again.');</script>";
        }
        $stmt->close();
    }

    // Update staff member
    if (isset($_POST['update'])) {
        $id = $_POST['staff_id'];
        $profile_picture = $_POST['profile_picture'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        $specialization = $_POST['specialization'];
        $availability = $_POST['availability'];

        $stmt = $con->prepare("UPDATE staff_profiles SET profile_picture = ?, name = ?, role = ?, specialization = ?, availability = ? WHERE staff_id = ?");
        $stmt->bind_param("sssssi", $profile_picture, $name, $role, $specialization, $availability, $id);
        
        if ($stmt->execute()) {
            echo "<script>alert('Staff member has been updated.');</script>";
            echo "<script>window.location.href = 'staff_list.php'</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again.');</script>";
        }
        $stmt->close();
    }

    // Fetch existing staff members
    $ret = mysqli_query($con, "SELECT * FROM staff_profiles");
    if (!$ret) {
        die("Query failed: " . mysqli_error($con)); // Check for query errors
    }
    $cnt = 1;
    while ($row = mysqli_fetch_array($ret)) {
        // Your existing code to display staff members
    }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>BPMS | Manage Staff List</title>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- font CSS -->
<!-- font-awesome icons -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
 <!-- js-->
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/modernizr.custom.js"></script>
<!--webfonts-->
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<!--//webfonts--> 
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
	<script>
		 new WOW().init();
	</script>
<!--//end-animate-->
<!-- Metis Menu -->
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<link href="css/custom.css" rel="stylesheet">
<!--//Metis Menu -->
</head> 
<style>
    .scrollable-table {
    max-height: 300px; /* Set your desired height */
    overflow-y: auto;  /* Enable vertical scrolling */
    overflow-x: hidden; /* Hide horizontal overflow */
    border: 1px solid #ddd; /* Optional: Add a border for better visibility */
}
</style>
<body class="cbp-spmenu-push">
	<div class="main-content">
		<!--left-fixed -navigation-->
		 <?php include_once('includes/sidebar.php');?>
		<!--left-fixed -navigation-->
		<!-- header-starts -->
		 <?php include_once('includes/header.php');?>
		<!-- //header-ends -->
		<!-- main content start-->
        <div id="page-wrapper">
            <div class="main-page">
                <div class="forms">
                    <h3 class="title1">Manage Staff List</h3>
                    <div class="form-grids row widget-shadow" data-example-id="basic-forms"> 
                        <div class="form-title">
                            <h4>Add New Staff Member:</h4>
                        </div>
                        <div class="form-body">
                            <form method="post" enctype="multipart/form-data">
                                <p style="font-size:16px; color:red" align="center"> <?php if($msg){ echo $msg; }  ?> </p>

                                <div class="form-group"> 
                                    <label for="profile_picture">Profile Picture</label> 
                                    <input type="file" id="profile_picture" name="profile_picture" class="form-control" accept="image/*" required="true">
                                    <small class="text-muted">Allowed formats: JPG, JPEG, PNG, GIF (Max size: 5MB)</small>
                                </div>
                                <div class="form-group"> 
                                    <label for="name">Name</label> 
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter Staff Name" value="" required="true"> 
                                </div>
                                <div class="form-group"> 
                                    <label for="role">Role</label> 
                                    <input type="text" id="role" name="role" class="form-control" placeholder="Enter Role" value="" required="true"> 
                                </div>
                                <div class="form-group"> 
                                    <label for="specialization">Specialization</label> 
                                    <input type="text" id="specialization" name="specialization" class="form-control" placeholder="Enter Specialization" value="" required="true"> 
                                </div>
                                <!-- Replace the existing availability input with this new form structure -->
<div class="form-group">
    <label>Working Days</label>
    <div class="checkbox">
        <label><input type="checkbox" name="working_days[]" value="Monday"> Monday</label>
        <label><input type="checkbox" name="working_days[]" value="Tuesday"> Tuesday</label>
        <label><input type="checkbox" name="working_days[]" value="Wednesday"> Wednesday</label>
        <label><input type="checkbox" name="working_days[]" value="Thursday"> Thursday</label>
        <label><input type="checkbox" name="working_days[]" value="Friday"> Friday</label>
        <label><input type="checkbox" name="working_days[]" value="Saturday"> Saturday</label>
        <label><input type="checkbox" name="working_days[]" value="Sunday"> Sunday</label>
    </div>
</div>
<div class="form-group">
    <label>Working Hours</label>
    <div class="row">
        <div class="col-md-6">
            <label for="start_time">Start Time</label>
            <input type="time" id="start_time" name="start_time" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label for="end_time">End Time</label>
            <input type="time" id="end_time" name="end_time" class="form-control" required>
        </div>
    </div>
</div>
                                <button type="submit" name="submit" class="btn btn-default">Add</button> 
                            </form> 
                        </div>
                    </div>

<!-- Display Staff List -->
<div class="form-grids row widget-shadow mt-4" data-example-id="basic-forms">
    <div class="form-title">
        <h4>Existing Staff Members:</h4>
    </div>
    <div class="form-body">
        <div class="table-responsive scrollable-table"> <!-- Add the scrollable-table class here -->
            <table class="table table-bordered">
    <thead>
        <tr>
            <th>Profile Picture</th>
            <th>Name</th>
            <th>Role</th>
            <th>Specialization</th>
            <th>Working Days</th>
            <th>Working Hours</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $ret = mysqli_query($con, "SELECT * FROM staff_profiles");
        while ($row = mysqli_fetch_array($ret)) {
        ?>
        <tr>
            <td><img src="<?php echo $row['profile_picture'];?>" alt="Profile Picture" width="50" height="50"></td>
            <td><?php echo $row['name'];?></td>
            <td><?php echo $row['role'];?></td>
            <td><?php echo $row['specialization'];?></td>
            <td><?php echo $row['working_days'];?></td>
            <td><?php echo $row['working_hours'];?></td>
            <td>
            <a href="update_staff.php?staff_id=<?php echo $row['staff_id'];?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="?delid=<?php echo $row['staff_id'];?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this staff member?');">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
        </div>
    </div>
</div>
				</div>
			</div>
		</div>
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
        <!--scrolling js-->
	<script src="js/jquery.nicescroll.js"></script>
	<script src="js/scripts.js"></script>
	<!--//scrolling js-->
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"> </script>
</body>
</html>

