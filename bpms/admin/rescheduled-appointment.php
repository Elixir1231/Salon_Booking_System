<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['bpmsaid'] == 0)) {
    header('location:logout.php');
} else {

    if ($_GET['delid']) {
        $sid = $_GET['delid'];
        mysqli_query($con, "delete from tblbook where ID ='$sid'");
        echo "<script>alert('Data Deleted');</script>";
        echo "<script>window.location.href='rescheduled-appointment.php'</script>";
    }

    // Check if search term is set and sanitize it
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

    // Modify the query to include a WHERE clause for searching
    $searchQuery = "";
    if ($searchTerm) {
        $searchQuery = "AND (tblbook.AptNumber LIKE '%$searchTerm%' 
                          OR CONCAT(tbluser.FirstName, ' ', tbluser.LastName) LIKE '%$searchTerm%' 
                          OR tbluser.MobileNumber LIKE '%$searchTerm%')";
    }

    // Handle reschedule success message
    $rescheduleSuccess = isset($_GET['reschedule']) && $_GET['reschedule'] == 'success';
?>
<!DOCTYPE HTML>
<html>
<head>
<title>BPMS || Rescheduled Appointment</title>

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
<style>
    .reschedule-success {
      background-color: #dff0d8;
      color: #3c763d;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 4px;
    }
</style>
</head> 
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
                <div class="tables">
                    <h3 class="title1">Rescheduled Appointment</h3>

                    <?php if($rescheduleSuccess): ?>
                        <div class="reschedule-success">
                            Appointment has been successfully rescheduled!
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive bs-example widget-shadow">
                        <h4>Rescheduled Appointments:</h4>
                        <!-- Search Bar -->
                        <form method="GET" action="rescheduled-appointment.php" class="mb-4">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by Appointment Number, Name, or Mobile" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            </div>
                        </form>

                        <!-- Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Appointment Number</th>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Appointment Date</th>
                                    <th>Appointment Time</th>
                                    <th>Status</th>
                                    <th>Action</ th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Update the query to show only rescheduled appointments
                                $ret = mysqli_query($con, "SELECT tbluser.FirstName, tbluser.LastName, tbluser.Email, tbluser.MobileNumber, tblbook.ID AS bid, tblbook.AptNumber, tblbook.AptDate, tblbook.AptTime, tblbook.Message, tblbook.BookingDate, tblbook.Status 
                                                           FROM tblbook 
                                                           JOIN tbluser ON tbluser.ID = tblbook.UserID 
                                                           WHERE tblbook.Status = 'Rescheduled' $searchQuery");
                                $cnt = 1;
                                while ($row = mysqli_fetch_array($ret)) {
                                ?>
                                <tr>
                                    <th scope="row"><?php echo $cnt;?></th>
                                    <td><?php echo $row['AptNumber'];?></td>
                                    <td><?php echo $row['FirstName'];?> <?php echo $row['LastName'];?></td>
                                    <td><?php echo $row['MobileNumber'];?></td>
                                    <td><?php echo $row['AptDate'];?></td>
                                    <td><?php echo $row['AptTime'];?></td>
                                    <td>
                                        <?php 
                                        if($row['Status'] == "Rescheduled") {
                                            echo "<span class='text-warning'>Rescheduled</span>";
                                        } else {
                                            echo $row['Status'];
                                        }
                                        ?> 
                                    </td>
                                    <td width="150">
                                        <a href="view-appointment.php?viewid=<?php echo $row['bid'];?>" class="btn btn-primary btn-sm">View</a>
                                        <a href="rescheduled-appointment.php?delid=<?php echo $row['bid'];?>" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                    </td>
                                </tr>
                                <?php 
                                $cnt = $cnt + 1;
                                }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--footer-->
        <?php include_once('includes/footer.php');?>
        <!--//footer-->
    </div>

    <!-- Classie -->
    <script src="js/classie.js"></script>
    <script>
        var menuLeft = document.getElementById('cbp-spmenu-s1'),
            showLeftPush = document.getElementById('showLeftPush'),
            body = document.body;

        showLeftPush.onclick = function() {
            classie.toggle(this, 'active');
            classie.toggle(body, 'cbp-spmenu-push-toright');
            classie.toggle(menuLeft, 'cbp-spmenu-open');
            disableOther('showLeftPush');
        };

        function disableOther(button) {
            if (button !== 'showLeftPush') {
                classie.toggle(showLeftPush, 'disabled');
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
<?php } ?> 