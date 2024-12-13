<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

// Define the checkDuplicateAppointment function first
function checkDuplicateAppointment($con, $staff_id, $apt_date, $apt_time) {
    try {
        // Prepare the SQL query to check for existing appointments
        $stmt = mysqli_prepare($con, 
            "SELECT COUNT(*) as count 
            FROM tblbook 
            WHERE staff_id = ? 
            AND AptDate = ? 
            AND AptTime = ? 
            AND (Status = 'Selected' OR Status = 'Accepted' OR Status IS NULL)"
        );
        
        if (!$stmt) {
            return array(
                'status' => 'error',
                'message' => 'Database preparation error: ' . mysqli_error($con)
            );
        }
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "iss", $staff_id, $apt_date, $apt_time);
        
        // Execute the query
        if (!mysqli_stmt_execute($stmt)) {
            return array(
                'status' => 'error',
                'message' => 'Database execution error: ' . mysqli_stmt_error($stmt)
            );
        }
        
        // Get the result
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        // Close the statement
        mysqli_stmt_close($stmt);
        
        if ($row['count'] > 0) {
            return array(
                'status' => 'duplicate',
                'message' => 'This time slot is already booked for the selected staff member. Please choose a different time.'
            );
        }
        
        return array(
            'status' => 'available',
            'message' => 'Time slot is available'
        );
    } catch (Exception $e) {
        return array(
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage()
        );
    }
}

if (strlen($_SESSION['bpmsuid']==0)) {
    header('location:logout.php');
} else {
    if(isset($_POST['submit'])) {
        try {
            // Debug logging
            error_log("Form submitted. POST data: " . print_r($_POST, true));
            
            $uid = $_SESSION['bpmsuid'];
            $adate = $_POST['adate'];
            $atime = $_POST['atime'];
            $staff_id = isset($_POST['staff_id']) && !empty($_POST['staff_id']) ? intval($_POST['staff_id']) : NULL;
            $msg = $_POST['message'];
            
            // Validate required fields
            if (empty($adate) || empty($atime)) {
                echo '<script>
                    alert("Please fill in all required fields");
                    window.location.href="book-appointment.php";
                </script>';
                exit();
            }
            
            // Check for duplicate appointments if staff is selected
            if ($staff_id) {
                $checkResult = checkDuplicateAppointment($con, $staff_id, $adate, $atime);
                
                if ($checkResult['status'] === 'duplicate') {
                    echo '<script>
                        alert("' . $checkResult['message'] . '");
                        window.location.href="book-appointment.php";
                    </script>';
                    exit();
                } elseif ($checkResult['status'] === 'error') {
                    echo '<script>
                        alert("Error checking appointment availability: ' . $checkResult['message'] . '");
                        window.location.href="book-appointment.php";
                    </script>';
                    exit();
                }
            }
            
            $aptnumber = mt_rand(100000000, 999999999);
            
            // Prepare the SQL query with proper NULL handling
            $stmt = mysqli_prepare($con, "INSERT INTO tblbook(UserID, staff_id, AptNumber, AptDate, AptTime, Message) VALUES (?, ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . mysqli_error($con));
            }
            
            mysqli_stmt_bind_param($stmt, "iissss", 
                $uid, 
                $staff_id, 
                $aptnumber, 
                $adate, 
                $atime, 
                $msg
            );
            
            // Execute the statement
            $query = mysqli_stmt_execute($stmt);
        
            if ($query) {
                $ret = mysqli_query($con, "SELECT AptNumber FROM tblbook WHERE UserID='$uid' ORDER BY ID DESC LIMIT 1;");
                $result = mysqli_fetch_array($ret);
                $_SESSION['aptno'] = $result['AptNumber'];
                echo "<script>window.location.href='thank-you.php'</script>";  
            } else {
                throw new Exception("Failed to book appointment: " . mysqli_error($con));
            }
        } catch (Exception $e) {
            echo '<script>
                alert("An error occurred: ' . $e->getMessage() . '");
                window.location.href="book-appointment.php";
            </script>';
        }
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Beauty Parlour Management System | Appointment Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    
    <style>
        .staff-search-container {
            position: relative;
        }
        .staff-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .staff-result-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s ease;
        }
        .staff-result-item:hover {
            background-color: #f5f5f5;
        }
        .staff-info {
            display: none;
            margin-top: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
        }
        .staff-info p {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .staff-info strong {
            color: #333;
        }
    </style>
  </head>
  <body id="home">
    <?php include_once('includes/header.php');?>

    <script src="assets/js/jquery-3.3.1.min.js"></script> <!-- Common jquery plugin -->
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
        <div class="about-inner contact">
            <div class="container">   
                <div class="main-titles-head text-center">
                    <h3 class="header-name">Book Appointment</h3>
                    <p class="tiltle-para">Choose your preferred date, time and beauty expert for your appointment.</p>
                </div>
            </div>
        </div>
        <div class="breadcrumbs-sub">
            <div class="container">   
                <ul class="breadcrumbs-custom-path">
                    <li class="right-side propClone"><a href="index.php" class="">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a></li>
                    <li class="active">Book Appointment</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- breadcrumbs //-->

    <section class="w3l-contact-info-main" id="contact">
        <div class="contact-sec">
            <div class="container">
                <div class="d-grid contact-view">
                    <div class="cont-details">
                        <?php
                        $ret=mysqli_query($con,"select * from tblpage where PageType='contactus' ");
                        $cnt=1;
                        while ($row=mysqli_fetch_array($ret)) {
                        ?>
                        <div class="cont-top">
                            <div class="cont-left text-center">
                                <span class="fa fa-phone text-primary"></span>
                            </div>
                            <div class="cont-right">
                                <h6>Call Us</h6>
                                <p class="para"><a href="tel:+<?php echo $row['MobileNumber'];?>">+<?php echo $row['MobileNumber'];?></a></p>
                            </div>
                        </div>
                        <div class="cont-top margin-up">
                            <div class="cont-left text-center">
                                <span class="fa fa-envelope-o text-primary"></span>
                            </div>
                            <div class="cont-right">
                                <h6>Email Us</h6>
                                <p class="para"><a href="mailto:<?php echo $row['Email'];?>" class="mail"><?php echo $row['Email'];?></a></p>
                            </div>
                        </div>
                        <div class="cont-top margin-up">
                            <div class="cont-left text-center">
                                <span class="fa fa-map-marker text-primary"></span>
                            </div>
                            <div class="cont-right">
                                <h6>Address</h6>
                                <p class="para"> <?php echo $row['PageDescription'];?></p>
                            </div>
                        </div>
                        <div class="cont-top margin-up">
                            <div class="cont-left text-center">
                                <span class="fa fa-clock-o text-primary"></span>
                            </div>
                            <div class="cont-right">
                                <h6>Time</h6>
                                <p class="para"> <?php echo $row['Timing'];?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="map-content-9 mt-lg-0 mt-4">
                        <form method="post" id="appointmentForm">
                        <div style="padding-top: 30px;">
                                <label>Preferred Staff</label>
                                <div class="staff-search-container">
                                    <input type="text" class="form-control" id="staffSearch" placeholder="Search for staff member" autocomplete="off">
                                    <input type="hidden" name="staff_id" id="selected_staff_id">
                                    <div class="staff-search-results"></div>
                                </div>
                                <div id="selectedStaffInfo" class="staff-info"></div>
                            </div>
                            <div style="padding-top: 30px;">
                                <label>Appointment Date</label>
                                <input type="date" class="form-control appointment_date" placeholder="Date" name="adate" id='adate' required="true">
                            </div>
                            <div style="padding-top: 30px;">
                                <label>Appointment Time</label>
                                <input type="time" class="form-control appointment_time" placeholder="Time" name="atime" id='atime' required="true">
                            </div>

                            <div style="padding-top: 30px;">
                                <textarea class="form-control" id="message" name="message" placeholder="Message" required=""></textarea>
                            </div>
                            <button type="submit" class="btn btn-contact" name="submit">Make an Appointment</button>
                        </form>
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
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Element references
    const staffSearch = document.getElementById('staffSearch');
    const searchResults = document.querySelector('.staff-search-results');
    const selectedStaffInfo = document.getElementById('selectedStaffInfo');
    const staffIdInput = document.getElementById('selected_staff_id');
    const appointmentForm = document.querySelector('form');
    const dateInput = document.getElementById('adate');
    const timeInput = document.getElementById('atime');
    
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
    // Updated form submission handling
    appointmentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const staffId = staffIdInput.value;
        const date = dateInput.value;
        const time = timeInput.value;
        const message = document.getElementById('message').value;
        
        // Basic validation
        if (!date || !time || !message) {
            alert('Please fill in all required fields');
            return;
        }

        // If staff is selected, check availability
        if (staffId) {
            try {
                const formData = new FormData();
                formData.append('staff_id', staffId);
                formData.append('apt_date', date);
                formData.append('apt_time', time);

                const response = await fetch('get_appointment_availability.php', {
                    method: 'POST',
                    body: formData
                });
                
                // Add response text logging for debugging
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                // Try to parse the response as JSON
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    alert('Invalid response from server. Please contact support.');
                    return;
                }
                
                if (result.status === 'success') {
                    // Create a hidden submit button and click it
                    const submitBtn = document.createElement('input');
                    submitBtn.type = 'hidden';
                    submitBtn.name = 'submit';
                    submitBtn.value = '1';
                    appointmentForm.appendChild(submitBtn);
                    
                    // Remove the event listener
                    appointmentForm.removeEventListener('submit', arguments.callee);
                    
                    // Trigger the native form submit
                    appointmentForm.querySelector('button[type="submit"]').click();
                } else {
                    const errorMessage = result.message + 
                        (result.details ? '\n' + JSON.stringify(result.details, null, 2) : '');
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Availability check error:', error);
                alert('Error checking availability: ' + error.message);
            }
        } else {
            // Create a hidden submit button and click it
            const submitBtn = document.createElement('input');
            submitBtn.type = 'hidden';
            submitBtn.name = 'submit';
            submitBtn.value = '1';
            appointmentForm.appendChild(submitBtn);
            
            // Remove the event listener
            appointmentForm.removeEventListener('submit', arguments.callee);
            
            // Trigger the native form submit
            appointmentForm.querySelector('button[type="submit"]').click();
        }
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!staffSearch.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Initialize scroll to top functionality
    const moveTopBtn = document.getElementById('movetop');
    if (moveTopBtn) {
        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                moveTopBtn.style.display = "block";
            } else {
                moveTopBtn.style.display = "none";
            }
        };

        moveTopBtn.onclick = function() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };
    }
});
    </script>
  </body>
</html>
<?php } ?>