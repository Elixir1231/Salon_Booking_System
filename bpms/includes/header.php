<!-- Import Stylish Font from Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">

<section class="w3l-header-4 header-sticky">
    <header class="absolute-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <!-- Brand Logo -->
                <h1 class="mb-0">
    <a class="navbar-brand" href="index.php" style="font-family: 'Dancing Script', cursive; font-size: 3rem;">
        Edna Salon
    </a>
</h1>

                <!-- Toggler button for mobile view -->
                <button class="navbar-toggler bg-gradient collapsed" type="button" data-toggle="collapse"
                    data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="fa icon-expand fa-bars"></span>
                    <span class="fa icon-close fa-times"></span>
                </button>

                <!-- Navbar Links -->
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="staff.php">Staffs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="services.php">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>

                        <!-- If the user is not logged in -->
                        <?php if (strlen($_SESSION['bpmsuid']) == 0) { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Get started!</a>
                            </li>
                        <?php } ?>

                        <!-- If the user is logged in -->
                        <?php if (strlen($_SESSION['bpmsuid']) > 0) { ?>
                            <!-- History Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="historyDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    History
                                </a>
                                <div class="dropdown-menu" aria-labelledby="historyDropdown">
                                    <a class="dropdown-item" href="invoice-history.php">Invoice History</a>
                                    <a class="dropdown-item" href="booking-history.php">Booking History</a>
                                </div>
                            </li>

                            <!-- Settings Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Settings
                                </a>
                                <div class="dropdown-menu" aria-labelledby="settingsDropdown">
                                    <a class="dropdown-item" href="profile.php">Edit Profile</a>
                                    <a class="dropdown-item" href="change-password.php">Change Password</a>
                                    <a class="dropdown-item bg-danger" style="color:white;" href="logout.php">Logout</a>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
</section>

<!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
