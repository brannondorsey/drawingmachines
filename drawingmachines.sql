-- phpMyAdmin SQL Dump
-- version 4.0.4.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 26, 2014 at 07:27 AM
-- Server version: 5.5.25
-- PHP Version: 5.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `drawingm_drawingmachines`
--
CREATE DATABASE IF NOT EXISTS `drawingm_drawingmachines` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `drawingm_drawingmachines`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`) VALUES
(23, 'Camera Obscura'),
(24, 'Camera Lucida'),
(25, 'Plotters'),
(26, 'Strings & Doors'),
(27, 'Profiles'),
(28, 'Sight Tracing'),
(29, 'Electronic'),
(30, 'Camera Obscura'),
(31, 'Camera Lucida'),
(32, 'Plotters'),
(33, 'Strings & Doors'),
(34, 'Profiles'),
(35, 'Sight Tracing'),
(36, 'Electronic');

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE IF NOT EXISTS `machines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `device_name` varchar(200) NOT NULL,
  `inventor` varchar(200) NOT NULL,
  `inventor_line_2` varchar(255) NOT NULL,
  `year` varchar(9) NOT NULL,
  `circa` tinyint(1) NOT NULL,
  `categories` varchar(255) NOT NULL,
  `primary_category` varchar(255) NOT NULL,
  `secondary_category` varchar(255) NOT NULL,
  `post_content` text NOT NULL,
  `tags` text NOT NULL,
  `source` varchar(255) NOT NULL,
  `source_line_2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `device_name`, `inventor`, `inventor_line_2`, `year`, `circa`, `categories`, `primary_category`, `secondary_category`, `post_content`, `tags`, `source`, `source_line_2`) VALUES
(1, 'Super Awesome Drawing Device', 'John Walters', 'test', '1660', 0, 'make,draw', 'make', 'draw', '[Lorem ipsum](http://brannondorsey.com) dolor sit amet, consectetur adipiscing elit. Proin at elit purus. Sed ac massa condimentum, ultrices nunc eu, interdum elit. In at felis ante. Maecenas vestibulum neque at ipsum viverra convallis. Ut vitae congue arcu. Integer nibh metus, porttitor eu massa tempor, vehicula sodales lorem. Fusce at elit at tellus interdum dignissim. Curabitur eu venenatis justo, vel tempus lectus. Nullam lobortis urna nec varius semper. Proin semper nec justo sed fringilla. Integer dictum sollicitudin dolor in eleifend. Donec ullamcorper orci quis mauris tincidunt scelerisque. Pellentesque ut metus accumsan, euismod leo in, convallis lacus.', ',,really,cool,drawing,aid,', 'http://supercooldrawings.com', 'http://reallycooldrawings.com');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag`) VALUES
(29, '#Uses lenses'),
(30, '#Uses prisms'),
(31, '#Is fully automated'),
(32, '#Mimics perspectival construction'),
(33, '#Makes copies'),
(34, '#Is useful for designers'),
(35, '#Is mechanical'),
(36, '#Helps me draw from life'),
(37, '#Is electronic'),
(38, '#Is digital'),
(39, '#Is analog'),
(40, '#Uses a picture plane'),
(41, '#Is portable'),
(42, '#Is from the 1400s'),
(43, '#Is from the 1500s'),
(44, '#Is from the 1600s'),
(45, '#Is from the 1700s'),
(46, '#Is from the 1800s'),
(47, '#Is from the 1900s'),
(48, '#Is from the 2000s'),
(49, '#Automates a complex process'),
(50, '#Helps me measure'),
(51, '#Works in 3D'),
(52, '#Uses light/shadow'),
(53, '#Makes silhouettes'),
(54, '#Is a plotter');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
