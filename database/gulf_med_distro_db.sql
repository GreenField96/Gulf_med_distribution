-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 26, 2026 at 07:26 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.5.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gulf_med_distro_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('gulf-med-distribution-cache-livewire-rate-limiter:f1a61930318cea3be8d263453959f1809d9f1f70', 'i:1;', 1790421862),
('gulf-med-distribution-cache-livewire-rate-limiter:f1a61930318cea3be8d263453959f1809d9f1f70:timer', 'i:1790421862;', 1790421862);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `phone_num` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `company_name`, `phone_num`, `created_at`, `updated_at`) VALUES
(1, 'ras lanouf', '0911239999', '2026-09-08 21:16:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dates_when_member_take_his_medicals`
--

CREATE TABLE `dates_when_member_take_his_medicals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `date_month_year` varchar(255) NOT NULL,
  `is_taken` tinyint(1) NOT NULL DEFAULT 0,
  `confirmed_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dates_when_member_take_his_medicals`
--

INSERT INTO `dates_when_member_take_his_medicals` (`id`, `member_id`, `date_month_year`, `is_taken`, `confirmed_by_user_id`, `created_at`, `updated_at`) VALUES
(54, 5, '2026-09-01 00:00:00', 1, 2, '2026-09-12 13:24:20', '2026-09-15 08:53:33'),
(55, 1, '2026-09-01', 1, 2, '2026-09-12 13:26:31', '2026-09-16 08:33:02'),
(56, 2, '2026-09-01', 1, 2, '2026-09-12 13:26:31', '2026-09-15 11:32:45');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
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
  `attempts` smallint(5) UNSIGNED NOT NULL,
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
-- Table structure for table `medical_documents`
--

CREATE TABLE `medical_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `pdf_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `uploaded_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

CREATE TABLE `medications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `medical_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dosages` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medications`
--

INSERT INTO `medications` (`id`, `medical_name`, `price`, `dosages`, `created_at`, `updated_at`) VALUES
(1, 'diabeton tab', 10.00, '1*', '2026-09-08 19:38:58', '2026-09-09 12:34:37'),
(2, 'metformin 500mg', 20.00, '1*', '2026-09-09 11:38:15', '2026-09-09 11:42:03'),
(3, 'exforge 5/160mg', 100.00, '1*', '2026-09-09 14:49:57', '2026-09-15 11:35:36');

-- --------------------------------------------------------

--
-- Table structure for table `medications_and_dosages`
--

CREATE TABLE `medications_and_dosages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `medication_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medications_and_dosages`
--

INSERT INTO `medications_and_dosages` (`id`, `medication_id`, `member_id`, `amount`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 10, '2026-09-08 19:40:29', '2026-09-14 18:21:48'),
(3, 1, 2, 1, '2026-09-09 14:50:16', '2026-09-14 16:48:20'),
(7, 3, 5, 1, '2026-09-14 17:02:10', '2026-09-14 17:02:10'),
(8, 2, 2, 1, '2026-09-14 17:28:03', '2026-09-14 17:28:03'),
(9, 1, 1, 2, '2026-09-15 07:11:59', '2026-09-16 08:32:57'),
(10, 2, 2, 2, '2026-09-15 11:32:28', '2026-09-15 11:32:28');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `member_ID` varchar(255) NOT NULL,
  `national_ID` varchar(255) NOT NULL,
  `family_ID` varchar(255) DEFAULT NULL,
  `phone_num` varchar(255) DEFAULT NULL,
  `reference_to_pdf` varchar(255) DEFAULT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `company_id`, `first_name`, `last_name`, `member_ID`, `national_ID`, `family_ID`, `phone_num`, `reference_to_pdf`, `is_locked`, `created_at`, `updated_at`) VALUES
(1, 1, 'munder', 'aljbali', '10001000', '120010461223', NULL, '0928882999', 'medical_pdfs/01M2JJ27QDG59GNAJ7SAB0QJW0.pdf', 0, '2026-09-08 19:26:43', '2026-09-16 08:32:57'),
(2, 1, 'نورية ميلود ', 'امراجع', '100013290', '21928028923', '10656', NULL, 'medical_pdfs/01M2JV3B9GVSX39T6SPWJ6NY5F.pdf', 0, '2026-09-09 14:48:51', '2026-09-15 11:32:28'),
(5, 1, 'hosam', 'ahmed', '10005000', '12000123254', '10655', '0928882931', 'medical_pdfs/01M2BAC3KHQPH1CTTS2S8SH293.pdf', 0, '2026-09-12 13:24:20', '2026-09-14 17:02:10');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone_num` varchar(255) DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'companies_members',
  `locale` varchar(5) NOT NULL DEFAULT 'ar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone_num`, `company_id`, `remember_token`, `created_at`, `updated_at`, `role`, `locale`) VALUES
(1, 'admin', 'admin@admin.com', NULL, '$2y$12$szruiOcCKm4eHqviodlWxOaE/HX3ODa1L1rZbsfsdSf1EmVMfPcUe', NULL, NULL, NULL, '2026-09-08 19:05:48', '2026-09-08 20:22:48', 'admin_user', 'ar'),
(2, 'كمال', 'kamal@admin.com', NULL, '$2y$12$aOSRDINVtW.UzSLphf4kFeJPtpwZWVpIHKzl94fb.tCr8ynO1sSYW', NULL, NULL, NULL, '2026-09-08 20:42:10', '2026-09-08 20:42:10', 'medical_distro_operator_user', 'ar'),
(3, 'مريم', 'mariam@admin.com', NULL, '$2y$12$.RrvW5ydP5aAvQsHWNXbQ.jS4oMMqIIJwTLvHiV29KbcyBOyl9v76', NULL, 1, NULL, '2026-09-08 20:45:12', '2026-09-08 20:45:12', 'companies_members', 'ar'),
(4, 'admin1', 'admin1@admin.com', NULL, '$2y$12$V65SEjiQ/wewSfS8A58hTOd5Adm52pScqJ3.XQA1XhWCV5cp587KW', NULL, NULL, NULL, '2026-09-17 23:57:40', '2026-09-17 23:57:40', 'companies_members', 'ar');

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
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dates_when_member_take_his_medicals`
--
ALTER TABLE `dates_when_member_take_his_medicals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dates_when_member_take_his_medicals_member_id_foreign` (`member_id`),
  ADD KEY `dates_when_member_take_his_medicals_confirmed_by_user_id_foreign` (`confirmed_by_user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

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
-- Indexes for table `medical_documents`
--
ALTER TABLE `medical_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medical_documents_member_id_foreign` (`member_id`),
  ADD KEY `medical_documents_uploaded_by_user_id_foreign` (`uploaded_by_user_id`);

--
-- Indexes for table `medications`
--
ALTER TABLE `medications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medications_and_dosages`
--
ALTER TABLE `medications_and_dosages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medications_and_dosages_medication_id_foreign` (`medication_id`),
  ADD KEY `medications_and_dosages_member_id_foreign` (`member_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `members_member_id_unique` (`member_ID`),
  ADD KEY `members_company_id_foreign` (`company_id`);

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
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

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
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dates_when_member_take_his_medicals`
--
ALTER TABLE `dates_when_member_take_his_medicals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

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
-- AUTO_INCREMENT for table `medical_documents`
--
ALTER TABLE `medical_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medications`
--
ALTER TABLE `medications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `medications_and_dosages`
--
ALTER TABLE `medications_and_dosages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dates_when_member_take_his_medicals`
--
ALTER TABLE `dates_when_member_take_his_medicals`
  ADD CONSTRAINT `dates_when_member_take_his_medicals_confirmed_by_user_id_foreign` FOREIGN KEY (`confirmed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dates_when_member_take_his_medicals_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medical_documents`
--
ALTER TABLE `medical_documents`
  ADD CONSTRAINT `medical_documents_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medical_documents_uploaded_by_user_id_foreign` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medications_and_dosages`
--
ALTER TABLE `medications_and_dosages`
  ADD CONSTRAINT `medications_and_dosages_medication_id_foreign` FOREIGN KEY (`medication_id`) REFERENCES `medications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medications_and_dosages_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
