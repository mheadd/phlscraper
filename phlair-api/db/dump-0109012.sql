--
-- Database: `phlair`
--

USE phlair;

-- --------------------------------------------------------

--
-- Table structure for table `arrivals`
--

CREATE TABLE IF NOT EXISTS `arrivals` (
  `date` date NOT NULL,
  `airline` varchar(50) NOT NULL,
  `flight_number` varchar(10) NOT NULL,
  `origin` varchar(75) NOT NULL,
  `time` varchar(20) NOT NULL,
  `gate` varchar(10) NOT NULL,
  `remarks` varchar(75) NOT NULL,
  PRIMARY KEY (`date`,`airline`,`flight_number`),
  KEY `date` (`date`,`airline`,`flight_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `departures`
--

CREATE TABLE IF NOT EXISTS `departures` (
  `date` date NOT NULL,
  `airline` varchar(50) NOT NULL,
  `flight_number` varchar(10) NOT NULL,
  `destination` varchar(75) NOT NULL,
  `time` varchar(20) NOT NULL,
  `gate` varchar(10) NOT NULL,
  `remarks` varchar(75) NOT NULL,
  PRIMARY KEY (`date`,`airline`,`flight_number`),
  KEY `date` (`date`,`airline`,`flight_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

