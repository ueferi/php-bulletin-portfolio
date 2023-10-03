-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2023-07-26 15:51:34
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `mini_bbs`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `member_id`, `created`, `modified`) VALUES
(8, 9, 6, '2023-07-26 13:15:16', '2023-07-26 04:15:16'),
(9, 3, 6, '2023-07-26 13:22:21', '2023-07-26 04:22:21'),
(10, 9, 8, '2023-07-26 14:31:52', '2023-07-26 05:31:52'),
(19, 3, 8, '2023-07-26 14:32:59', '2023-07-26 05:32:59');

-- --------------------------------------------------------

--
-- テーブルの構造 `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `password`, `picture`, `created`, `modified`) VALUES
(4, 'よっぺ', 'info@co.jp', '8cb2237d0679ca88db6464eac60da96345513964', '202307091259himawari.jpg', '2023-07-25 09:13:06', '2023-07-25 00:13:06'),
(5, 'よっぺ', 'smile.make.happy.427@gmail.com', '07e69c2d727acec7c724b99a6fad6de09bfd1d76', '20230725111042himawari.jpg', '2023-07-25 11:10:43', '2023-07-25 02:10:43'),
(6, '師範', 'like@co.jp', '3efc94ebcda70a8688831427ba5d41ddbfad9e25', '2023072511271620201205_120227.jpg', '2023-07-25 11:27:24', '2023-07-25 02:27:24'),
(8, 'haseken', 'haseken', '05114ed2ca8dcb2e9c5442ed532e31206e3a0b3e', '20230726143136item1.jpg', '2023-07-26 14:31:41', '2023-07-26 05:31:41');

-- --------------------------------------------------------

--
-- テーブルの構造 `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `member_id` int(11) NOT NULL,
  `reply_post_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `posts`
--

INSERT INTO `posts` (`id`, `message`, `member_id`, `reply_post_id`, `created`, `modified`) VALUES
(3, 'よろしくお願いします。', 5, 0, '2023-07-25 11:11:31', '2023-07-25 02:11:31'),
(9, 'こんにちは', 4, 0, '2023-07-25 14:00:57', '2023-07-25 05:00:57'),
(10, 'こんにちは、初めまして', 6, 0, '2023-07-25 14:03:15', '2023-07-25 05:03:15'),
(11, 'ああああああ', 6, 0, '2023-07-25 14:04:58', '2023-07-25 05:04:58'),
(12, 'にこ\r\nにこ\r\n\r\nにこ\r\n\r\nにこ\r\n', 6, 0, '2023-07-25 14:05:16', '2023-07-25 05:05:16'),
(13, 'たたたたたたたた', 6, 0, '2023-07-25 14:05:27', '2023-07-25 05:05:27'),
(14, 'ああああああああああああ', 6, 0, '2023-07-25 14:05:37', '2023-07-25 05:05:37'),
(15, 'ううううう', 6, 0, '2023-07-25 14:36:04', '2023-07-25 05:36:04'),
(16, 'えええええ', 6, 0, '2023-07-25 14:36:08', '2023-07-25 05:36:08'),
(17, 'おおおおお', 6, 0, '2023-07-25 14:36:12', '2023-07-25 05:36:12'),
(18, 'あああああ', 6, 0, '2023-07-26 14:40:37', '2023-07-26 05:40:37'),
(19, 'いいいい', 6, 0, '2023-07-26 14:40:42', '2023-07-26 05:40:42'),
(20, 'よろしくね', 5, 0, '2023-07-26 14:44:04', '2023-07-26 05:44:04');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- テーブルの AUTO_INCREMENT `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
