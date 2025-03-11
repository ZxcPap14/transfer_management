-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 12 2025 г., 01:32
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
-- Структура таблицы `department`
--

CREATE TABLE `department` (
  `id` int NOT NULL,
  `department` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `department`
--

INSERT INTO `department` (`id`, `department`) VALUES
(1, 'asd');

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `parts`
--

CREATE TABLE `parts` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `designation` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `transfer_requests`
--

CREATE TABLE `transfer_requests` (
  `id` int NOT NULL,
  `sender_department_id` int NOT NULL,
  `receiver_department_id` int NOT NULL,
  `status` enum('Создана','Подтверждена','Отправлена','Получена') DEFAULT 'Создана',
  `created_by` int NOT NULL,
  `confirmed_by` int DEFAULT NULL,
  `sent_by` int DEFAULT NULL,
  `received_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `received_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `transfer_request_logs`
--

CREATE TABLE `transfer_request_logs` (
  `id` int NOT NULL,
  `request_id` int NOT NULL,
  `changed_by` int NOT NULL,
  `old_status` enum('Создана','Подтверждена','Отправлена','Получена') NOT NULL,
  `new_status` enum('Создана','Подтверждена','Отправлена','Получена') NOT NULL,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `transfer_request_parts`
--

CREATE TABLE `transfer_request_parts` (
  `id` int NOT NULL,
  `request_id` int NOT NULL,
  `part_id` int NOT NULL,
  `quantity` int NOT NULL
) ;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('0','1','2') NOT NULL COMMENT '0 - Админ, 1 - Диспетчер, 2 - Начальник смены',
  `department_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `department_id`) VALUES
(2, 'asd', '$2y$10$iMClVPldCT2W70scBajzfuhUDZ4YAKl5UTxPjQFCDfvkblicGkH9q', '0', 1),
(3, 'qwe', '76d80224611fc919a5d54f0ff9fba446', '0', 1),
(7, '1', 'c4ca4238a0b923820dcc509a6f75849b', '1', 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department` (`department`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `parts`
--
ALTER TABLE `parts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `designation` (`designation`);

--
-- Индексы таблицы `transfer_requests`
--
ALTER TABLE `transfer_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_department_id` (`sender_department_id`),
  ADD KEY `receiver_department_id` (`receiver_department_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `confirmed_by` (`confirmed_by`),
  ADD KEY `sent_by` (`sent_by`),
  ADD KEY `received_by` (`received_by`);

--
-- Индексы таблицы `transfer_request_logs`
--
ALTER TABLE `transfer_request_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Индексы таблицы `transfer_request_parts`
--
ALTER TABLE `transfer_request_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `part_id` (`part_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `department`
--
ALTER TABLE `department`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `parts`
--
ALTER TABLE `parts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transfer_requests`
--
ALTER TABLE `transfer_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transfer_request_logs`
--
ALTER TABLE `transfer_request_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transfer_request_parts`
--
ALTER TABLE `transfer_request_parts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `transfer_requests`
--
ALTER TABLE `transfer_requests`
  ADD CONSTRAINT `transfer_requests_ibfk_1` FOREIGN KEY (`sender_department_id`) REFERENCES `department` (`id`),
  ADD CONSTRAINT `transfer_requests_ibfk_2` FOREIGN KEY (`receiver_department_id`) REFERENCES `department` (`id`),
  ADD CONSTRAINT `transfer_requests_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transfer_requests_ibfk_4` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transfer_requests_ibfk_5` FOREIGN KEY (`sent_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transfer_requests_ibfk_6` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `transfer_request_logs`
--
ALTER TABLE `transfer_request_logs`
  ADD CONSTRAINT `transfer_request_logs_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `transfer_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transfer_request_logs_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `transfer_request_parts`
--
ALTER TABLE `transfer_request_parts`
  ADD CONSTRAINT `transfer_request_parts_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `transfer_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transfer_request_parts_ibfk_2` FOREIGN KEY (`part_id`) REFERENCES `parts` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
