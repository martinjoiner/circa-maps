

--
-- Table structure for table `map`
--

CREATE TABLE IF NOT EXISTS `map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `map`
--

INSERT INTO `map` (`id`, `name`, `width`, `height`) VALUES
(1, 'Map1', 1200, 800);

-- --------------------------------------------------------

--
-- Table structure for table `path`
--

CREATE TABLE IF NOT EXISTS `path` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `d` varchar(640) NOT NULL,
  `type` enum('property','road') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `path`
--

INSERT INTO `path` (`id`, `map_id`, `d`, `type`) VALUES
(1, 1, 'M 55,312 108,0 0,30 -102.0,0 z', 'property'),
(2, 1, 'M 405,412 28,39 0,39 -28,35 z', 'property'),
(3, 1, 'M 50,50 8,55 0,90 -30,0 z', 'property'),
(4, 1, 'M 205,312 28,0 0,30 -28,35 z', 'property'),
(5, 1, 'M 655,362 78,30 10,32 -40,35 z', 'property');

-- --------------------------------------------------------

--
-- Table structure for table `point`
--

CREATE TABLE IF NOT EXISTS `point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) DEFAULT NULL,
  `route_id` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `point`
--

INSERT INTO `point` (`id`, `property_id`, `route_id`, `order`, `x`, `y`) VALUES
(1, NULL, 1, 1, 0, 40),
(2, NULL, 1, 2, 50, 48),
(3, NULL, 1, 3, 100, 42),
(4, NULL, 1, 4, 150, 38),
(5, NULL, 1, 5, 200, 47),
(6, NULL, 1, 6, 250, 30),
(7, 1, NULL, 1, 55, 312),
(8, 1, NULL, 2, 160, 312),
(9, 1, NULL, 3, 160, 330),
(10, 1, NULL, 4, 48, 330);

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE IF NOT EXISTS `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `area` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`id`, `map_id`, `name`, `area`) VALUES
(1, 1, 'My House', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `route`
--

CREATE TABLE IF NOT EXISTS `route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `route`
--

INSERT INTO `route` (`id`, `map_id`, `width`, `length`) VALUES
(1, 1, 4, NULL);
