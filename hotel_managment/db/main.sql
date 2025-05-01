SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




CREATE TABLE `inventory` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `inventory` (`item_id`, `item_name`, `quantity`, `unit`, `last_updated`) VALUES
(1, 'sss', 1, '5', '2025-02-17 12:23:06'),
(2, 'hello', 2, '40', '2025-02-17 12:29:05');


CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `menu_items` (`id`, `name`, `description`, `category`, `price`, `available`) VALUES
(3, 'Item 2', NULL, '', 150.00, 1),
(5, 'Item 1', NULL, '', 100.00, 1),
(7, 'Item 3', NULL, '', 200.00, 1),
(12, 'Margherita Pizza', 'A classic pizza with cheese and tomato', 'Pizza', 8.99, 1),
(13, 'Pepperoni Pizza', 'A pizza topped with pepperoni', 'Pizza', 9.99, 1),
(14, 'Cheeseburger', 'A beef burger with cheese', 'Burgers', 7.49, 1),
(15, 'French Fries', 'Crispy fries', 'Sides', 3.99, 1),
(16, 'Pasta Alfredo', 'Creamy Alfredo pasta', 'Pasta', 10.99, 1),
(17, 'Margherita Pizza', 'A classic pizza with cheese and tomato', 'Pizza', 8.99, 1),
(18, 'Pepperoni Pizza', 'A pizza topped with pepperoni', 'Pizza', 9.99, 1),
(19, 'Cheeseburger', 'A beef burger with cheese', 'Burgers', 7.49, 1),
(20, 'French Fries', 'Crispy fries', 'Sides', 3.99, 1),
(21, 'Pasta Alfredo', 'Creamy Alfredo pasta', 'Pasta', 10.99, 1),
(22, 'Grilled Chicken', 'Juicy grilled chicken', 'Mains', 12.99, 1),
(23, 'Chocolate Cake', 'Rich chocolate cake', 'Desserts', 4.99, 1),
(24, 'Orange Juice', 'Freshly squeezed orange juice', 'Drinks', 2.99, 1),
(25, 'Coffee', 'Hot brewed coffee', 'Drinks', 2.49, 1);


CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_number` int(11) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Preparing','Completed') DEFAULT 'Pending',
  `order_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(255) DEFAULT NULL,
  `order_details` varchar(255) DEFAULT NULL,
  `table_status` enum('Empty','Booked') DEFAULT 'Empty',
  `payment_status` enum('Pending','Paid') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `orders` (`order_id`, `user_id`, `table_number`, `total_amount`, `status`, `order_time`, `created_at`, `customer_name`, `order_details`, `table_status`, `payment_status`) VALUES
(60, 6, 1, 500.00, 'Pending', '2025-02-18 22:22:38', '2025-02-18 22:22:38', NULL, '', 'Booked', 'Pending'),
(61, 6, 2, 750.00, 'Pending', '2025-02-18 22:22:38', '2025-02-18 22:22:38', NULL, '', 'Booked', 'Pending'),
(62, 6, 3, 1200.00, 'Pending', '2025-02-18 22:22:38', '2025-02-18 22:22:38', NULL, '', 'Booked', 'Pending'),
(63, 6, 4, 7000.00, 'Pending', '2025-02-18 22:22:38', '2025-02-18 22:22:38', NULL, '', 'Booked', 'Pending'),
(64, 6, 5, 7899.00, 'Pending', '2025-02-18 22:22:38', '2025-02-18 22:22:38', NULL, '', 'Booked', 'Pending'),
(65, 6, 6, 450.00, 'Pending', '2025-02-18 22:22:38', '2025-02-18 22:22:38', NULL, '', 'Booked', 'Pending');


CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `cgst` decimal(10,2) NOT NULL,
  `sgst` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `order_items` (`item_id`, `order_id`, `menu_id`, `quantity`, `total_price`, `cgst`, `sgst`, `grand_total`) VALUES
(1, 61, 5, 12, 200.00, 18.00, 18.00, 236.00),
(2, 62, 3, 14, 150.00, 13.50, 13.50, 177.00),
(3, 63, 7, 13, 450.00, 40.50, 40.50, 531.00);


CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_status` enum('Pending','Paid') DEFAULT 'Pending',
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `room_type` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `bed_count` int(11) NOT NULL,
  `status` enum('Available','Booked') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_days` int(11) NOT NULL,
  `total_room_price` decimal(10,2) NOT NULL,
  `gst` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `rooms` (`id`, `room_number`, `room_type`, `price`, `bed_count`, `status`, `user_id`, `total_days`, `total_room_price`, `gst`, `grand_total`) VALUES
(1, '101', 'Delux', 7000.00, 8, 'Available', 5, 7, 7000.00, 70.00, 70.00);



CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Waiter','Kitchen','Billing') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `users` (`user_id`, `name`, `phone_number`, `username`, `password`, `role`) VALUES
(5, 'skanda', '7892459013', 'user_1234', 'skanda', 'Billing'),
(6, 'John Doe', '9876543210', 'johndoe', 'password123', 'Waiter'),
(101, 'Joh', '78924590137', 'joh101', '', 'Waiter'),
(102, 'Jan', '78924590130', 'jan102', '', 'Waiter'),
(103, 'Mik', '123456789', 'mik103', '', 'Waiter'),
(104, 'EmilDavis', '1234', 'emily104', '', 'Waiter'),
(105, 'Davi', '123456', 'davi105', '', 'Admin'),
(106, 'SophBrown', '1234567', 'soph106', '', 'Kitchen');


ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);


ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `waiter_id` (`user_id`);


ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);


ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);


ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);


ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `unique_phone_number` (`phone_number`);


ALTER TABLE `inventory`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;


ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`menu_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;


ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;


ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;
