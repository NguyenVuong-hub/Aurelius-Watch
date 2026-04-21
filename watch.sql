-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 30, 2026 lúc 02:26 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `watch`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `status`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$SaCWuzUS7dUUKe.ifwT77OWpSXx49/62WKEdEpRXdFeqwaMHpGC4S', 1, '2026-01-30 17:47:12', '2025-12-24 21:41:55');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'UNLOCK_USER', 'user', 18, 'Mở khóa tài khoản người dùng', '::1', '2025-12-25 15:00:26'),
(2, 1, 'LOCK_USER', 'user', 2, 'Khóa tài khoản người dùng', '::1', '2025-12-25 15:00:54'),
(3, 1, 'UNLOCK_USER', 'user', 2, 'Mở khóa tài khoản người dùng', '::1', '2025-12-25 15:01:07'),
(4, 1, 'LOCK_USER', 'user', 1, 'Khóa tài khoản người dùng', '::1', '2025-12-25 15:04:33'),
(5, 1, 'UNLOCK_USER', 'user', 1, 'Mở khóa tài khoản người dùng', '::1', '2025-12-25 15:04:42'),
(6, 1, 'LOCK_USER', 'user', 1, 'Khóa tài khoản người dùng', '::1', '2025-12-25 16:16:13'),
(7, 1, 'UNLOCK_USER', 'user', 1, 'Mở khóa tài khoản người dùng', '::1', '2025-12-25 16:16:26'),
(8, 1, 'LOCK_USER', 'user', 2, 'Khóa tài khoản người dùng', '::1', '2025-12-27 09:40:31'),
(9, 1, 'UNLOCK_USER', 'user', 2, 'Mở khóa tài khoản người dùng', '::1', '2025-12-27 09:40:35'),
(10, 1, 'CREATE_CATEGORY', 'brand', 12, 'Thêm hãng: Casio', '::1', '2026-01-07 16:44:38'),
(11, 1, 'REPLY_FEEDBACK', 'feedback', 12, 'Admin trả lời feedback ID=12', '::1', '2026-01-08 15:23:07'),
(12, 1, 'REPLY_FEEDBACK', 'feedback', 19, 'Admin trả lời feedback ID=19', '::1', '2026-01-08 15:23:16'),
(13, 1, 'REPLY_FEEDBACK', 'feedback', 18, 'Admin trả lời feedback ID=18', '::1', '2026-01-08 15:24:00'),
(14, 1, 'CREATE_PRODUCT', 'product', NULL, 'Thêm sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-08 15:44:01'),
(15, 1, 'DELETE_PRODUCT', 'product', NULL, 'Xóa sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-08 15:46:09'),
(16, 1, 'DELETE_PRODUCT', 'product', NULL, 'Xóa sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-08 15:46:18'),
(17, 1, 'DELETE_PRODUCT', 'product', NULL, 'Xóa sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-08 15:46:25'),
(18, 1, 'UPDATE_PRODUCT', 'product', 0, 'Cập nhật sản phẩm: IWC Big Pilot’s IW329501 Tourbillon Le Petit Prince Watch 43mm', '::1', '2026-01-08 15:52:24'),
(19, 1, 'CREATE_CATEGORY', 'brand', NULL, 'Cập nhật hãng: Casio', '::1', '2026-01-08 15:54:04'),
(20, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-08 16:17:31'),
(21, 1, 'CREATE_PRODUCT', 'product', NULL, 'Thêm sản phẩm: ME', '::1', '2026-01-08 16:18:30'),
(22, 1, 'DELETE_PRODUCT', 'product', NULL, 'Xóa sản phẩm: ME', '::1', '2026-01-08 16:18:49'),
(23, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-10 09:51:26'),
(24, 1, 'CREATE_CATEGORY', 'material', 14, 'Thêm chất liệu (case): Cartier', '::1', '2026-01-10 09:54:48'),
(25, 1, 'DELETE_CATEGORY', 'material', 14, 'Xóa chất liệu: Cartier', '::1', '2026-01-10 09:55:03'),
(26, 1, 'CREATE_CATEGORY', 'material', 15, 'Thêm chất liệu (strap): Cartier', '::1', '2026-01-10 09:55:10'),
(27, 1, 'DELETE_CATEGORY', 'material', 15, 'Xóa chất liệu: Cartier', '::1', '2026-01-10 09:55:22'),
(28, 1, 'CREATE_CATEGORY', 'material', 16, 'Thêm chất liệu (glass): Cartier', '::1', '2026-01-10 09:55:27'),
(29, 1, 'DELETE_CATEGORY', 'material', 16, 'Xóa chất liệu: Cartier', '::1', '2026-01-10 09:56:01'),
(30, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-10 09:56:52'),
(31, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-10 09:57:16'),
(32, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-10 09:57:33'),
(33, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR-0008 128348RBR-0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-10 10:05:15'),
(34, 1, 'CREATE_PRODUCT', 'product', NULL, 'Thêm sản phẩm: ME', '::1', '2026-01-10 10:09:13'),
(35, 1, 'DELETE_PRODUCT', 'product', NULL, 'Xóa sản phẩm: ME', '::1', '2026-01-10 10:09:48'),
(36, 1, 'CREATE_PRODUCT', 'product', NULL, 'Thêm sản phẩm: ME', '::1', '2026-01-10 10:11:51'),
(37, 1, 'DELETE_PRODUCT', 'product', NULL, 'Xóa sản phẩm: ME', '::1', '2026-01-10 10:11:57'),
(38, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-10 10:32:18'),
(39, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-10 13:12:11'),
(40, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-10 16:38:00'),
(41, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-10 17:15:06'),
(42, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:17:32'),
(43, 1, 'UPDATE_CONTACT_STATUS', 'contact', 4, 'Admin cập nhật trạng thái contact ID=4 thành Đang xử lý', '::1', '2026-01-10 17:17:35'),
(44, 1, 'UPDATE_CONTACT_STATUS', 'contact', 4, 'Admin cập nhật trạng thái contact ID=4 thành Đã giải quyết', '::1', '2026-01-10 17:17:38'),
(45, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đã giải quyết', '::1', '2026-01-10 17:17:41'),
(46, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đã giải quyết', '::1', '2026-01-10 17:19:23'),
(47, 1, 'UPDATE_CONTACT_STATUS', 'contact', 2, 'Admin cập nhật trạng thái contact ID=2 thành Đang xử lý', '::1', '2026-01-10 17:19:25'),
(48, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:19:30'),
(49, 1, 'UPDATE_CONTACT_STATUS', 'contact', 1, 'Admin cập nhật trạng thái contact ID=1 thành Đang xử lý', '::1', '2026-01-10 17:22:21'),
(50, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:22:23'),
(51, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đã giải quyết', '::1', '2026-01-10 17:22:25'),
(52, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:22:26'),
(53, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đã giải quyết', '::1', '2026-01-10 17:22:28'),
(54, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đã giải quyết', '::1', '2026-01-10 17:22:33'),
(55, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đã giải quyết', '::1', '2026-01-10 17:23:49'),
(56, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:23:51'),
(57, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đã giải quyết', '::1', '2026-01-10 17:23:52'),
(58, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đã giải quyết', '::1', '2026-01-10 17:23:53'),
(59, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:23:55'),
(60, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đang xử lý', '::1', '2026-01-10 17:23:57'),
(61, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:25:15'),
(62, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đã giải quyết', '::1', '2026-01-10 17:25:17'),
(63, 1, 'UPDATE_CONTACT_STATUS', 'contact', 4, 'Admin cập nhật trạng thái contact ID=4 thành Đang xử lý', '::1', '2026-01-10 17:26:42'),
(64, 1, 'UPDATE_CONTACT_STATUS', 'contact', 4, 'Admin cập nhật trạng thái contact ID=4 thành Mới', '::1', '2026-01-10 17:26:44'),
(65, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đang xử lý', '::1', '2026-01-10 17:26:46'),
(66, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Mới', '::1', '2026-01-10 17:26:47'),
(67, 1, 'UPDATE_CONTACT_STATUS', 'contact', 2, 'Admin cập nhật trạng thái contact ID=2 thành Đang xử lý', '::1', '2026-01-10 17:26:49'),
(68, 1, 'UPDATE_CONTACT_STATUS', 'contact', 2, 'Admin cập nhật trạng thái contact ID=2 thành Mới', '::1', '2026-01-10 17:26:50'),
(69, 1, 'UPDATE_CONTACT_STATUS', 'contact', 1, 'Admin cập nhật trạng thái contact ID=1 thành Đang xử lý', '::1', '2026-01-10 17:26:52'),
(70, 1, 'UPDATE_CONTACT_STATUS', 'contact', 1, 'Admin cập nhật trạng thái contact ID=1 thành Mới', '::1', '2026-01-10 17:26:53'),
(71, 1, 'UPDATE_CONTACT_STATUS', 'contact', 1, 'Admin cập nhật trạng thái contact ID=1 thành Đang xử lý', '::1', '2026-01-10 17:27:33'),
(72, 1, 'UPDATE_CONTACT_STATUS', 'contact', 4, 'Admin cập nhật trạng thái contact ID=4 thành Đã giải quyết', '::1', '2026-01-10 17:27:35'),
(73, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đang xử lý', '::1', '2026-01-10 17:27:37'),
(74, 1, 'UPDATE_CONTACT_STATUS', 'contact', 5, 'Admin cập nhật trạng thái contact ID=5 thành Đã giải quyết', '::1', '2026-01-10 17:29:12'),
(75, 1, 'UPDATE_CONTACT_STATUS', 'contact', 2, 'Admin cập nhật trạng thái contact ID=2 thành Đang xử lý', '::1', '2026-01-10 17:41:35'),
(76, 1, 'UPDATE_CONTACT_STATUS', 'contact', 6, 'Admin cập nhật trạng thái contact ID=6 thành Đã giải quyết', '::1', '2026-01-10 17:41:38'),
(77, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đang xử lý', '::1', '2026-01-10 17:41:39'),
(78, 1, 'UPDATE_CONTACT_STATUS', 'contact', 6, 'Admin cập nhật trạng thái contact ID=6 thành Mới', '::1', '2026-01-10 17:41:42'),
(79, 1, 'UPDATE_CONTACT_STATUS', 'contact', 2, 'Admin cập nhật trạng thái contact ID=2 thành Mới', '::1', '2026-01-10 17:41:45'),
(80, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Mới', '::1', '2026-01-10 17:41:46'),
(81, 1, 'UPDATE_CONTACT_STATUS', 'contact', 3, 'Admin cập nhật trạng thái contact ID=3 thành Đang xử lý', '::1', '2026-01-10 17:41:48'),
(82, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-19 13:53:26'),
(83, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Omega Constellation 131.50.41.21.99.001 Rose Gold Watch 41 mm', '::1', '2026-01-19 13:54:44'),
(84, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-19 14:46:46'),
(85, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-19 15:46:09'),
(86, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-19 17:58:19'),
(87, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-19 18:35:19'),
(88, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-21 14:48:20'),
(89, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-22 09:32:27'),
(90, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-22 14:55:02'),
(91, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-22 16:10:26'),
(92, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-23 12:58:12'),
(93, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-23 13:36:30'),
(94, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-23 14:24:44'),
(95, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Hublot Big Bang 455.JX.0120.JX Integral Tourbillon Full Sapphire 43mm', '::1', '2026-01-23 14:53:04'),
(96, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Hublot Big Bang 455.JX.0120.JX Integral Tourbillon Full Sapphire 43mm', '::1', '2026-01-23 14:55:13'),
(97, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Omega Seamaster 220.55.38.20.09.001 Aqua Terra 38mm', '::1', '2026-01-23 14:55:43'),
(98, 1, 'UPDATE_CATEGORY', 'brand', 10, 'Cập nhật hãng ID=10 → Chopard', '::1', '2026-01-23 14:55:49'),
(99, 1, 'UPDATE_CATEGORY', 'brand', 10, 'Cập nhật hãng ID=10 → Chopard', '::1', '2026-01-23 14:56:47'),
(100, 1, 'UPDATE_CATEGORY', 'brand', 6, 'Cập nhật hãng ID=6 → Gucci', '::1', '2026-01-23 14:56:49'),
(101, 1, 'UPDATE_CATEGORY', 'material', 3, 'Cập nhật chất liệu ID=3 → Bạch kim', '::1', '2026-01-23 14:56:53'),
(102, 1, 'UPDATE_CATEGORY', 'material', 9, 'Cập nhật chất liệu: Vàng 18K', '::1', '2026-01-23 15:40:46'),
(103, 1, 'UPDATE_CATEGORY', 'material', 2, 'Cập nhật chất liệu: Platinum', '::1', '2026-01-23 15:41:01'),
(104, 1, 'UPDATE_CATEGORY', 'material', 3, 'Cập nhật chất liệu: Vàng hồng Everose', '::1', '2026-01-23 15:41:25'),
(105, 1, 'CREATE_CATEGORY', 'material', 17, 'Thêm chất liệu (case): Oystersteel (thép)', '::1', '2026-01-23 15:41:36'),
(106, 1, 'CREATE_CATEGORY', 'material', 18, 'Thêm chất liệu (case): Vàng trắng', '::1', '2026-01-23 15:41:44'),
(107, 1, 'CREATE_CATEGORY', 'material', 19, 'Thêm chất liệu (case): Sapphire', '::1', '2026-01-23 15:42:15'),
(108, 1, 'CREATE_CATEGORY', 'material', 20, 'Thêm chất liệu (case): Magic Gold', '::1', '2026-01-23 15:42:24'),
(109, 1, 'CREATE_CATEGORY', 'case_color', 1, 'Thêm màu vỏ: Nâu', '::1', '2026-01-23 15:42:35'),
(110, 1, 'CREATE_CATEGORY', 'case_color', 2, 'Thêm màu vỏ: Nâu', '::1', '2026-01-23 15:42:41'),
(111, 1, 'CREATE_CATEGORY', 'case_color', 3, 'Thêm màu vỏ: Khác', '::1', '2026-01-23 15:42:48'),
(112, 1, 'CREATE_CATEGORY', 'case_color', 4, 'Thêm màu vỏ: Đen', '::1', '2026-01-23 15:42:55'),
(113, 1, 'CREATE_CATEGORY', 'case_color', 5, 'Thêm màu vỏ: Đen', '::1', '2026-01-23 15:43:00'),
(114, 1, 'UPDATE_CATEGORY', 'case_color', 5, 'Cập nhật màu vỏ: Bạc', '::1', '2026-01-23 15:43:11'),
(115, 1, 'UPDATE_CATEGORY', 'case_color', 2, 'Cập nhật màu vỏ: Xanh đen', '::1', '2026-01-23 15:43:23'),
(116, 1, 'CREATE_CATEGORY', 'case_color', 6, 'Thêm màu vỏ: Vàng hồng', '::1', '2026-01-23 15:43:33'),
(117, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: IWC Big Pilot’s IW329501 Tourbillon Le Petit Prince Watch 43mm', '::1', '2026-01-23 15:44:19'),
(118, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-23 17:09:44'),
(119, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-29 13:23:48'),
(120, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-29 14:51:36'),
(121, 1, 'ADMIN_LOGIN', 'ADMIN', 1, 'Admin đăng nhập hệ thống', '::1', '2026-01-30 17:47:12'),
(122, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Hublot Big Bang 431.MX.1330.RX 20th Anniversary Full Magic Gold Watch 43mm', '::1', '2026-01-30 20:20:21'),
(123, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Hublot Big Bang 455.JX.0120.JX Integral Tourbillon Full Sapphire 43mm', '::1', '2026-01-30 20:20:33'),
(124, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Gucci 25H Watch 36mm', '::1', '2026-01-30 20:21:03'),
(125, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Gucci 25H Watch 40mm', '::1', '2026-01-30 20:21:12'),
(126, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Omega Constellation 131.50.41.21.99.001 Rose Gold Watch 41 mm', '::1', '2026-01-30 20:21:23'),
(127, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Omega Seamaster 220.55.38.20.09.001 Aqua Terra 38mm', '::1', '2026-01-30 20:22:19'),
(128, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Cartier Baignoire CRHPI01823 Watch 23,1mm', '::1', '2026-01-30 20:23:07'),
(129, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Cartier Baignoire WJBA0041 Watch 23,1mm', '::1', '2026-01-30 20:23:19'),
(130, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR.0008 128348RBR.0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-30 20:23:45'),
(131, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: IWC Schaffhausen Big Pilot’s Watch 46.5mm', '::1', '2026-01-30 20:24:04'),
(132, 1, 'UPDATE_PRODUCT', 'product', NULL, 'Cập nhật sản phẩm: Rolex Day-Date 36 M128348RBR.0008 128348RBR.0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '::1', '2026-01-30 20:24:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `type` enum('order','review','user','contact') NOT NULL,
  `title` varchar(255) NOT NULL,
  `target_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `type`, `title`, `target_id`, `is_read`, `created_at`) VALUES
(1, 'user', 'Đăng ký người dùng mới', 27, 1, '2026-01-23 13:38:02'),
(2, 'order', 'Đơn hàng mới #0', 0, 1, '2026-01-23 13:51:00'),
(3, 'order', 'Đơn hàng mới #87', 87, 1, '2026-01-23 13:53:49'),
(4, 'review', 'Đánh giá sản phẩm mới', 25, 1, '2026-01-23 13:54:59'),
(5, 'user', 'Đăng ký người dùng mới', 28, 1, '2026-01-23 14:23:19'),
(6, 'order', 'Đơn hàng mới #88', 88, 1, '2026-01-23 14:24:00'),
(7, '', 'Liên hệ mới từ khách hàng', 9, 1, '2026-01-23 14:24:20'),
(8, '', 'Liên hệ mới từ khách hàng', 10, 1, '2026-01-23 14:25:10'),
(9, 'review', 'Đánh giá sản phẩm mới', 26, 1, '2026-01-23 14:25:26'),
(10, '', 'Liên hệ mới từ khách hàng', 11, 1, '2026-01-23 14:25:42'),
(11, '', 'Liên hệ mới từ khách hàng', 12, 1, '2026-01-23 14:28:34'),
(12, '', 'Liên hệ mới từ khách hàng', 13, 1, '2026-01-23 14:29:28'),
(13, '', 'Liên hệ mới từ khách hàng', 14, 1, '2026-01-23 14:29:54'),
(14, 'contact', 'Liên hệ mới từ khách hàng', 15, 1, '2026-01-23 14:49:20'),
(15, 'order', 'Đơn hàng mới #89', 89, 1, '2026-01-23 16:42:04'),
(16, 'order', 'Đơn hàng mới #90', 90, 1, '2026-01-23 16:52:48'),
(17, 'order', 'Đơn hàng mới #91', 91, 1, '2026-01-23 16:54:20'),
(18, 'order', 'Đơn hàng mới #92', 92, 1, '2026-01-23 17:04:46'),
(19, 'order', 'Đơn hàng mới #93', 93, 1, '2026-01-23 17:08:08'),
(20, 'order', 'Đơn hàng mới #95', 95, 1, '2026-01-23 17:11:57'),
(21, 'order', 'Đơn hàng mới #98', 98, 1, '2026-01-23 17:23:41'),
(22, 'review', 'Đánh giá sản phẩm mới', 27, 1, '2026-01-23 18:04:33'),
(23, 'user', 'Đăng ký người dùng mới', 29, 1, '2026-01-29 14:48:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baocao_sanpham_banchay`
--

CREATE TABLE `baocao_sanpham_banchay` (
  `id` int(11) NOT NULL,
  `idwatch` varchar(10) DEFAULT NULL,
  `namewatch` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `tongsoluongban` int(11) NOT NULL,
  `tongdoanhthu` decimal(15,0) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `baocao_sanpham_banchay`
--

INSERT INTO `baocao_sanpham_banchay` (`id`, `idwatch`, `namewatch`, `brand`, `tongsoluongban`, `tongdoanhthu`, `created_at`) VALUES
(1, 'AW001', 'IWC Big Pilot’s IW329501 Tourbillon Le Petit Prince Watch 43mm', 'IWC Schaffhausen', 2, 6377670000, '2025-12-25 16:25:02'),
(2, 'AW002', 'IWC Schaffhausen Big Pilot’s Watch 46.5mm', 'IWC Schaffhausen', 4, 5533920000, '2025-12-25 16:25:02'),
(3, 'AW005', 'Rolex Land-Dweller 127285tbr-0002 Oyster Everose Watch 36mm', 'Rolex', 3, 3099452850, '2025-12-25 16:25:02'),
(4, 'AW007', 'Patek Philippe 5168G-001 Aquanaut Arabic 42.2mm', 'Patek Philippe', 4, 425000000, '2025-12-25 16:25:02'),
(5, 'AW008', 'Patek Philippe Nautilus Haute Joaillerie Automatic Diamond Watch 35.2mm', 'Patek Philippe', 2, 2498458200, '2025-12-25 16:25:02'),
(6, 'AW013', 'Cartier Baignoire CRHPI01823 Watch 23,1mm', 'Cartier', 2, 2548461600, '2025-12-25 16:25:02'),
(7, 'AW014', 'Omega Seamaster 220.55.38.20.09.001 Aqua Terra 38mm', 'Omega', 2, 1987227360, '2025-12-25 16:25:02'),
(8, 'AW016', 'Omega Constellation 131.50.41.21.99.001 Rose Gold Watch 41 mm', 'Omega', 2, 1987227360, '2025-12-25 16:25:02'),
(16, 'AW001', 'IWC Big Pilot’s IW329501 Tourbillon Le Petit Prince Watch 43mm', 'IWC Schaffhausen', 2, 6377670000, '2025-12-25 16:53:56'),
(17, 'AW002', 'IWC Schaffhausen Big Pilot’s Watch 46.5mm', 'IWC Schaffhausen', 4, 5533920000, '2025-12-25 16:53:56'),
(18, 'AW005', 'Rolex Land-Dweller 127285tbr-0002 Oyster Everose Watch 36mm', 'Rolex', 3, 3099452850, '2025-12-25 16:53:56'),
(19, 'AW007', 'Patek Philippe 5168G-001 Aquanaut Arabic 42.2mm', 'Patek Philippe', 4, 425000000, '2025-12-25 16:53:56'),
(20, 'AW008', 'Patek Philippe Nautilus Haute Joaillerie Automatic Diamond Watch 35.2mm', 'Patek Philippe', 2, 2498458200, '2025-12-25 16:53:56'),
(21, 'AW013', 'Cartier Baignoire CRHPI01823 Watch 23,1mm', 'Cartier', 2, 2548461600, '2025-12-25 16:53:56'),
(22, 'AW014', 'Omega Seamaster 220.55.38.20.09.001 Aqua Terra 38mm', 'Omega', 2, 1987227360, '2025-12-25 16:53:56'),
(23, 'AW016', 'Omega Constellation 131.50.41.21.99.001 Rose Gold Watch 41 mm', 'Omega', 2, 1987227360, '2025-12-25 16:53:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baocao_theo_thuonghieu`
--

CREATE TABLE `baocao_theo_thuonghieu` (
  `id` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL COMMENT 'Thương hiệu',
  `tongsoluongban` int(11) NOT NULL,
  `tongdoanhthu` decimal(15,0) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `baocao_theo_thuonghieu`
--

INSERT INTO `baocao_theo_thuonghieu` (`id`, `brand`, `tongsoluongban`, `tongdoanhthu`, `created_at`) VALUES
(1, 'Cartier', 5, 6892751300, '2025-12-26 12:14:08'),
(2, 'Gucci', 2, 19999999998, '2025-12-26 12:14:08'),
(3, 'Hublot', 1, 856170500, '2025-12-26 12:14:08'),
(4, 'IWC Schaffhausen', 9, 17335750000, '2025-12-26 12:14:08'),
(5, 'Omega', 5, 4830625220, '2025-12-26 12:14:08'),
(6, 'Patek Philippe', 7, 4577896500, '2025-12-26 12:14:08'),
(7, 'Rolex', 4, 5699552850, '2025-12-26 12:14:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baocao_thongke`
--

CREATE TABLE `baocao_thongke` (
  `idreport` int(11) NOT NULL COMMENT 'Mã báo cáo',
  `iduser` int(10) UNSIGNED DEFAULT NULL COMMENT 'Người dùng (NULL = tổng hệ thống)',
  `tongdonhang` int(11) NOT NULL COMMENT 'Tổng số đơn hàng',
  `tongsoluongban` int(11) NOT NULL COMMENT 'Tổng số sản phẩm đã bán',
  `tongdoanhthu` decimal(15,0) NOT NULL COMMENT 'Tổng doanh thu (VNĐ)',
  `danhgia` enum('Hoàn thành','Chưa hoàn thành') DEFAULT 'Hoàn thành',
  `created_at` datetime DEFAULT current_timestamp(),
  `tong_sanpham` int(11) DEFAULT 0 COMMENT 'Tổng số sản phẩm',
  `tong_nguoidung` int(11) DEFAULT 0 COMMENT 'Tổng số người dùng',
  `tong_loinhuan` decimal(18,0) DEFAULT 0 COMMENT 'Tổng lợi nhuận',
  `bieudo_doanhthu` longtext DEFAULT NULL,
  `bieudo_doanhthu_nam` text DEFAULT NULL,
  `phanbo_trangthai_donhang` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `baocao_thongke`
--

INSERT INTO `baocao_thongke` (`idreport`, `iduser`, `tongdonhang`, `tongsoluongban`, `tongdoanhthu`, `danhgia`, `created_at`, `tong_sanpham`, `tong_nguoidung`, `tong_loinhuan`, `bieudo_doanhthu`, `bieudo_doanhthu_nam`, `phanbo_trangthai_donhang`) VALUES
(1, NULL, 17, 30, 54768586368, 'Hoàn thành', '2025-12-25 17:00:36', 21, 22, 16430575910, '[{\"thang\": \"2025-03\", \"doanhthu\": 856170500},{\"thang\": \"2025-06\", \"doanhthu\": 2448241200},{\"thang\": \"2025-07\", \"doanhthu\": 2843397860},{\"thang\": \"2025-08\", \"doanhthu\": 2498458200},{\"thang\": \"2025-09\", \"doanhthu\": 2694316300},{\"thang\": \"2025-10\", \"doanhthu\": 13099452849},{\"thang\": \"2025-11\", \"doanhthu\": 2600100000},{\"thang\": \"2025-12\", \"doanhthu\": 25741222099}]', '[{\"nam\": 2024, \"tong_doanhthu_nam\": 1987227360},{\"nam\": 2025, \"tong_doanhthu_nam\": 52781359008}]', '[{\"trang_thai\": \"Hoàn thành\", \"so_luong\": 17}]');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `birthday_popup_log`
--

CREATE TABLE `birthday_popup_log` (
  `id` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `shown_year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `birthday_popup_log`
--

INSERT INTO `birthday_popup_log` (`id`, `iduser`, `shown_year`, `created_at`) VALUES
(1, 2, 2026, '2026-01-19 07:08:16'),
(2, 28, 2026, '2026-01-23 07:23:30'),
(3, 29, 2026, '2026-01-29 07:51:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `idbrand` int(11) NOT NULL,
  `namebrand` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`idbrand`, `namebrand`, `country`, `status`) VALUES
(1, 'IWC Schaffhausen', 'Thụy Sĩ', 1),
(2, 'Rolex', 'Thụy Sĩ', 1),
(3, 'Patek Philippe', 'Thụy Sĩ', 1),
(4, 'Cartier', 'Pháp', 1),
(5, 'Omega', 'Thụy Sĩ', 1),
(6, 'Gucci', 'Ý', 1),
(7, 'Hublot', 'Thụy Sĩ', 1),
(8, 'Hermes', 'Pháp', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `idcart` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `status` enum('active','ordered') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `carts`
--

INSERT INTO `carts` (`idcart`, `user_id`, `session_id`, `status`, `created_at`) VALUES
(1, NULL, 'etbbk7h4lfuejgtf0p8abkr263', 'active', '2026-01-18 12:06:55'),
(2, NULL, 'fmfsjc1f3d2405mpplv5facc76', 'active', '2026-01-18 15:27:50'),
(3, NULL, 'tifqhsah2k0vl45b81o0qlpu6i', 'active', '2026-01-18 16:59:04'),
(4, NULL, '706f0b389990nu2ut2n4iuvtb4', 'active', '2026-01-19 03:04:11'),
(5, NULL, 's2h1p86aui0kvd57oo5lcbuebn', 'active', '2026-01-19 03:21:47'),
(6, NULL, 'bo1invp9t7oom6jjhahcvjstbs', 'active', '2026-01-19 03:55:21'),
(8, 2, 'rq0kv56ij2ia9qkcfsvvap993m', 'ordered', '2026-01-19 09:50:22'),
(9, NULL, '4h75g78op2eucv80oku7id3bfm', 'active', '2026-01-21 06:41:31'),
(10, 2, NULL, 'active', '2026-01-21 06:51:05'),
(12, NULL, 'dut77hvs5kb39ahqg3inlel384', 'active', '2026-01-22 07:26:12'),
(13, 26, NULL, 'active', '2026-01-23 06:27:49'),
(14, 27, NULL, 'active', '2026-01-23 06:38:15'),
(15, 28, NULL, 'active', '2026-01-23 07:23:29'),
(16, 29, NULL, 'active', '2026-01-29 07:49:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `iditem` int(11) NOT NULL,
  `idcart` int(11) NOT NULL,
  `idwatch` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`iditem`, `idcart`, `idwatch`, `quantity`, `price`, `created_at`) VALUES
(2, 1, 'AW020', 1, 8280906000, '2026-01-18 12:07:19'),
(6, 1, 'AW019', 1, 57500000, '2026-01-18 12:29:46'),
(8, 2, 'AW006', 2, 3188835000, '2026-01-18 15:36:59'),
(9, 2, 'AW018', 2, 106250000, '2026-01-18 16:34:02'),
(15, 2, 'AW004', 1, 1383480000, '2026-01-18 16:48:17'),
(16, 3, 'AW004', 1, 1383480000, '2026-01-18 16:59:04'),
(18, 3, 'AW020', 1, 8280906000, '2026-01-18 16:59:21'),
(20, 4, 'AW020', 1, 8280906000, '2026-01-19 03:04:11'),
(21, 4, 'AW019', 2, 57500000, '2026-01-19 03:20:55'),
(24, 5, 'AW021', 2, 1033150950, '2026-01-19 03:21:47'),
(25, 5, 'AW019', 1, 57500000, '2026-01-19 03:22:25'),
(26, 5, 'AW018', 1, 106250000, '2026-01-19 03:36:06'),
(27, 6, 'AW019', 1, 57500000, '2026-01-19 03:55:21'),
(28, 6, 'AW021', 2, 1033150950, '2026-01-19 03:55:49'),
(29, 6, 'AW018', 1, 106250000, '2026-01-19 03:55:55'),
(30, 6, 'AW015', 1, 1039878000, '2026-01-19 04:00:17'),
(31, 6, 'AW017', 1, 1249229100, '2026-01-19 04:18:42'),
(73, 10, 'AW020', 1, 8280906000, '2026-01-23 11:44:15'),
(74, 10, 'AW009', 1, 2498265000, '2026-01-29 07:35:36');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `case_colors`
--

CREATE TABLE `case_colors` (
  `idcolor` int(11) NOT NULL,
  `namecolor` varchar(50) NOT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `case_colors`
--

INSERT INTO `case_colors` (`idcolor`, `namecolor`, `status`) VALUES
(1, 'Nâu', 1),
(2, 'Xanh đen', 1),
(3, 'Khác', 1),
(4, 'Đen', 1),
(5, 'Bạc', 1),
(6, 'Vàng hồng', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact`
--

CREATE TABLE `contact` (
  `idcontact` int(11) NOT NULL,
  `iduser` int(11) DEFAULT NULL,
  `hoten` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('Mới','Đang xử lý','Đã giải quyết') NOT NULL DEFAULT 'Mới',
  `created_at` datetime DEFAULT current_timestamp(),
  `reply_sent_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contact`
--

INSERT INTO `contact` (`idcontact`, `iduser`, `hoten`, `phone`, `email`, `message`, `status`, `created_at`, `reply_sent_at`) VALUES
(1, NULL, 'Nguyễn Thi Hoa', '0395464554', NULL, 'Tôi cần tư vấn đồng hồ Rolex', 'Đang xử lý', '2026-01-10 16:47:18', NULL),
(2, NULL, 'Nguyễn Thi Hoa', '0395464554', NULL, 'Rolex', 'Mới', '2026-01-10 16:47:35', NULL),
(3, NULL, 'Nguyễn Thi Hoa', '0395464554', NULL, 'Rolex', 'Đang xử lý', '2026-01-10 16:49:55', NULL),
(4, NULL, 'Nguyễn Thi Hoa', '0395464554', NULL, 'Gucci', 'Đã giải quyết', '2026-01-10 16:50:32', NULL),
(5, NULL, 'Nguyễn An Phú', '0362165567', NULL, 'Casio', 'Đã giải quyết', '2026-01-10 16:55:47', NULL),
(6, NULL, 'Nguyễn An Phú', '0814611647', NULL, 'Patek', 'Mới', '2026-01-10 17:36:53', NULL),
(7, 2, 'Nguyễn An Phú', '0395464554', NULL, 'ok', 'Mới', '2026-01-10 17:43:08', NULL),
(8, 2, 'Nguyễn An Phú', '0395464554', NULL, 'ok', 'Mới', '2026-01-10 17:49:28', NULL),
(9, 28, 'abc', '0234567823', NULL, 'abc', 'Mới', '2026-01-23 14:24:20', NULL),
(10, 28, 'abc', '0234567823', NULL, 'abc', 'Mới', '2026-01-23 14:25:10', NULL),
(11, 28, 'abc', '0234567823', NULL, 'ok', 'Mới', '2026-01-23 14:25:42', NULL),
(12, 28, 'abc', '0234567823', NULL, 'ok', 'Mới', '2026-01-23 14:28:34', NULL),
(13, 28, 'abc', '0234567823', NULL, 'ok', 'Mới', '2026-01-23 14:29:28', NULL),
(14, 28, 'abc', '0234567823', NULL, 'ok', 'Mới', '2026-01-23 14:29:54', NULL),
(15, 28, 'abc', '0234567823', NULL, 'ok', 'Mới', '2026-01-23 14:49:20', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `genders`
--

CREATE TABLE `genders` (
  `idgender` int(11) NOT NULL,
  `namegender` enum('Nam','Nữ','Unisex') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `genders`
--

INSERT INTO `genders` (`idgender`, `namegender`) VALUES
(1, 'Nam'),
(2, 'Nữ'),
(3, 'Unisex');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `materials`
--

CREATE TABLE `materials` (
  `idmaterial` int(11) NOT NULL,
  `namematerial` varchar(100) NOT NULL,
  `material_type` enum('case','strap','glass') NOT NULL COMMENT 'Vỏ / Dây / Kính',
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `materials`
--

INSERT INTO `materials` (`idmaterial`, `namematerial`, `material_type`, `status`) VALUES
(1, 'Thép không gỉ', 'case', 1),
(2, 'Platinum', 'case', 1),
(3, 'Vàng hồng Everose', 'case', 1),
(4, 'Dây da', 'strap', 1),
(5, 'Dây kim loại', 'strap', 1),
(6, 'Sapphire', 'glass', 1),
(7, 'Mineral', 'glass', 1),
(9, 'Vàng 18K', 'case', 1),
(17, 'Oystersteel (thép)', 'case', 1),
(18, 'Vàng trắng', 'case', 1),
(19, 'Sapphire', 'case', 1),
(20, 'Magic Gold', 'case', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `idorder` int(11) NOT NULL COMMENT 'Mã đơn hàng',
  `iduser` int(11) DEFAULT NULL COMMENT 'NULL = khách vãng lai',
  `order_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày đặt hàng',
  `total_amount` decimal(10,0) NOT NULL COMMENT 'Tổng tiền (vnđ)',
  `status` enum('Đang xử lý','Đã thanh toán','Đã xác nhận','Đang giao','Hoàn thành','Đã hủy') NOT NULL DEFAULT 'Đang xử lý',
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_address` text DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`idorder`, `iduser`, `order_date`, `total_amount`, `status`, `guest_name`, `guest_phone`, `guest_email`, `guest_address`, `note`) VALUES
(1, 4, '2020-12-01 17:29:42', 6377670000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(2, 1, '2025-12-04 12:28:56', 5533920000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(3, 5, '2025-11-20 06:28:56', 2600100000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(5, 19, '2025-10-15 06:18:40', 3099452850, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(7, 17, '2025-12-25 05:20:40', 425000000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(8, 16, '2026-01-07 06:20:30', 2498458200, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(9, 2, '2025-09-08 05:32:52', 1654438300, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(10, 3, '2026-01-03 06:35:38', 1039878000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(11, 6, '2025-06-17 10:42:17', 2448241200, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(12, 7, '2026-01-09 22:23:03', 856170500, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(13, 9, '2025-07-23 22:23:21', 2548461600, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(14, 14, '2025-07-15 22:23:51', 1987227360, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(15, 13, '2025-03-10 05:24:38', 856170500, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(16, 11, '2024-11-15 22:24:43', 1987227360, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(17, 10, '2024-11-06 08:14:10', 9999999999, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(18, 2, '2024-12-16 22:25:29', 9999999999, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(20, 17, '2025-12-16 04:26:07', 856170500, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(22, 1, '2026-01-01 15:22:50', 5424160000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(23, NULL, '2026-01-18 23:25:42', 3246335000, 'Hoàn thành', 'Linh', '0901131783', 'ailind.91xx@gmail.com', 'HCM', NULL),
(24, NULL, '2026-01-18 23:29:01', 3246335000, 'Hoàn thành', 'Linh', '0901131783', 'ailind.91xx@gmail.com', 'HCM', NULL),
(25, NULL, '2026-01-18 23:32:05', 3246335000, 'Hoàn thành', 'Linh', '0901131783', 'ailind.91xx@gmail.com', 'HCM', NULL),
(26, 1, '2026-01-18 23:32:28', 1000000, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(27, NULL, '2026-01-18 23:32:45', 3246335000, 'Hoàn thành', 'Linh', '0901131783', 'ailind.91xx@gmail.com', 'HCM', NULL),
(46, NULL, '2026-01-19 15:45:50', 3087850000, 'Hoàn thành', 'Ca', '0395464554', 'abc@gmail.com', 'TP HCM', ''),
(52, 2, '2026-01-19 17:45:07', 163750000, 'Hoàn thành', 'Ca', '0395464554', 'anphu12@gmail.com', 'TP HCM', ''),
(53, 2, '2026-01-21 14:40:17', 0, 'Hoàn thành', NULL, NULL, NULL, NULL, NULL),
(54, 2, '2026-01-21 14:46:46', 57500000, 'Hoàn thành', 'Ca', '23456', 'gf@gmail.com', 'tphcm', NULL),
(55, 2, '2026-01-21 14:53:16', 57500000, 'Đã hủy', 'Ca', '23456', 'gf@gmail.com', 'tphcm', 'ádf'),
(56, 2, '2026-01-21 15:13:53', 1033150950, 'Đã hủy', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(57, NULL, '2026-01-21 15:24:19', 9999999999, 'Đang xử lý', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(58, 2, '2026-01-21 16:43:15', 8280906000, 'Đang xử lý', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(59, 2, '2026-01-21 16:52:34', 2448241200, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(60, 2, '2026-01-21 17:04:10', 8280906000, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(61, 2, '2026-01-21 17:09:03', 8280906000, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(62, 2, '2026-01-21 17:13:56', 8280906000, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(63, 2, '2026-01-21 17:17:05', 57500000, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(64, 2, '2026-01-21 17:17:33', 1033150950, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(65, 2, '2026-01-21 17:17:52', 8280906000, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(66, 2, '2026-01-21 17:18:37', 1033150950, 'Đã xác nhận', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(67, 2, '2026-01-21 17:21:06', 8280906000, 'Đang xử lý', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(68, 2, '2026-01-21 17:24:11', 106250000, 'Đang xử lý', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(69, NULL, '2026-01-22 09:30:47', 57500000, 'Đang xử lý', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(70, NULL, '2026-01-22 09:30:55', 57500000, 'Hoàn thành', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(71, 2, '2026-01-22 09:33:11', 1033150950, 'Hoàn thành', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(72, 2, '2026-01-22 09:34:08', 8387156000, 'Hoàn thành', 'Ca', '23456', 'gf@gmail.com', 'tphcm', ''),
(73, NULL, '2026-01-22 13:55:55', 1654438300, 'Đang xử lý', 'Ca', '0395464554', 'nthoa@gmail.com', 'TP HCM', ''),
(74, NULL, '2026-01-22 13:59:13', 1654438300, 'Đang xử lý', 'Ca', '0395464554', 'nthoa@gmail.com', 'TP HCM', ''),
(75, NULL, '2026-01-22 13:59:57', 1654438300, 'Đang xử lý', 'Ca', '0395464554', 'nthoa@gmail.com', 'TP HCM', ''),
(76, NULL, '2026-01-22 14:09:09', 106250000, 'Đang xử lý', 'Ca', '0395464554', 'anphu12@gmail.com', 'TP HCM', ''),
(77, NULL, '2026-01-22 14:09:18', 57500000, 'Đang xử lý', 'Ca', '0395464554', 'anphu12@gmail.com', 'TP HCM', ''),
(78, NULL, '2026-01-22 14:14:19', 57500000, 'Đang xử lý', 'Ca', '0395464554', 'abc@gmail.com', 'TP HCM', ''),
(79, NULL, '2026-01-22 14:26:26', 8338406000, 'Đang xử lý', 'Ca', '0395464554', 'abc@gmail.com', 'TP HCM', ''),
(80, NULL, '2026-01-22 14:26:51', 9320784000, 'Đang xử lý', 'Ca', '0362165567', 'abc@gmail.com', 'TP HCM', ''),
(81, NULL, '2026-01-22 14:42:23', 57500000, 'Đang xử lý', 'Ca', '0395464554', 'abc@gmail.com', 'TP HCM', ''),
(82, NULL, '2026-01-22 14:44:10', 57500000, 'Đang xử lý', 'Ca', '0395464554', 'abc@gmail.com', 'TP HCM', ''),
(83, 2, '2026-01-22 14:54:17', 8280906000, 'Hoàn thành', 'Phú', '0395464554', 'anphu12@gmail.com', 'TP HCM', 'Gói quà'),
(88, 28, '2026-01-23 14:24:00', 1033150950, 'Hoàn thành', 'abc', '0234567823', 'abc@gmail.com', 'tphcm', ''),
(89, 28, '2026-01-23 16:42:04', 1039878000, 'Đang xử lý', 'abc', '0234567823', 'gf@gmail.com', 'tphcm', ''),
(90, 28, '2026-01-23 16:52:48', 1039878000, 'Đang xử lý', 'abc', '0234567823', 'gf@gmail.com', 'tphcm', ''),
(91, 28, '2026-01-23 16:54:20', 1039878000, 'Đã thanh toán', 'abc', '0234567823', 'gf@gmail.com', 'tphcm', ''),
(92, 28, '2026-01-23 17:04:46', 8280906000, 'Đang giao', 'abc', '0234567823', 'gf@gmail.com', 'tphcm', ''),
(93, 28, '2026-01-23 17:08:08', 8280906000, 'Đã xác nhận', 'abc', '0234567823', 'gf@gmail.com', 'tphcm', ''),
(96, 2, '2026-01-23 17:15:12', 1654438300, 'Hoàn thành', 'phú', '0234567823', 'abc@gmail.com', 'tphcm', ''),
(97, 2, '2026-01-23 17:20:36', 1033150950, 'Hoàn thành', 'abc', '0234567823', 'abc@gmail.com', 'tphcm', ''),
(98, 2, '2026-01-23 17:23:41', 1654438300, 'Hoàn thành', 'abc', '0234567823', 'gf@gmail.com', 'tphcm', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `watch_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `watch_id`, `quantity`, `price`) VALUES
(1, 1, 'AW001', 2, 3188835000),
(2, 2, 'AW002', 4, 1383480000),
(3, 3, 'AW003', 1, 2600100000),
(4, 5, 'AW005', 3, 1033150950),
(5, 7, 'AW007', 4, 106250000),
(6, 8, 'AW008', 2, 1249229100),
(7, 9, 'AW009', 1, 1654438300),
(8, 10, 'AW010', 1, 1039878000),
(9, 11, 'AW011', 1, 2448241200),
(10, 12, 'AW012', 1, 856170500),
(11, 13, 'AW013', 2, 1274230800),
(12, 14, 'AW014', 2, 993613680),
(13, 15, 'AW015', 1, 856170500),
(14, 16, 'AW016', 2, 993613680),
(15, 17, 'AW017', 1, 9999999999),
(16, 18, 'AW018', 1, 9999999999),
(17, 20, 'AW020', 1, 856170500),
(50, 22, 'AW001', 1, 2657200000),
(51, 22, 'AW002', 2, 1383480000),
(63, 1, 'AW001', 1, 1000000),
(65, 1, 'AW001', 1, 1000000),
(67, 46, 'AW001', 1, 2657200000),
(68, 46, 'AW007', 1, 430650000),
(79, 52, 'AW018', 1, 106250000),
(80, 52, 'AW019', 1, 57500000),
(81, 53, 'AW016', 1, 1654438300),
(82, 54, 'AW019', 1, 57500000),
(83, 55, 'AW019', 1, 57500000),
(84, 56, 'AW021', 1, 1033150950),
(85, 57, 'AW020', 1, 8280906000),
(86, 57, 'AW014', 1, 2448241200),
(87, 58, 'AW020', 1, 8280906000),
(88, 59, 'AW014', 1, 2448241200),
(89, 60, 'AW020', 1, 8280906000),
(90, 61, 'AW020', 1, 8280906000),
(91, 62, 'AW020', 1, 8280906000),
(92, 63, 'AW019', 1, 57500000),
(93, 64, 'AW021', 1, 1033150950),
(94, 65, 'AW020', 1, 8280906000),
(95, 66, 'AW021', 1, 1033150950),
(96, 67, 'AW020', 1, 8280906000),
(97, 68, 'AW018', 1, 106250000),
(98, 69, 'AW019', 1, 57500000),
(99, 70, 'AW019', 1, 57500000),
(100, 71, 'AW021', 1, 1033150950),
(101, 72, 'AW020', 1, 8280906000),
(102, 72, 'AW018', 1, 106250000),
(103, 73, 'AW016', 1, 1654438300),
(104, 74, 'AW016', 1, 1654438300),
(105, 75, 'AW016', 1, 1654438300),
(106, 76, 'AW018', 1, 106250000),
(107, 77, 'AW019', 1, 57500000),
(108, 78, 'AW019', 1, 57500000),
(109, 79, 'AW019', 1, 57500000),
(110, 79, 'AW020', 1, 8280906000),
(111, 80, 'AW020', 1, 8280906000),
(112, 80, 'AW015', 1, 1039878000),
(113, 81, 'AW019', 1, 57500000),
(114, 82, 'AW019', 1, 57500000),
(115, 83, 'AW020', 1, 8280906000),
(120, 88, 'AW021', 1, 1033150950),
(121, 89, 'AW015', 1, 1039878000),
(122, 90, 'AW015', 1, 1039878000),
(123, 91, 'AW015', 1, 1039878000),
(124, 92, 'AW020', 1, 8280906000),
(125, 93, 'AW020', 1, 8280906000),
(128, 96, 'AW016', 1, 1654438300),
(129, 97, 'AW021', 1, 1033150950),
(130, 98, 'AW016', 1, 1654438300);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `idpayment` int(11) NOT NULL COMMENT 'Mã thanh toán',
  `idorder` int(11) NOT NULL COMMENT 'Thuộc đơn hàng',
  `payment_date` date NOT NULL COMMENT 'Ngày thanh toán',
  `amount` decimal(15,0) NOT NULL COMMENT 'Số tiền trả',
  `method` enum('momo','vnpay','visa','cod') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `payments`
--

INSERT INTO `payments` (`idpayment`, `idorder`, `payment_date`, `amount`, `method`) VALUES
(1, 1, '2021-12-15', 1383480000, 'vnpay'),
(2, 2, '2025-12-11', 2600100000, 'momo'),
(3, 3, '2025-12-19', 3188835000, 'momo'),
(4, 4, '2021-12-09', 17472000000, 'visa'),
(5, 5, '2024-12-19', 993613680, 'momo'),
(6, 6, '2025-01-08', 1274230800, 'cod'),
(7, 7, '2025-10-15', 856170500, 'momo'),
(8, 8, '2025-09-30', 1039878000, 'visa'),
(9, 9, '2025-11-19', 92648750, 'visa'),
(10, 10, '2025-12-25', 1249229100, 'visa'),
(11, 11, '2023-12-28', 106250000, 'visa'),
(12, 12, '2025-08-15', 57500000, 'momo'),
(13, 13, '2025-07-31', 1033150950, 'vnpay'),
(14, 14, '2025-10-23', 537670200, 'vnpay'),
(15, 15, '2025-05-23', 514840000, 'visa'),
(16, 16, '2025-04-03', 774056700, 'vnpay'),
(17, 17, '2025-06-05', 1274230800, 'momo'),
(18, 18, '2025-08-13', 2657200000, 'visa'),
(19, 19, '2025-09-17', 140647500, 'visa'),
(20, 20, '2025-12-02', 267900000, 'cod'),
(21, 59, '2026-01-21', 2448241200, 'momo'),
(22, 59, '2026-01-21', 2448241200, 'momo'),
(23, 60, '2026-01-21', 8280906000, 'momo'),
(24, 61, '2026-01-21', 8280906000, 'momo'),
(25, 61, '2026-01-21', 8280906000, 'momo'),
(26, 62, '2026-01-21', 8280906000, 'momo'),
(27, 62, '2026-01-21', 8280906000, 'momo'),
(28, 63, '2026-01-21', 57500000, 'momo'),
(29, 64, '2026-01-21', 1033150950, 'vnpay'),
(30, 65, '2026-01-21', 8280906000, 'visa'),
(31, 70, '2026-01-22', 57500000, 'vnpay'),
(32, 71, '2026-01-22', 1033150950, 'momo'),
(33, 72, '2026-01-22', 8387156000, 'momo'),
(34, 83, '2026-01-22', 8280906000, 'visa'),
(35, 84, '2026-01-23', 8280906000, 'vnpay'),
(36, 85, '2026-01-23', 2448241200, 'vnpay'),
(37, 88, '2026-01-23', 1033150950, 'vnpay'),
(38, 91, '2026-01-23', 1039878000, 'vnpay'),
(39, 93, '2026-01-23', 8280906000, 'vnpay'),
(40, 94, '2026-01-23', 1654438300, 'vnpay'),
(41, 96, '2026-01-23', 1654438300, 'vnpay'),
(42, 97, '2026-01-23', 1033150950, 'vnpay'),
(43, 98, '2026-01-23', 1654438300, 'vnpay');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `idreview` int(11) NOT NULL,
  `iduser` int(10) UNSIGNED NOT NULL,
  `idwatch` varchar(10) NOT NULL,
  `idorder` int(11) NOT NULL,
  `idorder_item` int(11) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `images` text DEFAULT NULL COMMENT 'Danh sách ảnh review (json hoặc csv)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`idreview`, `iduser`, `idwatch`, `idorder`, `idorder_item`, `rating`, `comment`, `is_approved`, `created_at`, `updated_at`, `images`) VALUES
(6, 1, 'AW001', 1, 1, 5, 'Đồng hồ đẹp xuất sắc, đeo rất sang, chất lượng vượt mong đợi. 5 sao tuyệt đối!', 1, '2025-12-10 14:20:00', '2026-01-19 14:46:15', NULL),
(7, 2, 'AW001', 22, 50, 4, 'Sản phẩm chính hãng, giao hàng nhanh chóng. Chỉ hơi tiếc là hộp hơi móp nhẹ khi nhận.', 1, '2026-01-05 09:45:00', '2026-01-19 14:46:15', NULL),
(8, 3, 'AW002', 2, 2, 5, 'Màu sắc đẹp lung linh, máy chạy êm, rất đáng giá với mức tiền này.', 1, '2025-11-28 16:30:00', '2026-01-19 14:46:15', NULL),
(9, 1, 'AW005', 5, 4, 5, 'Hublot Big Bang đúng chất, độ hoàn thiện cao cấp, đeo rất thoải mái.', 1, '2026-01-12 11:10:00', '2026-01-19 14:46:15', NULL),
(10, 4, 'AW007', 7, 5, 4, 'Đồng hồ nhẹ, thiết kế trẻ trung. Pin bền tốt, dùng được hơn 1 năm chưa cần thay.', 1, '2025-12-25 20:15:00', '2026-01-19 14:46:15', NULL),
(11, 2, 'AW008', 8, 6, 5, 'Quà tặng sinh nhật cho chồng, anh ấy thích lắm! Cảm ơn shop rất nhiều.', 1, '2026-01-18 15:40:00', '2026-01-21 15:08:23', NULL),
(12, 3, 'AW012', 12, 10, 4, 'Đẹp nhưng dây hơi ngắn với cổ tay to. Tổng thể vẫn rất hài lòng.', 1, '2026-01-19 10:00:00', '2026-01-21 15:08:23', NULL),
(13, 1, 'AW001', 22, 50, 5, 'Mua lần thứ 2, vẫn chất lượng như lần đầu. Shop uy tín!', 1, '2026-01-19 13:30:00', '2026-01-21 15:08:23', NULL),
(14, 5, 'AW020', 20, 17, 5, 'Bền bỉ, chống nước tốt, dùng đi biển thoải mái.', 1, '2026-01-15 17:55:00', '2026-01-19 15:00:43', NULL),
(15, 2, 'AW017', 17, 15, 3, 'Đẹp nhưng mặt kính hơi dễ xước. Cần cẩn thận hơn khi sử dụng.', 1, '2026-01-19 14:20:00', '2026-01-21 15:08:23', NULL),
(16, 2, 'AW018', 52, 79, 5, 'Đẹp', 1, '2026-01-19 18:35:44', '2026-01-21 15:08:23', NULL),
(17, 2, 'AW009   ', 9, 7, 5, 'rất đẹp', 1, '2026-01-21 13:35:53', '2026-01-21 15:08:23', NULL),
(21, 2, 'AW019   ', 52, 80, 5, 'ok', 1, '2026-01-21 13:40:02', '2026-01-21 15:08:23', NULL),
(23, 2, 'AW019   ', 54, 82, 5, 'ok', 1, '2026-01-21 14:49:18', '2026-01-22 18:01:15', NULL),
(24, 2, 'AW020   ', 83, 115, 4, 'ok', 1, '2026-01-22 15:07:14', '2026-01-29 15:13:52', 'uploads/reviews/review_1769069234_5051.jpg,uploads/reviews/review_1769069234_1717.jpg,uploads/reviews/review_1769069234_1944.jpg'),
(26, 28, 'AW021   ', 88, 120, 5, 'đẹp', 1, '2026-01-23 14:25:26', '2026-01-23 14:25:53', 'uploads/reviews/review_1769153126_6438.jpg'),
(27, 2, 'AW016   ', 98, 130, 4, 'ok', 1, '2026-01-23 18:04:33', '2026-01-23 18:12:15', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `iduser` int(11) UNSIGNED NOT NULL COMMENT 'Khóa chính',
  `hoten` varchar(100) NOT NULL COMMENT 'Họ tên đầy đủ',
  `username` varchar(50) NOT NULL COMMENT 'Tên đăng nhập',
  `phone` varchar(15) DEFAULT NULL COMMENT 'Số điện thoại',
  `ngaysinh` date DEFAULT NULL COMMENT 'Ngày sinh',
  `gioitinh` tinyint(1) UNSIGNED NOT NULL COMMENT '0: Nữ, 1: Nam',
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa',
  `email` varchar(100) NOT NULL COMMENT 'Email người dùng',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `ngaydangky` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Thời gian đăng ký',
  `role` enum('customer','staff','admin') DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`iduser`, `hoten`, `username`, `phone`, `ngaysinh`, `gioitinh`, `password`, `email`, `status`, `ngaydangky`, `role`) VALUES
(1, 'Đào Văn Nam', 'namdao15', '0987654321', '2000-05-12', 1, '$2y$10$fwVWg309jgVJSinSwkIL1e.6x8F/B4vdNI65oHU6j2y4u6MLQRGfG', 'daovannam123@gmail.com', 1, '2025-12-25 09:16:26', 'customer'),
(2, 'Nguyễn An Phú', 'BePhu123', '0912345678', '2004-01-19', 1, '$2y$10$L0S9fDjbYALdjeDi4HdWyO/tiGrLxSCt0ZJekmshKzSYWV2k.BvQK', 'anphu12@gmail.com', 1, '2026-01-19 07:08:12', 'customer'),
(3, 'Lê Thị Thúy Vân', 'cobemituot', '0901234567', '2005-04-19', 0, '$2y$10$9cIxm71DkPS9W8hVN6DiC.DVFRck8orZ/wpt4HqVdrx1VMIpTuORq', 'thuyvan890@gmail.com', 1, '2025-12-24 10:15:15', 'customer'),
(4, 'Nguyễn Cao Tiến', 'nguyencaotien212', '0934567890', '2004-06-01', 1, '$2y$10$I.1laaq4F/9ATg2eVUaSw.r78VuzZrSVMgyHSzxqKJNc1xUo9mUO2', 'caotienn1892@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(5, 'Lê Thị Thương', 'becung123', '0971234567', '2003-08-14', 0, '$2y$10$hwn.Tl3Mbims/VZPCL5cCuyxb7Gw6vqZxhOlA15m0rN0P3SCFU3ei', 'thithuong345@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(6, 'Cao Xuân Bá', 'caoxuanba', '0962345678', '2001-03-12', 1, '$2y$10$WqqjBdkJ6lAlWpxn/1gBheI4gQgL7nKcrQUMVH59BltbTZINLwSp6', 'caoxuanba1209@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(7, 'Nguyễn An Nhiên', 'beannhien', '0945678901', '2005-08-23', 0, '$2y$10$hsLAt.mDRmY.f6HSYlqCMeVqMfJoT1UM5gf5YgaMd.tGY4R6vFo66', 'bean1265@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(8, 'Nguyễn Lê Mẫn Tuyền', 'tuyennek', '0923456789', '2005-02-18', 0, '$2y$10$R03dLBxDHzCNAGE9CUC/nOgCO9bA3wPCJ3r/EDyr09nSKH6P3/eT6', 'tuyenem@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(9, 'Trần Ánh Mây', 'mayvatocembay', '0391234567', '2004-02-03', 0, '$2y$10$lo2b0vz0gOqvzQrzTcmfLex2RGJqbuIFTTtTJQ7lvaHxGpQV8gTLK', 'anhmay2004@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(10, 'Nguyễn Thị Trúc Linh', 'truclinh', '0391234567', '2005-12-19', 0, '$2y$10$ssfjF2qQjmg5yPcnvjNZZexhO51Wix3TPfV92khzSjxdi7rPnnRHG', 'truclinhh@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(11, 'Nguyễn Phan Kim Anh', 'kimanh12', '0373456789', '2005-09-21', 0, '$2y$10$jABXIpyeqO676Lwh.0W24.Q6mlDnmp6A.e9nQdPv5Qe6wJuECspCu', 'kimanhm@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(12, 'Trần Minh Hải', 'Haithunhat', '0364567890', '2002-10-23', 1, '$2y$10$6Gafkb0tN0q2GVF740JfwePyZbAFt7Fe1j5W2SbE4J3vjVo0Iay.e', 'haiminh2gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(13, 'Lê Minh Khoa', 'leminhkhoa', '0355678901', '2004-03-16', 1, '$2y$10$ItypUNSFLZmBvKyuTOKxH.9UHr1oyf9vX8dT0X7w2nRwoxI3UDkZO', 'khoaaa@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(14, 'Lê Vân Trinh', 'trinhsayhi', '0346789012', '2005-12-03', 0, '$2y$10$N7fiz1byrE9f1K.x9TvDeOAtbtMH0MWOOsHSm4TIih6U/4pCgmMBG', 'trinhtrinh@gmail.com', 1, '2025-12-24 10:15:16', 'customer'),
(15, 'Nguyễn Lê Ngọc Anh', 'lengocanh16', '0337890123', '2005-04-16', 1, '$2y$10$29kG/dg3DnVHXIuU4YQe0edurgfj1iPxYKvqJb0G2Xlhb/mFGpg66', 'lengocanh2gmail.com', 1, '2025-12-24 10:15:17', 'customer'),
(16, 'Phan Hoàng Yến', 'yennhonhan', '0328901234', '2005-05-15', 0, '$2y$10$iypPFKdUza0RmVkFV/Nx3Ooj6kXzlAxy4RoyA7db84/EGoUD8Ahjm', 'yenyen77@gmail.com', 1, '2025-12-24 10:15:17', 'customer'),
(17, 'Nguyễn Cao Kỳ Phong', 'phongbabaotap', '0701234567', '2005-06-18', 1, '$2y$10$fOwKWUOpAL/t5x1RqTDBZ.G3FTH3WGajzqh.Ark.HqOdGyvE501Ci', 'phongbabaotap@gmail.com', 1, '2025-12-24 10:15:17', 'customer'),
(18, 'Lê Hoàng Thúy Vy', 'vyvo', '0792345678', '2005-12-02', 0, '$2y$10$vnBnHFbNGRTmHM0tsSnJ8OFMM0/tbo4yjwPGE.Xck4bxScaCcs9WW', 'ponyoo@gmail.com', 1, '2025-12-25 08:00:26', 'customer'),
(19, 'Nguyễn Thị Thu Xuyến', 'thuxuyen', '0813456789', '2003-07-17', 0, '$2y$10$suIm4cYS5H6sq61/9AM6suUnGDK7PmBwa2YFBFhO.H7kb9q3x2Jwi', 'thuxuyenisp@gmail.com', 1, '2025-12-24 10:15:17', 'customer'),
(20, 'Lê Ngọc Hân ', 'chihanxinhdep', '0824567890', '2005-03-25', 0, '$2y$10$.UzwRb/1mxssVlf9BVZrCu2ZfqBmIzuv/pAfGLx1xM98RU6pQHKze', 'hanxinhisp@gmail.com', 1, '2025-12-24 10:15:17', 'customer'),
(23, '', 'abc', NULL, NULL, 0, '$2y$10$S.VPl8E2vDTIM9kqBPoTCeqy87aqUKsvrV3iOIQtPHRDwQ5OTCy.C', 'abc@gmail.com', 1, '2025-12-24 13:03:49', 'customer'),
(24, '', 'abcd', NULL, NULL, 0, '$2y$10$eUWHhuleDoYKeZV42/M.M.FrG2aF7EHhvZDE05lj8GEvJ11nwfhuy', 'abc@gmail.com', 1, '2025-12-24 13:08:37', 'customer'),
(25, '', 'ailind91', NULL, NULL, 0, '$2y$10$a2e9Q8k0.Y3IhvMIH43OtOos/DGfC6ifLnFgWL4fSZANUCvsjHjnW', 'ailind.91xx@gmail.com', 1, '2026-01-11 09:56:09', 'customer'),
(26, '', 'abc123', NULL, NULL, 0, '$2y$10$sHbgviL7yRw/lwpOj4lcg.3XAjoSVMtA21cVx49xGQHA6VuhMT.bO', 'abc123@gmail.com', 1, '2026-01-23 06:27:39', 'customer'),
(27, '', 'abc1234', NULL, NULL, 0, '$2y$10$3w.13/PImkKbq3XXbKzJZ.GXehxj7fFvQohCVU3vVldBewH9.vJtW', 'abc1234@gmail.com', 1, '2026-01-23 06:38:02', 'customer'),
(28, 'abc', 'abc12345', '0234567823', '2002-01-23', 1, '$2y$10$7GfhjQ94mBupsiVSbZx7tumRdNw/WS6FBoBhYVrFP/pJFxTwI8Op2', 'abc12345@gmail.com', 1, '2026-01-23 07:23:19', 'customer'),
(29, 'ABC', 'abc123456', '0395464554', '2000-01-29', 0, '$2y$10$L8OEW0SXdqwyC4nF.OUGh.DBWIOOkbPv7/VUNr3GdV0wjzERZY3.m', 'abc123456@gmail.com', 1, '2026-01-29 07:48:57', 'customer');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `iduser` int(11) UNSIGNED NOT NULL,
  `type` enum('order','promotion','system','birthday') DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `iduser`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(10, 2, 'order', 'Cập nhật đơn hàng #98', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang giao\"', '/AureliusWatch/pages/order/order_detail.php?id=98', 1, '2026-01-23 17:59:09'),
(11, 2, 'order', 'Cập nhật đơn hàng #98', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=98', 1, '2026-01-23 18:04:19'),
(12, 2, '', 'Aurelius Watch đã phản hồi đánh giá của bạn', 'Đánh giá của bạn đã được Aurelius Watch phản hồi tự động.', '/AureliusWatch/pages/product/detail.php?id=AW016   #review', 1, '2026-01-23 18:13:16'),
(13, 29, 'birthday', 'Chúc mừng sinh nhật', 'Aurelius Watch kính chúc Quý khách một ngày sinh nhật trọn vẹn.', NULL, 1, '2026-01-29 14:51:25'),
(14, 2, '', 'Aurelius Watch đã phản hồi đánh giá của bạn', 'Đánh giá của bạn đã được Aurelius Watch phản hồi tự động.', '/AureliusWatch/pages/product/detail.php?id=AW020   #review', 1, '2026-01-29 15:13:52'),
(15, 2, 'order', 'Cập nhật đơn hàng #97', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang giao\"', '/AureliusWatch/pages/order/order_detail.php?id=97', 1, '2026-01-30 18:59:27'),
(16, 2, 'order', 'Cập nhật đơn hàng #97', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang giao\"', '/AureliusWatch/pages/order/order_detail.php?id=97', 1, '2026-01-30 19:06:48'),
(17, 28, 'order', 'Cập nhật đơn hàng #92', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang giao\"', '/AureliusWatch/pages/order/order_detail.php?id=92', 0, '2026-01-30 19:49:51'),
(18, 2, 'order', 'Cập nhật đơn hàng #98', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang giao\"', '/AureliusWatch/pages/order/order_detail.php?id=98', 1, '2026-01-30 19:50:09'),
(19, 2, 'order', 'Cập nhật đơn hàng #98', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=98', 1, '2026-01-30 19:54:20'),
(20, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang giao\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 1, '2026-01-30 19:58:35'),
(21, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:01:00'),
(22, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang xử lý\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:04:15'),
(23, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang xử lý\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:04:29'),
(24, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang xử lý\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:04:33'),
(25, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:04:36'),
(26, 2, 'order', 'Cập nhật đơn hàng #98', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang xử lý\"', '/AureliusWatch/pages/order/order_detail.php?id=98', 0, '2026-01-30 20:07:46'),
(27, 2, 'order', 'Cập nhật đơn hàng #98', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=98', 0, '2026-01-30 20:08:04'),
(28, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đang xử lý\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:08:19'),
(29, 2, 'order', 'Cập nhật đơn hàng #96', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=96', 0, '2026-01-30 20:08:22'),
(30, 2, 'order', 'Cập nhật đơn hàng #97', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Đã xác nhận\"', '/AureliusWatch/pages/order/order_detail.php?id=97', 0, '2026-01-30 20:08:36'),
(31, 2, 'order', 'Cập nhật đơn hàng #97', 'Trạng thái đơn hàng của bạn đã được cập nhật thành: \"Hoàn thành\"', '/AureliusWatch/pages/order/order_detail.php?id=97', 0, '2026-01-30 20:08:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `warranty`
--

CREATE TABLE `warranty` (
  `id` int(11) NOT NULL,
  `warranty_code` varchar(50) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','processing','completed','expired') DEFAULT 'active',
  `admin_note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `warranty`
--

INSERT INTO `warranty` (`id`, `warranty_code`, `order_id`, `product_name`, `user_name`, `guest_name`, `phone`, `start_date`, `end_date`, `status`, `admin_note`, `created_at`) VALUES
(1, 'AW-1-AW001', 1, 'AW001', 'Nguyễn Cao Tiến', NULL, '0934567890', '2020-12-01', '2022-12-01', 'expired', '', '2026-01-30 17:54:38'),
(2, 'AW-2-AW002', 2, 'AW002', 'Đào Văn Nam', NULL, '0987654321', '2025-12-04', '2027-12-04', 'active', NULL, '2026-01-30 17:54:38'),
(3, 'AW-3-AW003', 3, 'AW003', 'Lê Thị Thương', NULL, '0971234567', '2025-11-20', '2027-11-20', 'active', NULL, '2026-01-30 17:54:38'),
(4, 'AW-5-AW005', 5, 'AW005', 'Nguyễn Thị Thu Xuyến', NULL, '0813456789', '2025-10-15', '2027-10-15', 'active', NULL, '2026-01-30 17:54:38'),
(5, 'AW-7-AW007', 7, 'AW007', 'Nguyễn Cao Kỳ Phong', NULL, '0701234567', '2025-12-25', '2027-12-25', 'active', NULL, '2026-01-30 17:54:38'),
(6, 'AW-8-AW008', 8, 'AW008', 'Phan Hoàng Yến', NULL, '0328901234', '2026-01-07', '2028-01-07', 'active', NULL, '2026-01-30 17:54:38'),
(7, 'AW-9-AW009', 9, 'AW009', 'Nguyễn An Phú', NULL, '0912345678', '2025-09-08', '2027-09-08', 'active', NULL, '2026-01-30 17:54:38'),
(8, 'AW-10-AW010', 10, 'AW010', 'Lê Thị Thúy Vân', NULL, '0901234567', '2026-01-03', '2028-01-03', 'active', NULL, '2026-01-30 17:54:38'),
(9, 'AW-11-AW011', 11, 'AW011', 'Cao Xuân Bá', NULL, '0962345678', '2025-06-17', '2027-06-17', 'active', NULL, '2026-01-30 17:54:38'),
(10, 'AW-12-AW012', 12, 'AW012', 'Nguyễn An Nhiên', NULL, '0945678901', '2026-01-09', '2028-01-09', 'active', NULL, '2026-01-30 17:54:38'),
(11, 'AW-13-AW013', 13, 'AW013', 'Trần Ánh Mây', NULL, '0391234567', '2025-07-23', '2027-07-23', 'active', '', '2026-01-30 17:54:38'),
(12, 'AW-14-AW014', 14, 'AW014', 'Lê Vân Trinh', NULL, '0346789012', '2025-07-15', '2027-07-15', 'active', NULL, '2026-01-30 17:54:38'),
(13, 'AW-15-AW015', 15, 'AW015', 'Lê Minh Khoa', NULL, '0355678901', '2025-03-10', '2027-03-10', 'active', NULL, '2026-01-30 17:54:38'),
(14, 'AW-16-AW016', 16, 'AW016', 'Nguyễn Phan Kim Anh', NULL, '0373456789', '2024-11-15', '2026-11-15', 'active', NULL, '2026-01-30 17:54:38'),
(15, 'AW-17-AW017', 17, 'AW017', 'Nguyễn Thị Trúc Linh', NULL, '0391234567', '2024-11-06', '2026-11-06', 'active', NULL, '2026-01-30 17:54:38'),
(16, 'AW-18-AW018', 18, 'AW018', 'Nguyễn An Phú', NULL, '0912345678', '2024-12-16', '2026-12-16', 'active', NULL, '2026-01-30 17:54:38'),
(17, 'AW-20-AW020', 20, 'AW020', 'Nguyễn Cao Kỳ Phong', NULL, '0701234567', '2025-12-16', '2027-12-16', 'active', NULL, '2026-01-30 17:54:38'),
(18, 'AW-22-AW001', 22, 'AW001', 'Đào Văn Nam', NULL, '0987654321', '2026-01-01', '2028-01-01', 'active', '', '2026-01-30 17:54:38'),
(19, 'AW-22-AW002', 22, 'AW002', 'Đào Văn Nam', NULL, '0987654321', '2026-01-01', '2028-01-01', 'active', NULL, '2026-01-30 17:54:38'),
(20, 'AW-1-AW001', 1, 'AW001', 'Nguyễn Cao Tiến', NULL, '0934567890', '2020-12-01', '2022-12-01', 'expired', NULL, '2026-01-30 17:54:38'),
(21, 'AW-1-AW001', 1, 'AW001', 'Nguyễn Cao Tiến', NULL, '0934567890', '2020-12-01', '2022-12-01', 'expired', NULL, '2026-01-30 17:54:38'),
(22, 'AW-46-AW001', 46, 'AW001', NULL, 'Ca', '0395464554', '2026-01-19', '2028-01-19', 'active', NULL, '2026-01-30 17:54:38'),
(23, 'AW-46-AW007', 46, 'AW007', NULL, 'Ca', '0395464554', '2026-01-19', '2028-01-19', 'active', NULL, '2026-01-30 17:54:38'),
(24, 'AW-52-AW018', 52, 'AW018', 'Nguyễn An Phú', 'Ca', '0912345678', '2026-01-19', '2028-01-19', 'active', NULL, '2026-01-30 17:54:38'),
(25, 'AW-52-AW019', 52, 'AW019', 'Nguyễn An Phú', 'Ca', '0912345678', '2026-01-19', '2028-01-19', 'active', NULL, '2026-01-30 17:54:38'),
(26, 'AW-53-AW016', 53, 'AW016', 'Nguyễn An Phú', NULL, '0912345678', '2026-01-21', '2028-01-21', 'active', NULL, '2026-01-30 17:54:38'),
(27, 'AW-54-AW019', 54, 'AW019', 'Nguyễn An Phú', 'Ca', '0912345678', '2026-01-21', '2028-01-21', 'active', NULL, '2026-01-30 17:54:38'),
(28, 'AW-70-AW019', 70, 'AW019', NULL, 'Ca', '23456', '2026-01-22', '2028-01-22', 'active', NULL, '2026-01-30 17:54:38'),
(29, 'AW-71-AW021', 71, 'AW021', 'Nguyễn An Phú', 'Ca', '0912345678', '2026-01-22', '2028-01-22', 'active', NULL, '2026-01-30 17:54:38'),
(30, 'AW-72-AW020', 72, 'AW020', 'Nguyễn An Phú', 'Ca', '0912345678', '2026-01-22', '2028-01-22', 'active', NULL, '2026-01-30 17:54:38'),
(31, 'AW-72-AW018', 72, 'AW018', 'Nguyễn An Phú', 'Ca', '0912345678', '2026-01-22', '2028-01-22', 'active', NULL, '2026-01-30 17:54:38'),
(32, 'AW-83-AW020', 83, 'AW020', 'Nguyễn An Phú', 'Phú', '0912345678', '2026-01-22', '2028-01-22', 'active', NULL, '2026-01-30 17:54:38'),
(33, 'AW-88-AW021', 88, 'AW021', 'abc', 'abc', '0234567823', '2026-01-23', '2028-01-23', 'active', NULL, '2026-01-30 17:54:38'),
(34, 'AW-98-AW016', 98, 'AW016', 'Nguyễn An Phú', 'abc', '0912345678', '2026-01-23', '2028-01-23', 'active', NULL, '2026-01-30 17:54:38'),
(64, 'AW-96-AW016', 96, 'AW016', 'Nguyễn An Phú', 'phú', '0912345678', '2026-01-23', '2028-01-23', 'active', NULL, '2026-01-30 20:08:22'),
(65, 'AW-97-AW021', 97, 'AW021', 'Nguyễn An Phú', 'abc', '0912345678', '2026-01-23', '2028-01-23', 'active', NULL, '2026-01-30 20:08:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `warranty_history`
--

CREATE TABLE `warranty_history` (
  `id` int(11) NOT NULL,
  `warranty_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `warranty_history`
--

INSERT INTO `warranty_history` (`id`, `warranty_id`, `status`, `note`, `created_at`) VALUES
(1, 11, 'completed', '', '2026-01-30 18:11:55'),
(2, 1, 'completed', '', '2026-01-30 18:23:32'),
(3, 11, 'active', '', '2026-01-30 18:36:24'),
(4, 18, 'processing', '', '2026-01-30 18:37:39'),
(5, 18, 'processing', '', '2026-01-30 18:37:41'),
(6, 18, 'completed', '', '2026-01-30 18:37:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `watches`
--

CREATE TABLE `watches` (
  `idwatch` varchar(10) NOT NULL,
  `namewatch` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `idbrand` int(11) NOT NULL,
  `idgender` int(11) NOT NULL,
  `price` decimal(15,0) NOT NULL,
  `quantity` int(11) NOT NULL,
  `mota` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `loaimay` varchar(100) DEFAULT NULL,
  `case_material_id` int(11) DEFAULT NULL,
  `case_color_id` int(11) DEFAULT NULL,
  `strap_material_id` int(11) DEFAULT NULL,
  `glass_material_id` int(11) DEFAULT NULL,
  `kichcomatso` float DEFAULT NULL,
  `doday` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `watches`
--

INSERT INTO `watches` (`idwatch`, `namewatch`, `image`, `idbrand`, `idgender`, `price`, `quantity`, `mota`, `country`, `loaimay`, `case_material_id`, `case_color_id`, `strap_material_id`, `glass_material_id`, `kichcomatso`, `doday`) VALUES
('AW001', 'IWC Big Pilot’s IW329501 Tourbillon Le Petit Prince Watch 43mm', '/uploads/IWC-Schaffhausen/iwc-big-pilot-s-iw329501-tourbillon-le-petit-prince-watch-43mm.jpg', 1, 1, 2657200000, 2, 'Đồng hồ phi công cao cấp với tourbillon lộ cơ, cảm hứng Hoàng Tử Bé.', 'Thụy Sĩ', 'Automatic', 17, 1, 4, 6, 42, 13),
('AW002', 'IWC Schaffhausen Big Pilot’s Watch 46.5mm', '/uploads/IWC-Schaffhausen/iwc-schaffhausen-big-pilot-s-watch-46-5mm.png', 1, 1, 3543865800, 1, 'Big Pilot biểu tượng, kích thước lớn, phong cách hàng không mạnh mẽ.', 'Thụy Sĩ', 'Automatic', 17, 2, 4, 6, 42, 13),
('AW003', 'Rolex Day-Date 36 M128348RBR.0008 128348RBR.0008 Vàng Kim 18ct Mặt champagne Niềng Kim cương', '/uploads/rolex/rolex-day-date-36-m128348rbr-0008-128348rbr-0008-vang-kim-18ct-mat-champagne-nieng-kim-cuong.png', 2, 1, 1510000000, 3, 'Đồng hồ cao cấp bằng vàng hồng 18 ct, mặt số champagne sang trọng với niềng đính kim cương, tích hợp lịch ngày và thứ hiển thị rõ ràng, biểu tượng đẳng cấp của Rolex.', 'Thụy Sĩ', 'Automatic', 3, 3, 5, 6, 36, 13),
('AW004', 'Rolex Sky-Dweller 336239-0003 Watch 42mm', '/uploads/Rolex/rolex-sky-dweller-336239-0003-watch-42mm.png', 2, 3, 1383480000, 5, 'Đồng hồ du lịch cao cấp với lịch thường niên và múi giờ kép.', 'Thụy Sĩ', 'Automatic', 17, 4, 4, 6, 42, 13),
('AW005', 'Rolex Land-Dweller 127336-0001 Oyster Platinum Watch 40mm', '/uploads/Rolex/rolex-land-dweller-127336-0001-oyster-platinum-watch-40mm.png', 2, 1, 2600100000, 3, 'Phiên bản platinum sang trọng, thiết kế thanh lịch, đẳng cấp.', 'Thụy Sĩ', 'Automatic', 2, 5, 5, 6, 40, 13),
('AW006', 'Rolex Land-Dweller 127285tbr-0002 Oyster Everose Watch 36mm', '/uploads/Rolex/rolex-land-dweller-127285tbr-0002-oyster-everose-watch-36mm.png', 2, 2, 3188835000, 1, 'Vàng Everose đính kim cương, tinh tế và nữ tính.', 'Thụy Sĩ', 'Automatic', 3, 6, 5, 6, 36, 13),
('AW007', 'Rolex Submariner 124060-0001 Oyster Oystersteel Watch 41mm', '/uploads/Rolex/rolex-submariner-124060-0001-oyster-oystersteel-watch-41mm.png', 2, 3, 430650000, 4, 'Đồng hồ lặn huyền thoại, bền bỉ và thể thao.', 'Thụy Sĩ', 'Automatic', 17, 5, 5, 6, 41, 12.5),
('AW008', 'Patek Philippe 5168G.001 Aquanaut Arabic 42.2mm', '/uploads/Patek-Philippe/patek-philippe-5168g-001-aquanaut-arabic-42-2mm.png', 3, 1, 1961238900, 8, 'Sport-luxury bằng vàng trắng, mặt số Ả Rập độc đáo.', 'Thụy Sĩ', 'Automatic', 18, 2, 4, 6, 42, 13),
('AW009', 'Patek Philippe Nautilus 40th Anniversary 5976 1G 001 White Gold Limited 1300 pieces', '/uploads/Patek-Philippe/patek-philippe-nautilus-40th-anniversary-5976-1g-001-white-gold-limited-1300-pieces.jpg', 3, 1, 2498265000, 3, 'Phiên bản kỷ niệm 40 năm, vàng trắng 18K, chronograph tự động, giới hạn 1.300 chiếc.', 'Thụy Sĩ', 'Automatic', 18, 5, 5, 6, 41, 13),
('AW010', 'Patek Philippe Grand Complications Watch 41mm', '/uploads/Patek-Philippe/patek-philippe-grand-complications-watch-41mm.jpg', 3, 1, 17472000000, 5, 'Dòng đồng hồ cơ phức tạp bậc nhất thế giới.', 'Thụy Sĩ', 'Automatic', 1, 4, 4, 6, 41, 0),
('AW011', 'Cartier Santos de Cartier WHSA0028 Skeleton Watch 47.5 mm x 39.7 mm', '/uploads/Cartier/cartier-santos-de-cartier-whsa0028-skeleton-watch-47-5-mm-x-39-7-mm.png', 4, 3, 993613680, 3, 'Santos skeleton lộ cơ, phong cách hiện đại và kỹ thuật cao.', 'Pháp', 'Automatic', 17, 5, 5, 6, 47.5, 13),
('AW012', 'Cartier Santos-Dumont WHSA0030 Watch 31mm', '/uploads/Cartier/cartier-santos-dumont-whsa0030-watch-31mm.jpg', 4, 2, 1274230800, 5, 'Thiết kế cổ điển, thanh lịch và tinh tế.', 'Pháp', 'Automatic', 17, 1, 4, 6, 31, 13),
('AW013', 'Cartier Baignoire WJBA0041 Watch 23,1mm', '/uploads/Cartier/cartier-baignoire-wjba0041-watch-23-1mm.jpg', 4, 2, 856170500, 4, 'Thiết kế oval biểu tượng, nữ tính và sang trọng.', 'Pháp', 'Automatic', 3, 6, 5, 6, 23.1, 13),
('AW014', 'Cartier Baignoire CRHPI01823 Watch 23,1mm', '/uploads/Cartier/cartier-baignoire-crhpi01823-watch-23-1mm.png', 4, 2, 2448241200, 3, 'Phiên bản cao cấp đính đá quý sang trọng.', 'Pháp', 'Automatic', 3, 5, 5, 6, 23.1, 13),
('AW015', 'Omega Seamaster 220.55.38.20.09.001 Aqua Terra 38mm', '/uploads/Omega/omega-seamaster-220-55-38-20-09-001-aqua-terra-38mm.png', 5, 2, 1039878000, 5, 'Đồng hồ thanh lịch, chống nước tốt, đa dụng.', 'Thụy Sĩ', 'Automatic', 17, 6, 5, 6, 38, 13.6),
('AW016', 'Omega Seamaster 220.92.41.21.03.002 Aqua Terra 150M Watch 41mm', '/uploads/Omega/omega-seamaster-220-92-41-21-03-002-aqua-terra-150m-watch-41mm.png', 5, 3, 1654438300, 5, 'Thể thao – sang trọng, chống nước 150m.', 'Thụy Sĩ', 'Automatic', 17, 3, 4, 6, 41, 13.6),
('AW017', 'Omega Constellation 131.50.41.21.99.001 Rose Gold Watch 41 mm', '/uploads/omega/1768805684_696dd534ed65e.png', 5, 3, 1249229100, 1, 'Vàng hồng cao cấp, phong cách sang trọng.', 'Thụy Sĩ', 'Automatic', 3, 6, 5, 6, 41, 13),
('AW018', 'Gucci 25H Watch 40mm', '/uploads/Gucci/gucci-25h-watch-40mm12.png', 6, 3, 106250000, 5, 'Thiết kế siêu mỏng, hiện đại và thời trang.', 'Ý', 'Automatic', 17, 5, 5, 6, 40, 13),
('AW019', 'Gucci 25H Watch 36mm', '/uploads/Gucci/gucci-25h-watch-36mm.jpg', 6, 2, 57500000, 2, 'Phiên bản nhỏ gọn, thanh lịch.', 'Ý', 'Automatic', 17, 3, 4, 6, 36, 13),
('AW020', 'Hublot Big Bang 455.JX.0120.JX Integral Tourbillon Full Sapphire 43mm', '/uploads/Hublot/hublot-big-bang-455-jx-0120-jx-integral-tourbillon-full-sapphire-43mm.jpg', 7, 3, 8280906000, 8, 'Vỏ và dây sapphire trong suốt, tourbillon cao cấp, thiết kế Big Bang mạnh mẽ.', 'Thụy Sĩ', 'Automatic', 19, 5, 5, 6, 43, 13),
('AW021', 'Hublot Big Bang 431.MX.1330.RX 20th Anniversary Full Magic Gold Watch 43mm', '/uploads/Hublot/hublot-big-bang-431-mx-1330-rx-20th-anniversary-full-magic-gold-watch-43mm.jpg', 7, 1, 1033150950, 4, 'Magic Gold độc quyền, thể thao mạnh mẽ.', 'Thụy Sĩ', 'Automatic', 20, 4, 4, 6, 43, 13);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `target_type` (`target_type`),
  ADD KEY `created_at` (`created_at`);

--
-- Chỉ mục cho bảng `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `baocao_sanpham_banchay`
--
ALTER TABLE `baocao_sanpham_banchay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bc_watch` (`idwatch`);

--
-- Chỉ mục cho bảng `baocao_theo_thuonghieu`
--
ALTER TABLE `baocao_theo_thuonghieu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `baocao_thongke`
--
ALTER TABLE `baocao_thongke`
  ADD PRIMARY KEY (`idreport`),
  ADD KEY `fk_baocao_user` (`iduser`);

--
-- Chỉ mục cho bảng `birthday_popup_log`
--
ALTER TABLE `birthday_popup_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_year` (`iduser`,`shown_year`);

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`idbrand`),
  ADD UNIQUE KEY `namebrand` (`namebrand`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`idcart`),
  ADD UNIQUE KEY `uniq_user_cart` (`user_id`,`status`),
  ADD UNIQUE KEY `uniq_session_status` (`session_id`,`status`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`iditem`),
  ADD UNIQUE KEY `uniq_cart_product` (`idcart`,`idwatch`),
  ADD KEY `fk_cart_items_watch` (`idwatch`);

--
-- Chỉ mục cho bảng `case_colors`
--
ALTER TABLE `case_colors`
  ADD PRIMARY KEY (`idcolor`);

--
-- Chỉ mục cho bảng `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`idcontact`);

--
-- Chỉ mục cho bảng `genders`
--
ALTER TABLE `genders`
  ADD PRIMARY KEY (`idgender`),
  ADD UNIQUE KEY `namegender` (`namegender`);

--
-- Chỉ mục cho bảng `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`idmaterial`),
  ADD UNIQUE KEY `namematerial` (`namematerial`,`material_type`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`idorder`),
  ADD KEY `iduser` (`iduser`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oi_order` (`order_id`),
  ADD KEY `fk_oi_watch` (`watch_id`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`idpayment`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`idreview`),
  ADD UNIQUE KEY `uniq_review_order_item` (`iduser`,`idorder`,`idwatch`),
  ADD UNIQUE KEY `unique_review_per_user_order_item` (`iduser`,`idwatch`,`idorder_item`),
  ADD KEY `idwatch` (`idwatch`),
  ADD KEY `idorder` (`idorder`),
  ADD KEY `idorder_item` (`idorder_item`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- Chỉ mục cho bảng `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`iduser`);

--
-- Chỉ mục cho bảng `warranty`
--
ALTER TABLE `warranty`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `warranty_history`
--
ALTER TABLE `warranty_history`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `watches`
--
ALTER TABLE `watches`
  ADD PRIMARY KEY (`idwatch`),
  ADD KEY `idbrand` (`idbrand`),
  ADD KEY `idgender` (`idgender`),
  ADD KEY `case_material_id` (`case_material_id`),
  ADD KEY `strap_material_id` (`strap_material_id`),
  ADD KEY `glass_material_id` (`glass_material_id`),
  ADD KEY `fk_case_color` (`case_color_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT cho bảng `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `baocao_sanpham_banchay`
--
ALTER TABLE `baocao_sanpham_banchay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT cho bảng `baocao_theo_thuonghieu`
--
ALTER TABLE `baocao_theo_thuonghieu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `baocao_thongke`
--
ALTER TABLE `baocao_thongke`
  MODIFY `idreport` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã báo cáo', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `birthday_popup_log`
--
ALTER TABLE `birthday_popup_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `idbrand` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `idcart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `iditem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT cho bảng `case_colors`
--
ALTER TABLE `case_colors`
  MODIFY `idcolor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `contact`
--
ALTER TABLE `contact`
  MODIFY `idcontact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `genders`
--
ALTER TABLE `genders`
  MODIFY `idgender` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `materials`
--
ALTER TABLE `materials`
  MODIFY `idmaterial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `idorder` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã đơn hàng', AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `idpayment` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã thanh toán', AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `idreview` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Khóa chính', AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `warranty`
--
ALTER TABLE `warranty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `warranty_history`
--
ALTER TABLE `warranty_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `baocao_sanpham_banchay`
--
ALTER TABLE `baocao_sanpham_banchay`
  ADD CONSTRAINT `fk_bc_watch` FOREIGN KEY (`idwatch`) REFERENCES `watches` (`idwatch`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `baocao_thongke`
--
ALTER TABLE `baocao_thongke`
  ADD CONSTRAINT `fk_baocao_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`idcart`) REFERENCES `carts` (`idcart`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_watch` FOREIGN KEY (`idwatch`) REFERENCES `watches` (`idwatch`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`idorder`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_watch` FOREIGN KEY (`watch_id`) REFERENCES `watches` (`idwatch`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`idwatch`) REFERENCES `watches` (`idwatch`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_3` FOREIGN KEY (`idorder`) REFERENCES `orders` (`idorder`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_4` FOREIGN KEY (`idorder_item`) REFERENCES `order_items` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `fk_user_notifications_user` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `watches`
--
ALTER TABLE `watches`
  ADD CONSTRAINT `fk_case_color` FOREIGN KEY (`case_color_id`) REFERENCES `case_colors` (`idcolor`),
  ADD CONSTRAINT `watches_ibfk_1` FOREIGN KEY (`idbrand`) REFERENCES `brands` (`idbrand`),
  ADD CONSTRAINT `watches_ibfk_2` FOREIGN KEY (`idgender`) REFERENCES `genders` (`idgender`),
  ADD CONSTRAINT `watches_ibfk_3` FOREIGN KEY (`case_material_id`) REFERENCES `materials` (`idmaterial`),
  ADD CONSTRAINT `watches_ibfk_4` FOREIGN KEY (`strap_material_id`) REFERENCES `materials` (`idmaterial`),
  ADD CONSTRAINT `watches_ibfk_5` FOREIGN KEY (`glass_material_id`) REFERENCES `materials` (`idmaterial`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
