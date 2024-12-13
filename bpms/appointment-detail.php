<?php 
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['bpmsuid']==0)) {
  header('location:logout.php');
  } else{

// Handle Reschedule Request
if (isset($_POST['reschedule'])) {
  $aptNumber = $_POST['aptnumber'];
  $newDate = $_POST['new_date'];
  $newTime = $_POST['new_time'];
  $staffId = $_POST['staff_id'] ?? null;

  // Validate input
  $errors = [];
  
  // Date validation
  if (empty($newDate)) {
      $errors[] = "Please select a new appointment date.";
  }
  
  // Time validation
  if (empty($newTime)) {
      $errors[] = "Please select a new appointment time.";
  }

  // If there are validation errors
  if (!empty($errors)) {
      $errorMsg = implode("<br>", $errors);
  } else {
      // Fetch the current user's ID from the session
      $userid = $_SESSION['bpmsuid'];

      // Prepare the update query with parameterized statements
      $updateQuery = "UPDATE tblbook 
                      SET AptDate = ?, 
                          AptTime = ?, 
                          staff_id = ?, 
                          Status = 'Rescheduled' 
                      WHERE AptNumber = ? AND UserID = ?";

      // Prepare the statement
      $stmt = mysqli_prepare($con, $updateQuery);

      if ($stmt) {
          // Bind parameters
          // Determine staff_id binding - use NULL if no staff selected
          $boundStaffId = $staffId ? $staffId : NULL;
          mysqli_stmt_bind_param($stmt, "sssii", 
              $newDate, 
              $newTime, 
              $boundStaffId, 
              $aptNumber, 
              $userid
          );

          // Execute the statement
          if (mysqli_stmt_execute($stmt)) {
              // Check if any rows were actually updated
              if (mysqli_stmt_affected_rows($stmt) > 0) {
                  // Store appointment number in session for thank you page
                  $_SESSION['aptno'] = $aptNumber;
                  
                  // Log the rescheduling action (optional)
                  $logQuery = "INSERT INTO appointment_logs 
                               (AptNumber, UserID, Action, ActionDate) 
                               VALUES (?, ?, 'Rescheduled', NOW())";
                  $logStmt = mysqli_prepare($con, $logQuery);
                  if ($logStmt) {
                      mysqli_stmt_bind_param($logStmt, "ii", $aptNumber, $userid);
                      mysqli_stmt_execute($logStmt);
                      mysqli_stmt_close($logStmt);
                  }

                  // Redirect to thank you page
                  header("Location: reschedule-thankyou.php");
                  exit();
              } else {
                  $errorMsg = "No changes were made. Please check the appointment details.";
              }
          } else {
              // Database execution error
              $errorMsg = "Failed to reschedule appointment: " . mysqli_stmt_error($stmt);
          }

          // Close the statement
          mysqli_stmt_close($stmt);
      } else {
          // Statement preparation error
          $errorMsg = "Error preparing the database statement: " . mysqli_error($con);
      }
  }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Beauty Parlour Management System | Booking History</title>

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    
    <style>
        .reschedule-form {
            margin-top: 20px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        .error-msg {
            color: red;
            margin-bottom: 10px;
        }
        .success-msg {
            color: green;
            margin-bottom: 10px;
        }
        .not-eligible {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
    </style>
  </head>
  <body id="home">
<?php include_once('includes/header.php');?>

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
            <h3 class="header-name ">Booking History</h3>
            <p class="tiltle-para ">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Hic fuga sit illo modi aut aspernatur tempore laboriosam saepe dolores eveniet.</p>
        </div>
</div>
</div>
<div class="breadcrumbs-sub">
<div class="container">   
<ul class="breadcrumbs-custom-path">
    <li class="right-side propClone"><a href="index.php" class="">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a> <p></li>
    <li class="active ">Booking History</li>
</ul>
</div>
</div>
    </div>
</section>
<!-- breadcrumbs //-->
<section class="w3l-contact-info-main" id="contact">
    <div class="contact-sec	">
        <div class="container">
            <div>
                <div class="cont-details">
                   <div class="table-content table-responsive cart-table-content m-t-30">
                   <h4 style="padding-bottom: 20px;text-align: center;color: blue;">Appointment Details</h4>
                        <?php
$cid=$_GET['aptnumber'];
$ret=mysqli_query($con,"SELECT 
    tbluser.FirstName, 
    tbluser.LastName, 
    tbluser.Email, 
    tbluser.MobileNumber, 
    tblbook.ID as bid, 
    tblbook.AptNumber, 
    tblbook.AptDate, 
    tblbook.AptTime, 
    tblbook.Message, 
    tblbook.BookingDate, 
    tblbook.Remark, 
    tblbook.Status, 
    tblbook.RemarkDate,
    staff_profiles.name AS PreferredStaff
FROM tblbook 
JOIN tbluser ON tbluser.ID=tblbook.UserID 
LEFT JOIN staff_profiles ON tblbook.staff_id = staff_profiles.staff_id 
WHERE tblbook.AptNumber='$cid'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {
    // Determine if rescheduling is possible
    $isReschedulePossible = ($row['Status'] == '' || $row['Status'] == 'Waiting for confirmation' || $row['Status'] == 'Rescheduled');
?>
                        <table class="table table-bordered">
                            <tr>
    <th>Appointment Number</th>
    <td><?php  echo $row['AptNumber'];?></td>
  </tr>
  <tr>
<th>Name</th>
    <td><?php  echo $row['FirstName'];?> <?php  echo $row['LastName'];?></td>
  </tr>

<tr>
    <th>Email</th>
    <td><?php  echo $row['Email'];?></td>
  </tr>
   <tr>
    <th>Mobile Number</th>
    <td><?php  echo $row['MobileNumber'];?></td>
  </tr>

  <tr>
    <th>Preferred Staff</th>
    <td><?php echo $row['PreferredStaff'] ? $row['PreferredStaff'] : 'Not Specified'; ?></td>
</tr>

   <tr>
    <th>Appointment Date</th>
    <td><?php  echo $row['AptDate'];?></td>
  </tr>
 
<tr>
    <th>Appointment Time</th>
    <td><?php  echo $row['AptTime'];?></td>
  </tr>
  
  
  <tr>
    <th>Apply Date</th>
    <td><?php  echo $row['BookingDate'];?></td>
  </tr>
  

<tr>
    <th>Status</th>
    <td> <?php  
if($row['Status']=="")
{
  echo "Waiting for confirmation";
}

if($row['Status']=="Selected")
{
  echo "Selected";
}

if($row['Status']=="Rejected")
{
  echo "Rejected";
}
if($row['Status']=="Rescheduled")
{
  echo "Rescheduled";
}

     ;?></td>
  </tr>
                        </table>
                        
<!-- Reschedule Section -->
<?php if($isReschedulePossible) { ?>
<div class="reschedule-form">
    <h4 style="text-align: center; color: blue;">Reschedule Appointment</h4>
    
    <?php 
    // Display error or success messages
    if(isset($errorMsg)) {
        echo "<div class='error-msg'>$errorMsg</div>";
    }
    if(isset($successMsg)) {
        echo "<div class='success-msg'>$successMsg</div>";
    }
    
    // Fetch available staff for selection
    $staffQuery = mysqli_query($con, "SELECT staff_id, name, role, specialization FROM staff_profiles");
    ?>
    
    <form method="POST" action="" id="rescheduleForm" onsubmit="return validateForm()">
    <input type="hidden" name="aptnumber" value="<?php echo $row['AptNumber']; ?>">
    
    <div class="form-group row">
            <label class="col-sm-3 col-form-label">Current Preferred Staff</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" value="<?php echo $row['PreferredStaff']; ?>" disabled>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Select New Preferred Staff</label>
            <div class="col-sm-9">
                <div class="staff-search-container">
                    <input type="text" class="form-control" id="staffSearch" placeholder="Search for staff member" autocomplete="off">
                    <input type="hidden" name="staff_id" id="selected_staff_id">
                    <div class="staff-search-results"></div>
                </div>
                <div id="selectedStaffInfo" class="staff-info"></div>
            </div>
        </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">New Appointment Date</label>
        <div class="col-sm-9">
            <input type="date" name="new_date" class="form-control" 
                   min="<?php echo date('Y-m-d'); ?>" 
                   value="<?php echo $row['AptDate']; ?>" 
                   required>
        </div>
    </div>
    
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">New Appointment Time</label>
        <div class="col-sm-9">
            <input type="time" name="new_time" class="form-control" 
                   value="<?php echo $row['AptTime']; ?>" 
                   required>
        </div>
    </div>

    <!-- Staff Selection as before -->
    <div class="form-group row">
        <div class="col-sm-12 text-center">
            <button type="submit" name="reschedule" class="btn btn-primary">
                Confirm Reschedule
            </button>
        </div>
    </div>
</form>
</div>
<?php } else { ?>
    <div class="not-eligible">
        Rescheduling is not available.
    </div>
<?php } ?>
                        <?php } ?>
                    </div> 
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
document.addEventListener('DOMContentLoaded', function() {
    const staffSearch = document.getElementById('staffSearch');
    const searchResults = document.querySelector('.staff-search-results');
    const selectedStaffInfo = document.getElementById('selectedStaffInfo');
    const staffIdInput = document.getElementById('selected_staff_id');
    const rescheduleForm = document.querySelector('form');
    const dateInput = document.querySelector('input[name="new_date"]');
    const timeInput = document.querySelector('input[name="new_time"]');
    const appointmentNumber = document.querySelector('input[name="aptnumber"]').value;

    // Set minimum date for appointment to today
    function setMinimumDate() {
        const dtToday = new Date();
        let month = dtToday.getMonth() + 1;
        let day = dtToday.getDate();
        const year = dtToday.getFullYear();
        
        month = month < 10 ? '0' + month : month;
        day = day < 10 ? '0' + day : day;
        
        const maxDate = year + '-' + month + '-' + day;
        dateInput.setAttribute('min', maxDate);
    }

    // Initialize minimum date
    setMinimumDate();

    // Debounce function to limit API calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Function to fetch staff data
    const fetchStaffData = debounce(async (searchTerm) => {
        try {
            const response = await fetch(`get_staff.php?search=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();
            
            // Clear previous results
            searchResults.innerHTML = '';
            
            if (result.status === 'success' && result.data.length > 0) {
                result.data.forEach(staff => {
                    const div = document.createElement('div');
                    div.className = 'staff-result-item';
                    div.innerHTML = `
                        <div class="d-flex align-items-center">
                            <div>
                                <div>${staff.name}</div>
                                <small class="text-muted">${staff.role}</small>
                            </div>
                        </div>`;
                    
                    div.addEventListener('click', () => {
                        selectStaff(staff);
                        searchResults.style.display = 'none';
                    });
                    
                    searchResults.appendChild(div);
                });
                searchResults.style.display = 'block';
            } else {
                const div = document.createElement('div');
                div.className = 'staff-result-item';
                div.textContent = 'No staff members found';
                searchResults.appendChild(div);
            }
        } catch (error) {
            console.error('Error fetching staff data:', error);
            searchResults.innerHTML = '<div class="staff-result-item">Error fetching staff data</div>';
        }
    }, 300);

    // Add input event listener for staff search
    staffSearch.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        if (searchTerm.length < 1) {
            searchResults.style.display = 'none';
            staffIdInput.value = '';
            selectedStaffInfo.innerHTML = '';
            selectedStaffInfo.style.display = 'none';
            return;
        }
        
        fetchStaffData(searchTerm);
    });

    // Function to select staff
    function selectStaff(staff) {
        staffSearch.value = staff.name;
        staffIdInput.value = staff.staff_id;

        selectedStaffInfo.innerHTML = `
            <div class="d-flex align-items-start">
                <div>
                    <p><strong>Name:</strong> ${staff.name}</p>
                    <p><strong>Role:</strong> ${staff.role}</p>
                    <p><strong>Specialization:</strong> ${staff.specialization}</p>
                    <p><strong>Working Days:</strong> ${staff.working_days}</p>
                    <p><strong>Working Hours:</strong> ${staff.working_hours}</p>
                    <p><strong>Maximum Appointments/Day:</strong> ${staff.max_appointments_per_day}</p>
                </div>
            </div>`;
        selectedStaffInfo.style.display = 'block';
        
        // Setup validations
        setupTimeValidation(staff);
        setupDateValidation(staff);
    }

    // Time validation setup
    function setupTimeValidation(staff) {
        const [startTime, endTime] = staff.working_hours.split(' - ');
        
        timeInput.addEventListener('change', function() {
            const selectedTime = this.value;
            if (!selectedTime) return;

            const [hours, minutes] = selectedTime.split(':');
            const selectedDate = new Date();
            selectedDate.setHours(hours, minutes);

            const [startHours, startMinutes] = startTime.replace('am', '').replace('pm', '').split(':');
            const [endHours, endMinutes] = endTime.replace('am', '').replace('pm', '').split(':');

            const startDate = new Date();
            const endDate = new Date();

            // Convert to 24-hour format
            if (startTime.includes('pm') && startHours !== '12') {
                startDate.setHours(parseInt(startHours) + 12, parseInt(startMinutes));
            } else {
                startDate.setHours(parseInt(startHours), parseInt(startMinutes));
            }

            if (endTime.includes('pm') && endHours !== '12') {
                endDate.setHours(parseInt(endHours) + 12, parseInt(endMinutes));
            } else {
                endDate.setHours(parseInt(endHours), parseInt(endMinutes));
            }

            if (selectedDate < startDate || selectedDate > endDate) {
                alert(`Please select a time between ${staff.working_hours}`);
                this.value = '';
            }
        });
    }

    // Date validation setup
    function setupDateValidation(staff) {
        const workingDays = staff.working_days.split(', ');
        
        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const dayOfWeek = selectedDate.toLocaleDateString('en-US', { weekday: 'long' });
            
            if (!workingDays.includes(dayOfWeek)) {
                alert(`Selected staff does not work on ${dayOfWeek}s.\nAvailable days: ${staff.working_days}`);
                this.value = '';
            }
        });
    }

    // Updated form submission handling
    rescheduleForm.addEventListener('submit', function(e) {
        // Remove any previous hidden inputs
        const oldHiddenInputs = rescheduleForm.querySelectorAll('input[name="reschedule"]');
        oldHiddenInputs.forEach(input => input.remove());

        // Create a hidden input to trigger reschedule
        const submitInput = document.createElement('input');
        submitInput.type = 'hidden';
        submitInput.name = 'reschedule';
        submitInput.value = '1';
        rescheduleForm.appendChild(submitInput);

        // Ensure all required inputs have values
        const requiredInputs = [dateInput, timeInput];
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.value) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        // If staff is selected, validate its input
        if (staffSearch.value && !staffIdInput.value) {
            staffSearch.classList.add('is-invalid');
            isValid = false;
        } else {
            staffSearch.classList.remove('is-invalid');
        }

        // If validation fails, prevent submission
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields correctly.');
            return false;
        }

        // All good, form will submit
        return true;
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!staffSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});

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
  function validateForm() {
    const dateInput = document.querySelector('input[name="new_date"]');
    const timeInput = document.querySelector('input[name="new_time"]');
    
    // Basic validation
    if (!dateInput.value) {
        alert('Please select a new date');
        return false;
    }
    
    if (!timeInput.value) {
        alert('Please select a new time');
        return false;
    }
    
    // Optional: Additional date/time validation
    const selectedDate = new Date(dateInput.value);
    const today = new Date();
    
    if (selectedDate < today) {
        alert('Please select a date in the future');
        return false;
    }
    
    return true;
}
</script>
<!-- /move top -->
</body>
</html><?php } ?>