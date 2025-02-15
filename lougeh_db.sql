-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2025 at 05:10 AM
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
-- Database: `lougeh_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `car`
--

CREATE TABLE `car` (
  `SerialNumber` varchar(50) NOT NULL,
  `Brand` varchar(50) NOT NULL,
  `Model` varchar(50) NOT NULL,
  `Year` int(11) NOT NULL,
  `Condition` enum('New','Used') NOT NULL,
  `SalespersonID` int(11) DEFAULT NULL,
  `CustomerID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `car`
--
DELIMITER $$
CREATE TRIGGER `before_car_insert` BEFORE INSERT ON `car` FOR EACH ROW BEGIN
    -- Set SerialNumber as: [First 3 letters of Brand][First 3 letters of Model][Year]
    SET NEW.SerialNumber = CONCAT(
        UPPER(LEFT(NEW.Brand, 3)),
        UPPER(LEFT(NEW.Model, 3)),
        NEW.Year
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`CustomerID`, `Name`, `Phone`, `Email`) VALUES
(1, 'Chel', '123412', 'chel@mail.com'),
(2, 'Star', '2345254', 'star@mail.com'),
(4, 'Star Bread Pan', '2342423', 'starbreadpan@mail.com'),
(5, 'Bon Jayvee', '7868768', 'bj@mail.com'),
(10, 'Billy Joel', '234234', 'buildmeupbuttercup@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `InvoiceID` int(11) NOT NULL,
  `InvoiceNumber` varchar(20) NOT NULL,
  `CarSerialNumber` varchar(50) NOT NULL,
  `SalespersonID` int(11) DEFAULT NULL,
  `CustomerID` int(11) NOT NULL,
  `SaleDate` datetime NOT NULL DEFAULT current_timestamp(),
  `SalePrice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `invoice`
--
DELIMITER $$
CREATE TRIGGER `before_invoice_insert` BEFORE INSERT ON `invoice` FOR EACH ROW BEGIN
    DECLARE count_today INT;
    -- Count invoices for today based on SaleDate
    SELECT COUNT(*) + 1 INTO count_today
      FROM Invoice
      WHERE DATE(SaleDate) = CURDATE();
      
    -- Format InvoiceNumber as: YYYYMMDD-XXXX (sequence resets daily)
    SET NEW.InvoiceNumber = CONCAT(
        DATE_FORMAT(CURDATE(), '%Y%m%d'),
        '-',
        LPAD(count_today, 4, '0')
    );
    
    -- Ensure SaleDate is set to the current time
    SET NEW.SaleDate = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mechanic`
--

CREATE TABLE `mechanic` (
  `MechanicID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mechanic`
--

INSERT INTO `mechanic` (`MechanicID`, `Name`, `Phone`, `Email`) VALUES
(1, 'mechanic 1', '435353', 'mechanic1@mail.com'),
(2, 'mechanic 2', '5645645', 'mechanic2@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `salesperson`
--

CREATE TABLE `salesperson` (
  `SalespersonID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salesperson`
--

INSERT INTO `salesperson` (`SalespersonID`, `Name`, `Phone`, `Email`) VALUES
(1, 'SP 1', '3253450347', 'sp1@mail.com'),
(2, 'SP 2', '9458349', 'sp2@mail.com');

-- --------------------------------------------------------

--
-- Table structure for table `servicepart`
--

CREATE TABLE `servicepart` (
  `PartID` int(11) NOT NULL,
  `TicketID` int(11) NOT NULL,
  `PartName` varchar(100) NOT NULL,
  `PartPrice` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `serviceticket`
--

CREATE TABLE `serviceticket` (
  `TicketID` int(11) NOT NULL,
  `CarSerialNumber` varchar(50) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `ServiceDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `serviceticketdetails`
-- (See below for the actual view)
--
CREATE TABLE `serviceticketdetails` (
`TicketID` int(11)
,`CarSerialNumber` varchar(50)
,`CustomerID` int(11)
,`ServiceDate` datetime
,`Description` text
,`MechanicsInvolved` mediumtext
);

-- --------------------------------------------------------

--
-- Table structure for table `serviceticket_mechanic`
--

CREATE TABLE `serviceticket_mechanic` (
  `TicketID` int(11) NOT NULL,
  `MechanicID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `serviceticketdetails`
--
DROP TABLE IF EXISTS `serviceticketdetails`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `serviceticketdetails`  AS SELECT `st`.`TicketID` AS `TicketID`, `st`.`CarSerialNumber` AS `CarSerialNumber`, `st`.`CustomerID` AS `CustomerID`, `st`.`ServiceDate` AS `ServiceDate`, `st`.`Description` AS `Description`, group_concat(`m`.`Name` separator ', ') AS `MechanicsInvolved` FROM ((`serviceticket` `st` left join `serviceticket_mechanic` `stm` on(`st`.`TicketID` = `stm`.`TicketID`)) left join `mechanic` `m` on(`stm`.`MechanicID` = `m`.`MechanicID`)) GROUP BY `st`.`TicketID` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car`
--
ALTER TABLE `car`
  ADD PRIMARY KEY (`SerialNumber`),
  ADD KEY `fk_car_salesperson` (`SalespersonID`),
  ADD KEY `fk_car_customer` (`CustomerID`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`InvoiceID`),
  ADD KEY `fk_invoice_car` (`CarSerialNumber`),
  ADD KEY `fk_invoice_salesperson` (`SalespersonID`),
  ADD KEY `fk_invoice_customer` (`CustomerID`);

--
-- Indexes for table `mechanic`
--
ALTER TABLE `mechanic`
  ADD PRIMARY KEY (`MechanicID`);

--
-- Indexes for table `salesperson`
--
ALTER TABLE `salesperson`
  ADD PRIMARY KEY (`SalespersonID`);

--
-- Indexes for table `servicepart`
--
ALTER TABLE `servicepart`
  ADD PRIMARY KEY (`PartID`),
  ADD KEY `TicketID` (`TicketID`);

--
-- Indexes for table `serviceticket`
--
ALTER TABLE `serviceticket`
  ADD PRIMARY KEY (`TicketID`),
  ADD KEY `fk_serviceticket_car` (`CarSerialNumber`),
  ADD KEY `fk_serviceticket_customer` (`CustomerID`);

--
-- Indexes for table `serviceticket_mechanic`
--
ALTER TABLE `serviceticket_mechanic`
  ADD PRIMARY KEY (`TicketID`,`MechanicID`),
  ADD KEY `fk_stm_mechanic` (`MechanicID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `InvoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `mechanic`
--
ALTER TABLE `mechanic`
  MODIFY `MechanicID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `salesperson`
--
ALTER TABLE `salesperson`
  MODIFY `SalespersonID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `servicepart`
--
ALTER TABLE `servicepart`
  MODIFY `PartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `serviceticket`
--
ALTER TABLE `serviceticket`
  MODIFY `TicketID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `fk_car_customer` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_car_salesperson` FOREIGN KEY (`SalespersonID`) REFERENCES `salesperson` (`SalespersonID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `fk_invoice_car` FOREIGN KEY (`CarSerialNumber`) REFERENCES `car` (`SerialNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoice_customer` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_invoice_salesperson` FOREIGN KEY (`SalespersonID`) REFERENCES `salesperson` (`SalespersonID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `servicepart`
--
ALTER TABLE `servicepart`
  ADD CONSTRAINT `servicepart_ibfk_1` FOREIGN KEY (`TicketID`) REFERENCES `serviceticket` (`TicketID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `serviceticket`
--
ALTER TABLE `serviceticket`
  ADD CONSTRAINT `fk_serviceticket_car` FOREIGN KEY (`CarSerialNumber`) REFERENCES `car` (`SerialNumber`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_serviceticket_customer` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `serviceticket_mechanic`
--
ALTER TABLE `serviceticket_mechanic`
  ADD CONSTRAINT `fk_stm_mechanic` FOREIGN KEY (`MechanicID`) REFERENCES `mechanic` (`MechanicID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stm_ticket` FOREIGN KEY (`TicketID`) REFERENCES `serviceticket` (`TicketID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
