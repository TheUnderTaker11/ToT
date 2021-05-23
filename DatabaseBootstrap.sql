SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `tot`;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `USER_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `type` enum('Customer','Waiter','Host','Manager') NOT NULL DEFAULT 'Customer',
  `pw_hash` varchar(255) NOT NULL,
  `name_first` varchar(40) DEFAULT NULL,
  `name_last` varchar(40) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`USER_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `ITEM_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` TEXT NOT NULL,
  `image` varchar(255),
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ITEM_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `RESERVATION_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `USER_ID` int(10) unsigned NOT NULL,
  `reservation_time` smallint NOT NULL,
  `total_people` smallint unsigned NOT NULL,
  `WAITER_ID` int(10) unsigned,
  PRIMARY KEY (`RESERVATION_ID`),
  CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE,
  CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`WAITER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `ORDER_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RESERVATION_ID` int(10) unsigned NOT NULL,
  `USER_ID` int(10) unsigned NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `complete` int(1) NOT NULL,
  PRIMARY KEY (`ORDER_ID`),
  CONSTRAINT `order_ibfk_1` FOREIGN KEY (`RESERVATION_ID`) REFERENCES `reservations` (`RESERVATION_ID`) ON DELETE CASCADE,
  CONSTRAINT `order_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf16;


DROP TABLE IF EXISTS `reservation_orders`;
CREATE TABLE `reservation_orders` (
  `ORDER_ID` int(10) unsigned NOT NULL,
  `RESERVATION_ID` int(10) unsigned NOT NULL,
  KEY `ORDER_ID` (`ORDER_ID`),
  KEY `RESERVATION_ID` (`RESERVATION_ID`),
  CONSTRAINT `reservation_orders_ibfk_1` FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ORDER_ID`) ON DELETE CASCADE,
  CONSTRAINT `reservation_orders_ibfk_2` FOREIGN KEY (`RESERVATION_ID`) REFERENCES `reservations` (`RESERVATION_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

DROP TABLE IF EXISTS `order_menu_items`;
CREATE TABLE `order_menu_items` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ORDER_ID` int(10) unsigned NOT NULL,
  `ITEM_ID` int(10) unsigned NOT NULL,
  `USER_ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ORDER_ID` (`ORDER_ID`),
  KEY `ITEM_ID` (`ITEM_ID`),
  CONSTRAINT `order_menu_items_ibfk_1` FOREIGN KEY (`ORDER_ID`) REFERENCES `orders` (`ORDER_ID`) ON DELETE CASCADE,
  CONSTRAINT `order_menu_items_ibfk_2` FOREIGN KEY (`ITEM_ID`) REFERENCES `menu_items` (`ITEM_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf16;

DROP TABLE IF EXISTS `reservation_users`;
CREATE TABLE `reservation_users` (
  `RESERVATION_ID` int(10) unsigned NOT NULL,
  `USER_ID` int(10) unsigned NOT NULL,
  KEY `RESERVATION_ID` (`RESERVATION_ID`),
  KEY `USER_ID` (`USER_ID`),
  CONSTRAINT `reservation_users_ibfk_1` FOREIGN KEY (`RESERVATION_ID`) REFERENCES `reservations` (`RESERVATION_ID`) ON DELETE CASCADE,
  CONSTRAINT `reservation_users_ibfk_2` FOREIGN KEY (`USER_ID`) REFERENCES `users` (`USER_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf16;


INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(0,	'dummyuseraccount',	'Manager',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'FAKE',	'USER',	'',	'');

INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(1,	'testManager',	'Manager',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Test',	'Manager',	'tot@olemiss.edu',	'123-555-9876');



