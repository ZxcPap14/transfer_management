-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 24 2025 г., 20:06
-- Версия сервера: 8.0.30
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `transfer_management`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounting_orders`
--

CREATE TABLE `accounting_orders` (
  `id` int NOT NULL,
  `order_number` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `customer` varchar(255) NOT NULL,
  `contract_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `accounting_orders`
--

INSERT INTO `accounting_orders` (`id`, `order_number`, `description`, `customer`, `contract_date`) VALUES
(1, '1234', '123', '123', '2025-04-23 12:56:49');

-- --------------------------------------------------------

--
-- Структура таблицы `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `department_number` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `departments`
--

INSERT INTO `departments` (`id`, `name`, `department_number`) VALUES
(1, '1231', '1233'),
(7, '321', '231');

-- --------------------------------------------------------

--
-- Структура таблицы `parts`
--

CREATE TABLE `parts` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `nomenclature_number` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `parts`
--

INSERT INTO `parts` (`id`, `name`, `nomenclature_number`) VALUES
(1, 'Шасси1', 'A-1001'),
(2, 'Каркас', 'B-200'),
(3, 'Проводка', 'C-300'),
(4, 'Фундамент', 'D-400'),
(5, 'Насосный блок', 'E-500'),
(6, 'Контроллер', 'F-600'),
(7, 'asd', '123()11');

-- --------------------------------------------------------

--
-- Структура таблицы `parts_in_stock`
--

CREATE TABLE `parts_in_stock` (
  `id` int NOT NULL,
  `part_id` int NOT NULL,
  `department_id` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `nomenclature_number` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `name`, `designation`, `nomenclature_number`) VALUES
(1, 'Трамвай', 'Трамвай', '53454567-В'),
(2, 'Нефтекачалка', 'Нефтекачалка', '45146272-А'),
(3, 'qwerty', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `product_plan`
--

CREATE TABLE `product_plan` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `year` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `order_number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `product_plan`
--

INSERT INTO `product_plan` (`id`, `product_id`, `year`, `quantity`, `order_number`) VALUES
(4, 1, 2025, 1, 1234),
(5, 3, 2026, 23, 1234);

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE `requests` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `request_parts`
--

CREATE TABLE `request_parts` (
  `id` int NOT NULL,
  `request_id` int DEFAULT NULL,
  `part_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `accounting_order_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `stages`
--

CREATE TABLE `stages` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `stages`
--

INSERT INTO `stages` (`id`, `product_id`, `name`) VALUES
(1, 1, 'Ходовая часть'),
(2, 1, 'Кузов'),
(3, 1, 'Электрика'),
(4, 2, 'Основание'),
(5, 2, 'Насос'),
(6, 2, 'Электроника'),
(7, 3, 'qwe'),
(8, 3, 'asd');

-- --------------------------------------------------------

--
-- Структура таблицы `stage_parts`
--

CREATE TABLE `stage_parts` (
  `id` int NOT NULL,
  `stage_id` int DEFAULT NULL,
  `part_id` int DEFAULT NULL,
  `quantity_required` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `stage_parts`
--

INSERT INTO `stage_parts` (`id`, `stage_id`, `part_id`, `quantity_required`) VALUES
(1, 1, 1, 4),
(2, 2, 2, 2),
(3, 3, 3, 10),
(4, 4, 4, 1),
(5, 5, 5, 1),
(6, 6, 6, 3),
(10, 7, 7, 123),
(11, 8, 6, 222);

-- --------------------------------------------------------

--
-- Структура таблицы `transfers`
--

CREATE TABLE `transfers` (
  `id` int NOT NULL,
  `part_id` int NOT NULL,
  `quantity` int NOT NULL,
  `from_department_id` int NOT NULL,
  `to_department_id` int NOT NULL,
  `user_id` int NOT NULL,
  `order_number` varchar(255) NOT NULL,
  `status` enum('ожидает подтверждения','отправлено') NOT NULL DEFAULT 'ожидает подтверждения',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `transfers`
--

INSERT INTO `transfers` (`id`, `part_id`, `quantity`, `from_department_id`, `to_department_id`, `user_id`, `order_number`, `status`, `created_at`) VALUES
(1, 1, 2, 1, 7, 2, '2', 'ожидает подтверждения', '2025-04-15 14:17:14'),
(2, 7, 12, 1, 7, 2, '321', 'ожидает подтверждения', '2025-04-18 09:41:40'),
(3, 7, 123, 1, 7, 2, '321', 'ожидает подтверждения', '2025-04-18 09:47:21'),
(4, 2, 1, 1, 7, 2, '321', 'отправлено', '2025-04-18 09:47:42');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dispatcher','shift_manager') NOT NULL,
  `full_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `full_name`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', '123'),
(2, '1233', 'e034fb6b66aacc1d48f445ddfb08da98', 'dispatcher', '1233'),
(3, '222', 'bcbe3365e6ac95ea2c0343a2395834dd', 'dispatcher', '222'),
(4, '321', 'caf1a3dfb505ffed0d024130f58c5cfa', 'shift_manager', '321'),
(5, '1', 'c4ca4238a0b923820dcc509a6f75849b', 'admin', '1');

-- --------------------------------------------------------

--
-- Структура таблицы `user_departments`
--

CREATE TABLE `user_departments` (
  `user_id` int NOT NULL,
  `department_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `user_departments`
--

INSERT INTO `user_departments` (`user_id`, `department_id`) VALUES
(1, 1),
(1, 7);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `accounting_orders`
--
ALTER TABLE `accounting_orders`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `parts`
--
ALTER TABLE `parts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomenclature_number` (`nomenclature_number`);

--
-- Индексы таблицы `parts_in_stock`
--
ALTER TABLE `parts_in_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `part_id` (`part_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `product_plan`
--
ALTER TABLE `product_plan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Индексы таблицы `request_parts`
--
ALTER TABLE `request_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `part_id` (`part_id`),
  ADD KEY `accounting_order_id` (`accounting_order_id`);

--
-- Индексы таблицы `stages`
--
ALTER TABLE `stages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Индексы таблицы `stage_parts`
--
ALTER TABLE `stage_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stage_id` (`stage_id`),
  ADD KEY `part_id` (`part_id`);

--
-- Индексы таблицы `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `part_id` (`part_id`),
  ADD KEY `from_department_id` (`from_department_id`),
  ADD KEY `to_department_id` (`to_department_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индексы таблицы `user_departments`
--
ALTER TABLE `user_departments`
  ADD PRIMARY KEY (`user_id`,`department_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `accounting_orders`
--
ALTER TABLE `accounting_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `parts`
--
ALTER TABLE `parts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `parts_in_stock`
--
ALTER TABLE `parts_in_stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `product_plan`
--
ALTER TABLE `product_plan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `request_parts`
--
ALTER TABLE `request_parts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `stages`
--
ALTER TABLE `stages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `stage_parts`
--
ALTER TABLE `stage_parts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблицы `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `parts_in_stock`
--
ALTER TABLE `parts_in_stock`
  ADD CONSTRAINT `parts_in_stock_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `parts` (`id`),
  ADD CONSTRAINT `parts_in_stock_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Ограничения внешнего ключа таблицы `product_plan`
--
ALTER TABLE `product_plan`
  ADD CONSTRAINT `product_plan_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ограничения внешнего ключа таблицы `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Ограничения внешнего ключа таблицы `request_parts`
--
ALTER TABLE `request_parts`
  ADD CONSTRAINT `request_parts_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`id`),
  ADD CONSTRAINT `request_parts_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `parts` (`id`),
  ADD CONSTRAINT `request_parts_ibfk_3` FOREIGN KEY (`accounting_order_id`) REFERENCES `accounting_orders` (`id`);

--
-- Ограничения внешнего ключа таблицы `stages`
--
ALTER TABLE `stages`
  ADD CONSTRAINT `stages_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ограничения внешнего ключа таблицы `stage_parts`
--
ALTER TABLE `stage_parts`
  ADD CONSTRAINT `stage_parts_ibfk_1` FOREIGN KEY (`stage_id`) REFERENCES `stages` (`id`),
  ADD CONSTRAINT `stage_parts_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `parts` (`id`);

--
-- Ограничения внешнего ключа таблицы `transfers`
--
ALTER TABLE `transfers`
  ADD CONSTRAINT `transfers_ibfk_1` FOREIGN KEY (`part_id`) REFERENCES `parts` (`id`),
  ADD CONSTRAINT `transfers_ibfk_2` FOREIGN KEY (`from_department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `transfers_ibfk_3` FOREIGN KEY (`to_department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `transfers_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_departments`
--
ALTER TABLE `user_departments`
  ADD CONSTRAINT `user_departments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_departments_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
