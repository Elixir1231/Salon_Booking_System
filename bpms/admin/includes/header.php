<?php
include('emailnotif.php');
?>
<div class="sticky-header header-section">
    <div class="header-left">
        <!--toggle button start-->
        <button id="showLeftPush"><i class="fa fa-bars"></i></button>
        <!--toggle button end-->

        <!--logo -->
        <div class="logo">
            <a href="index.html">
                <h1>Edna Salon</h1>
                <span>AdminPanel</span>
            </a>
        </div>
        <!--//logo-->

        <div class="clearfix"></div>
    </div>

    <div class="header-right">
        <div class="profile_details_left">
            <!--notifications of menu start -->
            <ul class="nofitications-dropdown">
                <?php
                
                // Fetch new appointments, ordered by ID descending (latest first)
                $ret1 = mysqli_query($con, "SELECT tbluser.FirstName, tbluser.LastName, tblbook.ID as bid, tblbook.AptNumber 
                                            FROM tblbook 
                                            JOIN tbluser ON tbluser.ID = tblbook.UserID 
                                            WHERE tblbook.Status IS NULL
                                            ORDER BY tblbook.ID DESC");
                $numNewAppointments = mysqli_num_rows($ret1);

                // Fetch rescheduled appointments, ordered by ID descending (latest first)
                $ret2 = mysqli_query($con, "SELECT tbluser.FirstName, tbluser.LastName, tblbook.ID as bid, tblbook.AptNumber 
                                            FROM tblbook 
                                            JOIN tbluser ON tbluser.ID = tblbook.UserID 
                                            WHERE tblbook.Status = 'Rescheduled'
                                            ORDER BY tblbook.ID DESC");
                $numRescheduledAppointments = mysqli_num_rows($ret2);
                ?>

                <li class="dropdown head-dpdn">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        <span class="badge blue"><?php echo $numNewAppointments + $numRescheduledAppointments; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="notification_header">
                                <h3>You have <?php echo $numNewAppointments + $numRescheduledAppointments; ?> new notifications</h3>
                            </div>
                        </li>

                        <!-- New Appointments -->
                        <li>
                            <div class="notification_desc">
                                <?php if ($numNewAppointments > 0) {
                                    while ($result = mysqli_fetch_array($ret1)) { ?>
                                        <a class="dropdown-item" href="view-appointment.php?viewid=<?php echo $result['bid']; ?>">
                                            New appointment received from <?php echo $result['FirstName']; ?> <?php echo $result['LastName']; ?> (<?php echo $result['AptNumber']; ?>)
                                        </a>
                                        <hr />
                                <?php }
                                } else { ?>
                                    <a class="dropdown-item" href="all-appointment.php">notification empty</a>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <hr>

                        <!-- Rescheduled Appointments -->
                        <li>
                            <div class="notification_desc">
                                <?php if ($numRescheduledAppointments > 0) {
                                    while ($result = mysqli_fetch_array($ret2)) { ?>
                                        <a class="dropdown-item" href="view-appointment.php?viewid=<?php echo $result['bid']; ?>">
                                            Appointment rescheduled by <?php echo $result['FirstName']; ?> <?php echo $result['LastName']; ?> (<?php echo $result['AptNumber']; ?>)
                                        </a>
                                        <hr />
                                <?php }
                                } else { ?>
                                    <a class="dropdown-item" href="all-appointment.php">notification empty</a>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                        </li>

                        <li>
                            <div class="notification_bottom">
                                <a href="new-appointment.php">See all notifications</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <!--notification menu end -->

        <div class="profile_details">
            <?php
            $adid = $_SESSION['bpmsaid'];
            $ret = mysqli_query($con, "SELECT AdminName FROM tbladmin WHERE ID='$adid'");
            $row = mysqli_fetch_array($ret);
            $name = $row['AdminName'];
            ?>
            <ul>
                <li class="dropdown profile_details_drop">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <div class="profile_img">
                            <span class="prfil-img">
                                <img src="images/admin.png" alt="" width="50" height="50">
                            </span>
                            <div class="user-name">
                                <p><?php echo $name; ?></p>
                                <span>Administrator</span>
                            </div>
                            <i class="fa fa-angle-down lnr"></i>
                            <i class="fa fa-angle-up lnr"></i>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu drp-mnu">
                        <li><a href="change-password.php"><i class="fa fa-cog"></i> Settings</a></li>
                        <li><a href="admin-profile.php"><i class="fa fa-user"></i> Profile</a></li>
                        <li><a href="index.php"><i class="fa fa-sign-out"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="clearfix"></div>
    </div>

    <div class="clearfix"></div>
</div>
