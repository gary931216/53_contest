-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2023-06-20 17:58:15
-- 伺服器版本： 10.4.28-MariaDB
-- PHP 版本： 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `00_module_d`
--

-- --------------------------------------------------------

--
-- 資料表結構 `comment`
--

CREATE TABLE `comment` (
  `id` int(100) NOT NULL,
  `image_id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `text` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `comment`
--

INSERT INTO `comment` (`id`, `image_id`, `user_id`, `text`) VALUES
(2, 1, 1, 'a'),
(3, 1, 1, 'ab'),
(4, 1, 1, 'abc');

-- --------------------------------------------------------

--
-- 資料表結構 `image`
--

CREATE TABLE `image` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(500) NOT NULL,
  `description` varchar(500) NOT NULL,
  `width` int(50) NOT NULL,
  `height` int(50) NOT NULL,
  `mimetype` varchar(50) NOT NULL,
  `view` int(50) NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `image`
--

INSERT INTO `image` (`id`, `user_id`, `url`, `title`, `description`, `width`, `height`, `mimetype`, `view`, `updated_at`, `created_at`, `deleted_at`) VALUES
(1, 1, '/storage/S7a7KKlN6Rp5AtfKcV8Xn7iqx7DTW642iq9v5s6F.png', 'b', 'b', 10, 10, 'image/png', 0, '2023-06-20 14:19:54', '2023-06-20 14:19:54', NULL),
(2, 1, '/storage/gDz9AEdQTjd6csNWukSHOCHFxoCq7TxgNRLJsuE5.png', 'afaa', 'gdsaa', 10, 10, 'image/png', 0, '2023-06-20 14:39:17', '2023-06-20 14:39:17', NULL);

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(100) NOT NULL,
  `email` varchar(500) NOT NULL,
  `nickname` varchar(500) NOT NULL,
  `password` varchar(500) NOT NULL,
  `profile_image` varchar(500) NOT NULL,
  `type` enum('ADMIN','USER') NOT NULL,
  `access_token` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `email`, `nickname`, `password`, `profile_image`, `type`, `access_token`) VALUES
(1, 'admin@web.tw', 'admin', '$2y$10$b2VBhOthiLumfp704yWu0.naqzNiCVh4iwpDC9atJmMv.zcNFVmHK', '', 'ADMIN', '291124ddb96730a006d48d4b74fa37f3a1e040dffbe2549b3e0778e7bbf51eb2'),
(2, 'user@web.tw', 'user', '$2y$10$WmJnmquU3HdFEWnzL5b9YuwlzDSl4GB29PTyARdN2GuE3rhPDjqC.', '', 'USER', ''),
(4, 'test1@web.tw', 'test1', '$2y$10$Nolmp3BXYg7DaIzCHXPpf.UTyMjTWxLZK8QbhqZd4XAdaoz9V44Su', '/storage/KjtY9uGIkYFRY81czJ6WYjCv17nMb0HFR5DwFUrO.png', 'USER', '7ff54fdb45b10da0ad5145df120f4d87e38887a472a258918049972b10de1b54');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `image_id` (`image_id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `image`
--
ALTER TABLE `image`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`image_id`) REFERENCES `image` (`id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- 資料表的限制式 `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
