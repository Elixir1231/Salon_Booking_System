<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['bpmsaid']) == 0) {
  header('location:logout.php');
} else {

    if ($_GET['delid']) {
        $sid = $_GET['delid'];
        mysqli_query($con, "DELETE FROM tblbook WHERE ID ='$sid'");
        echo "<script>alert('Data Deleted');</script>";
        echo "<script>window.location.href='all-appointment.php'</script>";
    }
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>BPMS || All Appointment</title>

    <script type="application/x-javascript">
        addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
        function hideURLbar(){ window.scrollTo(0,1); }
    </script>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="css/font-awesome.css" rel="stylesheet"> 
    <!-- JS -->
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/modernizr.custom.js"></script>

    <!-- Webfonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
    
    <!-- Animate CSS -->
    <link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
    <script src="js/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>

    <!-- Metis Menu -->
    <script src="js/metisMenu.min.js"></script>
    <script src="js/custom.js"></script>
    <link href="css/custom.css" rel="stylesheet">
</head> 

<body class="cbp-spmenu-push">
    <div class="main-content">
        <!-- Left-fixed navigation -->
        <?php include_once('includes/sidebar.php');?>
        <!-- Header -->
        <?php include_once('includes/header.php');?>

        <!-- Main content start -->
        <div id="page-wrapper">
            <div class="main-page">
                <div class="tables">
                    <h3 class="title1">All Appointment</h3>

                    <div class="table-responsive bs-example widget-shadow">
                        <h4>All Appointment:</h4>
                        
                        <!-- Search Bar -->
                        <form method="GET" action="all-appointment.php" class="mb-3">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" placeholder="Search by Appointment Number, Name, or Mobile" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            </div>
                        </form>

                        <!-- Appointment Table -->
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                                    $query = "SELECT tbluser.FirstName, tbluser.LastName, tbluser.MobileNumber, tblbook.ID as bid, tblbook.AptNumber, tblbook.AptDate, tblbook.AptTime, tblbook.Status 
                                              FROM tblbook 
                                              JOIN tbluser ON tbluser.ID = tblbook.UserID 
                                              WHERE tblbook.AptNumber LIKE '%$search%' 
                                              OR tbluser.FirstName LIKE '%$search%' 
                                              OR tbluser.LastName LIKE '%$search%' 
                                              OR tbluser.MobileNumber LIKE '%$search%'";

                                    $ret = mysqli_query($con, $query);
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($ret)) {
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo $cnt; ?></th>
                                        <td><?php echo $row['AptNumber']; ?></td>
                                        <td><?php echo $row['FirstName']; ?> <?php echo $row['LastName']; ?></td>
                                        <td><?php echo $row['MobileNumber']; ?></td>
                                        <td><?php echo $row['AptDate']; ?></td>
                                        <td><?php echo $row['AptTime']; ?></td>
                                        <td>
                                            <?php echo $row['Status'] ? $row['Status'] : "Not Updated Yet"; ?>
                                        </td>
                                        <td width="150">
                                            <a href="view-appointment.php?viewid=<?php echo $row['bid'];?>" class="btn btn-primary btn-sm">View</a>
                                            <a href="all-appointment.php?delid=<?php echo $row['bid'];?>" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure you want to delete?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php 
                                    $cnt++;
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <?php include_once('includes/footer.php');?>
    </div>

    <!-- Classie JS -->
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
            if(button !== 'showLeftPush') {
                classie.toggle(showLeftPush, 'disabled');
            }
        }
    </script>

    <!-- Scrolling JS -->
    <script src="js/jquery.nicescroll.js"></script>
    <script src="js/scripts.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.js"> </script>
</body>
</html>
<?php } ?>
