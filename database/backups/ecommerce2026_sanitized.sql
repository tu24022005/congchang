-- BẢN SAO DATABASE ĐÃ LỌC DỮ LIỆU NHẠY CẢM
-- Giữ schema toàn bộ bảng; chỉ kèm dữ liệu catalog/nội dung công khai và cấu hình quyền.
-- Không chứa users, địa chỉ, đơn hàng, giỏ hàng, tin nhắn, token, log, newsletter hoặc thông tin thanh toán.

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: ecommerce2026
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `description` text NOT NULL,
  `before` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`before`)),
  `after` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`after`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_actor_id_foreign` (`actor_id`),
  KEY `activity_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_logs_action_index` (`action`),
  CONSTRAINT `activity_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT 'Nhà riêng',
  `recipient_name` varchar(120) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(500) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_user_id_is_default_index` (`user_id`,`is_default`),
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_name_unique` (`name`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `variation_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `saved_for_later` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_product_id_variation_id_unique` (`cart_id`,`product_id`,`variation_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  KEY `cart_items_variation_id_foreign` (`variation_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_user_id_unique` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `home_banners`
--

DROP TABLE IF EXISTS `home_banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `home_banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `badge` varchar(100) DEFAULT NULL,
  `title` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `video_url` varchar(2048) DEFAULT NULL,
  `video_autoplay` tinyint(1) NOT NULL DEFAULT 1,
  `video_loop` tinyint(1) NOT NULL DEFAULT 1,
  `video_muted` tinyint(1) NOT NULL DEFAULT 1,
  `alt_text` varchar(180) DEFAULT NULL,
  `button_text` varchar(80) DEFAULT NULL,
  `button_url` varchar(255) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_logs`
--

DROP TABLE IF EXISTS `inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_variation_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `stock_before` int(10) unsigned NOT NULL,
  `stock_after` int(10) unsigned NOT NULL,
  `reason` varchar(255) NOT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_logs_product_id_foreign` (`product_id`),
  KEY `inventory_logs_user_id_foreign` (`user_id`),
  KEY `inventory_logs_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  KEY `inventory_logs_product_variation_id_created_at_index` (`product_variation_id`,`created_at`),
  KEY `inventory_logs_type_created_at_index` (`type`,`created_at`),
  CONSTRAINT `inventory_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_logs_product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loyalty_point_transactions`
--

DROP TABLE IF EXISTS `loyalty_point_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loyalty_point_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `points` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loyalty_point_transactions_user_id_order_id_unique` (`user_id`,`order_id`),
  KEY `loyalty_point_transactions_order_id_foreign` (`order_id`),
  CONSTRAINT `loyalty_point_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loyalty_point_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `message` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_user_id_foreign` (`user_id`),
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_subscribers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_subscribers_email_unique` (`email`),
  UNIQUE KEY `newsletter_subscribers_token_unique` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_notifiable_type_notifiable_id_read_at_index` (`notifiable_type`,`notifiable_id`,`read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `variation_id` bigint(20) unsigned DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(20,2) DEFAULT NULL,
  `promotion_label` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  KEY `order_items_variation_id_foreign` (`variation_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_voucher_usages`
--

DROP TABLE IF EXISTS `order_voucher_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_voucher_usages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `voucher_id` bigint(20) unsigned NOT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_voucher_usages_order_id_voucher_id_unique` (`order_id`,`voucher_id`),
  KEY `order_voucher_usages_voucher_id_foreign` (`voucher_id`),
  CONSTRAINT `order_voucher_usages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_voucher_usages_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `total` decimal(20,2) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'processing',
  `received_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `refund_bank_name` varchar(255) DEFAULT NULL,
  `refund_bank_bin` varchar(20) DEFAULT NULL,
  `refund_account_number` varchar(255) DEFAULT NULL,
  `refund_account_holder` varchar(255) DEFAULT NULL,
  `refund_reference` varchar(255) DEFAULT NULL,
  `refund_note` text DEFAULT NULL,
  `refund_status` varchar(255) DEFAULT NULL,
  `refund_rejection_note` text DEFAULT NULL,
  `refund_reviewed_at` timestamp NULL DEFAULT NULL,
  `refund_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `refunded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(50) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `shipping_zone` varchar(40) DEFAULT NULL,
  `shipping_fee` int(10) unsigned NOT NULL DEFAULT 0,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `shipping_provider` varchar(255) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_refunded_by_foreign` (`refunded_by`),
  KEY `orders_refund_reviewed_by_foreign` (`refund_reviewed_by`),
  KEY `orders_tracking_number_index` (`tracking_number`),
  CONSTRAINT `orders_refund_reviewed_by_foreign` FOREIGN KEY (`refund_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_refunded_by_foreign` FOREIGN KEY (`refunded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `post_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_category_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`),
  KEY `posts_post_category_id_foreign` (`post_category_id`),
  KEY `posts_user_id_foreign` (`user_id`),
  KEY `posts_status_index` (`status`),
  KEY `posts_published_at_index` (`published_at`),
  CONSTRAINT `posts_post_category_id_foreign` FOREIGN KEY (`post_category_id`) REFERENCES `post_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_questions`
--

DROP TABLE IF EXISTS `product_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `answered_by` bigint(20) unsigned DEFAULT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_questions_product_id_foreign` (`product_id`),
  KEY `product_questions_user_id_foreign` (`user_id`),
  KEY `product_questions_answered_by_foreign` (`answered_by`),
  CONSTRAINT `product_questions_answered_by_foreign` FOREIGN KEY (`answered_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_questions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `order_id` bigint(20) unsigned DEFAULT NULL,
  `reviewer_name` varchar(255) DEFAULT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `comment` text DEFAULT NULL,
  `variant_label` varchar(255) DEFAULT NULL,
  `is_verified_purchase` tinyint(1) NOT NULL DEFAULT 0,
  `has_media` tinyint(1) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `media_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`media_paths`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_foreign` (`product_id`),
  KEY `product_reviews_user_id_foreign` (`user_id`),
  KEY `product_reviews_order_id_foreign` (`order_id`),
  CONSTRAINT `product_reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_variations`
--

DROP TABLE IF EXISTS `product_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `storage` varchar(255) DEFAULT NULL,
  `size_value` decimal(10,2) DEFAULT NULL,
  `size_unit` varchar(20) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_variations_sku_unique` (`sku`),
  KEY `product_variations_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `usage_instructions` text DEFAULT NULL,
  `skin_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skin_types`)),
  `expiry_info` varchar(255) DEFAULT NULL,
  `origin` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `flash_sale_price` decimal(20,2) DEFAULT NULL,
  `flash_sale_starts_at` datetime DEFAULT NULL,
  `flash_sale_ends_at` datetime DEFAULT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_helpful_votes`
--

DROP TABLE IF EXISTS `review_helpful_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_helpful_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `review_helpful_votes_review_id_user_id_unique` (`review_id`,`user_id`),
  KEY `review_helpful_votes_user_id_foreign` (`user_id`),
  CONSTRAINT `review_helpful_votes_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `product_reviews` (`id`) ON DELETE CASCADE,
  CONSTRAINT `review_helpful_votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_order_id_foreign` (`order_id`),
  CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL,
  `permission` varchar(100) NOT NULL,
  `allowed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_permission_unique` (`role`,`permission`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stock_alert_subscriptions`
--

DROP TABLE IF EXISTS `stock_alert_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_alert_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_alert_subscriptions_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `stock_alert_subscriptions_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_alert_subscriptions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_alert_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'customer',
  `loyalty_points` int(10) unsigned NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `login_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `login_locked_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voucher_user`
--

DROP TABLE IF EXISTS `voucher_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voucher_user` (
  `voucher_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `voucher_user_voucher_id_user_id_unique` (`voucher_id`,`user_id`),
  KEY `voucher_user_user_id_foreign` (`user_id`),
  CONSTRAINT `voucher_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voucher_user_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vouchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `scope` varchar(255) NOT NULL DEFAULT 'shop',
  `applies_to` varchar(255) NOT NULL DEFAULT 'all',
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `type` enum('fixed','percent','free_shipping') NOT NULL,
  `value` int(11) NOT NULL,
  `min_order_value` int(11) NOT NULL DEFAULT 0,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(10) unsigned NOT NULL DEFAULT 0,
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  KEY `vouchers_user_id_foreign` (`user_id`),
  KEY `vouchers_category_id_foreign` (`category_id`),
  KEY `vouchers_product_id_foreign` (`product_id`),
  CONSTRAINT `vouchers_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vouchers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `vouchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-12 19:37:50

-- DỮ LIỆU CATALOG VÀ CẤU HÌNH AN TOÀN

-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: ecommerce2026
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'CeraVe','cerave','2026-09-24 06:50:03','2026-09-24 06:50:03'),(2,'La Roche-Posay','la-roche-posay','2026-09-24 06:50:03','2026-09-24 06:50:03'),(3,'The Ordinary','the-ordinary','2026-09-24 06:50:03','2026-09-24 06:50:03'),(4,'Cocoon','cocoon','2026-09-24 06:50:03','2026-09-24 06:50:03');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'2026-08-27 06:58:33','2026-09-10 12:22:22','Chăm sóc da','categories/ZuHQkmCFryOmiy2gxBeGdEL1Cc6tydxMrh5yO2HD.jpg'),(2,'2026-08-27 06:58:33','2026-09-10 12:23:04','Trang điểm','categories/dgmAAJEuXWXo6UiF464qy3zyhnH5F6mm23cyh6qR.png'),(3,'2026-08-27 06:58:33','2026-09-10 12:23:37','Chăm sóc tóc','categories/O8tW90yUuMr8fx57bophjLD9pRBtXve1Hc4WNNhK.jpg'),(4,'2026-08-27 06:58:33','2026-09-10 16:13:53','Chăm sóc cá nhân','categories/R7xJpQfwwy1pXB9wdbZAcLBV5lMPL6XMP5mCRWGn.jpg'),(5,'2026-09-19 18:31:41','2026-10-01 05:58:07','Chăm sóc cơ thể','categories/jv2w9N8KPkAUXjj8O95zagr1qVMgsZem2iCdkqaB.jpg');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `home_banners`
--

LOCK TABLES `home_banners` WRITE;
/*!40000 ALTER TABLE `home_banners` DISABLE KEYS */;
INSERT INTO `home_banners` VALUES (1,'Bộ sưu tập mới','Beauty Essentials','Những sản phẩm làm đẹp thiết yếu giúp bạn chăm sóc làn da và tỏa sáng mỗi ngày.',NULL,'https://images.unsplash.com/photo-1596462502278-27bfdc403348?q=80&w=2000&auto=format&fit=crop',NULL,'https://www.youtube.com/watch?v=zHDECJy0p7k',1,1,1,'Bộ sưu tập mỹ phẩm Aloha Beauty','Mua ngay','/products',1,1,'2026-09-20 13:06:38','2026-10-01 18:07:57'),(2,'Được yêu thích','Skincare Ritual','Xây dựng chu trình chăm sóc da dịu lành, hiệu quả với những thành phần được chọn lọc.',NULL,'https://images.unsplash.com/photo-1556228578-8c89e6adf883?q=80&w=2000&auto=format&fit=crop',NULL,NULL,1,1,1,'Chăm sóc da','Khám phá ngay','/products?category=1',2,1,'2026-09-20 13:06:38','2026-09-20 13:06:38'),(3,'Ưu đãi hôm nay','Self-care Moment','Tận hưởng những phút giây chăm sóc bản thân với sản phẩm lành tính và tiện dụng.',NULL,'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?q=80&w=2000&auto=format&fit=crop',NULL,NULL,1,1,1,'Chăm sóc cá nhân','Săn ưu đãi','/products',3,1,'2026-09-20 13:06:38','2026-09-20 13:06:38'),(4,'Hair care edit','Tóc mềm, mood xinh','Khám phá routine chăm sóc tóc nhẹ nhàng, thơm dịu và phù hợp cho mỗi ngày.',NULL,'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?q=80&w=2000&auto=format&fit=crop',NULL,NULL,1,1,1,'Chăm sóc tóc','Xem chăm sóc tóc','/products?category=3',4,1,'2026-09-20 13:06:38','2026-09-20 13:06:38'),(5,'Routine mới mỗi ngày','Chăm mình thật dịu','Chọn những món nhỏ xinh để biến vài phút skincare thành khoảng thời gian dành riêng cho bạn.',NULL,'https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=2000&auto=format&fit=crop',NULL,NULL,1,1,1,'Routine chăm sóc da','Khám phá bộ sưu tập','/products',5,1,'2026-09-20 13:06:38','2026-09-20 13:06:38');
/*!40000 ALTER TABLE `home_banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `post_categories`
--

LOCK TABLES `post_categories` WRITE;
/*!40000 ALTER TABLE `post_categories` DISABLE KEYS */;
INSERT INTO `post_categories` VALUES (1,'Chăm sóc da','cham-soc-da','Kiến thức xây dựng quy trình skincare phù hợp.','2026-09-19 15:49:30','2026-09-19 15:49:30'),(2,'Trang điểm','trang-diem','Mẹo trang điểm tự nhiên, lâu trôi và dễ áp dụng.','2026-09-19 15:49:30','2026-09-19 15:49:30'),(3,'Chăm sóc tóc','cham-soc-toc','Bí quyết chăm sóc tóc chắc khỏe, mềm mượt.','2026-09-19 15:49:30','2026-09-19 15:49:30'),(4,'Lối sống đẹp','loi-song-dep','Thói quen lành mạnh giúp bạn tự tin và rạng rỡ hơn.','2026-09-19 15:49:30','2026-09-19 15:49:30');
/*!40000 ALTER TABLE `post_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (2,1,5,'Skincare routine buổi sáng 5 bước cho làn da tươi tỉnh','skincare-routine-buoi-sang-5-buoc-cho-lan-da-tuoi-tinh','Một quy trình buổi sáng đơn giản giúp làm sạch, cấp ẩm và bảo vệ da trước tác động của môi trường.','<h2>Vì sao routine buổi sáng nên tối giản?</h2><p>Buổi sáng, làn da cần được làm sạch nhẹ nhàng và bảo vệ thay vì sử dụng quá nhiều lớp dưỡng. Một routine đều đặn sẽ dễ duy trì hơn một quy trình dài nhưng thiếu nhất quán.</p><h3>5 bước gợi ý</h3><ol><li>Rửa mặt với sản phẩm dịu nhẹ.</li><li>Dùng toner hoặc lotion cấp ẩm nếu da cần.</li><li>Thoa serum theo nhu cầu như cấp ẩm hoặc chống oxy hóa.</li><li>Khóa ẩm bằng kem dưỡng mỏng nhẹ.</li><li>Thoa kem chống nắng đủ lượng và dặm lại khi cần.</li></ol><p>Hãy quan sát phản ứng của da trong 2 đến 4 tuần trước khi thêm sản phẩm mới.</p>','products/beauty-01.jpg','published','2026-09-12 02:00:00','Bí quyết đánh thức làn da khỏe đẹp sau khi thức dậy','Khám phá các bước chăm da đầu ngày dễ thực hiện, giúp da mềm mịn và sẵn sàng trước ánh nắng, bụi bẩn.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(3,1,5,'Cách chọn kem chống nắng phù hợp với từng loại da','cach-chon-kem-chong-nang-phu-hop-voi-tung-loai-da','Da dầu, da khô hay da nhạy cảm sẽ phù hợp với kết cấu kem chống nắng khác nhau.','<h2>Đọc bảng thành phần và chọn kết cấu</h2><p>Da dầu thường hợp với dạng gel, essence hoặc finish ráo. Da khô có thể ưu tiên công thức có chất dưỡng ẩm. Với da nhạy cảm, hãy thử trước trên vùng da nhỏ và chọn sản phẩm không có hương liệu nếu bạn dễ kích ứng.</p><h3>Ba điều không nên bỏ qua</h3><ul><li>Chọn chỉ số SPF từ 30 và có khả năng bảo vệ phổ rộng.</li><li>Thoa đủ lượng, khoảng hai đốt ngón tay cho mặt và cổ.</li><li>Dặm lại sau mỗi vài giờ khi hoạt động ngoài trời hoặc đổ mồ hôi.</li></ul><p>Kem chống nắng cần được dùng mỗi ngày, kể cả khi trời nhiều mây.</p>','products/beauty-02.jpg','published','2026-09-13 03:00:00','Giải mã lựa chọn chống nắng cho làn da của bạn','So sánh kết cấu và lưu ý thành phần để tìm sản phẩm bảo vệ da thoải mái, không bí bết hay khô căng.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(4,1,5,'Phân biệt da thiếu nước và da khô: Đừng dưỡng sai cách','phan-biet-da-thieu-nuoc-va-da-kho-dung-duong-sai-cach','Da khô là một loại da, còn thiếu nước là tình trạng có thể xuất hiện ở bất kỳ loại da nào.','<h2>Da khô khác da thiếu nước như thế nào?</h2><p>Da khô thường thiếu dầu tự nhiên, dễ bong tróc và có cảm giác căng. Da thiếu nước lại thiếu độ ẩm, có thể tiết dầu nhiều hơn ở vùng chữ T nhưng vẫn căng sau khi rửa mặt.</p><h3>Routine hỗ trợ</h3><p>Hãy ưu tiên sữa rửa mặt dịu nhẹ, lớp dưỡng ẩm có humectant như glycerin hoặc hyaluronic acid, sau đó khóa ẩm bằng kem dưỡng phù hợp. Đừng quên giảm tần suất tẩy tế bào chết nếu da đang nhạy cảm.</p>','products/beauty-03.jpg','published','2026-09-14 04:00:00','Da căng rát hay bóng dầu: Tìm đúng nguyên nhân','Nhận diện dấu hiệu hàng rào ẩm mất cân bằng và điều chỉnh các bước dưỡng da để gương mặt dễ chịu hơn.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(5,2,5,'Mẹo trang điểm tự nhiên cho người mới bắt đầu','meo-trang-diem-tu-nhien-cho-nguoi-moi-bat-dau','Bắt đầu từ lớp nền mỏng, màu sắc hài hòa và những sản phẩm dễ tán để có diện mạo trong trẻo.','<h2>Trang điểm đẹp bắt đầu từ nền da</h2><p>Trước khi makeup, hãy dưỡng ẩm vừa đủ và chờ sản phẩm thấm. Lớp nền mỏng sẽ tự nhiên hơn khi da được chuẩn bị tốt.</p><ol><li>Dùng kem chống nắng và kem lót nếu cần.</li><li>Chấm kem nền ở vùng trung tâm rồi tán mỏng ra ngoài.</li><li>Dùng che khuyết điểm vừa đủ ở quầng thâm hoặc nốt mụn.</li><li>Thêm má hồng màu đào hoặc hồng đất để gương mặt có sức sống.</li><li>Chải mày, bấm mi và dùng son làm điểm nhấn.</li></ol><p>Ánh sáng tự nhiên là cách tốt nhất để kiểm tra lớp nền trước khi ra ngoài.</p>','products/beauty-04.jpg','published','2026-09-15 02:00:00','Công thức makeup trong veo cho diện mạo hàng ngày','Gợi ý cách tạo vẻ ngoài tươi tắn với lớp nền nhẹ, màu má hài hòa và điểm nhấn vừa đủ cho người mới.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(6,2,5,'Cách giữ lớp nền lâu trôi trong ngày nóng','cach-giu-lop-nen-lau-troi-trong-ngay-nong','Chuẩn bị da, chọn kết cấu mỏng và cố định đúng cách sẽ giúp lớp nền bền đẹp hơn.','<h2>Chuẩn bị da trước khi makeup</h2><p>Đừng bỏ qua bước dưỡng ẩm, nhưng hãy chọn sản phẩm thấm nhanh và không quá dày. Sau kem chống nắng, nên đợi vài phút rồi mới bắt đầu lớp nền.</p><h3>Trình tự giúp nền bền hơn</h3><p>Chọn nền có độ bám phù hợp, tán từng lớp mỏng, phủ phấn nhẹ ở vùng dễ đổ dầu và xịt cố định sau cùng. Khi cần dặm, hãy dùng giấy thấm dầu trước rồi mới thêm sản phẩm để tránh nền bị dày.</p>','products/beauty-05.jpg','published','2026-09-16 03:00:00','Bí kíp makeup bền màu khi thời tiết oi nóng','Từ bước dưỡng da đến thao tác dặm lại, đây là những lưu ý giúp gương mặt tươi tắn lâu hơn trong ngày hè.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(7,3,5,'Routine chăm tóc cơ bản cho mái tóc khô xơ','routine-cham-toc-co-ban-cho-mai-toc-kho-xo','Tóc khô xơ cần được làm sạch vừa đủ, bổ sung độ ẩm và hạn chế nhiệt trong quá trình tạo kiểu.','<h2>Ba thói quen nên duy trì</h2><ul><li>Chọn dầu gội phù hợp da đầu, chỉ tập trung làm sạch ở chân tóc.</li><li>Thoa dầu xả từ thân đến ngọn, tránh bôi quá sát da đầu.</li><li>Dùng khăn mềm thấm nước, không chà xát mạnh khi tóc còn ướt.</li></ul><p>Mỗi tuần có thể dùng mặt nạ ủ tóc một lần và thoa một lượng nhỏ tinh dầu ở phần ngọn. Nếu thường xuyên dùng máy sấy hoặc máy tạo kiểu, hãy sử dụng sản phẩm bảo vệ tóc trước nhiệt.</p>','products/beauty-06.jpg','published','2026-09-17 04:00:00','Cứu mái tóc xơ rối bằng những bước chăm sóc tại nhà','Tìm hiểu cách làm sạch, dưỡng ngọn và giảm tác động từ nhiệt để tóc khô trở nên mềm mại, dễ chải hơn.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(8,4,5,'5 thói quen nhỏ giúp làn da trông rạng rỡ hơn','5-thoi-quen-nho-giup-lan-da-trong-rang-ro-hon','Làn da khỏe không chỉ đến từ mỹ phẩm mà còn từ giấc ngủ, nước uống và cách bạn chăm sóc bản thân.','<h2>Đẹp bền vững từ những điều đơn giản</h2><p>Hãy bắt đầu bằng việc ngủ đủ, uống nước theo nhu cầu cơ thể, ăn đa dạng thực phẩm và vận động nhẹ mỗi ngày. Khi căng thẳng kéo dài, da cũng có thể trở nên nhạy cảm hơn.</p><ol><li>Rửa mặt và tẩy trang phù hợp mỗi ngày.</li><li>Thoa kem chống nắng đều đặn.</li><li>Thay vỏ gối thường xuyên.</li><li>Không tự ý nặn mụn viêm.</li><li>Ghi lại phản ứng của da khi dùng sản phẩm mới.</li></ol>','products/beauty-07.jpg','published','2026-09-18 02:00:00','Nền tảng của vẻ đẹp bắt đầu từ sinh hoạt hàng ngày','Những việc nhỏ trong giấc ngủ, ăn uống, vệ sinh và vận động có thể tạo khác biệt tích cực cho diện mạo.','2026-09-19 15:49:30','2026-09-19 18:33:07'),(9,4,5,'Checklist vệ sinh mỹ phẩm để dùng an toàn hơn','checklist-ve-sinh-my-pham-de-dung-an-toan-hon','Vệ sinh cọ, đóng nắp sản phẩm và kiểm tra hạn dùng là những việc nhỏ nhưng rất quan trọng.','<h2>Đừng để dụng cụ makeup thành nơi tích tụ vi khuẩn</h2><p>Cọ và mút trang điểm nên được làm sạch định kỳ, phơi khô hoàn toàn ở nơi thoáng trước khi cất. Không dùng chung son, mascara hoặc cọ mắt với người khác.</p><h3>Kiểm tra sản phẩm định kỳ</h3><p>Hãy ghi lại ngày mở nắp, bảo quản mỹ phẩm tránh nắng nóng và đóng nắp ngay sau khi sử dụng. Nếu sản phẩm đổi mùi, đổi màu, tách lớp bất thường hoặc gây kích ứng, nên ngừng dùng.</p>','products/beauty-08.jpg','published','2026-09-19 03:00:00','Bảo quản đồ làm đẹp đúng cách để dùng lâu và an tâm','Các nguyên tắc làm sạch dụng cụ, đóng nắp và kiểm tra chất lượng giúp hạn chế nguy cơ kích ứng khi sử dụng mỹ phẩm.','2026-09-19 15:49:30','2026-09-19 18:33:07');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (63,1,'products/beauty-02.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(64,1,'products/beauty-03.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(65,1,'products/beauty-04.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(66,1,'products/beauty-05.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(67,2,'products/beauty-01.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(68,2,'products/beauty-03.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(69,2,'products/beauty-04.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(70,2,'products/beauty-05.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(71,3,'products/beauty-01.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(72,3,'products/beauty-02.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(73,3,'products/beauty-04.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(74,3,'products/beauty-05.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(75,4,'products/beauty-01.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(76,4,'products/beauty-02.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(77,4,'products/beauty-03.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(78,4,'products/beauty-05.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(79,5,'products/beauty-01.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(80,5,'products/beauty-02.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(81,5,'products/beauty-03.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(82,5,'products/beauty-04.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(83,6,'products/beauty-07.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(84,6,'products/beauty-08.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(85,6,'products/beauty-09.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(86,6,'products/beauty-10.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(87,9,'products/beauty-06.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(88,9,'products/beauty-07.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(89,9,'products/beauty-08.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(90,9,'products/beauty-10.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(91,10,'products/beauty-06.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(92,10,'products/beauty-07.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(93,10,'products/beauty-08.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(94,10,'products/beauty-09.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(95,11,'products/beauty-12.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(96,11,'products/beauty-13.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(97,11,'products/beauty-14.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(98,11,'products/beauty-15.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(99,12,'products/beauty-11.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(100,12,'products/beauty-13.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(101,12,'products/beauty-14.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(102,12,'products/beauty-15.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(103,13,'products/beauty-11.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(104,13,'products/beauty-12.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(105,13,'products/beauty-14.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(106,13,'products/beauty-15.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(107,14,'products/beauty-11.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(108,14,'products/beauty-12.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(109,14,'products/beauty-13.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(110,14,'products/beauty-15.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(111,15,'products/beauty-11.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(112,15,'products/beauty-12.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(113,15,'products/beauty-13.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(114,15,'products/beauty-14.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(115,16,'products/beauty-17.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(116,16,'products/beauty-18.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(117,16,'products/beauty-19.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(118,16,'products/beauty-20.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(119,17,'products/beauty-16.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(120,17,'products/beauty-18.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(121,17,'products/beauty-19.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(122,17,'products/beauty-20.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(123,18,'products/beauty-16.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(124,18,'products/beauty-17.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(125,18,'products/beauty-19.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(126,18,'products/beauty-20.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(127,19,'products/beauty-16.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(128,19,'products/beauty-17.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(129,19,'products/beauty-18.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(130,19,'products/beauty-20.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(131,20,'products/beauty-16.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(132,20,'products/beauty-17.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(133,20,'products/beauty-18.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08'),(134,20,'products/beauty-19.jpg','2026-09-10 13:31:08','2026-09-10 13:31:08');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
INSERT INTO `product_variations` VALUES (1,1,NULL,'ALOHA-01-01','Mặc định',NULL,30.00,'ml',2800,134,'2026-09-10 14:48:36','2026-09-27 20:34:33'),(2,1,NULL,'ALOHA-01-02','Mặc định',NULL,50.00,'ml',289000,50,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(3,2,NULL,'ALOHA-02-01','Mặc định',NULL,30.00,'ml',200,20,'2026-09-10 14:48:36','2026-09-19 16:36:26'),(4,2,NULL,'ALOHA-02-02','Mặc định',NULL,50.00,'ml',30000,21,'2026-09-10 14:48:36','2026-09-19 16:36:26'),(5,3,NULL,'ALOHA-03-01','Mặc định',NULL,100.00,'ml',189000,12,'2026-09-10 14:48:36','2026-09-24 12:33:28'),(6,3,NULL,'ALOHA-03-02','Mặc định',NULL,200.00,'ml',189000,15,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(7,4,NULL,'ALOHA-04-01','Mặc định',NULL,50.00,'g',249000,1,'2026-09-10 14:48:36','2026-09-19 17:19:21'),(8,4,NULL,'ALOHA-04-02','Mặc định',NULL,100.00,'g',249000,10,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(9,5,NULL,'ALOHA-05-01','Mặc định',NULL,250.00,'ml',219000,49,'2026-09-10 14:48:36','2026-09-11 02:39:25'),(10,5,NULL,'ALOHA-05-02','Mặc định',NULL,500.00,'ml',219000,50,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(11,6,NULL,'ALOHA-06-01','Đỏ D01',NULL,3.00,'g',219000,30,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(12,6,NULL,'ALOHA-06-02','Hồng D02',NULL,3.00,'g',219000,30,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(13,9,NULL,'ALOHA-09-01','01 Sáng',NULL,30.00,'ml',389000,20,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(14,9,NULL,'ALOHA-09-02','02 Tự nhiên',NULL,30.00,'ml',389000,20,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(15,10,NULL,'ALOHA-10-01','Peach',NULL,5.00,'g',199000,10,'2026-09-10 14:48:36','2026-09-24 12:27:12'),(16,10,NULL,'ALOHA-10-02','Coral',NULL,5.00,'g',199000,13,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(17,11,NULL,'ALOHA-11-01','Mặc định',NULL,250.00,'ml',279000,40,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(18,11,NULL,'ALOHA-11-02','Mặc định',NULL,500.00,'ml',279000,40,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(19,12,NULL,'ALOHA-12-01','Mặc định',NULL,250.00,'ml',259000,17,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(20,12,NULL,'ALOHA-12-02','Mặc định',NULL,500.00,'ml',259000,18,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(21,13,NULL,'ALOHA-13-01','Mặc định',NULL,30.00,'ml',319000,25,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(22,13,NULL,'ALOHA-13-02','Mặc định',NULL,50.00,'ml',319000,25,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(23,14,NULL,'ALOHA-14-01','Mặc định',NULL,250.00,'g',289000,60,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(24,14,NULL,'ALOHA-14-02','Mặc định',NULL,500.00,'g',289000,60,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(25,15,NULL,'ALOHA-15-01','Gỗ sáng',NULL,NULL,NULL,99000,15,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(26,15,NULL,'ALOHA-15-02','Gỗ nâu',NULL,NULL,NULL,99000,15,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(27,16,NULL,'ALOHA-16-01','Mặc định',NULL,250.00,'ml',179000,5,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(28,16,NULL,'ALOHA-16-02','Mặc định',NULL,500.00,'ml',179000,5,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(29,17,NULL,'ALOHA-17-01','Mặc định',NULL,200.00,'ml',229000,22,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(30,17,NULL,'ALOHA-17-02','Mặc định',NULL,400.00,'ml',229000,23,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(31,18,NULL,'ALOHA-18-01','Mặc định',NULL,200.00,'g',199000,14,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(32,18,NULL,'ALOHA-18-02','Mặc định',NULL,400.00,'g',199000,14,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(33,19,NULL,'ALOHA-19-01','Mặc định',NULL,50.00,'ml',129000,30,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(34,19,NULL,'ALOHA-19-02','Mặc định',NULL,75.00,'ml',129000,30,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(35,20,NULL,'ALOHA-20-01','Bộ tiêu chuẩn',NULL,NULL,NULL,249000,7,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(36,20,NULL,'ALOHA-20-02','Bộ mini',NULL,NULL,NULL,249000,8,'2026-09-10 14:48:36','2026-09-10 14:48:36'),(37,21,'products/catalog-02.jpg','SAMPLE-01-A','Mặc định',NULL,30.00,'ml',2000,0,'2026-09-19 18:31:41','2026-10-01 06:45:58'),(38,21,'products/catalog-03.jpg','SAMPLE-01-B','Phiên bản nâng cao',NULL,50.00,'ml',179000,0,'2026-09-19 18:31:41','2026-10-01 06:41:20'),(39,22,'products/catalog-03.jpg','SAMPLE-02-A','Mặc định',NULL,30.00,'ml',189000,27,'2026-09-19 18:31:41','2026-10-01 07:52:02'),(40,22,'products/catalog-04.jpg','SAMPLE-02-B','Phiên bản nâng cao',NULL,50.00,'ml',209000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(41,23,'products/catalog-04.jpg','SAMPLE-03-A','Mặc định',NULL,30.00,'ml',349000,16,'2026-09-19 18:31:41','2026-09-27 18:17:26'),(42,23,'products/catalog-05.jpg','SAMPLE-03-B','Phiên bản nâng cao',NULL,50.00,'ml',369000,28,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(43,24,'products/catalog-05.jpg','SAMPLE-04-A','Mặc định',NULL,30.00,'ml',269000,17,'2026-09-19 18:31:41','2026-10-01 07:52:02'),(44,24,'products/catalog-06.jpg','SAMPLE-04-B','Phiên bản nâng cao',NULL,50.00,'ml',289000,23,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(45,25,'products/catalog-06.jpg','SAMPLE-05-A','Mặc định',NULL,30.00,'ml',299000,31,'2026-09-19 18:31:41','2026-10-01 06:47:51'),(46,25,'products/catalog-07.jpg','SAMPLE-05-B','Phiên bản nâng cao',NULL,50.00,'ml',319000,33,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(47,26,'products/catalog-07.jpg','SAMPLE-06-A','Mặc định',NULL,30.00,'ml',319000,20,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(48,26,'products/catalog-08.jpg','SAMPLE-06-B','Phiên bản nâng cao',NULL,50.00,'ml',339000,20,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(49,27,'products/catalog-08.jpg','SAMPLE-07-A','Mặc định',NULL,30.00,'ml',129000,41,'2026-09-19 18:31:41','2026-09-27 18:16:14'),(50,27,'products/catalog-09.jpg','SAMPLE-07-B','Phiên bản nâng cao',NULL,50.00,'ml',149000,45,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(51,28,'products/catalog-09.jpg','SAMPLE-08-A','Mặc định',NULL,30.00,'ml',279000,15,'2026-09-19 18:31:41','2026-10-01 06:43:00'),(52,28,'products/catalog-10.jpg','SAMPLE-08-B','Phiên bản nâng cao',NULL,50.00,'ml',299000,15,'2026-09-19 18:31:41','2026-10-01 06:43:00'),(53,29,'products/catalog-10.jpg','SAMPLE-09-A','Mặc định',NULL,30.00,'ml',99000,50,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(54,29,'products/catalog-11.jpg','SAMPLE-09-B','Phiên bản nâng cao',NULL,50.00,'ml',119000,50,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(55,30,'products/catalog-11.jpg','SAMPLE-10-A','Mặc định',NULL,30.00,'ml',89000,60,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(56,30,'products/catalog-12.jpg','SAMPLE-10-B','Phiên bản nâng cao',NULL,50.00,'ml',109000,60,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(57,31,'products/catalog-12.jpg','SAMPLE-11-A','Mặc định',NULL,30.00,'ml',149000,37,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(58,31,'products/catalog-13.jpg','SAMPLE-11-B','Phiên bản nâng cao',NULL,50.00,'ml',169000,38,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(59,32,'products/catalog-13.jpg','SAMPLE-12-A','Mặc định',NULL,30.00,'ml',169000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(60,32,'products/catalog-14.jpg','SAMPLE-12-B','Phiên bản nâng cao',NULL,50.00,'ml',189000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(61,33,'products/catalog-14.jpg','SAMPLE-13-A','Mặc định',NULL,30.00,'ml',289000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(62,33,'products/catalog-15.jpg','SAMPLE-13-B','Phiên bản nâng cao',NULL,50.00,'ml',309000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(63,34,'products/catalog-15.jpg','SAMPLE-14-A','Mặc định',NULL,30.00,'ml',279000,22,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(64,34,'products/catalog-16.jpg','SAMPLE-14-B','Phiên bản nâng cao',NULL,50.00,'ml',299000,23,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(65,35,'products/catalog-16.jpg','SAMPLE-15-A','Mặc định',NULL,30.00,'ml',239000,30,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(66,35,'products/catalog-17.jpg','SAMPLE-15-B','Phiên bản nâng cao',NULL,50.00,'ml',259000,30,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(67,36,'products/catalog-17.jpg','SAMPLE-16-A','Mặc định',NULL,30.00,'ml',229000,40,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(68,36,'products/catalog-18.jpg','SAMPLE-16-B','Phiên bản nâng cao',NULL,50.00,'ml',249000,40,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(69,37,'products/catalog-18.jpg','SAMPLE-17-A','Mặc định',NULL,30.00,'ml',359000,17,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(70,37,'products/catalog-19.jpg','SAMPLE-17-B','Phiên bản nâng cao',NULL,50.00,'ml',379000,18,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(71,38,'products/catalog-19.jpg','SAMPLE-18-A','Mặc định',NULL,30.00,'ml',179000,32,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(72,38,'products/catalog-20.jpg','SAMPLE-18-B','Phiên bản nâng cao',NULL,50.00,'ml',199000,33,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(73,39,'products/catalog-20.jpg','SAMPLE-19-A','Mặc định',NULL,30.00,'ml',219000,27,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(74,39,'products/catalog-21.jpg','SAMPLE-19-B','Phiên bản nâng cao',NULL,50.00,'ml',239000,28,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(75,40,'products/catalog-21.jpg','SAMPLE-20-A','Mặc định',NULL,30.00,'ml',249000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(76,40,'products/catalog-22.jpg','SAMPLE-20-B','Phiên bản nâng cao',NULL,50.00,'ml',269000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(77,41,'products/catalog-22.jpg','SAMPLE-21-A','Mặc định',NULL,30.00,'ml',239000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(78,41,'products/catalog-23.jpg','SAMPLE-21-B','Phiên bản nâng cao',NULL,50.00,'ml',259000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(79,42,'products/catalog-23.jpg','SAMPLE-22-A','Mặc định',NULL,30.00,'ml',249000,32,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(80,42,'products/beauty-01.jpg','SAMPLE-22-B','Phiên bản nâng cao',NULL,50.00,'ml',269000,33,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(81,43,'products/beauty-01.jpg','SAMPLE-23-A','Mặc định',NULL,30.00,'ml',219000,30,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(82,43,'products/beauty-02.jpg','SAMPLE-23-B','Phiên bản nâng cao',NULL,50.00,'ml',239000,30,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(83,44,'products/beauty-02.jpg','SAMPLE-24-A','Mặc định',NULL,30.00,'ml',189000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(84,44,'products/beauty-03.jpg','SAMPLE-24-B','Phiên bản nâng cao',NULL,50.00,'ml',209000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(85,45,'products/beauty-03.jpg','SAMPLE-25-A','Mặc định',NULL,30.00,'ml',199000,22,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(86,45,'products/beauty-04.jpg','SAMPLE-25-B','Phiên bản nâng cao',NULL,50.00,'ml',219000,23,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(87,46,'products/beauty-04.jpg','SAMPLE-26-A','Mặc định',NULL,30.00,'ml',119000,45,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(88,46,'products/beauty-05.jpg','SAMPLE-26-B','Phiên bản nâng cao',NULL,50.00,'ml',139000,45,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(89,47,'products/beauty-05.jpg','SAMPLE-27-A','Mặc định',NULL,30.00,'ml',139000,27,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(90,47,'products/beauty-06.jpg','SAMPLE-27-B','Phiên bản nâng cao',NULL,50.00,'ml',159000,28,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(91,48,'products/beauty-06.jpg','SAMPLE-28-A','Mặc định',NULL,30.00,'ml',229000,24,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(92,48,'products/beauty-07.jpg','SAMPLE-28-B','Phiên bản nâng cao',NULL,50.00,'ml',249000,24,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(93,49,'products/beauty-07.jpg','SAMPLE-29-A','Mặc định',NULL,30.00,'ml',329000,20,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(94,49,'products/beauty-08.jpg','SAMPLE-29-B','Phiên bản nâng cao',NULL,50.00,'ml',349000,20,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(95,50,'products/beauty-08.jpg','SAMPLE-30-A','Mặc định',NULL,30.00,'ml',159000,37,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(96,50,'products/beauty-09.jpg','SAMPLE-30-B','Phiên bản nâng cao',NULL,50.00,'ml',179000,38,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(97,51,'products/beauty-09.jpg','SAMPLE-31-A','Mặc định',NULL,30.00,'ml',169000,42,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(98,51,'products/beauty-10.jpg','SAMPLE-31-B','Phiên bản nâng cao',NULL,50.00,'ml',189000,43,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(99,52,'products/beauty-10.jpg','SAMPLE-32-A','Mặc định',NULL,30.00,'ml',199000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(100,52,'products/beauty-11.jpg','SAMPLE-32-B','Phiên bản nâng cao',NULL,50.00,'ml',219000,25,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(101,53,'products/beauty-11.jpg','SAMPLE-33-A','Mặc định',NULL,30.00,'ml',259000,22,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(102,53,'products/beauty-12.jpg','SAMPLE-33-B','Phiên bản nâng cao',NULL,50.00,'ml',279000,23,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(103,54,'products/beauty-12.jpg','SAMPLE-34-A','Mặc định',NULL,30.00,'ml',109000,50,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(104,54,'products/beauty-13.jpg','SAMPLE-34-B','Phiên bản nâng cao',NULL,50.00,'ml',129000,50,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(105,55,'products/beauty-13.jpg','SAMPLE-35-A','Mặc định',NULL,30.00,'ml',79000,55,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(106,55,'products/beauty-14.jpg','SAMPLE-35-B','Phiên bản nâng cao',NULL,50.00,'ml',99000,55,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(107,56,'products/beauty-14.jpg','SAMPLE-36-A','Mặc định',NULL,30.00,'ml',129000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(108,56,'products/beauty-15.jpg','SAMPLE-36-B','Phiên bản nâng cao',NULL,50.00,'ml',149000,35,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(109,57,'products/beauty-15.jpg','SAMPLE-37-A','Mặc định',NULL,30.00,'ml',139000,32,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(110,57,'products/beauty-16.jpg','SAMPLE-37-B','Phiên bản nâng cao',NULL,50.00,'ml',159000,33,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(111,58,'products/beauty-16.jpg','SAMPLE-38-A','Mặc định',NULL,30.00,'ml',279000,17,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(112,58,'products/beauty-17.jpg','SAMPLE-38-B','Phiên bản nâng cao',NULL,50.00,'ml',299000,18,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(113,59,'products/beauty-17.jpg','SAMPLE-39-A','Mặc định',NULL,30.00,'ml',189000,22,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(114,59,'products/catalog-01.jpg','SAMPLE-39-B','Phiên bản nâng cao',NULL,50.00,'ml',209000,23,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(115,60,'products/catalog-01.jpg','SAMPLE-40-A','Mặc định',NULL,30.00,'ml',99000,50,'2026-09-19 18:31:41','2026-09-19 19:04:37'),(116,60,'products/catalog-02.jpg','SAMPLE-40-B','Phiên bản nâng cao',NULL,50.00,'ml',119000,50,'2026-09-19 18:31:41','2026-09-19 19:04:37');
/*!40000 ALTER TABLE `product_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,NULL,'Serum cấp ẩm Hyaluronic Aloha','serum-cap-am-hyaluronic-aloha','Serum cấp ẩm dịu nhẹ giúp làn da căng mịn và tươi sáng.',NULL,NULL,NULL,NULL,NULL,184,2800.00,NULL,NULL,NULL,1,NULL,'products/beauty-01.jpg','2026-08-27 06:58:33','2026-09-25 02:27:09'),(2,NULL,'Kem chống nắng Daily Shield SPF50+','kem-chong-nang-daily-shield-spf50','Kem chống nắng phổ rộng, mỏng nhẹ, không để lại vệt trắng trên da.',NULL,NULL,NULL,NULL,NULL,41,30000.00,NULL,NULL,NULL,1,NULL,'products/beauty-02.jpg','2026-08-27 06:58:33','2026-10-01 18:56:52'),(3,NULL,'Sữa rửa mặt trà xanh dịu nhẹ','sua-rua-mat-tra-xanh-diu-nhe','Làm sạch bụi bẩn và dầu thừa mà không gây khô căng da.',NULL,NULL,NULL,NULL,NULL,27,189000.00,NULL,NULL,NULL,1,NULL,'products/beauty-03.jpg','2026-08-27 06:58:33','2026-09-25 02:27:04'),(4,NULL,'Mặt nạ đất sét thanh lọc da','mat-na-dat-set-thanh-loc-da','Mặt nạ đất sét giúp làm sạch lỗ chân lông và cân bằng bã nhờn.',NULL,NULL,NULL,NULL,NULL,11,249000.00,NULL,NULL,NULL,1,NULL,'products/beauty-04.jpg','2026-08-27 06:58:33','2026-10-01 18:56:52'),(5,NULL,'Nước tẩy trang Micellar dịu êm','nuoc-tay-trang-micellar-diu-em','Tẩy sạch lớp trang điểm và bụi mịn với công thức không cồn.',NULL,NULL,NULL,NULL,NULL,99,219000.00,NULL,NULL,NULL,1,NULL,'products/beauty-05.jpg','2026-08-27 06:58:33','2026-10-01 18:56:52'),(6,NULL,'Son tint dưỡng môi Berry Glow','son-tint-duong-moi-berry-glow','Son tint màu trong trẻo, lâu trôi và bổ sung độ ẩm cho môi.',NULL,NULL,NULL,NULL,NULL,60,219000.00,NULL,NULL,NULL,2,NULL,'products/beauty-06.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(9,NULL,'Kem nền mỏng nhẹ Natural Skin','kem-nen-mong-nhe-natural-skin','Lớp nền mỏng nhẹ, tiệp da và giữ vẻ tự nhiên suốt ngày dài.',NULL,NULL,NULL,NULL,NULL,40,389000.00,NULL,NULL,NULL,2,NULL,'products/beauty-09.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(10,NULL,'Phấn má hồng Peach Blush','phan-ma-hong-peach-blush','Phấn má màu đào tươi tắn, dễ tán và phù hợp trang điểm hằng ngày.',NULL,NULL,NULL,NULL,NULL,23,199000.00,NULL,NULL,NULL,2,NULL,'products/beauty-10.jpg','2026-08-27 06:58:33','2026-10-01 18:56:52'),(11,NULL,'Dầu gội phục hồi tóc hư tổn','dau-goi-phuc-hoi-toc-hu-ton','Làm sạch dịu nhẹ và hỗ trợ phục hồi mái tóc khô xơ.',NULL,NULL,NULL,NULL,NULL,80,279000.00,NULL,NULL,NULL,3,NULL,'products/beauty-11.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(12,NULL,'Dầu xả mềm mượt Silk Care','dau-xa-mem-muot-silk-care','Nuôi dưỡng tóc mềm mượt, dễ chải và giảm rối.',NULL,NULL,NULL,NULL,NULL,35,259000.00,NULL,NULL,NULL,3,NULL,'products/beauty-12.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(13,NULL,'Tinh dầu dưỡng tóc Argan','tinh-dau-duong-toc-argan','Tinh dầu Argan giúp tóc bóng khỏe và hạn chế chẻ ngọn.',NULL,NULL,NULL,NULL,NULL,50,319000.00,NULL,NULL,NULL,3,NULL,'products/beauty-13.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(14,NULL,'Mặt nạ ủ tóc Keratin','mat-na-u-toc-keratin','Mặt nạ ủ chuyên sâu cho mái tóc cần được chăm sóc và phục hồi.',NULL,NULL,NULL,NULL,NULL,120,289000.00,NULL,NULL,NULL,3,NULL,'products/beauty-14.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(15,NULL,'Lược gỗ massage da đầu','luoc-go-massage-da-dau','Lược gỗ thân thiện giúp massage da đầu thư giãn mỗi ngày.',NULL,NULL,NULL,NULL,NULL,30,99000.00,NULL,NULL,NULL,3,NULL,'products/beauty-15.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(16,NULL,'Sữa tắm hoa anh đào Soft Bloom','sua-tam-hoa-anh-dao-soft-bloom','Sữa tắm làm sạch dịu nhẹ với hương hoa thư giãn và lưu hương mềm mại.',NULL,NULL,NULL,NULL,NULL,10,179000.00,NULL,NULL,NULL,4,NULL,'products/beauty-16.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(17,NULL,'Kem dưỡng thể Vitamin E','kem-duong-the-vitamin-e','Dưỡng ẩm cho làn da cơ thể mềm mại và mịn màng.',NULL,NULL,NULL,NULL,NULL,45,229000.00,NULL,NULL,NULL,4,NULL,'products/beauty-17.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(18,NULL,'Tẩy tế bào chết cà phê','tay-te-bao-chet-ca-phe','Hạt cà phê mịn giúp làm sạch da chết và mang lại cảm giác thư giãn.',NULL,NULL,NULL,NULL,NULL,28,199000.00,NULL,NULL,NULL,4,NULL,'products/beauty-18.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(19,NULL,'Lăn khử mùi Cotton Fresh','lan-khu-mui-cotton-fresh','Khử mùi nhẹ nhàng, khô thoáng với hương cotton sạch sẽ.',NULL,NULL,NULL,NULL,NULL,60,129000.00,NULL,NULL,NULL,4,NULL,'products/beauty-19.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(20,NULL,'Bộ chăm sóc cá nhân Travel Kit','bo-cham-soc-ca-nhan-travel-kit','Bộ sản phẩm nhỏ gọn tiện lợi cho những chuyến đi và ngày bận rộn.',NULL,NULL,NULL,NULL,NULL,15,249000.00,NULL,NULL,NULL,4,NULL,'products/beauty-20.jpg','2026-08-27 06:58:33','2026-09-20 13:28:00'),(21,NULL,'Sữa rửa mặt Amino dịu nhẹ','sua-rua-mat-amino-diu-nhe','Làm sạch sâu nhưng vẫn giữ lại độ ẩm tự nhiên cho da.',NULL,NULL,NULL,NULL,NULL,0,2000.00,1000.00,'2026-09-24 08:30:00','2026-09-24 08:35:00',1,1,'products/catalog-01.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(22,NULL,'Toner cân bằng hoa cúc','toner-can-bang-hoa-cuc','Nước cân bằng dịu nhẹ giúp da sẵn sàng cho các bước dưỡng tiếp theo.',NULL,NULL,NULL,NULL,NULL,62,189000.00,1000.00,'2026-09-20 20:34:00','2026-09-21 12:34:00',1,NULL,'products/catalog-02.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(23,NULL,'Kem dưỡng phục hồi Ceramide','kem-duong-phuc-hoi-ceramide','Công thức ceramide hỗ trợ hàng rào bảo vệ và làn da khô nhạy cảm.',NULL,NULL,NULL,NULL,NULL,44,349000.00,2000.00,'2026-09-24 12:01:00','2026-09-25 12:01:00',1,NULL,'products/catalog-03.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(24,NULL,'Mặt nạ ngủ Vitamin B5','mat-na-ngu-vitamin-b5','Mặt nạ ngủ cấp ẩm và làm dịu da sau một ngày dài.',NULL,NULL,NULL,NULL,NULL,40,269000.00,NULL,NULL,NULL,1,NULL,'products/catalog-04.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(25,NULL,'Tinh chất Niacinamide 10%','tinh-chat-niacinamide-10','Tinh chất hỗ trợ cải thiện vẻ ngoài của lỗ chân lông và sắc da.',NULL,NULL,NULL,NULL,NULL,64,299000.00,NULL,NULL,NULL,1,NULL,'products/catalog-05.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(26,NULL,'Kem mắt Caffeine sáng mịn','kem-mat-caffeine-sang-min','Chăm sóc vùng da quanh mắt với caffeine và peptide nhẹ nhàng.',NULL,NULL,NULL,NULL,NULL,40,319000.00,NULL,NULL,NULL,1,NULL,'products/catalog-06.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(27,NULL,'Xịt khoáng trà xanh','xit-khoang-tra-xanh','Xịt khoáng tươi mát, tiện dụng cho làn da trong ngày.',NULL,NULL,NULL,NULL,NULL,86,129000.00,NULL,NULL,NULL,1,NULL,'products/catalog-07.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(28,NULL,'Dầu tẩy trang hoa trà','dau-tay-trang-hoa-tra','Dầu tẩy trang nhũ hóa nhanh, làm sạch lớp trang điểm và kem chống nắng.',NULL,NULL,NULL,NULL,NULL,30,279000.00,NULL,NULL,NULL,1,NULL,'products/catalog-08.jpg','2026-09-19 18:31:41','2026-10-01 18:56:52'),(29,NULL,'Son dưỡng bơ hạt mỡ','son-duong-bo-hat-mo','Son dưỡng không màu giúp đôi môi mềm mại và dễ chịu.',NULL,NULL,NULL,NULL,NULL,100,99000.00,NULL,NULL,NULL,1,NULL,'products/catalog-09.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(30,NULL,'Miếng dán mụn tràm trà','mieng-dan-mun-tram-tra','Miếng dán mỏng giúp bảo vệ nốt mụn trong sinh hoạt hằng ngày.',NULL,NULL,NULL,NULL,NULL,120,89000.00,NULL,NULL,NULL,1,NULL,'products/catalog-10.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(31,NULL,'Chì kẻ mày lâu trôi Brown Taupe','chi-ke-may-lau-troi-brown-taupe','Chì kẻ mày đầu mảnh, dễ tạo dáng tự nhiên và lâu trôi.',NULL,NULL,NULL,NULL,NULL,75,149000.00,NULL,NULL,NULL,2,NULL,'products/catalog-11.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(32,NULL,'Bút kẻ mắt nước Espresso','but-ke-mat-nuoc-espresso','Đường kẻ sắc nét với đầu bút linh hoạt, khô nhanh.',NULL,NULL,NULL,NULL,NULL,70,169000.00,NULL,NULL,NULL,2,NULL,'products/catalog-12.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(33,NULL,'Phấn phủ kiềm dầu Soft Matte','phan-phu-kiem-dau-soft-matte','Phấn phủ mịn nhẹ giúp lớp nền bền đẹp và hạn chế bóng dầu.',NULL,NULL,NULL,NULL,NULL,50,289000.00,NULL,NULL,NULL,2,NULL,'products/catalog-13.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(34,NULL,'Kem lót cấp ẩm Glow Base','kem-lot-cap-am-glow-base','Kem lót tạo bề mặt mượt mà và hiệu ứng căng bóng tự nhiên.',NULL,NULL,NULL,NULL,NULL,45,279000.00,NULL,NULL,NULL,2,NULL,'products/catalog-14.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(35,NULL,'Che khuyết điểm Cover Fit','che-khuyet-diem-cover-fit','Độ che phủ vừa phải, tiệp da và không gây nặng mặt.',NULL,NULL,NULL,NULL,NULL,60,239000.00,NULL,NULL,NULL,2,NULL,'products/catalog-15.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(36,NULL,'Son thỏi Velvet Rose','son-thoi-velvet-rose','Son lì mịn môi với sắc hồng đất dễ dùng mỗi ngày.',NULL,NULL,NULL,NULL,NULL,80,229000.00,NULL,NULL,NULL,2,NULL,'products/catalog-16.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(37,NULL,'Bảng tạo khối Face Sculpt','bang-tao-khoi-face-sculpt','Bảng màu tạo khối và bắt sáng hài hòa cho nhiều phong cách.',NULL,NULL,NULL,NULL,NULL,35,359000.00,NULL,NULL,NULL,2,NULL,'products/catalog-17.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(38,NULL,'Phấn mắt nhũ Champagne','phan-mat-nhu-champagne','Màu nhũ champagne bắt sáng nhẹ, phù hợp cả ngày và tối.',NULL,NULL,NULL,NULL,NULL,65,179000.00,NULL,NULL,NULL,2,NULL,'products/catalog-18.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(39,NULL,'Má hồng dạng kem Coral Mist','ma-hong-dang-kem-coral-mist','Má hồng dạng kem dễ tán, cho gò má ửng màu tự nhiên.',NULL,NULL,NULL,NULL,NULL,55,219000.00,NULL,NULL,NULL,2,NULL,'products/catalog-19.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(40,NULL,'Xịt khóa nền Long Wear','xit-khoa-nen-long-wear','Lớp xịt mỏng giúp cố định lớp trang điểm lâu hơn.',NULL,NULL,NULL,NULL,NULL,50,249000.00,NULL,NULL,NULL,2,NULL,'products/catalog-20.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(41,NULL,'Dầu gội bưởi giảm gãy rụng','dau-goi-buoi-giam-gay-rung','Dầu gội hương bưởi làm sạch nhẹ và chăm sóc mái tóc yếu.',NULL,NULL,NULL,NULL,NULL,70,239000.00,NULL,NULL,NULL,3,NULL,'products/catalog-21.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(42,NULL,'Dầu xả phục hồi Protein','dau-xa-phuc-hoi-protein','Dầu xả protein giúp tóc mềm, dễ chải và bớt xơ rối.',NULL,NULL,NULL,NULL,NULL,65,249000.00,NULL,NULL,NULL,3,NULL,'products/catalog-22.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(43,NULL,'Xịt dưỡng tóc chống nhiệt','xit-duong-toc-chong-nhiet','Lớp dưỡng nhẹ bảo vệ tóc trước tác động của nhiệt tạo kiểu.',NULL,NULL,NULL,NULL,NULL,60,219000.00,NULL,NULL,NULL,3,NULL,'products/catalog-23.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(44,NULL,'Dầu gội khô hương bạc hà','dau-goi-kho-huong-bac-ha','Làm mới chân tóc nhanh chóng giữa những lần gội.',NULL,NULL,NULL,NULL,NULL,50,189000.00,NULL,NULL,NULL,3,NULL,'products/beauty-01.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(45,NULL,'Sáp vuốt tóc tự nhiên Matte Clay','sap-vuot-toc-tu-nhien-matte-clay','Độ giữ nếp vừa phải với hiệu ứng lì tự nhiên, dễ gội sạch.',NULL,NULL,NULL,NULL,NULL,45,199000.00,NULL,NULL,NULL,3,NULL,'products/beauty-02.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(46,NULL,'Lược chải tóc chống rối','luoc-chai-toc-chong-roi','Răng lược mềm giúp gỡ rối nhẹ nhàng và hạn chế kéo tóc.',NULL,NULL,NULL,NULL,NULL,90,119000.00,NULL,NULL,NULL,3,NULL,'products/beauty-03.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(47,NULL,'Khăn ủ tóc sợi tre','khan-u-toc-soi-tre','Khăn ủ thấm hút tốt, tiện dụng trong quy trình chăm sóc tóc.',NULL,NULL,NULL,NULL,NULL,55,139000.00,NULL,NULL,NULL,3,NULL,'products/beauty-04.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(48,NULL,'Mặt nạ tóc dầu dừa','mat-na-toc-dau-dua','Mặt nạ dưỡng chuyên sâu cho mái tóc khô và thiếu sức sống.',NULL,NULL,NULL,NULL,NULL,48,229000.00,NULL,NULL,NULL,3,NULL,'products/beauty-05.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(49,NULL,'Tinh chất mọc tóc biotin','tinh-chat-moc-toc-biotin','Tinh chất chăm sóc da đầu với biotin và chiết xuất thực vật.',NULL,NULL,NULL,NULL,NULL,40,329000.00,NULL,NULL,NULL,3,NULL,'products/beauty-06.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(50,NULL,'Bộ dầu gội mini Weekend','bo-dau-goi-mini-weekend','Bộ dầu gội và dầu xả nhỏ gọn cho những chuyến đi ngắn.',NULL,NULL,NULL,NULL,NULL,75,159000.00,NULL,NULL,NULL,3,NULL,'products/beauty-07.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(51,NULL,'Sữa tắm yến mạch dịu da','sua-tam-yen-mach-diu-da','Sữa tắm yến mạch làm sạch êm dịu và lưu lại cảm giác mềm mại.',NULL,NULL,NULL,NULL,NULL,85,169000.00,NULL,NULL,NULL,5,NULL,'products/beauty-08.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(52,NULL,'Muối tắm thư giãn Lavender','muoi-tam-thu-gian-lavender','Muối tắm hương lavender cho những phút thư giãn tại nhà.',NULL,NULL,NULL,NULL,NULL,50,199000.00,NULL,NULL,NULL,5,NULL,'products/beauty-09.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(53,NULL,'Dầu dưỡng thể hoa hồng','dau-duong-the-hoa-hong','Dầu dưỡng mỏng nhẹ giúp da cơ thể trông mịn và sáng khỏe.',NULL,NULL,NULL,NULL,NULL,45,259000.00,NULL,NULL,NULL,5,NULL,'products/beauty-10.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(54,NULL,'Kem tay hương vanilla','kem-tay-huong-vanilla','Kem tay thấm nhanh, bổ sung độ ẩm mà không nhờn dính.',NULL,NULL,NULL,NULL,NULL,100,109000.00,NULL,NULL,NULL,5,NULL,'products/beauty-11.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(55,NULL,'Xà phòng thiên nhiên Oải hương','xa-phong-thien-nhien-oai-huong','Xà phòng thực vật tạo bọt nhẹ với hương oải hương thư giãn.',NULL,NULL,NULL,NULL,NULL,110,79000.00,NULL,NULL,NULL,5,NULL,'products/beauty-12.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(56,NULL,'Bàn chải tắm cán gỗ','ban-chai-tam-can-go','Bàn chải cán gỗ hỗ trợ làm sạch và massage cơ thể nhẹ nhàng.',NULL,NULL,NULL,NULL,NULL,70,129000.00,NULL,NULL,NULL,5,NULL,'products/beauty-13.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(57,NULL,'Kem gót chân bơ hạt mỡ','kem-got-chan-bo-hat-mo','Kem dưỡng chuyên biệt giúp vùng gót chân mềm mại hơn.',NULL,NULL,NULL,NULL,NULL,65,139000.00,NULL,NULL,NULL,5,NULL,'products/beauty-14.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(58,NULL,'Nến thơm Cotton Clean','nen-thom-cotton-clean','Nến thơm hương cotton sạch, tạo không gian thư thái.',NULL,NULL,NULL,NULL,NULL,35,279000.00,NULL,NULL,NULL,5,NULL,'products/beauty-15.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(59,NULL,'Bộ chăm sóc tay Mini Spa','bo-cham-soc-tay-mini-spa','Bộ mini gồm kem tay và mặt nạ tay cho đôi tay được chăm sóc.',NULL,NULL,NULL,NULL,NULL,45,189000.00,NULL,NULL,NULL,5,NULL,'products/beauty-16.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00'),(60,NULL,'Khăn mặt cotton hữu cơ','khan-mat-cotton-huu-co','Khăn cotton mềm, thấm hút tốt và phù hợp dùng hằng ngày.',NULL,NULL,NULL,NULL,NULL,100,99000.00,NULL,NULL,NULL,5,NULL,'products/beauty-17.jpg','2026-09-19 18:31:41','2026-09-20 13:28:00');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,'admin','dashboard',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(2,'manager','dashboard',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(3,'warehouse_staff','dashboard',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(4,'customer_service','dashboard',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(5,'admin','reports',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(6,'manager','reports',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(7,'warehouse_staff','reports',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(8,'customer_service','reports',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(9,'admin','products.view',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(10,'manager','products.view',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(11,'warehouse_staff','products.view',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(12,'customer_service','products.view',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(13,'admin','products.create_delete',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(14,'manager','products.create_delete',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(15,'warehouse_staff','products.create_delete',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(16,'customer_service','products.create_delete',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(17,'admin','products.edit',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(18,'manager','products.edit',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(19,'warehouse_staff','products.edit',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(20,'customer_service','products.edit',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(21,'admin','inventory.edit',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(22,'manager','inventory.edit',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(23,'warehouse_staff','inventory.edit',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(24,'customer_service','inventory.edit',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(25,'admin','inventory.logs',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(26,'manager','inventory.logs',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(27,'warehouse_staff','inventory.logs',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(28,'customer_service','inventory.logs',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(29,'admin','orders.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(30,'manager','orders.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(31,'warehouse_staff','orders.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(32,'customer_service','orders.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(33,'admin','refunds.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(34,'manager','refunds.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(35,'warehouse_staff','refunds.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(36,'customer_service','refunds.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(37,'admin','chat',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(38,'manager','chat',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(39,'warehouse_staff','chat',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(40,'customer_service','chat',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(41,'admin','members.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(42,'manager','members.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(43,'warehouse_staff','members.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(44,'customer_service','members.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(45,'admin','marketing.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(46,'manager','marketing.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(47,'warehouse_staff','marketing.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(48,'customer_service','marketing.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(49,'admin','staff.manage',1,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(50,'manager','staff.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(51,'warehouse_staff','staff.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38'),(52,'customer_service','staff.manage',0,'2026-10-12 12:32:38','2026-10-12 12:32:38');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-12 19:37:50
