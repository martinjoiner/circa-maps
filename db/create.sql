
-- Table structure for table `map`

CREATE TABLE IF NOT EXISTS `map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



-- Table structure for table `path`

CREATE TABLE IF NOT EXISTS `path` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `d` varchar(640) NOT NULL,
  `type` enum('property','road') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;









-- Dumping test data for table `map`

INSERT INTO `map` (`id`, `name`, `width`, `height`) VALUES
(1, 'Map1', 1200, 800);


-- Dumping test data for table `path`

INSERT INTO `path` (`id`, `map_id`, `d`, `type`) VALUES
(1, 1, 'M 55,312 108,0 0,30 -102.0,0 z', 'property'),
(2, 1, 'M 405,412 28,39 0,39 -28,35 z', 'property'),
(3, 1, 'M 50,50 8,55 0,90 -30,0 z', 'property'),
(4, 1, 'M 205,312 28,0 0,30 -28,35 z', 'property'),
(5, 1, 'M 655,362 78,30 10,32 -40,35 z', 'property'),
(6, 1, 'M 344,370 298.57141,0 0,11.42857 -298.57141,0 z', 'road'),
(7, 1, 'M 880,358 298,0 0,11 -298,0 z', 'road'),
(8, 1, 'M 467,139 298,0 0,11 -298.5714,0 z', 'road');

