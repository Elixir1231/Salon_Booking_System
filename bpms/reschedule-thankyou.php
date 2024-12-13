<?php  
session_start(); 
error_reporting(0); 
include('includes/dbconnection.php'); 
if (strlen($_SESSION['bpmsuid']==0)) {   
  header('location:logout.php');   
} else {
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Edna Salon | Reschedule Confirmation</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    
    <style>
      .reschedule-details {
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        padding: 20px;
        margin-top: 20px;
        border-radius: 5px;
      }
      .reschedule-details p {
        margin-bottom: 10px;
      }
    </style>
  </head>
  <body id="home">
    <?php include_once('includes/header.php');?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
    <script>
    $(function () {
      $('.navbar-toggler').click(function () {
        $('body').toggleClass('noscroll');
      })
    });
    </script>
    
    <!-- breadcrumbs -->
    <section class="w3l-inner-banner-main">
      <div class="about-inner contact ">
        <div class="container">
          <div class="main-titles-head text-center">
            <h3 class="header-name ">Appointment Rescheduled</h3>
          </div>
        </div>
      </div>
      <div class="breadcrumbs-sub">
        <div class="container">
          <ul class="breadcrumbs-custom-path">
            <li class="right-side propClone">
              <a href="index.php" class="">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a>
            </li>
            <li class="active ">Reschedule Confirmation</li>
          </ul>
        </div>
      </div>
    </section>
    <!-- breadcrumbs //-->
    
    <section class="w3l-contact-info-main" id="contact">
      <div class="contact-sec">
        <div class="container">
          <div>
            <h4 class="w3ls_head">
              Thank you for rescheduling your appointment.
            </h4>
            
            <?php
            // Fetch the rescheduled appointment details
            $userid = $_SESSION['bpmsuid'];
            $query = mysqli_query($con, "SELECT * FROM tblbook 
                                         WHERE UserID='$userid' 
                                         AND Status='Rescheduled' 
                                         ORDER BY ID DESC 
                                         LIMIT 1");
            $appointment = mysqli_fetch_assoc($query);
            
            if ($appointment) {
            ?>
            <div class="reschedule-details">
              <p><strong>Appointment Number:</strong> <?php echo $appointment['AptNumber']; ?></p>
              <p><strong>New Appointment Date:</strong> <?php echo $appointment['AptDate']; ?></p>
              <p><strong>New Appointment Time:</strong> <?php echo $appointment['AptTime']; ?></p>
              
              <?php if(!empty($appointment['staff_id'])): ?>
                <?php 
                // Fetch staff details
                $staffQuery = mysqli_query($con, "SELECT name FROM staff_profiles WHERE staff_id='" . $appointment['staff_id'] . "'");
                $staffDetails = mysqli_fetch_assoc($staffQuery);
                ?>
                <p><strong>Preferred Staff:</strong> <?php echo $staffDetails['name']; ?></p>
              <?php endif; ?>
            </div>
            <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <?php include_once('includes/footer.php');?>
    
    <!-- move top -->
    <button onclick="topFunction()" id="movetop" title="Go to top">
      <span class="fa fa-long-arrow-up"></span>
    </button>
    
    <script>
      // When the user scrolls down 20px from the top of the document, show the button
      window.onscroll = function () {
        scrollFunction()
      };

      function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
          document.getElementById("movetop").style.display = "block";
        } else {
          document.getElementById("movetop").style.display = "none";
        }
      }

      // When the user clicks on the button, scroll to the top of the document
      function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
      }
    </script>
    <!-- /move top -->
  </body>
</html>
<?php } ?>