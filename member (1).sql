-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024 年 12 月 04 日 03:05
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `系學會`
--

-- --------------------------------------------------------

--
-- 資料表結構 `member`
--

CREATE TABLE `member` (
  `name` varchar(20) NOT NULL,
  `stu_id` int(10) NOT NULL,
  `contact` varchar(10) NOT NULL,
  `admission` date NOT NULL,
  `position` varchar(20) DEFAULT NULL,
  `activities` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `member`
--

INSERT INTO `member` (`name`, `stu_id`, `contact`, `admission`, `position`, `activities`) VALUES
('Sean', 412344158, '0983222555', '2024-12-01', NULL, '宿營'),
('Cindy', 412401122, '0958711622', '2021-11-01', '助教', '宿營'),
('Karina', 412401267, '0966514888', '2024-11-30', NULL, '制服趴'),
('Wonyoung', 412401271, '0925486332', '2024-11-20', '經濟課代', '一日營'),
('Monica', 412401430, '0935875966', '2024-08-31', '班代', NULL),
('Lisa', 412401431, '0968352475', '0000-00-00', NULL, '一日營'),
('Lizzy', 412401455, '0956834658', '2024-11-20', NULL, '一日營'),
('Lucy', 412401456, '0927658411', '2024-12-01', NULL, NULL),
('Sofia', 412402745, '0935268875', '2024-12-01', NULL, '宿營'),
('Bryan', 412407122, '0968542273', '2024-11-20', NULL, '制服趴'),
('Nicole', 412578406, '0965822473', '2024-12-01', NULL, '一日營'),
('Jimm', 412578481, '0958632623', '2024-12-01', '英語課代', NULL),
('Alex', 412587711, '0911452362', '2024-12-01', NULL, NULL),
('Jenny', 412678412, '0922658355', '2024-11-26', NULL, '制服趴'),
('Mark', 412701255, '0932655899', '2024-11-03', '統計課代', '一日營');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`stu_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
