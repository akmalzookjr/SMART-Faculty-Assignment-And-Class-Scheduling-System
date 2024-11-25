-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 11:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `assign_schedule`
--

CREATE TABLE `assign_schedule` (
  `Assign_Sche_ID` int(11) NOT NULL,
  `Sche_ID` int(11) NOT NULL,
  `Section_ID` int(11) NOT NULL,
  `Course_Section_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assign_schedule`
--

INSERT INTO `assign_schedule` (`Assign_Sche_ID`, `Sche_ID`, `Section_ID`, `Course_Section_ID`) VALUES
(63, 10, 13, 5),
(64, 11, 13, 5),
(65, 20, 13, 6),
(66, 28, 13, 5),
(67, 29, 13, 5),
(70, 37, 13, 6),
(72, 41, 13, 7),
(73, 42, 13, 7),
(74, 45, 13, 4),
(75, 46, 13, 4),
(76, 6, 14, 8),
(77, 7, 14, 8),
(78, 8, 14, 9),
(79, 9, 14, 9),
(80, 17, 14, 9),
(82, 29, 14, 10),
(83, 41, 14, 8),
(84, 42, 14, 8),
(85, 32, 14, 11),
(86, 47, 14, 11),
(87, 48, 14, 11),
(94, 3, 14, 4),
(109, 38, 13, 6),
(112, 1, 11, 4),
(113, 2, 11, 31),
(114, 3, 11, 13),
(115, 4, 11, 4),
(116, 5, 11, 4),
(117, 12, 11, 4),
(118, 13, 11, 4),
(119, 14, 11, 4),
(120, 15, 11, 4),
(121, 16, 11, 4),
(124, 2, 14, 4),
(129, 23, 11, 9),
(130, 34, 11, 34),
(131, 45, 11, 34),
(133, 4, 13, 5),
(134, 47, 13, 4),
(135, 9, 13, 4),
(136, 45, 15, 7);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `Course_ID` int(11) NOT NULL,
  `Course_Name` varchar(100) NOT NULL,
  `Course_CH` int(50) DEFAULT NULL,
  `Course_Code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`Course_ID`, `Course_Name`, `Course_CH`, `Course_Code`) VALUES
(1, 'LINUX OS', 3, 'ARC3043'),
(2, 'COMPUTING PROJECT', 4, 'FYP3024'),
(3, 'INTRODUCTION TO MOBILE APPLICATION', 3, 'SWC3403'),
(4, 'ENTREPRENUERSHIP WITH DIGITAL APPLICATION', 3, 'UCS2083'),
(5, 'COMPUTER ORGANIZATION AND ARCHITECTURE', 3, 'ARC1033'),
(6, 'OPERATING SYSTEMS', 3, 'ARC1043'),
(7, 'GENERAL ENGLISH PROFICIENCY', 3, 'EGN2103'),
(8, 'COMPUTING MATHEMATICS', 3, 'MAT1063'),
(9, 'PENGAJIAN MALAYSIA 2', 2, 'MPU2162'),
(10, 'FUNDAMENTAL OF PROGRAMMING', 3, 'SWC1323'),
(11, 'STUDY SKILLS', 2, 'UCS2072'),
(12, 'LEADERSHIP AND INTERPERSONAL SKILLS 1', 2, 'MPU2242'),
(13, 'STATISTICS', 3, 'STA2093'),
(14, 'OBJECT-ORIENTED PROGRAMMING', 3, 'SWC2333'),
(15, 'BUSINESS INFORMATION MANAGAMENT STRATEGY', 3, 'ITC1083'),
(16, 'DATABASE CONCEPTS', 3, 'ITC2143'),
(17, 'CALCULUS', 4, 'MAT2024'),
(18, 'PENGAJIAN ISLAM 2', 3, 'MPU2353'),
(19, 'DATA STRUCTURE', 4, 'SWC3344'),
(20, 'HUMAN COMPUTER INTERACTION', 3, 'MMC1123'),
(21, 'KHIDMAT MASYARAKAT 1', 2, 'MPU2412'),
(22, 'DATA COMMUNICATION CONCEPT', 3, 'NWC2063'),
(23, 'WEB DESIGN', 3, 'SWC2353'),
(24, 'WEB APPLICATION DEVELOPMENT', 3, 'SWC2363'),
(25, 'EXPRESS APPLICATION DEVELOPMENT', 3, 'SWC2383'),
(26, 'INTRODUCTION TO NETWORKS', 3, 'NWC2043'),
(27, 'COMPUTER NETWORK SECURITY', 3, 'NWC3053'),
(28, 'SYSTEM ANALYSIS AND DESIGN (SAD)', 3, 'SWC3393'),
(30, 'INFORMATION TECHNOLOGY ESSENTIALS', 3, 'ITC2193'),
(31, 'EMERGING TECHNOLOGIES', 3, 'SWC2373'),
(32, 'ENTERPRISE INFORMATION SYSTEMS', 3, 'ITC2173'),
(45, 'SCIENCE', 4, 'SCE2003');

-- --------------------------------------------------------

--
-- Table structure for table `course_section`
--

CREATE TABLE `course_section` (
  `Course_Section_ID` int(11) NOT NULL,
  `Course_ID` int(11) NOT NULL,
  `Course_Section` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_section`
--

INSERT INTO `course_section` (`Course_Section_ID`, `Course_ID`, `Course_Section`) VALUES
(4, 1, 1),
(13, 1, 2),
(31, 1, 3),
(32, 1, 4),
(33, 1, 5),
(35, 1, 6),
(36, 1, 7),
(37, 1, 8),
(38, 1, 9),
(41, 1, 10),
(42, 1, 11),
(43, 1, 12),
(44, 1, 13),
(57, 1, 14),
(102, 1, 15),
(7, 2, 1),
(10, 2, 2),
(34, 2, 3),
(5, 3, 1),
(8, 3, 2),
(6, 4, 6),
(9, 4, 7),
(73, 4, 8),
(17, 8, 1),
(1, 17, 1),
(3, 17, 2),
(16, 17, 3),
(18, 19, 1),
(11, 30, 1),
(53, 32, 1),
(103, 45, 1),
(104, 45, 2),
(105, 45, 3),
(106, 45, 4),
(107, 45, 5);

-- --------------------------------------------------------

--
-- Table structure for table `course_student`
--

CREATE TABLE `course_student` (
  `Course_Stud_ID` int(11) NOT NULL,
  `Course_ID` int(11) NOT NULL,
  `Stud_ID` int(11) NOT NULL,
  `Assignment_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_student`
--

INSERT INTO `course_student` (`Course_Stud_ID`, `Course_ID`, `Stud_ID`, `Assignment_ID`) VALUES
(14, 2, 21, NULL),
(77, 2, 3, NULL),
(80, 4, 23, NULL),
(85, 2, 23, NULL),
(86, 2, 1, NULL),
(87, 4, 1, NULL),
(88, 4, 24, NULL),
(89, 3, 24, NULL),
(90, 6, 25, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deputydean`
--

CREATE TABLE `deputydean` (
  `Dean_ID` int(11) NOT NULL,
  `Dean_Name` varchar(30) NOT NULL,
  `Dean_Email` varchar(200) NOT NULL,
  `Dean_Password` char(255) NOT NULL,
  `Dean_CH` int(50) DEFAULT NULL,
  `type` enum('dean','coordinator') NOT NULL DEFAULT 'dean'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deputydean`
--

INSERT INTO `deputydean` (`Dean_ID`, `Dean_Name`, `Dean_Email`, `Dean_Password`, `Dean_CH`, `type`) VALUES
(1, 'Akmal', 'Akmalzookjr@gmail.com', 'Akm@l1976', NULL, 'dean'),
(2, 'Dean', 'Dean@gmail.com', '1234', 20, 'dean'),
(3, 'Coordinator', 'Coordinator@gmail.com', '1234', 20, 'coordinator');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

CREATE TABLE `lecturer` (
  `Lect_ID` int(11) NOT NULL,
  `Lect_Name` varchar(100) NOT NULL,
  `Lect_Email` varchar(200) NOT NULL,
  `Lect_Password` char(255) NOT NULL,
  `Lect_CH` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`Lect_ID`, `Lect_Name`, `Lect_Email`, `Lect_Password`, `Lect_CH`) VALUES
(10, 'DR.MARIANI', 'Mariani@gmail.com', '1234', 12),
(15, 'MUHAMMAD AKMAL BIN MARZUKI', 'akmalzookjr@gmail.comm', 'akmal1234', 22),
(16, 'a', 'a@gmail.com', '1234', 1),
(17, 'b', 'b@gmail.com', 'a', 22),
(30, 'lect1', 'lect1@gmail.com', '1234', 12);

-- --------------------------------------------------------

--
-- Table structure for table `lecturer_assignment`
--

CREATE TABLE `lecturer_assignment` (
  `Assignment_ID` int(11) NOT NULL,
  `Lect_ID` int(11) DEFAULT NULL,
  `Assign_Sche_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturer_assignment`
--

INSERT INTO `lecturer_assignment` (`Assignment_ID`, `Lect_ID`, `Assign_Sche_ID`) VALUES
(252, NULL, 76),
(253, NULL, 77),
(83, NULL, 83),
(84, NULL, 84),
(85, NULL, 85),
(86, NULL, 86),
(87, NULL, 87),
(245, NULL, 133),
(365, NULL, 136),
(377, 10, 65),
(378, 10, 70),
(379, 10, 109),
(375, 15, 74),
(376, 15, 75),
(367, 15, 130),
(368, 15, 131),
(374, 15, 134),
(373, 15, 135),
(142, 30, 63),
(143, 30, 64),
(62, 30, 66),
(63, 30, 67);

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `Level_ID` int(11) NOT NULL,
  `Level_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`Level_ID`, `Level_Name`) VALUES
(1, 'Diploma\r\n'),
(2, 'Degree');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `Sche_ID` int(11) NOT NULL,
  `Time_Slot` varchar(10) NOT NULL,
  `Day` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`Sche_ID`, `Time_Slot`, `Day`) VALUES
(1, '8-9', 'Monday'),
(2, '9-10', 'Monday'),
(3, '10-11', 'Monday'),
(4, '11-12', 'Monday'),
(5, '12-1', 'Monday'),
(6, '1-2', 'Monday'),
(7, '2-3', 'Monday'),
(8, '3-4', 'Monday'),
(9, '4-5', 'Monday'),
(10, '5-6', 'Monday'),
(11, '6-7', 'Monday'),
(12, '8-9', 'Tuesday'),
(13, '9-10', 'Tuesday'),
(14, '10-11', 'Tuesday'),
(15, '11-12', 'Tuesday'),
(16, '12-1', 'Tuesday'),
(17, '1-2', 'Tuesday'),
(18, '2-3', 'Tuesday'),
(19, '3-4', 'Tuesday'),
(20, '4-5', 'Tuesday'),
(21, '5-6', 'Tuesday'),
(22, '6-7', 'Tuesday'),
(23, '8-9', 'Wednesday'),
(24, '9-10', 'Wednesday'),
(25, '10-11', 'Wednesday'),
(26, '11-12', 'Wednesday'),
(27, '12-1', 'Wednesday'),
(28, '1-2', 'Wednesday'),
(29, '2-3', 'Wednesday'),
(30, '3-4', 'Wednesday'),
(31, '4-5', 'Wednesday'),
(32, '5-6', 'Wednesday'),
(33, '6-7', 'Wednesday'),
(34, '8-9', 'Thursday'),
(35, '9-10', 'Thursday'),
(36, '10-11', 'Thursday'),
(37, '11-12', 'Thursday'),
(38, '12-1', 'Thursday'),
(39, '1-2', 'Thursday'),
(40, '2-3', 'Thursday'),
(41, '3-4', 'Thursday'),
(42, '4-5', 'Thursday'),
(43, '5-6', 'Thursday'),
(44, '6-7', 'Thursday'),
(45, '8-9', 'Friday'),
(46, '9-10', 'Friday'),
(47, '10-11', 'Friday'),
(48, '11-12', 'Friday'),
(49, '12-1', 'Friday'),
(50, '1-2', 'Friday'),
(51, '2-3', 'Friday'),
(52, '3-4', 'Friday'),
(53, '4-5', 'Friday'),
(54, '5-6', 'Friday'),
(55, '6-7', 'Friday');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `Section_ID` int(11) NOT NULL,
  `Section_Number` int(11) NOT NULL,
  `Sem_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`Section_ID`, `Section_Number`, `Sem_ID`) VALUES
(11, 1, 7),
(12, 2, 10),
(13, 1, 11),
(14, 2, 11),
(15, 1, 9);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `Sem_ID` int(11) NOT NULL,
  `Sem_Number` int(11) NOT NULL,
  `Level_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`Sem_ID`, `Sem_Number`, `Level_ID`) VALUES
(7, 1, 1),
(8, 2, 1),
(9, 3, 1),
(10, 3, 2),
(11, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Stud_ID` int(11) NOT NULL,
  `Stud_Name` varchar(100) NOT NULL,
  `Section_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Stud_ID`, `Stud_Name`, `Section_ID`) VALUES
(1, 'NURIY NAZIHAH BINTI SAHFARUL AMRI', 13),
(3, 'aaaabdsadasdasdasdsadzzxasaaaa', 15),
(19, 'stud2', 14),
(21, 'stud3', 15),
(23, 'stud4', 13),
(24, 'test1.0', 11),
(25, 'MUHAMMAD AKMAL BIN MARZUKI', 13);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assign_schedule`
--
ALTER TABLE `assign_schedule`
  ADD PRIMARY KEY (`Assign_Sche_ID`),
  ADD UNIQUE KEY `Sche_Section_UNIQUE` (`Sche_ID`,`Section_ID`),
  ADD KEY `FK2_Section_ID` (`Section_ID`),
  ADD KEY `FK_Course_Section_ID` (`Course_Section_ID`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`Course_ID`);

--
-- Indexes for table `course_section`
--
ALTER TABLE `course_section`
  ADD PRIMARY KEY (`Course_Section_ID`),
  ADD UNIQUE KEY `Course_ID_Section_UNIQUE` (`Course_ID`,`Course_Section`);

--
-- Indexes for table `course_student`
--
ALTER TABLE `course_student`
  ADD PRIMARY KEY (`Course_Stud_ID`),
  ADD KEY `Course_ID` (`Course_ID`),
  ADD KEY `Stud_ID` (`Stud_ID`),
  ADD KEY `FK_CourseStudent_AssignmentID` (`Assignment_ID`);

--
-- Indexes for table `deputydean`
--
ALTER TABLE `deputydean`
  ADD PRIMARY KEY (`Dean_ID`),
  ADD UNIQUE KEY `Dean_Name` (`Dean_Name`);

--
-- Indexes for table `lecturer`
--
ALTER TABLE `lecturer`
  ADD PRIMARY KEY (`Lect_ID`);

--
-- Indexes for table `lecturer_assignment`
--
ALTER TABLE `lecturer_assignment`
  ADD PRIMARY KEY (`Assignment_ID`),
  ADD UNIQUE KEY `unique_lect_assign` (`Lect_ID`,`Assign_Sche_ID`),
  ADD UNIQUE KEY `unique_assign_sche_id` (`Assign_Sche_ID`);

--
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`Level_ID`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`Sche_ID`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`Section_ID`),
  ADD KEY `Sem_ID` (`Sem_ID`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`Sem_ID`),
  ADD KEY `Level_ID` (`Level_ID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Stud_ID`),
  ADD KEY `Section_ID` (`Section_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assign_schedule`
--
ALTER TABLE `assign_schedule`
  MODIFY `Assign_Sche_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `Course_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `course_section`
--
ALTER TABLE `course_section`
  MODIFY `Course_Section_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `course_student`
--
ALTER TABLE `course_student`
  MODIFY `Course_Stud_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `deputydean`
--
ALTER TABLE `deputydean`
  MODIFY `Dean_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lecturer`
--
ALTER TABLE `lecturer`
  MODIFY `Lect_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `lecturer_assignment`
--
ALTER TABLE `lecturer_assignment`
  MODIFY `Assignment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=380;

--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `Level_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `Sche_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `Section_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `Sem_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `Stud_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assign_schedule`
--
ALTER TABLE `assign_schedule`
  ADD CONSTRAINT `FK2_Section_ID` FOREIGN KEY (`Section_ID`) REFERENCES `section` (`Section_ID`),
  ADD CONSTRAINT `FK_Course_Section_ID` FOREIGN KEY (`Course_Section_ID`) REFERENCES `course_section` (`Course_Section_ID`),
  ADD CONSTRAINT `Sche_ID` FOREIGN KEY (`Sche_ID`) REFERENCES `schedule` (`Sche_ID`);

--
-- Constraints for table `course_section`
--
ALTER TABLE `course_section`
  ADD CONSTRAINT `FK2_Course_ID` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`);

--
-- Constraints for table `course_student`
--
ALTER TABLE `course_student`
  ADD CONSTRAINT `Course_ID` FOREIGN KEY (`Course_ID`) REFERENCES `course` (`Course_ID`),
  ADD CONSTRAINT `FK_CourseStudent_AssignmentID` FOREIGN KEY (`Assignment_ID`) REFERENCES `lecturer_assignment` (`Assignment_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `Stud_ID` FOREIGN KEY (`Stud_ID`) REFERENCES `student` (`Stud_ID`);

--
-- Constraints for table `lecturer_assignment`
--
ALTER TABLE `lecturer_assignment`
  ADD CONSTRAINT `FK_Assign_Sche_ID` FOREIGN KEY (`Assign_Sche_ID`) REFERENCES `assign_schedule` (`Assign_Sche_ID`),
  ADD CONSTRAINT `Lect_ID` FOREIGN KEY (`Lect_ID`) REFERENCES `lecturer` (`Lect_ID`);

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`Sem_ID`) REFERENCES `semester` (`Sem_ID`) ON DELETE CASCADE;

--
-- Constraints for table `semester`
--
ALTER TABLE `semester`
  ADD CONSTRAINT `semester_ibfk_1` FOREIGN KEY (`Level_ID`) REFERENCES `level` (`Level_ID`) ON DELETE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `Section_ID` FOREIGN KEY (`Section_ID`) REFERENCES `section` (`Section_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
