
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(2,	'testCustomer',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'FirstName1',	'LastName1',	'Email1',	'1111111');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(3,	'testWaiter',	'Waiter',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Test',	'Waiter',	'',	'');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(4,	'testHost',	'Host',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Test',	'Host',	'',	'');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(5,	'testCustomer2',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'FirstName2',	'LastName2',	'Email2',	'2222222');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(6,	'testCustomer3',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'FirstName3',	'LastName3',	'Email3',	'3333333');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(7,	'testCustomer4',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'FirstName4',	'LastName4',	'Email4',	'4444444');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(8,	'testWaiter2',	'Waiter',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Test',	'Waiter2',	'',	'');

INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(9,	'testCustomer6',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Customer',	'Reservation1',	'Email3',	'3333333');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(10,	'testCustomer7',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Customer',	'Reservation2',	'Email4',	'4444444');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(11,	'testCustomer8',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Customer',	'Reservation3',	'Email3',	'3333333');
INSERT INTO `users` (`USER_ID`, `username`, `type`, `pw_hash`, `name_first`, `name_last`, `email`, `phone`) VALUES
(12,	'testCustomer9',	'Customer',	'$2y$10$egM0NpoIHRq/sLL41tF2auPOXQjryCZQv.jl4yMOT711LKPXF5FKG',	'Customer',	'Reservation4',	'Email4',	'4444444');


INSERT INTO `reservations` (`RESERVATION_ID`,`USER_ID`,`reservation_time`,`total_people`,`WAITER_ID`) VALUES
(1, 9, 1, 55, null);
INSERT INTO `reservations` (`RESERVATION_ID`,`USER_ID`,`reservation_time`,`total_people`,`WAITER_ID`) VALUES
(2, 10, 11, 80, null);
INSERT INTO `reservations` (`RESERVATION_ID`,`USER_ID`,`reservation_time`,`total_people`,`WAITER_ID`) VALUES
(3, 11, 12, 15, null);
INSERT INTO `reservations` (`RESERVATION_ID`,`USER_ID`,`reservation_time`,`total_people`,`WAITER_ID`) VALUES
(4, 12, 16, 55, null);
INSERT INTO `reservations` (`RESERVATION_ID`,`USER_ID`,`reservation_time`,`total_people`,`WAITER_ID`) VALUES
(5, 6, 20, 3, null);


INSERT INTO `reservation_users` (`RESERVATION_ID`, `USER_ID` ) VALUES
(5,6);
INSERT INTO `reservation_users` (`RESERVATION_ID`, `USER_ID` ) VALUES
(5,7);
INSERT INTO `reservation_users` (`RESERVATION_ID`, `USER_ID` ) VALUES
(1,9);
INSERT INTO `reservation_users` (`RESERVATION_ID`, `USER_ID` ) VALUES
(2,10);
INSERT INTO `reservation_users` (`RESERVATION_ID`, `USER_ID` ) VALUES
(3,11);
INSERT INTO `reservation_users` (`RESERVATION_ID`, `USER_ID` ) VALUES
(4,12);

INSERT INTO `orders` (`ORDER_ID`,`RESERVATION_ID` , `USER_ID`, `cost`,`complete`) VALUES  
(1,5,6,0,0);
INSERT INTO `orders` (`ORDER_ID`,`RESERVATION_ID` , `USER_ID`, `cost`,`complete`) VALUES  
(2,5,7,0,0);

INSERT INTO `reservation_orders` (`ORDER_ID`, `RESERVATION_ID`) VALUES
(1,5);
INSERT INTO `reservation_orders` (`ORDER_ID`, `RESERVATION_ID`) VALUES
(2,5);


INSERT INTO `menu_items` (`ITEM_ID`, `name`, `category`, `description`, `image`, `price`) VALUES
(1, 'chick soup', 'soup', 'its a soup', 'soupImage', 6.99);
INSERT INTO `menu_items` (`ITEM_ID`, `name`, `category`, `description`, `image`, `price`) VALUES
(2, 'chicken', 'meat', 'prettyy fowel', 'chickenImage', 10.99);

INSERT INTO `order_menu_items` (`ORDER_ID`, `ITEM_ID`,`USER_ID`) VALUES
(1,1,1);
INSERT INTO `order_menu_items` (`ORDER_ID`, `ITEM_ID`,`USER_ID`) VALUES
(2,2,2);






