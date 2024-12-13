-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2024 at 07:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bpmsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `payment_option`
--

CREATE TABLE `payment_option` (
  `payment_ID` int(11) NOT NULL,
  `payment_name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_option`
--

INSERT INTO `payment_option` (`payment_ID`, `payment_name`) VALUES
(1, 'Gcash'),
(2, 'Paypal'),
(3, 'Paymaya'),
(14, 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `staff_profiles`
--

CREATE TABLE `staff_profiles` (
  `staff_id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `specialization` text DEFAULT NULL,
  `working_days` varchar(100) NOT NULL,
  `working_hours` varchar(50) NOT NULL,
  `max_appointments_per_day` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_profiles`
--

INSERT INTO `staff_profiles` (`staff_id`, `profile_picture`, `name`, `role`, `specialization`, `working_days`, `working_hours`, `max_appointments_per_day`) VALUES
(9, 'uploads/staff/20241128184040_6748ab18b4855.jpg', 'Whiskers the Stylist', 'Senior Hair Stylist', 'Haircuts, Coloring, and Styling', 'Monday, Tuesday, Wednesday, Thursday, Friday', '9:00am - 5:00pm', 5),
(10, 'uploads/staff/20241128184229_6748ab8548d0d.jpg', 'Purrfect Polish', 'Nail Technician', 'Manicures, Pedicures, Nail Art', 'Tuesday, Wednesday, Thursday, Friday, Saturday', '10:00am - 6:00pm', 5),
(11, 'uploads/staff/20241128184355_6748abdbb2109.jpg', 'Fluffy the Facialist', 'Skincare Specialist', 'Facials, Skin Treatments, and Relaxing Massages', 'Monday, Tuesday, Wednesday, Thursday, Friday', '9:00am - 6:00pm', 5),
(12, 'uploads/staff/20241128184449_6748ac111f608.jpg', 'Meowster Massage', 'Massage Therapist', ' Aroma Oil Massage, Deep Tissue Massage', 'Wednesday, Thursday, Friday, Saturday, Sunday', '11:00am - 6:00pm', 5),
(13, 'uploads/staff/20241128184916_6748ad1c81735.jpg', 'Tabby the Colorist', 'Hair Color Specialist', 'Hair Coloring, Highlights, Balayage', 'Tuesday, Wednesday, Thursday, Friday, Saturday', '10:00am - 6:00pm', 5),
(17, 'uploads/staff/20241128185228_6748addcc2496.jpg', 'Kitty Waxington', 'Waxing Specialist', 'Full Body Waxing, Eyebrow Shaping', 'Monday, Tuesday, Wednesday, Thursday', '9:00am - 5:00pm', 5),
(18, 'uploads/staff/20241128185412_6748ae4431f34.jpg', 'Sir Paws-a-Lot', 'Body Spa Specialist', 'Body Scrubs, Relaxation Treatments, and Massages', 'Thursday, Friday, Saturday, Sunday', '10:00am - 6:00pm', 5),
(19, 'uploads/staff/20241128185531_6748ae93c23a9.jpg', 'Cattitude the Consultant', 'Salon Manager & Consultant', 'Client Consultation, Service Coordination, and Scheduling', 'Monday, Tuesday, Wednesday, Thursday, Friday', '9:00am - 5:00pm', 5);

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` char(50) DEFAULT NULL,
  `UserName` char(50) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Admin', 'admin', 7898799798, 'ramirezadriankyle@gmail.com\r\n', 'f925916e2754e5e03f75dd58a5733251', '2024-05-01 06:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `tblbook`
--

CREATE TABLE `tblbook` (
  `ID` int(10) NOT NULL,
  `UserID` int(10) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `AptNumber` int(10) DEFAULT NULL,
  `AptDate` date DEFAULT NULL,
  `AptTime` time DEFAULT NULL,
  `Message` mediumtext DEFAULT NULL,
  `BookingDate` timestamp NULL DEFAULT current_timestamp(),
  `Remark` varchar(250) DEFAULT NULL,
  `Status` varchar(250) DEFAULT NULL,
  `RemarkDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbook`
--

INSERT INTO `tblbook` (`ID`, `UserID`, `staff_id`, `AptNumber`, `AptDate`, `AptTime`, `Message`, `BookingDate`, `Remark`, `Status`, `RemarkDate`) VALUES
(49, 2, 9, 983281348, '2024-12-02', '15:58:00', 'haha', '2024-11-28 17:59:06', 'OK!', 'Selected', '2024-11-28 18:01:56'),
(50, 2, 11, 535290588, '2025-01-14', '15:58:00', 'hoho', '2024-11-28 18:05:47', 'OK!', 'Selected', '2024-11-28 18:07:48'),
(51, 2, 12, 852793973, '2024-12-04', '17:13:00', 'hehe', '2024-11-28 18:12:38', 'OK!', 'Selected', '2024-11-28 18:14:58'),
(52, 2, 18, 551795750, '2024-12-05', '14:41:00', 'asd', '2024-11-28 18:41:26', NULL, NULL, NULL),
(53, 3, 10, 874357638, '2024-11-30', '14:51:00', 'asda', '2024-11-28 18:51:24', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcontact`
--

CREATE TABLE `tblcontact` (
  `ID` int(10) NOT NULL,
  `FirstName` varchar(200) DEFAULT NULL,
  `LastName` varchar(200) DEFAULT NULL,
  `Phone` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Message` mediumtext DEFAULT NULL,
  `EnquiryDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `IsRead` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcontact`
--

INSERT INTO `tblcontact` (`ID`, `FirstName`, `LastName`, `Phone`, `Email`, `Message`, `EnquiryDate`, `IsRead`) VALUES
(1, 'Adrian Kyle', 'Ramirez', 9466821279, 'adriankyleramirez4@gmail.com', 'yo!', '2024-11-16 00:39:30', 1),
(2, 'Juan', 'Dela Cruz', 9123456789, 'juandelacruz@gmail.com', 'wazzupp', '2024-11-16 04:09:51', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblinvoice`
--

CREATE TABLE `tblinvoice` (
  `id` int(11) NOT NULL,
  `Userid` int(11) DEFAULT NULL,
  `ServiceId` int(11) DEFAULT NULL,
  `BillingId` int(11) DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp(),
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `payment_option_id` int(11) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblinvoice`
--

INSERT INTO `tblinvoice` (`id`, `Userid`, `ServiceId`, `BillingId`, `PostingDate`, `payment_status`, `payment_option_id`, `payment_date`) VALUES
(13, 2, 5, 981091867, '2024-11-28 18:09:03', 'paid', 1, '2024-11-29 02:09:30');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL,
  `PageType` varchar(200) DEFAULT NULL,
  `PageTitle` mediumtext DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` date DEFAULT NULL,
  `Timing` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `MobileNumber`, `UpdationDate`, `Timing`) VALUES
(1, 'aboutus', 'About Us', '                At our beauty salon, we’ll make you look so good, even your ghost will want to haunt you for eternity. Come for the makeover, stay because we’ve already erased all evidence of your former self—don’t worry, it’s like you were never this ugly to begin with.', NULL, NULL, NULL, ''),
(2, 'contactus', 'Contact Us', '        Seait MST Building 3rd Floor, Male Comfort Room to the Left wing', 'Ruolrap@gmail.com', 6666666666, NULL, '6:30 am to 11 pm ');

-- --------------------------------------------------------

--
-- Table structure for table `tblservices`
--

CREATE TABLE `tblservices` (
  `ID` int(10) NOT NULL,
  `ServiceName` varchar(200) DEFAULT NULL,
  `ServiceDescription` mediumtext DEFAULT NULL,
  `Cost` int(10) DEFAULT NULL,
  `Image` varchar(200) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblservices`
--

INSERT INTO `tblservices` (`ID`, `ServiceName`, `ServiceDescription`, `Cost`, `Image`, `CreationDate`) VALUES
(1, 'O3 Facial', 'A rejuvenating skincare treatment designed to deeply cleanse, hydrate, and brighten your skin. The O3 facial combats dullness, reduces pigmentation, and leaves your skin glowing with a fresh, radiant look. Perfect for all skin types!', 1200, 'o3plus-professional-bridal-facial-kit-for-radiant-glowing-skin.jpg', '2024-05-09 22:37:38'),
(2, 'Fruit Facial', 'A revitalizing treatment using natural fruit extracts to exfoliate and hydrate, leaving your skin glowing and refreshed. Perfect for a natural, healthy complexion!', 500, 'How-To-Do-Fruit-Facial-At-Home.jpg', '2024-05-09 22:37:38'),
(3, 'Charcol Facial', 'A deep-cleansing facial that uses charcoal to detoxify and purify the skin, removing impurities and excess oil for a smoother, clearer complexion. Ideal for oily or acne-prone skin.', 1000, 'b9fb9d37bdf15a699bc071ce49baea531652852364.jpg', '2024-05-09 22:37:38'),
(4, 'Deluxe Menicure', 'A luxurious manicure treatment that includes nail shaping, cuticle care, exfoliation, a relaxing hand massage, and a moisturizing mask, finished with a polish of your choice for perfectly groomed hands.', 1000, 'efc1a80c391be252d7d777a437f868701652852477.jpg', '2024-05-09 22:37:38'),
(5, 'Deluxe Pedicure', 'A premium pedicure treatment that includes nail trimming, cuticle care, exfoliation, a soothing foot soak, relaxing massage, and a moisturizing mask, finished with a polish of your choice for smooth, beautiful feet.', 1200, '1e6ae4ada992769567b71815f124fac51652852492.jpg', '2024-05-09 22:37:38'),
(6, 'Normal Menicure', 'A simple yet effective treatment that includes nail shaping, cuticle care, and a polish of your choice for neat, well-groomed nails.', 400, 'The-Dummys-Guide-To-Using-A-Manicure-Kit_OI.jpg', '2024-05-09 22:37:38'),
(7, 'Normal Pedicure', 'A basic pedicure that includes nail trimming, cuticle care, and a polish of your choice, leaving your feet looking neat and refreshed.', 500, '1e6ae4ada992769567b71815f124fac51652852492.jpg', '2024-05-09 22:37:38'),
(8, 'U-Shape Hair Cut', 'A stylish haircut with a U-shaped back that adds volume and movement to your hair, creating a soft, flattering look for all hair types.', 800, 'cff8ad28cf40ebf4fbdd383fe546098d1652852593.jpg', '2024-05-09 22:37:38'),
(9, 'Layer Haircut', 'A trendy haircut that adds texture and volume by cutting the hair in varying lengths, creating a natural, layered look that suits all hair types.', 500, '74375080377499ab76dad37484ee7f151652852649.jpg', '2024-05-09 22:37:38'),
(10, 'Rebonding', 'A chemical treatment that straightens and smooths hair, giving it a sleek, shiny, and frizz-free look. Ideal for those with curly or unruly hair.', 3000, 'c362f21370120580f5779a2d019392851652852555.jpg', '2024-05-09 22:37:38'),
(11, 'Loreal Hair Color(Full)', 'A professional hair coloring treatment using L’Oréal’s high-quality products, offering vibrant, long-lasting color that enhances shine and covers grays perfectly.', 1500, 'images.jpg', '2024-05-09 22:37:38'),
(12, 'Body Spa', 'A relaxing treatment that rejuvenates your skin and soothes your body with a combination of exfoliation, massage, and moisturizing, leaving you feeling refreshed and revitalized.', 1500, 'efc1a80c391be252d7d777a437f868701652852477.jpg', '2024-05-09 22:37:38'),
(16, 'Aroma Oil Massage Therapy', 'A therapeutic massage using essential oils to relax the body, relieve stress, and promote overall well-being. The calming scents help soothe the mind while the massage eases tension in the muscles.', 1500, '032b2cc936860b03048302d991c3498f1652173213.jpg', '2024-05-09 22:37:38');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `ID` int(10) NOT NULL,
  `FirstName` varchar(120) DEFAULT NULL,
  `LastName` varchar(250) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `Password` varchar(120) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`ID`, `FirstName`, `LastName`, `MobileNumber`, `Email`, `Password`, `RegDate`) VALUES
(1, 'John', 'Doe', 1414253612, 'johndoe@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-09-05 06:46:36'),
(2, 'Adrian Kyle', 'Ramirez', 9466821279, 'adriankyleramirez4@gmail.com', '3ba63bbde907eb04ce2ffc606151e7db', '2024-11-16 00:36:15'),
(3, 'Purrlock', 'Holmes', 1234567891, 'ramirezadriankyle@gmail.com', '202cb962ac59075b964b07152d234b70', '2024-11-28 18:50:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payment_option`
--
ALTER TABLE `payment_option`
  ADD PRIMARY KEY (`payment_ID`);

--
-- Indexes for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblbook`
--
ALTER TABLE `tblbook`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_staff_profile` (`staff_id`);

--
-- Indexes for table `tblcontact`
--
ALTER TABLE `tblcontact`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `payment_option_id` (`payment_option_id`);

--
-- Indexes for table `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblservices`
--
ALTER TABLE `tblservices`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID` (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payment_option`
--
ALTER TABLE `payment_option`
  MODIFY `payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `staff_profiles`
--
ALTER TABLE `staff_profiles`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblbook`
--
ALTER TABLE `tblbook`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tblcontact`
--
ALTER TABLE `tblcontact`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblservices`
--
ALTER TABLE `tblservices`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblbook`
--
ALTER TABLE `tblbook`
  ADD CONSTRAINT `fk_staff_profile` FOREIGN KEY (`staff_id`) REFERENCES `staff_profiles` (`staff_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblinvoice`
--
ALTER TABLE `tblinvoice`
  ADD CONSTRAINT `tblinvoice_ibfk_1` FOREIGN KEY (`payment_option_id`) REFERENCES `payment_option` (`payment_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
