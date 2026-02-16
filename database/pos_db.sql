-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 17, 2026 at 12:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Kopi', 'Berbagai jenis minuman kopi', 'categories/NQgtegqQlMwmXJjTUMgYbrU5wzGu9CtIKqfpEyBc.jpg', 1, '2026-02-13 02:42:32', '2026-02-14 19:41:55'),
(2, 'Teh', 'Berbagai jenis minuman teh', 'categories/24VHHPNN9gwtapaMbUOkEWgqoP39eB3NOZasyKyd.jpg', 1, '2026-02-13 02:42:32', '2026-02-14 19:36:27'),
(3, 'Pastry', 'Kue dan roti segar', 'categories/lL9lEL3DGsS1ZMotPdPMRbaxHCEZuDcLqtqxQRWb.jpg', 1, '2026-02-13 02:42:32', '2026-02-14 19:36:36'),
(4, 'Makanan Ringan', 'Snack dan makanan ringan', 'categories/JHQZSC1YWKoVcmYdGyOOaiMbdXW2js5BlRSaWd2D.jpg', 1, '2026-02-13 02:42:32', '2026-02-14 19:36:52'),
(5, 'Merchandise', 'Barang dagangan kopi', 'categories/bAUNOEiWPpJiWU4ujEgALeZrCjfUAx8Bj6AqHzOa.jpg', 1, '2026-02-13 02:42:32', '2026-02-14 19:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_30_120454_create_personal_access_tokens_table', 1),
(5, '2026_01_30_120609_create_categories_table', 1),
(6, '2026_01_30_120618_create_products_table', 1),
(7, '2026_01_30_120625_create_transactions_table', 1),
(8, '2026_01_30_120634_create_transaction_details_table', 1),
(9, '2026_02_13_222700_create_payment_methods_table', 2),
(10, '2026_02_13_222749_add_payment_method_to_transactions_table', 2),
(11, '2026_02_14_003029_update_payment_method_column_in_transactions_table', 3),
(12, '2026_02_14_223208_add_image_to_categories_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'cash',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `code`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Cash', 'cash', 'cash', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(2, 'QRIS', 'qris', 'digital', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(3, 'BCA', 'bca', 'bank', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(4, 'Mandiri', 'mandiri', 'bank', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(5, 'BRI', 'bri', 'bank', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(6, 'BNI', 'bni', 'bank', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(7, 'DANA', 'dana', 'digital', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54'),
(8, 'OVO', 'ovo', 'digital', 1, '2026-02-13 15:41:54', '2026-02-13 15:41:54');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '0b7b2c8b52857b852f9bd223914b686b3ecda1dab380ce230ff676a367d20a3f', '[\"*\"]', NULL, NULL, '2026-01-30 05:40:30', '2026-01-30 05:40:30'),
(2, 'App\\Models\\User', 1, 'auth_token', '2507c4cce8c078ccb19bf993811665ce295314eb411bb9df32bc7ac4805caf95', '[\"*\"]', NULL, NULL, '2026-01-30 06:23:31', '2026-01-30 06:23:31'),
(4, 'App\\Models\\User', 1, 'auth_token', '4308ceca73888a320e77a3cdbc2dea7c03d442c21168352a88d6236ddf476ba4', '[\"*\"]', '2026-01-30 06:35:11', NULL, '2026-01-30 06:32:15', '2026-01-30 06:35:11'),
(5, 'App\\Models\\User', 1, 'auth_token', '514b745fa7e9729dd67cdf3b82468639075af50570d622d2cfc1fcfc022d6343', '[\"*\"]', '2026-01-31 05:52:21', NULL, '2026-01-31 05:51:39', '2026-01-31 05:52:21'),
(6, 'App\\Models\\User', 2, 'auth_token', '0ff28861033d7cd455a4ab0c9e0fa99076245f80e9ff3f30cd92fab9e0eff089', '[\"*\"]', NULL, NULL, '2026-01-31 06:02:31', '2026-01-31 06:02:31'),
(7, 'App\\Models\\User', 3, 'auth_token', 'cfe0ab59a28821d7fe6533f5776be021f257873d6c1c9bf10074e12a9d28b6de', '[\"*\"]', NULL, NULL, '2026-01-31 06:02:58', '2026-01-31 06:02:58'),
(8, 'App\\Models\\User', 2, 'auth_token', 'b6a5793d322eaca45085a3e112a46efb08323c0c95eee96585f29003d4cfc679', '[\"*\"]', '2026-01-31 06:39:52', NULL, '2026-01-31 06:03:27', '2026-01-31 06:39:52'),
(9, 'App\\Models\\User', 2, 'auth_token', 'fef9b5c0d3fc7a23f6edcf145bbb8f1f5472bee2a889f41f3c903f94dc7e6aac', '[\"*\"]', '2026-01-31 07:23:23', NULL, '2026-01-31 06:40:50', '2026-01-31 07:23:23'),
(10, 'App\\Models\\User', 1, 'auth_token', '971b11bdd9e38f49abe5d482543cac48bb280ee9de4df037f9f2059a73e0b0cc', '[\"*\"]', NULL, NULL, '2026-01-31 08:35:05', '2026-01-31 08:35:05'),
(11, 'App\\Models\\User', 1, 'auth_token', '29f09f0d1f906fc7fb7e6ae125c67186fdb3008ed617ae05eadbbf2579278ca9', '[\"*\"]', '2026-01-31 14:34:46', NULL, '2026-01-31 14:26:36', '2026-01-31 14:34:46'),
(12, 'App\\Models\\User', 3, 'auth_token', 'f10078a72152511f5545ad9fca23a98285709bbb1513620ad779eb07b079d840', '[\"*\"]', '2026-01-31 14:29:47', NULL, '2026-01-31 14:29:07', '2026-01-31 14:29:47'),
(13, 'App\\Models\\User', 3, 'auth_token', '7b4a83c7265d3d10b8cf0438b1166eb445119ad891b62f9c5419c1cd3a1d68e1', '[\"*\"]', '2026-01-31 14:35:09', NULL, '2026-01-31 14:35:09', '2026-01-31 14:35:09'),
(14, 'App\\Models\\User', 3, 'auth_token', 'b2e99e33dc47568a6598a7083125e733e044474506e77cfac67e7ab5d2ff717e', '[\"*\"]', '2026-01-31 14:35:28', NULL, '2026-01-31 14:35:27', '2026-01-31 14:35:28'),
(17, 'App\\Models\\User', 1, 'auth_token', 'c80efaa642e21e5d8af19ba4038eddab4cf07566da0cda0ebff518fd89963778', '[\"*\"]', '2026-01-31 19:41:43', NULL, '2026-01-31 14:44:37', '2026-01-31 19:41:43'),
(18, 'App\\Models\\User', 4, 'auth_token', 'bf555ef4b85a2b89890aa0053c6201762c07aa932a5841ce0901fcc87d1d6848', '[\"*\"]', '2026-01-31 19:39:59', NULL, '2026-01-31 19:39:44', '2026-01-31 19:39:59'),
(19, 'App\\Models\\User', 4, 'auth_token', '5b0676ed595100002c1fc632ceaddccc2d50e832ce93f89ef71b18d6e68ba1e6', '[\"*\"]', '2026-01-31 19:42:14', NULL, '2026-01-31 19:42:09', '2026-01-31 19:42:14'),
(21, 'App\\Models\\User', 1, 'auth_token', '8f1146b6b44ae20ad3826fff346815cf3ab174b18a8ce882a385ee6138259979', '[\"*\"]', '2026-02-05 11:26:40', NULL, '2026-01-31 19:42:39', '2026-02-05 11:26:40'),
(22, 'App\\Models\\User', 2, 'auth_token', '06542a43a91339ad57b22686edbd242b516dd82aaa24667c7781cb23871306a3', '[\"*\"]', '2026-02-05 11:27:04', NULL, '2026-02-05 11:27:00', '2026-02-05 11:27:04'),
(23, 'App\\Models\\User', 2, 'auth_token', '3ad3868a6118041b33978c617bf1eecc108b260fca52c46585d9bb25dca332ba', '[\"*\"]', '2026-02-05 11:41:05', NULL, '2026-02-05 11:38:31', '2026-02-05 11:41:05'),
(26, 'App\\Models\\User', 1, 'auth_token', '0b8b556176f40bbd3011f1068196ad109d2defd6545b6ae847bbd2efd0eda002', '[\"*\"]', '2026-02-05 13:18:22', NULL, '2026-02-05 13:18:22', '2026-02-05 13:18:22'),
(52, 'App\\Models\\User', 3, 'auth_token', '506b3f2df86fc69721c79bd53792fb8ece569d982990cfafa9839dd05ee1f386', '[\"*\"]', '2026-02-16 10:38:52', NULL, '2026-02-07 12:49:42', '2026-02-16 10:38:52'),
(57, 'App\\Models\\User', 6, 'auth_token', 'acd59935f8595f67c00158ae8126d45d8d677891a55a290664a29b509921ea92', '[\"*\"]', NULL, NULL, '2026-02-07 13:01:01', '2026-02-07 13:01:01'),
(62, 'App\\Models\\User', 5, 'auth_token', 'a08e4112328e74567ef96d250a4f8a0f7a0452017fd8ce911d00b29586e3c699', '[\"*\"]', '2026-02-07 13:11:31', NULL, '2026-02-07 13:11:29', '2026-02-07 13:11:31'),
(71, 'App\\Models\\User', 5, 'auth_token', 'ef555ab9132aa264f1127b7ff7d6339700d5474d4760805b58ab5023bbf71de2', '[\"*\"]', '2026-02-07 15:43:29', NULL, '2026-02-07 15:03:30', '2026-02-07 15:43:29'),
(72, 'App\\Models\\User', 5, 'auth_token', '34932919392a3fd4ef276009e5e5b45b6da0cf2068d3c7163c5205388f153152', '[\"*\"]', '2026-02-07 18:17:48', NULL, '2026-02-07 15:09:25', '2026-02-07 18:17:48'),
(80, 'App\\Models\\User', 6, 'auth_token', '6ba6d6fca17960115b1452cf5280f1613d1564ab8ab0ea4625eb4995ebb60eb5', '[\"*\"]', '2026-02-07 16:46:47', NULL, '2026-02-07 16:46:45', '2026-02-07 16:46:47'),
(81, 'App\\Models\\User', 6, 'auth_token', '7aefb3b1f80d3cccdfde7d668995332647ee1f1c7b7eec31d60ac1637bbc0564', '[\"*\"]', '2026-02-07 16:46:54', NULL, '2026-02-07 16:46:53', '2026-02-07 16:46:54'),
(82, 'App\\Models\\User', 6, 'auth_token', '473c5122760ed0e8655e38ded6f84479f7e567eda926c48c7123b2d3458afa5b', '[\"*\"]', '2026-02-07 16:47:11', NULL, '2026-02-07 16:47:08', '2026-02-07 16:47:11'),
(87, 'App\\Models\\User', 5, 'auth_token', '88e78cfa597a7dbc6a510ffa338be3ffb9ea680bc94d3cf37a353c692a656812', '[\"*\"]', '2026-02-07 17:00:44', NULL, '2026-02-07 16:54:34', '2026-02-07 17:00:44'),
(114, 'App\\Models\\User', 6, 'auth_token', '61e86af8a7a6dd5a91c682e50d8bf15cd3b4f89c27073cc519e05d33c1253143', '[\"*\"]', '2026-02-13 15:44:42', NULL, '2026-02-13 15:44:27', '2026-02-13 15:44:42'),
(120, 'App\\Models\\User', 6, 'auth_token', '565debfe8a09cd84d250b24ab0a56c002386d9cbfba6cb6dc24e5e491a14d54b', '[\"*\"]', '2026-02-13 17:40:59', NULL, '2026-02-13 17:40:57', '2026-02-13 17:40:59'),
(147, 'App\\Models\\User', 5, 'auth_token', '88c15f11f880ab0ca457557da9624336f0188848e76b6cc85b8bb69fecac0ae5', '[\"*\"]', '2026-02-16 12:29:26', NULL, '2026-02-16 12:25:30', '2026-02-16 12:29:26'),
(148, 'App\\Models\\User', 6, 'auth_token', '7949d44b71900940b5d84162e86681c9b70503d20444c2691831c726b2d7bf43', '[\"*\"]', '2026-02-16 13:11:59', NULL, '2026-02-16 13:11:54', '2026-02-16 13:11:59'),
(151, 'App\\Models\\User', 6, 'auth_token', '2ca248ef18ede28423cf72a96c825b65bf0c18d68f194243b20f4e8821448b90', '[\"*\"]', '2026-02-16 13:20:49', NULL, '2026-02-16 13:20:45', '2026-02-16 13:20:49'),
(153, 'App\\Models\\User', 5, 'auth_token', '85985dad5b3c41deaac62240f0d0c999b0ad63afb202c0526c1aa2fb77393b7b', '[\"*\"]', '2026-02-16 13:22:53', NULL, '2026-02-16 13:21:50', '2026-02-16 13:22:53'),
(154, 'App\\Models\\User', 5, 'auth_token', 'c17b062de19f875daea971881cf4efcfeb273eb1edf48115de9476b283a0b886', '[\"*\"]', '2026-02-16 13:49:00', NULL, '2026-02-16 13:32:56', '2026-02-16 13:49:00'),
(155, 'App\\Models\\User', 5, 'auth_token', '28f5ac90a2250a6c96e0c2544a8fb24cddc4bb2a953a2c201719dd077a56db76', '[\"*\"]', '2026-02-16 13:59:31', NULL, '2026-02-16 13:59:24', '2026-02-16 13:59:31'),
(156, 'App\\Models\\User', 6, 'auth_token', '65561b39e9a6757ce287d46929fff93fd1fd61255473442da8bf49d4caf5cff5', '[\"*\"]', '2026-02-16 15:30:43', NULL, '2026-02-16 14:23:39', '2026-02-16 15:30:43'),
(158, 'App\\Models\\User', 5, 'auth_token', '2e5683c9c12908eeebd482ceaf080a8a8db78497742abd3b5a03c48717834283', '[\"*\"]', '2026-02-16 14:27:55', NULL, '2026-02-16 14:27:37', '2026-02-16 14:27:55'),
(167, 'App\\Models\\User', 5, 'auth_token', '01a15d5b8b0955fcbcd1f2cbb715f1cf983a09e2fe738d76bac5fe39b2c3be4b', '[\"*\"]', '2026-02-16 15:56:44', NULL, '2026-02-16 15:52:41', '2026-02-16 15:56:44');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `min_stock` int(11) NOT NULL DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `sku`, `price`, `stock`, `min_stock`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Americano', 'Kopi hitam klasik dengan sedikit crema', 'COF-AME-001', 25000.00, 95, 5, 'products/1_americano.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(2, 1, 'Cappuccino', 'Espresso dengan susu uap dan busa tebal', 'COF-CAP-002', 28000.00, 98, 5, 'products/2_cappucino.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(3, 1, 'Latte', 'Espresso dengan susu uap lembut dan sedikit busa', 'COF-LAT-003', 30000.00, 97, 5, 'products/3_latte.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(4, 1, 'Mocha', 'Espresso, susu, cokelat, dan whipped cream', 'COF-MOC-004', 32000.00, 98, 5, 'products/4_mochaccino.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(5, 1, 'Flat White', 'Espresso pekat dengan microfoam halus', 'COF-FLAT-005', 30000.00, 96, 5, 'products/5_flat_white.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(6, 1, 'Macchiato', 'Espresso dengan sedikit busa susu di atasnya', 'COF-MAC-006', 27000.00, 98, 5, 'products/6_macchiato.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(7, 1, 'Cold Brew', 'Kopi diseduh dingin selama 16 jam', 'COF-COLD-007', 30000.00, 97, 5, 'products/7_cold_brew.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(8, 1, 'Iced Latte', 'Espresso dengan susu dingin dan es batu', 'COF-ICED-008', 32000.00, 98, 5, 'products/8_iced_latte.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(9, 1, 'Pour Over', 'Kopi single-origin diseduh manual', 'COF-POUR-009', 35000.00, 94, 5, 'products/9_pour_over.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:50'),
(10, 1, 'French Press', 'Kopi full-body dengan metode perendaman', 'COF-FREN-010', 33000.00, 98, 5, 'products/10_french_press.jpg', 1, '2026-02-13 21:32:35', '2026-02-16 14:31:02'),
(11, 1, 'Nitro Cold Brew', 'Cold brew dengan nitrogen untuk tekstur creamy', 'COF-NITRO-011', 37000.00, 100, 5, 'products/11_nitro_cold_brew.jpg', 1, '2026-02-13 21:35:41', '2026-02-13 21:35:41'),
(12, 1, 'Affogato', 'Espresso dituang di atas es krim vanilla', 'COF-AFFO-012', 38000.00, 100, 5, 'products/12_affogato.jpg', 1, '2026-02-13 21:35:41', '2026-02-13 21:35:41'),
(13, 1, 'Espresso Tonic', 'Espresso dengan air tonik dan kulit jeruk', 'COF-ESP-013', 30000.00, 99, 5, 'products/13_espresso_tonic.jpg', 1, '2026-02-13 21:35:41', '2026-02-14 19:54:06'),
(14, 1, 'Cortado', 'Perpaduan espresso dan susu uap sama rata', 'COF-CORT-014', 27000.00, 99, 0, 'products/14_cortado.jpg', 1, '2026-02-13 21:35:41', '2026-02-14 19:54:06'),
(15, 1, 'Caramel Macchiato', 'Espresso, susu, karamel, dan vanila', 'COF-CAR-015', 33000.00, 99, 5, 'products/15_caramel_macchiato.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:50'),
(16, 2, 'Earl Grey', 'Teh hitam dengan aroma bergamot', 'TEA-ERL-016', 22000.00, 99, 5, 'products/16_earl_grey.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:50'),
(17, 2, 'Green Tea', 'Teh hijau Jepang berkualitas tinggi', 'TEA-GRE-017', 20000.00, 96, 5, 'products/17_green_tea.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:50'),
(18, 2, 'Chamomile Tea', 'Teh herbal tanpa kafein, menenangkan', 'TEA-CHAM-018', 22000.00, 98, 5, 'products/18_chamomile_tea.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:50'),
(19, 2, 'Matcha Latte', 'Matcha premium dengan susu uap', 'TEA-MAT-019', 32000.00, 100, 5, 'products/19_matcha_latte.jpg', 1, '2026-02-13 21:35:41', '2026-02-13 21:35:41'),
(20, 2, 'Chai Latte', 'Teh rempah dengan susu dan madu', 'TEA-CHAI-020', 30000.00, 100, 5, 'products/20_chai_latte.jpg', 1, '2026-02-13 21:35:41', '2026-02-13 21:35:41'),
(21, 2, 'Peppermint Tea', 'Teh peppermint segar tanpa kafein', 'TEA-PEP-021', 22000.00, 97, 5, 'products/21_peppermint_tea.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:50'),
(22, 2, 'Hibiscus Tea', 'Teh herbal merah dengan rasa asam segar', 'TEA-HIB-022', 24000.00, 97, 5, 'products/22_hibiscus_tea.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:02'),
(23, 2, 'Oolong Tea', 'Teh Oolong China setengah teroksidasi', 'TEA-OOL-023', 26000.00, 98, 5, 'products/23_oolong_tea.jpg', 1, '2026-02-13 21:35:41', '2026-02-16 14:31:50'),
(24, 2, 'Jasmine Tea', 'Teh hijau dengan aroma melati', 'TEA-JAS-024', 24000.00, 100, 5, 'products/24_jasmine_tea.jpg', 1, '2026-02-13 21:36:52', '2026-02-13 21:36:52'),
(25, 2, 'Black Tea', 'Teh hitam klasik Assam', 'TEA-BLK-025', 20000.00, 100, 5, 'products/25_black_tea.jpg', 1, '2026-02-13 21:37:05', '2026-02-13 21:37:05'),
(26, 3, 'Croissant', 'Pastry mentega berlapis renyah', 'PAS-CRO-026', 18000.00, 98, 5, 'products/26_croissant.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 17:31:27'),
(27, 3, 'Blueberry Muffin', 'Muffin blueberry dengan topping streusel', 'PAS-MUFF-027', 18000.00, 99, 5, 'products/27_blueberry_muffin.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 17:31:11'),
(28, 3, 'Scone', 'Scone tradisional dengan krim dan selai', 'PAS-SCO-028', 20000.00, 100, 5, 'products/28_scone.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(29, 3, 'Cinnamon Roll', 'Roti gulung kayu manis dengan glaze keju', 'PAS-CIN-029', 22000.00, 100, 5, 'products/29_cinnamon_roll.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(30, 3, 'Donat Glaze', 'Donat empuk dengan glaze gula', 'PAS-DON-030', 15000.00, 100, 5, 'products/30_donat_glaze.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(31, 3, 'Chocolate Chip Cookie', 'Kue cokelat chip hangat', 'PAS-COOKIE-031', 15000.00, 100, 5, 'products/31_ccc.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(32, 3, 'Banana Bread', 'Roti pisang lembut dengan kenari', 'PAS-BAN-032', 25000.00, 100, 5, 'products/32_banana_bread.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(33, 3, 'Tiramisu', 'Dessert Italia dengan rasa kopi', 'PAS-TIRA-033', 32000.00, 100, 5, 'products/33_tiramisu.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(34, 3, 'Cheesecake', 'Cheesecake ala New York', 'PAS-CHE-034', 30000.00, 100, 5, 'products/34_cheesecake.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(35, 3, 'Almond Biscotti', 'Kue kering almond panggang dua kali', 'PAS-BISC-035', 18000.00, 99, 5, 'products/35_almond_biscotti.jpg', 1, '2026-02-13 21:37:33', '2026-02-14 19:54:06'),
(36, 4, 'Granola Bar', 'Granola buatan sendiri dengan madu & kacang', 'SNK-GRAN-036', 20000.00, 100, 5, 'products/36_granola_bar.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(37, 4, 'Fruit Salad', 'Potongan buah segar musiman', 'SNK-FRUIT-037', 32000.00, 100, 5, 'products/37_fruit_salad.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(38, 4, 'Yogurt Parfait', 'Yogurt berlapis granola dan buah beri', 'SNK-YOG-038', 28000.00, 100, 5, 'products/38_yogurt_parfait.jpg', 1, '2026-02-13 21:37:33', '2026-02-13 21:37:33'),
(39, 4, 'Avocado Toast', 'Sourdough with smashed avocado and chili flakes', 'SNK-AVOC-039', 45000.00, 94, 5, 'products/1771276455_39_avocado_toast.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(40, 4, 'Veggie Wrap', 'Wrap hummus, bayam, dan sayur panggang', 'SNK-WRAP-040', 42000.00, 96, 5, 'products/1771276470_40_veggie_wrap.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(41, 4, 'Quiche Lorraine', 'Quiche Prancis dengan bacon & keju', 'SNK-QUIC-041', 38000.00, 94, 5, 'products/41_quiche_lorraine.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(42, 4, 'Hummus & Pita', 'Hummus kacang arab dengan roti pita hangat', 'SNK-HUM-042', 35000.00, 95, 5, 'products/42_hummus_and_pita.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(43, 4, 'Energy Ball', 'Bola energi tanpa panggang dari kurma & kacang', 'SNK-ENER-043', 18000.00, 96, 5, 'products/43_energy_ball.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 15:30:43'),
(44, 4, 'Chicken Sandwich', 'Roti dengan ayam panggang, selada & mayones', 'SNK-SAND-044', 40000.00, 97, 5, 'products/44_chicken_sandwich.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(45, 4, 'Egg Sandwich', 'Roti dengan telur dadar dan keju', 'SNK-EGG-045', 35000.00, 93, 5, 'products/45_egg_sandwich.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 15:30:43'),
(46, 5, 'Tumbler Kopi', 'Tumbler bambu 400ml dengan tutup', 'MER-TUMB-046', 85000.00, 48, 5, 'products/46_tumbler_kopi.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(47, 5, 'Biji Kopi 250gr', 'Biji kopi single-origin Ethiopia', 'MER-BEAN-047', 65000.00, 74, 5, 'products/47_biji_kopi.jpg', 1, '2026-02-13 21:39:23', '2026-02-13 17:47:59'),
(48, 5, 'Dripper Keramik', 'Set pour-over keramik + filter stainless', 'MER-DRIP-048', 165000.00, 30, 5, 'products/48_dripper_keramik.jpg', 1, '2026-02-13 21:39:23', '2026-02-13 21:39:23'),
(49, 5, 'Mug Keramik', 'Mug keramik handmade 350ml', 'MER-MUG-049', 75000.00, 59, 5, 'products/49_mug_keramik.jpg', 1, '2026-02-13 21:39:23', '2026-02-16 14:34:43'),
(50, 5, 'Apron Barista', 'Apron katun dengan saku', 'MER-APRON-050', 100000.00, 40, 5, 'products/50_apron_barista.jpg', 1, '2026-02-13 21:39:23', '2026-02-13 21:39:23'),
(51, 4, 'Pancong', 'Kue pancong dengan taburan gula', 'SNK-PCG-051', 25000.00, 47, 5, 'products/51_pancong.jpg', 1, '2026-02-13 18:25:09', '2026-02-13 18:25:09'),
(52, 5, 'Keychain', 'Gantungan kunci Lets Go For a Coffee', 'MER-KCN-052', 20000.00, 9, 5, 'products/52_keychain.jpg', 1, '2026-02-13 18:37:06', '2026-02-13 18:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_code` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed','cancelled') NOT NULL DEFAULT 'completed',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_code`, `user_id`, `customer_name`, `total_amount`, `payment_method`, `payment_amount`, `payment_method_id`, `payment_reference`, `change_amount`, `status`, `transaction_date`, `created_at`, `updated_at`) VALUES
(11, 'TRX-20260213-TFDHX7', 6, 'Rodi', 45000.00, 'cash', 50000.00, NULL, NULL, 5000.00, 'completed', '2026-02-13 15:13:39', '2026-02-13 15:13:39', '2026-02-13 15:13:39'),
(12, 'TRX-20260213-WADDI6', 6, 'Ryo', 42000.00, 'qris', 42000.00, 2, 'QRIS-20260213233639-2IXUNDXX', 0.00, 'completed', '2026-02-13 16:36:39', '2026-02-13 16:36:39', '2026-02-13 16:36:39'),
(13, 'TRX-20260213-TIZBDW', 6, 'Yandi', 38000.00, 'cash', 100000.00, 1, NULL, 62000.00, 'completed', '2026-02-13 16:37:44', '2026-02-13 16:37:44', '2026-02-13 16:37:44'),
(14, 'TRX-20260214-O77PYS', 6, 'Rey', 35000.00, 'qris', 35000.00, 2, 'QRIS-20260214001244-92DJ7S2S', 0.00, 'completed', '2026-02-13 17:12:44', '2026-02-13 17:12:44', '2026-02-13 17:12:44'),
(15, 'TRX-20260214-FLUQXC', 6, 'Rhino', 18000.00, 'qris', 18000.00, 2, 'QRIS-20260214001736-P8P03IKE', 0.00, 'completed', '2026-02-13 17:17:36', '2026-02-13 17:17:36', '2026-02-13 17:17:36'),
(16, 'TRX-20260214-CWSBLH', 6, 'Ara', 35000.00, 'qris', 35000.00, 2, 'QRIS-20260214001934-QJQUSCPO', 0.00, 'completed', '2026-02-13 17:19:34', '2026-02-13 17:19:34', '2026-02-13 17:19:34'),
(17, 'TRX-20260214-DG2DAC', 6, 'Erika', 40000.00, 'qris', 40000.00, 2, 'QRIS-20260214002348-0EFW89ZZ', 0.00, 'completed', '2026-02-13 17:23:48', '2026-02-13 17:23:48', '2026-02-13 17:23:48'),
(18, 'TRX-20260214-RXCDHI', 6, 'Ana', 18000.00, 'qris', 18000.00, 2, 'QRIS-20260214002628-ZAGNKIYA', 0.00, 'completed', '2026-02-13 17:26:28', '2026-02-13 17:26:28', '2026-02-13 17:26:28'),
(19, 'TRX-20260214-Q7TBGC', 6, 'Heni', 18000.00, 'BRI', 18000.00, 5, '00200311180005137', 0.00, 'completed', '2026-02-13 17:31:11', '2026-02-13 17:31:11', '2026-02-13 17:31:11'),
(20, 'TRX-20260214-QI5IQM', 6, 'Era', 18000.00, 'DANA', 18000.00, 7, 'DANA-20260214003127-VUPVZ1', 0.00, 'completed', '2026-02-13 17:31:27', '2026-02-13 17:31:27', '2026-02-13 17:31:27'),
(21, 'TRX-20260214-MLBPMP', 6, 'Zeni', 65000.00, 'BRI', 65000.00, 5, '00200431350002538', 0.00, 'completed', '2026-02-14 00:47:59', '2026-02-13 17:43:13', '2026-02-13 17:47:59'),
(22, 'TRX-20260214-KML8SZ', 6, 'Ita', 85000.00, 'BRI', 85000.00, 5, '00200432750005352', 0.00, 'completed', '2026-02-14 00:45:03', '2026-02-13 17:43:27', '2026-02-13 17:45:03'),
(23, 'TRX-20260214-OBUKTX', 6, 'Zizi', 18000.00, 'Mandiri', 18000.00, 4, '00821353680006868', 0.00, 'pending', '2026-02-14 14:35:36', '2026-02-14 14:35:36', '2026-02-14 14:35:36'),
(24, 'TRX-20260214-NG4JKY', 6, 'Roy', 18000.00, 'DANA', 18000.00, 7, 'DANA-20260214213627-P9T3B4', 0.00, 'cancelled', '2026-02-14 21:36:44', '2026-02-14 14:36:27', '2026-02-14 14:36:44'),
(25, 'TRX-20260214-PPNMHZ', 6, 'Erna', 35000.00, 'QRIS', 35000.00, 2, 'QRIS-20260214221044-QRI7CMOS', 0.00, 'completed', '2026-02-14 22:10:51', '2026-02-14 15:10:44', '2026-02-14 15:10:51'),
(26, 'TRX-20260215-4OBGNK', 6, 'Guest', 75000.00, 'BNI', 75000.00, 6, '00902540050006309', 0.00, 'completed', '2026-02-15 02:54:06', '2026-02-14 19:54:00', '2026-02-14 19:54:06'),
(27, 'TRX-20260216-DF0SLR', 6, 'Guest', 986000.00, 'Cash', 1000000.00, 1, NULL, 14000.00, 'completed', '2026-02-16 14:31:02', '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(28, 'TRX-20260216-PWNAWN', 6, 'Guest', 866000.00, 'QRIS', 866000.00, 2, 'QRIS-20260216213135-ZQ0W2GZH', 0.00, 'completed', '2026-02-16 21:31:50', '2026-02-16 14:31:35', '2026-02-16 14:31:50'),
(29, 'TRX-20260216-TZEX8F', 6, 'Senda', 395000.00, 'QRIS', 395000.00, 2, 'QRIS-20260216213321-ZSEAWJDK', 0.00, 'completed', '2026-02-16 21:34:43', '2026-02-16 14:33:21', '2026-02-16 14:34:43'),
(30, 'TRX-20260216-CGXGSI', 6, 'Nonik', 211000.00, 'OVO', 211000.00, 8, 'OVO-20260216222935-8OL2SS', 0.00, 'completed', '2026-02-16 22:30:43', '2026-02-16 15:29:35', '2026-02-16 15:30:43');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(15,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `product_name`, `product_price`, `quantity`, `subtotal`, `created_at`, `updated_at`) VALUES
(13, 11, 39, 'Avocado Toast', 45000.00, 1, 45000.00, '2026-02-13 15:13:39', '2026-02-13 15:13:39'),
(14, 12, 40, 'Veggie Wrap', 42000.00, 1, 42000.00, '2026-02-13 16:36:39', '2026-02-13 16:36:39'),
(15, 13, 41, 'Quiche Lorraine', 38000.00, 1, 38000.00, '2026-02-13 16:37:44', '2026-02-13 16:37:44'),
(16, 14, 45, 'Egg Sandwich', 35000.00, 1, 35000.00, '2026-02-13 17:12:44', '2026-02-13 17:12:44'),
(17, 15, 43, 'Energy Ball', 18000.00, 1, 18000.00, '2026-02-13 17:17:36', '2026-02-13 17:17:36'),
(18, 16, 42, 'Hummus & Pita', 35000.00, 1, 35000.00, '2026-02-13 17:19:34', '2026-02-13 17:19:34'),
(19, 17, 44, 'Chicken Sandwich', 40000.00, 1, 40000.00, '2026-02-13 17:23:48', '2026-02-13 17:23:48'),
(20, 18, 26, 'Croissant', 18000.00, 1, 18000.00, '2026-02-13 17:26:28', '2026-02-13 17:26:28'),
(21, 19, 27, 'Blueberry Muffin', 18000.00, 1, 18000.00, '2026-02-13 17:31:11', '2026-02-13 17:31:11'),
(22, 20, 26, 'Croissant', 18000.00, 1, 18000.00, '2026-02-13 17:31:27', '2026-02-13 17:31:27'),
(23, 21, 47, 'Biji Kopi 250gr', 65000.00, 1, 65000.00, '2026-02-13 17:43:13', '2026-02-13 17:43:13'),
(24, 22, 46, 'Tumbler Kopi', 85000.00, 1, 85000.00, '2026-02-13 17:43:27', '2026-02-13 17:43:27'),
(25, 23, 43, 'Energy Ball', 18000.00, 1, 18000.00, '2026-02-14 14:35:36', '2026-02-14 14:35:36'),
(26, 24, 26, 'Croissant', 18000.00, 1, 18000.00, '2026-02-14 14:36:27', '2026-02-14 14:36:27'),
(27, 25, 42, 'Hummus & Pita', 35000.00, 1, 35000.00, '2026-02-14 15:10:44', '2026-02-14 15:10:44'),
(28, 26, 35, 'Almond Biscotti', 18000.00, 1, 18000.00, '2026-02-14 19:54:00', '2026-02-14 19:54:00'),
(29, 26, 13, 'Espresso Tonic', 30000.00, 1, 30000.00, '2026-02-14 19:54:00', '2026-02-14 19:54:00'),
(30, 26, 14, 'Cortado', 27000.00, 1, 27000.00, '2026-02-14 19:54:00', '2026-02-14 19:54:00'),
(31, 27, 39, 'Avocado Toast', 45000.00, 4, 180000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(32, 27, 40, 'Veggie Wrap', 42000.00, 2, 84000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(33, 27, 41, 'Quiche Lorraine', 38000.00, 4, 152000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(34, 27, 43, 'Energy Ball', 18000.00, 1, 18000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(35, 27, 42, 'Hummus & Pita', 35000.00, 1, 35000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(36, 27, 44, 'Chicken Sandwich', 40000.00, 1, 40000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(37, 27, 45, 'Egg Sandwich', 35000.00, 1, 35000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(38, 27, 8, 'Iced Latte', 32000.00, 1, 32000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(39, 27, 7, 'Cold Brew', 30000.00, 1, 30000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(40, 27, 6, 'Macchiato', 27000.00, 1, 27000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(41, 27, 5, 'Flat White', 30000.00, 1, 30000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(42, 27, 4, 'Mocha', 32000.00, 1, 32000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(43, 27, 3, 'Latte', 30000.00, 1, 30000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(44, 27, 2, 'Cappuccino', 28000.00, 1, 28000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(45, 27, 1, 'Americano', 25000.00, 1, 25000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(46, 27, 23, 'Oolong Tea', 26000.00, 1, 26000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(47, 27, 22, 'Hibiscus Tea', 24000.00, 3, 72000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(48, 27, 21, 'Peppermint Tea', 22000.00, 2, 44000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(49, 27, 10, 'French Press', 33000.00, 2, 66000.00, '2026-02-16 14:31:02', '2026-02-16 14:31:02'),
(50, 28, 9, 'Pour Over', 35000.00, 6, 210000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(51, 28, 8, 'Iced Latte', 32000.00, 1, 32000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(52, 28, 7, 'Cold Brew', 30000.00, 2, 60000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(53, 28, 6, 'Macchiato', 27000.00, 1, 27000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(54, 28, 5, 'Flat White', 30000.00, 3, 90000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(55, 28, 4, 'Mocha', 32000.00, 1, 32000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(56, 28, 3, 'Latte', 30000.00, 2, 60000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(57, 28, 2, 'Cappuccino', 28000.00, 1, 28000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(58, 28, 1, 'Americano', 25000.00, 4, 100000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(59, 28, 23, 'Oolong Tea', 26000.00, 1, 26000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(60, 28, 21, 'Peppermint Tea', 22000.00, 1, 22000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(61, 28, 18, 'Chamomile Tea', 22000.00, 2, 44000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(62, 28, 17, 'Green Tea', 20000.00, 4, 80000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(63, 28, 16, 'Earl Grey', 22000.00, 1, 22000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(64, 28, 15, 'Caramel Macchiato', 33000.00, 1, 33000.00, '2026-02-16 14:31:35', '2026-02-16 14:31:35'),
(65, 29, 39, 'Avocado Toast', 45000.00, 1, 45000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(66, 29, 40, 'Veggie Wrap', 42000.00, 1, 42000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(67, 29, 41, 'Quiche Lorraine', 38000.00, 1, 38000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(68, 29, 42, 'Hummus & Pita', 35000.00, 2, 70000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(69, 29, 44, 'Chicken Sandwich', 40000.00, 1, 40000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(70, 29, 46, 'Tumbler Kopi', 85000.00, 1, 85000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(71, 29, 49, 'Mug Keramik', 75000.00, 1, 75000.00, '2026-02-16 14:33:21', '2026-02-16 14:33:21'),
(72, 30, 43, 'Energy Ball', 18000.00, 2, 36000.00, '2026-02-16 15:29:35', '2026-02-16 15:29:35'),
(73, 30, 45, 'Egg Sandwich', 35000.00, 5, 175000.00, '2026-02-16 15:29:35', '2026-02-16 15:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('owner','admin','kasir') NOT NULL DEFAULT 'kasir',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Budi', 'budi@mail.com', NULL, '$2y$12$KLmF5atWW1MCkH69ES9tIOFh8Wu.b5BFiFxTadFc7wzq6reIHvsBq', 'kasir', 1, NULL, '2026-01-30 05:40:30', '2026-01-30 05:40:30'),
(2, 'Rafli', 'rafli@mail.com', NULL, '$2y$12$gbSCT7mrerpH7H/JRjUvB.1cyP2D3nrJwTbxt0CprEhbXlnJlpgDy', 'owner', 1, NULL, '2026-01-31 06:02:31', '2026-01-31 06:02:31'),
(3, 'Erlangga', 'erlangga@mail.com', NULL, '$2y$12$9rc7fJyAyxcruKP4LYLkXeChqFs4lYU.eyOaOSxVpi3CY1bixqKG.', 'admin', 1, NULL, '2026-01-31 06:02:58', '2026-01-31 06:02:58'),
(4, 'Rafli Erlangga', 'owner@mail.com', NULL, '$2y$12$g4b7hQKZkv/XjT6EtuOgMuBajOU4NlmfCa5gLAGLwBWbx3jF64E9W', 'owner', 1, NULL, '2026-01-31 19:39:44', '2026-01-31 19:39:44'),
(5, 'Erlangga Rafli', 'admin@mail.com', NULL, '$2y$12$MrFnfEGT7NBknkdOnXAhz.CmufwLNJTVkToKseZRLoq1WNDmaTV/y', 'admin', 1, NULL, '2026-02-07 12:58:00', '2026-02-07 12:58:00'),
(6, 'Nasir', 'kasir@mail.com', NULL, '$2y$12$Xi53PVYHlHzTwO38vIz5C.C1Iw0RwCIU5/wk3/bdkqZfb3H4bW/4K', 'kasir', 1, NULL, '2026-02-07 13:01:01', '2026-02-07 13:01:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_methods_code_unique` (`code`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_transaction_code_unique` (`transaction_code`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_details_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
