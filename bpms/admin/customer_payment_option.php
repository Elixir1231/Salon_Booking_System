<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['bpmsaid']==0)) {
  header('location:logout.php');
  } else{

// Add new payment option
if(isset($_POST['submit']))
  {
    $payment_name=$_POST['payment_option'];
    
    $query=mysqli_query($con, "INSERT INTO payment_option(payment_name) VALUES ('$payment_name')");
    if ($query) {
    	echo "<script>alert('Payment option has been added.');</script>"; 
        echo "<script>window.location.href = 'customer_payment_option.php'</script>";
  }
  else
    {
    echo "<script>alert('Something Went Wrong. Please try again.');</script>";  	
    }
}

// Delete payment option
if(isset($_GET['delid']))
  {
    $id=$_GET['delid'];
    $query=mysqli_query($con,"delete from payment_option where payment_ID='$id'");
    if($query){
      echo "<script>alert('Payment option has been deleted.');</script>";
      echo "<script>window.location.href = 'customer_payment_option.php'</script>";
    } else {
      echo "<script>alert('Something Went Wrong. Please try again.');</script>";
    }
  }

// Update payment option
if(isset($_POST['update']))
  {
    $id=$_POST['payment_id'];
    $payment_name=$_POST['payment_name'];
    
    $query=mysqli_query($con,"update payment_option set payment_name='$payment_name' where payment_ID='$id'");
    if($query){
      echo "<script>alert('Payment option has been updated.');</script>";
      echo "<script>window.location.href = 'customer_payment_option.php'</script>";
    } else {
      echo "<script>alert('Something Went Wrong. Please try again.');</script>";
    }
  }
  ?>
<!DOCTYPE HTML>
<html>
<head>
<title>BPMS | Manage Payment Options</title>

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
					<h3 class="title1">Manage Payment Options</h3>
					<div class="form-grids row widget-shadow" data-example-id="basic-forms"> 
						<div class="form-title">
							<h4>Add New Payment Option:</h4>
						</div>
						<div class="form-body">
							<form method="post">
								<p style="font-size:16px; color:red" align="center"> <?php if($msg){
    echo $msg;
  }  ?> </p>

							  <div class="form-group"> 
                  <label for="payment_option">Payment Option Name</label> 
                  <input type="text" id="payment_option" name="payment_option" class="form-control" placeholder="Enter Payment Option" value="" required="true"> 
              </div>
							  <button type="submit" name="submit" class="btn btn-default">Add</button> 
              </form> 
						</div>
					</div>

<!-- Display Payment Options -->
<div class="form-grids row widget-shadow mt-4" data-example-id="basic-forms">
    <div class="form-title">
        <h4>Existing Payment Options:</h4>
    </div>
    <div class="form-body">
        <div class="table-responsive scrollable-table"> <!-- Add the scrollable-table class here -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Payment Option</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ret=mysqli_query($con,"select * from payment_option");
                    $cnt=1;
                    while ($row=mysqli_fetch_array($ret)) {
                    ?>
                    <tr id="payment_row_<?php echo $row['payment_ID'];?>">
                        <td><?php echo $row['payment_ID'];?></td>
                        <td>
                            <span id="payment_text_<?php echo $row['payment_ID'];?>"><?php echo $row['payment_name'];?></span>
                            <form method="post" class="edit-form" id="edit_form_<?php echo $row['payment_ID'];?>" style="display:none;">
                                <input type="hidden" name="payment_id" value="<?php echo $row['payment_ID'];?>">
                                <div class="input-group">
                                    <input type="text" name="payment_name" value="<?php echo $row['payment_name'];?>" class="form-control">
                                    <div class="input-group-append">
                                        <button type="submit" name="update" class="btn btn-success">Save</button>
                                        <button type="button" class="btn btn-secondary" onclick="cancelEdit(<?php echo $row['payment_ID'];?>)">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" onclick="editPayment(<?php echo $row['payment_ID'];?>)">Edit</button>
                            <a href="?delid=<?php echo $row['payment_ID'];?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payment option?');">Delete</a>
                        </td>
                    </tr>
                    <?php 
                    $cnt=$cnt+1;
                    }?>
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

      // Edit payment option
      function editPayment(id) {
        document.getElementById('payment_text_' + id).style.display = 'none';
        document.getElementById('edit_form_' + id).style.display = 'block';
      }

      // Cancel edit
      function cancelEdit(id) {
        document.getElementById('payment_text_' + id).style.display = 'block';
        document.getElementById('edit_form_' + id).style.display = 'none';
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