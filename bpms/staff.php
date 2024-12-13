<?php 
session_start(); 
error_reporting(0); 
include('includes/dbconnection.php');   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edna Salon | Staff Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    
    <style>
        .role-section {
            margin-bottom: 40px;
            padding: 20px 0;
        }

        .role-title {
            font-size: 24px;
            color: #ff69b4;
            margin-bottom: 20px;
            padding-left: 20px;
            border-left: 4px solid #ff69b4;
        }

        .staff-outer-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            background: #fff;
            padding: 20px 0;
        }

        .staff-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 20px 0;
        }

        .staff-track {
            display: flex;
            gap: 30px;
            position: relative;
            left: 0;
            top: 0;
        }

        .staff-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 250px;
            min-height: 300px;
            flex-shrink: 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .staff-image-container {
            width: 100%;
            height: 200px;
            position: relative;
            overflow: hidden;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .staff-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .staff-info {
            display: flex;
            flex-direction: column;
            padding: 0;
        }

        .staff-name {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .staff-role {
            color: #ff69b4;
            font-size: 1.1em;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .staff-specialization {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 12px;
            flex-grow: 1;
            line-height: 1.4;
        }

        .staff-schedule {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.9em;
        }

        .schedule-title {
            font-weight: bold;
            color: #444;
            margin-bottom: 5px;
        }

        .image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            color: #666;
            font-size: 14px;
        }

        @media (max-width: 767px) {
            .staff-container {
                padding: 10px 0;
            }

            .staff-card {
                width: 220px;
                min-height: 280px;
            }

            .staff-image-container {
                height: 180px;
            }

            .role-title {
                font-size: 20px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body id="home">
    <?php include_once('includes/header.php');?>
    <script src="assets/js/jquery-3.3.1.min.js"></script> <!-- Common jquery plugin -->
<!--bootstrap working-->
<script src="assets/js/bootstrap.min.js"></script>
<!-- //bootstrap working-->
<!-- disable body scroll which navbar is in active -->
<script>
$(function () {
  $('.navbar-toggler').click(function () {
    $('body').toggleClass('noscroll');
  })
});
</script>
    <!-- breadcrumbs -->
    <section class="w3l-inner-banner-main">
        <div class="about-inner services">
            <div class="container">
                <div class="main-titles-head text-center">
                    <h3 class="header-name">Our Staff</h3>
                    <p class="tiltle-para ">Meet our team of skilled professionals dedicated to providing you with exceptional service and care. Each member brings expertise and passion to ensure you leave feeling your best.</p>
                </div>
            </div>
        </div>
        <div class="breadcrumbs-sub">
            <div class="container">
                <ul class="breadcrumbs-custom-path">
                    <li class="right-side propClone"><a href="index.php" class="">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a></li>
                    <li class="active">Our Staff</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- staff section -->
    <section class="w3l-recent-work-hobbies">
    <div class="recent-work">
        <div class="container">
            <?php
            // Fetch staff details from the database
            $ret = mysqli_query($con, "SELECT * FROM staff_profiles");
            $staffProfiles = [];

            // Group staff by role
            $staffByRole = [];
            while ($row = mysqli_fetch_array($ret)) {
                $staffByRole[$row['role']][] = $row;
            }

            // Display carousels for each role
            foreach ($staffByRole as $role => $staffMembers) {
                $roleId = preg_replace('/[^A-Za-z0-9]/', '', strtolower($role)); // Create safe ID from role
                ?>
                <div class="role-section">
                    <h4 class="role-title"><?php echo htmlspecialchars($role); ?></h4>
                    <div class="staff-outer-container">
                        <div class="staff-container" id="staffContainer_<?php echo $roleId; ?>">
                            <div class="staff-track">
                                <?php
                                // Create an array with 5 copies of the staff members
                                $duplicatedStaff = array();
                                for ($i = 0; $i < 5; $i++) {
                                    $duplicatedStaff = array_merge($duplicatedStaff, $staffMembers);
                                }

                                // Output staff profiles with 5x duplication
                                foreach ($duplicatedStaff as $staff) {
                                    $imagePath = $staff['profile_picture'];
                                    if (!empty($imagePath)) {
                                        $imagePath = ltrim($imagePath, '/');
                                        $imagePath = str_replace('uploads/', '', $imagePath);
                                        $fullImagePath = 'admin/uploads/' . $imagePath;
                                    }
                                    ?>
                                    <div class="staff-card">
                                        <div class="staff-image-container">
                                            <?php if (!empty($imagePath)) { ?>
                                                <img src="<?php echo htmlspecialchars($fullImagePath); ?>"
                                                     alt="<?php echo htmlspecialchars($staff['name']); ?>"
                                                     class="staff-image"
                                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                                <div class="image-placeholder" style="display:none;">
                                                    <span>No image available</span>
                                                </div>
                                            <?php } else { ?>
                                                <div class="image-placeholder">
                                                    <span>No image available</span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="staff-info">
                                            <h5 class="staff-name"><?php echo htmlspecialchars($staff['name']); ?></h5>
                                            <p class="staff-role"><?php echo htmlspecialchars($staff['role']); ?></p>
                                            <p class="staff-specialization"><?php echo htmlspecialchars($staff['specialization']); ?></p>
                                            <div class="staff-schedule">
                                                <p class="schedule-title">Available:</p>
                                                <p>Days: <?php echo htmlspecialchars($staff['working_days']); ?></p>
                                                <p>Hours: <?php echo htmlspecialchars($staff['working_hours']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

    <?php include_once('includes/footer.php');?>

    <!-- move top -->
    <button onclick="topFunction()" id="movetop" title="Go to top">
        <span class="fa fa-long-arrow-up"></span>
    </button>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize carousel for each role section
    document.querySelectorAll('.staff-container').forEach(container => {
        const track = container.querySelector('.staff-track');
        const cards = Array.from(track.querySelectorAll('.staff-card'));
        
        if (cards.length > 0) {
            // Set up carousel variables
            const cardWidth = cards[0].offsetWidth + 30; // Card width including margin
            let currentPosition = 0;
            const speed = 1; // Adjust speed as needed (pixels per frame)
            const totalCards = cards.length / 5; // Divide by 5 because we now have 5 copies
            
            // Set track width dynamically
            track.style.width = `${totalCards * 5 * cardWidth}px`;

            // Animation function
            function animate() {
                currentPosition -= speed;
                
                // Reset position when first set of cards is fully scrolled
                if (Math.abs(currentPosition) >= totalCards * cardWidth) {
                    currentPosition = 0;
                }
                
                track.style.transform = `translateX(${currentPosition}px)`;
                requestAnimationFrame(animate);
            }
            
            // Start the animation
            animate();
            }
        });
    });

    // Scroll to top functionality
    window.onscroll = function() {
        scrollFunction()
    };

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("movetop").style.display = "block";
        } else {
            document.getElementById("movetop").style.display = "none";
        }
    }

    function topFunction() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
    </script>
</body>
</html>