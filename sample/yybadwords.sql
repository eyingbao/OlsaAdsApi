-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-01-19 03:27:21
-- 服务器版本： 5.7.14
-- PHP Version: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yybadwords`
--

-- --------------------------------------------------------

--
-- 表的结构 `ads_column`
--

CREATE TABLE `ads_column` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pid` smallint(6) NOT NULL,
  `controller` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(2) NOT NULL,
  `listorder` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `ads_column`
--

INSERT INTO `ads_column` (`id`, `name`, `pid`, `controller`, `icon`, `status`, `listorder`) VALUES
(1, '广告系列', 0, 'Home/Campaign', 'fa fa-cube', 1, 102),
(2, '日预算异常', 1, 'Home/Campaign/index', '', 1, 97),
(3, '广告组', 0, 'Home/AdGroup', 'fa fa-share-alt', 1, 99),
(4, '广告组（CTR）异常', 3, 'Home/AdGroup/index', '', 1, 103),
(5, '系统设置', 0, 'Home/Manager', 'fa fa-cog', 1, 96),
(6, '用户管理', 5, 'Home/Manager/index', '', 1, 99),
(7, '消耗异常', 1, 'Home/Campaign/costErr', '', 1, 103),
(8, '关键词（得分）异常', 3, 'Home/AdGroup/keywordsErr', '', 1, 98),
(9, '广告异常', 3, 'Home/AdGroup/adErr', '', 1, 101),
(10, '同步MCC账户', 5, 'Home/Manager/update', '', 0, 0),
(11, '账户', 0, 'Home/Mcc', 'fa fa-user', 1, 103),
(12, '余额提醒', 11, 'Home/Mcc/index', '', 1, 98),
(13, '投放渠道', 1, 'Home/Mcc/deliveryChannel', '', 1, 102),
(14, 'MCC（总消耗）', 11, 'Home/Mcc/monthCost', '', 1, 101),
(15, '广告语（CTR）异常', 3, 'Home/AdGroup/adCtrErr', '', 1, 0),
(16, '广告语（数量）异常', 3, 'Home/AdGroup/adCountErr', '', 1, 102),
(17, 'MCC（活跃数）', 11, 'Home/Mcc/active', '', 1, 100),
(18, '转化数据', 11, 'Home/Mcc/tools', '', 1, 0),
(19, '转化次数对比', 1, 'Home/Campaign/conversionsContrast', '', 1, 101),
(20, 'CPA对比', 1, 'Home/Campaign/cpaContrast', '', 1, 99),
(21, '更新MCC', 5, 'Home/Manager/updateMcc', '', 1, 0),
(22, 'olsa日报', 11, 'Home/Mcc/olsa', '', 0, 102),
(23, '账户评级', 11, 'Home/Mcc/alertedList', '', 1, 97);

-- --------------------------------------------------------

--
-- 表的结构 `ads_manager`
--

CREATE TABLE `ads_manager` (
  `id` int(11) NOT NULL,
  `mcc` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `account` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `create_time` int(11) NOT NULL,
  `login_time` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tel` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `ads_manager`
--

INSERT INTO `ads_manager` (`id`, `mcc`, `account`, `nickname`, `password`, `create_time`, `login_time`, `status`, `email`, `tel`) VALUES
(1, '376-046-0982', 'admin@admin.com', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 1435652670, 1435652670, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `n_accountinfo`
--

CREATE TABLE `n_accountinfo` (
  `id` int(11) NOT NULL,
  `customer_Id` varchar(12) DEFAULT NULL,
  `loginName` varchar(50) DEFAULT NULL,
  `loginPassword` varchar(32) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `address` varchar(70) DEFAULT NULL,
  `compaign` varchar(50) DEFAULT NULL,
  `mcc` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `n_accountinfo`
--

INSERT INTO `n_accountinfo` (`id`, `customer_Id`, `loginName`, `loginPassword`, `tel`, `address`, `compaign`, `mcc`) VALUES
(1, '971-996-6574', 'test1', 'e10adc3949ba59abbe56e057f20f883e', '1322222111', '山东济南市二环东路', '济南中信公司', '376-046-0982');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads_column`
--
ALTER TABLE `ads_column`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ads_manager`
--
ALTER TABLE `ads_manager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `n_accountinfo`
--
ALTER TABLE `n_accountinfo`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `ads_column`
--
ALTER TABLE `ads_column`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- 使用表AUTO_INCREMENT `ads_manager`
--
ALTER TABLE `ads_manager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `n_accountinfo`
--
ALTER TABLE `n_accountinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
