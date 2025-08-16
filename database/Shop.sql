-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.0
-- Время создания: Авг 16 2025 г., 22:31
-- Версия сервера: 8.0.41
-- Версия PHP: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Shop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Baskets`
--

CREATE TABLE `Baskets` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `totalSum` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Category`
--

CREATE TABLE `Category` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Category`
--

INSERT INTO `Category` (`id`, `name`) VALUES
(1, 'toys'),
(2, 'fructs');

-- --------------------------------------------------------

--
-- Структура таблицы `ImagesProducts`
--

CREATE TABLE `ImagesProducts` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `ImagesProducts`
--

INSERT INTO `ImagesProducts` (`id`, `product_id`, `image`) VALUES
(5, 7, 'C:\\OSPanel\\home\\web3-14-08-2025\\controllers/../imageForProduct/_eW2WX-312c10f6e2f6cfee7afd836555965079.jpeg'),
(6, 7, 'C:\\OSPanel\\home\\web3-14-08-2025\\controllers/../imageForProduct/gq7etm--ESntrplLKwfqhROk58MUTAqyFxib9z-haqPK-4JWtBT_5ABv7zGqktFjc6ccKkeI97HajB5.jpg'),
(7, 8, 'C:\\OSPanel\\home\\web3-14-08-2025\\controllers/../imageForProduct/aE2qGx-312c10f6e2f6cfee7afd836555965079.jpeg'),
(8, 8, 'C:\\OSPanel\\home\\web3-14-08-2025\\controllers/../imageForProduct/uyOpr4--ESntrplLKwfqhROk58MUTAqyFxib9z-haqPK-4JWtBT_5ABv7zGqktFjc6ccKkeI97HajB5.jpg'),
(9, 9, 'C:\\OSPanel\\home\\web3-14-08-2025\\controllers/../imageForProduct/98QQzR-312c10f6e2f6cfee7afd836555965079.jpeg'),
(10, 9, 'C:\\OSPanel\\home\\web3-14-08-2025\\controllers/../imageForProduct/6CkBUa--ESntrplLKwfqhROk58MUTAqyFxib9z-haqPK-4JWtBT_5ABv7zGqktFjc6ccKkeI97HajB5.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `Orders`
--

CREATE TABLE `Orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `data_of_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `general_price` int NOT NULL,
  `track_code` varchar(255) NOT NULL,
  `status_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Orders`
--

INSERT INTO `Orders` (`id`, `user_id`, `data_of_creation`, `general_price`, `track_code`, `status_id`) VALUES
(16, 4, '2025-08-15 22:24:19', 1775, 'yX7uRIB3XQrh', 3),
(17, 5, '2025-08-16 15:48:55', 3195, 'fKLVByzzLDng', 3),
(18, 5, '2025-08-16 16:28:43', 710, 'OYjqqOS4inru', 1),
(19, 4, '2025-08-16 19:28:29', 8875, '6FOJvnI-gZ0K', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `OrdersProducts`
--

CREATE TABLE `OrdersProducts` (
  `id` int NOT NULL,
  `products_id` int NOT NULL,
  `orders_id` int NOT NULL,
  `count` int NOT NULL,
  `totalPrice` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `OrdersProducts`
--

INSERT INTO `OrdersProducts` (`id`, `products_id`, `orders_id`, `count`, `totalPrice`) VALUES
(11, 8, 16, 3, 3),
(12, 9, 16, 2, 2),
(13, 7, 17, 7, 7),
(14, 8, 17, 2, 2),
(15, 8, 18, 2, 2),
(16, 8, 19, 1, 1),
(17, 9, 19, 24, 24);

-- --------------------------------------------------------

--
-- Структура таблицы `ProductBasket`
--

CREATE TABLE `ProductBasket` (
  `id` int NOT NULL,
  `products_id` int NOT NULL,
  `basket_id` int NOT NULL,
  `count` int NOT NULL,
  `totalPrice` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Products`
--

CREATE TABLE `Products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Products`
--

INSERT INTO `Products` (`id`, `name`, `category_id`, `quantity`, `price`) VALUES
(7, 'jjj12d', 2, 500, 355),
(8, 'Toys Cars 4', 2, 494, 355),
(9, 'TeddyBears3', 2, 476, 355);

-- --------------------------------------------------------

--
-- Структура таблицы `Reason_cancellation`
--

CREATE TABLE `Reason_cancellation` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Reason_cancellation`
--

INSERT INTO `Reason_cancellation` (`id`, `order_id`, `text`) VALUES
(2, 16, 'potomy chto'),
(3, 16, 'potomy chto'),
(4, 17, 'potomy chto');

-- --------------------------------------------------------

--
-- Структура таблицы `Role`
--

CREATE TABLE `Role` (
  `id` int NOT NULL,
  `role` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Role`
--

INSERT INTO `Role` (`id`, `role`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Структура таблицы `Status_orders`
--

CREATE TABLE `Status_orders` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Status_orders`
--

INSERT INTO `Status_orders` (`id`, `name`) VALUES
(1, 'Заказ в обработке'),
(2, 'Готов к получению'),
(3, 'Отменён');

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` int NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `balance` int NOT NULL DEFAULT '0',
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `role_id` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`id`, `first_name`, `last_name`, `email`, `password`, `balance`, `token`, `role_id`) VALUES
(4, 'Egr', 'Frkv', 'email@mail.ru', '$2y$13$FRyvnW5Q27QbFRvAsIjcwuRsH3WGJqhjdfyvVjPl8nizOWL714xSG', 125, 'smGiR7A7HKcwCTxHYqfJkFogHr4Fh97r', 2),
(5, 'ff', 'f', 'email2@mail.ru', '$2y$13$HGAPIgEm0tOlSUzLS.A1cuX1t9Z84pZkG0kkYAh00xwZrqOjx6Yoe', 0, NULL, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Baskets`
--
ALTER TABLE `Baskets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ImagesProducts`
--
ALTER TABLE `ImagesProducts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `OrdersProducts`
--
ALTER TABLE `OrdersProducts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_orders` (`orders_id`),
  ADD KEY `id_products` (`products_id`);

--
-- Индексы таблицы `ProductBasket`
--
ALTER TABLE `ProductBasket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basket_id` (`basket_id`),
  ADD KEY `products_id` (`products_id`);

--
-- Индексы таблицы `Products`
--
ALTER TABLE `Products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `Reason_cancellation`
--
ALTER TABLE `Reason_cancellation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Индексы таблицы `Role`
--
ALTER TABLE `Role`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Status_orders`
--
ALTER TABLE `Status_orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Baskets`
--
ALTER TABLE `Baskets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `Category`
--
ALTER TABLE `Category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `ImagesProducts`
--
ALTER TABLE `ImagesProducts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `Orders`
--
ALTER TABLE `Orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `OrdersProducts`
--
ALTER TABLE `OrdersProducts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `ProductBasket`
--
ALTER TABLE `ProductBasket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `Products`
--
ALTER TABLE `Products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `Reason_cancellation`
--
ALTER TABLE `Reason_cancellation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `Role`
--
ALTER TABLE `Role`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `Status_orders`
--
ALTER TABLE `Status_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Baskets`
--
ALTER TABLE `Baskets`
  ADD CONSTRAINT `baskets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `ImagesProducts`
--
ALTER TABLE `ImagesProducts`
  ADD CONSTRAINT `imagesproducts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `Products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Orders`
--
ALTER TABLE `Orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `Status_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `OrdersProducts`
--
ALTER TABLE `OrdersProducts`
  ADD CONSTRAINT `ordersproducts_ibfk_1` FOREIGN KEY (`orders_id`) REFERENCES `Orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordersproducts_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `Products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `ProductBasket`
--
ALTER TABLE `ProductBasket`
  ADD CONSTRAINT `productbasket_ibfk_1` FOREIGN KEY (`basket_id`) REFERENCES `Baskets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `productbasket_ibfk_2` FOREIGN KEY (`products_id`) REFERENCES `Products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Products`
--
ALTER TABLE `Products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `Category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Reason_cancellation`
--
ALTER TABLE `Reason_cancellation`
  ADD CONSTRAINT `reason_cancellation_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `Orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `Role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
