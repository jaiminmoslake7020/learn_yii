-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2016 at 12:33 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `advanced`
--

-- --------------------------------------------------------

--
-- Table structure for table `project_team_members`
--

CREATE TABLE IF NOT EXISTS `project_team_members` (
  `project_team_member_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `member_name` varchar(20) NOT NULL,
  `member_email` varchar(320) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `project_team_members`
--

INSERT INTO `project_team_members` (`project_team_member_id`, `project_id`, `member_name`, `member_email`, `created_at`, `updated_at`) VALUES
(1, 8, 'fdgdgf', 'bfvjk@jhhbj.vs', 1469299089, 1469299089),
(2, 8, 'sfsdgf', 'dsds@dsdsfds.dssdf', 1469299089, 1469299089),
(3, 8, 'grtthgfg', 'gfsfr@gfdg.ffdv', 1469299089, 1469299089),
(4, 9, 'grtthgfg', 'gfsfr@gfdg.ffdv', 1469299554, 1469299554),
(5, 9, 'grtthgfg', 'gfsfr@gfdg.ffdv', 1469299554, 1469299554),
(6, 9, 'grtthgfg', 'gfsfr@gfdg.ffdv', 1469299554, 1469299554);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project_team_members`
--
ALTER TABLE `project_team_members`
  ADD PRIMARY KEY (`project_team_member_id`), ADD KEY `project_id` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_team_members`
--
ALTER TABLE `project_team_members`
  MODIFY `project_team_member_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_team_members`
--
ALTER TABLE `project_team_members`
ADD CONSTRAINT `project_team_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
