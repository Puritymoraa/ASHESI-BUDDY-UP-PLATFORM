-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2024 at 06:01 AM
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
-- Database: `buddyup`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `activity_name` varchar(255) NOT NULL,
  `semester` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_modified` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `activity_name`, `semester`, `description`, `created_by`, `creation_date`, `last_modified`) VALUES
(4, 'Study Group Session', '2023/2024', 'Regular study sessions focusing on challenging courses and exam preparation. Mentors guide mentees through difficult concepts and study strategies.', 1, '2024-12-15 05:21:01', '2024-12-15 21:14:59'),
(5, 'Library Research Workshop', '2023/2024', 'Introduction to library resources, research databases, and academic journals. Learn effective research techniques for assignments.', 1, '2024-12-15 05:21:01', '2024-12-15 21:14:59'),
(6, 'Course Selection Guidance', '2023/2024', 'Helping freshmen understand course requirements, prerequisites, and making informed decisions about their academic path.', 1, '2024-12-15 05:21:01', '2024-12-15 21:14:59'),
(7, 'Academic Writing Skills', '2023/2024', 'Workshop on academic writing, citation styles, and research paper organization.', 1, '2024-12-15 05:21:01', '2024-12-15 21:14:59'),
(9, 'Nature walk', '2023/2024', 'A nature walk in the wild with me', 12, '2024-12-15 21:09:06', '2024-12-16 04:13:52'),
(10, 'Dancing', 'Fall 2024', 'Joined a dancing club activity', 12, '2024-12-15 21:34:22', '2024-12-15 21:34:22'),
(11, 'Camping', 'Fall 2025', 'Going for camping in the bundus.\r\n', 12, '2024-12-15 22:26:42', '2024-12-15 22:26:42'),
(12, 'Going to the beach', 'Spring 2024', 'Have a nature walk at the beach', 12, '2024-12-16 00:10:45', '2024-12-16 00:10:45'),
(13, 'PEER TUTOR', 'Spring 2024', 'Peer tutor other students', 26, '2024-12-16 00:35:50', '2024-12-16 00:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `activity_completions`
--

CREATE TABLE `activity_completions` (
  `completion_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `mentorship_id` int(11) DEFAULT NULL,
  `completion_date` datetime DEFAULT current_timestamp(),
  `experience` text DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_completions`
--

INSERT INTO `activity_completions` (`completion_id`, `activity_id`, `mentorship_id`, `completion_date`, `experience`, `status`) VALUES
(1, 4, 1, '2024-12-15 05:39:56', 'This was perfect', 'completed'),
(2, 7, 1, '2024-12-15 05:45:35', 'Another amazing activity. Highly recommend', 'completed'),
(3, 6, 1, '2024-12-15 05:46:42', 'Great', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`post_id`, `user_id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(3, 3, 'Welcome', 'Welcome freshman', '2024-12-15 06:31:27', '2024-12-15 06:31:27'),
(4, 3, 'This is a new blog', 'There are 6 freshmen so far', '2024-12-15 17:29:00', '2024-12-15 17:29:00'),
(5, 11, 'This is', 'My name is Diana kerubo', '2024-12-15 17:49:09', '2024-12-15 17:49:34'),
(6, 29, 'This is a new blog.', 'Created by the superadmib', '2024-12-16 04:47:16', '2024-12-16 04:47:16');

-- --------------------------------------------------------

--
-- Table structure for table `continuing_freshman_buddies`
--

CREATE TABLE `continuing_freshman_buddies` (
  `mapping_id` int(11) NOT NULL,
  `continuing_student_id` int(11) DEFAULT NULL,
  `freshman_id` int(11) DEFAULT NULL,
  `paired_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `continuing_student_details`
--

CREATE TABLE `continuing_student_details` (
  `continuing_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `student_id` varchar(8) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `hobby` text DEFAULT NULL,
  `fun_fact` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `continuing_student_details`
--

INSERT INTO `continuing_student_details` (`continuing_id`, `user_id`, `avatar_url`, `full_name`, `student_id`, `age`, `gender`, `major`, `nationality`, `hobby`, `fun_fact`) VALUES
(0, 3, 'https://api.dicebear.com/6.x/fun-emoji/svg?seed=675e416b11294', 'Purity Moraa', '09542026', 21, 'Female', 'Computer Science', 'Ghanaian', 'Sleeping', 'I love cooking'),
(1, 19, 'https://api.dicebear.com/6.x/fun-emoji/svg?seed=james', 'James Bond', '12345678', 21, 'Male', 'Computer Science', 'Ghanaian', 'Reading', 'Loves coding'),
(2, 20, 'https://api.dicebear.com/6.x/fun-emoji/svg?seed=mary', 'Mary Jane', '30782029', 20, 'Female', 'Business Administration', 'Nigerian', 'Swimming', 'Speaks 4 languages');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_details`
--

CREATE TABLE `faculty_details` (
  `faculty_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `research_area` varchar(255) DEFAULT NULL,
  `max_mentees` int(11) DEFAULT 8
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_details`
--

INSERT INTO `faculty_details` (`faculty_id`, `user_id`, `avatar_url`, `full_name`, `department`, `research_area`, `max_mentees`) VALUES
(0, 26, 'https://api.dicebear.com/6.x/adventurer/svg?seed=675f743ea0dcd', 'Samuel Mavole', 'Computer science', 'Economics', 10);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_mentees`
--

CREATE TABLE `faculty_mentees` (
  `relationship_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `mentee_id` int(11) DEFAULT NULL,
  `mentee_type` enum('Freshman','Continuing') NOT NULL,
  `relationship_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `freshman_details`
--

CREATE TABLE `freshman_details` (
  `freshman_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `student_id` varchar(8) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `hobby` text DEFAULT NULL,
  `fun_fact` text DEFAULT NULL,
  `continuing_buddy_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freshman_details`
--

INSERT INTO `freshman_details` (`freshman_id`, `user_id`, `avatar_url`, `full_name`, `student_id`, `age`, `gender`, `major`, `nationality`, `hobby`, `fun_fact`, `continuing_buddy_id`) VALUES
(1, 5, 'https://api.dicebear.com/6.x/avataaars/svg?seed=Sarah', 'Sarah Smith', '12345679', 19, 'Female', 'Business Administration', 'Nigerian', 'Photography', 'Has visited 10 countries', NULL),
(2, 6, 'https://api.dicebear.com/6.x/avataaars/svg?seed=Michael', 'Michael Brown', '19902027', 18, 'Male', 'Computer Science', 'Kenyan', 'Soccer', 'Plays three musical instruments', NULL),
(3, 7, 'https://api.dicebear.com/6.x/avataaars/svg?seed=Emma', 'Emma Wilson', '12345681', 19, 'Female', 'Management Information Systems', 'South African', 'Painting', 'Won a national art competition', NULL),
(4, 8, 'https://api.dicebear.com/6.x/avataaars/svg?seed=David', 'David Zhang', '12345682', 18, 'Male', 'Computer Science', 'Chinese', 'Robotics', 'Built a robot at age 15', NULL),
(5, 10, 'https://api.dicebear.com/6.x/adventurer/svg?seed=675f010d92a86', 'Jane Moraa', '09542028', 17, 'Female', 'Management Information Systems', 'American', 'mowing', 'I am a night owl', NULL),
(6, 11, 'https://api.dicebear.com/6.x/avataaars/svg?seed=675f07cb44c1a', 'Diana Kerubo', '09542029', 18, 'Male', 'Computer Science', 'Gambian', 'Good', 'The best', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mentorship`
--

CREATE TABLE `mentorship` (
  `mentorship_id` int(11) NOT NULL,
  `continuing_id` int(11) NOT NULL,
  `freshman_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `end_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentorship`
--

INSERT INTO `mentorship` (`mentorship_id`, `continuing_id`, `freshman_id`, `start_date`, `status`, `end_date`) VALUES
(1, 3, 8, '2024-12-15 04:59:16', 'active', NULL),
(3, 3, 7, '2024-12-15 05:08:21', 'inactive', '2024-12-15 05:10:32'),
(4, 3, 11, '2024-12-15 16:52:43', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  `status` enum('sent','delivered','read') DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `content`, `sent_at`, `read_at`, `status`) VALUES
(2, 3, 8, 'jjiijjihgyfyg', '2024-12-15 08:14:08', NULL, 'sent'),
(3, 3, 11, 'Hii Diana.', '2024-12-15 17:06:04', NULL, 'sent'),
(4, 11, 3, 'Hii', '2024-12-15 17:11:31', NULL, 'sent');

-- --------------------------------------------------------

--
-- Table structure for table `superadmin_details`
--

CREATE TABLE `superadmin_details` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superadmin_details`
--

INSERT INTO `superadmin_details` (`admin_id`, `user_id`, `avatar_url`, `full_name`) VALUES
(1, 29, 'https://api.dicebear.com/6.x/initials/svg?seed=SuperAdmin', 'Super Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Freshman','Continuing','Faculty','SuperAdmin') NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `registration_date`, `last_login`) VALUES
(1, 'esterina.khoboso@ashesi.edu.gh', '$2y$10$RgrInmD3CD96/TkXgqEh2uA2NwOOY8EfyYVUwIaZ0b1myetcvtNpu', 'Faculty', '2024-12-15 01:57:20', '2024-12-15 02:35:54'),
(3, 'puritymoraa@ashesi.edu.gh', '$2y$10$wjC7Fr3iJiRiew6ZRGlpMu0OE0EG1sBnmziwtG2tT8vaLQpOhyxUS', 'Continuing', '2024-12-15 02:40:21', '2024-12-15 03:58:18'),
(5, 'sarah.smith@ashesi.edu.gh', '$2y$10$dummyhashedpassword123', 'Freshman', '2024-12-15 04:46:01', NULL),
(6, 'michael.brown@ashesi.edu.gh', '$2y$10$dummyhashedpassword123', 'Freshman', '2024-12-15 04:46:01', NULL),
(7, 'emma.wilson@ashesi.edu.gh', '$2y$10$dummyhashedpassword123', 'Freshman', '2024-12-15 04:46:01', NULL),
(8, 'david.zhang@ashesi.edu.gh', '$2y$10$dummyhashedpassword123', 'Freshman', '2024-12-15 04:46:01', NULL),
(10, 'janemoraa@ashesi.edu.gh', '$2y$10$bFdl4bflSn5SEv5FHyQ2DOk.lBxKXz13kKFZCtUeBTH9zbyPq20lW', 'Freshman', '2024-12-15 16:18:19', NULL),
(11, 'diana.kerubo@ashesi.edu.gh', '$2y$10$2Mtd.P.WQb.5k1TJOK09Le/qnXsKVGSHN4CHaXY8L2F0YoW7bRL6C', 'Freshman', '2024-12-15 16:47:10', NULL),
(12, 'emily.nyaboke@ashesi.edu.gh', '$2y$10$VBVqT1X9Zq2KGoFDxDClAul2J1TnDjII.R8EZErXnzWu/J7Ruyi/i', 'Faculty', '2024-12-15 20:23:36', NULL),
(19, 'james.bond@ashesi.edu.gh', '$2y$10$dummy_hash', 'Continuing', '2024-12-15 23:52:49', NULL),
(20, 'mary.jane@ashesi.edu.gh', '$2y$10$dummy_hash', 'Continuing', '2024-12-15 23:52:49', NULL),
(21, 'fresh.student1@ashesi.edu.gh', '$2y$10$dummy_hash', 'Freshman', '2024-12-15 23:52:49', NULL),
(22, 'fresh.student2@ashesi.edu.gh', '$2y$10$dummy_hash', 'Freshman', '2024-12-15 23:52:49', NULL),
(26, 'samuel.mavole@ashesi.edu.gh', '$2y$10$DNx0SXAa7ckusp5/Xy.4g.e9r.Tqb.FIt86Aq15iOHxT4cFndTrgG', 'Faculty', '2024-12-16 00:29:36', NULL),
(29, 'superadmin@ashesi.edu.gh', '$2y$10$te5q4b4zr/szzBU9oLSvVu9RFp.mb.3m5FiREGb8HPucrCusTwqAC', 'SuperAdmin', '2024-12-16 02:12:14', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `activity_completions`
--
ALTER TABLE `activity_completions`
  ADD PRIMARY KEY (`completion_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `mentorship_id` (`mentorship_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `idx_blog_user` (`user_id`);

--
-- Indexes for table `continuing_freshman_buddies`
--
ALTER TABLE `continuing_freshman_buddies`
  ADD PRIMARY KEY (`mapping_id`),
  ADD UNIQUE KEY `unique_buddy_pair` (`continuing_student_id`,`freshman_id`),
  ADD KEY `freshman_id` (`freshman_id`);

--
-- Indexes for table `continuing_student_details`
--
ALTER TABLE `continuing_student_details`
  ADD PRIMARY KEY (`continuing_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_continuing_student_id` (`student_id`);

--
-- Indexes for table `faculty_details`
--
ALTER TABLE `faculty_details`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `faculty_mentees`
--
ALTER TABLE `faculty_mentees`
  ADD PRIMARY KEY (`relationship_id`),
  ADD UNIQUE KEY `unique_mentorship` (`faculty_id`,`mentee_id`,`mentee_type`);

--
-- Indexes for table `freshman_details`
--
ALTER TABLE `freshman_details`
  ADD PRIMARY KEY (`freshman_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `continuing_buddy_id` (`continuing_buddy_id`),
  ADD KEY `idx_freshman_student_id` (`student_id`);

--
-- Indexes for table `mentorship`
--
ALTER TABLE `mentorship`
  ADD PRIMARY KEY (`mentorship_id`),
  ADD UNIQUE KEY `unique_pair` (`continuing_id`,`freshman_id`),
  ADD KEY `freshman_id` (`freshman_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `superadmin_details`
--
ALTER TABLE `superadmin_details`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `activity_completions`
--
ALTER TABLE `activity_completions`
  MODIFY `completion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `continuing_freshman_buddies`
--
ALTER TABLE `continuing_freshman_buddies`
  MODIFY `mapping_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty_mentees`
--
ALTER TABLE `faculty_mentees`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentorship`
--
ALTER TABLE `mentorship`
  MODIFY `mentorship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `superadmin_details`
--
ALTER TABLE `superadmin_details`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `activity_completions`
--
ALTER TABLE `activity_completions`
  ADD CONSTRAINT `activity_completions_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`),
  ADD CONSTRAINT `activity_completions_ibfk_2` FOREIGN KEY (`mentorship_id`) REFERENCES `mentorship` (`mentorship_id`);

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `continuing_freshman_buddies`
--
ALTER TABLE `continuing_freshman_buddies`
  ADD CONSTRAINT `continuing_freshman_buddies_ibfk_1` FOREIGN KEY (`continuing_student_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `continuing_freshman_buddies_ibfk_2` FOREIGN KEY (`freshman_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `continuing_student_details`
--
ALTER TABLE `continuing_student_details`
  ADD CONSTRAINT `continuing_student_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `faculty_details`
--
ALTER TABLE `faculty_details`
  ADD CONSTRAINT `faculty_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `faculty_mentees`
--
ALTER TABLE `faculty_mentees`
  ADD CONSTRAINT `faculty_mentees_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty_details` (`faculty_id`);

--
-- Constraints for table `freshman_details`
--
ALTER TABLE `freshman_details`
  ADD CONSTRAINT `freshman_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `freshman_details_ibfk_2` FOREIGN KEY (`continuing_buddy_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `mentorship`
--
ALTER TABLE `mentorship`
  ADD CONSTRAINT `mentorship_ibfk_1` FOREIGN KEY (`continuing_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `mentorship_ibfk_2` FOREIGN KEY (`freshman_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `superadmin_details`
--
ALTER TABLE `superadmin_details`
  ADD CONSTRAINT `superadmin_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
