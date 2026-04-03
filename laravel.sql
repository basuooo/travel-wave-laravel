-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 06:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_customer_accounts`
--

CREATE TABLE `accounting_customer_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `crm_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `service_label` varchar(255) DEFAULT NULL,
  `service_destination` varchar(255) DEFAULT NULL,
  `lead_source` varchar(255) DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_customer_expenses` decimal(12,2) NOT NULL DEFAULT 0.00,
  `company_profit_before_seller` decimal(12,2) NOT NULL DEFAULT 0.00,
  `seller_profit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `final_company_profit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(30) NOT NULL DEFAULT 'unpaid',
  `notes` text DEFAULT NULL,
  `last_payment_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_customer_expenses`
--

CREATE TABLE `accounting_customer_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_customer_account_id` bigint(20) UNSIGNED NOT NULL,
  `accounting_expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `accounting_expense_subcategory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accounting_treasury_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_customer_payments`
--

CREATE TABLE `accounting_customer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_customer_account_id` bigint(20) UNSIGNED NOT NULL,
  `accounting_treasury_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_type` varchar(30) NOT NULL DEFAULT 'payment',
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_employee_transactions`
--

CREATE TABLE `accounting_employee_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `accounting_treasury_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_type` varchar(30) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_expense_categories`
--

CREATE TABLE `accounting_expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounting_expense_categories`
--

INSERT INTO `accounting_expense_categories` (`id`, `name_ar`, `name_en`, `slug`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'حجز معاد السفارة', 'Embassy Appointment', 'embassy-appointment', 1, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(2, 'الترجمات الرئيسية', 'Main Translations', 'main-translations', 2, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(3, 'دعوة المؤتمر او المعرض', 'Conference Invitation', 'conference-invitation', 3, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(4, 'توصيل اوبر او جو باص', 'Transportation', 'transportation', 4, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `accounting_expense_subcategories`
--

CREATE TABLE `accounting_expense_subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounting_expense_subcategories`
--

INSERT INTO `accounting_expense_subcategories` (`id`, `accounting_expense_category_id`, `name_ar`, `name_en`, `slug`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'سجل تجاري', 'Commercial Register', 'commercial-register', 1, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(2, 2, 'بطاقة ضريبية', 'Tax Card', 'tax-card', 2, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(3, 2, 'خطاب جهة العمل', 'Employment Letter', 'employment-letter', 3, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(4, 2, 'شهادة تحركات', 'Movements Certificate', 'movements-certificate', 4, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(5, 2, 'قيد عائلي', 'Family Record', 'family-record', 5, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(6, 2, 'الممتلكات', 'Properties', 'properties', 6, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `accounting_general_expenses`
--

CREATE TABLE `accounting_general_expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_general_expense_category_id` bigint(20) UNSIGNED NOT NULL,
  `accounting_treasury_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_date` date NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_general_expense_categories`
--

CREATE TABLE `accounting_general_expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounting_general_expense_categories`
--

INSERT INTO `accounting_general_expense_categories` (`id`, `name_ar`, `name_en`, `slug`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'ايجار', 'Rent', 'rent', 1, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(2, 'كهرباء', 'Electricity', 'electricity', 2, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(3, 'مياة', 'Water', 'water', 3, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(4, 'موبايل', 'Mobile', 'mobile', 4, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(5, 'مشروبات', 'Beverages', 'beverages', 5, 1, '2026-03-25 20:41:08', '2026-03-25 20:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `accounting_treasuries`
--

CREATE TABLE `accounting_treasuries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `accounting_treasury_transactions`
--

CREATE TABLE `accounting_treasury_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `accounting_treasury_id` bigint(20) UNSIGNED NOT NULL,
  `direction` varchar(10) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `related_type` varchar(255) DEFAULT NULL,
  `related_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `auditable_type` varchar(255) DEFAULT NULL,
  `auditable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `changed_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changed_fields`)),
  `target_label` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action_type`, `module`, `auditable_type`, `auditable_id`, `title`, `description`, `old_values`, `new_values`, `changed_fields`, `target_label`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 3, 'created', 'chatbot', 'App\\Models\\ChatbotKnowledgeEntry', 1, 'العنوان', 'العنوان', NULL, '{\"title_en\":\"address\",\"title_ar\":\"\\u0627\\u0644\\u0639\\u0646\\u0648\\u0627\\u0646\",\"question_en\":\"address\",\"question_ar\":\"\\u0627\\u0644\\u0639\\u0646\\u0648\\u0627\\u0646\",\"answer_en\":\"45 \\u0634\\u0627\\u0631\\u0639 \\u0631\\u0634\\u064a\\u062f \\u0627\\u062d\\u0645\\u062f \\u0639\\u0631\\u0627\\u0628\\u064a \\u0627\\u0644\\u0645\\u0647\\u0646\\u062f\\u0633\\u064a\\u0646 \\u0627\\u0644\\u062c\\u064a\\u0632\\u0629 \\u0628\\u062c\\u0648\\u0627\\u0631 \\u0645\\u062d\\u0637\\u0629 \\u0645\\u062a\\u0631\\u0648 \\u0627\\u0644\\u062a\\u0648\\u0641\\u064a\\u0642\\u064a\\u0629\",\"answer_ar\":\"45 \\u0634\\u0627\\u0631\\u0639 \\u0631\\u0634\\u064a\\u062f \\u0627\\u062d\\u0645\\u062f \\u0639\\u0631\\u0627\\u0628\\u064a \\u0627\\u0644\\u0645\\u0647\\u0646\\u062f\\u0633\\u064a\\u0646 \\u0627\\u0644\\u062c\\u064a\\u0632\\u0629 \\u0628\\u062c\\u0648\\u0627\\u0631 \\u0645\\u062d\\u0637\\u0629 \\u0645\\u062a\\u0631\\u0648 \\u0627\\u0644\\u062a\\u0648\\u0641\\u064a\\u0642\\u064a\\u0629\",\"keywords_en\":\"\\u0627\\u0644\\u0639\\u0646\\u0648\\u0627\\u0646,\\u0645\\u0643\\u0627\\u0646\\u0643\\u0645,\\u0645\\u0648\\u0642\\u0639\\u0643\\u0645\",\"keywords_ar\":\"\\u0627\\u0644\\u0639\\u0646\\u0648\\u0627\\u0646,\\u0645\\u0643\\u0627\\u0646\\u0643\\u0645,\\u0645\\u0648\\u0642\\u0639\\u0643\\u0645\",\"category_en\":null,\"category_ar\":null,\"is_active\":1,\"priority\":0}', '[\"title_en\",\"title_ar\",\"question_en\",\"question_ar\",\"answer_en\",\"answer_ar\",\"keywords_en\",\"keywords_ar\",\"category_en\",\"category_ar\",\"is_active\",\"priority\"]', 'ChatbotKnowledgeEntry #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 03:02:13');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description_en` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `name_en`, `name_ar`, `slug`, `description_en`, `description_ar`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'Travel Insights', 'محتوى السفر', 'travel-insights', 'Practical guidance for visas, hotels, flights, and smart travel planning.', 'محتوى عملي عن التأشيرات والفنادق والطيران وتخطيط السفر الذكي.', 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `blog_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt_en` text DEFAULT NULL,
  `excerpt_ar` text DEFAULT NULL,
  `content_en` longtext DEFAULT NULL,
  `content_ar` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `tags_en` text DEFAULT NULL,
  `tags_ar` text DEFAULT NULL,
  `meta_title_en` varchar(255) DEFAULT NULL,
  `meta_title_ar` varchar(255) DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `blog_category_id`, `title_en`, `title_ar`, `slug`, `excerpt_en`, `excerpt_ar`, `content_en`, `content_ar`, `featured_image`, `tags_en`, `tags_ar`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `is_published`, `is_featured`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 1, 'Top reasons visas get rejected and how to avoid them', 'أهم أسباب رفض التأشيرات وكيف تتجنبها', 'top-reasons-visas-get-rejected', 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.', 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.', 'Travel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.', 'تنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-22 23:07:02', '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 1, 'Best time to apply for Europe visas', 'أفضل وقت للتقديم على تأشيرات أوروبا', 'best-time-to-apply-for-europe-visas', 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.', 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.', 'Travel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.', 'تنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-22 23:07:02', '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(3, 1, 'How to choose the right hotel for your trip', 'كيف تختار الفندق المناسب لرحلتك', 'how-to-choose-the-right-hotel', 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.', 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.', 'Travel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.', 'تنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2026-03-22 23:07:02', '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_interactions`
--

CREATE TABLE `chatbot_interactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `session_key` varchar(100) DEFAULT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'ar',
  `question` text NOT NULL,
  `answer` longtext DEFAULT NULL,
  `matched_sources` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`matched_sources`)),
  `was_answered` tinyint(1) NOT NULL DEFAULT 0,
  `used_handoff` tinyint(1) NOT NULL DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_interactions`
--

INSERT INTO `chatbot_interactions` (`id`, `session_key`, `locale`, `question`, `answer`, `matched_sources`, `was_answered`, `used_handoff`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 'QqypEb6jwTYbyfEveGYivJyibFfz3lVSs5EvaCM2', 'ar', 'عايز اسافر فرنسا', 'وجدت لك أقرب المعلومات داخل محتوى Travel Wave:\n\n• فرنسا: - تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\nالرابط: http://127.0.0.1:8000/visa-country/france-visa\n\nإذا أردت، يمكنني أيضًا توجيهك إلى واتساب أو صفحة التواصل لمتابعة الطلب.', '[{\"title\":\"\\u0641\\u0631\\u0646\\u0633\\u0627\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/visa-country\\/france-visa\",\"source_type\":\"visa_countries\"}]', 1, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 02:34:02', '2026-03-27 02:34:02'),
(2, 'QqypEb6jwTYbyfEveGYivJyibFfz3lVSs5EvaCM2', 'ar', 'العنوان', 'هذه بيانات التواصل الحالية الخاصة بـ Travel Wave:\nالهاتف: +20 100 123 4567\nهاتف إضافي: +20 122 555 7788\nواتساب: +20 100 123 4567\nالبريد الإلكتروني: info@travelwave.com\nالعنوان: مدينة نصر، القاهرة، مصر\nمواعيد العمل: يوميًا من 10 صباحًا حتى 8 مساءً', '[]', 1, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 03:02:34', '2026-03-27 03:02:34'),
(3, 'QqypEb6jwTYbyfEveGYivJyibFfz3lVSs5EvaCM2', 'ar', 'مكانكم', '45 شارع رشيد احمد عرابي المهندسين الجيزة بجوار محطة مترو التوفيقية', '[{\"title\":\"\\u0627\\u0644\\u0639\\u0646\\u0648\\u0627\\u0646\",\"url\":null,\"source_type\":\"manual_knowledge\"}]', 1, 0, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-27 03:02:57', '2026-03-27 03:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_knowledge_entries`
--

CREATE TABLE `chatbot_knowledge_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `question_en` text DEFAULT NULL,
  `question_ar` text DEFAULT NULL,
  `answer_en` longtext DEFAULT NULL,
  `answer_ar` longtext DEFAULT NULL,
  `keywords_en` text DEFAULT NULL,
  `keywords_ar` text DEFAULT NULL,
  `category_en` varchar(255) DEFAULT NULL,
  `category_ar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `priority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_knowledge_entries`
--

INSERT INTO `chatbot_knowledge_entries` (`id`, `title_en`, `title_ar`, `question_en`, `question_ar`, `answer_en`, `answer_ar`, `keywords_en`, `keywords_ar`, `category_en`, `category_ar`, `is_active`, `priority`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'address', 'العنوان', 'address', 'العنوان', '45 شارع رشيد احمد عرابي المهندسين الجيزة بجوار محطة مترو التوفيقية', '45 شارع رشيد احمد عرابي المهندسين الجيزة بجوار محطة مترو التوفيقية', 'العنوان,مكانكم,موقعكم', 'العنوان,مكانكم,موقعكم', NULL, NULL, 1, 0, 3, 3, '2026-03-27 03:02:13', '2026-03-27 03:02:13');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_knowledge_items`
--

CREATE TABLE `chatbot_knowledge_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `source_type` varchar(80) NOT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_key` varchar(120) DEFAULT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'ar',
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_knowledge_items`
--

INSERT INTO `chatbot_knowledge_items` (`id`, `source_type`, `source_id`, `source_key`, `locale`, `title`, `summary`, `content`, `url`, `metadata`, `sort_order`, `created_at`, `updated_at`) VALUES
(213, 'pages', 1, 'home', 'en', 'Home', 'With Travel Wave, you get an integrated service that makes travel planning easier, from file preparation and bookings to destination selection and trip planning.', 'Integrated Travel Services\nWe organize your journey from the first step to the final detail\nWith Travel Wave, you get an integrated service that makes travel planning easier, from file preparation and bookings to destination selection and trip planning.\nOne team for visas, trips, flights, and hotels\nTravel Wave combines outbound travel, domestic tourism, visa services, hotels, and flights in one organized customer journey.\nخدمات التأشيرات\nدعم منظم لتجهيز الملفات وفهم المتطلبات.\nVS\nالسياحة الخارجية\nتخطيط للسفر الخارجي وعروض مرنة للرحلات.\nIT\nالسياحة الداخلية\nباقات داخل مصر مع خيارات فنادق وأنشطة.\nDT\nالتنظيم\nنجعل خطوات السفر سهلة وواضحة.\nسرعة الاستجابة\nردود عملية ومتابعة وقت الحاجة.\nأخبرنا بطلبك\nأخبرنا بالوجهة والهدف والموعد.\nاستلم خطة واضحة\nنوضح لك الخدمة المناسبة والخطوات التالية.\nالطيران والفنادق ضمن خطة واحدة\nنسق رحلتك مع دعم متكامل للطيران والفنادق لتخطيط أكثر سهولة.\nاكتشف الخدمات\n/flights\nأخبرنا إلى أين تريد السفر\nيمكن لفريقنا ترشيح التأشيرة أو الوجهة أو مسار الحجز الأنسب لك.\nابدأ طلبك بثقة\nيصبح تخطيط السفر أسهل عندما تكون كل تفصيلة مرتبطة بخطوة واضحة.\nتواصل مع Travel Wave\n/contact', 'http://127.0.0.1:8000', NULL, 1, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(214, 'pages', 1, 'home', 'ar', 'الرئيسية', 'مع Travel Wave تحصل على خدمة متكاملة تجعل تخطيط السفر أسهل، من تجهيز الملف والحجوزات إلى اختيار الوجهة وتنظيم الرحلة.', 'خدمات سفر متكاملة\nننظم رحلتك من أول خطوة حتى آخر تفصيلة\nمع Travel Wave تحصل على خدمة متكاملة تجعل تخطيط السفر أسهل، من تجهيز الملف والحجوزات إلى اختيار الوجهة وتنظيم الرحلة.\nفريق واحد للتأشيرات والرحلات والطيران والفنادق\nتجمع Travel Wave بين السفر الخارجي والسياحة الداخلية وخدمات التأشيرات والفنادق والطيران ضمن رحلة عميل منظمة وواضحة.\nخدمات التأشيرات\nدعم منظم لتجهيز الملفات وفهم المتطلبات.\nVS\nالسياحة الخارجية\nتخطيط للسفر الخارجي وعروض مرنة للرحلات.\nIT\nالسياحة الداخلية\nباقات داخل مصر مع خيارات فنادق وأنشطة.\nDT\nالتنظيم\nنجعل خطوات السفر سهلة وواضحة.\nسرعة الاستجابة\nردود عملية ومتابعة وقت الحاجة.\nأخبرنا بطلبك\nأخبرنا بالوجهة والهدف والموعد.\nاستلم خطة واضحة\nنوضح لك الخدمة المناسبة والخطوات التالية.\nالطيران والفنادق ضمن خطة واحدة\nنسق رحلتك مع دعم متكامل للطيران والفنادق لتخطيط أكثر سهولة.\nاكتشف الخدمات\n/flights\nأخبرنا إلى أين تريد السفر\nيمكن لفريقنا ترشيح التأشيرة أو الوجهة أو مسار الحجز الأنسب لك.\nابدأ طلبك بثقة\nيصبح تخطيط السفر أسهل عندما تكون كل تفصيلة مرتبطة بخطوة واضحة.\nتواصل مع Travel Wave\n/contact', 'http://127.0.0.1:8000', NULL, 2, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(215, 'pages', 2, 'visas', 'en', 'Visas', 'Explore categories, compare countries, and request support.', 'Overseas visa services\nExplore categories, compare countries, and request support.', 'http://127.0.0.1:8000/visas', NULL, 3, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(216, 'pages', 2, 'visas', 'ar', 'التأشيرات', 'استعرض الفئات وقارن بين الدول واطلب الدعم المناسب.', 'خدمات التأشيرات الخارجية\nاستعرض الفئات وقارن بين الدول واطلب الدعم المناسب.', 'http://127.0.0.1:8000/visas', NULL, 4, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(217, 'pages', 3, 'domestic', 'en', 'Domestic Tourism', 'Trips to the most requested destinations with practical packages.', 'Domestic tourism in Egypt\nTrips to the most requested destinations with practical packages.', 'http://127.0.0.1:8000/domestic-tourism', NULL, 5, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(218, 'pages', 3, 'domestic', 'ar', 'السياحة الداخلية', 'رحلات إلى أكثر الوجهات طلبًا مع باقات عملية.', 'السياحة الداخلية داخل مصر\nرحلات إلى أكثر الوجهات طلبًا مع باقات عملية.', 'http://127.0.0.1:8000/domestic-tourism', NULL, 6, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(219, 'pages', 4, 'flights', 'en', 'Flights', 'Travel Wave supports customers with flight planning, route comparison, and booking coordination.', 'Flight booking support\nChoose the right route and timing\nTravel Wave supports customers with flight planning, route comparison, and booking coordination.', 'http://127.0.0.1:8000/flights', NULL, 7, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(220, 'pages', 4, 'flights', 'ar', 'الطيران', 'تدعم Travel Wave العملاء في تخطيط الرحلات الجوية ومقارنة المسارات وتنسيق الحجز.', 'دعم حجز الطيران\nاختر المسار والتوقيت المناسبين\nتدعم Travel Wave العملاء في تخطيط الرحلات الجوية ومقارنة المسارات وتنسيق الحجز.', 'http://127.0.0.1:8000/flights', NULL, 8, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(221, 'pages', 5, 'hotels', 'en', 'Hotels', 'We help travelers compare hotel categories and select the right stay for their trip.', 'Hotel booking support\nStay options that match your budget\nWe help travelers compare hotel categories and select the right stay for their trip.', 'http://127.0.0.1:8000/hotels', NULL, 9, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(222, 'pages', 5, 'hotels', 'ar', 'الفنادق', 'نساعد المسافرين على مقارنة الفئات الفندقية واختيار الإقامة المناسبة لرحلتهم.', 'دعم حجز الفنادق\nخيارات إقامة تناسب ميزانيتك\nنساعد المسافرين على مقارنة الفئات الفندقية واختيار الإقامة المناسبة لرحلتهم.', 'http://127.0.0.1:8000/hotels', NULL, 10, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(223, 'pages', 6, 'about', 'en', 'About Us', 'Travel Wave was created to organize the customer journey across visas, travel planning, flights, hotels, and domestic tourism with one reliable team.', 'About Travel Wave\nA travel company built around clarity and follow-up\nTravel Wave was created to organize the customer journey across visas, travel planning, flights, hotels, and domestic tourism with one reliable team.', 'http://127.0.0.1:8000/about', NULL, 11, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(224, 'pages', 6, 'about', 'ar', 'من نحن', 'تم إنشاء Travel Wave لتنظيم رحلة العميل في التأشيرات وتخطيط السفر والطيران والفنادق والسياحة الداخلية من خلال فريق واحد موثوق.', 'عن Travel Wave\nشركة سفر مبنية على الوضوح والمتابعة\nتم إنشاء Travel Wave لتنظيم رحلة العميل في التأشيرات وتخطيط السفر والطيران والفنادق والسياحة الداخلية من خلال فريق واحد موثوق.', 'http://127.0.0.1:8000/about', NULL, 12, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(225, 'pages', 7, 'contact', 'en', 'Contact', 'Share your inquiry and our team will guide you toward the right service.', 'Speak with Travel Wave\nWe are ready to help with the next step\nShare your inquiry and our team will guide you toward the right service.', 'http://127.0.0.1:8000/contact', NULL, 13, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(226, 'pages', 7, 'contact', 'ar', 'تواصل معنا', 'أرسل استفسارك وسيقوم فريقنا بتوجيهك إلى الخدمة المناسبة.', 'تحدث مع Travel Wave\nنحن جاهزون لمساعدتك في الخطوة التالية\nأرسل استفسارك وسيقوم فريقنا بتوجيهك إلى الخدمة المناسبة.', 'http://127.0.0.1:8000/contact', NULL, 14, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(227, 'pages', 8, 'blog', 'en', 'Blog', 'Articles that help travelers prepare before booking or applying.', 'Travel insights and practical tips\nArticles that help travelers prepare before booking or applying.', 'http://127.0.0.1:8000/blog', NULL, 15, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(228, 'pages', 8, 'blog', 'ar', 'المقالات', 'مقالات تساعد المسافرين على الاستعداد قبل الحجز أو التقديم.', 'محتوى السفر والنصائح العملية\nمقالات تساعد المسافرين على الاستعداد قبل الحجز أو التقديم.', 'http://127.0.0.1:8000/blog', NULL, 16, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(229, 'service_pages', NULL, 'visas', 'en', 'خدمات التأشيرات الخارجية', 'نجعل رحلة التقديم أوضح وأسهل من أول استشارة حتى تجهيز الملف والحجوزات والمتابعة، بأسلوب احترافي يليق بعلامة Travel Wave.', 'خدمات التأشيرات الخارجية\nrtl\nمنصة تأشيرات احترافية\nنجعل رحلة التقديم أوضح وأسهل من أول استشارة حتى تجهيز الملف والحجوزات والمتابعة، بأسلوب احترافي يليق بعلامة Travel Wave.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الآن\n#service-form\nprimary\nاستعرض الوجهات\n#service-featured\noutline\n+24\nوجهة متاحة\nخيارات واسعة لتأشيرات أوروبا وآسيا والعالم العربي وأمريكا الشمالية.\n15-30\nيوم عمل\nمدد معالجة تقريبية أوضح حسب كل وجهة واكتمال الملف.\n360°\nدعم كامل\nمراجعة ملف، حجوزات، إرشاد، ومتابعة حتى مرحلة التقديم.\nhttp://127.0.0.1:8000/visas\nابحث الآن\nservice_type\nنوع الخدمة\nاختر نوع الخدمة\nتأشيرة سياحية\nزيارة عائلية\nتأشيرة أعمال\nحجز موعد ومتابعة\ndestination\nالوجهة\nاختر الوجهة\nفرنسا\nhttp://127.0.0.1:8000/visa-country/france-visa\nألمانيا\nhttp://127.0.0.1:8000/visa-country/germany-visa\nإيطاليا\nhttp://127.0.0.1:8000/visa-country/italy-visa\nإسبانيا\nhttp://127.0.0.1:8000/visa-country/spain-visa\nهولندا\nhttp://127.0.0.1:8000/visa-country/netherlands-visa\nاليونان\nhttp://127.0.0.1:8000/visa-country/greece-visa\nأمريكا\nhttp://127.0.0.1:8000/visa-country/usa-visa\nالإمارات\nhttp://127.0.0.1:8000/visa-country/uae-visa\nتركيا\nhttp://127.0.0.1:8000/visa-country/turkey-visa\nكندا\nhttp://127.0.0.1:8000/visa-country/canada-visa\nvisa_type\nنوع التأشيرة\nاختر نوع التأشيرة\nشنغن قصيرة الإقامة\nرحلات أعمال\nمتعددة السفرات\nservice-featured\nالوجهات الأكثر طلبًا\nأشهر وجهات التأشيرات\nبطاقات مختارة لوجهات يطلبها العملاء باستمرار مع معلومات سريعة ورابط مباشر للتفاصيل.\n3600\nتأشيرة شنغن قصيرة الإقامة\nتتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.\nالأكثر طلبًا\nعرض التفاصيل\nحوالي 15 إلى 30 يوم عمل\nشنغن\nإقامة قصيرة\nلماذا Travel Wave\nلماذا يختارنا العملاء في خدمات التأشيرات\nخدمة واضحة ومرتبة تمنح العميل ثقة أعلى وتجربة أكثر احترافية في كل خطوة.\n01\nمراجعة المستندات\nفحص الملف وتحديد النواقص ونقاط التحسين قبل التقديم.\n02\nمتابعة الملف\nمتابعة منظمة لكل مرحلة من التجهيز وحتى ما بعد التقديم.\n03\nتنسيق الحجوزات\nمساعدة في ترتيب الطيران والفنادق بما يدعم ملف الرحلة.\n04\nتنظيم برنامج الرحلة\nبناء تصور أوضح للرحلة والمدة والهدف بما يعزز الملف.\n05\nسرعة في التنفيذ\nخطوات أوضح وتجهيز أسرع للنماذج والمستندات الأساسية.\n06\nدعم كامل حتى التقديم\nإرشاد واضح وحجز موعد ومتابعة مستمرة حتى آخر خطوة.\nخدمات داعمة\nباقات خدمة مرنة حسب احتياجك\nاختر مستوى الدعم الأنسب لرحلتك ونوع التأشيرة المطلوبة.\nمراجعة الملف قبل التقديم\nللملفات الجاهزة التي تحتاج مراجعة دقيقة\nتنسيق بيانات الرحلة\nملاحظات واضحة قبل التقديم\nيحدد حسب الوجهة وحجم الملف\nاطلب الخدمة\nخدمة تجهيز كاملة\nمن أول خطوة حتى ترتيب المستندات الأساسية\nإرشاد كامل\nتنظيم الملف\nتنسيق الحجوزات والمتطلبات\nخطة مرنة حسب نوع التأشيرة\nدعم المواعيد والمتابعة\nلمن يحتاج دعماً عملياً في الموعد والإجراءات\nحجز موعد إذا أمكن\nمتابعة الخطوات\nإجابات أوضح قبل التقديم\nيحدد حسب الوجهة والمركز\nخطوات واضحة\nكيف تسير الخدمة معنا\nرحلة منظمة تمنحك وضوحًا أكبر من أول تواصل وحتى مرحلة التقديم والمتابعة.\nنحدد معك الدولة المناسبة ونوع التأشيرة الأنسب لسبب السفر.\nأرسل المستندات\nتشاركنا المستندات الأساسية والبيانات المهمة الخاصة بالرحلة.\nمراجعة الملف\nنفحص الملف ونوضح المطلوب استكماله أو تحسينه قبل التقديم.\nحجز الموعد\nنرتب خطوة الموعد ونجهزك لما قبل التقديم حسب الوجهة.\nالتقديم والمتابعة\nتستكمل التقديم بثقة مع متابعة أوضح لحالة الطلب والخطوات التالية.\nشبكة الوجهات\nوجهات يمكنك البدء بها الآن\nمجموعة من أشهر الوجهات الخارجية التي يمكن إدارة ملفها من خلال Travel Wave.\n- تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.\nتأشيرة ألمانيا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nتأشيرة إيطاليا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nخدمة تأشيرة\nخدمة متكاملة لتجهيز الملف وتوضيح الخطوات الأساسية قبل التقديم.\nدعم تأشيرة الإمارات مع تخطيط عملي وإرشاد للمستندات.\nدعم تأشيرة تركيا مع تخطيط عملي وإرشاد للمستندات.\nدعم تأشيرة كندا مع تخطيط عملي وإرشاد للمستندات.\nمعلومات سريعة\nأهم ما يحتاجه العميل قبل البدء\nمدة المعالجة\nغالبًا من 15 إلى 30 يوم عمل حسب الوجهة والموسم.\nnavy\nالرسوم\nتتحدد حسب الدولة، الرسوم القنصلية، ومستوى الخدمة المطلوب.\nroyal\nالمستندات المطلوبة\nجواز سفر، صور، مستندات مالية، حجوزات، وأوراق داعمة حسب الحالة.\namber\nسهولة الملف\nترتفع مع اكتمال البيانات وتناسق المستندات وخطة الرحلة.\nslate\nابدأ بثقة\nابدأ ملف التأشيرة بخطوات أوضح ودعم أكثر احترافية\nنمنحك تجربة أكثر ترتيبًا وراحة من أول استشارة حتى تجهيز الملف والحجوزات وشرح الرسوم والمدة المتوقعة.\nاحجز استشارتك الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nlight-outline\nالأسئلة الشائعة\nإجابات سريعة قبل أن تبدأ\nمجموعة أسئلة شائعة تساعد العميل على فهم ما ينتظره قبل تجهيز الملف.\nما مدة استخراج التأشيرة؟\nتختلف حسب الدولة والموسم واكتمال الملف، لكن كثيرًا من الوجهات تقع بين 15 و30 يوم عمل.\nما الأوراق المطلوبة؟\nيعتمد ذلك على الوجهة ونوع التأشيرة، لكن الأساس يشمل الجواز والصور والمستندات المالية والحجوزات.\nهل يوجد متابعة بعد التقديم؟\nنعم، يتم إرشادك لما بعد التقديم مع متابعة أوضح للخطوات التالية عند الحاجة.\nهل يمكن المساعدة في الحجوزات؟\nنعم، يمكن المساعدة في تنسيق الطيران والفنادق بما يناسب ملف الرحلة.\nما أفضل وقت للتقديم؟\nكلما كان التقديم مبكرًا كان أفضل، خصوصًا قبل المواسم المزدحمة أو عند محدودية المواعيد.\nطلب استشارة\nابدأ معنا بطلب واضح وسريع\nاترك بياناتك وسيتواصل معك فريق Travel Wave لتحديد الخطوات المناسبة للوجهة ونوع التأشيرة.\nاستشارة أولية مباشرة\nتوجيه للمستندات والحجوزات\nمتابعة بعد إرسال الطلب\nvisa\nExternal Visa Services\nأرسل الطلب\nfull_name\nالاسم\ntext\nاكتب الاسم الكامل\nphone\nرقم الهاتف\nرقم الهاتف أو واتساب\nselect\nاختر النوع\nmessage\nملاحظات\ntextarea\nاكتب أي تفاصيل مهمة عن الرحلة أو الملف', 'http://127.0.0.1:8000/visas', '{\"page_key\":\"visas\"}', 17, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(230, 'service_pages', NULL, 'visas', 'ar', 'خدمات التأشيرات الخارجية', 'نجعل رحلة التقديم أوضح وأسهل من أول استشارة حتى تجهيز الملف والحجوزات والمتابعة، بأسلوب احترافي يليق بعلامة Travel Wave.', 'خدمات التأشيرات الخارجية\nrtl\nمنصة تأشيرات احترافية\nنجعل رحلة التقديم أوضح وأسهل من أول استشارة حتى تجهيز الملف والحجوزات والمتابعة، بأسلوب احترافي يليق بعلامة Travel Wave.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الآن\n#service-form\nprimary\nاستعرض الوجهات\n#service-featured\noutline\n+24\nوجهة متاحة\nخيارات واسعة لتأشيرات أوروبا وآسيا والعالم العربي وأمريكا الشمالية.\n15-30\nيوم عمل\nمدد معالجة تقريبية أوضح حسب كل وجهة واكتمال الملف.\n360°\nدعم كامل\nمراجعة ملف، حجوزات، إرشاد، ومتابعة حتى مرحلة التقديم.\nhttp://127.0.0.1:8000/visas\nابحث الآن\nservice_type\nنوع الخدمة\nاختر نوع الخدمة\nتأشيرة سياحية\nزيارة عائلية\nتأشيرة أعمال\nحجز موعد ومتابعة\ndestination\nالوجهة\nاختر الوجهة\nفرنسا\nhttp://127.0.0.1:8000/visa-country/france-visa\nألمانيا\nhttp://127.0.0.1:8000/visa-country/germany-visa\nإيطاليا\nhttp://127.0.0.1:8000/visa-country/italy-visa\nإسبانيا\nhttp://127.0.0.1:8000/visa-country/spain-visa\nهولندا\nhttp://127.0.0.1:8000/visa-country/netherlands-visa\nاليونان\nhttp://127.0.0.1:8000/visa-country/greece-visa\nأمريكا\nhttp://127.0.0.1:8000/visa-country/usa-visa\nالإمارات\nhttp://127.0.0.1:8000/visa-country/uae-visa\nتركيا\nhttp://127.0.0.1:8000/visa-country/turkey-visa\nكندا\nhttp://127.0.0.1:8000/visa-country/canada-visa\nvisa_type\nنوع التأشيرة\nاختر نوع التأشيرة\nشنغن قصيرة الإقامة\nرحلات أعمال\nمتعددة السفرات\nservice-featured\nالوجهات الأكثر طلبًا\nأشهر وجهات التأشيرات\nبطاقات مختارة لوجهات يطلبها العملاء باستمرار مع معلومات سريعة ورابط مباشر للتفاصيل.\n3600\nتأشيرة شنغن قصيرة الإقامة\nتتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.\nالأكثر طلبًا\nعرض التفاصيل\nحوالي 15 إلى 30 يوم عمل\nشنغن\nإقامة قصيرة\nلماذا Travel Wave\nلماذا يختارنا العملاء في خدمات التأشيرات\nخدمة واضحة ومرتبة تمنح العميل ثقة أعلى وتجربة أكثر احترافية في كل خطوة.\n01\nمراجعة المستندات\nفحص الملف وتحديد النواقص ونقاط التحسين قبل التقديم.\n02\nمتابعة الملف\nمتابعة منظمة لكل مرحلة من التجهيز وحتى ما بعد التقديم.\n03\nتنسيق الحجوزات\nمساعدة في ترتيب الطيران والفنادق بما يدعم ملف الرحلة.\n04\nتنظيم برنامج الرحلة\nبناء تصور أوضح للرحلة والمدة والهدف بما يعزز الملف.\n05\nسرعة في التنفيذ\nخطوات أوضح وتجهيز أسرع للنماذج والمستندات الأساسية.\n06\nدعم كامل حتى التقديم\nإرشاد واضح وحجز موعد ومتابعة مستمرة حتى آخر خطوة.\nخدمات داعمة\nباقات خدمة مرنة حسب احتياجك\nاختر مستوى الدعم الأنسب لرحلتك ونوع التأشيرة المطلوبة.\nمراجعة الملف قبل التقديم\nللملفات الجاهزة التي تحتاج مراجعة دقيقة\nتنسيق بيانات الرحلة\nملاحظات واضحة قبل التقديم\nيحدد حسب الوجهة وحجم الملف\nاطلب الخدمة\nخدمة تجهيز كاملة\nمن أول خطوة حتى ترتيب المستندات الأساسية\nإرشاد كامل\nتنظيم الملف\nتنسيق الحجوزات والمتطلبات\nخطة مرنة حسب نوع التأشيرة\nدعم المواعيد والمتابعة\nلمن يحتاج دعماً عملياً في الموعد والإجراءات\nحجز موعد إذا أمكن\nمتابعة الخطوات\nإجابات أوضح قبل التقديم\nيحدد حسب الوجهة والمركز\nخطوات واضحة\nكيف تسير الخدمة معنا\nرحلة منظمة تمنحك وضوحًا أكبر من أول تواصل وحتى مرحلة التقديم والمتابعة.\nنحدد معك الدولة المناسبة ونوع التأشيرة الأنسب لسبب السفر.\nأرسل المستندات\nتشاركنا المستندات الأساسية والبيانات المهمة الخاصة بالرحلة.\nمراجعة الملف\nنفحص الملف ونوضح المطلوب استكماله أو تحسينه قبل التقديم.\nحجز الموعد\nنرتب خطوة الموعد ونجهزك لما قبل التقديم حسب الوجهة.\nالتقديم والمتابعة\nتستكمل التقديم بثقة مع متابعة أوضح لحالة الطلب والخطوات التالية.\nشبكة الوجهات\nوجهات يمكنك البدء بها الآن\nمجموعة من أشهر الوجهات الخارجية التي يمكن إدارة ملفها من خلال Travel Wave.\n- تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.\nتأشيرة ألمانيا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nتأشيرة إيطاليا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nخدمة تأشيرة\nخدمة متكاملة لتجهيز الملف وتوضيح الخطوات الأساسية قبل التقديم.\nدعم تأشيرة الإمارات مع تخطيط عملي وإرشاد للمستندات.\nدعم تأشيرة تركيا مع تخطيط عملي وإرشاد للمستندات.\nدعم تأشيرة كندا مع تخطيط عملي وإرشاد للمستندات.\nمعلومات سريعة\nأهم ما يحتاجه العميل قبل البدء\nمدة المعالجة\nغالبًا من 15 إلى 30 يوم عمل حسب الوجهة والموسم.\nnavy\nالرسوم\nتتحدد حسب الدولة، الرسوم القنصلية، ومستوى الخدمة المطلوب.\nroyal\nالمستندات المطلوبة\nجواز سفر، صور، مستندات مالية، حجوزات، وأوراق داعمة حسب الحالة.\namber\nسهولة الملف\nترتفع مع اكتمال البيانات وتناسق المستندات وخطة الرحلة.\nslate\nابدأ بثقة\nابدأ ملف التأشيرة بخطوات أوضح ودعم أكثر احترافية\nنمنحك تجربة أكثر ترتيبًا وراحة من أول استشارة حتى تجهيز الملف والحجوزات وشرح الرسوم والمدة المتوقعة.\nاحجز استشارتك الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nlight-outline\nالأسئلة الشائعة\nإجابات سريعة قبل أن تبدأ\nمجموعة أسئلة شائعة تساعد العميل على فهم ما ينتظره قبل تجهيز الملف.\nما مدة استخراج التأشيرة؟\nتختلف حسب الدولة والموسم واكتمال الملف، لكن كثيرًا من الوجهات تقع بين 15 و30 يوم عمل.\nما الأوراق المطلوبة؟\nيعتمد ذلك على الوجهة ونوع التأشيرة، لكن الأساس يشمل الجواز والصور والمستندات المالية والحجوزات.\nهل يوجد متابعة بعد التقديم؟\nنعم، يتم إرشادك لما بعد التقديم مع متابعة أوضح للخطوات التالية عند الحاجة.\nهل يمكن المساعدة في الحجوزات؟\nنعم، يمكن المساعدة في تنسيق الطيران والفنادق بما يناسب ملف الرحلة.\nما أفضل وقت للتقديم؟\nكلما كان التقديم مبكرًا كان أفضل، خصوصًا قبل المواسم المزدحمة أو عند محدودية المواعيد.\nطلب استشارة\nابدأ معنا بطلب واضح وسريع\nاترك بياناتك وسيتواصل معك فريق Travel Wave لتحديد الخطوات المناسبة للوجهة ونوع التأشيرة.\nاستشارة أولية مباشرة\nتوجيه للمستندات والحجوزات\nمتابعة بعد إرسال الطلب\nvisa\nExternal Visa Services\nأرسل الطلب\nfull_name\nالاسم\ntext\nاكتب الاسم الكامل\nphone\nرقم الهاتف\nرقم الهاتف أو واتساب\nselect\nاختر النوع\nmessage\nملاحظات\ntextarea\nاكتب أي تفاصيل مهمة عن الرحلة أو الملف', 'http://127.0.0.1:8000/visas', '{\"page_key\":\"visas\"}', 18, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(231, 'service_pages', NULL, 'domestic', 'en', 'السياحة الداخلية', 'استمتع بأفضل الوجهات المحلية مع برامج منظمة وتجارب مريحة تجمع بين الاسترخاء، الترفيه، وسهولة الحجز من خلال Travel Wave.', 'domestic\nالسياحة الداخلية\nرحلات داخلية بروح مميزة\nاستمتع بأفضل الوجهات المحلية مع برامج منظمة وتجارب مريحة تجمع بين الاسترخاء، الترفيه، وسهولة الحجز من خلال Travel Wave.\nاحجز رحلتك الآن\n#service-contact\nاستعرض البرامج\n#service-packages\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\ndestination\nالوجهة\nشرم الشيخ\nhttp://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh\nمرسى علم\nhttp://127.0.0.1:8000/domestic-tourism/marsa-alam\nالغردقة\nhttp://127.0.0.1:8000/domestic-tourism/hurghada\nدهب\nhttp://127.0.0.1:8000/domestic-tourism/dahab\nالساحل الشمالي\nhttp://127.0.0.1:8000/domestic-tourism/north-coast\nالأقصر وأسوان\nhttp://127.0.0.1:8000/domestic-tourism/luxor-aswan\nduration\nمدة الرحلة\n3 أيام\nhttp://127.0.0.1:8000/domestic-tourism\n4 أيام\n5 أيام\nأسبوع كامل\ntrip_type\nنوع الرحلة\nعائلية\nشواطئ\nاسترخاء ومنتجعات\nثقافية\nابحث الآن\nوجهات داخلية مميزة\nأشهر الوجهات المحلية\nوجهات مختارة بعناية تجمع بين الراحة والتنظيم والبرامج المناسبة للأفراد والعائلات.\nرحلات بحرية واستجمام\n3 ليالٍ / 4 أيام\nعرض التفاصيل\nرحلة داخلية\nإجازات عائلية مرنة\nإقامة وتنقلات\nالأكثر طلباً\nبرامج موسمية مميزة\nأسعار تبدأ من 4,500 جنيه\nشامل الإقامة\nإقامة مريحة وتجارب متنوعة\nحجوزات مؤكدة\nبرنامج مرن\nرحلات قصيرة وسريعة\nعروض موسمية\nوجهة مثالية للاسترخاء\nبرامج مرنة\nلماذا تختار برامجنا الداخلية؟\n01\nبرامج متنوعة\nخيارات متعددة تناسب الأزواج والعائلات والمجموعات وبرامج الراحة السريعة.\n02\nتنظيم كامل\nترتيب شامل للإقامة والتنقلات والتفاصيل الأساسية لتجربة أكثر راحة.\n03\nحجوزات مضمونة\nتأكيدات واضحة للفنادق والخدمات وفق البرنامج المختار.\n04\nأسعار مناسبة\nباقات مدروسة تجمع بين القيمة والجودة والمرونة.\n05\nمتابعة مستمرة\nفريقنا يتابع معك من لحظة الحجز وحتى بدء الرحلة.\n06\nفرص متجددة على الوجهات المطلوبة في أفضل المواسم.\nبرامج داخلية مميزة\nباقة شرم الشيخ الذهبية\n4 أيام / 3 ليالٍ\nفندق 5 نجوم\nإفطار وعشاء\nتنقلات داخلية\nتبدأ من 6,900 جنيه\nعرض الباقة\nباقة الغردقة العائلية\n5 أيام / 4 ليالٍ\nبرنامج عائلي\nشاطئ خاص\nأنشطة يومية\nتبدأ من 8,250 جنيه\nباقة الأقصر وأسوان\n6 أيام / 5 ليالٍ\nمزج ثقافي وترفيهي\nإقامة مريحة\nبرنامج منظم\nتبدأ من 9,800 جنيه\nاختر الوجهة\nحدد البرنامج\nأكمل الحجز\nاستلم التأكيد\nاستعد للرحلة\nخطوات الحجز\nوجهات سياحية داخلية\nرحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.\nإقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.\nوجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.\nبرامج دهب مع إقامة مرنة ودعم قبل الحجز.\nبرامج الساحل الشمالي مع إقامة مرنة ودعم قبل الحجز.\nبرامج الأقصر وأسوان مع إقامة مرنة ودعم قبل الحجز.\nأفضل أوقات السفر\nالربيع والصيف والعطلات الطويلة تعتبر من أفضل الفترات للبرامج الداخلية.\nمدة البرامج\nمن رحلات قصيرة لثلاثة أيام حتى برامج كاملة لأسبوع أو أكثر.\nطرق الحجز\nعن طريق الهاتف أو الواتساب أو طلب الحجز المباشر من الصفحة.\nالعروض المتاحة\nعروض موسمية وبرامج مخصصة للمجموعات والعائلات.\nرحلتك القادمة تبدأ هنا\nاحجز رحلتك الداخلية القادمة الآن\nاستمتع ببرنامج محلي منظم وخيارات إقامة مميزة وتجربة حجز أكثر راحة مع Travel Wave.\nاحجز الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nما أفضل الوجهات المتاحة؟\nتختلف الأفضلية حسب نوع الرحلة، لكن شرم الشيخ والغردقة والساحل الشمالي والأقصر من الوجهات الأكثر طلباً.\nهل البرامج تشمل الإقامة؟\nنعم، كثير من البرامج تشمل الإقامة ويمكن توضيح مستوى الفندق والخدمات عند الاختيار.\nهل يوجد رحلات عائلية؟\nنعم، تتوفر برامج مصممة خصيصاً للعائلات مع خيارات أكثر مرونة في الإقامة والأنشطة.\nهل يمكن تعديل البرنامج؟\nفي كثير من الحالات يمكن تكييف البرنامج وفق المدة والميزانية ونوع الرحلة.\nما طريقة الحجز؟\nيمكن طلب الحجز من خلال النموذج أو التواصل المباشر مع فريق Travel Wave.\nابدأ حجز رحلتك الداخلية\nأرسل بياناتك وسنساعدك في اختيار الوجهة والبرنامج الأنسب لك.\nاقتراح أفضل برنامج\nتنسيق الحجز والإقامة\nمتابعة حتى التأكيد النهائي\nDomestic Tourism\nfull_name\nالاسم\ntext\nphone\nرقم الهاتف\nالوجهة المطلوبة\nselect\ntravelers_count\nعدد الأفراد\nnumber\ntravel_date\nتاريخ السفر\ndate\nmessage\nملاحظات\ntextarea', 'http://127.0.0.1:8000/domestic-tourism', '{\"page_key\":\"domestic\"}', 19, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(232, 'service_pages', NULL, 'domestic', 'ar', 'السياحة الداخلية', 'استمتع بأفضل الوجهات المحلية مع برامج منظمة وتجارب مريحة تجمع بين الاسترخاء، الترفيه، وسهولة الحجز من خلال Travel Wave.', 'domestic\nالسياحة الداخلية\nرحلات داخلية بروح مميزة\nاستمتع بأفضل الوجهات المحلية مع برامج منظمة وتجارب مريحة تجمع بين الاسترخاء، الترفيه، وسهولة الحجز من خلال Travel Wave.\nاحجز رحلتك الآن\n#service-contact\nاستعرض البرامج\n#service-packages\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\ndestination\nالوجهة\nشرم الشيخ\nhttp://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh\nمرسى علم\nhttp://127.0.0.1:8000/domestic-tourism/marsa-alam\nالغردقة\nhttp://127.0.0.1:8000/domestic-tourism/hurghada\nدهب\nhttp://127.0.0.1:8000/domestic-tourism/dahab\nالساحل الشمالي\nhttp://127.0.0.1:8000/domestic-tourism/north-coast\nالأقصر وأسوان\nhttp://127.0.0.1:8000/domestic-tourism/luxor-aswan\nduration\nمدة الرحلة\n3 أيام\nhttp://127.0.0.1:8000/domestic-tourism\n4 أيام\n5 أيام\nأسبوع كامل\ntrip_type\nنوع الرحلة\nعائلية\nشواطئ\nاسترخاء ومنتجعات\nثقافية\nابحث الآن\nوجهات داخلية مميزة\nأشهر الوجهات المحلية\nوجهات مختارة بعناية تجمع بين الراحة والتنظيم والبرامج المناسبة للأفراد والعائلات.\nرحلات بحرية واستجمام\n3 ليالٍ / 4 أيام\nعرض التفاصيل\nرحلة داخلية\nإجازات عائلية مرنة\nإقامة وتنقلات\nالأكثر طلباً\nبرامج موسمية مميزة\nأسعار تبدأ من 4,500 جنيه\nشامل الإقامة\nإقامة مريحة وتجارب متنوعة\nحجوزات مؤكدة\nبرنامج مرن\nرحلات قصيرة وسريعة\nعروض موسمية\nوجهة مثالية للاسترخاء\nبرامج مرنة\nلماذا تختار برامجنا الداخلية؟\n01\nبرامج متنوعة\nخيارات متعددة تناسب الأزواج والعائلات والمجموعات وبرامج الراحة السريعة.\n02\nتنظيم كامل\nترتيب شامل للإقامة والتنقلات والتفاصيل الأساسية لتجربة أكثر راحة.\n03\nحجوزات مضمونة\nتأكيدات واضحة للفنادق والخدمات وفق البرنامج المختار.\n04\nأسعار مناسبة\nباقات مدروسة تجمع بين القيمة والجودة والمرونة.\n05\nمتابعة مستمرة\nفريقنا يتابع معك من لحظة الحجز وحتى بدء الرحلة.\n06\nفرص متجددة على الوجهات المطلوبة في أفضل المواسم.\nبرامج داخلية مميزة\nباقة شرم الشيخ الذهبية\n4 أيام / 3 ليالٍ\nفندق 5 نجوم\nإفطار وعشاء\nتنقلات داخلية\nتبدأ من 6,900 جنيه\nعرض الباقة\nباقة الغردقة العائلية\n5 أيام / 4 ليالٍ\nبرنامج عائلي\nشاطئ خاص\nأنشطة يومية\nتبدأ من 8,250 جنيه\nباقة الأقصر وأسوان\n6 أيام / 5 ليالٍ\nمزج ثقافي وترفيهي\nإقامة مريحة\nبرنامج منظم\nتبدأ من 9,800 جنيه\nاختر الوجهة\nحدد البرنامج\nأكمل الحجز\nاستلم التأكيد\nاستعد للرحلة\nخطوات الحجز\nوجهات سياحية داخلية\nرحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.\nإقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.\nوجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.\nبرامج دهب مع إقامة مرنة ودعم قبل الحجز.\nبرامج الساحل الشمالي مع إقامة مرنة ودعم قبل الحجز.\nبرامج الأقصر وأسوان مع إقامة مرنة ودعم قبل الحجز.\nأفضل أوقات السفر\nالربيع والصيف والعطلات الطويلة تعتبر من أفضل الفترات للبرامج الداخلية.\nمدة البرامج\nمن رحلات قصيرة لثلاثة أيام حتى برامج كاملة لأسبوع أو أكثر.\nطرق الحجز\nعن طريق الهاتف أو الواتساب أو طلب الحجز المباشر من الصفحة.\nالعروض المتاحة\nعروض موسمية وبرامج مخصصة للمجموعات والعائلات.\nرحلتك القادمة تبدأ هنا\nاحجز رحلتك الداخلية القادمة الآن\nاستمتع ببرنامج محلي منظم وخيارات إقامة مميزة وتجربة حجز أكثر راحة مع Travel Wave.\nاحجز الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nما أفضل الوجهات المتاحة؟\nتختلف الأفضلية حسب نوع الرحلة، لكن شرم الشيخ والغردقة والساحل الشمالي والأقصر من الوجهات الأكثر طلباً.\nهل البرامج تشمل الإقامة؟\nنعم، كثير من البرامج تشمل الإقامة ويمكن توضيح مستوى الفندق والخدمات عند الاختيار.\nهل يوجد رحلات عائلية؟\nنعم، تتوفر برامج مصممة خصيصاً للعائلات مع خيارات أكثر مرونة في الإقامة والأنشطة.\nهل يمكن تعديل البرنامج؟\nفي كثير من الحالات يمكن تكييف البرنامج وفق المدة والميزانية ونوع الرحلة.\nما طريقة الحجز؟\nيمكن طلب الحجز من خلال النموذج أو التواصل المباشر مع فريق Travel Wave.\nابدأ حجز رحلتك الداخلية\nأرسل بياناتك وسنساعدك في اختيار الوجهة والبرنامج الأنسب لك.\nاقتراح أفضل برنامج\nتنسيق الحجز والإقامة\nمتابعة حتى التأكيد النهائي\nDomestic Tourism\nfull_name\nالاسم\ntext\nphone\nرقم الهاتف\nالوجهة المطلوبة\nselect\ntravelers_count\nعدد الأفراد\nnumber\ntravel_date\nتاريخ السفر\ndate\nmessage\nملاحظات\ntextarea', 'http://127.0.0.1:8000/domestic-tourism', '{\"page_key\":\"domestic\"}', 20, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(233, 'service_pages', NULL, 'flights', 'en', 'الطيران', 'نوفّر لك خيارات محلية ودولية بترتيب أسرع ودعم أوضح، لتصل إلى الرحلة المناسبة بالسعر والخدمة التي تناسبك.', 'flights\nالطيران\nحلول حجز سريعة وموثوقة\nحجوزات الطيران\nنوفّر لك خيارات محلية ودولية بترتيب أسرع ودعم أوضح، لتصل إلى الرحلة المناسبة بالسعر والخدمة التي تناسبك.\nاحجز رحلتك\n#service-contact\nاستعرض الوجهات\n#service-popular\nfrom\nمن\nالقاهرة\nhttp://127.0.0.1:8000/flights\nالإسكندرية\nجدة\nدبي\nto\nإلى\nإسطنبول\nباريس\nلندن\ntravel_date\nتاريخ السفر\nهذا الأسبوع\nالأسبوع القادم\nهذا الشهر\ntravelers\nعدد المسافرين\n1 مسافر\n2 مسافر\n3 مسافرين\n4+ مسافرين\nابحث عن رحلة\nخطوط مطلوبة\nأشهر مسارات الطيران\nمسارات متكررة وحجوزات مناسبة للأفراد والعائلات ورحلات الأعمال.\nالقاهرة → جدة\nرحلات مباشرة وموسمية\nأسعار تبدأ من 8,900 جنيه\nالأكثر طلباً\nعرض التفاصيل\nالقاهرة → دبي\nخيارات متنوعة على مدار الأسبوع\nأسعار تبدأ من 9,500 جنيه\nمرن\nالقاهرة → الرياض\nحلول مناسبة لرحلات العمل والزيارات\nأسعار تبدأ من 8,700 جنيه\nسريع\nالقاهرة → إسطنبول\nوجهة شائعة للسياحة والتسوق\nأسعار تبدأ من 10,200 جنيه\nرحلات يومية\nالقاهرة → باريس\nخيارات أوروبية مميزة\nأسعار تبدأ من 16,800 جنيه\nدولي\nالقاهرة → لندن\nرحلات منتظمة مع خيارات متعددة\nأسعار تبدأ من 18,500 جنيه\nممتاز\nلماذا تحجز الطيران معنا؟\n01\nأفضل الخيارات\nنعرض لك المسارات والبدائل المناسبة حسب الموعد والميزانية.\n02\nدعم سريع\nاستجابة أسرع للاستفسارات والحجوزات والتعديلات الممكنة.\n03\nأسعار تنافسية\nخيارات مدروسة ومناسبة للأفراد والعائلات والسفر المتكرر.\n04\nمتابعة الحجز\nنؤكد معك البيانات الأساسية ونرتب خطوات الحجز بوضوح.\n05\nحجوزات مرنة\nمساعدة في اختيار الأنسب بين الرحلات المباشرة وغير المباشرة.\n06\nخدمة موثوقة\nتنفيذ أدق للحجوزات مع وضوح أكبر في التفاصيل والمتطلبات.\nخدمات الطيران\nرحلات داخلية\nللمدن المحلية والانتقالات السريعة\nمواعيد متنوعة\nحجز واضح\nخيارات اقتصادية\nأسعار مرنة حسب الموعد\nاطلب الخدمة\nرحلات دولية\nللسياحة والأعمال والزيارات\nوجهات متعددة\nدعم في الاختيار\nتنسيق أوضح\nعروض حسب الوجهة\nحجوزات عائلية وأعمال\nتنسيق أفضل للمجموعات والملفات المنظمة\nأسماء وبيانات دقيقة\nحلول مرنة\nتسعير حسب العدد والخدمة\nاختر خط السير\nحدد الموعد\nاختر عدد المسافرين\nأكد البيانات\nاستلم الحجز\nخطوات حجز الطيران\nخدمات الطيران المتاحة\nمحلي\nحلول مرنة للانتقال بين المدن المحلية بسرعة ووضوح.\nوجهات خارجية متعددة مع خيارات تناسب التوقيت والميزانية.\nحجوزات ذهاب وعودة\nمتكامل\nتنسيق أفضل لرحلات الذهاب والعودة في نفس الحجز.\nحجوزات عائلية\nعائلة\nخيارات مناسبة للعائلات مع دعم أوضح للبيانات والحجوزات.\nحجوزات أعمال\nأعمال\nحلول أسرع لرحلات العمل المتكررة والمواعيد الدقيقة.\nعروض موسمية\nعروض\nمتابعة أفضل للعروض المتاحة في الفترات المطلوبة.\nسياسات الحجز\nتختلف حسب شركة الطيران ونوع التذكرة وسياسة التغيير أو الإلغاء.\nالأمتعة\nيتم توضيح الأمتعة المسموح بها حسب المسار وشركة الطيران المختارة.\nالتعديلات\nبعض الحجوزات تسمح بالتعديل وفق شروط الناقل ونوع السعر.\nالعروض الخاصة\nتتوفر عروض على بعض الخطوط والمواعيد حسب التوفر الفعلي.\nاحجز بسرعة واطمئنان\nابدأ حجز رحلة الطيران الآن\nاختر مسارك ودع Travel Wave ترتب لك الحجز بصورة أوضح وأسرع مع متابعة أفضل للتفاصيل.\nابدأ الحجز الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nهل يوجد حجز داخلي ودولي؟\nنعم، تتوفر خدمات لحجوزات الطيران المحلية والدولية حسب الوجهة المطلوبة.\nهل يمكن تعديل الحجز؟\nيعتمد ذلك على شركة الطيران ونوع التذكرة وسياسة التعديل المعتمدة.\nهل توجد عروض على الرحلات؟\nتتوفر عروض على بعض المسارات والمواسم حسب التوفر الفعلي وقت الحجز.\nهل أستطيع حجز رحلة لعائلة؟\nنعم، يمكن تنسيق حجوزات عائلية مع مراعاة عدد المسافرين ومتطلبات كل رحلة.\nما البيانات المطلوبة للحجز؟\nالاسم كما في الجواز أو الهوية، خط السير، الموعد، وعدد المسافرين هي أهم البيانات الأساسية.\nاطلب حجز الطيران المناسب\nأرسل بياناتك وسنساعدك في اختيار الرحلة الأنسب ومتابعة الحجز.\nترشيح أنسب الرحلات\nدعم في البيانات الأساسية\nمتابعة حتى تأكيد الحجز\nFlights\nfull_name\nالاسم\ntext\nphone\nرقم الهاتف\nnationality\ndestination\ndate\ntravelers_count\nnumber\nmessage\nملاحظات\ntextarea', 'http://127.0.0.1:8000/flights', '{\"page_key\":\"flights\"}', 21, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(234, 'service_pages', NULL, 'flights', 'ar', 'الطيران', 'نوفّر لك خيارات محلية ودولية بترتيب أسرع ودعم أوضح، لتصل إلى الرحلة المناسبة بالسعر والخدمة التي تناسبك.', 'flights\nالطيران\nحلول حجز سريعة وموثوقة\nحجوزات الطيران\nنوفّر لك خيارات محلية ودولية بترتيب أسرع ودعم أوضح، لتصل إلى الرحلة المناسبة بالسعر والخدمة التي تناسبك.\nاحجز رحلتك\n#service-contact\nاستعرض الوجهات\n#service-popular\nfrom\nمن\nالقاهرة\nhttp://127.0.0.1:8000/flights\nالإسكندرية\nجدة\nدبي\nto\nإلى\nإسطنبول\nباريس\nلندن\ntravel_date\nتاريخ السفر\nهذا الأسبوع\nالأسبوع القادم\nهذا الشهر\ntravelers\nعدد المسافرين\n1 مسافر\n2 مسافر\n3 مسافرين\n4+ مسافرين\nابحث عن رحلة\nخطوط مطلوبة\nأشهر مسارات الطيران\nمسارات متكررة وحجوزات مناسبة للأفراد والعائلات ورحلات الأعمال.\nالقاهرة → جدة\nرحلات مباشرة وموسمية\nأسعار تبدأ من 8,900 جنيه\nالأكثر طلباً\nعرض التفاصيل\nالقاهرة → دبي\nخيارات متنوعة على مدار الأسبوع\nأسعار تبدأ من 9,500 جنيه\nمرن\nالقاهرة → الرياض\nحلول مناسبة لرحلات العمل والزيارات\nأسعار تبدأ من 8,700 جنيه\nسريع\nالقاهرة → إسطنبول\nوجهة شائعة للسياحة والتسوق\nأسعار تبدأ من 10,200 جنيه\nرحلات يومية\nالقاهرة → باريس\nخيارات أوروبية مميزة\nأسعار تبدأ من 16,800 جنيه\nدولي\nالقاهرة → لندن\nرحلات منتظمة مع خيارات متعددة\nأسعار تبدأ من 18,500 جنيه\nممتاز\nلماذا تحجز الطيران معنا؟\n01\nأفضل الخيارات\nنعرض لك المسارات والبدائل المناسبة حسب الموعد والميزانية.\n02\nدعم سريع\nاستجابة أسرع للاستفسارات والحجوزات والتعديلات الممكنة.\n03\nأسعار تنافسية\nخيارات مدروسة ومناسبة للأفراد والعائلات والسفر المتكرر.\n04\nمتابعة الحجز\nنؤكد معك البيانات الأساسية ونرتب خطوات الحجز بوضوح.\n05\nحجوزات مرنة\nمساعدة في اختيار الأنسب بين الرحلات المباشرة وغير المباشرة.\n06\nخدمة موثوقة\nتنفيذ أدق للحجوزات مع وضوح أكبر في التفاصيل والمتطلبات.\nخدمات الطيران\nرحلات داخلية\nللمدن المحلية والانتقالات السريعة\nمواعيد متنوعة\nحجز واضح\nخيارات اقتصادية\nأسعار مرنة حسب الموعد\nاطلب الخدمة\nرحلات دولية\nللسياحة والأعمال والزيارات\nوجهات متعددة\nدعم في الاختيار\nتنسيق أوضح\nعروض حسب الوجهة\nحجوزات عائلية وأعمال\nتنسيق أفضل للمجموعات والملفات المنظمة\nأسماء وبيانات دقيقة\nحلول مرنة\nتسعير حسب العدد والخدمة\nاختر خط السير\nحدد الموعد\nاختر عدد المسافرين\nأكد البيانات\nاستلم الحجز\nخطوات حجز الطيران\nخدمات الطيران المتاحة\nمحلي\nحلول مرنة للانتقال بين المدن المحلية بسرعة ووضوح.\nوجهات خارجية متعددة مع خيارات تناسب التوقيت والميزانية.\nحجوزات ذهاب وعودة\nمتكامل\nتنسيق أفضل لرحلات الذهاب والعودة في نفس الحجز.\nحجوزات عائلية\nعائلة\nخيارات مناسبة للعائلات مع دعم أوضح للبيانات والحجوزات.\nحجوزات أعمال\nأعمال\nحلول أسرع لرحلات العمل المتكررة والمواعيد الدقيقة.\nعروض موسمية\nعروض\nمتابعة أفضل للعروض المتاحة في الفترات المطلوبة.\nسياسات الحجز\nتختلف حسب شركة الطيران ونوع التذكرة وسياسة التغيير أو الإلغاء.\nالأمتعة\nيتم توضيح الأمتعة المسموح بها حسب المسار وشركة الطيران المختارة.\nالتعديلات\nبعض الحجوزات تسمح بالتعديل وفق شروط الناقل ونوع السعر.\nالعروض الخاصة\nتتوفر عروض على بعض الخطوط والمواعيد حسب التوفر الفعلي.\nاحجز بسرعة واطمئنان\nابدأ حجز رحلة الطيران الآن\nاختر مسارك ودع Travel Wave ترتب لك الحجز بصورة أوضح وأسرع مع متابعة أفضل للتفاصيل.\nابدأ الحجز الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nهل يوجد حجز داخلي ودولي؟\nنعم، تتوفر خدمات لحجوزات الطيران المحلية والدولية حسب الوجهة المطلوبة.\nهل يمكن تعديل الحجز؟\nيعتمد ذلك على شركة الطيران ونوع التذكرة وسياسة التعديل المعتمدة.\nهل توجد عروض على الرحلات؟\nتتوفر عروض على بعض المسارات والمواسم حسب التوفر الفعلي وقت الحجز.\nهل أستطيع حجز رحلة لعائلة؟\nنعم، يمكن تنسيق حجوزات عائلية مع مراعاة عدد المسافرين ومتطلبات كل رحلة.\nما البيانات المطلوبة للحجز؟\nالاسم كما في الجواز أو الهوية، خط السير، الموعد، وعدد المسافرين هي أهم البيانات الأساسية.\nاطلب حجز الطيران المناسب\nأرسل بياناتك وسنساعدك في اختيار الرحلة الأنسب ومتابعة الحجز.\nترشيح أنسب الرحلات\nدعم في البيانات الأساسية\nمتابعة حتى تأكيد الحجز\nFlights\nfull_name\nالاسم\ntext\nphone\nرقم الهاتف\nnationality\ndestination\ndate\ntravelers_count\nnumber\nmessage\nملاحظات\ntextarea', 'http://127.0.0.1:8000/flights', '{\"page_key\":\"flights\"}', 22, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(235, 'service_pages', NULL, 'hotels', 'en', 'الفنادق', 'نساعدك في حجز الإقامة المناسبة بمستوى راحة أعلى وخيارات متنوعة وأسعار مدروسة تناسب الرحلات الفردية والعائلية.', 'hotels\nالفنادق\nإقامة أذكى وأكثر راحة\nحجوزات الفنادق\nنساعدك في حجز الإقامة المناسبة بمستوى راحة أعلى وخيارات متنوعة وأسعار مدروسة تناسب الرحلات الفردية والعائلية.\nاحجز إقامتك الآن\n#service-contact\nاستعرض الفنادق\n#service-popular\ndestination\nالوجهة\nشرم الشيخ\nhttp://127.0.0.1:8000/hotels\nالغردقة\nدبي\nمكة\nإسطنبول\nباريس\ncheck_in\nتاريخ الوصول\nهذا الأسبوع\nالأسبوع القادم\nهذا الشهر\ncheck_out\nتاريخ المغادرة\nبعد 2 ليلة\nبعد 4 ليالٍ\nبعد أسبوع\nrooms\nعدد الغرف\nغرفة واحدة\nغرفتان\n3 غرف\nguests\nعدد النزلاء\n2 نزلاء\n4 نزلاء\n6+ نزلاء\nابحث الآن\nإقامات مختارة\nفنادق ووجهات مميزة\nمجموعة مختارة من الوجهات الفندقية المطلوبة بخيارات إقامة متنوعة ومرنة.\nفنادق شرم الشيخ\nمنتجعات شاطئية وإقامات مريحة\nتقييمات مرتفعة وخيارات عائلية\nشاطئي\nعرض التفاصيل\nفنادق الغردقة\nإقامة مناسبة للاسترخاء والأنشطة البحرية\nخيارات متنوعة حسب الميزانية\nالأكثر طلباً\nفنادق دبي\nإقامة راقية في مواقع مميزة\nخيارات أعمال وترفيه\nفاخر\nفنادق مكة\nحلول إقامة مريحة بقرب مناسب\nحجوزات منظمة ومرنة\nقرب أفضل\nفنادق إسطنبول\nتنوع كبير في الفئات والمناطق\nإقامات عائلية وفردية\nمتنوع\nفنادق باريس\nخيارات إقامة مناسبة للرحلات الأوروبية\nتأكيدات سريعة وخيارات متعددة\nأوروبي\nلماذا تحجز الفنادق معنا؟\n01\nخيارات متنوعة\nمجموعة أوسع من الفنادق والمنتجعات والإقامات المناسبة لفئات مختلفة.\n02\nأفضل الأسعار\nترشيح الخيارات الأكثر توازناً بين السعر والموقع والخدمة.\n03\nمواقع مميزة\nمساعدة في اختيار الفندق بحسب المنطقة والاحتياج الفعلي للرحلة.\n04\nدعم في الحجز\nتأكيد أوضح لبيانات الحجز وتفاصيل الإقامة المطلوبة.\n05\nإقامة مريحة\nحلول مناسبة للعائلات والأفراد والرحلات العملية والترفيهية.\n06\nتأكيد سريع\nتجهيز أسرع للحجز حسب التوفر وخيارات الغرف المطلوبة.\nفئات الإقامة\nفنادق اقتصادية\nحلول مناسبة للميزانيات العملية\nتكلفة أفضل\nمواقع مناسبة\nحجز واضح\nأسعار تبدأ حسب الوجهة\nاطلب الخدمة\nفنادق 4 و5 نجوم\nمستوى أعلى من الراحة والخدمة\nمرافق أفضل\nخدمة مميزة\nخيارات حسب الموسم\nمنتجعات وشقق فندقية\nحلول عائلية وإقامات أطول\nمساحات أوسع\nمرونة أكبر\nخيارات عائلية\nتسعير حسب المدة\nاختر الوجهة\nحدد التواريخ\nاختر نوع الإقامة\nأكد الحجز\nاستلم التأكيد\nخطوات حجز الفندق\nأنواع الفنادق والإقامات\nاقتصادي\nحلول إقامة مناسبة للرحلات القصيرة والميزانيات العملية.\nفنادق 4 نجوم\nمريح\nتوازن جيد بين السعر والموقع والخدمات الأساسية.\nفنادق 5 نجوم\nإقامة راقية وتجربة أكثر فخامة وراحة.\nمنتجعات\nاستجمام\nخيارات مناسبة للعطلات والاسترخاء والرحلات الشاطئية.\nشقق فندقية\nمرن\nحلول أوسع للإقامات العائلية أو الطويلة نسبياً.\nإقامات عائلية\nعائلي\nخيارات أكثر ملاءمة للعائلات وعدد النزلاء الأكبر.\nأنواع الإقامة\nفنادق اقتصادية، 4 و5 نجوم، منتجعات، وشقق فندقية.\nسياسة الحجز\nتختلف حسب الفندق ونوع السعر وشروط الإلغاء أو التعديل.\nالمزايا المتاحة\nتتغير حسب الفندق وتشمل الإفطار أو الإطلالة أو الموقع أو المرافق.\nخيارات الدفع\nتتحدد بحسب الحجز والتأكيد وسياسة الفندق أو المزود.\nإقامتك تبدأ من هنا\nاحجز إقامتك الآن بثقة وراحة\nاستفد من خيارات فندقية أوضح وأكثر مرونة مع Travel Wave، ودع فريقنا يساعدك في اختيار الأنسب.\nاحجز الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nهل تتوفر فنادق اقتصادية وفاخرة؟\nنعم، تتوفر فئات متعددة تناسب الميزانيات المختلفة ونوع الرحلة المطلوبة.\nهل يمكن الحجز لعائلات؟\nنعم، توجد خيارات مناسبة للعائلات من حيث الغرف والمساحة والخدمات.\nهل يوجد فنادق في وجهات متعددة؟\nنعم، يمكن المساعدة في حجوزات محلية وخارجية في وجهات متنوعة.\nهل الحجز مؤكد؟\nيتم توضيح حالة التوفر والتأكيد النهائي حسب الفندق المختار ووقت الحجز.\nهل يمكن المساعدة في اختيار الفندق المناسب؟\nنعم، نرشح لك الخيارات الأنسب حسب الوجهة والميزانية وعدد النزلاء ونوع الرحلة.\nابدأ طلب حجز الفندق\nأرسل بيانات الإقامة المطلوبة وسنساعدك في اختيار الفندق الأنسب لك.\nاقتراح أفضل خيارات الإقامة\nمقارنة أوضح بين البدائل\nمتابعة حتى تأكيد الحجز\nHotels\nfull_name\nالاسم\ntext\nphone\nرقم الهاتف\ntravel_date\ndate\nreturn_date\naccommodation_type\ntravelers_count\nnumber\nmessage\nملاحظات\ntextarea', 'http://127.0.0.1:8000/hotels', '{\"page_key\":\"hotels\"}', 23, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(236, 'service_pages', NULL, 'hotels', 'ar', 'الفنادق', 'نساعدك في حجز الإقامة المناسبة بمستوى راحة أعلى وخيارات متنوعة وأسعار مدروسة تناسب الرحلات الفردية والعائلية.', 'hotels\nالفنادق\nإقامة أذكى وأكثر راحة\nحجوزات الفنادق\nنساعدك في حجز الإقامة المناسبة بمستوى راحة أعلى وخيارات متنوعة وأسعار مدروسة تناسب الرحلات الفردية والعائلية.\nاحجز إقامتك الآن\n#service-contact\nاستعرض الفنادق\n#service-popular\ndestination\nالوجهة\nشرم الشيخ\nhttp://127.0.0.1:8000/hotels\nالغردقة\nدبي\nمكة\nإسطنبول\nباريس\ncheck_in\nتاريخ الوصول\nهذا الأسبوع\nالأسبوع القادم\nهذا الشهر\ncheck_out\nتاريخ المغادرة\nبعد 2 ليلة\nبعد 4 ليالٍ\nبعد أسبوع\nrooms\nعدد الغرف\nغرفة واحدة\nغرفتان\n3 غرف\nguests\nعدد النزلاء\n2 نزلاء\n4 نزلاء\n6+ نزلاء\nابحث الآن\nإقامات مختارة\nفنادق ووجهات مميزة\nمجموعة مختارة من الوجهات الفندقية المطلوبة بخيارات إقامة متنوعة ومرنة.\nفنادق شرم الشيخ\nمنتجعات شاطئية وإقامات مريحة\nتقييمات مرتفعة وخيارات عائلية\nشاطئي\nعرض التفاصيل\nفنادق الغردقة\nإقامة مناسبة للاسترخاء والأنشطة البحرية\nخيارات متنوعة حسب الميزانية\nالأكثر طلباً\nفنادق دبي\nإقامة راقية في مواقع مميزة\nخيارات أعمال وترفيه\nفاخر\nفنادق مكة\nحلول إقامة مريحة بقرب مناسب\nحجوزات منظمة ومرنة\nقرب أفضل\nفنادق إسطنبول\nتنوع كبير في الفئات والمناطق\nإقامات عائلية وفردية\nمتنوع\nفنادق باريس\nخيارات إقامة مناسبة للرحلات الأوروبية\nتأكيدات سريعة وخيارات متعددة\nأوروبي\nلماذا تحجز الفنادق معنا؟\n01\nخيارات متنوعة\nمجموعة أوسع من الفنادق والمنتجعات والإقامات المناسبة لفئات مختلفة.\n02\nأفضل الأسعار\nترشيح الخيارات الأكثر توازناً بين السعر والموقع والخدمة.\n03\nمواقع مميزة\nمساعدة في اختيار الفندق بحسب المنطقة والاحتياج الفعلي للرحلة.\n04\nدعم في الحجز\nتأكيد أوضح لبيانات الحجز وتفاصيل الإقامة المطلوبة.\n05\nإقامة مريحة\nحلول مناسبة للعائلات والأفراد والرحلات العملية والترفيهية.\n06\nتأكيد سريع\nتجهيز أسرع للحجز حسب التوفر وخيارات الغرف المطلوبة.\nفئات الإقامة\nفنادق اقتصادية\nحلول مناسبة للميزانيات العملية\nتكلفة أفضل\nمواقع مناسبة\nحجز واضح\nأسعار تبدأ حسب الوجهة\nاطلب الخدمة\nفنادق 4 و5 نجوم\nمستوى أعلى من الراحة والخدمة\nمرافق أفضل\nخدمة مميزة\nخيارات حسب الموسم\nمنتجعات وشقق فندقية\nحلول عائلية وإقامات أطول\nمساحات أوسع\nمرونة أكبر\nخيارات عائلية\nتسعير حسب المدة\nاختر الوجهة\nحدد التواريخ\nاختر نوع الإقامة\nأكد الحجز\nاستلم التأكيد\nخطوات حجز الفندق\nأنواع الفنادق والإقامات\nاقتصادي\nحلول إقامة مناسبة للرحلات القصيرة والميزانيات العملية.\nفنادق 4 نجوم\nمريح\nتوازن جيد بين السعر والموقع والخدمات الأساسية.\nفنادق 5 نجوم\nإقامة راقية وتجربة أكثر فخامة وراحة.\nمنتجعات\nاستجمام\nخيارات مناسبة للعطلات والاسترخاء والرحلات الشاطئية.\nشقق فندقية\nمرن\nحلول أوسع للإقامات العائلية أو الطويلة نسبياً.\nإقامات عائلية\nعائلي\nخيارات أكثر ملاءمة للعائلات وعدد النزلاء الأكبر.\nأنواع الإقامة\nفنادق اقتصادية، 4 و5 نجوم، منتجعات، وشقق فندقية.\nسياسة الحجز\nتختلف حسب الفندق ونوع السعر وشروط الإلغاء أو التعديل.\nالمزايا المتاحة\nتتغير حسب الفندق وتشمل الإفطار أو الإطلالة أو الموقع أو المرافق.\nخيارات الدفع\nتتحدد بحسب الحجز والتأكيد وسياسة الفندق أو المزود.\nإقامتك تبدأ من هنا\nاحجز إقامتك الآن بثقة وراحة\nاستفد من خيارات فندقية أوضح وأكثر مرونة مع Travel Wave، ودع فريقنا يساعدك في اختيار الأنسب.\nاحجز الآن\nتواصل واتساب\nhttps://wa.me/201000000000\nهل تتوفر فنادق اقتصادية وفاخرة؟\nنعم، تتوفر فئات متعددة تناسب الميزانيات المختلفة ونوع الرحلة المطلوبة.\nهل يمكن الحجز لعائلات؟\nنعم، توجد خيارات مناسبة للعائلات من حيث الغرف والمساحة والخدمات.\nهل يوجد فنادق في وجهات متعددة؟\nنعم، يمكن المساعدة في حجوزات محلية وخارجية في وجهات متنوعة.\nهل الحجز مؤكد؟\nيتم توضيح حالة التوفر والتأكيد النهائي حسب الفندق المختار ووقت الحجز.\nهل يمكن المساعدة في اختيار الفندق المناسب؟\nنعم، نرشح لك الخيارات الأنسب حسب الوجهة والميزانية وعدد النزلاء ونوع الرحلة.\nابدأ طلب حجز الفندق\nأرسل بيانات الإقامة المطلوبة وسنساعدك في اختيار الفندق الأنسب لك.\nاقتراح أفضل خيارات الإقامة\nمقارنة أوضح بين البدائل\nمتابعة حتى تأكيد الحجز\nHotels\nfull_name\nالاسم\ntext\nphone\nرقم الهاتف\ntravel_date\ndate\nreturn_date\naccommodation_type\ntravelers_count\nnumber\nmessage\nملاحظات\ntextarea', 'http://127.0.0.1:8000/hotels', '{\"page_key\":\"hotels\"}', 24, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(237, 'service_pages', NULL, 'about', 'en', 'من نحن', 'Travel Wave شريك موثوق في خدمات السفر والتأشيرات، نجمع بين التنظيم العملي، والمتابعة الواضحة، والتجربة الراقية التي تمنح العميل ثقة أكبر في كل خطوة.', 'rtl\nمن نحن | Travel Wave\nTravel Wave\nمن نحن\nTravel Wave شريك موثوق في خدمات السفر والتأشيرات، نجمع بين التنظيم العملي، والمتابعة الواضحة، والتجربة الراقية التي تمنح العميل ثقة أكبر في كل خطوة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nالرئيسية\nhttp://127.0.0.1:8000\nهوية Travel Wave\nرحلة مبنية على الوضوح والثقة\nنؤمن في Travel Wave أن خدمات السفر والتأشيرات لا يجب أن تكون معقدة أو مرهقة. لذلك نصمم تجربة أكثر ترتيبًا ووضوحًا لمساعدة العملاء في التأشيرات الخارجية، والسياحة الداخلية، وحجوزات الطيران، وحجوزات الفنادق، مع اهتمام حقيقي بالتفاصيل وجودة الخدمة.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nحلول متكاملة تبدأ من الاستفسار وحتى إتمام الخدمة.\nلغة واضحة وخطوات عملية تناسب احتياج كل عميل.\nتجربة أكثر احترافية في الحجوزات والملفات والمتابعة.\nأساس العلامة\nالرؤية والرسالة والقيم\nنصنع تجربة سفر أكثر رقيًا وتنظيمًا عبر خدمة واضحة، وتفاصيل مدروسة، واهتمام فعلي بما يحتاجه العميل.\ncol-md-6 col-xl-4\nvision\nر\nرؤيتنا\nأن تكون Travel Wave من الأسماء الأكثر ثقة في خدمات السفر والتأشيرات من خلال تجربة تجمع بين الفخامة والوضوح وسهولة التنفيذ.\nرسالتنا\nتبسيط رحلات العملاء وطلبات التأشيرات عبر تنظيم احترافي، وخيارات مناسبة، ودعم متواصل يرفع مستوى الثقة والراحة.\nق\nقيمنا\nالوضوح، والالتزام، والدقة، وسرعة الاستجابة، والاهتمام بالتفاصيل التي تصنع فارقًا حقيقيًا في جودة الخدمة.\nلماذا نحن\nلماذا يختارنا العملاء؟\nfeature\nخب\nخبرة في خدمات السفر والتأشيرات\nفهم أعمق لاحتياجات السفر والملفات والحجوزات يجعل التجربة أكثر سلاسة ووضوحًا.\nمت\nمتابعة دقيقة للملفات\nنهتم بالتفاصيل المهمة ونوضح للعميل ما ينقصه وما يحتاجه قبل أي خطوة أساسية.\nحج\nتنظيم احترافي للحجوزات\nتنسيق أفضل بين الفنادق والطيران والخدمات المساندة بما يتوافق مع الهدف من الرحلة.\nدع\nدعم مستمر للعملاء\nاستجابة أوضح ومتابعة أكثر قربًا لتقليل التردد ورفع مستوى الثقة.\nحل\nحلول متنوعة تناسب كل عميل\nنقترح خيارات مرنة تتناسب مع الميزانية، والوجهة، وطبيعة الخدمة المطلوبة.\nسر\nسرعة ووضوح في الإجراءات\nنرتب الأولويات ونقدم خطوات عملية تساعد العميل على التحرك بشكل أسرع وأكثر راحة.\nخدمات Travel Wave\nنظرة على خدماتنا\nمجموعة خدمات متكاملة صممت لتمنح العميل تجربة سفر أكثر جودة وتنظيمًا من البداية وحتى آخر خطوة.\ncol-md-6 col-xl-3\nservice\nتأ\nالتأشيرات الخارجية\nمساعدة في تجهيز الملفات، ومراجعة المستندات، وتنسيق خطوات التقديم.\nاستعرض الخدمة\nhttp://127.0.0.1:8000/visas\nسي\nالسياحة الداخلية\nبرامج محلية منظمة بترتيب أوضح للإقامة والتنقلات وخيارات الرحلة.\nhttp://127.0.0.1:8000/domestic-tourism\nطي\nحجوزات الطيران\nحلول أكثر مرونة لحجز الرحلات المحلية والدولية ومتابعة بيانات الحجز.\nhttp://127.0.0.1:8000/flights\nفن\nحجوزات الفنادق\nخيارات إقامة تناسب الوجهات المختلفة مع ترشيحات أكثر راحة ووضوحًا.\nhttp://127.0.0.1:8000/hotels\nمؤشرات الثقة\nأرقام تعكس حجم الخبرة\n+12K\nعميل تمت خدمته\nطلبات واستفسارات وخطط سفر تم التعامل معها باحترافية.\n+35\nوجهة وخدمة نشطة\nبين التأشيرات، والرحلات، والخدمات المرتبطة بالسفر.\n+8K\nطلبات تأشيرة\nملفات جرى ترتيبها ومتابعتها بمستوى أوضح من التنظيم.\n94%\nرضا العملاء\nمؤشر ثقة يعكس جودة التواصل والوضوح وسهولة المتابعة.\nاحترافية العمل\nفريق يعمل بعقلية الخدمة الراقية\nنعتمد على أسلوب عمل يوازن بين السرعة والدقة والاهتمام بالتفاصيل، حتى يشعر العميل أن كل جزء من الرحلة أو الطلب يتم التعامل معه باحترافية واضحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\nفريق متخصص يفهم طبيعة الخدمات والملفات ومتطلبات التنفيذ.\nتنسيق أوضح بين عناصر الخدمة المختلفة لتقليل التشتت.\nاهتمام بالتفاصيل الصغيرة التي ترفع جودة التجربة بالكامل.\nابدأ رحلتك معنا\nجاهز لتبدأ تجربة سفر أكثر وضوحًا واحترافية؟\nتواصل مع Travel Wave ودعنا نساعدك في اختيار الخدمة المناسبة وترتيب الخطوة التالية بثقة أكبر.\nابدأ الآن\nhttp://127.0.0.1:8000/contact\nprimary\nتواصل معنا\nhttp://127.0.0.1:8000/contact#premium-contact-form\noutline', 'http://127.0.0.1:8000/about', '{\"page_key\":\"about\"}', 25, '2026-03-27 02:59:33', '2026-03-27 02:59:33');
INSERT INTO `chatbot_knowledge_items` (`id`, `source_type`, `source_id`, `source_key`, `locale`, `title`, `summary`, `content`, `url`, `metadata`, `sort_order`, `created_at`, `updated_at`) VALUES
(238, 'service_pages', NULL, 'about', 'ar', 'من نحن', 'Travel Wave شريك موثوق في خدمات السفر والتأشيرات، نجمع بين التنظيم العملي، والمتابعة الواضحة، والتجربة الراقية التي تمنح العميل ثقة أكبر في كل خطوة.', 'rtl\nمن نحن | Travel Wave\nTravel Wave\nمن نحن\nTravel Wave شريك موثوق في خدمات السفر والتأشيرات، نجمع بين التنظيم العملي، والمتابعة الواضحة، والتجربة الراقية التي تمنح العميل ثقة أكبر في كل خطوة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nالرئيسية\nhttp://127.0.0.1:8000\nهوية Travel Wave\nرحلة مبنية على الوضوح والثقة\nنؤمن في Travel Wave أن خدمات السفر والتأشيرات لا يجب أن تكون معقدة أو مرهقة. لذلك نصمم تجربة أكثر ترتيبًا ووضوحًا لمساعدة العملاء في التأشيرات الخارجية، والسياحة الداخلية، وحجوزات الطيران، وحجوزات الفنادق، مع اهتمام حقيقي بالتفاصيل وجودة الخدمة.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nحلول متكاملة تبدأ من الاستفسار وحتى إتمام الخدمة.\nلغة واضحة وخطوات عملية تناسب احتياج كل عميل.\nتجربة أكثر احترافية في الحجوزات والملفات والمتابعة.\nأساس العلامة\nالرؤية والرسالة والقيم\nنصنع تجربة سفر أكثر رقيًا وتنظيمًا عبر خدمة واضحة، وتفاصيل مدروسة، واهتمام فعلي بما يحتاجه العميل.\ncol-md-6 col-xl-4\nvision\nر\nرؤيتنا\nأن تكون Travel Wave من الأسماء الأكثر ثقة في خدمات السفر والتأشيرات من خلال تجربة تجمع بين الفخامة والوضوح وسهولة التنفيذ.\nرسالتنا\nتبسيط رحلات العملاء وطلبات التأشيرات عبر تنظيم احترافي، وخيارات مناسبة، ودعم متواصل يرفع مستوى الثقة والراحة.\nق\nقيمنا\nالوضوح، والالتزام، والدقة، وسرعة الاستجابة، والاهتمام بالتفاصيل التي تصنع فارقًا حقيقيًا في جودة الخدمة.\nلماذا نحن\nلماذا يختارنا العملاء؟\nfeature\nخب\nخبرة في خدمات السفر والتأشيرات\nفهم أعمق لاحتياجات السفر والملفات والحجوزات يجعل التجربة أكثر سلاسة ووضوحًا.\nمت\nمتابعة دقيقة للملفات\nنهتم بالتفاصيل المهمة ونوضح للعميل ما ينقصه وما يحتاجه قبل أي خطوة أساسية.\nحج\nتنظيم احترافي للحجوزات\nتنسيق أفضل بين الفنادق والطيران والخدمات المساندة بما يتوافق مع الهدف من الرحلة.\nدع\nدعم مستمر للعملاء\nاستجابة أوضح ومتابعة أكثر قربًا لتقليل التردد ورفع مستوى الثقة.\nحل\nحلول متنوعة تناسب كل عميل\nنقترح خيارات مرنة تتناسب مع الميزانية، والوجهة، وطبيعة الخدمة المطلوبة.\nسر\nسرعة ووضوح في الإجراءات\nنرتب الأولويات ونقدم خطوات عملية تساعد العميل على التحرك بشكل أسرع وأكثر راحة.\nخدمات Travel Wave\nنظرة على خدماتنا\nمجموعة خدمات متكاملة صممت لتمنح العميل تجربة سفر أكثر جودة وتنظيمًا من البداية وحتى آخر خطوة.\ncol-md-6 col-xl-3\nservice\nتأ\nالتأشيرات الخارجية\nمساعدة في تجهيز الملفات، ومراجعة المستندات، وتنسيق خطوات التقديم.\nاستعرض الخدمة\nhttp://127.0.0.1:8000/visas\nسي\nالسياحة الداخلية\nبرامج محلية منظمة بترتيب أوضح للإقامة والتنقلات وخيارات الرحلة.\nhttp://127.0.0.1:8000/domestic-tourism\nطي\nحجوزات الطيران\nحلول أكثر مرونة لحجز الرحلات المحلية والدولية ومتابعة بيانات الحجز.\nhttp://127.0.0.1:8000/flights\nفن\nحجوزات الفنادق\nخيارات إقامة تناسب الوجهات المختلفة مع ترشيحات أكثر راحة ووضوحًا.\nhttp://127.0.0.1:8000/hotels\nمؤشرات الثقة\nأرقام تعكس حجم الخبرة\n+12K\nعميل تمت خدمته\nطلبات واستفسارات وخطط سفر تم التعامل معها باحترافية.\n+35\nوجهة وخدمة نشطة\nبين التأشيرات، والرحلات، والخدمات المرتبطة بالسفر.\n+8K\nطلبات تأشيرة\nملفات جرى ترتيبها ومتابعتها بمستوى أوضح من التنظيم.\n94%\nرضا العملاء\nمؤشر ثقة يعكس جودة التواصل والوضوح وسهولة المتابعة.\nاحترافية العمل\nفريق يعمل بعقلية الخدمة الراقية\nنعتمد على أسلوب عمل يوازن بين السرعة والدقة والاهتمام بالتفاصيل، حتى يشعر العميل أن كل جزء من الرحلة أو الطلب يتم التعامل معه باحترافية واضحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\nفريق متخصص يفهم طبيعة الخدمات والملفات ومتطلبات التنفيذ.\nتنسيق أوضح بين عناصر الخدمة المختلفة لتقليل التشتت.\nاهتمام بالتفاصيل الصغيرة التي ترفع جودة التجربة بالكامل.\nابدأ رحلتك معنا\nجاهز لتبدأ تجربة سفر أكثر وضوحًا واحترافية؟\nتواصل مع Travel Wave ودعنا نساعدك في اختيار الخدمة المناسبة وترتيب الخطوة التالية بثقة أكبر.\nابدأ الآن\nhttp://127.0.0.1:8000/contact\nprimary\nتواصل معنا\nhttp://127.0.0.1:8000/contact#premium-contact-form\noutline', 'http://127.0.0.1:8000/about', '{\"page_key\":\"about\"}', 26, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(239, 'service_pages', NULL, 'contact', 'en', 'تواصل معنا', 'للاستفسارات، والحجوزات، وخدمات التأشيرات، وخطط السفر المختلفة، فريق Travel Wave جاهز لمساعدتك بخطوات أوضح واستجابة أسرع.', 'rtl\nتواصل معنا | Travel Wave\nTravel Wave Support\nتواصل معنا\nللاستفسارات، والحجوزات، وخدمات التأشيرات، وخطط السفر المختلفة، فريق Travel Wave جاهز لمساعدتك بخطوات أوضح واستجابة أسرع.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nمعلومات التواصل\nاختر وسيلة التواصل الأنسب لك\ncol-md-6 col-xl-4\ncontact\nها\nرقم الهاتف\n+20 100 123 4567\n+20 122 555 7788\ntel:+20 100 123 4567\nاتصل الآن\nوت\nواتساب\nرد أسرع للاستفسارات والمتابعة\nhttps://wa.me/201001234567\nراسلنا واتساب\nبر\nالبريد الإلكتروني\ninfo@travelwave.com\nللطلبات والاستفسارات العامة\nmailto:info@travelwave.com\nأرسل بريدًا\nعن\nالعنوان\nمدينة نصر، القاهرة، مصر\nيسعدنا استقبال استفساراتك ومساعدتك\n#contact-location\nاعرض الموقع\nدو\nساعات العمل\nيوميًا من 10 صباحًا حتى 8 مساءً\nمواعيد الدعم والمتابعة المعتادة\n#premium-contact-form\nأرسل طلبك\nنموذج التواصل\nأرسل استفسارك إلى Travel Wave\nاكتب تفاصيل طلبك وسنساعدك في اختيار الخدمة المناسبة وترتيب الخطوة التالية بطريقة أوضح.\nمساعدة في التأشيرات الخارجية ومتطلبات الملفات.\nتنسيق حجوزات الرحلات الداخلية والطيران والفنادق.\nمتابعة أوضح للطلبات والاستفسارات الخاصة.\nContact Us\nابدأ رسالتك الآن\nاملأ البيانات الأساسية وسيتواصل معك فريق Travel Wave.\nأرسل الآن\nتم إرسال رسالتك بنجاح، وسيتواصل معك فريق Travel Wave قريبًا.\nemail\nservice_type\ndestination\nmessage\nالاسم\nنوع الخدمة\nالوجهة\nالرسالة / ملاحظات\nاكتب اسمك الكامل\nاكتب رقم الهاتف\nexample@email.com\nمثال: فرنسا، شرم الشيخ، دبي\nاكتب تفاصيل طلبك أو استفسارك\nالتأشيرات الخارجية\nالسياحة الداخلية\nحجوزات الطيران\nحجوزات الفنادق\nطلب مخصص\nكيف نساعدك؟\nيمكنك التواصل معنا بخصوص\nhelp\nتأ\nالاستفسار عن التأشيرات\nمراجعة الطلبات والمستندات وخيارات التقديم والمتابعة.\nدا\nحجز رحلات داخلية\nبرامج محلية، وإقامة، وتنقلات، وخيارات تناسب طبيعة الرحلة.\nطي\nحجز طيران\nمساعدة في اختيار المسارات المناسبة والبيانات الأساسية للحجز.\nفن\nحجز فنادق\nترشيحات إقامة مناسبة حسب الوجهة والميزانية ومستوى الراحة المطلوب.\nمت\nمتابعة الطلبات\nتوضيح الخطوات التالية وتأكيد ما يلزم لاستكمال الخدمة بوضوح أكبر.\nالموقع\nزورنا أو استخدم الموقع كمرجع\nيمكنك استخدام بيانات الموقع للتواصل أو للوصول إلى المكتب عند الحاجة إلى زيارة مباشرة أو متابعة خاصة.\nإجابات سريعة\nأسئلة شائعة حول التواصل\nما أوقات العمل؟\nكيف أتواصل بسرعة؟\nأسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.\nهل يمكن التواصل عبر واتساب؟\nنعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.\nهل يمكن طلب خدمة مخصصة؟\nنعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.\nمتى يتم الرد على الاستفسارات؟\nيتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.\nابدأ التواصل الآن\nهل ترغب في رد أسرع وخطوة أوضح؟\nتواصل مع Travel Wave الآن ودعنا نساعدك في ترتيب الخدمة المناسبة لك بثقة أكبر وتجربة أكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nراسلنا الآن\nprimary\nابدأ طلبك\noutline', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\"}', 27, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(240, 'faqs', NULL, 'contact-faq-0', 'en', 'ما أوقات العمل؟', 'يوميًا من 10 صباحًا حتى 8 مساءً', 'ما أوقات العمل؟\nيوميًا من 10 صباحًا حتى 8 مساءً', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 28, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(241, 'faqs', NULL, 'contact-faq-1', 'en', 'كيف أتواصل بسرعة؟', 'أسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.', 'كيف أتواصل بسرعة؟\nأسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 29, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(242, 'faqs', NULL, 'contact-faq-2', 'en', 'هل يمكن التواصل عبر واتساب؟', 'نعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.', 'هل يمكن التواصل عبر واتساب؟\nنعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 30, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(243, 'faqs', NULL, 'contact-faq-3', 'en', 'هل يمكن طلب خدمة مخصصة؟', 'نعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.', 'هل يمكن طلب خدمة مخصصة؟\nنعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 31, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(244, 'faqs', NULL, 'contact-faq-4', 'en', 'متى يتم الرد على الاستفسارات؟', 'يتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.', 'متى يتم الرد على الاستفسارات؟\nيتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 32, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(245, 'service_pages', NULL, 'contact', 'ar', 'تواصل معنا', 'للاستفسارات، والحجوزات، وخدمات التأشيرات، وخطط السفر المختلفة، فريق Travel Wave جاهز لمساعدتك بخطوات أوضح واستجابة أسرع.', 'rtl\nتواصل معنا | Travel Wave\nTravel Wave Support\nتواصل معنا\nللاستفسارات، والحجوزات، وخدمات التأشيرات، وخطط السفر المختلفة، فريق Travel Wave جاهز لمساعدتك بخطوات أوضح واستجابة أسرع.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nمعلومات التواصل\nاختر وسيلة التواصل الأنسب لك\ncol-md-6 col-xl-4\ncontact\nها\nرقم الهاتف\n+20 100 123 4567\n+20 122 555 7788\ntel:+20 100 123 4567\nاتصل الآن\nوت\nواتساب\nرد أسرع للاستفسارات والمتابعة\nhttps://wa.me/201001234567\nراسلنا واتساب\nبر\nالبريد الإلكتروني\ninfo@travelwave.com\nللطلبات والاستفسارات العامة\nmailto:info@travelwave.com\nأرسل بريدًا\nعن\nالعنوان\nمدينة نصر، القاهرة، مصر\nيسعدنا استقبال استفساراتك ومساعدتك\n#contact-location\nاعرض الموقع\nدو\nساعات العمل\nيوميًا من 10 صباحًا حتى 8 مساءً\nمواعيد الدعم والمتابعة المعتادة\n#premium-contact-form\nأرسل طلبك\nنموذج التواصل\nأرسل استفسارك إلى Travel Wave\nاكتب تفاصيل طلبك وسنساعدك في اختيار الخدمة المناسبة وترتيب الخطوة التالية بطريقة أوضح.\nمساعدة في التأشيرات الخارجية ومتطلبات الملفات.\nتنسيق حجوزات الرحلات الداخلية والطيران والفنادق.\nمتابعة أوضح للطلبات والاستفسارات الخاصة.\nContact Us\nابدأ رسالتك الآن\nاملأ البيانات الأساسية وسيتواصل معك فريق Travel Wave.\nأرسل الآن\nتم إرسال رسالتك بنجاح، وسيتواصل معك فريق Travel Wave قريبًا.\nemail\nservice_type\ndestination\nmessage\nالاسم\nنوع الخدمة\nالوجهة\nالرسالة / ملاحظات\nاكتب اسمك الكامل\nاكتب رقم الهاتف\nexample@email.com\nمثال: فرنسا، شرم الشيخ، دبي\nاكتب تفاصيل طلبك أو استفسارك\nالتأشيرات الخارجية\nالسياحة الداخلية\nحجوزات الطيران\nحجوزات الفنادق\nطلب مخصص\nكيف نساعدك؟\nيمكنك التواصل معنا بخصوص\nhelp\nتأ\nالاستفسار عن التأشيرات\nمراجعة الطلبات والمستندات وخيارات التقديم والمتابعة.\nدا\nحجز رحلات داخلية\nبرامج محلية، وإقامة، وتنقلات، وخيارات تناسب طبيعة الرحلة.\nطي\nحجز طيران\nمساعدة في اختيار المسارات المناسبة والبيانات الأساسية للحجز.\nفن\nحجز فنادق\nترشيحات إقامة مناسبة حسب الوجهة والميزانية ومستوى الراحة المطلوب.\nمت\nمتابعة الطلبات\nتوضيح الخطوات التالية وتأكيد ما يلزم لاستكمال الخدمة بوضوح أكبر.\nالموقع\nزورنا أو استخدم الموقع كمرجع\nيمكنك استخدام بيانات الموقع للتواصل أو للوصول إلى المكتب عند الحاجة إلى زيارة مباشرة أو متابعة خاصة.\nإجابات سريعة\nأسئلة شائعة حول التواصل\nما أوقات العمل؟\nكيف أتواصل بسرعة؟\nأسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.\nهل يمكن التواصل عبر واتساب؟\nنعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.\nهل يمكن طلب خدمة مخصصة؟\nنعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.\nمتى يتم الرد على الاستفسارات؟\nيتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.\nابدأ التواصل الآن\nهل ترغب في رد أسرع وخطوة أوضح؟\nتواصل مع Travel Wave الآن ودعنا نساعدك في ترتيب الخدمة المناسبة لك بثقة أكبر وتجربة أكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nراسلنا الآن\nprimary\nابدأ طلبك\noutline', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\"}', 33, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(246, 'faqs', NULL, 'contact-faq-0', 'ar', 'ما أوقات العمل؟', 'يوميًا من 10 صباحًا حتى 8 مساءً', 'ما أوقات العمل؟\nيوميًا من 10 صباحًا حتى 8 مساءً', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 34, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(247, 'faqs', NULL, 'contact-faq-1', 'ar', 'كيف أتواصل بسرعة؟', 'أسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.', 'كيف أتواصل بسرعة؟\nأسرع وسيلة للتواصل عادة تكون عبر الهاتف أو واتساب حسب طبيعة الاستفسار والخدمة المطلوبة.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 35, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(248, 'faqs', NULL, 'contact-faq-2', 'ar', 'هل يمكن التواصل عبر واتساب؟', 'نعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.', 'هل يمكن التواصل عبر واتساب؟\nنعم، يمكن استخدام واتساب للاستفسارات السريعة وبدء الطلبات ومتابعة التفاصيل الأساسية.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 36, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(249, 'faqs', NULL, 'contact-faq-3', 'ar', 'هل يمكن طلب خدمة مخصصة؟', 'نعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.', 'هل يمكن طلب خدمة مخصصة؟\nنعم، يمكن إرسال طلب خاص أو خدمة مركبة وسيقوم فريق Travel Wave بتوضيح الخيارات المناسبة.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 37, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(250, 'faqs', NULL, 'contact-faq-4', 'ar', 'متى يتم الرد على الاستفسارات؟', 'يتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.', 'متى يتم الرد على الاستفسارات؟\nيتم الرد بأسرع وقت ممكن حسب توقيت الاستفسار ونوع الخدمة المطلوبة.', 'http://127.0.0.1:8000/contact', '{\"page_key\":\"contact\",\"faq\":true}', 38, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(251, 'visa_countries', 1, 'france-visa', 'en', 'France', '- France visa usually falls under the short-stay Schengen category.\n- Suitable for tourism, family visits, and selected business travel.\n- It usually allows stays of up to 90 days within 180 days.\n- Processing often takes around 15 to 30 working days depending on season and file completeness.\n- Travel Wave helps review documents, align bookings, and organize the file more clearly.', 'visa\nفرنسا\n- تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.\nخدمات تأشيرة فرنسا | Travel Wave\nاكتشف خدمات Travel Wave لتأشيرة فرنسا والمستندات والخطوات والرسوم والأسئلة الشائعة ونموذج الاستفسار في قالب تأشيرات قابل لإعادة الاستخدام.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالاتحاد الأوروبي\nhttp://127.0.0.1:8000/visas/european-union\nدعم تأشيرة شنغن\nخدمات تأشيرة فرنسا المصممة بوضوح وثقة\nمن أول قائمة مستندات حتى التقديم النهائي تساعدك Travel Wave على تجهيز ملف فرنسا بشكل أوضح وأكثر احترافية.\nhttp://127.0.0.1:8000/storage/visa-countries/france-flag.svg\n0.5\nابدأ طلب تأشيرة فرنسا\n#visa-inquiry\nملخص سريع\n#destination-summary\nنوع التأشيرة\nشنغن قصيرة الإقامة\nVS\nمدة المعالجة\n15 إلى 30 يوم عمل\nPT\nمدة الإقامة\nحتى 90 يوما\nSD\nالرسوم التقريبية\nتحدد بعد المراجعة\nFE\nنظرة عامة على تأشيرة فرنسا\nتظل فرنسا من أكثر وجهات شنغن طلبا للسياحة والزيارات العائلية ورحلات الأعمال. تم تصميم هذه الصفحة لتمنح المتقدم فهما سريعا لنوع التأشيرة والخطوات المتوقعة وكيف تساعد Travel Wave في تقليل أي ارتباك قبل التقديم.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nدعم موجه من Travel Wave\nتجهيز واضح للملف قبل التقديم.\nدعم عملي للحجوزات وتوقيت التقديم.\nاتساق أفضل للمستندات مع غرض السفر المعلن.\nوضوح أكبر للخطوة التالية بعد كل مرحلة.\nشرح التأشيرة بالتفصيل\nغالبا ما تندرج تأشيرة فرنسا ضمن تأشيرات شنغن قصيرة الإقامة للمسافرين بغرض السياحة أو الزيارة العائلية أو بعض رحلات الأعمال.\n\nيجب على المتقدم تجهيز غرض سفر واضح ومستندات مالية متناسقة وحجوزات تدعم خط السير المقدم.\n\nقبل التقديم من المهم التأكد من صلاحية جواز السفر وأن الملف متوافق مع سبب السفر المعلن وأن تواريخ الحجز والطيران والتأمين متطابقة.\nأفضل وقت للتقديم\nتتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.\nأبرز النقاط المهمة\nمناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n•\nالملفات المنظمة تجعل مراجعة الطلب أكثر سلاسة ووضوحا.\nالتحضير المبكر يساعد في المواعيد وتجنب ضغط المواسم.\nلماذا تختار Travel Wave\nتجربة دعم احترافية تجعل مسار تأشيرة فرنسا أوضح وأكثر تنظيما وأسهل في المتابعة.\nمراجعة احترافية للمستندات\nنراجع ترتيب الملف ونوضح النواقص قبل موعد التقديم.\nshield\nتنظيم الملف بشكل واضح\nيتم ترتيب المستندات بشكل أوضح وأسهل للفهم والتقديم.\nfile\nدعم الحجوزات\nنساعد في تنسيق تفاصيل الفندق والطيران والتأمين مع خطة السفر.\ncalendar\nمتابعة خطوة بخطوة\nيعرف المتقدم ما هي الخطوة التالية في كل مرحلة دون تخمين.\nsupport\nالمستندات المطلوبة\nقد يختلف الملف بحسب حالة المتقدم، لكن هذه هي المستندات الأكثر شيوعا لتجهيز تأشيرة فرنسا السياحية.\nجواز سفر ساري\nيجب أن يغطي جواز السفر مدة الصلاحية المطلوبة وأن يحتوي على صفحات متاحة.\nOK\nصور شخصية حديثة\nيجب أن تطابق الصور مقاسات ومتطلبات السفارة.\nكشف حساب بنكي\nيجب أن يدعم كشف الحساب توقيت الرحلة ومستوى التكلفة المقترح.\nإثبات عمل أو دراسة\nخطاب العمل أو ما يعادله يدعم سبب السفر ونية العودة.\nحجوزات الفندق والطيران\nيجب أن تتوافق تواريخ الحجز مع خطة السفر وفترة الطلب.\nتأمين السفر\nيجب أن يحقق التأمين متطلبات شنغن طوال فترة الإقامة.\nخطوات التقديم\n1\nأرسل بياناتك\nشارك سبب السفر وتوقيته وبياناتك الأساسية لتحديد اتجاه الملف.\n2\nمراجعة الملف\nتراجع Travel Wave الملف الحالي وتوضح ما يحتاج إلى استكمال أو تحسين.\n3\nتجهيز المستندات\nيتم استكمال المستندات ومطابقتها مع غرض السفر وتواريخ الحجوزات.\n4\nالحجوزات والمتابعة\nيتم تنسيق تفاصيل الفندق والطيران والتأمين قبل مرحلة الموعد.\n5\nإتمام التقديم\nتقوم بالتقديم والبصمة ثم تتابع حالة الطلب بعد ذلك.\nالرسوم ومدة المعالجة\nقد تختلف التكلفة النهائية بحسب تحديثات السفارة أو عمر المسافر أو الخدمات الإضافية. تؤكد Travel Wave التقدير الأحدث قبل التقديم.\nرسوم السفارة\nتختلف حسب نوع المسافر\nرسوم مركز التأشيرات\nرسوم خدمة إضافية\nرسوم خدمة Travel Wave\nيتم تحديدها بعد مراجعة الملف\nعادة من 15 إلى 30 يوم عمل\nالأسئلة الشائعة\nهل تأشيرة فرنسا تعتبر تأشيرة شنغن؟\nنعم. في أغلب حالات السفر تتم معالجة تأشيرة فرنسا قصيرة الإقامة ضمن نظام شنغن.\nكم تستغرق المعالجة عادة؟\nغالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.\nهل الحضور للبصمة مطلوب؟\nفي كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.\nمتى يجب أن أبدأ التقديم قبل السفر؟\nيفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.\nموقع المكتب ودعم التأشيرات\nيمكن استخدام هذا القسم لعرض موقع المكتب أو السفارة أو مركز التأشيرات كمرجع للمتقدمين.\nجاهز لبدء ملف تأشيرة فرنسا؟\nدع Travel Wave تحول خطوات التأشيرة المعقدة إلى رحلة أكثر تنظيما ووضوحا وثقة.\nقدّم مع Travel Wave\nprimary\nتحدث مع مستشار\noutline\nتواصل معنا\nتواصل مع Travel Wave بخصوص تأشيرة فرنسا\nأرسل بياناتك وسيرشدك فريقنا بخصوص الأهلية والمستندات والخطوة العملية التالية.\nفرنسا Visa\nFrance Visa\nأرسل استفسار تأشيرة فرنسا\nتم استلام استفسارك الخاص بتأشيرة فرنسا وسيتواصل معك أحد مستشاري Travel Wave قريبا.\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"category\":\"European Union\"}', 39, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(252, 'faqs', 1, 'france-visa-faq-0', 'en', 'هل تأشيرة فرنسا تعتبر تأشيرة شنغن؟', 'نعم. في أغلب حالات السفر تتم معالجة تأشيرة فرنسا قصيرة الإقامة ضمن نظام شنغن.', 'هل تأشيرة فرنسا تعتبر تأشيرة شنغن؟\nنعم. في أغلب حالات السفر تتم معالجة تأشيرة فرنسا قصيرة الإقامة ضمن نظام شنغن.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 40, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(253, 'faqs', 1, 'france-visa-faq-1', 'en', 'كم تستغرق المعالجة عادة؟', 'غالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.', 'كم تستغرق المعالجة عادة؟\nغالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 41, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(254, 'faqs', 1, 'france-visa-faq-2', 'en', 'هل الحضور للبصمة مطلوب؟', 'في كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.', 'هل الحضور للبصمة مطلوب؟\nفي كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 42, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(255, 'faqs', 1, 'france-visa-faq-3', 'en', 'متى يجب أن أبدأ التقديم قبل السفر؟', 'يفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.', 'متى يجب أن أبدأ التقديم قبل السفر؟\nيفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 43, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(256, 'visa_countries', 1, 'france-visa', 'ar', 'فرنسا', '- تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.', 'visa\nفرنسا\n- تأشيرة فرنسا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.\nخدمات تأشيرة فرنسا | Travel Wave\nاكتشف خدمات Travel Wave لتأشيرة فرنسا والمستندات والخطوات والرسوم والأسئلة الشائعة ونموذج الاستفسار في قالب تأشيرات قابل لإعادة الاستخدام.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالاتحاد الأوروبي\nhttp://127.0.0.1:8000/visas/european-union\nدعم تأشيرة شنغن\nخدمات تأشيرة فرنسا المصممة بوضوح وثقة\nمن أول قائمة مستندات حتى التقديم النهائي تساعدك Travel Wave على تجهيز ملف فرنسا بشكل أوضح وأكثر احترافية.\nhttp://127.0.0.1:8000/storage/visa-countries/france-flag.svg\n0.5\nابدأ طلب تأشيرة فرنسا\n#visa-inquiry\nملخص سريع\n#destination-summary\nنوع التأشيرة\nشنغن قصيرة الإقامة\nVS\nمدة المعالجة\n15 إلى 30 يوم عمل\nPT\nمدة الإقامة\nحتى 90 يوما\nSD\nالرسوم التقريبية\nتحدد بعد المراجعة\nFE\nنظرة عامة على تأشيرة فرنسا\nتظل فرنسا من أكثر وجهات شنغن طلبا للسياحة والزيارات العائلية ورحلات الأعمال. تم تصميم هذه الصفحة لتمنح المتقدم فهما سريعا لنوع التأشيرة والخطوات المتوقعة وكيف تساعد Travel Wave في تقليل أي ارتباك قبل التقديم.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nدعم موجه من Travel Wave\nتجهيز واضح للملف قبل التقديم.\nدعم عملي للحجوزات وتوقيت التقديم.\nاتساق أفضل للمستندات مع غرض السفر المعلن.\nوضوح أكبر للخطوة التالية بعد كل مرحلة.\nشرح التأشيرة بالتفصيل\nغالبا ما تندرج تأشيرة فرنسا ضمن تأشيرات شنغن قصيرة الإقامة للمسافرين بغرض السياحة أو الزيارة العائلية أو بعض رحلات الأعمال.\n\nيجب على المتقدم تجهيز غرض سفر واضح ومستندات مالية متناسقة وحجوزات تدعم خط السير المقدم.\n\nقبل التقديم من المهم التأكد من صلاحية جواز السفر وأن الملف متوافق مع سبب السفر المعلن وأن تواريخ الحجز والطيران والتأمين متطابقة.\nأفضل وقت للتقديم\nتتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.\nأبرز النقاط المهمة\nمناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n•\nالملفات المنظمة تجعل مراجعة الطلب أكثر سلاسة ووضوحا.\nالتحضير المبكر يساعد في المواعيد وتجنب ضغط المواسم.\nلماذا تختار Travel Wave\nتجربة دعم احترافية تجعل مسار تأشيرة فرنسا أوضح وأكثر تنظيما وأسهل في المتابعة.\nمراجعة احترافية للمستندات\nنراجع ترتيب الملف ونوضح النواقص قبل موعد التقديم.\nshield\nتنظيم الملف بشكل واضح\nيتم ترتيب المستندات بشكل أوضح وأسهل للفهم والتقديم.\nfile\nدعم الحجوزات\nنساعد في تنسيق تفاصيل الفندق والطيران والتأمين مع خطة السفر.\ncalendar\nمتابعة خطوة بخطوة\nيعرف المتقدم ما هي الخطوة التالية في كل مرحلة دون تخمين.\nsupport\nالمستندات المطلوبة\nقد يختلف الملف بحسب حالة المتقدم، لكن هذه هي المستندات الأكثر شيوعا لتجهيز تأشيرة فرنسا السياحية.\nجواز سفر ساري\nيجب أن يغطي جواز السفر مدة الصلاحية المطلوبة وأن يحتوي على صفحات متاحة.\nOK\nصور شخصية حديثة\nيجب أن تطابق الصور مقاسات ومتطلبات السفارة.\nكشف حساب بنكي\nيجب أن يدعم كشف الحساب توقيت الرحلة ومستوى التكلفة المقترح.\nإثبات عمل أو دراسة\nخطاب العمل أو ما يعادله يدعم سبب السفر ونية العودة.\nحجوزات الفندق والطيران\nيجب أن تتوافق تواريخ الحجز مع خطة السفر وفترة الطلب.\nتأمين السفر\nيجب أن يحقق التأمين متطلبات شنغن طوال فترة الإقامة.\nخطوات التقديم\n1\nأرسل بياناتك\nشارك سبب السفر وتوقيته وبياناتك الأساسية لتحديد اتجاه الملف.\n2\nمراجعة الملف\nتراجع Travel Wave الملف الحالي وتوضح ما يحتاج إلى استكمال أو تحسين.\n3\nتجهيز المستندات\nيتم استكمال المستندات ومطابقتها مع غرض السفر وتواريخ الحجوزات.\n4\nالحجوزات والمتابعة\nيتم تنسيق تفاصيل الفندق والطيران والتأمين قبل مرحلة الموعد.\n5\nإتمام التقديم\nتقوم بالتقديم والبصمة ثم تتابع حالة الطلب بعد ذلك.\nالرسوم ومدة المعالجة\nقد تختلف التكلفة النهائية بحسب تحديثات السفارة أو عمر المسافر أو الخدمات الإضافية. تؤكد Travel Wave التقدير الأحدث قبل التقديم.\nرسوم السفارة\nتختلف حسب نوع المسافر\nرسوم مركز التأشيرات\nرسوم خدمة إضافية\nرسوم خدمة Travel Wave\nيتم تحديدها بعد مراجعة الملف\nعادة من 15 إلى 30 يوم عمل\nالأسئلة الشائعة\nهل تأشيرة فرنسا تعتبر تأشيرة شنغن؟\nنعم. في أغلب حالات السفر تتم معالجة تأشيرة فرنسا قصيرة الإقامة ضمن نظام شنغن.\nكم تستغرق المعالجة عادة؟\nغالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.\nهل الحضور للبصمة مطلوب؟\nفي كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.\nمتى يجب أن أبدأ التقديم قبل السفر؟\nيفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.\nموقع المكتب ودعم التأشيرات\nيمكن استخدام هذا القسم لعرض موقع المكتب أو السفارة أو مركز التأشيرات كمرجع للمتقدمين.\nجاهز لبدء ملف تأشيرة فرنسا؟\nدع Travel Wave تحول خطوات التأشيرة المعقدة إلى رحلة أكثر تنظيما ووضوحا وثقة.\nقدّم مع Travel Wave\nprimary\nتحدث مع مستشار\noutline\nتواصل معنا\nتواصل مع Travel Wave بخصوص تأشيرة فرنسا\nأرسل بياناتك وسيرشدك فريقنا بخصوص الأهلية والمستندات والخطوة العملية التالية.\nفرنسا Visa\nFrance Visa\nأرسل استفسار تأشيرة فرنسا\nتم استلام استفسارك الخاص بتأشيرة فرنسا وسيتواصل معك أحد مستشاري Travel Wave قريبا.\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"category\":\"الاتحاد الأوروبي\"}', 44, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(257, 'faqs', 1, 'france-visa-faq-0', 'ar', 'هل تأشيرة فرنسا تعتبر تأشيرة شنغن؟', 'نعم. في أغلب حالات السفر تتم معالجة تأشيرة فرنسا قصيرة الإقامة ضمن نظام شنغن.', 'هل تأشيرة فرنسا تعتبر تأشيرة شنغن؟\nنعم. في أغلب حالات السفر تتم معالجة تأشيرة فرنسا قصيرة الإقامة ضمن نظام شنغن.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 45, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(258, 'faqs', 1, 'france-visa-faq-1', 'ar', 'كم تستغرق المعالجة عادة؟', 'غالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.', 'كم تستغرق المعالجة عادة؟\nغالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 46, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(259, 'faqs', 1, 'france-visa-faq-2', 'ar', 'هل الحضور للبصمة مطلوب؟', 'في كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.', 'هل الحضور للبصمة مطلوب؟\nفي كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 47, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(260, 'faqs', 1, 'france-visa-faq-3', 'ar', 'متى يجب أن أبدأ التقديم قبل السفر؟', 'يفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.', 'متى يجب أن أبدأ التقديم قبل السفر؟\nيفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.', 'http://127.0.0.1:8000/visa-country/france-visa', '{\"slug\":\"france-visa\",\"faq\":true}', 48, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(261, 'visa_countries', 2, 'germany-visa', 'en', 'Germany', 'Germany visa is commonly requested for tourism, family visits, and selected business travel under the short-stay Schengen category.', 'visa\nألمانيا\nتأشيرة ألمانيا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nدعم تأشيرة ألمانيا بملف أكثر تنظيمًا ووضوحًا\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالاتحاد الأوروبي\nhttp://127.0.0.1:8000/visas/european-union\nدعم تأشيرة شنغن\nتساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.\nhttp://127.0.0.1:8000/storage/visa-countries/germany-flag.svg\n0.45\nابدأ طلب تأشيرة ألمانيا\n#destination-form\nملخص سريع\n#destination-summary\nنوع التأشيرة\nشنغن قصيرة الإقامة\nVS\nمدة المعالجة\n15 إلى 30 يوم عمل\nPT\nمدة الإقامة\nحتى 90 يومًا\nSD\nالرسوم التقريبية\nتحدد بعد المراجعة\nFE\nنظرة عامة على تأشيرة ألمانيا\nتعد ألمانيا من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.\nدعم Travel Wave\nمناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\nيساعد تنظيم الملف على جعل مسار التقديم أوضح.\nتساعدك Travel Wave في تنظيم الملف ومراجعة التناسق بين المستندات والحجوزات وشرح ما يلزم قبل الموعد.\nشرح التأشيرة بالتفصيل\nتحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.\nأفضل وقت للتقديم\nتعتمد المدة النهائية على الموسم ومدى جاهزية الملف.\nأبرز النقاط المهمة\nمناسبة للسياحة والزيارات العائلية.\n•\nيفيد التحضير المبكر في المواسم المزدحمة.\nلماذا تختار Travel Wave\nنساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.\nمراجعة المستندات\nمراجعة اتساق الملف قبل التقديم.\nshield\nتنسيق الحجوزات\nتنسيق توقيتات الفندق والطيران والتأمين.\ncalendar\nمتابعة الخطوات\nsupport\nالمستندات المطلوبة\nقد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.\nجواز سفر ساري\nيجب أن تكون مدة الصلاحية والصفحات المتاحة مناسبة للطلب.\nOK\nكشف حساب بنكي\nينبغي أن يدعم الحركة المالية خطة الرحلة.\nإثبات عمل أو دراسة\nلدعم الملف والارتباطات بعد السفر.\nحجوزات الفندق والطيران والتأمين\nيجب أن تكون التواريخ متسقة في كامل خط السير.\nخطوات التقديم\n1\nأرسل البيانات الأساسية\nالغرض من السفر والتوقيت ونبذة عن الملف.\n2\nمراجعة الملف\nتوضيح النواقص وترتيب الأولويات.\n3\nتجهيز المستندات\nتنظيم الملف والحجوزات الداعمة.\n4\nالتقديم والمتابعة\nإتمام التقديم ثم متابعة حالة الطلب.\nالرسوم ومدة المعالجة\nرسوم السفارة\nتختلف حسب الملف\nرسوم الخدمة\nعادة من 15 إلى 30 يوم عمل\nالأسئلة الشائعة\nهل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.\nمتى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.\nالخريطة والموقع\nجاهز لبدء تجهيز تأشيرة ألمانيا؟\nتساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nابدأ الآن\nprimary\nتواصل معنا\nاستفسر عن تأشيرة ألمانيا\nأرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.\nألمانيا Visa\nGermany Visa\nأرسل الاستفسار\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/germany-visa', '{\"slug\":\"germany-visa\",\"category\":\"European Union\"}', 49, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(262, 'faqs', 2, 'germany-visa-faq-0', 'en', 'هل هذه تأشيرة شنغن؟', 'نعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'هل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'http://127.0.0.1:8000/visa-country/germany-visa', '{\"slug\":\"germany-visa\",\"faq\":true}', 50, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(263, 'faqs', 2, 'germany-visa-faq-1', 'en', 'متى أبدأ؟', 'يفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'متى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'http://127.0.0.1:8000/visa-country/germany-visa', '{\"slug\":\"germany-visa\",\"faq\":true}', 51, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(264, 'visa_countries', 2, 'germany-visa', 'ar', 'ألمانيا', 'تأشيرة ألمانيا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.', 'visa\nألمانيا\nتأشيرة ألمانيا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nدعم تأشيرة ألمانيا بملف أكثر تنظيمًا ووضوحًا\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالاتحاد الأوروبي\nhttp://127.0.0.1:8000/visas/european-union\nدعم تأشيرة شنغن\nتساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.\nhttp://127.0.0.1:8000/storage/visa-countries/germany-flag.svg\n0.45\nابدأ طلب تأشيرة ألمانيا\n#destination-form\nملخص سريع\n#destination-summary\nنوع التأشيرة\nشنغن قصيرة الإقامة\nVS\nمدة المعالجة\n15 إلى 30 يوم عمل\nPT\nمدة الإقامة\nحتى 90 يومًا\nSD\nالرسوم التقريبية\nتحدد بعد المراجعة\nFE\nنظرة عامة على تأشيرة ألمانيا\nتعد ألمانيا من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.\nدعم Travel Wave\nمناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\nيساعد تنظيم الملف على جعل مسار التقديم أوضح.\nتساعدك Travel Wave في تنظيم الملف ومراجعة التناسق بين المستندات والحجوزات وشرح ما يلزم قبل الموعد.\nشرح التأشيرة بالتفصيل\nتحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.\nأفضل وقت للتقديم\nتعتمد المدة النهائية على الموسم ومدى جاهزية الملف.\nأبرز النقاط المهمة\nمناسبة للسياحة والزيارات العائلية.\n•\nيفيد التحضير المبكر في المواسم المزدحمة.\nلماذا تختار Travel Wave\nنساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.\nمراجعة المستندات\nمراجعة اتساق الملف قبل التقديم.\nshield\nتنسيق الحجوزات\nتنسيق توقيتات الفندق والطيران والتأمين.\ncalendar\nمتابعة الخطوات\nsupport\nالمستندات المطلوبة\nقد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.\nجواز سفر ساري\nيجب أن تكون مدة الصلاحية والصفحات المتاحة مناسبة للطلب.\nOK\nكشف حساب بنكي\nينبغي أن يدعم الحركة المالية خطة الرحلة.\nإثبات عمل أو دراسة\nلدعم الملف والارتباطات بعد السفر.\nحجوزات الفندق والطيران والتأمين\nيجب أن تكون التواريخ متسقة في كامل خط السير.\nخطوات التقديم\n1\nأرسل البيانات الأساسية\nالغرض من السفر والتوقيت ونبذة عن الملف.\n2\nمراجعة الملف\nتوضيح النواقص وترتيب الأولويات.\n3\nتجهيز المستندات\nتنظيم الملف والحجوزات الداعمة.\n4\nالتقديم والمتابعة\nإتمام التقديم ثم متابعة حالة الطلب.\nالرسوم ومدة المعالجة\nرسوم السفارة\nتختلف حسب الملف\nرسوم الخدمة\nعادة من 15 إلى 30 يوم عمل\nالأسئلة الشائعة\nهل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.\nمتى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.\nالخريطة والموقع\nجاهز لبدء تجهيز تأشيرة ألمانيا؟\nتساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nابدأ الآن\nprimary\nتواصل معنا\nاستفسر عن تأشيرة ألمانيا\nأرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.\nألمانيا Visa\nGermany Visa\nأرسل الاستفسار\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/germany-visa', '{\"slug\":\"germany-visa\",\"category\":\"الاتحاد الأوروبي\"}', 52, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(265, 'faqs', 2, 'germany-visa-faq-0', 'ar', 'هل هذه تأشيرة شنغن؟', 'نعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'هل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'http://127.0.0.1:8000/visa-country/germany-visa', '{\"slug\":\"germany-visa\",\"faq\":true}', 53, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(266, 'faqs', 2, 'germany-visa-faq-1', 'ar', 'متى أبدأ؟', 'يفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'متى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'http://127.0.0.1:8000/visa-country/germany-visa', '{\"slug\":\"germany-visa\",\"faq\":true}', 54, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(267, 'visa_countries', 3, 'italy-visa', 'en', 'Italy', 'Italy visa is commonly requested for tourism, family visits, and selected business travel under the short-stay Schengen category.', 'visa\nإيطاليا\nتأشيرة إيطاليا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nدعم تأشيرة إيطاليا بملف أكثر تنظيمًا ووضوحًا\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالاتحاد الأوروبي\nhttp://127.0.0.1:8000/visas/european-union\nدعم تأشيرة شنغن\nتساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.\nhttp://127.0.0.1:8000/storage/visa-countries/italy-flag.svg\n0.45\nابدأ طلب تأشيرة إيطاليا\n#destination-form\nملخص سريع\n#destination-summary\nنوع التأشيرة\nشنغن قصيرة الإقامة\nVS\nمدة المعالجة\n15 إلى 30 يوم عمل\nPT\nمدة الإقامة\nحتى 90 يومًا\nSD\nالرسوم التقريبية\nتحدد بعد المراجعة\nFE\nنظرة عامة على تأشيرة إيطاليا\nتعد إيطاليا من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.\nدعم Travel Wave\nمناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\nيساعد تنظيم الملف على جعل مسار التقديم أوضح.\nتساعدك Travel Wave في تجهيز الملف بطريقة أوضح ومراجعة المستندات وتنظيم الحجوزات بما يدعم غرض السفر المعلن.\nشرح التأشيرة بالتفصيل\nتحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.\nأفضل وقت للتقديم\nتعتمد المدة النهائية على الموسم ومدى جاهزية الملف.\nأبرز النقاط المهمة\nمناسبة للسياحة والزيارات العائلية.\n•\nيفيد التحضير المبكر في المواسم المزدحمة.\nلماذا تختار Travel Wave\nنساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.\nمراجعة المستندات\nمراجعة اتساق الملف قبل التقديم.\nshield\nتنسيق الحجوزات\nتنسيق توقيتات الفندق والطيران والتأمين.\ncalendar\nمتابعة الخطوات\nsupport\nالمستندات المطلوبة\nقد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.\nجواز سفر ساري\nيجب أن تكون مدة الصلاحية والصفحات المتاحة مناسبة للطلب.\nOK\nكشف حساب بنكي\nينبغي أن يدعم الحركة المالية خطة الرحلة.\nإثبات عمل أو دراسة\nلدعم الملف والارتباطات بعد السفر.\nحجوزات الفندق والطيران والتأمين\nيجب أن تكون التواريخ متسقة في كامل خط السير.\nخطوات التقديم\n1\nأرسل البيانات الأساسية\nالغرض من السفر والتوقيت ونبذة عن الملف.\n2\nمراجعة الملف\nتوضيح النواقص وترتيب الأولويات.\n3\nتجهيز المستندات\nتنظيم الملف والحجوزات الداعمة.\n4\nالتقديم والمتابعة\nإتمام التقديم ثم متابعة حالة الطلب.\nالرسوم ومدة المعالجة\nرسوم السفارة\nتختلف حسب الملف\nرسوم الخدمة\nعادة من 15 إلى 30 يوم عمل\nالأسئلة الشائعة\nهل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.\nمتى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.\nالخريطة والموقع\nجاهز لبدء تجهيز تأشيرة إيطاليا؟\nتساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nابدأ الآن\nprimary\nتواصل معنا\nاستفسر عن تأشيرة إيطاليا\nأرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.\nإيطاليا Visa\nItaly Visa\nأرسل الاستفسار\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/italy-visa', '{\"slug\":\"italy-visa\",\"category\":\"European Union\"}', 55, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(268, 'faqs', 3, 'italy-visa-faq-0', 'en', 'هل هذه تأشيرة شنغن؟', 'نعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'هل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'http://127.0.0.1:8000/visa-country/italy-visa', '{\"slug\":\"italy-visa\",\"faq\":true}', 56, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(269, 'faqs', 3, 'italy-visa-faq-1', 'en', 'متى أبدأ؟', 'يفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'متى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'http://127.0.0.1:8000/visa-country/italy-visa', '{\"slug\":\"italy-visa\",\"faq\":true}', 57, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(270, 'visa_countries', 3, 'italy-visa', 'ar', 'إيطاليا', 'تأشيرة إيطاليا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.', 'visa\nإيطاليا\nتأشيرة إيطاليا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.\nدعم تأشيرة إيطاليا بملف أكثر تنظيمًا ووضوحًا\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالاتحاد الأوروبي\nhttp://127.0.0.1:8000/visas/european-union\nدعم تأشيرة شنغن\nتساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.\nhttp://127.0.0.1:8000/storage/visa-countries/italy-flag.svg\n0.45\nابدأ طلب تأشيرة إيطاليا\n#destination-form\nملخص سريع\n#destination-summary\nنوع التأشيرة\nشنغن قصيرة الإقامة\nVS\nمدة المعالجة\n15 إلى 30 يوم عمل\nPT\nمدة الإقامة\nحتى 90 يومًا\nSD\nالرسوم التقريبية\nتحدد بعد المراجعة\nFE\nنظرة عامة على تأشيرة إيطاليا\nتعد إيطاليا من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.\nدعم Travel Wave\nمناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\nيساعد تنظيم الملف على جعل مسار التقديم أوضح.\nتساعدك Travel Wave في تجهيز الملف بطريقة أوضح ومراجعة المستندات وتنظيم الحجوزات بما يدعم غرض السفر المعلن.\nشرح التأشيرة بالتفصيل\nتحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.\nأفضل وقت للتقديم\nتعتمد المدة النهائية على الموسم ومدى جاهزية الملف.\nأبرز النقاط المهمة\nمناسبة للسياحة والزيارات العائلية.\n•\nيفيد التحضير المبكر في المواسم المزدحمة.\nلماذا تختار Travel Wave\nنساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.\nمراجعة المستندات\nمراجعة اتساق الملف قبل التقديم.\nshield\nتنسيق الحجوزات\nتنسيق توقيتات الفندق والطيران والتأمين.\ncalendar\nمتابعة الخطوات\nsupport\nالمستندات المطلوبة\nقد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.\nجواز سفر ساري\nيجب أن تكون مدة الصلاحية والصفحات المتاحة مناسبة للطلب.\nOK\nكشف حساب بنكي\nينبغي أن يدعم الحركة المالية خطة الرحلة.\nإثبات عمل أو دراسة\nلدعم الملف والارتباطات بعد السفر.\nحجوزات الفندق والطيران والتأمين\nيجب أن تكون التواريخ متسقة في كامل خط السير.\nخطوات التقديم\n1\nأرسل البيانات الأساسية\nالغرض من السفر والتوقيت ونبذة عن الملف.\n2\nمراجعة الملف\nتوضيح النواقص وترتيب الأولويات.\n3\nتجهيز المستندات\nتنظيم الملف والحجوزات الداعمة.\n4\nالتقديم والمتابعة\nإتمام التقديم ثم متابعة حالة الطلب.\nالرسوم ومدة المعالجة\nرسوم السفارة\nتختلف حسب الملف\nرسوم الخدمة\nعادة من 15 إلى 30 يوم عمل\nالأسئلة الشائعة\nهل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.\nمتى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.\nالخريطة والموقع\nجاهز لبدء تجهيز تأشيرة إيطاليا؟\nتساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nابدأ الآن\nprimary\nتواصل معنا\nاستفسر عن تأشيرة إيطاليا\nأرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.\nإيطاليا Visa\nItaly Visa\nأرسل الاستفسار\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/italy-visa', '{\"slug\":\"italy-visa\",\"category\":\"الاتحاد الأوروبي\"}', 58, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(271, 'faqs', 3, 'italy-visa-faq-0', 'ar', 'هل هذه تأشيرة شنغن؟', 'نعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'هل هذه تأشيرة شنغن؟\nنعم، في أغلب حالات السفر القصير يندرج الطلب ضمن نظام شنغن.', 'http://127.0.0.1:8000/visa-country/italy-visa', '{\"slug\":\"italy-visa\",\"faq\":true}', 59, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(272, 'faqs', 3, 'italy-visa-faq-1', 'ar', 'متى أبدأ؟', 'يفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'متى أبدأ؟\nيفضل البدء مبكرًا، خصوصًا قبل فترات الضغط المرتفع على المواعيد.', 'http://127.0.0.1:8000/visa-country/italy-visa', '{\"slug\":\"italy-visa\",\"faq\":true}', 60, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(273, 'visa_countries', 4, 'spain-visa', 'en', 'Spain', '', 'visa\nإسبانيا\nخدمات تأشيرة إسبانيا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nإسبانيا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/spain-visa', '{\"slug\":\"spain-visa\",\"category\":\"Other Countries\"}', 61, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(274, 'visa_countries', 4, 'spain-visa', 'ar', 'إسبانيا', '', 'visa\nإسبانيا\nخدمات تأشيرة إسبانيا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nإسبانيا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/spain-visa', '{\"slug\":\"spain-visa\",\"category\":\"دول أخرى\"}', 62, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(275, 'visa_countries', 5, 'netherlands-visa', 'en', 'Netherlands', '', 'visa\nهولندا\nخدمات تأشيرة هولندا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nهولندا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/netherlands-visa', '{\"slug\":\"netherlands-visa\",\"category\":\"Other Countries\"}', 63, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(276, 'visa_countries', 5, 'netherlands-visa', 'ar', 'هولندا', '', 'visa\nهولندا\nخدمات تأشيرة هولندا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nهولندا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/netherlands-visa', '{\"slug\":\"netherlands-visa\",\"category\":\"دول أخرى\"}', 64, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(277, 'visa_countries', 6, 'greece-visa', 'en', 'Greece', '', 'visa\nاليونان\nخدمات تأشيرة اليونان\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nاليونان Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/greece-visa', '{\"slug\":\"greece-visa\",\"category\":\"Other Countries\"}', 65, '2026-03-27 02:59:33', '2026-03-27 02:59:33');
INSERT INTO `chatbot_knowledge_items` (`id`, `source_type`, `source_id`, `source_key`, `locale`, `title`, `summary`, `content`, `url`, `metadata`, `sort_order`, `created_at`, `updated_at`) VALUES
(278, 'visa_countries', 6, 'greece-visa', 'ar', 'اليونان', '', 'visa\nاليونان\nخدمات تأشيرة اليونان\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nاليونان Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/greece-visa', '{\"slug\":\"greece-visa\",\"category\":\"دول أخرى\"}', 66, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(279, 'visa_countries', 7, 'uae-visa', 'en', 'UAE', 'UAE visa support with practical planning and document guidance.', 'visa\nالإمارات\nدعم تأشيرة الإمارات مع تخطيط عملي وإرشاد للمستندات.\nخدمات تأشيرة الإمارات\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالدول العربية\nhttp://127.0.0.1:8000/visas/arab-countries\nتوفر Travel Wave دعمًا منظمًا لطلبات تأشيرة الإمارات مع مراجعة الملف والنصائح قبل السفر.\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nالإمارات Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/uae-visa', '{\"slug\":\"uae-visa\",\"category\":\"Arab Countries\"}', 67, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(280, 'visa_countries', 7, 'uae-visa', 'ar', 'الإمارات', 'دعم تأشيرة الإمارات مع تخطيط عملي وإرشاد للمستندات.', 'visa\nالإمارات\nدعم تأشيرة الإمارات مع تخطيط عملي وإرشاد للمستندات.\nخدمات تأشيرة الإمارات\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nالدول العربية\nhttp://127.0.0.1:8000/visas/arab-countries\nتوفر Travel Wave دعمًا منظمًا لطلبات تأشيرة الإمارات مع مراجعة الملف والنصائح قبل السفر.\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nالإمارات Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/uae-visa', '{\"slug\":\"uae-visa\",\"category\":\"الدول العربية\"}', 68, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(281, 'visa_countries', 8, 'usa-visa', 'en', 'USA', '', 'visa\nأمريكا\nخدمات تأشيرة أمريكا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nأمريكا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/usa-visa', '{\"slug\":\"usa-visa\",\"category\":\"Other Countries\"}', 69, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(282, 'visa_countries', 8, 'usa-visa', 'ar', 'أمريكا', '', 'visa\nأمريكا\nخدمات تأشيرة أمريكا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nأمريكا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/usa-visa', '{\"slug\":\"usa-visa\",\"category\":\"دول أخرى\"}', 70, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(283, 'visa_countries', 9, 'canada-visa', 'en', 'Canada', 'Canada visa support with practical planning and document guidance.', 'visa\nكندا\nدعم تأشيرة كندا مع تخطيط عملي وإرشاد للمستندات.\nخدمات تأشيرة كندا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\nتوفر Travel Wave دعمًا منظمًا لطلبات تأشيرة كندا مع مراجعة الملف والنصائح قبل السفر.\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nكندا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/canada-visa', '{\"slug\":\"canada-visa\",\"category\":\"Other Countries\"}', 71, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(284, 'visa_countries', 9, 'canada-visa', 'ar', 'كندا', 'دعم تأشيرة كندا مع تخطيط عملي وإرشاد للمستندات.', 'visa\nكندا\nدعم تأشيرة كندا مع تخطيط عملي وإرشاد للمستندات.\nخدمات تأشيرة كندا\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nدول أخرى\nhttp://127.0.0.1:8000/visas/other-countries\nتوفر Travel Wave دعمًا منظمًا لطلبات تأشيرة كندا مع مراجعة الملف والنصائح قبل السفر.\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nكندا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/canada-visa', '{\"slug\":\"canada-visa\",\"category\":\"دول أخرى\"}', 72, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(285, 'visa_countries', 10, 'turkey-visa', 'en', 'Turkey', 'Turkey visa support with practical planning and document guidance.', 'visa\nتركيا\nدعم تأشيرة تركيا مع تخطيط عملي وإرشاد للمستندات.\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nآسيا\nhttp://127.0.0.1:8000/visas/asia\nتوفر Travel Wave دعمًا منظمًا لطلبات تأشيرة تركيا مع مراجعة الملف والنصائح قبل السفر.\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nتركيا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/turkey-visa', '{\"slug\":\"turkey-visa\",\"category\":\"Asia\"}', 73, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(286, 'visa_countries', 10, 'turkey-visa', 'ar', 'تركيا', 'دعم تأشيرة تركيا مع تخطيط عملي وإرشاد للمستندات.', 'visa\nتركيا\nدعم تأشيرة تركيا مع تخطيط عملي وإرشاد للمستندات.\nالرئيسية\nhttp://127.0.0.1:8000\nالتأشيرات الخارجية\nhttp://127.0.0.1:8000/visas\nآسيا\nhttp://127.0.0.1:8000/visas/asia\nتوفر Travel Wave دعمًا منظمًا لطلبات تأشيرة تركيا مع مراجعة الملف والنصائح قبل السفر.\n0.45\nأرسل استفسارك\n#destination-form\nملخص سريع\n#destination-summary\nنظرة عامة على التأشيرة\nui.visa_details\nأفضل وقت للتقديم\nأبرز النقاط المهمة\nلماذا تختار Travel Wave\nالأوراق المطلوبة\nخطوات التقديم\nالرسوم ومدة المعالجة\nالأسئلة الشائعة\nالخريطة والموقع\nجاهز للتقديم؟\nprimary\nتواصل معنا\nاستفسر عن هذه التأشيرة\nتركيا Visa\nfull_name\nphone\nwhatsapp_number\nemail\nservice_type\ndestination\ntravel_date\nmessage', 'http://127.0.0.1:8000/visa-country/turkey-visa', '{\"slug\":\"turkey-visa\",\"category\":\"آسيا\"}', 74, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(287, 'destinations', 1, 'sharm-el-sheikh', 'en', 'Sharm El Sheikh', 'Beach escapes, resort stays, and organized Red Sea programs.', 'domestic\nشرم الشيخ\nرحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.\nرحلات شرم الشيخ مع Travel Wave\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\nتساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\n0.45\nاحجز الآن\n#destination-form\nاستعرض المزايا\n#destination-highlights\nمعلومات سريعة\nنوع البرنامج\nبرنامج سياحة داخلية\nTP\nالمدة المقترحة\n3 إلى 5 ليالٍ\nDU\nالسعر يبدأ من\nاطلب أحدث عرض\nPR\nأفضل وقت\nبحسب الموسم\nBT\nنبذة عن الوجهة\nشرم الشيخ من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nمناسبة للأزواج والعائلات والإجازات القصيرة.\nمرونة في مستويات الفنادق وشكل البرنامج.\nتساعد Travel Wave على مقارنة الخيارات بشكل أوضح.\nتفاصيل الرحلة\nتنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.\nأفضل وقت للزيارة\nالربيع والخريف غالبًا أفضل الفترات من حيث الطقس والراحة، بينما يبقى الصيف مناسبًا للعائلات وبرامج المنتجعات.\nأهم المعالم والأنشطة\nخليج نعمة\nمنطقة مناسبة للتنزه والخروجات المسائية السهلة.\nخ\nرأس محمد\nمن أشهر المناطق البحرية للرحلات والأنشطة المرتبطة بالبحر.\nر\nالمنتجعات\nخيارات متنوعة من الإقامة تناسب الأزواج والعائلات.\nا\nالخدمات المتضمنة\nيمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.\nحجز الفنادق\nترشيح الفندق المناسب حسب الميزانية وطبيعة الرحلة.\nح\nالتنقلات\nتنسيق الانتقالات المرتبطة بالوصول والمغادرة.\nبرنامج الرحلة\nموازنة واضحة بين الاسترخاء والأنشطة.\nب\nمتابعة مستمرة\nتواصل أوضح قبل السفر وبعد تأكيد الحجز.\nم\nما يلزم قبل الحجز\nالسفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.\nأسماء المسافرين\nالأسماء الصحيحة لبيانات الحجز.\nNM\nتواريخ السفر\nفترة السفر المفضلة وعدد الليالي.\nDT\nعدد الأفراد\nلتحديد نوع الغرفة ومستوى العرض.\nGS\nخطوات الحجز\n1\nاختر الوجهة\nحدد طبيعة الرحلة والتواريخ المناسبة.\n2\nراجع الخيارات\nقارن الفنادق والباقات المقترحة.\n3\nأكد الحجز\nاختر العرض النهائي واستكمل التفاصيل.\n4\nاستلم التأكيد\nتؤكد Travel Wave الحجز والخطوات التالية.\nنظرة على الأسعار\nتتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.\nبرنامج 3 ليالٍ\nيبدأ من 6,900 جنيه\nمناسب للإجازات القصيرة.\nبرنامج 5 ليالٍ\nيبدأ من 9,250 جنيه\nأنسب للمزج بين الإقامة والأنشطة.\nالأسئلة الشائعة\nهل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.\nهل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.\nجاهز للحجز بشكل أوضح؟\nتساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الحجز\nprimary\nواتساب\nhttps://wa.me/201000000000\noutline\nاستفسر عن شرم الشيخ\nأرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.\ndestination\nأرسل الطلب\nemail\ntravel_date\nreturn_date\ntravelers_count\nmessage', 'http://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh', '{\"slug\":\"sharm-el-sheikh\",\"destination_type\":\"domestic\"}', 75, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(288, 'faqs', 1, 'sharm-el-sheikh-faq-0', 'en', 'هل يمكن تعديل البرنامج؟', 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'هل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'http://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh', '{\"slug\":\"sharm-el-sheikh\",\"faq\":true}', 76, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(289, 'faqs', 1, 'sharm-el-sheikh-faq-1', 'en', 'هل توجد برامج مناسبة للعائلات؟', 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'هل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'http://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh', '{\"slug\":\"sharm-el-sheikh\",\"faq\":true}', 77, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(290, 'destinations', 1, 'sharm-el-sheikh', 'ar', 'شرم الشيخ', 'رحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.', 'domestic\nشرم الشيخ\nرحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.\nرحلات شرم الشيخ مع Travel Wave\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\nتساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\n0.45\nاحجز الآن\n#destination-form\nاستعرض المزايا\n#destination-highlights\nمعلومات سريعة\nنوع البرنامج\nبرنامج سياحة داخلية\nTP\nالمدة المقترحة\n3 إلى 5 ليالٍ\nDU\nالسعر يبدأ من\nاطلب أحدث عرض\nPR\nأفضل وقت\nبحسب الموسم\nBT\nنبذة عن الوجهة\nشرم الشيخ من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nمناسبة للأزواج والعائلات والإجازات القصيرة.\nمرونة في مستويات الفنادق وشكل البرنامج.\nتساعد Travel Wave على مقارنة الخيارات بشكل أوضح.\nتفاصيل الرحلة\nتنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.\nأفضل وقت للزيارة\nالربيع والخريف غالبًا أفضل الفترات من حيث الطقس والراحة، بينما يبقى الصيف مناسبًا للعائلات وبرامج المنتجعات.\nأهم المعالم والأنشطة\nخليج نعمة\nمنطقة مناسبة للتنزه والخروجات المسائية السهلة.\nخ\nرأس محمد\nمن أشهر المناطق البحرية للرحلات والأنشطة المرتبطة بالبحر.\nر\nالمنتجعات\nخيارات متنوعة من الإقامة تناسب الأزواج والعائلات.\nا\nالخدمات المتضمنة\nيمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.\nحجز الفنادق\nترشيح الفندق المناسب حسب الميزانية وطبيعة الرحلة.\nح\nالتنقلات\nتنسيق الانتقالات المرتبطة بالوصول والمغادرة.\nبرنامج الرحلة\nموازنة واضحة بين الاسترخاء والأنشطة.\nب\nمتابعة مستمرة\nتواصل أوضح قبل السفر وبعد تأكيد الحجز.\nم\nما يلزم قبل الحجز\nالسفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.\nأسماء المسافرين\nالأسماء الصحيحة لبيانات الحجز.\nNM\nتواريخ السفر\nفترة السفر المفضلة وعدد الليالي.\nDT\nعدد الأفراد\nلتحديد نوع الغرفة ومستوى العرض.\nGS\nخطوات الحجز\n1\nاختر الوجهة\nحدد طبيعة الرحلة والتواريخ المناسبة.\n2\nراجع الخيارات\nقارن الفنادق والباقات المقترحة.\n3\nأكد الحجز\nاختر العرض النهائي واستكمل التفاصيل.\n4\nاستلم التأكيد\nتؤكد Travel Wave الحجز والخطوات التالية.\nنظرة على الأسعار\nتتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.\nبرنامج 3 ليالٍ\nيبدأ من 6,900 جنيه\nمناسب للإجازات القصيرة.\nبرنامج 5 ليالٍ\nيبدأ من 9,250 جنيه\nأنسب للمزج بين الإقامة والأنشطة.\nالأسئلة الشائعة\nهل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.\nهل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.\nجاهز للحجز بشكل أوضح؟\nتساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الحجز\nprimary\nواتساب\nhttps://wa.me/201000000000\noutline\nاستفسر عن شرم الشيخ\nأرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.\ndestination\nأرسل الطلب\nemail\ntravel_date\nreturn_date\ntravelers_count\nmessage', 'http://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh', '{\"slug\":\"sharm-el-sheikh\",\"destination_type\":\"domestic\"}', 78, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(291, 'faqs', 1, 'sharm-el-sheikh-faq-0', 'ar', 'هل يمكن تعديل البرنامج؟', 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'هل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'http://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh', '{\"slug\":\"sharm-el-sheikh\",\"faq\":true}', 79, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(292, 'faqs', 1, 'sharm-el-sheikh-faq-1', 'ar', 'هل توجد برامج مناسبة للعائلات؟', 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'هل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'http://127.0.0.1:8000/domestic-tourism/sharm-el-sheikh', '{\"slug\":\"sharm-el-sheikh\",\"faq\":true}', 80, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(293, 'destinations', 2, 'hurghada', 'en', 'Hurghada', 'A balanced domestic destination for beach stays, family travel, and activity-focused resort holidays.', 'domestic\nالغردقة\nوجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.\nرحلات الغردقة مع Travel Wave\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\nتساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\n0.45\nاحجز الآن\n#destination-form\nاستعرض المزايا\n#destination-highlights\nمعلومات سريعة\nنوع البرنامج\nبرنامج سياحة داخلية\nTP\nالمدة المقترحة\n3 إلى 5 ليالٍ\nDU\nالسعر يبدأ من\nاطلب أحدث عرض\nPR\nأفضل وقت\nبحسب الموسم\nBT\nنبذة عن الوجهة\nالغردقة من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nمناسبة للأزواج والعائلات والإجازات القصيرة.\nمرونة في مستويات الفنادق وشكل البرنامج.\nتساعد Travel Wave على مقارنة الخيارات بشكل أوضح.\nتفاصيل الرحلة\nتنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.\nأفضل وقت للزيارة\nتناسب الغردقة معظم شهور السنة، ويظل الربيع والخريف من الفترات المفضلة لتوازن الطقس.\nأهم المعالم والأنشطة\nفنادق عائلية\nخيارات كثيرة مناسبة للعائلات والإجازات الجماعية.\nف\nعروض موسمية\nبرامج تتغير حسب الموسم وفئة الفندق.\nع\nتنوع في الإقامة\nاختيارات واسعة تناسب مستويات مختلفة من الميزانية.\nت\nالخدمات المتضمنة\nيمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.\nمقارنة الفنادق\nإظهار الفروق بين الفئات والمواقع ونظام الوجبات.\nم\nدعم الحجز\nتنسيق أوضح لتأكيد البرنامج والتفاصيل.\nد\nمتابعة قبل السفر\nمراجعة البيانات النهائية قبل الانطلاق.\nما يلزم قبل الحجز\nالسفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.\nأسماء المسافرين\nالأسماء الصحيحة لبيانات الحجز.\nNM\nتواريخ السفر\nفترة السفر المفضلة وعدد الليالي.\nDT\nعدد الأفراد\nلتحديد نوع الغرفة ومستوى العرض.\nGS\nخطوات الحجز\n1\nاختر الوجهة\nحدد طبيعة الرحلة والتواريخ المناسبة.\n2\nراجع الخيارات\nقارن الفنادق والباقات المقترحة.\n3\nأكد الحجز\nاختر العرض النهائي واستكمل التفاصيل.\n4\nاستلم التأكيد\nتؤكد Travel Wave الحجز والخطوات التالية.\nنظرة على الأسعار\nتتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.\nالباقة القياسية\nيبدأ من 5,800 جنيه\nبحسب الفندق وعدد الليالي.\nالأسئلة الشائعة\nهل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.\nهل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.\nجاهز للحجز بشكل أوضح؟\nتساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الحجز\nprimary\nواتساب\nhttps://wa.me/201000000000\noutline\nاستفسر عن الغردقة\nأرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.\ndestination\nأرسل الطلب\nemail\ntravel_date\nreturn_date\ntravelers_count\nmessage', 'http://127.0.0.1:8000/domestic-tourism/hurghada', '{\"slug\":\"hurghada\",\"destination_type\":\"domestic\"}', 81, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(294, 'faqs', 2, 'hurghada-faq-0', 'en', 'هل يمكن تعديل البرنامج؟', 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'هل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'http://127.0.0.1:8000/domestic-tourism/hurghada', '{\"slug\":\"hurghada\",\"faq\":true}', 82, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(295, 'faqs', 2, 'hurghada-faq-1', 'en', 'هل توجد برامج مناسبة للعائلات؟', 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'هل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'http://127.0.0.1:8000/domestic-tourism/hurghada', '{\"slug\":\"hurghada\",\"faq\":true}', 83, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(296, 'destinations', 2, 'hurghada', 'ar', 'الغردقة', 'وجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.', 'domestic\nالغردقة\nوجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.\nرحلات الغردقة مع Travel Wave\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\nتساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\n0.45\nاحجز الآن\n#destination-form\nاستعرض المزايا\n#destination-highlights\nمعلومات سريعة\nنوع البرنامج\nبرنامج سياحة داخلية\nTP\nالمدة المقترحة\n3 إلى 5 ليالٍ\nDU\nالسعر يبدأ من\nاطلب أحدث عرض\nPR\nأفضل وقت\nبحسب الموسم\nBT\nنبذة عن الوجهة\nالغردقة من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nمناسبة للأزواج والعائلات والإجازات القصيرة.\nمرونة في مستويات الفنادق وشكل البرنامج.\nتساعد Travel Wave على مقارنة الخيارات بشكل أوضح.\nتفاصيل الرحلة\nتنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.\nأفضل وقت للزيارة\nتناسب الغردقة معظم شهور السنة، ويظل الربيع والخريف من الفترات المفضلة لتوازن الطقس.\nأهم المعالم والأنشطة\nفنادق عائلية\nخيارات كثيرة مناسبة للعائلات والإجازات الجماعية.\nف\nعروض موسمية\nبرامج تتغير حسب الموسم وفئة الفندق.\nع\nتنوع في الإقامة\nاختيارات واسعة تناسب مستويات مختلفة من الميزانية.\nت\nالخدمات المتضمنة\nيمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.\nمقارنة الفنادق\nإظهار الفروق بين الفئات والمواقع ونظام الوجبات.\nم\nدعم الحجز\nتنسيق أوضح لتأكيد البرنامج والتفاصيل.\nد\nمتابعة قبل السفر\nمراجعة البيانات النهائية قبل الانطلاق.\nما يلزم قبل الحجز\nالسفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.\nأسماء المسافرين\nالأسماء الصحيحة لبيانات الحجز.\nNM\nتواريخ السفر\nفترة السفر المفضلة وعدد الليالي.\nDT\nعدد الأفراد\nلتحديد نوع الغرفة ومستوى العرض.\nGS\nخطوات الحجز\n1\nاختر الوجهة\nحدد طبيعة الرحلة والتواريخ المناسبة.\n2\nراجع الخيارات\nقارن الفنادق والباقات المقترحة.\n3\nأكد الحجز\nاختر العرض النهائي واستكمل التفاصيل.\n4\nاستلم التأكيد\nتؤكد Travel Wave الحجز والخطوات التالية.\nنظرة على الأسعار\nتتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.\nالباقة القياسية\nيبدأ من 5,800 جنيه\nبحسب الفندق وعدد الليالي.\nالأسئلة الشائعة\nهل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.\nهل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.\nجاهز للحجز بشكل أوضح؟\nتساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الحجز\nprimary\nواتساب\nhttps://wa.me/201000000000\noutline\nاستفسر عن الغردقة\nأرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.\ndestination\nأرسل الطلب\nemail\ntravel_date\nreturn_date\ntravelers_count\nmessage', 'http://127.0.0.1:8000/domestic-tourism/hurghada', '{\"slug\":\"hurghada\",\"destination_type\":\"domestic\"}', 84, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(297, 'faqs', 2, 'hurghada-faq-0', 'ar', 'هل يمكن تعديل البرنامج؟', 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'هل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'http://127.0.0.1:8000/domestic-tourism/hurghada', '{\"slug\":\"hurghada\",\"faq\":true}', 85, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(298, 'faqs', 2, 'hurghada-faq-1', 'ar', 'هل توجد برامج مناسبة للعائلات؟', 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'هل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'http://127.0.0.1:8000/domestic-tourism/hurghada', '{\"slug\":\"hurghada\",\"faq\":true}', 86, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(299, 'destinations', 3, 'marsa-alam', 'en', 'Marsa Alam', 'Quiet Red Sea stays, diving experiences, and resort-focused domestic travel.', 'domestic\nمرسى علم\nإقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.\nرحلات مرسى علم مع Travel Wave\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\nتساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\n0.45\nاحجز الآن\n#destination-form\nاستعرض المزايا\n#destination-highlights\nمعلومات سريعة\nنوع البرنامج\nبرنامج سياحة داخلية\nTP\nالمدة المقترحة\n3 إلى 5 ليالٍ\nDU\nالسعر يبدأ من\nاطلب أحدث عرض\nPR\nأفضل وقت\nبحسب الموسم\nBT\nنبذة عن الوجهة\nمرسى علم من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nمناسبة للأزواج والعائلات والإجازات القصيرة.\nمرونة في مستويات الفنادق وشكل البرنامج.\nتساعد Travel Wave على مقارنة الخيارات بشكل أوضح.\nتفاصيل الرحلة\nتنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.\nأفضل وقت للزيارة\nالخريف والشتاء والربيع من الفترات المناسبة للزيارة، بينما يفضلها البعض صيفًا للإقامة داخل المنتجعات.\nأهم المعالم والأنشطة\nالرحلات البحرية\nمناسبة للسنوركلينج والأنشطة الساحلية.\nا\nالمنتجعات الهادئة\nتجربة مناسبة لمحبي الراحة والخصوصية.\nالإقامة الطويلة\nالوجهة مناسبة لعدد ليالٍ أكبر من الرحلات السريعة.\nالخدمات المتضمنة\nيمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.\nاختيار الفندق\nمقارنة فئات المنتجعات ومستوى الراحة.\nتنسيق التنقلات\nتنظيم أفضل لتوقيتات الوصول والمغادرة.\nت\nبرنامج مرن\nالموازنة بين الرحلات البحرية والوقت الحر.\nب\nما يلزم قبل الحجز\nالسفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.\nأسماء المسافرين\nالأسماء الصحيحة لبيانات الحجز.\nNM\nتواريخ السفر\nفترة السفر المفضلة وعدد الليالي.\nDT\nعدد الأفراد\nلتحديد نوع الغرفة ومستوى العرض.\nGS\nخطوات الحجز\n1\nاختر الوجهة\nحدد طبيعة الرحلة والتواريخ المناسبة.\n2\nراجع الخيارات\nقارن الفنادق والباقات المقترحة.\n3\nأكد الحجز\nاختر العرض النهائي واستكمل التفاصيل.\n4\nاستلم التأكيد\nتؤكد Travel Wave الحجز والخطوات التالية.\nنظرة على الأسعار\nتتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.\nإقامة 4 ليالٍ\nيبدأ من 8,400 جنيه\nبحسب فئة المنتجع والموسم.\nالأسئلة الشائعة\nهل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.\nهل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.\nجاهز للحجز بشكل أوضح؟\nتساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الحجز\nprimary\nواتساب\nhttps://wa.me/201000000000\noutline\nاستفسر عن مرسى علم\nأرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.\ndestination\nأرسل الطلب\nemail\ntravel_date\nreturn_date\ntravelers_count\nmessage', 'http://127.0.0.1:8000/domestic-tourism/marsa-alam', '{\"slug\":\"marsa-alam\",\"destination_type\":\"domestic\"}', 87, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(300, 'faqs', 3, 'marsa-alam-faq-0', 'en', 'هل يمكن تعديل البرنامج؟', 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'هل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'http://127.0.0.1:8000/domestic-tourism/marsa-alam', '{\"slug\":\"marsa-alam\",\"faq\":true}', 88, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(301, 'faqs', 3, 'marsa-alam-faq-1', 'en', 'هل توجد برامج مناسبة للعائلات؟', 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'هل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'http://127.0.0.1:8000/domestic-tourism/marsa-alam', '{\"slug\":\"marsa-alam\",\"faq\":true}', 89, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(302, 'destinations', 3, 'marsa-alam', 'ar', 'مرسى علم', 'إقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.', 'domestic\nمرسى علم\nإقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.\nرحلات مرسى علم مع Travel Wave\nhttp://127.0.0.1:8000/storage/hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\nتساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.\nhttp://127.0.0.1:8000/storage/hero-slides/slide-3.svg\n0.45\nاحجز الآن\n#destination-form\nاستعرض المزايا\n#destination-highlights\nمعلومات سريعة\nنوع البرنامج\nبرنامج سياحة داخلية\nTP\nالمدة المقترحة\n3 إلى 5 ليالٍ\nDU\nالسعر يبدأ من\nاطلب أحدث عرض\nPR\nأفضل وقت\nبحسب الموسم\nBT\nنبذة عن الوجهة\nمرسى علم من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.\nhttp://127.0.0.1:8000/storage/hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\nمناسبة للأزواج والعائلات والإجازات القصيرة.\nمرونة في مستويات الفنادق وشكل البرنامج.\nتساعد Travel Wave على مقارنة الخيارات بشكل أوضح.\nتفاصيل الرحلة\nتنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.\nأفضل وقت للزيارة\nالخريف والشتاء والربيع من الفترات المناسبة للزيارة، بينما يفضلها البعض صيفًا للإقامة داخل المنتجعات.\nأهم المعالم والأنشطة\nالرحلات البحرية\nمناسبة للسنوركلينج والأنشطة الساحلية.\nا\nالمنتجعات الهادئة\nتجربة مناسبة لمحبي الراحة والخصوصية.\nالإقامة الطويلة\nالوجهة مناسبة لعدد ليالٍ أكبر من الرحلات السريعة.\nالخدمات المتضمنة\nيمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.\nاختيار الفندق\nمقارنة فئات المنتجعات ومستوى الراحة.\nتنسيق التنقلات\nتنظيم أفضل لتوقيتات الوصول والمغادرة.\nت\nبرنامج مرن\nالموازنة بين الرحلات البحرية والوقت الحر.\nب\nما يلزم قبل الحجز\nالسفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.\nأسماء المسافرين\nالأسماء الصحيحة لبيانات الحجز.\nNM\nتواريخ السفر\nفترة السفر المفضلة وعدد الليالي.\nDT\nعدد الأفراد\nلتحديد نوع الغرفة ومستوى العرض.\nGS\nخطوات الحجز\n1\nاختر الوجهة\nحدد طبيعة الرحلة والتواريخ المناسبة.\n2\nراجع الخيارات\nقارن الفنادق والباقات المقترحة.\n3\nأكد الحجز\nاختر العرض النهائي واستكمل التفاصيل.\n4\nاستلم التأكيد\nتؤكد Travel Wave الحجز والخطوات التالية.\nنظرة على الأسعار\nتتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.\nإقامة 4 ليالٍ\nيبدأ من 8,400 جنيه\nبحسب فئة المنتجع والموسم.\nالأسئلة الشائعة\nهل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.\nهل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.\nجاهز للحجز بشكل أوضح؟\nتساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.\nhttp://127.0.0.1:8000/storage/hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\nابدأ الحجز\nprimary\nواتساب\nhttps://wa.me/201000000000\noutline\nاستفسر عن مرسى علم\nأرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.\ndestination\nأرسل الطلب\nemail\ntravel_date\nreturn_date\ntravelers_count\nmessage', 'http://127.0.0.1:8000/domestic-tourism/marsa-alam', '{\"slug\":\"marsa-alam\",\"destination_type\":\"domestic\"}', 90, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(303, 'faqs', 3, 'marsa-alam-faq-0', 'ar', 'هل يمكن تعديل البرنامج؟', 'نعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'هل يمكن تعديل البرنامج؟\nنعم، يمكن غالبًا تعديل مستوى الباقة وتفاصيل الإقامة حسب احتياجك.', 'http://127.0.0.1:8000/domestic-tourism/marsa-alam', '{\"slug\":\"marsa-alam\",\"faq\":true}', 91, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(304, 'faqs', 3, 'marsa-alam-faq-1', 'ar', 'هل توجد برامج مناسبة للعائلات؟', 'نعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'هل توجد برامج مناسبة للعائلات؟\nنعم، يمكن تجهيز الرحلة بخيارات مناسبة للعائلات من حيث الفندق والغرف.', 'http://127.0.0.1:8000/domestic-tourism/marsa-alam', '{\"slug\":\"marsa-alam\",\"faq\":true}', 92, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(305, 'destinations', 4, 'dahab', 'en', 'Dahab', 'Dahab travel programs with flexible accommodation and support.', 'domestic\nدهب\nبرامج دهب مع إقامة مرنة ودعم قبل الحجز.\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\n0.45\nاحجز الآن\n#destination-form\nاستعرض التفاصيل\n#destination-highlights\nملخص سريع\nنبذة عن الوجهة\nتقدم Travel Wave باقات عملية إلى دهب تناسب العطلات القصيرة والمواسم المختلفة.\nتفاصيل الخدمة\nأفضل وقت للزيارة\nأهم المعالم والأنشطة\nالخدمات المتضمنة\nالأوراق المطلوبة\nخطوات التقديم\nالأسعار والرسوم\nالأسئلة الشائعة\nجاهز للحجز؟\nprimary\nابدأ طلب الحجز\ndestination\nأرسل الطلب\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/domestic-tourism/dahab', '{\"slug\":\"dahab\",\"destination_type\":\"domestic\"}', 93, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(306, 'destinations', 4, 'dahab', 'ar', 'دهب', 'برامج دهب مع إقامة مرنة ودعم قبل الحجز.', 'domestic\nدهب\nبرامج دهب مع إقامة مرنة ودعم قبل الحجز.\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\n0.45\nاحجز الآن\n#destination-form\nاستعرض التفاصيل\n#destination-highlights\nملخص سريع\nنبذة عن الوجهة\nتقدم Travel Wave باقات عملية إلى دهب تناسب العطلات القصيرة والمواسم المختلفة.\nتفاصيل الخدمة\nأفضل وقت للزيارة\nأهم المعالم والأنشطة\nالخدمات المتضمنة\nالأوراق المطلوبة\nخطوات التقديم\nالأسعار والرسوم\nالأسئلة الشائعة\nجاهز للحجز؟\nprimary\nابدأ طلب الحجز\ndestination\nأرسل الطلب\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/domestic-tourism/dahab', '{\"slug\":\"dahab\",\"destination_type\":\"domestic\"}', 94, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(307, 'destinations', 5, 'north-coast', 'en', 'North Coast', 'North Coast travel programs with flexible accommodation and support.', 'domestic\nالساحل الشمالي\nبرامج الساحل الشمالي مع إقامة مرنة ودعم قبل الحجز.\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\n0.45\nاحجز الآن\n#destination-form\nاستعرض التفاصيل\n#destination-highlights\nملخص سريع\nنبذة عن الوجهة\nتقدم Travel Wave باقات عملية إلى الساحل الشمالي تناسب العطلات القصيرة والمواسم المختلفة.\nتفاصيل الخدمة\nأفضل وقت للزيارة\nأهم المعالم والأنشطة\nالخدمات المتضمنة\nالأوراق المطلوبة\nخطوات التقديم\nالأسعار والرسوم\nالأسئلة الشائعة\nجاهز للحجز؟\nprimary\nابدأ طلب الحجز\ndestination\nأرسل الطلب\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/domestic-tourism/north-coast', '{\"slug\":\"north-coast\",\"destination_type\":\"domestic\"}', 95, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(308, 'destinations', 5, 'north-coast', 'ar', 'الساحل الشمالي', 'برامج الساحل الشمالي مع إقامة مرنة ودعم قبل الحجز.', 'domestic\nالساحل الشمالي\nبرامج الساحل الشمالي مع إقامة مرنة ودعم قبل الحجز.\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\n0.45\nاحجز الآن\n#destination-form\nاستعرض التفاصيل\n#destination-highlights\nملخص سريع\nنبذة عن الوجهة\nتقدم Travel Wave باقات عملية إلى الساحل الشمالي تناسب العطلات القصيرة والمواسم المختلفة.\nتفاصيل الخدمة\nأفضل وقت للزيارة\nأهم المعالم والأنشطة\nالخدمات المتضمنة\nالأوراق المطلوبة\nخطوات التقديم\nالأسعار والرسوم\nالأسئلة الشائعة\nجاهز للحجز؟\nprimary\nابدأ طلب الحجز\ndestination\nأرسل الطلب\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/domestic-tourism/north-coast', '{\"slug\":\"north-coast\",\"destination_type\":\"domestic\"}', 96, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(309, 'destinations', 6, 'luxor-aswan', 'en', 'Luxor & Aswan', 'Luxor & Aswan travel programs with flexible accommodation and support.', 'domestic\nالأقصر وأسوان\nبرامج الأقصر وأسوان مع إقامة مرنة ودعم قبل الحجز.\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\n0.45\nاحجز الآن\n#destination-form\nاستعرض التفاصيل\n#destination-highlights\nملخص سريع\nنبذة عن الوجهة\nتقدم Travel Wave باقات عملية إلى الأقصر وأسوان تناسب العطلات القصيرة والمواسم المختلفة.\nتفاصيل الخدمة\nأفضل وقت للزيارة\nأهم المعالم والأنشطة\nالخدمات المتضمنة\nالأوراق المطلوبة\nخطوات التقديم\nالأسعار والرسوم\nالأسئلة الشائعة\nجاهز للحجز؟\nprimary\nابدأ طلب الحجز\ndestination\nأرسل الطلب\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/domestic-tourism/luxor-aswan', '{\"slug\":\"luxor-aswan\",\"destination_type\":\"domestic\"}', 97, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(310, 'destinations', 6, 'luxor-aswan', 'ar', 'الأقصر وأسوان', 'برامج الأقصر وأسوان مع إقامة مرنة ودعم قبل الحجز.', 'domestic\nالأقصر وأسوان\nبرامج الأقصر وأسوان مع إقامة مرنة ودعم قبل الحجز.\nالرئيسية\nhttp://127.0.0.1:8000\nالسياحة الداخلية\nhttp://127.0.0.1:8000/domestic-tourism\n0.45\nاحجز الآن\n#destination-form\nاستعرض التفاصيل\n#destination-highlights\nملخص سريع\nنبذة عن الوجهة\nتقدم Travel Wave باقات عملية إلى الأقصر وأسوان تناسب العطلات القصيرة والمواسم المختلفة.\nتفاصيل الخدمة\nأفضل وقت للزيارة\nأهم المعالم والأنشطة\nالخدمات المتضمنة\nالأوراق المطلوبة\nخطوات التقديم\nالأسعار والرسوم\nالأسئلة الشائعة\nجاهز للحجز؟\nprimary\nابدأ طلب الحجز\ndestination\nأرسل الطلب\nemail\ntravel_date\nmessage', 'http://127.0.0.1:8000/domestic-tourism/luxor-aswan', '{\"slug\":\"luxor-aswan\",\"destination_type\":\"domestic\"}', 98, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(311, 'blog_posts', 1, 'top-reasons-visas-get-rejected', 'en', 'Top reasons visas get rejected and how to avoid them', 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.', 'Top reasons visas get rejected and how to avoid them\nA practical Travel Wave article designed to help travelers make better booking and planning decisions.\nTravel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.\nTravel Insights', 'http://127.0.0.1:8000/blog/top-reasons-visas-get-rejected', NULL, 99, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(312, 'blog_posts', 1, 'top-reasons-visas-get-rejected', 'ar', 'أهم أسباب رفض التأشيرات وكيف تتجنبها', 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.', 'أهم أسباب رفض التأشيرات وكيف تتجنبها\nمقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.\nتنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.\nمحتوى السفر', 'http://127.0.0.1:8000/blog/top-reasons-visas-get-rejected', NULL, 100, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(313, 'blog_posts', 2, 'best-time-to-apply-for-europe-visas', 'en', 'Best time to apply for Europe visas', 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.', 'Best time to apply for Europe visas\nA practical Travel Wave article designed to help travelers make better booking and planning decisions.\nTravel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.\nTravel Insights', 'http://127.0.0.1:8000/blog/best-time-to-apply-for-europe-visas', NULL, 101, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(314, 'blog_posts', 2, 'best-time-to-apply-for-europe-visas', 'ar', 'أفضل وقت للتقديم على تأشيرات أوروبا', 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.', 'أفضل وقت للتقديم على تأشيرات أوروبا\nمقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.\nتنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.\nمحتوى السفر', 'http://127.0.0.1:8000/blog/best-time-to-apply-for-europe-visas', NULL, 102, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(315, 'blog_posts', 3, 'how-to-choose-the-right-hotel', 'en', 'How to choose the right hotel for your trip', 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.', 'How to choose the right hotel for your trip\nA practical Travel Wave article designed to help travelers make better booking and planning decisions.\nTravel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.\nTravel Insights', 'http://127.0.0.1:8000/blog/how-to-choose-the-right-hotel', NULL, 103, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(316, 'blog_posts', 3, 'how-to-choose-the-right-hotel', 'ar', 'كيف تختار الفندق المناسب لرحلتك', 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.', 'كيف تختار الفندق المناسب لرحلتك\nمقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.\nتنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.\nمحتوى السفر', 'http://127.0.0.1:8000/blog/how-to-choose-the-right-hotel', NULL, 104, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(317, 'contact_details', NULL, 'site-contact', 'en', 'Travel Wave Contact Details', 'Phone, WhatsApp, email, address, and working hours.', 'info@travelwave.com\n+20 100 123 4567\n+20 122 555 7788\nNasr City, Cairo, Egypt\nDaily from 10:00 AM to 8:00 PM', 'http://127.0.0.1:8000/contact', '{\"email\":\"info@travelwave.com\",\"phone\":\"+20 100 123 4567\",\"whatsapp\":\"+20 100 123 4567\"}', 105, '2026-03-27 02:59:33', '2026-03-27 02:59:33'),
(318, 'contact_details', NULL, 'site-contact', 'ar', 'بيانات التواصل مع Travel Wave', 'هاتف وواتساب وبريد إلكتروني وعنوان ومواعيد العمل.', 'info@travelwave.com\n+20 100 123 4567\n+20 122 555 7788\nمدينة نصر، القاهرة، مصر\nيوميًا من 10 صباحًا حتى 8 مساءً', 'http://127.0.0.1:8000/contact', '{\"email\":\"info@travelwave.com\",\"phone\":\"+20 100 123 4567\",\"whatsapp\":\"+20 100 123 4567\"}', 106, '2026-03-27 02:59:33', '2026-03-27 02:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `commission_statements`
--

CREATE TABLE `commission_statements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `basis_type` varchar(100) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `earned_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `remaining_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `payment_status` varchar(30) NOT NULL DEFAULT 'unpaid',
  `calculation_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`calculation_snapshot`)),
  `note` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_customers`
--

CREATE TABLE `crm_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `customer_code` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `crm_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crm_service_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crm_service_subtype_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `stage` varchar(40) NOT NULL DEFAULT 'new_customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `converted_at` timestamp NULL DEFAULT NULL,
  `appointment_at` timestamp NULL DEFAULT NULL,
  `submission_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_customer_activities`
--

CREATE TABLE `crm_customer_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crm_customer_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_type` varchar(80) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_documents`
--

CREATE TABLE `crm_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crm_document_category_id` bigint(20) UNSIGNED NOT NULL,
  `documentable_type` varchar(255) NOT NULL,
  `documentable_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `original_file_name` varchar(255) NOT NULL,
  `stored_file_name` varchar(255) NOT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'local',
  `directory` varchar(255) DEFAULT NULL,
  `path` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'uploaded',
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_document_categories`
--

CREATE TABLE `crm_document_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_document_categories`
--

INSERT INTO `crm_document_categories` (`id`, `slug`, `name_ar`, `name_en`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'passport', 'جواز السفر', 'Passport', 1, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(2, 'bank-statement', 'كشف حساب', 'Bank Statement', 2, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(3, 'employment-letter', 'خطاب جهة العمل', 'Employment Letter', 3, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(4, 'commercial-register', 'السجل التجاري', 'Commercial Register', 4, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(5, 'tax-card', 'البطاقة الضريبية', 'Tax Card', 5, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(6, 'family-registry', 'قيد عائلي', 'Family Registry', 6, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(7, 'movement-certificate', 'شهادة تحركات', 'Movement Certificate', 7, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(8, 'translation', 'ترجمة', 'Translation', 8, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(9, 'invitation', 'دعوة', 'Invitation', 9, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(10, 'insurance', 'تأمين', 'Insurance', 10, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(11, 'flight-booking', 'حجز طيران', 'Flight Booking', 11, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(12, 'hotel-booking', 'حجز فندق', 'Hotel Booking', 12, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(13, 'national-id', 'بطاقة رقم قومي', 'National ID', 13, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(14, 'other', 'أخرى', 'Other', 14, 1, '2026-03-25 20:41:09', '2026-03-25 20:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `crm_follow_ups`
--

CREATE TABLE `crm_follow_ups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `crm_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `completed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `scheduled_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reminder_offset_minutes` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `remind_at` timestamp NULL DEFAULT NULL,
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  `completion_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_information`
--

CREATE TABLE `crm_information` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `priority` varchar(255) DEFAULT NULL,
  `audience_type` varchar(255) NOT NULL,
  `event_date` date DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_information_recipients`
--

CREATE TABLE `crm_information_recipients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crm_information_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `seen_at` timestamp NULL DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_lead_assignments`
--

CREATE TABLE `crm_lead_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `old_assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `new_assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_lead_notes`
--

CREATE TABLE `crm_lead_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_lead_sources`
--

CREATE TABLE `crm_lead_sources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_lead_sources`
--

INSERT INTO `crm_lead_sources` (`id`, `name_en`, `name_ar`, `slug`, `is_default`, `is_active`, `sort_order`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Facebook (lead Generation)', 'فيسبوك (ليد جنريشن)', 'facebook-lead-generation', 0, 1, 1, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(2, 'Facebook (message)', 'فيسبوك (رسائل)', 'facebook-message', 0, 1, 2, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(3, 'Whatsapp (message)', 'واتساب (رسائل)', 'whatsapp-message', 0, 1, 3, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(4, 'twitter', 'تويتر', 'twitter', 0, 1, 4, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(5, 'instgram', 'انستجرام', 'instagram', 0, 1, 5, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(6, 'linkedIn', 'لينكدإن', 'linkedin', 0, 1, 6, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(7, 'E-mail Marketing', 'التسويق عبر البريد الإلكتروني', 'email-marketing', 0, 1, 7, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(8, 'SMS', 'رسائل SMS', 'sms', 0, 1, 8, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(9, 'other', 'أخرى', 'other', 1, 1, 9, NULL, '2026-03-25 20:41:06', '2026-03-25 20:41:06');

-- --------------------------------------------------------

--
-- Table structure for table `crm_service_subtypes`
--

CREATE TABLE `crm_service_subtypes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crm_service_type_id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_service_subtypes`
--

INSERT INTO `crm_service_subtypes` (`id`, `crm_service_type_id`, `name_en`, `name_ar`, `slug`, `is_active`, `sort_order`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'European Union', 'الاتحاد الأوروبي', 'european-union', 1, 1, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07'),
(2, 1, 'Asian Countries', 'دول آسيا', 'asian-countries', 1, 2, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07'),
(3, 1, 'Arab Countries', 'الدول العربية', 'arab-countries', 1, 3, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07'),
(4, 1, 'USA, Canada, and Australia', 'أمريكا وكندا وأستراليا', 'usa-canada-australia', 1, 4, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `crm_service_types`
--

CREATE TABLE `crm_service_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `destination_label_en` varchar(255) DEFAULT NULL,
  `destination_label_ar` varchar(255) DEFAULT NULL,
  `requires_subtype` tinyint(1) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_service_types`
--

INSERT INTO `crm_service_types` (`id`, `name_en`, `name_ar`, `slug`, `destination_label_en`, `destination_label_ar`, `requires_subtype`, `is_default`, `is_active`, `sort_order`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'External Visas', 'تأشيرات خارجية', 'external-visas', 'Country', 'الدولة', 1, 1, 1, 1, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07'),
(2, 'Domestic Tourism', 'رحلات داخلية', 'domestic-tourism', 'Tourism Destination', 'الوجهة السياحية', 0, 0, 1, 2, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07'),
(3, 'Flight Tickets', 'تذاكر طيران', 'flight-tickets', 'Travel Destination', 'جهة السفر', 0, 0, 1, 3, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07'),
(4, 'Hotel Booking', 'حجز فنادق', 'hotel-booking', 'Hotel Destination', 'المدينة / الدولة', 0, 0, 1, 4, NULL, '2026-03-25 20:41:07', '2026-03-25 20:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `crm_statuses`
--

CREATE TABLE `crm_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status_group` varchar(20) NOT NULL DEFAULT 'primary',
  `color` varchar(30) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_statuses`
--

INSERT INTO `crm_statuses` (`id`, `name_en`, `name_ar`, `slug`, `status_group`, `color`, `sort_order`, `is_default`, `is_system`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'New Lead', 'ليد جديد', 'new-lead', 'lead', 'warning', 1, 1, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(2, 'No Answer', 'لم يتم الرد', 'no-answer', 'lead', 'secondary', 2, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(3, 'Not Interested', 'غير مهتم', 'not-interested', 'lead', 'danger', 8, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(4, 'Duplicate', 'مكرر', 'duplicate', 'lead', 'dark', 18, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(5, 'Qualified', 'مؤهل', 'qualified', 'primary', 'info', 7, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(6, 'Complete Customer', 'عميل مكتمل', 'complete-lead', 'primary', 'success', 6, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(7, 'Pending Callback', 'بانتظار إعادة الاتصال', 'pending-callback', 'secondary', 'secondary', 1, 1, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(8, 'WhatsApp Follow-up', 'متابعة واتساب', 'whatsapp-follow-up', 'secondary', 'success', 2, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(9, 'Awaiting Documents', 'بانتظار المستندات', 'awaiting-documents', 'secondary', 'warning', 3, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(10, 'Appointment Booked', 'تم حجز الموعد', 'appointment-booked', 'secondary', 'info', 4, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(11, 'Closed', 'مغلق', 'closed', 'lead', 'dark', 3, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(12, 'Unavailable', 'غير متاح', 'unavailable', 'lead', 'secondary', 4, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(13, 'Busy', 'مشغول', 'busy', 'lead', 'secondary', 5, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(14, 'Cancelled', 'إلغاء', 'cancelled', 'secondary', 'danger', 12, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(15, 'No Bank Account', 'لا يوجد حساب بنكي', 'no-bank-account', 'secondary', 'warning', 13, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(16, 'Bank Account أقل من 6 Months', 'حساب بنكي أقل من 6 أشهر', 'bank-account-less-than-6-months', 'secondary', 'warning', 14, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(17, 'Bank Account أقل من 80K', 'حساب بنكي أقل من 80 ألف', 'bank-account-less-than-80k', 'secondary', 'warning', 15, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(18, 'Work Contract, Not Tourism', 'عقد عمل وليس سياحة', 'work-contract-not-tourism', 'secondary', 'danger', 16, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(19, 'Wrong Number', 'الرقم غلط', 'wrong-number', 'lead', 'danger', 13, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(20, 'Far Location', 'خارج القاهرة المكان بعيد عليه', 'far-location', 'lead', 'secondary', 14, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(21, 'International Number', 'الرقم دولي', 'international-number', 'lead', 'secondary', 15, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(22, 'Documents Complete', 'الأوراق مكتملة', 'documents-complete', 'lead', 'success', 16, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(23, 'Missing Documents', 'أوراق ناقصة مستندات', 'missing-documents', 'secondary', 'warning', 21, 0, 1, 0, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(24, 'Call Later', 'اتصل لاحقًا', 'call-later', 'lead', 'info', 11, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:07'),
(25, 'Dedicated Number', 'الرقم مخصص', 'dedicated-number', 'lead', 'secondary', 6, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(26, 'Lazy Lead', 'كسلان', 'lazy-lead', 'lead', 'secondary', 7, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(27, 'Waiting for Bank Account', 'منتظر حساب بنكي', 'waiting-bank-account', 'lead', 'warning', 9, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(28, 'Bank Account Less Than 120K', 'موجود حساب بنكي أقل من 120 ألف جنيه', 'bank-account-less-than-120k', 'lead', 'warning', 10, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(29, 'Will Be Contacted', 'سيتم التواصل', 'will-be-contacted', 'lead', 'info', 11, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(30, 'Work Not Tourism', 'عاوز عنده عمل مش سياحة', 'work-not-tourism', 'lead', 'danger', 12, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(31, 'Documents Need Commercial Register or Employment Letter', 'الأوراق مكملة (اخلص سجل تجاري أو خطاب عمل)', 'documents-needs-followup', 'lead', 'warning', 17, 0, 1, 1, '2026-03-25 20:41:06', '2026-03-25 20:41:06'),
(32, 'Merged', 'دمج', 'merged', 'primary', NULL, 999, 0, 1, 1, '2026-03-25 20:41:07', '2026-03-25 20:41:07');

-- --------------------------------------------------------

--
-- Table structure for table `crm_status_updates`
--

CREATE TABLE `crm_status_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED NOT NULL,
  `status_level` varchar(20) NOT NULL,
  `old_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `new_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_at` timestamp NULL DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_tasks`
--

CREATE TABLE `crm_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inquiry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `task_type` varchar(20) NOT NULL DEFAULT 'lead',
  `category` varchar(100) DEFAULT NULL,
  `priority` varchar(20) NOT NULL DEFAULT 'medium',
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `due_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `closed_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `crm_tasks`
--

INSERT INTO `crm_tasks` (`id`, `inquiry_id`, `assigned_user_id`, `created_by`, `title`, `description`, `notes`, `task_type`, `category`, `priority`, `status`, `due_at`, `completed_at`, `last_activity_at`, `closed_by`, `closed_note`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 1, 'Collect missing document', NULL, NULL, 'general', 'documents', 'medium', 'new', '2026-03-24 20:41:09', NULL, '2026-03-24 20:41:09', NULL, NULL, '2026-03-25 20:41:09', '2026-03-25 20:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `crm_task_activities`
--

CREATE TABLE `crm_task_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `crm_task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_type` varchar(40) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `destination_type` varchar(255) NOT NULL DEFAULT 'domestic',
  `excerpt_en` text DEFAULT NULL,
  `excerpt_ar` text DEFAULT NULL,
  `subtitle_en` varchar(255) DEFAULT NULL,
  `subtitle_ar` varchar(255) DEFAULT NULL,
  `hero_badge_en` varchar(255) DEFAULT NULL,
  `hero_badge_ar` varchar(255) DEFAULT NULL,
  `hero_title_en` varchar(255) DEFAULT NULL,
  `hero_title_ar` varchar(255) DEFAULT NULL,
  `hero_subtitle_en` text DEFAULT NULL,
  `hero_subtitle_ar` text DEFAULT NULL,
  `hero_cta_text_en` varchar(255) DEFAULT NULL,
  `hero_cta_text_ar` varchar(255) DEFAULT NULL,
  `hero_cta_url` varchar(255) DEFAULT NULL,
  `hero_secondary_cta_text_en` varchar(255) DEFAULT NULL,
  `hero_secondary_cta_text_ar` varchar(255) DEFAULT NULL,
  `hero_secondary_cta_url` varchar(255) DEFAULT NULL,
  `hero_overlay_opacity` decimal(3,2) NOT NULL DEFAULT 0.45,
  `hero_image` varchar(255) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `hero_mobile_image` varchar(255) DEFAULT NULL,
  `flag_image` varchar(255) DEFAULT NULL,
  `overview_en` longtext DEFAULT NULL,
  `overview_ar` longtext DEFAULT NULL,
  `quick_info_title_en` varchar(255) DEFAULT NULL,
  `quick_info_title_ar` varchar(255) DEFAULT NULL,
  `quick_summary_destination_label_en` varchar(255) DEFAULT NULL,
  `quick_summary_destination_label_ar` varchar(255) DEFAULT NULL,
  `quick_summary_destination_icon` varchar(255) DEFAULT NULL,
  `quick_info_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quick_info_items`)),
  `about_title_en` varchar(255) DEFAULT NULL,
  `about_title_ar` varchar(255) DEFAULT NULL,
  `about_description_en` longtext DEFAULT NULL,
  `about_description_ar` longtext DEFAULT NULL,
  `about_image` varchar(255) DEFAULT NULL,
  `about_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`about_points`)),
  `detailed_title_en` varchar(255) DEFAULT NULL,
  `detailed_title_ar` varchar(255) DEFAULT NULL,
  `detailed_description_en` longtext DEFAULT NULL,
  `detailed_description_ar` longtext DEFAULT NULL,
  `best_time_badge_en` varchar(255) DEFAULT NULL,
  `best_time_badge_ar` varchar(255) DEFAULT NULL,
  `best_time_title_en` varchar(255) DEFAULT NULL,
  `best_time_title_ar` varchar(255) DEFAULT NULL,
  `best_time_description_en` longtext DEFAULT NULL,
  `best_time_description_ar` longtext DEFAULT NULL,
  `highlights_section_label_en` varchar(255) DEFAULT NULL,
  `highlights_section_label_ar` varchar(255) DEFAULT NULL,
  `highlights_title_en` varchar(255) DEFAULT NULL,
  `highlights_title_ar` varchar(255) DEFAULT NULL,
  `highlight_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlight_items`)),
  `services_title_en` varchar(255) DEFAULT NULL,
  `services_title_ar` varchar(255) DEFAULT NULL,
  `services_intro_en` text DEFAULT NULL,
  `services_intro_ar` text DEFAULT NULL,
  `service_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_items`)),
  `documents_title_en` varchar(255) DEFAULT NULL,
  `documents_title_ar` varchar(255) DEFAULT NULL,
  `documents_subtitle_en` text DEFAULT NULL,
  `documents_subtitle_ar` text DEFAULT NULL,
  `document_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`document_items`)),
  `steps_title_en` varchar(255) DEFAULT NULL,
  `steps_title_ar` varchar(255) DEFAULT NULL,
  `step_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`step_items`)),
  `pricing_title_en` varchar(255) DEFAULT NULL,
  `pricing_title_ar` varchar(255) DEFAULT NULL,
  `pricing_notes_en` text DEFAULT NULL,
  `pricing_notes_ar` text DEFAULT NULL,
  `pricing_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pricing_items`)),
  `faq_title_en` varchar(255) DEFAULT NULL,
  `faq_title_ar` varchar(255) DEFAULT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `packages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`packages`)),
  `included_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`included_items`)),
  `excluded_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`excluded_items`)),
  `itinerary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`itinerary`)),
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `cta_title_en` varchar(255) DEFAULT NULL,
  `cta_title_ar` varchar(255) DEFAULT NULL,
  `cta_text_en` text DEFAULT NULL,
  `cta_text_ar` text DEFAULT NULL,
  `cta_button_en` varchar(255) DEFAULT NULL,
  `cta_button_ar` varchar(255) DEFAULT NULL,
  `cta_secondary_button_en` varchar(255) DEFAULT NULL,
  `cta_secondary_button_ar` varchar(255) DEFAULT NULL,
  `cta_secondary_url` varchar(255) DEFAULT NULL,
  `cta_background_image` varchar(255) DEFAULT NULL,
  `form_title_en` varchar(255) DEFAULT NULL,
  `form_title_ar` varchar(255) DEFAULT NULL,
  `form_subtitle_en` text DEFAULT NULL,
  `form_subtitle_ar` text DEFAULT NULL,
  `form_submit_text_en` varchar(255) DEFAULT NULL,
  `form_submit_text_ar` varchar(255) DEFAULT NULL,
  `form_visible_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_visible_fields`)),
  `cta_url` varchar(255) DEFAULT NULL,
  `meta_title_en` varchar(255) DEFAULT NULL,
  `meta_title_ar` varchar(255) DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `show_hero` tinyint(1) NOT NULL DEFAULT 1,
  `show_quick_info` tinyint(1) NOT NULL DEFAULT 1,
  `show_about` tinyint(1) NOT NULL DEFAULT 1,
  `show_detailed` tinyint(1) NOT NULL DEFAULT 1,
  `show_best_time` tinyint(1) NOT NULL DEFAULT 1,
  `show_highlights` tinyint(1) NOT NULL DEFAULT 1,
  `show_services` tinyint(1) NOT NULL DEFAULT 1,
  `show_documents` tinyint(1) NOT NULL DEFAULT 1,
  `show_steps` tinyint(1) NOT NULL DEFAULT 1,
  `show_pricing` tinyint(1) NOT NULL DEFAULT 1,
  `show_faq` tinyint(1) NOT NULL DEFAULT 1,
  `show_cta` tinyint(1) NOT NULL DEFAULT 1,
  `show_form` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `title_en`, `title_ar`, `slug`, `destination_type`, `excerpt_en`, `excerpt_ar`, `subtitle_en`, `subtitle_ar`, `hero_badge_en`, `hero_badge_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_cta_text_en`, `hero_cta_text_ar`, `hero_cta_url`, `hero_secondary_cta_text_en`, `hero_secondary_cta_text_ar`, `hero_secondary_cta_url`, `hero_overlay_opacity`, `hero_image`, `featured_image`, `hero_mobile_image`, `flag_image`, `overview_en`, `overview_ar`, `quick_info_title_en`, `quick_info_title_ar`, `quick_summary_destination_label_en`, `quick_summary_destination_label_ar`, `quick_summary_destination_icon`, `quick_info_items`, `about_title_en`, `about_title_ar`, `about_description_en`, `about_description_ar`, `about_image`, `about_points`, `detailed_title_en`, `detailed_title_ar`, `detailed_description_en`, `detailed_description_ar`, `best_time_badge_en`, `best_time_badge_ar`, `best_time_title_en`, `best_time_title_ar`, `best_time_description_en`, `best_time_description_ar`, `highlights_section_label_en`, `highlights_section_label_ar`, `highlights_title_en`, `highlights_title_ar`, `highlight_items`, `services_title_en`, `services_title_ar`, `services_intro_en`, `services_intro_ar`, `service_items`, `documents_title_en`, `documents_title_ar`, `documents_subtitle_en`, `documents_subtitle_ar`, `document_items`, `steps_title_en`, `steps_title_ar`, `step_items`, `pricing_title_en`, `pricing_title_ar`, `pricing_notes_en`, `pricing_notes_ar`, `pricing_items`, `faq_title_en`, `faq_title_ar`, `highlights`, `packages`, `included_items`, `excluded_items`, `itinerary`, `gallery`, `faqs`, `cta_title_en`, `cta_title_ar`, `cta_text_en`, `cta_text_ar`, `cta_button_en`, `cta_button_ar`, `cta_secondary_button_en`, `cta_secondary_button_ar`, `cta_secondary_url`, `cta_background_image`, `form_title_en`, `form_title_ar`, `form_subtitle_en`, `form_subtitle_ar`, `form_submit_text_en`, `form_submit_text_ar`, `form_visible_fields`, `cta_url`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `show_hero`, `show_quick_info`, `show_about`, `show_detailed`, `show_best_time`, `show_highlights`, `show_services`, `show_documents`, `show_steps`, `show_pricing`, `show_faq`, `show_cta`, `show_form`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'Sharm El Sheikh', 'شرم الشيخ', 'sharm-el-sheikh', 'domestic', 'Beach escapes, resort stays, and organized Red Sea programs.', 'رحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.', 'Beach escapes, resort stays, and organized Red Sea programs.', 'رحلات شاطئية وإقامات فندقية وبرامج منظمة على البحر الأحمر.', 'Domestic Tourism', 'السياحة الداخلية', 'Sharm El Sheikh Trips with Travel Wave', 'رحلات شرم الشيخ مع Travel Wave', 'Travel Wave helps organize the hotel, timing, and trip details in a cleaner domestic travel experience.', 'تساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.', 'Book Now', 'احجز الآن', '#destination-form', 'Quick Summary', 'ملخص سريع', '#destination-summary', 0.45, 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/slide-3.svg', NULL, 'Enjoy a memorable experience in Sharm El Sheikh with flexible packages, trusted hotels, and support before and after booking.', 'استمتع بتجربة مميزة في شرم الشيخ مع باقات مرنة وفنادق موثوقة ودعم قبل الحجز وبعده.', 'Quick Summary', 'ملخص سريع', 'Destination', 'الوجهة', 'material-symbols:globe-location-pin-outline', '[{\"label_en\":\"Trip Type\",\"label_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c\",\"value_en\":\"Domestic tourism package\",\"value_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0633\\u064a\\u0627\\u062d\\u0629 \\u062f\\u0627\\u062e\\u0644\\u064a\\u0629\",\"icon\":\"material-symbols:travel\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Suggested Duration\",\"label_ar\":\"\\u0627\\u0644\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d\\u0629\",\"value_en\":\"3 to 5 nights\",\"value_ar\":\"3 \\u0625\\u0644\\u0649 5 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"icon\":\"mdi:clock-outline\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Starting Price\",\"label_ar\":\"\\u0627\\u0644\\u0633\\u0639\\u0631 \\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646\",\"value_en\":\"Ask for latest offer\",\"value_ar\":\"\\u0627\\u0637\\u0644\\u0628 \\u0623\\u062d\\u062f\\u062b \\u0639\\u0631\\u0636\",\"icon\":\"solar:tag-price-linear\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Best Time\",\"label_ar\":\"\\u0623\\u0641\\u0636\\u0644 \\u0648\\u0642\\u062a\",\"value_en\":\"Depends on season\",\"value_ar\":\"\\u0628\\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0648\\u0633\\u0645\",\"icon\":\"solar:calendar-linear\",\"sort_order\":4,\"is_active\":true}]', 'About the Destination', 'نبذة عن الوجهة', 'Sharm El Sheikh remains one of the practical domestic options for travelers looking for a more organized holiday with hotels, transfers, and balanced activities.', 'شرم الشيخ من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', '[{\"text_en\":\"Suitable for couples, families, or short leisure breaks.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0623\\u0632\\u0648\\u0627\\u062c \\u0648\\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631\\u0629.\"},{\"text_en\":\"Flexible hotel levels and package structure.\",\"text_ar\":\"\\u0645\\u0631\\u0648\\u0646\\u0629 \\u0641\\u064a \\u0645\\u0633\\u062a\\u0648\\u064a\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0634\\u0643\\u0644 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c.\"},{\"text_en\":\"Travel Wave helps compare the options more clearly.\",\"text_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f Travel Wave \\u0639\\u0644\\u0649 \\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0627\\u0644\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0648\\u0636\\u062d.\"}]', 'Trip Details', 'تفاصيل الرحلة', 'Travel Wave organizes the destination around the right stay length, hotel category, and guest preferences.\r\n\r\nThis makes the trip easier to compare and confirm without getting lost in scattered options.', 'تنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\r\n\r\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.', 'Best Time', 'أفضل وقت', 'Best Time to Visit', 'أفضل وقت للزيارة', 'Best travel timing depends on weather preference, budget level, and the type of activities planned.', 'الربيع والخريف غالبًا أفضل الفترات من حيث الطقس والراحة، بينما يبقى الصيف مناسبًا للعائلات وبرامج المنتجعات.', 'Helpful Guidance Points', 'أهم الإرشادات', 'Top Highlights', 'أبرز النقاط المهمة', '[{\"title_en\":\"\\u062e\\u0644\\u064a\\u062c \\u0646\\u0639\\u0645\\u0629\",\"title_ar\":\"\\u062e\\u0644\\u064a\\u062c \\u0646\\u0639\\u0645\\u0629\",\"description_en\":\"\\u0645\\u0646\\u0637\\u0642\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u062a\\u0646\\u0632\\u0647 \\u0648\\u0627\\u0644\\u062e\\u0631\\u0648\\u062c\\u0627\\u062a \\u0627\\u0644\\u0645\\u0633\\u0627\\u0626\\u064a\\u0629 \\u0627\\u0644\\u0633\\u0647\\u0644\\u0629.\",\"description_ar\":\"\\u0645\\u0646\\u0637\\u0642\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u062a\\u0646\\u0632\\u0647 \\u0648\\u0627\\u0644\\u062e\\u0631\\u0648\\u062c\\u0627\\u062a \\u0627\\u0644\\u0645\\u0633\\u0627\\u0626\\u064a\\u0629 \\u0627\\u0644\\u0633\\u0647\\u0644\\u0629.\",\"image\":\"hero-slides\\/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\",\"icon\":\"material-symbols:beach-access-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"\\u0631\\u0623\\u0633 \\u0645\\u062d\\u0645\\u062f\",\"title_ar\":\"\\u0631\\u0623\\u0633 \\u0645\\u062d\\u0645\\u062f\",\"description_en\":\"\\u0645\\u0646 \\u0623\\u0634\\u0647\\u0631 \\u0627\\u0644\\u0645\\u0646\\u0627\\u0637\\u0642 \\u0627\\u0644\\u0628\\u062d\\u0631\\u064a\\u0629 \\u0644\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629 \\u0627\\u0644\\u0645\\u0631\\u062a\\u0628\\u0637\\u0629 \\u0628\\u0627\\u0644\\u0628\\u062d\\u0631.\",\"description_ar\":\"\\u0645\\u0646 \\u0623\\u0634\\u0647\\u0631 \\u0627\\u0644\\u0645\\u0646\\u0627\\u0637\\u0642 \\u0627\\u0644\\u0628\\u062d\\u0631\\u064a\\u0629 \\u0644\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629 \\u0627\\u0644\\u0645\\u0631\\u062a\\u0628\\u0637\\u0629 \\u0628\\u0627\\u0644\\u0628\\u062d\\u0631.\",\"image\":\"hero-slides\\/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\",\"icon\":\"material-symbols:scuba-diving\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"\\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a\",\"title_ar\":\"\\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a\",\"description_en\":\"\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0645\\u062a\\u0646\\u0648\\u0639\\u0629 \\u0645\\u0646 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u062a\\u0646\\u0627\\u0633\\u0628 \\u0627\\u0644\\u0623\\u0632\\u0648\\u0627\\u062c \\u0648\\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a.\",\"description_ar\":\"\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0645\\u062a\\u0646\\u0648\\u0639\\u0629 \\u0645\\u0646 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u062a\\u0646\\u0627\\u0633\\u0628 \\u0627\\u0644\\u0623\\u0632\\u0648\\u0627\\u062c \\u0648\\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a.\",\"image\":\"hero-slides\\/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\",\"icon\":\"material-symbols:hotel-class-outline-rounded\",\"sort_order\":3,\"is_active\":true}]', 'Included Services', 'الخدمات المتضمنة', 'The trip can be built around your timing, comfort level, and package preference.', 'يمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.', '[{\"title_en\":\"\\u062d\\u062c\\u0632 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642\",\"title_ar\":\"\\u062d\\u062c\\u0632 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642\",\"description_en\":\"\\u062a\\u0631\\u0634\\u064a\\u062d \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u064a\\u0632\\u0627\\u0646\\u064a\\u0629 \\u0648\\u0637\\u0628\\u064a\\u0639\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629.\",\"description_ar\":\"\\u062a\\u0631\\u0634\\u064a\\u062d \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u064a\\u0632\\u0627\\u0646\\u064a\\u0629 \\u0648\\u0637\\u0628\\u064a\\u0639\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629.\",\"icon\":\"material-symbols:hotel-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"\\u0627\\u0644\\u062a\\u0646\\u0642\\u0644\\u0627\\u062a\",\"title_ar\":\"\\u0627\\u0644\\u062a\\u0646\\u0642\\u0644\\u0627\\u062a\",\"description_en\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0627\\u0644\\u0627\\u0646\\u062a\\u0642\\u0627\\u0644\\u0627\\u062a \\u0627\\u0644\\u0645\\u0631\\u062a\\u0628\\u0637\\u0629 \\u0628\\u0627\\u0644\\u0648\\u0635\\u0648\\u0644 \\u0648\\u0627\\u0644\\u0645\\u063a\\u0627\\u062f\\u0631\\u0629.\",\"description_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0627\\u0644\\u0627\\u0646\\u062a\\u0642\\u0627\\u0644\\u0627\\u062a \\u0627\\u0644\\u0645\\u0631\\u062a\\u0628\\u0637\\u0629 \\u0628\\u0627\\u0644\\u0648\\u0635\\u0648\\u0644 \\u0648\\u0627\\u0644\\u0645\\u063a\\u0627\\u062f\\u0631\\u0629.\",\"icon\":\"material-symbols:directions-car-outline-rounded\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629\",\"title_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629\",\"description_en\":\"\\u0645\\u0648\\u0627\\u0632\\u0646\\u0629 \\u0648\\u0627\\u0636\\u062d\\u0629 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0627\\u0633\\u062a\\u0631\\u062e\\u0627\\u0621 \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629.\",\"description_ar\":\"\\u0645\\u0648\\u0627\\u0632\\u0646\\u0629 \\u0648\\u0627\\u0636\\u062d\\u0629 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0627\\u0633\\u062a\\u0631\\u062e\\u0627\\u0621 \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629.\",\"icon\":\"material-symbols:concierge\",\"sort_order\":3,\"is_active\":true},{\"title_en\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0645\\u0633\\u062a\\u0645\\u0631\\u0629\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0645\\u0633\\u062a\\u0645\\u0631\\u0629\",\"description_en\":\"\\u062a\\u0648\\u0627\\u0635\\u0644 \\u0623\\u0648\\u0636\\u062d \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0628\\u0639\\u062f \\u062a\\u0623\\u0643\\u064a\\u062f \\u0627\\u0644\\u062d\\u062c\\u0632.\",\"description_ar\":\"\\u062a\\u0648\\u0627\\u0635\\u0644 \\u0623\\u0648\\u0636\\u062d \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0628\\u0639\\u062f \\u062a\\u0623\\u0643\\u064a\\u062f \\u0627\\u0644\\u062d\\u062c\\u0632.\",\"icon\":\"material-symbols:concierge\",\"sort_order\":4,\"is_active\":true}]', 'Required Before Booking', 'ما يلزم قبل الحجز', 'Domestic travel does not need visa paperwork, but some basic details help confirm the booking faster.', 'السفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.', '[{\"title_en\":\"Traveler Names\",\"title_ar\":\"\\u0623\\u0633\\u0645\\u0627\\u0621 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0641\\u0631\\u064a\\u0646\",\"description_en\":\"Correct names for reservation records.\",\"description_ar\":\"\\u0627\\u0644\\u0623\\u0633\\u0645\\u0627\\u0621 \\u0627\\u0644\\u0635\\u062d\\u064a\\u062d\\u0629 \\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u062d\\u062c\\u0632.\",\"icon\":\"material-symbols:badge-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Travel Dates\",\"title_ar\":\"\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u0633\\u0641\\u0631\",\"description_en\":\"Preferred travel period and stay length.\",\"description_ar\":\"\\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0641\\u0636\\u0644\\u0629 \\u0648\\u0639\\u062f\\u062f \\u0627\\u0644\\u0644\\u064a\\u0627\\u0644\\u064a.\",\"icon\":\"solar:calendar-linear\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Guest Count\",\"title_ar\":\"\\u0639\\u062f\\u062f \\u0627\\u0644\\u0623\\u0641\\u0631\\u0627\\u062f\",\"description_en\":\"Used to match room type and offer level.\",\"description_ar\":\"\\u0644\\u062a\\u062d\\u062f\\u064a\\u062f \\u0646\\u0648\\u0639 \\u0627\\u0644\\u063a\\u0631\\u0641\\u0629 \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0639\\u0631\\u0636.\",\"icon\":\"material-symbols:group-outline-rounded\",\"sort_order\":3,\"is_active\":true}]', 'Booking Steps', 'خطوات الحجز', '[{\"title_en\":\"Choose the destination\",\"title_ar\":\"\\u0627\\u062e\\u062a\\u0631 \\u0627\\u0644\\u0648\\u062c\\u0647\\u0629\",\"description_en\":\"Share the trip type and preferred dates.\",\"description_ar\":\"\\u062d\\u062f\\u062f \\u0637\\u0628\\u064a\\u0639\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0648\\u0627\\u0644\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629.\",\"icon\":\"\",\"step_number\":1,\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Review the options\",\"title_ar\":\"\\u0631\\u0627\\u062c\\u0639 \\u0627\\u0644\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a\",\"description_en\":\"Compare the suggested hotels and packages.\",\"description_ar\":\"\\u0642\\u0627\\u0631\\u0646 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0628\\u0627\\u0642\\u0627\\u062a \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d\\u0629.\",\"icon\":\"\",\"step_number\":2,\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Confirm the booking\",\"title_ar\":\"\\u0623\\u0643\\u062f \\u0627\\u0644\\u062d\\u062c\\u0632\",\"description_en\":\"Choose the final option and complete the details.\",\"description_ar\":\"\\u0627\\u062e\\u062a\\u0631 \\u0627\\u0644\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0646\\u0647\\u0627\\u0626\\u064a \\u0648\\u0627\\u0633\\u062a\\u0643\\u0645\\u0644 \\u0627\\u0644\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644.\",\"icon\":\"\",\"step_number\":3,\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Receive confirmation\",\"title_ar\":\"\\u0627\\u0633\\u062a\\u0644\\u0645 \\u0627\\u0644\\u062a\\u0623\\u0643\\u064a\\u062f\",\"description_en\":\"Travel Wave confirms the reservation and next steps.\",\"description_ar\":\"\\u062a\\u0624\\u0643\\u062f Travel Wave \\u0627\\u0644\\u062d\\u062c\\u0632 \\u0648\\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629.\",\"icon\":\"\",\"step_number\":4,\"sort_order\":4,\"is_active\":true}]', 'Pricing Overview', 'نظرة على الأسعار', 'Final pricing changes by hotel level, trip duration, and season.', 'تتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.', '[{\"label_en\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c 3 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"label_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c 3 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"value_en\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 6,900 \\u062c\\u0646\\u064a\\u0647\",\"value_ar\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 6,900 \\u062c\\u0646\\u064a\\u0647\",\"note_en\":\"\\u0645\\u0646\\u0627\\u0633\\u0628 \\u0644\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631\\u0629.\",\"note_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628 \\u0644\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631\\u0629.\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c 5 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"label_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c 5 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"value_en\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 9,250 \\u062c\\u0646\\u064a\\u0647\",\"value_ar\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 9,250 \\u062c\\u0646\\u064a\\u0647\",\"note_en\":\"\\u0623\\u0646\\u0633\\u0628 \\u0644\\u0644\\u0645\\u0632\\u062c \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629.\",\"note_ar\":\"\\u0623\\u0646\\u0633\\u0628 \\u0644\\u0644\\u0645\\u0632\\u062c \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629.\",\"sort_order\":2,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', '[{\"text_en\":\"Resorts and hotels selected by category and budget.\",\"text_ar\":\"\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a \\u0648\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0645\\u062e\\u062a\\u0627\\u0631\\u0629 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0641\\u0626\\u0629 \\u0648\\u0627\\u0644\\u0645\\u064a\\u0632\\u0627\\u0646\\u064a\\u0629.\"},{\"text_en\":\"Programs suitable for families, couples, and groups.\",\"text_ar\":\"\\u0628\\u0631\\u0627\\u0645\\u062c \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0623\\u0632\\u0648\\u0627\\u062c \\u0648\\u0627\\u0644\\u0645\\u062c\\u0645\\u0648\\u0639\\u0627\\u062a.\"},{\"text_en\":\"Transport and optional activities when requested.\",\"text_ar\":\"\\u0625\\u0645\\u0643\\u0627\\u0646\\u064a\\u0629 \\u0625\\u0636\\u0627\\u0641\\u0629 \\u0627\\u0644\\u062a\\u0646\\u0642\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629 \\u0639\\u0646\\u062f \\u0627\\u0644\\u0637\\u0644\\u0628.\"}]', '[{\"text_en\":\"Weekend getaway packages.\",\"text_ar\":\"\\u0628\\u0627\\u0642\\u0627\\u062a \\u0639\\u0637\\u0644\\u0627\\u062a \\u0646\\u0647\\u0627\\u064a\\u0629 \\u0627\\u0644\\u0623\\u0633\\u0628\\u0648\\u0639.\"},{\"text_en\":\"4-night family offers.\",\"text_ar\":\"\\u0639\\u0631\\u0648\\u0636 \\u0639\\u0627\\u0626\\u0644\\u064a\\u0629 \\u0644\\u0645\\u062f\\u0629 4 \\u0644\\u064a\\u0627\\u0644\\u064d.\"},{\"text_en\":\"Seasonal high-demand offers.\",\"text_ar\":\"\\u0639\\u0631\\u0648\\u0636 \\u0645\\u0648\\u0633\\u0645\\u064a\\u0629 \\u0644\\u0644\\u0637\\u0644\\u0628\\u0627\\u062a \\u0627\\u0644\\u0645\\u0631\\u062a\\u0641\\u0639\\u0629.\"}]', '[{\"text_en\":\"Accommodation\",\"text_ar\":\"\\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\"},{\"text_en\":\"Meals depending on package type\",\"text_ar\":\"\\u0627\\u0644\\u0648\\u062c\\u0628\\u0627\\u062a \\u062d\\u0633\\u0628 \\u0646\\u0648\\u0639 \\u0627\\u0644\\u0628\\u0627\\u0642\\u0629\"},{\"text_en\":\"Support and follow-up\",\"text_ar\":\"\\u0627\\u0644\\u062f\\u0639\\u0645 \\u0648\\u0627\\u0644\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629\"}]', '[{\"text_en\":\"Personal spending\",\"text_ar\":\"\\u0627\\u0644\\u0645\\u0635\\u0631\\u0648\\u0641\\u0627\\u062a \\u0627\\u0644\\u0634\\u062e\\u0635\\u064a\\u0629\"},{\"text_en\":\"Optional tours unless stated\",\"text_ar\":\"\\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0627\\u062e\\u062a\\u064a\\u0627\\u0631\\u064a\\u0629 \\u0645\\u0627 \\u0644\\u0645 \\u064a\\u062a\\u0645 \\u0630\\u0643\\u0631\\u0647\\u0627\"}]', '[{\"text_en\":\"Arrival, hotel check-in, and free evening.\",\"text_ar\":\"\\u0627\\u0644\\u0648\\u0635\\u0648\\u0644 \\u0648\\u062a\\u0633\\u062c\\u064a\\u0644 \\u0627\\u0644\\u062f\\u062e\\u0648\\u0644 \\u0628\\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0623\\u0645\\u0633\\u064a\\u0629 \\u062d\\u0631\\u0629.\"},{\"text_en\":\"Beach or leisure day with optional activities.\",\"text_ar\":\"\\u064a\\u0648\\u0645 \\u0634\\u0627\\u0637\\u0626\\u064a \\u0623\\u0648 \\u062a\\u0631\\u0641\\u064a\\u0647\\u064a \\u0645\\u0639 \\u0623\\u0646\\u0634\\u0637\\u0629 \\u0627\\u062e\\u062a\\u064a\\u0627\\u0631\\u064a\\u0629.\"},{\"text_en\":\"Final day follow-up and departure support.\",\"text_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0641\\u064a \\u0627\\u0644\\u064a\\u0648\\u0645 \\u0627\\u0644\\u0623\\u062e\\u064a\\u0631 \\u0648\\u062f\\u0639\\u0645 \\u062d\\u062a\\u0649 \\u0627\\u0644\\u0645\\u063a\\u0627\\u062f\\u0631\\u0629.\"}]', '[]', '[{\"question_en\":\"Can the package be adjusted?\",\"question_ar\":\"\\u0647\\u0644 \\u064a\\u0645\\u0643\\u0646 \\u062a\\u0639\\u062f\\u064a\\u0644 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c\\u061f\",\"answer_en\":\"Yes, package level and stay details can often be tailored around your needs.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u064a\\u0645\\u0643\\u0646 \\u063a\\u0627\\u0644\\u0628\\u064b\\u0627 \\u062a\\u0639\\u062f\\u064a\\u0644 \\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0628\\u0627\\u0642\\u0629 \\u0648\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u062d\\u0633\\u0628 \\u0627\\u062d\\u062a\\u064a\\u0627\\u062c\\u0643.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"Are family options available?\",\"question_ar\":\"\\u0647\\u0644 \\u062a\\u0648\\u062c\\u062f \\u0628\\u0631\\u0627\\u0645\\u062c \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a\\u061f\",\"answer_en\":\"Yes, the destination can be prepared with family-friendly hotel and room options.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u064a\\u0645\\u0643\\u0646 \\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0628\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0645\\u0646 \\u062d\\u064a\\u062b \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u063a\\u0631\\u0641.\",\"sort_order\":2,\"is_active\":true}]', 'Ready to Book with More Clarity?', 'جاهز للحجز بشكل أوضح؟', 'Travel Wave helps compare the right options and organize the next step more smoothly.', 'تساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.', 'Start Booking', 'ابدأ الحجز', 'WhatsApp', 'واتساب', 'https://wa.me/201000000000', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'Ask About Sharm El Sheikh', 'استفسر عن شرم الشيخ', 'Send your details and Travel Wave will help with a suitable package.', 'أرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.', 'Send Request', 'أرسل الطلب', '[\"email\",\"travel_date\",\"return_date\",\"travelers_count\",\"message\"]', '#destination-form', NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2026-03-25 23:07:02', '2026-03-28 00:33:22', NULL, NULL),
(2, 'Hurghada', 'الغردقة', 'hurghada', 'domestic', 'A balanced domestic destination for beach stays, family travel, and activity-focused resort holidays.', 'وجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.', 'A balanced domestic destination for beach stays, family travel, and activity-focused resort holidays.', 'وجهة داخلية متوازنة للشاطئ والعائلات وبرامج الإقامة المليئة بالأنشطة.', 'Domestic Tourism', 'السياحة الداخلية', 'Hurghada Trips with Travel Wave', 'رحلات الغردقة مع Travel Wave', 'Travel Wave helps organize the hotel, timing, and trip details in a cleaner domestic travel experience.', 'تساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.', 'Book Now', 'احجز الآن', '#destination-form', 'Quick Summary', 'ملخص سريع', '#destination-summary', 0.45, 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/slide-3.svg', NULL, 'Travel Wave offers practical Hurghada packages for short breaks and seasonal travel.', 'تقدم Travel Wave باقات عملية إلى الغردقة تناسب العطلات القصيرة والمواسم المختلفة.', 'Quick Summary', 'ملخص سريع', 'Destination', 'الوجهة', 'material-symbols:globe-location-pin-outline', '[{\"label_en\":\"Trip Type\",\"label_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c\",\"value_en\":\"Domestic tourism package\",\"value_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0633\\u064a\\u0627\\u062d\\u0629 \\u062f\\u0627\\u062e\\u0644\\u064a\\u0629\",\"icon\":\"material-symbols:travel\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Suggested Duration\",\"label_ar\":\"\\u0627\\u0644\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d\\u0629\",\"value_en\":\"3 to 5 nights\",\"value_ar\":\"3 \\u0625\\u0644\\u0649 5 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"icon\":\"mdi:clock-outline\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Starting Price\",\"label_ar\":\"\\u0627\\u0644\\u0633\\u0639\\u0631 \\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646\",\"value_en\":\"Ask for latest offer\",\"value_ar\":\"\\u0627\\u0637\\u0644\\u0628 \\u0623\\u062d\\u062f\\u062b \\u0639\\u0631\\u0636\",\"icon\":\"solar:tag-price-linear\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Best Time\",\"label_ar\":\"\\u0623\\u0641\\u0636\\u0644 \\u0648\\u0642\\u062a\",\"value_en\":\"Depends on season\",\"value_ar\":\"\\u0628\\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0648\\u0633\\u0645\",\"icon\":\"solar:calendar-linear\",\"sort_order\":4,\"is_active\":true}]', 'About the Destination', 'نبذة عن الوجهة', 'Hurghada remains one of the practical domestic options for travelers looking for a more organized holiday with hotels, transfers, and balanced activities.', 'الغردقة من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', '[{\"text_en\":\"Suitable for couples, families, or short leisure breaks.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0623\\u0632\\u0648\\u0627\\u062c \\u0648\\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631\\u0629.\"},{\"text_en\":\"Flexible hotel levels and package structure.\",\"text_ar\":\"\\u0645\\u0631\\u0648\\u0646\\u0629 \\u0641\\u064a \\u0645\\u0633\\u062a\\u0648\\u064a\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0634\\u0643\\u0644 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c.\"},{\"text_en\":\"Travel Wave helps compare the options more clearly.\",\"text_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f Travel Wave \\u0639\\u0644\\u0649 \\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0627\\u0644\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0648\\u0636\\u062d.\"}]', 'Trip Details', 'تفاصيل الرحلة', 'Travel Wave organizes the destination around the right stay length, hotel category, and guest preferences.\n\nThis makes the trip easier to compare and confirm without getting lost in scattered options.', 'تنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.', 'Best Time', 'أفضل وقت', 'Best Time to Visit', 'أفضل وقت للزيارة', 'Best travel timing depends on weather preference, budget level, and the type of activities planned.', 'تناسب الغردقة معظم شهور السنة، ويظل الربيع والخريف من الفترات المفضلة لتوازن الطقس.', 'Helpful Guidance Points', 'أهم الإرشادات', 'Top Highlights', 'أبرز النقاط المهمة', '[{\"title_en\":\"\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0639\\u0627\\u0626\\u0644\\u064a\\u0629\",\"title_ar\":\"\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0639\\u0627\\u0626\\u0644\\u064a\\u0629\",\"description_en\":\"\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0643\\u062b\\u064a\\u0631\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u0627\\u0639\\u064a\\u0629.\",\"description_ar\":\"\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0643\\u062b\\u064a\\u0631\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u062c\\u0645\\u0627\\u0639\\u064a\\u0629.\",\"image\":\"hero-slides\\/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\",\"icon\":\"material-symbols:beach-access-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"\\u0639\\u0631\\u0648\\u0636 \\u0645\\u0648\\u0633\\u0645\\u064a\\u0629\",\"title_ar\":\"\\u0639\\u0631\\u0648\\u0636 \\u0645\\u0648\\u0633\\u0645\\u064a\\u0629\",\"description_en\":\"\\u0628\\u0631\\u0627\\u0645\\u062c \\u062a\\u062a\\u063a\\u064a\\u0631 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0648\\u0633\\u0645 \\u0648\\u0641\\u0626\\u0629 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642.\",\"description_ar\":\"\\u0628\\u0631\\u0627\\u0645\\u062c \\u062a\\u062a\\u063a\\u064a\\u0631 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0648\\u0633\\u0645 \\u0648\\u0641\\u0626\\u0629 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642.\",\"image\":\"hero-slides\\/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\",\"icon\":\"material-symbols:scuba-diving\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"\\u062a\\u0646\\u0648\\u0639 \\u0641\\u064a \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"title_ar\":\"\\u062a\\u0646\\u0648\\u0639 \\u0641\\u064a \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"description_en\":\"\\u0627\\u062e\\u062a\\u064a\\u0627\\u0631\\u0627\\u062a \\u0648\\u0627\\u0633\\u0639\\u0629 \\u062a\\u0646\\u0627\\u0633\\u0628 \\u0645\\u0633\\u062a\\u0648\\u064a\\u0627\\u062a \\u0645\\u062e\\u062a\\u0644\\u0641\\u0629 \\u0645\\u0646 \\u0627\\u0644\\u0645\\u064a\\u0632\\u0627\\u0646\\u064a\\u0629.\",\"description_ar\":\"\\u0627\\u062e\\u062a\\u064a\\u0627\\u0631\\u0627\\u062a \\u0648\\u0627\\u0633\\u0639\\u0629 \\u062a\\u0646\\u0627\\u0633\\u0628 \\u0645\\u0633\\u062a\\u0648\\u064a\\u0627\\u062a \\u0645\\u062e\\u062a\\u0644\\u0641\\u0629 \\u0645\\u0646 \\u0627\\u0644\\u0645\\u064a\\u0632\\u0627\\u0646\\u064a\\u0629.\",\"image\":\"hero-slides\\/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\",\"icon\":\"material-symbols:hotel-class-outline-rounded\",\"sort_order\":3,\"is_active\":true}]', 'Included Services', 'الخدمات المتضمنة', 'The trip can be built around your timing, comfort level, and package preference.', 'يمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.', '[{\"title_en\":\"\\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642\",\"title_ar\":\"\\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642\",\"description_en\":\"\\u0625\\u0638\\u0647\\u0627\\u0631 \\u0627\\u0644\\u0641\\u0631\\u0648\\u0642 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0641\\u0626\\u0627\\u062a \\u0648\\u0627\\u0644\\u0645\\u0648\\u0627\\u0642\\u0639 \\u0648\\u0646\\u0638\\u0627\\u0645 \\u0627\\u0644\\u0648\\u062c\\u0628\\u0627\\u062a.\",\"description_ar\":\"\\u0625\\u0638\\u0647\\u0627\\u0631 \\u0627\\u0644\\u0641\\u0631\\u0648\\u0642 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0641\\u0626\\u0627\\u062a \\u0648\\u0627\\u0644\\u0645\\u0648\\u0627\\u0642\\u0639 \\u0648\\u0646\\u0638\\u0627\\u0645 \\u0627\\u0644\\u0648\\u062c\\u0628\\u0627\\u062a.\",\"icon\":\"material-symbols:hotel-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"\\u062f\\u0639\\u0645 \\u0627\\u0644\\u062d\\u062c\\u0632\",\"title_ar\":\"\\u062f\\u0639\\u0645 \\u0627\\u0644\\u062d\\u062c\\u0632\",\"description_en\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0623\\u0648\\u0636\\u062d \\u0644\\u062a\\u0623\\u0643\\u064a\\u062f \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0648\\u0627\\u0644\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644.\",\"description_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0623\\u0648\\u0636\\u062d \\u0644\\u062a\\u0623\\u0643\\u064a\\u062f \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0648\\u0627\\u0644\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644.\",\"icon\":\"material-symbols:directions-car-outline-rounded\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0633\\u0641\\u0631\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0633\\u0641\\u0631\",\"description_en\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u0646\\u0647\\u0627\\u0626\\u064a\\u0629 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0627\\u0646\\u0637\\u0644\\u0627\\u0642.\",\"description_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u0646\\u0647\\u0627\\u0626\\u064a\\u0629 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0627\\u0646\\u0637\\u0644\\u0627\\u0642.\",\"icon\":\"material-symbols:concierge\",\"sort_order\":3,\"is_active\":true}]', 'Required Before Booking', 'ما يلزم قبل الحجز', 'Domestic travel does not need visa paperwork, but some basic details help confirm the booking faster.', 'السفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.', '[{\"title_en\":\"Traveler Names\",\"title_ar\":\"\\u0623\\u0633\\u0645\\u0627\\u0621 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0641\\u0631\\u064a\\u0646\",\"description_en\":\"Correct names for reservation records.\",\"description_ar\":\"\\u0627\\u0644\\u0623\\u0633\\u0645\\u0627\\u0621 \\u0627\\u0644\\u0635\\u062d\\u064a\\u062d\\u0629 \\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u062d\\u062c\\u0632.\",\"icon\":\"material-symbols:badge-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Travel Dates\",\"title_ar\":\"\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u0633\\u0641\\u0631\",\"description_en\":\"Preferred travel period and stay length.\",\"description_ar\":\"\\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0641\\u0636\\u0644\\u0629 \\u0648\\u0639\\u062f\\u062f \\u0627\\u0644\\u0644\\u064a\\u0627\\u0644\\u064a.\",\"icon\":\"solar:calendar-linear\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Guest Count\",\"title_ar\":\"\\u0639\\u062f\\u062f \\u0627\\u0644\\u0623\\u0641\\u0631\\u0627\\u062f\",\"description_en\":\"Used to match room type and offer level.\",\"description_ar\":\"\\u0644\\u062a\\u062d\\u062f\\u064a\\u062f \\u0646\\u0648\\u0639 \\u0627\\u0644\\u063a\\u0631\\u0641\\u0629 \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0639\\u0631\\u0636.\",\"icon\":\"material-symbols:group-outline-rounded\",\"sort_order\":3,\"is_active\":true}]', 'Booking Steps', 'خطوات الحجز', '[{\"title_en\":\"Choose the destination\",\"title_ar\":\"\\u0627\\u062e\\u062a\\u0631 \\u0627\\u0644\\u0648\\u062c\\u0647\\u0629\",\"description_en\":\"Share the trip type and preferred dates.\",\"description_ar\":\"\\u062d\\u062f\\u062f \\u0637\\u0628\\u064a\\u0639\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0648\\u0627\\u0644\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629.\",\"step_number\":1,\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Review the options\",\"title_ar\":\"\\u0631\\u0627\\u062c\\u0639 \\u0627\\u0644\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a\",\"description_en\":\"Compare the suggested hotels and packages.\",\"description_ar\":\"\\u0642\\u0627\\u0631\\u0646 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0628\\u0627\\u0642\\u0627\\u062a \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d\\u0629.\",\"step_number\":2,\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Confirm the booking\",\"title_ar\":\"\\u0623\\u0643\\u062f \\u0627\\u0644\\u062d\\u062c\\u0632\",\"description_en\":\"Choose the final option and complete the details.\",\"description_ar\":\"\\u0627\\u062e\\u062a\\u0631 \\u0627\\u0644\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0646\\u0647\\u0627\\u0626\\u064a \\u0648\\u0627\\u0633\\u062a\\u0643\\u0645\\u0644 \\u0627\\u0644\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644.\",\"step_number\":3,\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Receive confirmation\",\"title_ar\":\"\\u0627\\u0633\\u062a\\u0644\\u0645 \\u0627\\u0644\\u062a\\u0623\\u0643\\u064a\\u062f\",\"description_en\":\"Travel Wave confirms the reservation and next steps.\",\"description_ar\":\"\\u062a\\u0624\\u0643\\u062f Travel Wave \\u0627\\u0644\\u062d\\u062c\\u0632 \\u0648\\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629.\",\"step_number\":4,\"sort_order\":4,\"is_active\":true}]', 'Pricing Overview', 'نظرة على الأسعار', 'Final pricing changes by hotel level, trip duration, and season.', 'تتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.', '[{\"label_en\":\"\\u0627\\u0644\\u0628\\u0627\\u0642\\u0629 \\u0627\\u0644\\u0642\\u064a\\u0627\\u0633\\u064a\\u0629\",\"label_ar\":\"\\u0627\\u0644\\u0628\\u0627\\u0642\\u0629 \\u0627\\u0644\\u0642\\u064a\\u0627\\u0633\\u064a\\u0629\",\"value_en\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 5,800 \\u062c\\u0646\\u064a\\u0647\",\"value_ar\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 5,800 \\u062c\\u0646\\u064a\\u0647\",\"note_en\":\"\\u0628\\u062d\\u0633\\u0628 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0639\\u062f\\u062f \\u0627\\u0644\\u0644\\u064a\\u0627\\u0644\\u064a.\",\"note_ar\":\"\\u0628\\u062d\\u0633\\u0628 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0639\\u062f\\u062f \\u0627\\u0644\\u0644\\u064a\\u0627\\u0644\\u064a.\",\"sort_order\":1,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"question_en\":\"Can the package be adjusted?\",\"question_ar\":\"\\u0647\\u0644 \\u064a\\u0645\\u0643\\u0646 \\u062a\\u0639\\u062f\\u064a\\u0644 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c\\u061f\",\"answer_en\":\"Yes, package level and stay details can often be tailored around your needs.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u064a\\u0645\\u0643\\u0646 \\u063a\\u0627\\u0644\\u0628\\u064b\\u0627 \\u062a\\u0639\\u062f\\u064a\\u0644 \\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0628\\u0627\\u0642\\u0629 \\u0648\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u062d\\u0633\\u0628 \\u0627\\u062d\\u062a\\u064a\\u0627\\u062c\\u0643.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"Are family options available?\",\"question_ar\":\"\\u0647\\u0644 \\u062a\\u0648\\u062c\\u062f \\u0628\\u0631\\u0627\\u0645\\u062c \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a\\u061f\",\"answer_en\":\"Yes, the destination can be prepared with family-friendly hotel and room options.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u064a\\u0645\\u0643\\u0646 \\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0628\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0645\\u0646 \\u062d\\u064a\\u062b \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u063a\\u0631\\u0641.\",\"sort_order\":2,\"is_active\":true}]', 'Ready to Book with More Clarity?', 'جاهز للحجز بشكل أوضح؟', 'Travel Wave helps compare the right options and organize the next step more smoothly.', 'تساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.', 'Start Booking', 'ابدأ الحجز', 'WhatsApp', 'واتساب', 'https://wa.me/201000000000', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'Ask About Hurghada', 'استفسر عن الغردقة', 'Send your details and Travel Wave will help with a suitable package.', 'أرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.', 'Send Request', 'أرسل الطلب', '[\"email\",\"travel_date\",\"return_date\",\"travelers_count\",\"message\"]', '#destination-form', NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 3, '2026-03-25 23:07:02', '2026-03-28 00:35:32', '2026-03-28 00:35:32', 3);
INSERT INTO `destinations` (`id`, `title_en`, `title_ar`, `slug`, `destination_type`, `excerpt_en`, `excerpt_ar`, `subtitle_en`, `subtitle_ar`, `hero_badge_en`, `hero_badge_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_cta_text_en`, `hero_cta_text_ar`, `hero_cta_url`, `hero_secondary_cta_text_en`, `hero_secondary_cta_text_ar`, `hero_secondary_cta_url`, `hero_overlay_opacity`, `hero_image`, `featured_image`, `hero_mobile_image`, `flag_image`, `overview_en`, `overview_ar`, `quick_info_title_en`, `quick_info_title_ar`, `quick_summary_destination_label_en`, `quick_summary_destination_label_ar`, `quick_summary_destination_icon`, `quick_info_items`, `about_title_en`, `about_title_ar`, `about_description_en`, `about_description_ar`, `about_image`, `about_points`, `detailed_title_en`, `detailed_title_ar`, `detailed_description_en`, `detailed_description_ar`, `best_time_badge_en`, `best_time_badge_ar`, `best_time_title_en`, `best_time_title_ar`, `best_time_description_en`, `best_time_description_ar`, `highlights_section_label_en`, `highlights_section_label_ar`, `highlights_title_en`, `highlights_title_ar`, `highlight_items`, `services_title_en`, `services_title_ar`, `services_intro_en`, `services_intro_ar`, `service_items`, `documents_title_en`, `documents_title_ar`, `documents_subtitle_en`, `documents_subtitle_ar`, `document_items`, `steps_title_en`, `steps_title_ar`, `step_items`, `pricing_title_en`, `pricing_title_ar`, `pricing_notes_en`, `pricing_notes_ar`, `pricing_items`, `faq_title_en`, `faq_title_ar`, `highlights`, `packages`, `included_items`, `excluded_items`, `itinerary`, `gallery`, `faqs`, `cta_title_en`, `cta_title_ar`, `cta_text_en`, `cta_text_ar`, `cta_button_en`, `cta_button_ar`, `cta_secondary_button_en`, `cta_secondary_button_ar`, `cta_secondary_url`, `cta_background_image`, `form_title_en`, `form_title_ar`, `form_subtitle_en`, `form_subtitle_ar`, `form_submit_text_en`, `form_submit_text_ar`, `form_visible_fields`, `cta_url`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `show_hero`, `show_quick_info`, `show_about`, `show_detailed`, `show_best_time`, `show_highlights`, `show_services`, `show_documents`, `show_steps`, `show_pricing`, `show_faq`, `show_cta`, `show_form`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(3, 'Marsa Alam', 'مرسى علم', 'marsa-alam', 'domestic', 'Quiet Red Sea stays, diving experiences, and resort-focused domestic travel.', 'إقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.', 'Quiet Red Sea stays, diving experiences, and resort-focused domestic travel.', 'إقامات هادئة على البحر الأحمر وتجارب بحرية وبرامج داخلية بطابع استرخائي.', 'Domestic Tourism', 'السياحة الداخلية', 'Marsa Alam Trips with Travel Wave', 'رحلات مرسى علم مع Travel Wave', 'Travel Wave helps organize the hotel, timing, and trip details in a cleaner domestic travel experience.', 'تساعدك Travel Wave على تنظيم الفندق والتوقيت وتفاصيل الرحلة في تجربة سفر داخلية أوضح وأكثر راحة.', 'Book Now', 'احجز الآن', '#destination-form', 'Quick Summary', 'ملخص سريع', '#destination-summary', 0.45, 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/slide-3.svg', NULL, 'Travel Wave offers practical Marsa Alam packages for short breaks and seasonal travel.', 'تقدم Travel Wave باقات عملية إلى مرسى علم تناسب العطلات القصيرة والمواسم المختلفة.', 'Quick Summary', 'ملخص سريع', 'Destination', 'الوجهة', 'material-symbols:globe-location-pin-outline', '[{\"label_en\":\"Trip Type\",\"label_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c\",\"value_en\":\"Domestic tourism package\",\"value_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0633\\u064a\\u0627\\u062d\\u0629 \\u062f\\u0627\\u062e\\u0644\\u064a\\u0629\",\"icon\":\"material-symbols:travel\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Suggested Duration\",\"label_ar\":\"\\u0627\\u0644\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d\\u0629\",\"value_en\":\"3 to 5 nights\",\"value_ar\":\"3 \\u0625\\u0644\\u0649 5 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"icon\":\"mdi:clock-outline\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Starting Price\",\"label_ar\":\"\\u0627\\u0644\\u0633\\u0639\\u0631 \\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646\",\"value_en\":\"Ask for latest offer\",\"value_ar\":\"\\u0627\\u0637\\u0644\\u0628 \\u0623\\u062d\\u062f\\u062b \\u0639\\u0631\\u0636\",\"icon\":\"solar:tag-price-linear\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Best Time\",\"label_ar\":\"\\u0623\\u0641\\u0636\\u0644 \\u0648\\u0642\\u062a\",\"value_en\":\"Depends on season\",\"value_ar\":\"\\u0628\\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0648\\u0633\\u0645\",\"icon\":\"solar:calendar-linear\",\"sort_order\":4,\"is_active\":true}]', 'About the Destination', 'نبذة عن الوجهة', 'Marsa Alam remains one of the practical domestic options for travelers looking for a more organized holiday with hotels, transfers, and balanced activities.', 'مرسى علم من الوجهات المحلية المناسبة لمن يريد رحلة منظمة بشكل أفضل تشمل الفندق والتنقلات والأنشطة دون تعقيد في الحجز.', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', '[{\"text_en\":\"Suitable for couples, families, or short leisure breaks.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0623\\u0632\\u0648\\u0627\\u062c \\u0648\\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0648\\u0627\\u0644\\u0625\\u062c\\u0627\\u0632\\u0627\\u062a \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631\\u0629.\"},{\"text_en\":\"Flexible hotel levels and package structure.\",\"text_ar\":\"\\u0645\\u0631\\u0648\\u0646\\u0629 \\u0641\\u064a \\u0645\\u0633\\u062a\\u0648\\u064a\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0634\\u0643\\u0644 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c.\"},{\"text_en\":\"Travel Wave helps compare the options more clearly.\",\"text_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f Travel Wave \\u0639\\u0644\\u0649 \\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0627\\u0644\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0648\\u0636\\u062d.\"}]', 'Trip Details', 'تفاصيل الرحلة', 'Travel Wave organizes the destination around the right stay length, hotel category, and guest preferences.\n\nThis makes the trip easier to compare and confirm without getting lost in scattered options.', 'تنظم Travel Wave الوجهة حسب مدة الإقامة المناسبة وفئة الفندق واحتياج المسافرين.\n\nوهذا يجعل الرحلة أسهل في المقارنة والتأكيد دون تشتت بين الخيارات.', 'Best Time', 'أفضل وقت', 'Best Time to Visit', 'أفضل وقت للزيارة', 'Best travel timing depends on weather preference, budget level, and the type of activities planned.', 'الخريف والشتاء والربيع من الفترات المناسبة للزيارة، بينما يفضلها البعض صيفًا للإقامة داخل المنتجعات.', 'Helpful Guidance Points', 'أهم الإرشادات', 'Top Highlights', 'أبرز النقاط المهمة', '[{\"title_en\":\"\\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0628\\u062d\\u0631\\u064a\\u0629\",\"title_ar\":\"\\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0628\\u062d\\u0631\\u064a\\u0629\",\"description_en\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0633\\u0646\\u0648\\u0631\\u0643\\u0644\\u064a\\u0646\\u062c \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0627\\u062d\\u0644\\u064a\\u0629.\",\"description_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0633\\u0646\\u0648\\u0631\\u0643\\u0644\\u064a\\u0646\\u062c \\u0648\\u0627\\u0644\\u0623\\u0646\\u0634\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0627\\u062d\\u0644\\u064a\\u0629.\",\"image\":\"hero-slides\\/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png\",\"icon\":\"material-symbols:beach-access-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"\\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a \\u0627\\u0644\\u0647\\u0627\\u062f\\u0626\\u0629\",\"title_ar\":\"\\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a \\u0627\\u0644\\u0647\\u0627\\u062f\\u0626\\u0629\",\"description_en\":\"\\u062a\\u062c\\u0631\\u0628\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0645\\u062d\\u0628\\u064a \\u0627\\u0644\\u0631\\u0627\\u062d\\u0629 \\u0648\\u0627\\u0644\\u062e\\u0635\\u0648\\u0635\\u064a\\u0629.\",\"description_ar\":\"\\u062a\\u062c\\u0631\\u0628\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0645\\u062d\\u0628\\u064a \\u0627\\u0644\\u0631\\u0627\\u062d\\u0629 \\u0648\\u0627\\u0644\\u062e\\u0635\\u0648\\u0635\\u064a\\u0629.\",\"image\":\"hero-slides\\/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png\",\"icon\":\"material-symbols:scuba-diving\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"\\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u0627\\u0644\\u0637\\u0648\\u064a\\u0644\\u0629\",\"title_ar\":\"\\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u0627\\u0644\\u0637\\u0648\\u064a\\u0644\\u0629\",\"description_en\":\"\\u0627\\u0644\\u0648\\u062c\\u0647\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0639\\u062f\\u062f \\u0644\\u064a\\u0627\\u0644\\u064d \\u0623\\u0643\\u0628\\u0631 \\u0645\\u0646 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0633\\u0631\\u064a\\u0639\\u0629.\",\"description_ar\":\"\\u0627\\u0644\\u0648\\u062c\\u0647\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0639\\u062f\\u062f \\u0644\\u064a\\u0627\\u0644\\u064d \\u0623\\u0643\\u0628\\u0631 \\u0645\\u0646 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0633\\u0631\\u064a\\u0639\\u0629.\",\"image\":\"hero-slides\\/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg\",\"icon\":\"material-symbols:hotel-class-outline-rounded\",\"sort_order\":3,\"is_active\":true}]', 'Included Services', 'الخدمات المتضمنة', 'The trip can be built around your timing, comfort level, and package preference.', 'يمكن تصميم الرحلة حسب توقيتك ومستوى الراحة المناسب والبرنامج المطلوب.', '[{\"title_en\":\"\\u0627\\u062e\\u062a\\u064a\\u0627\\u0631 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642\",\"title_ar\":\"\\u0627\\u062e\\u062a\\u064a\\u0627\\u0631 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642\",\"description_en\":\"\\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0641\\u0626\\u0627\\u062a \\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0631\\u0627\\u062d\\u0629.\",\"description_ar\":\"\\u0645\\u0642\\u0627\\u0631\\u0646\\u0629 \\u0641\\u0626\\u0627\\u062a \\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639\\u0627\\u062a \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0631\\u0627\\u062d\\u0629.\",\"icon\":\"material-symbols:hotel-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0627\\u0644\\u062a\\u0646\\u0642\\u0644\\u0627\\u062a\",\"title_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0627\\u0644\\u062a\\u0646\\u0642\\u0644\\u0627\\u062a\",\"description_en\":\"\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0623\\u0641\\u0636\\u0644 \\u0644\\u062a\\u0648\\u0642\\u064a\\u062a\\u0627\\u062a \\u0627\\u0644\\u0648\\u0635\\u0648\\u0644 \\u0648\\u0627\\u0644\\u0645\\u063a\\u0627\\u062f\\u0631\\u0629.\",\"description_ar\":\"\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0623\\u0641\\u0636\\u0644 \\u0644\\u062a\\u0648\\u0642\\u064a\\u062a\\u0627\\u062a \\u0627\\u0644\\u0648\\u0635\\u0648\\u0644 \\u0648\\u0627\\u0644\\u0645\\u063a\\u0627\\u062f\\u0631\\u0629.\",\"icon\":\"material-symbols:directions-car-outline-rounded\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0645\\u0631\\u0646\",\"title_ar\":\"\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c \\u0645\\u0631\\u0646\",\"description_en\":\"\\u0627\\u0644\\u0645\\u0648\\u0627\\u0632\\u0646\\u0629 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0628\\u062d\\u0631\\u064a\\u0629 \\u0648\\u0627\\u0644\\u0648\\u0642\\u062a \\u0627\\u0644\\u062d\\u0631.\",\"description_ar\":\"\\u0627\\u0644\\u0645\\u0648\\u0627\\u0632\\u0646\\u0629 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0628\\u062d\\u0631\\u064a\\u0629 \\u0648\\u0627\\u0644\\u0648\\u0642\\u062a \\u0627\\u0644\\u062d\\u0631.\",\"icon\":\"material-symbols:concierge\",\"sort_order\":3,\"is_active\":true}]', 'Required Before Booking', 'ما يلزم قبل الحجز', 'Domestic travel does not need visa paperwork, but some basic details help confirm the booking faster.', 'السفر الداخلي لا يحتاج مستندات تأشيرة، لكن بعض البيانات الأساسية تساعد على تأكيد الحجز بسرعة.', '[{\"title_en\":\"Traveler Names\",\"title_ar\":\"\\u0623\\u0633\\u0645\\u0627\\u0621 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0641\\u0631\\u064a\\u0646\",\"description_en\":\"Correct names for reservation records.\",\"description_ar\":\"\\u0627\\u0644\\u0623\\u0633\\u0645\\u0627\\u0621 \\u0627\\u0644\\u0635\\u062d\\u064a\\u062d\\u0629 \\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u062d\\u062c\\u0632.\",\"icon\":\"material-symbols:badge-outline-rounded\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Travel Dates\",\"title_ar\":\"\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u0633\\u0641\\u0631\",\"description_en\":\"Preferred travel period and stay length.\",\"description_ar\":\"\\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0641\\u0636\\u0644\\u0629 \\u0648\\u0639\\u062f\\u062f \\u0627\\u0644\\u0644\\u064a\\u0627\\u0644\\u064a.\",\"icon\":\"solar:calendar-linear\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Guest Count\",\"title_ar\":\"\\u0639\\u062f\\u062f \\u0627\\u0644\\u0623\\u0641\\u0631\\u0627\\u062f\",\"description_en\":\"Used to match room type and offer level.\",\"description_ar\":\"\\u0644\\u062a\\u062d\\u062f\\u064a\\u062f \\u0646\\u0648\\u0639 \\u0627\\u0644\\u063a\\u0631\\u0641\\u0629 \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0639\\u0631\\u0636.\",\"icon\":\"material-symbols:group-outline-rounded\",\"sort_order\":3,\"is_active\":true}]', 'Booking Steps', 'خطوات الحجز', '[{\"title_en\":\"Choose the destination\",\"title_ar\":\"\\u0627\\u062e\\u062a\\u0631 \\u0627\\u0644\\u0648\\u062c\\u0647\\u0629\",\"description_en\":\"Share the trip type and preferred dates.\",\"description_ar\":\"\\u062d\\u062f\\u062f \\u0637\\u0628\\u064a\\u0639\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0648\\u0627\\u0644\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629.\",\"step_number\":1,\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Review the options\",\"title_ar\":\"\\u0631\\u0627\\u062c\\u0639 \\u0627\\u0644\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a\",\"description_en\":\"Compare the suggested hotels and packages.\",\"description_ar\":\"\\u0642\\u0627\\u0631\\u0646 \\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0628\\u0627\\u0642\\u0627\\u062a \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d\\u0629.\",\"step_number\":2,\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Confirm the booking\",\"title_ar\":\"\\u0623\\u0643\\u062f \\u0627\\u0644\\u062d\\u062c\\u0632\",\"description_en\":\"Choose the final option and complete the details.\",\"description_ar\":\"\\u0627\\u062e\\u062a\\u0631 \\u0627\\u0644\\u0639\\u0631\\u0636 \\u0627\\u0644\\u0646\\u0647\\u0627\\u0626\\u064a \\u0648\\u0627\\u0633\\u062a\\u0643\\u0645\\u0644 \\u0627\\u0644\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644.\",\"step_number\":3,\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Receive confirmation\",\"title_ar\":\"\\u0627\\u0633\\u062a\\u0644\\u0645 \\u0627\\u0644\\u062a\\u0623\\u0643\\u064a\\u062f\",\"description_en\":\"Travel Wave confirms the reservation and next steps.\",\"description_ar\":\"\\u062a\\u0624\\u0643\\u062f Travel Wave \\u0627\\u0644\\u062d\\u062c\\u0632 \\u0648\\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629.\",\"step_number\":4,\"sort_order\":4,\"is_active\":true}]', 'Pricing Overview', 'نظرة على الأسعار', 'Final pricing changes by hotel level, trip duration, and season.', 'تتغير الأسعار النهائية حسب فئة الفندق ومدة الرحلة والموسم.', '[{\"label_en\":\"\\u0625\\u0642\\u0627\\u0645\\u0629 4 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"label_ar\":\"\\u0625\\u0642\\u0627\\u0645\\u0629 4 \\u0644\\u064a\\u0627\\u0644\\u064d\",\"value_en\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 8,400 \\u062c\\u0646\\u064a\\u0647\",\"value_ar\":\"\\u064a\\u0628\\u062f\\u0623 \\u0645\\u0646 8,400 \\u062c\\u0646\\u064a\\u0647\",\"note_en\":\"\\u0628\\u062d\\u0633\\u0628 \\u0641\\u0626\\u0629 \\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639 \\u0648\\u0627\\u0644\\u0645\\u0648\\u0633\\u0645.\",\"note_ar\":\"\\u0628\\u062d\\u0633\\u0628 \\u0641\\u0626\\u0629 \\u0627\\u0644\\u0645\\u0646\\u062a\\u062c\\u0639 \\u0648\\u0627\\u0644\\u0645\\u0648\\u0633\\u0645.\",\"sort_order\":1,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', NULL, NULL, NULL, NULL, NULL, NULL, '[{\"question_en\":\"Can the package be adjusted?\",\"question_ar\":\"\\u0647\\u0644 \\u064a\\u0645\\u0643\\u0646 \\u062a\\u0639\\u062f\\u064a\\u0644 \\u0627\\u0644\\u0628\\u0631\\u0646\\u0627\\u0645\\u062c\\u061f\",\"answer_en\":\"Yes, package level and stay details can often be tailored around your needs.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u064a\\u0645\\u0643\\u0646 \\u063a\\u0627\\u0644\\u0628\\u064b\\u0627 \\u062a\\u0639\\u062f\\u064a\\u0644 \\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u0628\\u0627\\u0642\\u0629 \\u0648\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u062d\\u0633\\u0628 \\u0627\\u062d\\u062a\\u064a\\u0627\\u062c\\u0643.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"Are family options available?\",\"question_ar\":\"\\u0647\\u0644 \\u062a\\u0648\\u062c\\u062f \\u0628\\u0631\\u0627\\u0645\\u062c \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a\\u061f\",\"answer_en\":\"Yes, the destination can be prepared with family-friendly hotel and room options.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u064a\\u0645\\u0643\\u0646 \\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0628\\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0639\\u0627\\u0626\\u0644\\u0627\\u062a \\u0645\\u0646 \\u062d\\u064a\\u062b \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u063a\\u0631\\u0641.\",\"sort_order\":2,\"is_active\":true}]', 'Ready to Book with More Clarity?', 'جاهز للحجز بشكل أوضح؟', 'Travel Wave helps compare the right options and organize the next step more smoothly.', 'تساعدك Travel Wave على مقارنة الخيارات المناسبة وتنظيم الخطوة التالية بسهولة أكبر.', 'Start Booking', 'ابدأ الحجز', 'WhatsApp', 'واتساب', 'https://wa.me/201000000000', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'Ask About Marsa Alam', 'استفسر عن مرسى علم', 'Send your details and Travel Wave will help with a suitable package.', 'أرسل بياناتك وستساعدك Travel Wave في الوصول إلى البرنامج الأنسب.', 'Send Request', 'أرسل الطلب', '[\"email\",\"travel_date\",\"return_date\",\"travelers_count\",\"message\"]', '#destination-form', NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, '2026-03-25 23:07:02', '2026-03-28 00:35:30', '2026-03-28 00:35:30', 3),
(4, 'Dahab', 'دهب', 'dahab', 'domestic', 'Dahab travel programs with flexible accommodation and support.', 'برامج دهب مع إقامة مرنة ودعم قبل الحجز.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, 'Travel Wave offers practical Dahab packages for short breaks and seasonal travel.', 'تقدم Travel Wave باقات عملية إلى دهب تناسب العطلات القصيرة والمواسم المختلفة.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 10, '2026-03-25 23:07:02', '2026-03-28 00:35:34', '2026-03-28 00:35:34', 3),
(5, 'North Coast', 'الساحل الشمالي', 'north-coast', 'domestic', 'North Coast travel programs with flexible accommodation and support.', 'برامج الساحل الشمالي مع إقامة مرنة ودعم قبل الحجز.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, 'Travel Wave offers practical North Coast packages for short breaks and seasonal travel.', 'تقدم Travel Wave باقات عملية إلى الساحل الشمالي تناسب العطلات القصيرة والمواسم المختلفة.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 10, '2026-03-25 23:07:02', '2026-03-28 00:35:36', '2026-03-28 00:35:36', 3),
(6, 'Luxor & Aswan', 'الأقصر وأسوان', 'luxor-aswan', 'domestic', 'Luxor & Aswan travel programs with flexible accommodation and support.', 'برامج الأقصر وأسوان مع إقامة مرنة ودعم قبل الحجز.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, 'Travel Wave offers practical Luxor & Aswan packages for short breaks and seasonal travel.', 'تقدم Travel Wave باقات عملية إلى الأقصر وأسوان تناسب العطلات القصيرة والمواسم المختلفة.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 10, '2026-03-25 23:07:02', '2026-03-28 00:35:39', '2026-03-28 00:35:39', 3);

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
-- Table structure for table `goal_targets`
--

CREATE TABLE `goal_targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `target_type` varchar(100) NOT NULL,
  `target_value` decimal(14,2) NOT NULL,
  `period_type` varchar(30) NOT NULL DEFAULT 'monthly',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `note` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `mobile_image_path` varchar(255) DEFAULT NULL,
  `image_framing` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`image_framing`)),
  `headline_en` varchar(255) DEFAULT NULL,
  `headline_ar` varchar(255) DEFAULT NULL,
  `subtitle_en` text DEFAULT NULL,
  `subtitle_ar` text DEFAULT NULL,
  `cta_text_en` varchar(255) DEFAULT NULL,
  `cta_text_ar` varchar(255) DEFAULT NULL,
  `cta_link` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `image_path`, `mobile_image_path`, `image_framing`, `headline_en`, `headline_ar`, `subtitle_en`, `subtitle_ar`, `cta_text_en`, `cta_text_ar`, `cta_link`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'hero-slides/mmjsGhPqpbdxZL1b932XHONE5GHYvBHSg6Ef4WEq.png', 'hero-slides/mmjsGhPqpbdxZL1b932XHONE5GHYvBHSg6Ef4WEq.png', '{\"desktop_banner\":{\"x\":74.81,\"y\":5.15},\"mobile_banner\":{\"x\":55.43,\"y\":55.68}}', 'Luxury journeys shaped around your next visa, flight, and stay', 'رحلات راقية نصممها حول تأشيرتك ورحلتك الجوية وإقامتك', 'Travel Wave combines visa support, premium bookings, and responsive trip planning in one smooth customer journey.', 'تجمع Travel Wave بين دعم التأشيرات والحجوزات الراقية وتخطيط الرحلات السريع ضمن تجربة واحدة متكاملة.', 'Start Planning', 'ابدأ التخطيط', '/contact', 1, 1, '2026-03-25 23:07:02', '2026-03-26 01:52:48'),
(2, 'media-library/DExTaYIB43hfIRCdpPiFH37AisJoMIUpfxuHYm3I.png', 'media-library/DExTaYIB43hfIRCdpPiFH37AisJoMIUpfxuHYm3I.png', '{\"desktop_banner\":{\"x\":69.27,\"y\":0},\"mobile_banner\":{\"x\":66.2,\"y\":67.32}}', 'Europe, Gulf, and Asia visa services with a clearer path', 'خدمات تأشيرات أوروبا والخليج وآسيا بمسار أوضح', 'From file preparation to booking coordination, we help you move with confidence and less last-minute pressure.', 'من تجهيز الملف إلى تنسيق الحجوزات نساعدك على التحرك بثقة وبضغط أقل في اللحظات الأخيرة.', 'Explore Visa Services', 'استكشف التأشيرات', '/visas', 2, 1, '2026-03-25 23:07:02', '2026-03-26 02:27:49'),
(3, 'media-library/N6MHrb8TDobuCmToWomGNkjxF7vGwO8boqEV8QUc.png', 'media-library/N6MHrb8TDobuCmToWomGNkjxF7vGwO8boqEV8QUc.png', '{\"desktop_banner\":{\"x\":33.41,\"y\":100},\"mobile_banner\":{\"x\":77.84,\"y\":76.69}}', NULL, NULL, NULL, NULL, NULL, NULL, '/domestic-tourism', 3, 1, '2026-03-25 23:07:02', '2026-03-26 02:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `home_country_strip_items`
--

CREATE TABLE `home_country_strip_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visa_country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `subtitle_en` varchar(255) DEFAULT NULL,
  `subtitle_ar` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `flag_image_path` varchar(255) DEFAULT NULL,
  `custom_url` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_on_homepage` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_country_strip_items`
--

INSERT INTO `home_country_strip_items` (`id`, `visa_country_id`, `name_en`, `name_ar`, `subtitle_en`, `subtitle_ar`, `image_path`, `flag_image_path`, `custom_url`, `sort_order`, `is_active`, `show_on_homepage`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 1, 'France', 'فرنسا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 1, 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 2, 'Germany', 'ألمانيا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 2, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:41', '2026-03-27 23:58:41', 3),
(3, 3, 'Italy', 'إيطاليا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 3, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:44', '2026-03-27 23:58:44', 3),
(4, 4, 'Spain', 'إسبانيا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 4, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:47', '2026-03-27 23:58:47', 3),
(5, 5, 'Netherlands', 'هولندا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 5, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:49', '2026-03-27 23:58:49', 3),
(6, 6, 'Greece', 'اليونان', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 6, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:51', '2026-03-27 23:58:51', 3),
(7, 7, 'UAE', 'الإمارات', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 7, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:53', '2026-03-27 23:58:53', 3),
(8, 8, 'USA', 'أمريكا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 8, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:55', '2026-03-27 23:58:55', 3),
(9, 9, 'Canada', 'كندا', NULL, NULL, 'visa-countries/france-flag.svg', NULL, NULL, 9, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:58:57', '2026-03-27 23:58:57', 3);

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_form_assignment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `marketing_landing_page_id` bigint(20) UNSIGNED DEFAULT NULL,
  `utm_campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `form_name` varchar(255) DEFAULT NULL,
  `form_category` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `whatsapp_number` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `service_type` varchar(255) DEFAULT NULL,
  `service_country_name` varchar(255) DEFAULT NULL,
  `tourism_destination` varchar(255) DEFAULT NULL,
  `travel_destination` varchar(255) DEFAULT NULL,
  `hotel_destination` varchar(255) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `travelers_count` int(10) UNSIGNED DEFAULT NULL,
  `nights_count` int(10) UNSIGNED DEFAULT NULL,
  `accommodation_type` varchar(255) DEFAULT NULL,
  `estimated_budget` varchar(255) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `expenses` decimal(12,2) DEFAULT NULL,
  `net_price` decimal(12,2) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `paid_amount` decimal(12,2) DEFAULT NULL,
  `remaining_amount` decimal(12,2) DEFAULT NULL,
  `payment_status` varchar(30) DEFAULT NULL,
  `preferred_language` varchar(255) NOT NULL DEFAULT 'en',
  `source_page` varchar(255) DEFAULT NULL,
  `display_position` varchar(255) DEFAULT NULL,
  `message` longtext DEFAULT NULL,
  `submitted_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`submitted_data`)),
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `crm_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crm_status_updated_at` timestamp NULL DEFAULT NULL,
  `crm_status_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status_1_updated_at` timestamp NULL DEFAULT NULL,
  `status_1_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `crm_status2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status_2_updated_at` timestamp NULL DEFAULT NULL,
  `status_2_updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lead_source` text DEFAULT NULL,
  `crm_source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crm_service_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `crm_service_subtype_id` bigint(20) UNSIGNED DEFAULT NULL,
  `campaign_name` text DEFAULT NULL,
  `utm_source` text DEFAULT NULL,
  `utm_medium` text DEFAULT NULL,
  `utm_campaign` text DEFAULT NULL,
  `utm_id` text DEFAULT NULL,
  `utm_term` text DEFAULT NULL,
  `utm_content` text DEFAULT NULL,
  `landing_page` text DEFAULT NULL,
  `referrer` text DEFAULT NULL,
  `first_touch_at` timestamp NULL DEFAULT NULL,
  `last_touch_at` timestamp NULL DEFAULT NULL,
  `first_utm_source` text DEFAULT NULL,
  `first_utm_medium` text DEFAULT NULL,
  `first_utm_campaign` text DEFAULT NULL,
  `first_utm_id` text DEFAULT NULL,
  `first_utm_term` text DEFAULT NULL,
  `first_utm_content` text DEFAULT NULL,
  `first_landing_page` text DEFAULT NULL,
  `first_referrer` text DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `last_follow_up_at` timestamp NULL DEFAULT NULL,
  `next_follow_up_at` timestamp NULL DEFAULT NULL,
  `follow_up_result` text DEFAULT NULL,
  `admin_notes` longtext DEFAULT NULL,
  `additional_notes` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `lead_form_id`, `lead_form_assignment_id`, `marketing_landing_page_id`, `utm_campaign_id`, `type`, `form_name`, `form_category`, `full_name`, `phone`, `whatsapp_number`, `country`, `email`, `nationality`, `destination`, `service_type`, `service_country_name`, `tourism_destination`, `travel_destination`, `hotel_destination`, `travel_date`, `return_date`, `travelers_count`, `nights_count`, `accommodation_type`, `estimated_budget`, `total_price`, `expenses`, `net_price`, `total_amount`, `paid_amount`, `remaining_amount`, `payment_status`, `preferred_language`, `source_page`, `display_position`, `message`, `submitted_data`, `status`, `crm_status_id`, `crm_status_updated_at`, `crm_status_updated_by`, `status_1_updated_at`, `status_1_updated_by`, `crm_status2_id`, `status_2_updated_at`, `status_2_updated_by`, `assigned_user_id`, `lead_source`, `crm_source_id`, `crm_service_type_id`, `crm_service_subtype_id`, `campaign_name`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_id`, `utm_term`, `utm_content`, `landing_page`, `referrer`, `first_touch_at`, `last_touch_at`, `first_utm_source`, `first_utm_medium`, `first_utm_campaign`, `first_utm_id`, `first_utm_term`, `first_utm_content`, `first_landing_page`, `first_referrer`, `priority`, `last_follow_up_at`, `next_follow_up_at`, `follow_up_result`, `admin_notes`, `additional_notes`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, NULL, NULL, NULL, NULL, 'general', NULL, NULL, 'Sample Travel Lead', '+20 111 222 3333', NULL, NULL, 'lead@example.com', NULL, 'France', 'Visa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'en', NULL, NULL, 'I need support preparing my file for a Europe visa this summer.', NULL, 'new', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 3, 17, NULL, NULL, 'visa', 'Marketing France Visa Demo Form Copy', 'visa', 'Mohamed Bassiouny', '01147099377', '+20 100 123 4567', NULL, 'sh7n2014@gmail.com', NULL, 'سلوفكيا', 'شنغن قصيرة الإقامة', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ar', 'سلوفاكيا', 'bottom', NULL, '{\"full_name\":\"Mohamed Bassiouny\",\"phone\":\"01147099377\",\"whatsapp_number\":\"+20 100 123 4567\",\"email\":\"sh7n2014@gmail.com\",\"service_type\":\"\\u0634\\u0646\\u063a\\u0646 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"destination\":\"\\u0633\\u0644\\u0648\\u0641\\u0643\\u064a\\u0627\",\"travel_date\":null,\"message\":null}', 'new', 1, '2026-03-27 23:57:07', NULL, '2026-03-27 23:57:07', NULL, NULL, NULL, NULL, NULL, 'visa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'normal', NULL, NULL, NULL, NULL, NULL, '2026-03-27 23:57:07', '2026-03-27 23:57:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_articles`
--

CREATE TABLE `knowledge_base_articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `knowledge_base_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `visibility_scope` varchar(30) NOT NULL DEFAULT 'all_staff',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_base_categories`
--

CREATE TABLE `knowledge_base_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_base_categories`
--

INSERT INTO `knowledge_base_categories` (`id`, `slug`, `name_ar`, `name_en`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'embassy-rules', 'السفارات', 'Embassy Rules', 'Embassy rules and country-specific instructions.', 1, 10, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(2, 'visa-procedures', 'التأشيرات', 'Visa Procedures', 'Visa flows, process notes, and requirements.', 1, 20, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(3, 'required-documents', 'الأوراق المطلوبة', 'Required Documents', 'Required document checklists for services and countries.', 1, 30, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(4, 'pricing-guidance', 'الأسعار', 'Pricing', 'Pricing references and internal guidance.', 1, 40, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(5, 'internal-procedures', 'الإجراءات الداخلية', 'Internal Procedures', 'SOPs and internal operational workflows.', 1, 50, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(6, 'faq', 'الأسئلة الشائعة', 'FAQ', 'Frequently asked internal questions and answers.', 1, 60, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(7, 'sales-scripts', 'سكربتات البيع', 'Sales Scripts', 'Seller scripts and response templates.', 1, 70, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(8, 'accounting-notes', 'المحاسبة', 'Accounting Notes', 'Accounting and collections reference notes.', 1, 80, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(9, 'operations', 'التشغيل', 'Operations', 'Operations handling notes and best practices.', 1, 90, '2026-03-25 20:41:09', '2026-03-25 20:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `lead_forms`
--

CREATE TABLE `lead_forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `form_category` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `subtitle_en` text DEFAULT NULL,
  `subtitle_ar` text DEFAULT NULL,
  `submit_text_en` varchar(255) DEFAULT NULL,
  `submit_text_ar` varchar(255) DEFAULT NULL,
  `success_message_en` text DEFAULT NULL,
  `success_message_ar` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_forms`
--

INSERT INTO `lead_forms` (`id`, `name`, `slug`, `form_category`, `title_en`, `title_ar`, `subtitle_en`, `subtitle_ar`, `submit_text_en`, `submit_text_ar`, `success_message_en`, `success_message_ar`, `is_active`, `settings`, `created_at`, `updated_at`) VALUES
(1, 'Default External Visa Form', 'default-external-visa-form', 'visa', 'Talk to Travel Wave About Your Visa', 'تواصل مع Travel Wave بخصوص التأشيرة', 'Send your details and our team will guide you on eligibility, documents, and the next practical step.', 'أرسل بياناتك وسيرشدك فريقنا بخصوص الأهلية والمستندات والخطوة العملية التالية.', 'Send Visa Inquiry', 'أرسل استفسار التأشيرة', 'Your visa inquiry has been received. A Travel Wave advisor will contact you shortly.', 'تم استلام استفسارك الخاص بالتأشيرة وسيتواصل معك أحد مستشاري Travel Wave قريبًا.', 0, '{\"layout_variant\":\"visa_split\",\"info_label_en\":null,\"info_label_ar\":null,\"info_heading_en\":null,\"info_heading_ar\":null,\"info_description_en\":null,\"info_description_ar\":null,\"info_items\":[]}', '2026-03-25 23:07:02', '2026-03-27 16:21:37'),
(2, 'Marketing France Visa Demo Form', 'marketing-france-visa-demo-form', 'visa', 'Talk to Travel Wave About Your France Visa', 'تواصل مع Travel Wave بخصوص تأشيرة فرنسا', 'Send your details and our team will help with the next visa step.', 'أرسل بياناتك وسيقوم فريقنا بمساعدتك في الخطوة التالية للتأشيرة.', 'Send Request', 'أرسل الطلب', 'Your request has been received successfully.', 'تم استلام طلبك بنجاح.', 1, '{\"layout_type\":\"split_details\",\"layout_variant\":\"split_details\",\"info_label_en\":null,\"info_label_ar\":null,\"info_heading_en\":null,\"info_heading_ar\":null,\"info_description_en\":null,\"info_description_ar\":null,\"info_items\":[]}', '2026-03-25 23:07:02', '2026-03-27 17:18:47'),
(3, 'Marketing France Visa Demo Form Copy', 'marketing-france-visa-demo-form-copy', 'visa', 'Talk to Travel Wave About Your France Visa', 'تواصل مع Travel Wave بخصوص تأشيرة سلوفكيا', 'Send your details and our team will help with the next visa step.', 'أرسل بياناتك وسيقوم فريقنا بمساعدتك في الخطوة التالية للتأشيرة.', 'Send Request', 'أرسل الطلب', 'Your request has been received successfully.', 'تم استلام طلبك بنجاح.', 1, '{\"layout_type\":\"split_details\",\"layout_variant\":\"split_details\",\"info_label_en\":null,\"info_label_ar\":null,\"info_heading_en\":null,\"info_heading_ar\":null,\"info_description_en\":null,\"info_description_ar\":null,\"info_items\":[]}', '2026-03-27 23:54:42', '2026-03-27 23:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `lead_form_assignments`
--

CREATE TABLE `lead_form_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_form_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_type` varchar(255) NOT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_key` varchar(255) DEFAULT NULL,
  `display_position` varchar(255) NOT NULL DEFAULT 'bottom',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_form_assignments`
--

INSERT INTO `lead_form_assignments` (`id`, `lead_form_id`, `assignment_type`, `target_id`, `target_key`, `display_position`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 1, 'page_group', NULL, 'visa-destinations', 'bottom', 1, 1, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(12, 2, 'visa_country', 1, NULL, 'bottom', 1, 1, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(17, 3, 'visa_country', 11, NULL, 'bottom', 1, 1, '2026-03-27 23:56:32', '2026-03-27 23:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `lead_form_fields`
--

CREATE TABLE `lead_form_fields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_form_id` bigint(20) UNSIGNED NOT NULL,
  `field_key` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `label_en` varchar(255) DEFAULT NULL,
  `label_ar` varchar(255) DEFAULT NULL,
  `placeholder_en` varchar(255) DEFAULT NULL,
  `placeholder_ar` varchar(255) DEFAULT NULL,
  `help_text_en` text DEFAULT NULL,
  `help_text_ar` text DEFAULT NULL,
  `validation_rule` text DEFAULT NULL,
  `default_value` text DEFAULT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_form_fields`
--

INSERT INTO `lead_form_fields` (`id`, `lead_form_id`, `field_key`, `type`, `label_en`, `label_ar`, `placeholder_en`, `placeholder_ar`, `help_text_en`, `help_text_ar`, `validation_rule`, `default_value`, `options`, `is_required`, `is_enabled`, `sort_order`, `created_at`, `updated_at`) VALUES
(25, 1, 'full_name', 'text', 'Full Name', 'الاسم الكامل', '', '', '', '', '', '', '[]', 1, 1, 1, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(26, 1, 'phone', 'phone', 'Phone Number', 'رقم الهاتف', '', '', '', '', '', '', '[]', 1, 1, 2, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(27, 1, 'whatsapp_number', 'text', 'WhatsApp Number', 'رقم واتساب', '', '', '', '', '', '', '[]', 0, 1, 3, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(28, 1, 'email', 'email', 'Email Address', 'البريد الإلكتروني', '', '', '', '', '', '', '[]', 0, 1, 4, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(29, 1, 'service_type', 'text', 'Visa Type', 'نوع التأشيرة', '', '', '', '', '', '', '[]', 0, 1, 5, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(30, 1, 'destination', 'text', 'Country', 'الدولة', '', '', '', '', '', '', '[]', 0, 1, 6, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(31, 1, 'travel_date', 'date', 'Travel Date', 'تاريخ السفر', '', '', '', '', '', '', '[]', 0, 1, 7, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(32, 1, 'message', 'textarea', 'Your Message', 'رسالتك', '', '', '', '', '', '', '[]', 0, 1, 8, '2026-03-27 16:21:37', '2026-03-27 16:21:37'),
(97, 2, 'full_name', 'text', 'Full Name', 'الاسم الكامل', '', '', '', '', '', '', '[]', 1, 1, 1, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(98, 2, 'phone', 'phone', 'Phone Number', 'رقم الهاتف', '', '', '', '', '', '', '[]', 1, 1, 2, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(99, 2, 'whatsapp_number', 'text', 'WhatsApp Number', 'رقم واتساب', '', '', '', '', '', '', '[]', 0, 1, 3, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(100, 2, 'email', 'email', 'Email Address', 'البريد الإلكتروني', '', '', '', '', '', '', '[]', 0, 1, 4, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(101, 2, 'service_type', 'text', 'Visa Type', 'نوع التأشيرة', '', '', '', '', '', 'شنغن قصيرة الإقامة', '[]', 0, 1, 5, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(102, 2, 'destination', 'text', 'Country', 'الدولة', '', '', '', '', '', 'فرنسا', '[]', 0, 1, 6, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(103, 2, 'travel_date', 'date', 'Travel Date', 'تاريخ السفر', '', '', '', '', '', '', '[]', 0, 1, 7, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(104, 2, 'message', 'textarea', 'Your Message', 'رسالتك', '', '', '', '', '', '', '[]', 0, 1, 8, '2026-03-27 23:54:39', '2026-03-27 23:54:39'),
(137, 3, 'full_name', 'text', 'Full Name', 'الاسم الكامل', '', '', '', '', '', '', '[]', 1, 1, 1, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(138, 3, 'phone', 'phone', 'Phone Number', 'رقم الهاتف', '', '', '', '', '', '', '[]', 1, 1, 2, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(139, 3, 'whatsapp_number', 'text', 'WhatsApp Number', 'رقم واتساب', '', '', '', '', '', '', '[]', 0, 1, 3, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(140, 3, 'email', 'email', 'Email Address', 'البريد الإلكتروني', '', '', '', '', '', '', '[]', 0, 1, 4, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(141, 3, 'service_type', 'text', 'Visa Type', 'نوع التأشيرة', '', '', '', '', '', 'شنغن قصيرة الإقامة', '[]', 0, 1, 5, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(142, 3, 'destination', 'text', 'Country', 'الدولة', '', '', '', '', '', 'سلوفكيا', '[]', 0, 1, 6, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(143, 3, 'travel_date', 'date', 'Travel Date', 'تاريخ السفر', '', '', '', '', '', '', '[]', 0, 1, 7, '2026-03-27 23:56:32', '2026-03-27 23:56:32'),
(144, 3, 'message', 'textarea', 'Your Message', 'رسالتك', '', '', '', '', '', '', '[]', 0, 1, 8, '2026-03-27 23:56:32', '2026-03-27 23:56:32');

-- --------------------------------------------------------

--
-- Table structure for table `map_sections`
--

CREATE TABLE `map_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `subtitle_en` text DEFAULT NULL,
  `subtitle_ar` text DEFAULT NULL,
  `address_en` text DEFAULT NULL,
  `address_ar` text DEFAULT NULL,
  `button_text_en` varchar(255) DEFAULT NULL,
  `button_text_ar` varchar(255) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `embed_code` longtext DEFAULT NULL,
  `map_url` text DEFAULT NULL,
  `layout_type` varchar(255) NOT NULL DEFAULT 'split',
  `height` int(10) UNSIGNED NOT NULL DEFAULT 380,
  `background_style` varchar(255) NOT NULL DEFAULT 'default',
  `spacing_preset` varchar(255) NOT NULL DEFAULT 'normal',
  `rounded_corners` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `map_sections`
--

INSERT INTO `map_sections` (`id`, `name`, `slug`, `title_en`, `title_ar`, `subtitle_en`, `subtitle_ar`, `address_en`, `address_ar`, `button_text_en`, `button_text_ar`, `button_link`, `embed_code`, `map_url`, `layout_type`, `height`, `background_style`, `spacing_preset`, `rounded_corners`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Default Contact Map', 'default-contact-map', 'Visit Travel Wave or Use the Map as a Quick Reference', 'زورنا أو استخدم الخريطة كمرجع سريع', 'Use the location details for office visits, faster directions, or contact support with more clarity.', 'استخدم تفاصيل الموقع للوصول إلى المكتب بسهولة أو كمرجع سريع عند التواصل مع فريق Travel Wave.', 'Nasr City, Cairo, Egypt', 'مدينة نصر، القاهرة، مصر', 'Open in Maps', 'افتح في الخرائط', 'https://www.google.com/maps?q=Cairo%20Egypt', '<iframe src=\"https://www.google.com/maps?q=Cairo%20Egypt&output=embed\" width=\"100%\" height=\"320\" style=\"border:0;\" loading=\"lazy\"></iframe>', NULL, 'split', 380, 'soft', 'normal', 1, 0, '2026-03-25 23:07:02', '2026-03-27 16:13:15'),
(2, 'France', 'default-contact-map-copy', 'Visit Travel Wave or Use the Map as a Quick Reference', 'زورنا أو استخدم الخريطة كمرجع سريع', 'Use the location details for office visits, faster directions, or contact support with more clarity.', 'استخدم تفاصيل الموقع للوصول إلى المكتب بسهولة أو كمرجع سريع عند التواصل مع فريق Travel Wave.', 'Nasr City, Cairo, Egypt', 'مدينة نصر، القاهرة، مصر', 'Open in Maps', 'افتح في الخرائط', 'https://www.google.com/maps/place/Arab+African+Tours/@30.0645816,31.201088,21z/data=!4m6!3m5!1s0x145841166ada8bb5:0x7cbb5073e5fc766f!8m2!3d30.0647334!4d31.2008714!16s%2Fg%2F1tfysjdn?entry=ttu&g_ep=EgoyMDI2MDMyNC4wIKXMDSoASAFQAw%3D%3D', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d215.8145777130353!2d31.20108802358605!3d30.06458157416359!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145841166ada8bb5%3A0x7cbb5073e5fc766f!2sArab%20African%20Tours!5e0!3m2!1sen!2seg!4v1774658627598!5m2!1sen!2seg\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', NULL, 'split', 380, 'soft', 'normal', 1, 1, '2026-03-27 16:09:48', '2026-03-28 00:45:05');

-- --------------------------------------------------------

--
-- Table structure for table `map_section_assignments`
--

CREATE TABLE `map_section_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `map_section_id` bigint(20) UNSIGNED NOT NULL,
  `assignment_type` varchar(255) NOT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_key` varchar(255) DEFAULT NULL,
  `display_position` varchar(255) NOT NULL DEFAULT 'bottom',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `map_section_assignments`
--

INSERT INTO `map_section_assignments` (`id`, `map_section_id`, `assignment_type`, `target_id`, `target_key`, `display_position`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(9, 1, 'page_key', NULL, 'contact', 'before_faq', 1, 1, '2026-03-27 16:13:15', '2026-03-27 16:13:15'),
(19, 2, 'visa_country', 1, NULL, 'bottom', 1, 1, '2026-03-28 00:45:05', '2026-03-28 00:45:05');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_landing_pages`
--

CREATE TABLE `marketing_landing_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `internal_name` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `campaign_name` varchar(255) DEFAULT NULL,
  `ad_platform` varchar(255) DEFAULT NULL,
  `campaign_type` varchar(255) DEFAULT NULL,
  `traffic_source` varchar(255) DEFAULT NULL,
  `target_audience_note` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `assigned_lead_form_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tracking_integration_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tracking_integration_ids`)),
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`)),
  `seo_title_en` varchar(255) DEFAULT NULL,
  `seo_title_ar` varchar(255) DEFAULT NULL,
  `seo_description_en` text DEFAULT NULL,
  `seo_description_ar` text DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_medium` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `final_url` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marketing_landing_pages`
--

INSERT INTO `marketing_landing_pages` (`id`, `internal_name`, `title_en`, `title_ar`, `slug`, `campaign_name`, `ad_platform`, `campaign_type`, `traffic_source`, `target_audience_note`, `status`, `assigned_lead_form_id`, `tracking_integration_ids`, `sections`, `seo_title_en`, `seo_title_ar`, `seo_description_en`, `seo_description_ar`, `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`, `final_url`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'France Visa Campaign Demo', 'France Visa 2026', 'تأشيرة فرنسا 2026', 'france-visa-campaign-demo', 'France Visa Lead Campaign', 'Meta Ads', 'Lead Generation', 'meta', 'Arabic-speaking travelers interested in short-stay France visa support.', 'published', 2, '[1]', '{\"hero\":{\"enabled\":true,\"eyebrow_en\":\"Travel Wave Campaign\",\"eyebrow_ar\":\"\\u062d\\u0645\\u0644\\u0629 Travel Wave\",\"title_en\":\"France Visa 2026\",\"title_ar\":\"\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627 2026\",\"subtitle_en\":\"Get your France visa with a clearer process and practical support from Travel Wave.\",\"subtitle_ar\":\"\\u0627\\u0633\\u062a\\u062e\\u0631\\u062c \\u062a\\u0623\\u0634\\u064a\\u0631\\u062a\\u0643 \\u0628\\u0633\\u0647\\u0648\\u0644\\u0629 \\u0645\\u0639 \\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0627\\u062d\\u062a\\u0631\\u0627\\u0641\\u064a\\u0629 \\u0645\\u0646 Travel Wave\",\"primary_button_text_en\":\"Start Now\",\"primary_button_text_ar\":\"\\u0627\\u0628\\u062f\\u0623 \\u0627\\u0644\\u0622\\u0646\",\"primary_button_url\":\"#marketing-form\",\"secondary_button_text_en\":\"View Details\",\"secondary_button_text_ar\":\"\\u0627\\u0639\\u0631\\u0636 \\u0627\\u0644\\u062a\\u0641\\u0627\\u0635\\u064a\\u0644\",\"secondary_button_url\":\"#marketing-benefits\"},\"benefits\":{\"enabled\":true,\"title_en\":\"Why Choose Travel Wave\",\"title_ar\":\"\\u0644\\u0645\\u0627\\u0630\\u0627 \\u062a\\u062e\\u062a\\u0627\\u0631 Travel Wave\",\"subtitle_en\":\"A clearer service path for one of the most requested Schengen visa destinations.\",\"subtitle_ar\":\"\\u0645\\u0633\\u0627\\u0631 \\u0623\\u0648\\u0636\\u062d \\u0644\\u0644\\u062e\\u062f\\u0645\\u0629 \\u0641\\u064a \\u0648\\u0627\\u062d\\u062f\\u0629 \\u0645\\u0646 \\u0623\\u0643\\u062b\\u0631 \\u0648\\u062c\\u0647\\u0627\\u062a \\u0634\\u0646\\u063a\\u0646 \\u0637\\u0644\\u0628\\u064b\\u0627.\",\"items\":[{\"title_en\":\"Document Review\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"text_en\":\"We review the file before submission.\",\"text_ar\":\"\\u0646\\u0631\\u0627\\u062c\\u0639 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"meta_en\":\"File readiness\",\"meta_ar\":\"\\u062c\\u0627\\u0647\\u0632\\u064a\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"File Follow-up\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"text_en\":\"We keep the process organized and clear.\",\"text_ar\":\"\\u0646\\u062a\\u0627\\u0628\\u0639 \\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a \\u0628\\u0634\\u0643\\u0644 \\u0645\\u0646\\u0638\\u0645 \\u0648\\u0648\\u0627\\u0636\\u062d.\",\"meta_en\":\"Practical coordination\",\"meta_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0639\\u0645\\u0644\\u064a\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Full Support\",\"title_ar\":\"\\u062f\\u0639\\u0645 \\u0643\\u0627\\u0645\\u0644 \\u062d\\u062a\\u0649 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\",\"text_en\":\"From the first inquiry to the next action.\",\"text_ar\":\"\\u0645\\u0646 \\u0623\\u0648\\u0644 \\u0627\\u0633\\u062a\\u0641\\u0633\\u0627\\u0631 \\u0648\\u062d\\u062a\\u0649 \\u0627\\u0644\\u062e\\u0637\\u0648\\u0629 \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629.\",\"meta_en\":\"Step-by-step support\",\"meta_ar\":\"\\u062f\\u0639\\u0645 \\u062e\\u0637\\u0648\\u0629 \\u0628\\u062e\\u0637\\u0648\\u0629\",\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Fast Execution\",\"title_ar\":\"\\u0633\\u0631\\u0639\\u0629 \\u0641\\u064a \\u0627\\u0644\\u062a\\u0646\\u0641\\u064a\\u0630\",\"text_en\":\"A faster workflow when the documents are ready.\",\"text_ar\":\"\\u0645\\u0633\\u0627\\u0631 \\u0623\\u0633\\u0631\\u0639 \\u0639\\u0646\\u062f \\u0627\\u0643\\u062a\\u0645\\u0627\\u0644 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a.\",\"meta_en\":\"Clear timeline\",\"meta_ar\":\"\\u062c\\u062f\\u0648\\u0644 \\u0632\\u0645\\u0646\\u064a \\u0623\\u0648\\u0636\\u062d\",\"sort_order\":4,\"is_active\":true}]},\"quick_info\":{\"enabled\":true,\"title_en\":\"Quick France Visa Highlights\",\"title_ar\":\"\\u0645\\u0639\\u0644\\u0648\\u0645\\u0627\\u062a \\u0633\\u0631\\u064a\\u0639\\u0629 \\u0639\\u0646 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627\",\"subtitle_en\":\"Three points the campaign team can surface immediately for ad traffic.\",\"subtitle_ar\":\"\\u062b\\u0644\\u0627\\u062b \\u0646\\u0642\\u0627\\u0637 \\u0623\\u0633\\u0627\\u0633\\u064a\\u0629 \\u062a\\u0638\\u0647\\u0631 \\u0645\\u0628\\u0627\\u0634\\u0631\\u0629 \\u0644\\u0632\\u0648\\u0627\\u0631 \\u0627\\u0644\\u062d\\u0645\\u0644\\u0629 \\u0627\\u0644\\u0625\\u0639\\u0644\\u0627\\u0646\\u064a\\u0629.\",\"items\":[{\"label_en\":\"Visa Type\",\"label_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629\",\"value_en\":\"Short-Stay Schengen\",\"value_ar\":\"\\u0634\\u0646\\u063a\\u0646 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"15 to 30 working days\",\"value_ar\":\"15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Stay Duration\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"value_en\":\"Up to 90 days\",\"value_ar\":\"\\u062d\\u062a\\u0649 90 \\u064a\\u0648\\u0645\\u064b\\u0627\",\"sort_order\":3,\"is_active\":true}]},\"testimonials\":{\"enabled\":false,\"items\":[]},\"faq\":{\"enabled\":true,\"title_en\":\"Common Questions\",\"title_ar\":\"\\u0627\\u0644\\u0623\\u0633\\u0626\\u0644\\u0629 \\u0627\\u0644\\u0634\\u0627\\u0626\\u0639\\u0629\",\"subtitle_en\":\"Useful answers for paid campaign visitors.\",\"subtitle_ar\":\"\\u0625\\u062c\\u0627\\u0628\\u0627\\u062a \\u0633\\u0631\\u064a\\u0639\\u0629 \\u0648\\u0645\\u0647\\u0645\\u0629 \\u0644\\u0632\\u0648\\u0627\\u0631 \\u0627\\u0644\\u062d\\u0645\\u0644\\u0627\\u062a \\u0627\\u0644\\u0625\\u0639\\u0644\\u0627\\u0646\\u064a\\u0629.\",\"items\":[{\"question_en\":\"How long does the visa take?\",\"question_ar\":\"\\u0645\\u0627 \\u0645\\u062f\\u0629 \\u0627\\u0633\\u062a\\u062e\\u0631\\u0627\\u062c \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629\\u061f\",\"answer_en\":\"Usually around 15 to 30 working days depending on season and file completeness.\",\"answer_ar\":\"\\u063a\\u0627\\u0644\\u0628\\u064b\\u0627 \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0648\\u0633\\u0645 \\u0648\\u0627\\u0643\\u062a\\u0645\\u0627\\u0644 \\u0627\\u0644\\u0645\\u0644\\u0641.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"What documents are required?\",\"question_ar\":\"\\u0645\\u0627 \\u0627\\u0644\\u0623\\u0648\\u0631\\u0627\\u0642 \\u0627\\u0644\\u0645\\u0637\\u0644\\u0648\\u0628\\u0629\\u061f\",\"answer_en\":\"Passport, photos, financial proof, booking details, and supporting travel documents.\",\"answer_ar\":\"\\u062c\\u0648\\u0627\\u0632 \\u0627\\u0644\\u0633\\u0641\\u0631\\u060c \\u0627\\u0644\\u0635\\u0648\\u0631\\u060c \\u0627\\u0644\\u0625\\u062b\\u0628\\u0627\\u062a\\u0627\\u062a \\u0627\\u0644\\u0645\\u0627\\u0644\\u064a\\u0629\\u060c \\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a\\u060c \\u0648\\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0627\\u0644\\u062f\\u0627\\u0639\\u0645\\u0629.\",\"sort_order\":2,\"is_active\":true},{\"question_en\":\"Do you provide support until submission?\",\"question_ar\":\"\\u0647\\u0644 \\u064a\\u0648\\u062c\\u062f \\u062f\\u0639\\u0645 \\u062d\\u062a\\u0649 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\\u061f\",\"answer_en\":\"Yes, Travel Wave supports the file review and next practical steps until submission readiness.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u062a\\u0642\\u062f\\u0645 Travel Wave \\u0627\\u0644\\u062f\\u0639\\u0645 \\u0641\\u064a \\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a \\u0627\\u0644\\u0639\\u0645\\u0644\\u064a\\u0629 \\u062d\\u062a\\u0649 \\u0627\\u0644\\u0627\\u0633\\u062a\\u0639\\u062f\\u0627\\u062f \\u0644\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"sort_order\":3,\"is_active\":true}]},\"cta\":{\"enabled\":true,\"title_en\":\"Start Your France Visa Request Today\",\"title_ar\":\"\\u0627\\u0628\\u062f\\u0623 \\u0637\\u0644\\u0628 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627 \\u0627\\u0644\\u064a\\u0648\\u0645\",\"description_en\":\"Use this page as a live marketing example for ad traffic, forms, and performance tracking.\",\"description_ar\":\"\\u0627\\u0633\\u062a\\u062e\\u062f\\u0645 \\u0647\\u0630\\u0647 \\u0627\\u0644\\u0635\\u0641\\u062d\\u0629 \\u0643\\u0646\\u0645\\u0648\\u0630\\u062c \\u062d\\u064a \\u0644\\u0641\\u0631\\u064a\\u0642 \\u0627\\u0644\\u062a\\u0633\\u0648\\u064a\\u0642 \\u0644\\u062a\\u062c\\u0631\\u0628\\u0629 \\u0627\\u0644\\u0625\\u0639\\u0644\\u0627\\u0646\\u0627\\u062a \\u0648\\u0627\\u0644\\u0646\\u0645\\u0627\\u0630\\u062c \\u0648\\u062a\\u062a\\u0628\\u0639 \\u0627\\u0644\\u0623\\u062f\\u0627\\u0621.\",\"primary_button_text_en\":\"Start Now\",\"primary_button_text_ar\":\"\\u0627\\u0628\\u062f\\u0623 \\u0627\\u0644\\u0622\\u0646\",\"primary_button_url\":\"#marketing-form\",\"secondary_button_text_en\":\"Chat on WhatsApp\",\"secondary_button_text_ar\":\"\\u062a\\u0648\\u0627\\u0635\\u0644 \\u0648\\u0627\\u062a\\u0633\\u0627\\u0628\",\"secondary_button_url\":\"https:\\/\\/wa.me\\/201060500236?text=%D9%85%D8%B1%D8%AD%D8%A8%D9%8B%D8%A7%D8%8C%20%D8%A3%D8%B1%D9%8A%D8%AF%20%D8%A7%D9%84%D8%A7%D8%B3%D8%AA%D9%81%D8%B3%D8%A7%D8%B1%20%D8%B9%D9%86%20%D8%AE%D8%AF%D9%85%D8%A7%D8%AA%20Travel%20Wave\"},\"form\":{\"enabled\":true,\"title_en\":\"Request Your France Visa Callback\",\"title_ar\":\"\\u0627\\u0637\\u0644\\u0628 \\u0627\\u0644\\u062a\\u0648\\u0627\\u0635\\u0644 \\u0628\\u062e\\u0635\\u0648\\u0635 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627\",\"subtitle_en\":\"A demo lead form connected to the new Marketing module.\",\"subtitle_ar\":\"\\u0646\\u0645\\u0648\\u0630\\u062c \\u062a\\u062c\\u0631\\u064a\\u0628\\u064a \\u0645\\u0631\\u062a\\u0628\\u0637 \\u0645\\u0628\\u0627\\u0634\\u0631\\u0629 \\u0628\\u0648\\u062d\\u062f\\u0629 \\u0627\\u0644\\u062a\\u0633\\u0648\\u064a\\u0642 \\u0627\\u0644\\u062c\\u062f\\u064a\\u062f\\u0629.\"}}', 'France Visa 2026 | Travel Wave Campaign', 'تأشيرة فرنسا 2026 | حملة Travel Wave', 'France visa campaign landing page with a lead form and quick visa highlights.', 'صفحة هبوط لحملة تأشيرة فرنسا مع نموذج تواصل ومعلومات سريعة عن التأشيرة.', 'meta', 'cpc', 'france_visa_2026', 'creative_a', NULL, 'http://localhost/campaigns/france-visa-campaign-demo', 'Demo marketing landing page for testing the marketing workflow.', '2026-03-25 23:07:03', '2026-03-25 23:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `marketing_landing_page_events`
--

CREATE TABLE `marketing_landing_page_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `marketing_landing_page_id` bigint(20) UNSIGNED NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `session_key` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `campaign` varchar(255) DEFAULT NULL,
  `content` varchar(255) DEFAULT NULL,
  `term` varchar(255) DEFAULT NULL,
  `referrer` text DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `occurred_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_assets`
--

CREATE TABLE `media_assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `disk` varchar(255) NOT NULL DEFAULT 'public',
  `directory` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `width` int(10) UNSIGNED DEFAULT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL,
  `is_favorite` tinyint(1) NOT NULL DEFAULT 0,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_assets`
--

INSERT INTO `media_assets` (`id`, `title`, `alt_text`, `caption`, `disk`, `directory`, `file_name`, `path`, `mime_type`, `extension`, `size`, `width`, `height`, `is_favorite`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'travel-wave-logo', NULL, NULL, 'public', 'settings', 'travel-wave-logo.svg', 'settings/travel-wave-logo.svg', 'image/svg+xml', 'svg', 1194, NULL, NULL, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(2, 'PCcL15rNg2aOwPEisbjcjDAg95DNHyGzERBvzhm8', NULL, NULL, 'public', 'settings', 'PCcL15rNg2aOwPEisbjcjDAg95DNHyGzERBvzhm8.png', 'settings/PCcL15rNg2aOwPEisbjcjDAg95DNHyGzERBvzhm8.png', 'image/png', 'png', 37236, 500, 500, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(3, 'cyBFoWR1Xbhefdk05xxPFszwiQOiPpzeaJcMrmKO', NULL, NULL, 'public', 'settings', 'cyBFoWR1Xbhefdk05xxPFszwiQOiPpzeaJcMrmKO.png', 'settings/cyBFoWR1Xbhefdk05xxPFszwiQOiPpzeaJcMrmKO.png', 'image/png', 'png', 92233, 420, 450, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(4, 'XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj', NULL, NULL, 'public', 'hero-slides', 'XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 'image/png', 'png', 1734494, 1520, 704, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(5, '1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0', NULL, NULL, 'public', 'hero-slides', '1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'image/png', 'png', 2176028, 1520, 704, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(6, 'AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh', NULL, NULL, 'public', 'hero-slides', 'AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'hero-slides/AHA77CWYemeYDheAtHSRseA0io46WT0GuRkU8Vfh.jpg', 'image/jpeg', 'jpg', 227216, 1520, 704, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(7, 'slide-3', NULL, NULL, 'public', 'hero-slides', 'slide-3.svg', 'hero-slides/slide-3.svg', 'image/svg+xml', 'svg', 1495, NULL, NULL, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(8, 'france-flag', NULL, NULL, 'public', 'visa-countries', 'france-flag.svg', 'visa-countries/france-flag.svg', 'image/svg+xml', 'svg', 468, NULL, NULL, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(9, 'germany-flag', NULL, NULL, 'public', 'visa-countries', 'germany-flag.svg', 'visa-countries/germany-flag.svg', 'image/svg+xml', 'svg', 233, NULL, NULL, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(10, 'italy-flag', NULL, NULL, 'public', 'visa-countries', 'italy-flag.svg', 'visa-countries/italy-flag.svg', 'image/svg+xml', 'svg', 248, NULL, NULL, 0, NULL, '2026-03-25 23:07:12', '2026-03-25 23:07:12'),
(11, '22', NULL, NULL, 'public', 'media-library', 'WzBlBWlLYv6Zi6sGNyOnhzw71nR8l2yCB5ecZOON.png', 'media-library/WzBlBWlLYv6Zi6sGNyOnhzw71nR8l2yCB5ecZOON.png', 'image/png', 'png', 92233, 420, 450, 0, 1, '2026-03-26 00:31:41', '2026-03-26 00:31:41'),
(12, 'Trans Travel Wave logo', NULL, NULL, 'public', 'settings', 'hrjm7Ro5OXBalGjcu7gRUk5RDAq1phPxwnd5J0w6.png', 'settings/hrjm7Ro5OXBalGjcu7gRUk5RDAq1phPxwnd5J0w6.png', 'image/png', 'png', 33125, 482, 160, 0, 1, '2026-03-26 00:32:18', '2026-03-26 00:32:18'),
(13, 'Copy of Travel Wave logo (1)', NULL, NULL, 'public', 'settings', '2hdFdt8w0L0aebsrV3YJRrubQua0kHRHIBhbz0Ao.png', 'settings/2hdFdt8w0L0aebsrV3YJRrubQua0kHRHIBhbz0Ao.png', 'image/png', 'png', 53666, 500, 300, 0, 1, '2026-03-26 00:35:46', '2026-03-26 00:35:46'),
(14, 'Gemini_Generated_Image_313cwy313cwy313c (1)', NULL, NULL, 'public', 'settings', 'SQsoiQLdYYvPtrWnKWCDlxaJV3GpVui4vuYBgfa3.png', 'settings/SQsoiQLdYYvPtrWnKWCDlxaJV3GpVui4vuYBgfa3.png', 'image/png', 'png', 188105, 992, 1063, 0, 1, '2026-03-26 00:42:01', '2026-03-26 00:42:01'),
(15, 'Gemini_Generated_Image_sjhbhgsjhbhgsjhb', NULL, NULL, 'public', 'hero-slides', 'mmjsGhPqpbdxZL1b932XHONE5GHYvBHSg6Ef4WEq.png', 'hero-slides/mmjsGhPqpbdxZL1b932XHONE5GHYvBHSg6Ef4WEq.png', 'image/png', 'png', 2100532, 1264, 832, 0, 1, '2026-03-26 01:00:35', '2026-03-26 01:00:35'),
(16, 'Gemini_Generated_Image_e39n5oe39n5oe39n', NULL, NULL, 'public', 'hero-slides', 'xCrNFx3yBThlbiH4s0vtTCIdBeQ6dKGGAzsBrfrg.png', 'hero-slides/xCrNFx3yBThlbiH4s0vtTCIdBeQ6dKGGAzsBrfrg.png', 'image/png', 'png', 1610492, 1815, 592, 0, 1, '2026-03-26 01:03:36', '2026-03-26 01:03:36'),
(17, 'Gemini_Generated_Image_xqznzfxqznzfxqzn', NULL, NULL, 'public', 'media-library', 'DExTaYIB43hfIRCdpPiFH37AisJoMIUpfxuHYm3I.png', 'media-library/DExTaYIB43hfIRCdpPiFH37AisJoMIUpfxuHYm3I.png', 'image/png', 'png', 2246210, 1584, 672, 0, 1, '2026-03-26 02:26:47', '2026-03-26 02:26:47'),
(18, 'Gemini_Generated_Image_8aej38aej38aej38 (1) - Edited', NULL, NULL, 'public', 'media-library', 'rEi6yTR7oeKXH5ywxISdsn8gpZqIGGaoTeaxPLUN.png', 'media-library/rEi6yTR7oeKXH5ywxISdsn8gpZqIGGaoTeaxPLUN.png', 'image/png', 'png', 1597962, 1520, 704, 0, 1, '2026-03-26 02:35:59', '2026-03-26 02:35:59'),
(19, 'unnamed - Edited', NULL, NULL, 'public', 'media-library', 'gjMGlbJjGNbkrjuOvDOIsnIMAOQHL4WiUcnShpm4.jpg', 'media-library/gjMGlbJjGNbkrjuOvDOIsnIMAOQHL4WiUcnShpm4.jpg', 'image/jpeg', 'jpg', 253608, 1520, 704, 0, 1, '2026-03-26 02:35:59', '2026-03-26 02:35:59'),
(20, 'Untitled design', NULL, NULL, 'public', 'media-library', 'N6MHrb8TDobuCmToWomGNkjxF7vGwO8boqEV8QUc.png', 'media-library/N6MHrb8TDobuCmToWomGNkjxF7vGwO8boqEV8QUc.png', 'image/png', 'png', 1724539, 1520, 704, 0, 1, '2026-03-26 02:35:59', '2026-03-26 02:35:59'),
(21, '07d5b9b2-f1e8-4081-8967-cb22c7a9810c', NULL, NULL, 'public', 'visa-countries', 'WaXDA447Ww9ouToBtyb0AJePJgQZpvj2QkK44RmP.png', 'visa-countries/WaXDA447Ww9ouToBtyb0AJePJgQZpvj2QkK44RmP.png', 'image/png', 'png', 2895068, 1536, 1024, 0, 3, '2026-03-27 19:02:58', '2026-03-27 19:02:58'),
(22, 'international-2681315_1280-780x470', NULL, NULL, 'public', 'media-library', 'DNDaBfaFXeEOOMlHp5pCrCLCKT6w4fT6JeaZhT1W.jpg', 'media-library/DNDaBfaFXeEOOMlHp5pCrCLCKT6w4fT6JeaZhT1W.jpg', 'image/jpeg', 'jpg', 40159, 780, 470, 0, 3, '2026-03-27 21:33:37', '2026-03-27 21:33:37'),
(23, 'kIGUFgWrDz8qmHK1O3eokREfQEf66r2XpyFTdDNm', NULL, NULL, 'public', 'visa-countries/highlights', 'kIGUFgWrDz8qmHK1O3eokREfQEf66r2XpyFTdDNm.jpg', 'visa-countries/highlights/kIGUFgWrDz8qmHK1O3eokREfQEf66r2XpyFTdDNm.jpg', 'image/jpeg', 'jpg', 40159, 780, 470, 0, 3, '2026-03-27 21:35:10', '2026-03-27 21:35:10'),
(24, 'Gemini_Generated_Image_313cwy313cwy313c', NULL, NULL, 'public', 'visa-countries/highlights', 'ROPFjByXiumOxWhvgMvssXrhWOtaxgpfAFqdFsPx.png', 'visa-countries/highlights/ROPFjByXiumOxWhvgMvssXrhWOtaxgpfAFqdFsPx.png', 'image/png', 'png', 737733, 992, 1063, 0, 3, '2026-03-27 21:38:04', '2026-03-27 21:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT 'header',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `route_name` varchar(255) DEFAULT NULL,
  `target` varchar(255) NOT NULL DEFAULT '_self',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `title_en`, `title_ar`, `url`, `route_name`, `target`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'header', NULL, 'Home', 'الرئيسية', NULL, 'home', '_self', 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 'header', NULL, 'Overseas Visas', 'التأشيرات الخارجية', NULL, 'visas.index', '_self', 2, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(3, 'header', NULL, 'Domestic Tourism', 'السياحة الداخلية', NULL, 'destinations.index', '_self', 3, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(4, 'header', NULL, 'Flights', 'الطيران', NULL, 'flights', '_self', 4, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(5, 'header', NULL, 'Hotels', 'الفنادق', NULL, 'hotels', '_self', 5, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(6, 'header', NULL, 'About Us', 'من نحن', NULL, 'about', '_self', 6, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(7, 'header', NULL, 'Blog', 'المقالات', NULL, 'blog.index', '_self', 7, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(8, 'header', NULL, 'Contact Us', 'تواصل معنا', NULL, 'contact', '_self', 8, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(9, 'footer', NULL, 'About Us', 'من نحن', NULL, 'about', '_self', 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(10, 'footer', NULL, 'Blog', 'المقالات', NULL, 'blog.index', '_self', 2, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(11, 'footer', NULL, 'Contact Us', 'تواصل معنا', NULL, 'contact', '_self', 3, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL);

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_03_18_155647_add_is_admin_to_users_table', 1),
(6, '2026_03_18_155647_create_pages_table', 1),
(7, '2026_03_18_155647_create_settings_table', 1),
(8, '2026_03_18_155647_create_visa_categories_table', 1),
(9, '2026_03_18_155647_create_visa_countries_table', 1),
(10, '2026_03_18_155649_create_blog_categories_table', 1),
(11, '2026_03_18_155649_create_blog_posts_table', 1),
(12, '2026_03_18_155649_create_destinations_table', 1),
(13, '2026_03_18_155649_create_inquiries_table', 1),
(14, '2026_03_18_155649_create_menu_items_table', 1),
(15, '2026_03_18_155649_create_testimonials_table', 1),
(16, '2026_03_18_204350_create_hero_slides_table', 1),
(17, '2026_03_18_204600_add_hero_slider_settings_to_settings_table', 1),
(18, '2026_03_19_120000_add_template_fields_to_visa_countries_table', 1),
(19, '2026_03_19_150000_add_branding_fields_to_settings_table', 1),
(20, '2026_03_19_150100_add_mobile_image_to_hero_slides_table', 1),
(21, '2026_03_19_170000_add_header_footer_and_country_strip_settings_to_settings_table', 1),
(22, '2026_03_19_170100_create_home_country_strip_items_table', 1),
(23, '2026_03_19_190000_add_reference_layout_fields_to_visa_countries_table', 1),
(24, '2026_03_19_210000_add_premium_fields_to_home_country_strip_items_and_settings', 1),
(25, '2026_03_19_220000_add_carousel_controls_to_settings_table', 1),
(26, '2026_03_20_100000_add_dynamic_page_fields_to_destinations_table', 1),
(27, '2026_03_20_130000_create_lead_forms_tables', 1),
(28, '2026_03_20_130100_add_form_manager_fields_to_inquiries_table', 1),
(29, '2026_03_20_140000_add_inquiry_section_fields_to_visa_countries_table', 1),
(30, '2026_03_21_100000_create_map_sections_tables', 1),
(31, '2026_03_21_120000_add_floating_whatsapp_settings_to_settings_table', 1),
(32, '2026_03_21_140000_create_tracking_integrations_table', 1),
(33, '2026_03_21_160000_create_marketing_landing_pages_tables', 1),
(34, '2026_03_21_170000_add_meta_conversion_api_settings_to_settings_table', 1),
(35, '2026_03_21_180000_create_seo_manager_tables', 1),
(36, '2026_03_21_190000_add_user_access_fields_and_create_rbac_tables', 1),
(37, '2026_03_22_100000_create_media_assets_table', 1),
(38, '2026_03_22_110000_add_logo_size_controls_to_settings_table', 1),
(39, '2026_03_22_120000_ensure_header_logo_setting_columns_exist', 1),
(40, '2026_03_22_130000_add_separate_header_logo_fields_to_settings_table', 1),
(41, '2026_03_22_140000_add_logo_display_modes_to_settings_table', 1),
(42, '2026_03_22_150000_add_more_social_links_to_settings_table', 1),
(43, '2026_03_22_160000_add_soft_deletes_to_pages_table', 1),
(44, '2026_03_22_170000_add_soft_deletes_to_home_country_strip_items_table', 1),
(45, '2026_03_22_180000_add_soft_deletes_to_managed_content_tables', 1),
(46, '2026_03_22_190000_add_settings_to_tracking_integrations_table', 1),
(47, '2026_03_22_200000_add_chatbot_settings_to_settings_table', 1),
(48, '2026_03_22_200100_create_chatbot_tables', 1),
(49, '2026_03_22_210000_create_crm_core_tables', 1),
(50, '2026_03_22_220000_upgrade_crm_status_workflow', 1),
(51, '2026_03_22_230000_simplify_crm_workflow', 1),
(52, '2026_03_22_231000_create_crm_follow_ups_and_notifications_table', 1),
(53, '2026_03_22_232000_add_crm_service_type_workflow', 1),
(54, '2026_03_23_090000_ensure_call_later_crm_status_is_active', 1),
(55, '2026_03_23_100000_add_crm_lead_assignment_history', 1),
(56, '2026_03_23_110000_ensure_merged_crm_status_is_active', 1),
(57, '2026_03_23_120000_create_crm_information_tables', 1),
(58, '2026_03_23_120100_ensure_crm_information_permission_exists', 1),
(59, '2026_03_25_100000_create_accounting_module_tables', 1),
(60, '2026_03_25_100100_ensure_accounting_permissions_exist', 1),
(61, '2026_03_25_130000_upgrade_crm_tasks_module', 1),
(62, '2026_03_25_140000_create_utm_analytics_tables', 1),
(63, '2026_03_25_150000_add_category_to_crm_tasks_table', 1),
(64, '2026_03_25_160000_create_crm_customers_tables', 1),
(65, '2026_03_25_160100_ensure_customer_permissions_exist', 1),
(66, '2026_03_25_180000_create_crm_documents_tables', 1),
(67, '2026_03_25_180100_ensure_document_permissions_and_categories_exist', 1),
(68, '2026_03_25_210000_create_audit_logs_table', 1),
(69, '2026_03_25_210100_ensure_audit_log_permission_exists', 1),
(70, '2026_03_25_220000_create_knowledge_base_tables', 1),
(71, '2026_03_25_220100_ensure_knowledge_base_permissions_exist', 1),
(72, '2026_03_25_230000_create_workflow_automation_tables', 1),
(73, '2026_03_25_230100_ensure_workflow_permissions_exist', 1),
(74, '2026_03_25_231000_expand_utm_campaigns_for_marketing_module', 2),
(75, '2026_03_25_232000_create_goals_and_commissions_tables', 3),
(76, '2026_03_25_232100_ensure_goals_commissions_permissions_exist', 3),
(77, '2026_03_26_110000_add_image_framing_to_hero_slides_table', 3),
(78, '2026_03_26_120000_add_treasuries_to_accounting_module', 4),
(79, '2026_03_27_120000_add_header_locale_alignment_settings_to_settings_table', 5),
(80, '2026_03_27_140000_create_chatbot_knowledge_entries_table', 6),
(81, '2026_03_27_193000_add_best_time_fields_to_visa_countries_table', 7),
(82, '2026_03_27_201000_add_highlights_section_heading_fields_to_visa_countries_table', 8),
(83, '2026_03_28_120000_add_quick_summary_destination_fields_to_visa_countries_table', 9),
(84, '2026_03_27_190000_add_best_time_badge_to_destinations_table', 10),
(85, '2026_03_28_140000_add_highlights_section_label_fields_to_destinations_table', 10),
(86, '2026_03_28_160000_add_quick_summary_destination_fields_to_destinations_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_ar` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `hero_badge_en` varchar(255) DEFAULT NULL,
  `hero_badge_ar` varchar(255) DEFAULT NULL,
  `hero_title_en` varchar(255) DEFAULT NULL,
  `hero_title_ar` varchar(255) DEFAULT NULL,
  `hero_subtitle_en` text DEFAULT NULL,
  `hero_subtitle_ar` text DEFAULT NULL,
  `hero_primary_cta_text_en` varchar(255) DEFAULT NULL,
  `hero_primary_cta_text_ar` varchar(255) DEFAULT NULL,
  `hero_primary_cta_url` varchar(255) DEFAULT NULL,
  `hero_secondary_cta_text_en` varchar(255) DEFAULT NULL,
  `hero_secondary_cta_text_ar` varchar(255) DEFAULT NULL,
  `hero_secondary_cta_url` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `intro_title_en` varchar(255) DEFAULT NULL,
  `intro_title_ar` varchar(255) DEFAULT NULL,
  `intro_body_en` text DEFAULT NULL,
  `intro_body_ar` text DEFAULT NULL,
  `sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sections`)),
  `meta_title_en` varchar(255) DEFAULT NULL,
  `meta_title_ar` varchar(255) DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `key`, `title_en`, `title_ar`, `slug`, `hero_badge_en`, `hero_badge_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_primary_cta_text_en`, `hero_primary_cta_text_ar`, `hero_primary_cta_url`, `hero_secondary_cta_text_en`, `hero_secondary_cta_text_ar`, `hero_secondary_cta_url`, `hero_image`, `intro_title_en`, `intro_title_ar`, `intro_body_en`, `intro_body_ar`, `sections`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'home', 'Home', 'الرئيسية', 'home', 'Integrated Travel Services', 'خدمات سفر متكاملة', 'We organize your journey from the first step to the final detail', 'ننظم رحلتك من أول خطوة حتى آخر تفصيلة', 'With Travel Wave, you get an integrated service that makes travel planning easier, from file preparation and bookings to destination selection and trip planning.', 'مع Travel Wave تحصل على خدمة متكاملة تجعل تخطيط السفر أسهل، من تجهيز الملف والحجوزات إلى اختيار الوجهة وتنظيم الرحلة.', 'Browse Visas', 'تصفح التأشيرات', '/visas', 'Browse Trips', 'تصفح الرحلات', '/domestic-tourism', NULL, 'One team for visas, trips, flights, and hotels', 'فريق واحد للتأشيرات والرحلات والطيران والفنادق', 'Travel Wave combines outbound travel, domestic tourism, visa services, hotels, and flights in one organized customer journey.', 'تجمع Travel Wave بين السفر الخارجي والسياحة الداخلية وخدمات التأشيرات والفنادق والطيران ضمن رحلة عميل منظمة وواضحة.', '{\"services\":[{\"title_en\":\"Visa Services\",\"title_ar\":\"\\u062e\\u062f\\u0645\\u0627\\u062a \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0627\\u062a\",\"text_en\":\"Guided support for preparing files and understanding requirements.\",\"text_ar\":\"\\u062f\\u0639\\u0645 \\u0645\\u0646\\u0638\\u0645 \\u0644\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0644\\u0641\\u0627\\u062a \\u0648\\u0641\\u0647\\u0645 \\u0627\\u0644\\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a.\",\"icon\":\"VS\"},{\"title_en\":\"International Travel\",\"title_ar\":\"\\u0627\\u0644\\u0633\\u064a\\u0627\\u062d\\u0629 \\u0627\\u0644\\u062e\\u0627\\u0631\\u062c\\u064a\\u0629\",\"text_en\":\"Travel planning for outbound tourism and flexible offers.\",\"text_ar\":\"\\u062a\\u062e\\u0637\\u064a\\u0637 \\u0644\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u062e\\u0627\\u0631\\u062c\\u064a \\u0648\\u0639\\u0631\\u0648\\u0636 \\u0645\\u0631\\u0646\\u0629 \\u0644\\u0644\\u0631\\u062d\\u0644\\u0627\\u062a.\",\"icon\":\"IT\"},{\"title_en\":\"Domestic Tourism\",\"title_ar\":\"\\u0627\\u0644\\u0633\\u064a\\u0627\\u062d\\u0629 \\u0627\\u0644\\u062f\\u0627\\u062e\\u0644\\u064a\\u0629\",\"text_en\":\"Egypt destination packages with hotel and activity options.\",\"text_ar\":\"\\u0628\\u0627\\u0642\\u0627\\u062a \\u062f\\u0627\\u062e\\u0644 \\u0645\\u0635\\u0631 \\u0645\\u0639 \\u062e\\u064a\\u0627\\u0631\\u0627\\u062a \\u0641\\u0646\\u0627\\u062f\\u0642 \\u0648\\u0623\\u0646\\u0634\\u0637\\u0629.\",\"icon\":\"DT\"}],\"why_choose_us\":[{\"title_en\":\"Organization\",\"title_ar\":\"\\u0627\\u0644\\u062a\\u0646\\u0638\\u064a\\u0645\",\"text_en\":\"We make the travel process easy to follow.\",\"text_ar\":\"\\u0646\\u062c\\u0639\\u0644 \\u062e\\u0637\\u0648\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0633\\u0647\\u0644\\u0629 \\u0648\\u0648\\u0627\\u0636\\u062d\\u0629.\"},{\"title_en\":\"Responsiveness\",\"title_ar\":\"\\u0633\\u0631\\u0639\\u0629 \\u0627\\u0644\\u0627\\u0633\\u062a\\u062c\\u0627\\u0628\\u0629\",\"text_en\":\"Practical answers and follow-up when you need them.\",\"text_ar\":\"\\u0631\\u062f\\u0648\\u062f \\u0639\\u0645\\u0644\\u064a\\u0629 \\u0648\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0648\\u0642\\u062a \\u0627\\u0644\\u062d\\u0627\\u062c\\u0629.\"}],\"how_it_works\":[{\"title_en\":\"Share your need\",\"title_ar\":\"\\u0623\\u062e\\u0628\\u0631\\u0646\\u0627 \\u0628\\u0637\\u0644\\u0628\\u0643\",\"text_en\":\"Tell us the destination, purpose, and timing.\",\"text_ar\":\"\\u0623\\u062e\\u0628\\u0631\\u0646\\u0627 \\u0628\\u0627\\u0644\\u0648\\u062c\\u0647\\u0629 \\u0648\\u0627\\u0644\\u0647\\u062f\\u0641 \\u0648\\u0627\\u0644\\u0645\\u0648\\u0639\\u062f.\"},{\"title_en\":\"Receive a clear plan\",\"title_ar\":\"\\u0627\\u0633\\u062a\\u0644\\u0645 \\u062e\\u0637\\u0629 \\u0648\\u0627\\u0636\\u062d\\u0629\",\"text_en\":\"We outline the suitable service and next steps.\",\"text_ar\":\"\\u0646\\u0648\\u0636\\u062d \\u0644\\u0643 \\u0627\\u0644\\u062e\\u062f\\u0645\\u0629 \\u0627\\u0644\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0648\\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629.\"}],\"promo\":{\"title_en\":\"Flights and hotels, planned together\",\"title_ar\":\"\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0636\\u0645\\u0646 \\u062e\\u0637\\u0629 \\u0648\\u0627\\u062d\\u062f\\u0629\",\"text_en\":\"Bundle your trip with coordinated flight and hotel support for smoother planning.\",\"text_ar\":\"\\u0646\\u0633\\u0642 \\u0631\\u062d\\u0644\\u062a\\u0643 \\u0645\\u0639 \\u062f\\u0639\\u0645 \\u0645\\u062a\\u0643\\u0627\\u0645\\u0644 \\u0644\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u0641\\u0646\\u0627\\u062f\\u0642 \\u0644\\u062a\\u062e\\u0637\\u064a\\u0637 \\u0623\\u0643\\u062b\\u0631 \\u0633\\u0647\\u0648\\u0644\\u0629.\",\"button_en\":\"Explore Services\",\"button_ar\":\"\\u0627\\u0643\\u062a\\u0634\\u0641 \\u0627\\u0644\\u062e\\u062f\\u0645\\u0627\\u062a\",\"url\":\"\\/flights\"},\"inquiry\":{\"title_en\":\"Tell us where you want to go\",\"title_ar\":\"\\u0623\\u062e\\u0628\\u0631\\u0646\\u0627 \\u0625\\u0644\\u0649 \\u0623\\u064a\\u0646 \\u062a\\u0631\\u064a\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631\",\"text_en\":\"Our team can recommend the right visa, destination, or booking path.\",\"text_ar\":\"\\u064a\\u0645\\u0643\\u0646 \\u0644\\u0641\\u0631\\u064a\\u0642\\u0646\\u0627 \\u062a\\u0631\\u0634\\u064a\\u062d \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0623\\u0648 \\u0627\\u0644\\u0648\\u062c\\u0647\\u0629 \\u0623\\u0648 \\u0645\\u0633\\u0627\\u0631 \\u0627\\u0644\\u062d\\u062c\\u0632 \\u0627\\u0644\\u0623\\u0646\\u0633\\u0628 \\u0644\\u0643.\"},\"final_cta\":{\"title_en\":\"Start your request with confidence\",\"title_ar\":\"\\u0627\\u0628\\u062f\\u0623 \\u0637\\u0644\\u0628\\u0643 \\u0628\\u062b\\u0642\\u0629\",\"text_en\":\"Travel planning becomes easier when every detail has a clear next step.\",\"text_ar\":\"\\u064a\\u0635\\u0628\\u062d \\u062a\\u062e\\u0637\\u064a\\u0637 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0623\\u0633\\u0647\\u0644 \\u0639\\u0646\\u062f\\u0645\\u0627 \\u062a\\u0643\\u0648\\u0646 \\u0643\\u0644 \\u062a\\u0641\\u0635\\u064a\\u0644\\u0629 \\u0645\\u0631\\u062a\\u0628\\u0637\\u0629 \\u0628\\u062e\\u0637\\u0648\\u0629 \\u0648\\u0627\\u0636\\u062d\\u0629.\",\"button_en\":\"Contact Travel Wave\",\"button_ar\":\"\\u062a\\u0648\\u0627\\u0635\\u0644 \\u0645\\u0639 Travel Wave\",\"url\":\"\\/contact\"}}', NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 'visas', 'Visas', 'التأشيرات', 'visas', NULL, NULL, 'Overseas visa services', 'خدمات التأشيرات الخارجية', 'Explore categories, compare countries, and request support.', 'استعرض الفئات وقارن بين الدول واطلب الدعم المناسب.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(3, 'domestic', 'Domestic Tourism', 'السياحة الداخلية', 'domestic', NULL, NULL, 'Domestic tourism in Egypt', 'السياحة الداخلية داخل مصر', 'Trips to the most requested destinations with practical packages.', 'رحلات إلى أكثر الوجهات طلبًا مع باقات عملية.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(4, 'flights', 'Flights', 'الطيران', 'flights', NULL, NULL, 'Flight booking support', 'دعم حجز الطيران', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Choose the right route and timing', 'اختر المسار والتوقيت المناسبين', 'Travel Wave supports customers with flight planning, route comparison, and booking coordination.', 'تدعم Travel Wave العملاء في تخطيط الرحلات الجوية ومقارنة المسارات وتنسيق الحجز.', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(5, 'hotels', 'Hotels', 'الفنادق', 'hotels', NULL, NULL, 'Hotel booking support', 'دعم حجز الفنادق', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Stay options that match your budget', 'خيارات إقامة تناسب ميزانيتك', 'We help travelers compare hotel categories and select the right stay for their trip.', 'نساعد المسافرين على مقارنة الفئات الفندقية واختيار الإقامة المناسبة لرحلتهم.', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(6, 'about', 'About Us', 'من نحن', 'about', NULL, NULL, 'About Travel Wave', 'عن Travel Wave', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A travel company built around clarity and follow-up', 'شركة سفر مبنية على الوضوح والمتابعة', 'Travel Wave was created to organize the customer journey across visas, travel planning, flights, hotels, and domestic tourism with one reliable team.', 'تم إنشاء Travel Wave لتنظيم رحلة العميل في التأشيرات وتخطيط السفر والطيران والفنادق والسياحة الداخلية من خلال فريق واحد موثوق.', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(7, 'contact', 'Contact', 'تواصل معنا', 'contact', NULL, NULL, 'Speak with Travel Wave', 'تحدث مع Travel Wave', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'We are ready to help with the next step', 'نحن جاهزون لمساعدتك في الخطوة التالية', 'Share your inquiry and our team will guide you toward the right service.', 'أرسل استفسارك وسيقوم فريقنا بتوجيهك إلى الخدمة المناسبة.', NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(8, 'blog', 'Blog', 'المقالات', 'blog', NULL, NULL, 'Travel insights and practical tips', 'محتوى السفر والنصائح العملية', 'Articles that help travelers prepare before booking or applying.', 'مقالات تساعد المسافرين على الاستعداد قبل الحجز أو التقديم.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `module`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Manage CRM Information', 'information.manage', 'leads', 'Create and review targeted CRM information notices and acknowledgements.', '2026-03-25 20:41:07', '2026-03-25 20:41:08'),
(2, 'Dashboard Access', 'dashboard.access', 'dashboard', 'Access the admin dashboard and authenticated admin area.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(3, 'View Reports', 'reports.view', 'dashboard', 'View dashboard reports and high-level analytics.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(4, 'View Audit Logs', 'audit_logs.view', 'audit', 'View immutable audit records for sensitive operational actions across the system.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(5, 'View Workflow Automations', 'workflow_automations.view', 'workflow_automations', 'View workflow automation rules and execution traces.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(6, 'Manage Workflow Automations', 'workflow_automations.manage', 'workflow_automations', 'Create, edit, enable, and disable controlled workflow rules.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(7, 'View Knowledge Base', 'knowledge_base.view', 'knowledge_base', 'View published internal knowledge base articles and operational reference content.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(8, 'Manage Knowledge Base', 'knowledge_base.manage', 'knowledge_base', 'Create, edit, publish, and archive knowledge base articles.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(9, 'Manage Knowledge Base Categories', 'knowledge_base.categories.manage', 'knowledge_base', 'Manage reusable categories used by the knowledge base module.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(10, 'View Users', 'users.view', 'users', 'View the users list and user profile details.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(11, 'Create Users', 'users.create', 'users', 'Create new dashboard users.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(12, 'Edit Users', 'users.edit', 'users', 'Edit existing dashboard users.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(13, 'Delete Users', 'users.delete', 'users', 'Delete dashboard users when allowed.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(14, 'Reset User Passwords', 'users.reset_password', 'users', 'Reset dashboard user passwords.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(15, 'Manage Roles', 'roles.manage', 'roles', 'Create, edit, and assign role permissions.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(16, 'Manage Permissions', 'permissions.manage', 'roles', 'Create, edit, and delete permissions.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(17, 'Manage Core Settings', 'settings.manage', 'settings', 'Manage brand, header, footer, and core dashboard settings.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(18, 'Manage Security Settings', 'security.manage', 'settings', 'Manage sensitive system-level and security settings.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(19, 'Manage Translations', 'translations.manage', 'settings', 'Manage localization and translation-related settings.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(20, 'View Pages', 'pages.view', 'pages', 'View pages and service content.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(21, 'Create Pages', 'pages.create', 'pages', 'Create new page content where supported.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(22, 'Edit Pages', 'pages.edit', 'pages', 'Edit page and service content.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(23, 'Delete Pages', 'pages.delete', 'pages', 'Delete or archive page content.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(24, 'Publish Pages', 'pages.publish', 'pages', 'Publish or unpublish page content.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(25, 'Manage Destinations', 'destinations.manage', 'pages', 'Manage domestic destinations, visa destinations, and categories.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(26, 'Manage Blog', 'blog.manage', 'pages', 'Manage blog categories and posts.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(27, 'Manage Media', 'media.manage', 'pages', 'Upload or manage media assets used in content.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(28, 'Manage Navigation', 'menu.manage', 'pages', 'Manage site menus and navigation items.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(29, 'Manage Testimonials', 'testimonials.manage', 'pages', 'Manage testimonials and social proof sections.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(30, 'Manage Forms', 'forms.manage', 'forms', 'Manage reusable lead and inquiry forms.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(31, 'View Form Submissions', 'forms.submissions.view', 'forms', 'View form submissions across the site.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(32, 'Manage Form Submissions', 'forms.submissions.edit', 'forms', 'Edit submission status and follow-up metadata.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(33, 'Manage Maps', 'maps.manage', 'maps', 'Manage reusable map sections and assignments.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(34, 'Manage Tracking', 'tracking.manage', 'tracking', 'Manage GTM, GA4, Meta Pixel, and custom tracking integrations.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(35, 'Manage UTM Builder', 'utm.manage', 'tracking', 'Manage UTM builder and related campaign tracking tools.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(36, 'Manage SEO', 'seo.manage', 'seo', 'Access core SEO dashboard and settings.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(37, 'Manage SEO Meta', 'seo.meta.manage', 'seo', 'Manage page-level SEO fields and overrides.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(38, 'Manage SEO Redirects', 'seo.redirects.manage', 'seo', 'Manage SEO redirects.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(39, 'Manage SEO Sitemap', 'seo.sitemap.manage', 'seo', 'Regenerate and configure sitemap output.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(40, 'Manage Marketing', 'marketing.manage', 'marketing', 'Access marketing dashboards and campaign tools.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(41, 'Manage Landing Pages', 'landing_pages.manage', 'marketing', 'Create and manage marketing landing pages.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(42, 'Manage AI Chatbot', 'chatbot.manage', 'marketing', 'Manage the website AI assistant, knowledge base, and chatbot logs.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(43, 'View Leads', 'leads.view', 'leads', 'View inquiries and leads.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(44, 'Manage Leads', 'leads.edit', 'leads', 'Update lead status and internal notes.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(45, 'Delete Leads', 'leads.delete', 'leads', 'Move leads to trash, restore them, or delete them permanently when allowed.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(46, 'Export Leads', 'leads.export', 'leads', 'Export leads for sales and reporting.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(47, 'View Customers', 'customers.view', 'customers', 'View converted customers and active customer cases.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(48, 'Manage Customers', 'customers.manage', 'customers', 'Convert leads to customers and update customer case data.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(49, 'View Documents', 'documents.view', 'documents', 'View operational documents linked to CRM records.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(50, 'Manage Documents', 'documents.manage', 'documents', 'Upload, download, and delete documents for allowed CRM records.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(51, 'Manage Document Categories', 'documents.categories.manage', 'documents', 'Manage reusable document categories used by the documents module.', '2026-03-25 20:41:08', '2026-03-25 23:07:03'),
(52, 'View Accounting', 'accounting.view', 'accounting', 'View accounting dashboard, customer accounting, expenses, and payroll records.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(53, 'Manage Accounting', 'accounting.manage', 'accounting', 'Manage collections, expenses, payroll entries, and accounting settings.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(54, 'View Accounting Reports', 'accounting.reports.view', 'accounting', 'View detailed accounting and finance reports.', '2026-03-25 20:41:08', '2026-03-25 20:41:08'),
(55, 'View Goals & Commissions', 'goals_commissions.view', 'goals_commissions', 'View seller targets, commission statements, and performance ranking.', '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(56, 'Manage Goals & Commissions', 'goals_commissions.manage', 'goals_commissions', 'Create targets, generate commission statements, and manage performance setup.', '2026-03-25 23:07:03', '2026-03-25 23:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`id`, `permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 53, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(2, 54, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(3, 52, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(4, 4, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(5, 26, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(6, 42, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(7, 48, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(8, 47, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(9, 2, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(10, 25, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(11, 51, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(12, 50, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(13, 49, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(14, 30, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(15, 32, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(16, 31, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(17, 56, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(18, 55, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(19, 1, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(20, 9, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(21, 8, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(22, 7, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(23, 41, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(24, 45, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(25, 44, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(26, 46, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(27, 43, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(28, 33, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(29, 40, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(30, 27, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(31, 28, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(32, 21, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(33, 23, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(34, 22, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(35, 24, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(36, 20, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(37, 16, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(38, 3, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(39, 15, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(40, 18, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(41, 36, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(42, 37, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(43, 38, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(44, 39, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(45, 17, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(46, 29, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(47, 34, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(48, 19, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(49, 11, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(50, 13, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(51, 12, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(52, 14, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(53, 10, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(54, 35, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(55, 6, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(56, 5, 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(57, 53, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(58, 54, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(59, 52, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(60, 4, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(61, 26, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(62, 42, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(63, 48, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(64, 47, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(65, 2, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(66, 25, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(67, 51, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(68, 50, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(69, 49, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(70, 30, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(71, 32, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(72, 31, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(73, 56, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(74, 55, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(75, 1, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(76, 9, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(77, 8, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(78, 7, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(79, 41, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(80, 45, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(81, 44, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(82, 46, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(83, 43, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(84, 33, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(85, 40, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(86, 27, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(87, 28, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(88, 21, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(89, 23, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(90, 22, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(91, 24, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(92, 20, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(93, 3, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(94, 36, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(95, 37, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(96, 38, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(97, 39, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(98, 17, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(99, 29, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(100, 34, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(101, 35, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(102, 6, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(103, 5, 2, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(104, 42, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(105, 2, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(106, 30, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(107, 31, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(108, 7, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(109, 41, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(110, 43, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(111, 40, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(112, 3, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(113, 34, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(114, 35, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(115, 2, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(116, 20, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(117, 3, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(118, 36, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(119, 37, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(120, 38, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(121, 39, 4, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(122, 26, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(123, 2, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(124, 25, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(125, 8, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(126, 7, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(127, 27, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(128, 28, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(129, 21, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(130, 22, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(131, 24, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(132, 20, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(133, 29, 5, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(134, 48, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(135, 47, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(136, 2, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(137, 50, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(138, 49, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(139, 32, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(140, 31, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(141, 55, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(142, 1, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(143, 8, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(144, 7, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(145, 44, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(146, 46, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(147, 43, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(148, 3, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(149, 5, 6, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(150, 2, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(151, 31, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(152, 55, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(153, 7, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(154, 43, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(155, 20, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(156, 3, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(157, 5, 7, '2026-03-25 23:07:03', '2026-03-25 23:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Full system owner access across all dashboard modules and security controls.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(2, 'Admin', 'admin', 'Broad operational access for day-to-day management without full system-owner privileges.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(3, 'Marketing Manager', 'marketing-manager', 'Manage landing pages, campaigns, UTM links, tracking, and marketing performance.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(4, 'SEO Manager', 'seo-manager', 'Manage technical SEO, metadata, redirects, sitemap, schema, and SEO reporting.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(5, 'Content Manager / Editor', 'content-manager', 'Manage content pages, destinations, blog content, testimonials, and menus.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(6, 'Sales / Leads Manager', 'sales-leads-manager', 'Monitor and update leads, submissions, and sales-related follow-up records.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03'),
(7, 'Viewer / Analyst', 'viewer-analyst', 'Read-only access to dashboards, reports, and approved admin areas.', 1, '2026-03-25 23:07:03', '2026-03-25 23:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '2026-03-25 23:07:03', '2026-03-25 23:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `seo_meta_entries`
--

CREATE TABLE `seo_meta_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `target_type` varchar(255) NOT NULL,
  `target_id` bigint(20) UNSIGNED NOT NULL,
  `meta_title_en` varchar(255) DEFAULT NULL,
  `meta_title_ar` varchar(255) DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots_meta` varchar(255) DEFAULT NULL,
  `og_title_en` varchar(255) DEFAULT NULL,
  `og_title_ar` varchar(255) DEFAULT NULL,
  `og_description_en` text DEFAULT NULL,
  `og_description_ar` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `twitter_title_en` varchar(255) DEFAULT NULL,
  `twitter_title_ar` varchar(255) DEFAULT NULL,
  `twitter_description_en` text DEFAULT NULL,
  `twitter_description_ar` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `schema_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `schema_type` varchar(255) DEFAULT NULL,
  `hreflang_en_url` varchar(255) DEFAULT NULL,
  `hreflang_ar_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seo_redirects`
--

CREATE TABLE `seo_redirects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `source_path` varchar(255) NOT NULL,
  `destination_url` varchar(255) NOT NULL,
  `redirect_type` smallint(5) UNSIGNED NOT NULL DEFAULT 301,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `hit_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_hit_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seo_settings`
--

CREATE TABLE `seo_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sitemap_include_pages` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_include_visa_destinations` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_include_destinations` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_include_blog_posts` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_include_marketing_pages` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_include_images` tinyint(1) NOT NULL DEFAULT 1,
  `sitemap_last_generated_at` timestamp NULL DEFAULT NULL,
  `robots_txt_content` longtext DEFAULT NULL,
  `search_console_property` varchar(255) DEFAULT NULL,
  `search_console_notes` text DEFAULT NULL,
  `schema_organization_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `schema_organization_name` varchar(255) DEFAULT NULL,
  `schema_organization_logo` varchar(255) DEFAULT NULL,
  `schema_local_business_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `schema_breadcrumb_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `schema_faq_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `schema_article_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `schema_destination_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `merchant_center_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `merchant_center_verification_code` varchar(255) DEFAULT NULL,
  `merchant_center_notes` text DEFAULT NULL,
  `default_robots_meta` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seo_settings`
--

INSERT INTO `seo_settings` (`id`, `sitemap_include_pages`, `sitemap_include_visa_destinations`, `sitemap_include_destinations`, `sitemap_include_blog_posts`, `sitemap_include_marketing_pages`, `sitemap_include_images`, `sitemap_last_generated_at`, `robots_txt_content`, `search_console_property`, `search_console_notes`, `schema_organization_enabled`, `schema_organization_name`, `schema_organization_logo`, `schema_local_business_enabled`, `schema_breadcrumb_enabled`, `schema_faq_enabled`, `schema_article_enabled`, `schema_destination_enabled`, `merchant_center_enabled`, `merchant_center_verification_code`, `merchant_center_notes`, `default_robots_meta`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, NULL, 'User-agent: *\nAllow: /\n\nSitemap: http://127.0.0.1:8000/sitemap.xml', NULL, NULL, 1, 'Travel Wave', NULL, 0, 1, 1, 1, 1, 0, NULL, NULL, 'index,follow', '2026-03-25 22:02:52', '2026-03-25 22:02:52');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name_en` varchar(255) DEFAULT NULL,
  `site_name_ar` varchar(255) DEFAULT NULL,
  `site_tagline_en` varchar(255) DEFAULT NULL,
  `site_tagline_ar` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `header_logo_path` varchar(255) DEFAULT NULL,
  `footer_logo_path` varchar(255) DEFAULT NULL,
  `footer_logo_width` int(10) UNSIGNED NOT NULL DEFAULT 200,
  `footer_logo_height` int(10) UNSIGNED DEFAULT NULL,
  `footer_logo_keep_aspect_ratio` tinyint(1) NOT NULL DEFAULT 1,
  `footer_logo_display_mode` varchar(20) DEFAULT NULL,
  `logo_width` int(10) UNSIGNED NOT NULL DEFAULT 220,
  `logo_height` int(10) UNSIGNED DEFAULT NULL,
  `logo_keep_aspect_ratio` tinyint(1) NOT NULL DEFAULT 1,
  `mobile_logo_width` int(10) UNSIGNED NOT NULL DEFAULT 168,
  `header_logo_width` int(10) UNSIGNED NOT NULL DEFAULT 220,
  `header_logo_height` int(10) UNSIGNED DEFAULT NULL,
  `header_logo_keep_aspect_ratio` tinyint(1) NOT NULL DEFAULT 1,
  `header_logo_display_mode` varchar(20) DEFAULT NULL,
  `header_mobile_logo_width` int(10) UNSIGNED NOT NULL DEFAULT 168,
  `favicon_path` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `secondary_phone` varchar(255) DEFAULT NULL,
  `whatsapp_number` varchar(255) DEFAULT NULL,
  `address_en` text DEFAULT NULL,
  `address_ar` text DEFAULT NULL,
  `working_hours_en` text DEFAULT NULL,
  `working_hours_ar` text DEFAULT NULL,
  `map_iframe` longtext DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `snapchat_url` varchar(255) DEFAULT NULL,
  `telegram_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `footer_text_en` text DEFAULT NULL,
  `footer_text_ar` text DEFAULT NULL,
  `copyright_text_en` text DEFAULT NULL,
  `copyright_text_ar` text DEFAULT NULL,
  `default_meta_title_en` varchar(255) DEFAULT NULL,
  `default_meta_title_ar` varchar(255) DEFAULT NULL,
  `default_meta_description_en` text DEFAULT NULL,
  `default_meta_description_ar` text DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#12395b',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `accent_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `button_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `button_hover_color` varchar(255) NOT NULL DEFAULT '#ef5c00',
  `link_hover_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `header_background_color` varchar(255) NOT NULL DEFAULT '#12395b',
  `header_text_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `header_link_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `header_hover_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `header_active_link_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `header_button_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `header_button_text_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `header_logo_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `header_is_sticky` tinyint(1) NOT NULL DEFAULT 1,
  `header_vertical_padding` int(10) UNSIGNED NOT NULL DEFAULT 8,
  `header_logo_position_en` varchar(10) NOT NULL DEFAULT 'left',
  `header_logo_position_ar` varchar(10) NOT NULL DEFAULT 'right',
  `header_menu_position_en` varchar(10) NOT NULL DEFAULT 'left',
  `header_menu_position_ar` varchar(10) NOT NULL DEFAULT 'right',
  `footer_background_color` varchar(255) NOT NULL DEFAULT '#0d2438',
  `footer_text_color` varchar(255) NOT NULL DEFAULT '#d9e3ed',
  `footer_link_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `footer_hover_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `footer_heading_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `footer_button_color` varchar(255) NOT NULL DEFAULT '#ff8c32',
  `footer_button_text_color` varchar(255) NOT NULL DEFAULT '#ffffff',
  `footer_vertical_padding` int(10) UNSIGNED NOT NULL DEFAULT 80,
  `footer_quick_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`footer_quick_links`)),
  `home_country_strip_title_en` varchar(255) DEFAULT NULL,
  `home_country_strip_title_ar` varchar(255) DEFAULT NULL,
  `home_country_strip_subtitle_en` varchar(255) DEFAULT NULL,
  `home_country_strip_subtitle_ar` varchar(255) DEFAULT NULL,
  `home_country_strip_autoplay` tinyint(1) NOT NULL DEFAULT 1,
  `home_country_strip_speed` int(10) UNSIGNED NOT NULL DEFAULT 32,
  `home_destinations_autoplay` tinyint(1) NOT NULL DEFAULT 1,
  `home_destinations_interval` int(10) UNSIGNED NOT NULL DEFAULT 3200,
  `home_destinations_speed` int(10) UNSIGNED NOT NULL DEFAULT 500,
  `home_destinations_pause_on_hover` tinyint(1) NOT NULL DEFAULT 1,
  `home_destinations_loop` tinyint(1) NOT NULL DEFAULT 1,
  `global_cta_title_en` varchar(255) DEFAULT NULL,
  `global_cta_title_ar` varchar(255) DEFAULT NULL,
  `global_cta_text_en` text DEFAULT NULL,
  `global_cta_text_ar` text DEFAULT NULL,
  `global_cta_button_en` varchar(255) DEFAULT NULL,
  `global_cta_button_ar` varchar(255) DEFAULT NULL,
  `global_cta_url` varchar(255) DEFAULT NULL,
  `hero_slider_autoplay` tinyint(1) NOT NULL DEFAULT 1,
  `hero_slider_interval` int(10) UNSIGNED NOT NULL DEFAULT 5000,
  `hero_slider_overlay_opacity` decimal(3,2) NOT NULL DEFAULT 0.45,
  `hero_slider_show_dots` tinyint(1) NOT NULL DEFAULT 1,
  `hero_slider_show_arrows` tinyint(1) NOT NULL DEFAULT 1,
  `hero_slider_content_alignment` varchar(255) NOT NULL DEFAULT 'start',
  `hero_slider_layout_mode` varchar(255) NOT NULL DEFAULT 'custom-1408',
  `floating_whatsapp_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `floating_whatsapp_number` varchar(255) DEFAULT NULL,
  `floating_whatsapp_message_en` text DEFAULT NULL,
  `floating_whatsapp_message_ar` text DEFAULT NULL,
  `floating_whatsapp_button_text_en` varchar(255) DEFAULT NULL,
  `floating_whatsapp_button_text_ar` varchar(255) DEFAULT NULL,
  `floating_whatsapp_show_icon` tinyint(1) NOT NULL DEFAULT 1,
  `floating_whatsapp_position` varchar(255) NOT NULL DEFAULT 'bottom_right',
  `floating_whatsapp_animation_style` varchar(255) NOT NULL DEFAULT 'pulse',
  `floating_whatsapp_animation_speed` int(10) UNSIGNED NOT NULL DEFAULT 3200,
  `floating_whatsapp_show_desktop` tinyint(1) NOT NULL DEFAULT 1,
  `floating_whatsapp_show_mobile` tinyint(1) NOT NULL DEFAULT 1,
  `floating_whatsapp_background_color` varchar(255) DEFAULT NULL,
  `floating_whatsapp_visibility_mode` varchar(255) NOT NULL DEFAULT 'all',
  `floating_whatsapp_visibility_targets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`floating_whatsapp_visibility_targets`)),
  `meta_pixel_id` varchar(255) DEFAULT NULL,
  `meta_conversion_api_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `meta_conversion_api_access_token` text DEFAULT NULL,
  `meta_conversion_api_test_event_code` varchar(255) DEFAULT NULL,
  `meta_conversion_api_default_event_source_url` varchar(255) DEFAULT NULL,
  `chatbot_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `chatbot_bot_name_en` text DEFAULT NULL,
  `chatbot_bot_name_ar` text DEFAULT NULL,
  `chatbot_welcome_message_en` text DEFAULT NULL,
  `chatbot_welcome_message_ar` text DEFAULT NULL,
  `chatbot_fallback_message_en` text DEFAULT NULL,
  `chatbot_fallback_message_ar` text DEFAULT NULL,
  `chatbot_primary_language` text DEFAULT NULL,
  `chatbot_suggested_questions_en` longtext DEFAULT NULL,
  `chatbot_suggested_questions_ar` longtext DEFAULT NULL,
  `chatbot_show_whatsapp_handoff` tinyint(1) NOT NULL DEFAULT 1,
  `chatbot_show_contact_handoff` tinyint(1) NOT NULL DEFAULT 1,
  `chatbot_content_sources` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name_en`, `site_name_ar`, `site_tagline_en`, `site_tagline_ar`, `logo_path`, `header_logo_path`, `footer_logo_path`, `footer_logo_width`, `footer_logo_height`, `footer_logo_keep_aspect_ratio`, `footer_logo_display_mode`, `logo_width`, `logo_height`, `logo_keep_aspect_ratio`, `mobile_logo_width`, `header_logo_width`, `header_logo_height`, `header_logo_keep_aspect_ratio`, `header_logo_display_mode`, `header_mobile_logo_width`, `favicon_path`, `contact_email`, `phone`, `secondary_phone`, `whatsapp_number`, `address_en`, `address_ar`, `working_hours_en`, `working_hours_ar`, `map_iframe`, `facebook_url`, `instagram_url`, `twitter_url`, `youtube_url`, `linkedin_url`, `snapchat_url`, `telegram_url`, `tiktok_url`, `footer_text_en`, `footer_text_ar`, `copyright_text_en`, `copyright_text_ar`, `default_meta_title_en`, `default_meta_title_ar`, `default_meta_description_en`, `default_meta_description_ar`, `primary_color`, `secondary_color`, `accent_color`, `button_color`, `button_hover_color`, `link_hover_color`, `header_background_color`, `header_text_color`, `header_link_color`, `header_hover_color`, `header_active_link_color`, `header_button_color`, `header_button_text_color`, `header_logo_enabled`, `header_is_sticky`, `header_vertical_padding`, `header_logo_position_en`, `header_logo_position_ar`, `header_menu_position_en`, `header_menu_position_ar`, `footer_background_color`, `footer_text_color`, `footer_link_color`, `footer_hover_color`, `footer_heading_color`, `footer_button_color`, `footer_button_text_color`, `footer_vertical_padding`, `footer_quick_links`, `home_country_strip_title_en`, `home_country_strip_title_ar`, `home_country_strip_subtitle_en`, `home_country_strip_subtitle_ar`, `home_country_strip_autoplay`, `home_country_strip_speed`, `home_destinations_autoplay`, `home_destinations_interval`, `home_destinations_speed`, `home_destinations_pause_on_hover`, `home_destinations_loop`, `global_cta_title_en`, `global_cta_title_ar`, `global_cta_text_en`, `global_cta_text_ar`, `global_cta_button_en`, `global_cta_button_ar`, `global_cta_url`, `hero_slider_autoplay`, `hero_slider_interval`, `hero_slider_overlay_opacity`, `hero_slider_show_dots`, `hero_slider_show_arrows`, `hero_slider_content_alignment`, `hero_slider_layout_mode`, `floating_whatsapp_enabled`, `floating_whatsapp_number`, `floating_whatsapp_message_en`, `floating_whatsapp_message_ar`, `floating_whatsapp_button_text_en`, `floating_whatsapp_button_text_ar`, `floating_whatsapp_show_icon`, `floating_whatsapp_position`, `floating_whatsapp_animation_style`, `floating_whatsapp_animation_speed`, `floating_whatsapp_show_desktop`, `floating_whatsapp_show_mobile`, `floating_whatsapp_background_color`, `floating_whatsapp_visibility_mode`, `floating_whatsapp_visibility_targets`, `meta_pixel_id`, `meta_conversion_api_enabled`, `meta_conversion_api_access_token`, `meta_conversion_api_test_event_code`, `meta_conversion_api_default_event_source_url`, `chatbot_enabled`, `chatbot_bot_name_en`, `chatbot_bot_name_ar`, `chatbot_welcome_message_en`, `chatbot_welcome_message_ar`, `chatbot_fallback_message_en`, `chatbot_fallback_message_ar`, `chatbot_primary_language`, `chatbot_suggested_questions_en`, `chatbot_suggested_questions_ar`, `chatbot_show_whatsapp_handoff`, `chatbot_show_contact_handoff`, `chatbot_content_sources`, `created_at`, `updated_at`) VALUES
(1, 'Travel Wave', 'ترافل ويف', 'Visas, flights, hotels, and travel planning in one place.', 'التأشيرات والطيران والفنادق وتخطيط الرحلات في مكان واحد.', 'settings/hrjm7Ro5OXBalGjcu7gRUk5RDAq1phPxwnd5J0w6.png', 'settings/hrjm7Ro5OXBalGjcu7gRUk5RDAq1phPxwnd5J0w6.png', 'settings/SQsoiQLdYYvPtrWnKWCDlxaJV3GpVui4vuYBgfa3.png', 150, NULL, 1, 'custom', 220, NULL, 0, 160, 220, NULL, 0, 'original', 160, 'settings/cyBFoWR1Xbhefdk05xxPFszwiQOiPpzeaJcMrmKO.png', 'info@travelwave.com', '201027780053', '201034498782', '201027780053', '45 Al Rashid Street, Ahmed Orabi, Mohandessin, Giza, in front of El Tawfiqia Metro Station.', '45 شارع الرشيد،احمد عرابي،المهندسين،الجيزة،امام محطة مترو التوفيقية', 'Daily from 10:00 AM to 8:00 PM', 'يوميًا من 10 صباحًا حتى 8 مساءً', '<iframe src=\"https://www.google.com/maps?q=Cairo%20Egypt&output=embed\" width=\"100%\" height=\"320\" style=\"border:0;\" loading=\"lazy\"></iframe>', 'https://www.facebook.com/TravelWaveTours', 'https://www.instagram.com/travelwavetours/', NULL, NULL, NULL, NULL, NULL, NULL, 'Travel Wave organizes your trip with clear planning, responsive support, and practical travel solutions.', 'تنظم Travel Wave رحلتك بخطة واضحة ومتابعة سريعة وحلول سفر عملية.', 'Copyright © Travel Wave. All rights reserved.', 'جميع الحقوق محفوظة لشركة Travel Wave.', 'Travel Wave Travel & Tourism', 'Travel Wave للسفر والسياحة', 'Travel Wave provides visas, domestic tourism, outbound trips, flights, hotels, and travel consultation services.', 'تقدم Travel Wave خدمات التأشيرات والسياحة الداخلية والخارجية والطيران والفنادق والاستشارات السياحية.', '#12395b', '#ff8c32', '#ff8c32', '#ff8c32', '#ef5c00', '#ff8c32', '#ffffff', '#050000', '#000000', '#ff8c32', '#ff8c32', '#ff8c32', '#ffffff', 1, 1, 1, 'left', 'right', 'left', 'right', '#0d2438', '#d9e3ed', '#ffffff', '#ff8c32', '#ffffff', '#ff8c32', '#ffffff', 35, '[]', 'Popular Visa Destinations', 'أشهر وجهات التأشيرات', NULL, NULL, 1, 32, 1, 3200, 500, 1, 1, 'Plan your next move with Travel Wave', 'خطط خطوتك القادمة مع Travel Wave', 'Tell us about your trip and we will match you with the right service, timeline, and budget-friendly plan.', 'أخبرنا عن رحلتك وسنساعدك في اختيار الخدمة المناسبة والجدول الزمني وخطة السفر الملائمة لميزانيتك.', 'Start Your Request', 'ابدأ طلبك الآن', '/contact', 1, 5000, 0.48, 1, 1, 'start', 'fullscreen-hero', 1, '201027780053', 'Hello, I want to ask about Travel Wave services', 'مرحبًا، أريد الاستفسار عن خدمات Travel Wave', 'Chat on WhatsApp', 'تواصل واتساب', 1, 'bottom_right', 'pulse', 3200, 1, 1, '#25D366', 'all', '[]', NULL, 0, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'ar', NULL, NULL, 1, 1, '[\"pages\",\"service_pages\",\"visa_countries\",\"destinations\",\"faqs\",\"blog_posts\",\"contact_details\"]', '2026-03-25 22:26:23', '2026-03-27 16:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_role_en` varchar(255) DEFAULT NULL,
  `client_role_ar` varchar(255) DEFAULT NULL,
  `testimonial_en` text NOT NULL,
  `testimonial_ar` text NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `client_name`, `client_role_en`, `client_role_ar`, `testimonial_en`, `testimonial_ar`, `rating`, `image`, `sort_order`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'Ahmed Samir', 'Family Traveler', 'مسافر مع العائلة', 'The team was responsive, organized, and helped us move forward with a much clearer travel plan.', 'كان الفريق سريع الاستجابة ومنظمًا وساعدنا على التحرك بخطة سفر أوضح بكثير.', 5, NULL, 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 'Mona Adel', 'Visa Client', 'عميلة تأشيرات', 'The team was responsive, organized, and helped us move forward with a much clearer travel plan.', 'كان الفريق سريع الاستجابة ومنظمًا وساعدنا على التحرك بخطة سفر أوضح بكثير.', 5, NULL, 2, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(3, 'Youssef Nabil', 'Corporate Traveler', 'مسافر أعمال', 'The team was responsive, organized, and helped us move forward with a much clearer travel plan.', 'كان الفريق سريع الاستجابة ومنظمًا وساعدنا على التحرك بخطة سفر أوضح بكثير.', 5, NULL, 3, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tracking_integrations`
--

CREATE TABLE `tracking_integrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `integration_type` varchar(50) NOT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `tracking_code` varchar(255) DEFAULT NULL,
  `script_code` longtext DEFAULT NULL,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `placement` varchar(50) NOT NULL DEFAULT 'standard',
  `notes` text DEFAULT NULL,
  `visibility_mode` varchar(50) NOT NULL DEFAULT 'all',
  `visibility_targets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`visibility_targets`)),
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tracking_integrations`
--

INSERT INTO `tracking_integrations` (`id`, `name`, `slug`, `integration_type`, `platform`, `tracking_code`, `script_code`, `settings`, `placement`, `notes`, `visibility_mode`, `visibility_targets`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'France Visa Meta Demo', 'france-visa-meta-demo', 'meta_pixel', 'Meta Ads', '123456789012345', NULL, NULL, 'standard', 'Demo Meta Pixel for the France visa campaign landing page.', 'all', NULL, 20, 0, '2026-03-25 23:07:02', '2026-03-25 23:07:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `preferred_language` varchar(5) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `profile_image`, `email_verified_at`, `password`, `is_admin`, `is_active`, `preferred_language`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'A', 'a@example.com', NULL, NULL, NULL, '$2y$10$fHTPGcvI02.u8G6dVIwRVOnsVCueDX4xDFbuac6NHx3OXUFCO4y2C', 1, 1, NULL, NULL, NULL, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(2, 'S', 's@example.com', NULL, NULL, NULL, '$2y$10$yb0fpQ.1N3YAnMCtMSHCT.csAEHtcrcwpeIk.wBHIcZnqwFUtSrvu', 0, 1, NULL, NULL, NULL, '2026-03-25 20:41:09', '2026-03-25 20:41:09'),
(3, 'Travel Wave Admin', 'admin@travelwave.test', NULL, NULL, NULL, '$2y$10$DjxcxmUos0KKXaU8lmtnp.Uk6/ZsT8eg52cHA89MN9GnJMGWtbvCy', 1, 1, 'en', '2026-04-03 13:36:33', NULL, '2026-03-25 23:07:02', '2026-04-03 13:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_permission_overrides`
--

CREATE TABLE `user_permission_overrides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `is_allowed` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utm_campaigns`
--

CREATE TABLE `utm_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `campaign_code` varchar(255) DEFAULT NULL,
  `base_url` varchar(2048) NOT NULL,
  `generated_url` varchar(2048) NOT NULL,
  `campaign_type` varchar(255) DEFAULT NULL,
  `objective` varchar(255) DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_medium` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `utm_id` varchar(255) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `platform` varchar(255) DEFAULT NULL,
  `external_campaign_id` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT NULL,
  `owner_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utm_visits`
--

CREATE TABLE `utm_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `utm_campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_key` varchar(255) DEFAULT NULL,
  `utm_source` varchar(255) DEFAULT NULL,
  `utm_medium` varchar(255) DEFAULT NULL,
  `utm_campaign` varchar(255) DEFAULT NULL,
  `utm_id` varchar(255) DEFAULT NULL,
  `utm_term` varchar(255) DEFAULT NULL,
  `utm_content` varchar(255) DEFAULT NULL,
  `landing_page` varchar(2048) DEFAULT NULL,
  `referrer` varchar(2048) DEFAULT NULL,
  `request_path` varchar(2048) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `visited_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visa_categories`
--

CREATE TABLE `visa_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description_en` text DEFAULT NULL,
  `short_description_ar` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visa_categories`
--

INSERT INTO `visa_categories` (`id`, `name_en`, `name_ar`, `slug`, `short_description_en`, `short_description_ar`, `icon`, `image`, `sort_order`, `is_active`, `is_featured`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 'European Union', 'الاتحاد الأوروبي', 'european-union', 'Europe visa support with clear document preparation and appointment coordination.', 'دعم تأشيرات أوروبا مع تجهيز واضح للملف وتنظيم المواعيد.', 'EU', NULL, 1, 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(2, 'Arab Countries', 'الدول العربية', 'arab-countries', 'Fast guidance for tourism, visit, and business destinations in the region.', 'إرشاد سريع لوجهات السياحة والزيارة والأعمال في المنطقة.', 'AR', NULL, 2, 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(3, 'Asia', 'آسيا', 'asia', 'Popular Asian destinations with travel-ready guidance and planning support.', 'وجهات آسيوية مطلوبة مع إرشاد عملي قبل السفر ودعم للتخطيط.', 'AS', NULL, 3, 1, 1, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL),
(4, 'Other Countries', 'دول أخرى', 'other-countries', 'Dedicated support for standalone destinations like USA, Canada, Georgia, and Armenia.', 'دعم مخصص للوجهات المنفردة مثل أمريكا وكندا وجورجيا وأرمينيا.', 'OT', NULL, 4, 1, 0, '2026-03-25 23:07:02', '2026-03-25 23:07:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visa_countries`
--

CREATE TABLE `visa_countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visa_category_id` bigint(20) UNSIGNED NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt_en` text DEFAULT NULL,
  `excerpt_ar` text DEFAULT NULL,
  `hero_badge_en` varchar(255) DEFAULT NULL,
  `hero_badge_ar` varchar(255) DEFAULT NULL,
  `hero_title_en` varchar(255) DEFAULT NULL,
  `hero_title_ar` varchar(255) DEFAULT NULL,
  `hero_subtitle_en` text DEFAULT NULL,
  `hero_subtitle_ar` text DEFAULT NULL,
  `hero_cta_text_en` varchar(255) DEFAULT NULL,
  `hero_cta_text_ar` varchar(255) DEFAULT NULL,
  `hero_cta_url` varchar(255) DEFAULT NULL,
  `hero_overlay_opacity` decimal(3,2) NOT NULL DEFAULT 0.45,
  `hero_image` varchar(255) DEFAULT NULL,
  `hero_mobile_image` varchar(255) DEFAULT NULL,
  `flag_image` varchar(255) DEFAULT NULL,
  `overview_en` longtext DEFAULT NULL,
  `overview_ar` longtext DEFAULT NULL,
  `visa_type_en` varchar(255) DEFAULT NULL,
  `visa_type_ar` varchar(255) DEFAULT NULL,
  `stay_duration_en` varchar(255) DEFAULT NULL,
  `stay_duration_ar` varchar(255) DEFAULT NULL,
  `quick_summary_destination_label_en` text DEFAULT NULL,
  `quick_summary_destination_label_ar` text DEFAULT NULL,
  `quick_summary_destination_icon` text DEFAULT NULL,
  `quick_summary_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quick_summary_items`)),
  `intro_image` varchar(255) DEFAULT NULL,
  `introduction_title_en` varchar(255) DEFAULT NULL,
  `introduction_title_ar` varchar(255) DEFAULT NULL,
  `introduction_badge_en` varchar(255) DEFAULT NULL,
  `introduction_badge_ar` varchar(255) DEFAULT NULL,
  `introduction_points` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`introduction_points`)),
  `detailed_title_en` varchar(255) DEFAULT NULL,
  `detailed_title_ar` varchar(255) DEFAULT NULL,
  `detailed_description_en` longtext DEFAULT NULL,
  `detailed_description_ar` longtext DEFAULT NULL,
  `best_time_badge_en` varchar(255) DEFAULT NULL,
  `best_time_badge_ar` varchar(255) DEFAULT NULL,
  `best_time_title_en` varchar(255) DEFAULT NULL,
  `best_time_title_ar` varchar(255) DEFAULT NULL,
  `best_time_description_en` text DEFAULT NULL,
  `best_time_description_ar` text DEFAULT NULL,
  `highlights_section_label_en` varchar(255) DEFAULT NULL,
  `highlights_section_label_ar` varchar(255) DEFAULT NULL,
  `highlights_section_title_en` text DEFAULT NULL,
  `highlights_section_title_ar` text DEFAULT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `required_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_documents`)),
  `documents_title_en` varchar(255) DEFAULT NULL,
  `documents_title_ar` varchar(255) DEFAULT NULL,
  `documents_subtitle_en` text DEFAULT NULL,
  `documents_subtitle_ar` text DEFAULT NULL,
  `document_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`document_items`)),
  `application_steps` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`application_steps`)),
  `steps_title_en` varchar(255) DEFAULT NULL,
  `steps_title_ar` varchar(255) DEFAULT NULL,
  `step_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`step_items`)),
  `services` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`services`)),
  `why_choose_title_en` varchar(255) DEFAULT NULL,
  `why_choose_title_ar` varchar(255) DEFAULT NULL,
  `why_choose_intro_en` text DEFAULT NULL,
  `why_choose_intro_ar` text DEFAULT NULL,
  `why_choose_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`why_choose_items`)),
  `processing_time_en` text DEFAULT NULL,
  `processing_time_ar` text DEFAULT NULL,
  `fees_en` longtext DEFAULT NULL,
  `fees_ar` longtext DEFAULT NULL,
  `fees_title_en` varchar(255) DEFAULT NULL,
  `fees_title_ar` varchar(255) DEFAULT NULL,
  `fee_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fee_items`)),
  `fees_notes_en` longtext DEFAULT NULL,
  `fees_notes_ar` longtext DEFAULT NULL,
  `faqs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`faqs`)),
  `faq_title_en` varchar(255) DEFAULT NULL,
  `faq_title_ar` varchar(255) DEFAULT NULL,
  `support_title_en` varchar(255) DEFAULT NULL,
  `support_title_ar` varchar(255) DEFAULT NULL,
  `support_subtitle_en` text DEFAULT NULL,
  `support_subtitle_ar` text DEFAULT NULL,
  `support_button_en` varchar(255) DEFAULT NULL,
  `support_button_ar` varchar(255) DEFAULT NULL,
  `support_button_link` varchar(255) DEFAULT NULL,
  `support_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `map_title_en` varchar(255) DEFAULT NULL,
  `map_title_ar` varchar(255) DEFAULT NULL,
  `map_description_en` text DEFAULT NULL,
  `map_description_ar` text DEFAULT NULL,
  `map_embed_code` longtext DEFAULT NULL,
  `map_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `inquiry_form_title_en` varchar(255) DEFAULT NULL,
  `inquiry_form_title_ar` varchar(255) DEFAULT NULL,
  `inquiry_form_subtitle_en` text DEFAULT NULL,
  `inquiry_form_subtitle_ar` text DEFAULT NULL,
  `inquiry_form_button_en` varchar(255) DEFAULT NULL,
  `inquiry_form_button_ar` varchar(255) DEFAULT NULL,
  `inquiry_form_success_en` text DEFAULT NULL,
  `inquiry_form_success_ar` text DEFAULT NULL,
  `inquiry_form_default_service_type` varchar(255) DEFAULT NULL,
  `inquiry_form_visible_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inquiry_form_visible_fields`)),
  `inquiry_form_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `inquiry_form_label_en` varchar(255) DEFAULT NULL,
  `inquiry_form_label_ar` varchar(255) DEFAULT NULL,
  `cta_title_en` varchar(255) DEFAULT NULL,
  `cta_title_ar` varchar(255) DEFAULT NULL,
  `cta_text_en` text DEFAULT NULL,
  `cta_text_ar` text DEFAULT NULL,
  `cta_button_en` varchar(255) DEFAULT NULL,
  `cta_button_ar` varchar(255) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `final_cta_background_image` varchar(255) DEFAULT NULL,
  `final_cta_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title_en` varchar(255) DEFAULT NULL,
  `meta_title_ar` varchar(255) DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_description_ar` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visa_countries`
--

INSERT INTO `visa_countries` (`id`, `visa_category_id`, `name_en`, `name_ar`, `slug`, `excerpt_en`, `excerpt_ar`, `hero_badge_en`, `hero_badge_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_cta_text_en`, `hero_cta_text_ar`, `hero_cta_url`, `hero_overlay_opacity`, `hero_image`, `hero_mobile_image`, `flag_image`, `overview_en`, `overview_ar`, `visa_type_en`, `visa_type_ar`, `stay_duration_en`, `stay_duration_ar`, `quick_summary_destination_label_en`, `quick_summary_destination_label_ar`, `quick_summary_destination_icon`, `quick_summary_items`, `intro_image`, `introduction_title_en`, `introduction_title_ar`, `introduction_badge_en`, `introduction_badge_ar`, `introduction_points`, `detailed_title_en`, `detailed_title_ar`, `detailed_description_en`, `detailed_description_ar`, `best_time_badge_en`, `best_time_badge_ar`, `best_time_title_en`, `best_time_title_ar`, `best_time_description_en`, `best_time_description_ar`, `highlights_section_label_en`, `highlights_section_label_ar`, `highlights_section_title_en`, `highlights_section_title_ar`, `highlights`, `required_documents`, `documents_title_en`, `documents_title_ar`, `documents_subtitle_en`, `documents_subtitle_ar`, `document_items`, `application_steps`, `steps_title_en`, `steps_title_ar`, `step_items`, `services`, `why_choose_title_en`, `why_choose_title_ar`, `why_choose_intro_en`, `why_choose_intro_ar`, `why_choose_items`, `processing_time_en`, `processing_time_ar`, `fees_en`, `fees_ar`, `fees_title_en`, `fees_title_ar`, `fee_items`, `fees_notes_en`, `fees_notes_ar`, `faqs`, `faq_title_en`, `faq_title_ar`, `support_title_en`, `support_title_ar`, `support_subtitle_en`, `support_subtitle_ar`, `support_button_en`, `support_button_ar`, `support_button_link`, `support_is_active`, `map_title_en`, `map_title_ar`, `map_description_en`, `map_description_ar`, `map_embed_code`, `map_is_active`, `inquiry_form_title_en`, `inquiry_form_title_ar`, `inquiry_form_subtitle_en`, `inquiry_form_subtitle_ar`, `inquiry_form_button_en`, `inquiry_form_button_ar`, `inquiry_form_success_en`, `inquiry_form_success_ar`, `inquiry_form_default_service_type`, `inquiry_form_visible_fields`, `inquiry_form_is_active`, `inquiry_form_label_en`, `inquiry_form_label_ar`, `cta_title_en`, `cta_title_ar`, `cta_text_en`, `cta_text_ar`, `cta_button_en`, `cta_button_ar`, `cta_url`, `final_cta_background_image`, `final_cta_is_active`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `og_image`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(1, 1, 'France', 'فرنسا', 'france-visa', 'Visa type: Short-Stay Schengen Visa. Typical stay allowance: Up to 90 days within 180 days. Expected processing time: Estimated processing is usually around 15 to 30 working days, depending on seasonality, embassy load, and file completeness.. Common required documents include Valid Passport, Recent Personal Photos, and Bank Statement. Travel Wave support includes Professional Document Review and Organized File Preparation.', 'تشمل الخدمة تأشيرة شنغن قصيرة الإقامة. مدة الإقامة المعتادة حتى 90 يوما خلال 180 يوما. المدة المتوقعة للمعالجة تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.. أبرز المستندات المطلوبة: جواز سفر ساري، صور شخصية حديثة، وكشف حساب بنكي. توفر Travel Wave دعماً يشمل مراجعة احترافية للمستندات وتنظيم الملف بشكل واضح.', 'Schengen Visa Support', 'دعم تأشيرة شنغن', 'Apply for a France Visa with Clarity and Confidence', 'استخراج فيزا فرنسا للمصريين بوضوح وثقة', 'With Travel Wave, we help you prepare your France tourist visa application, book your appointment, review your documents, and follow up on your application through a clear and professional process.', 'مع Travel Wave نساعدك في تجهيز ملف تأشيرة فرنسا السياحية، وحجز الموعد، ومراجعة المستندات، ومتابعة الطلب بخطوات واضحة وتنظيم احترافي.', 'Start Your France Visa Request', 'ابدأ طلب تأشيرة فرنسا', '#visa-inquiry', 0.50, 'visa-countries/highlights/ROPFjByXiumOxWhvgMvssXrhWOtaxgpfAFqdFsPx.png', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'visa-countries/france-flag.svg', 'The France visa is one of the most requested Schengen visas for travelers planning tourism, visits, or certain short business activities.\r\nThis page is designed to give you a clear understanding of the requirements for a France visa application, the main steps involved, and how Travel Wave helps you prepare your file and reduce errors before submission.', 'تُعد فيزا فرنسا من أكثر تأشيرات شنغن طلبًا للمسافرين الراغبين في السياحة أو الزيارة أو حضور بعض الأنشطة التجارية القصيرة.\r\nتم تصميم هذه الصفحة لتمنحك فهمًا واضحًا لمتطلبات تأشيرة فرنسا للمصريين، والخطوات الأساسية للتقديم، وكيف تساعدك Travel Wave في تجهيز الملف وتقليل الأخطاء قبل التقديم.', 'Short-Stay Schengen Visa', 'تأشيرة شنغن قصيرة الإقامة', 'Up to 90 days within 180 days', 'حتى 90 يوما خلال 180 يوما', 'Country', 'الدولة', 'fxemoji:franceflag', '[{\"label_en\":\"Visa Type\",\"label_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629\",\"value_en\":\"Short-Stay Schengen\",\"value_ar\":\"\\u0634\\u0646\\u063a\\u0646 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"icon\":\"material-symbols:travel-explore-rounded\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"15 to 30 working days\",\"value_ar\":\"15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"icon\":\"tdesign:time-filled\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Stay Duration\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"value_en\":\"Up to 90 days\",\"value_ar\":\"\\u062d\\u062a\\u0649 90 \\u064a\\u0648\\u0645\\u0627\",\"icon\":\"fluent-mdl2:date-time\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Approx. Fees\",\"label_ar\":\"\\u0627\\u0644\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u062a\\u0642\\u0631\\u064a\\u0628\\u064a\\u0629\",\"value_en\":\"Determined after file review\",\"value_ar\":\"\\u062a\\u062d\\u062f\\u062f \\u0628\\u0639\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"icon\":\"material-symbols:attach-money\",\"sort_order\":4,\"is_active\":true}]', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 'France Visa Overview', 'نظرة عامة على تأشيرة فرنسا', 'Travel Wave Guided Support', 'دعم موجه من Travel Wave', '[{\"text_en\":\"Clear file preparation before submission.\",\"text_ar\":\"\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0648\\u0627\\u0636\\u062d \\u0644\\u0644\\u0645\\u0644\\u0641 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\"},{\"text_en\":\"Practical support for bookings and application timing\",\"text_ar\":\"\\u062f\\u0639\\u0645 \\u0639\\u0645\\u0644\\u064a \\u0644\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0648\\u062a\\u0648\\u0642\\u064a\\u062a \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\"},{\"text_en\":\"Better document organization based on your travel purpose\",\"text_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0623\\u0641\\u0636\\u0644 \\u0644\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0628\\u0645\\u0627 \\u064a\\u0646\\u0627\\u0633\\u0628 \\u0633\\u0628\\u0628 \\u0627\\u0644\\u0633\\u0641\\u0631\"},{\"text_en\":\"A clearer understanding of the next step at every stage\",\"text_ar\":\"\\u0641\\u0647\\u0645 \\u0623\\u0648\\u0636\\u062d \\u0644\\u0644\\u062e\\u0637\\u0648\\u0629 \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629 \\u0628\\u0639\\u062f \\u0643\\u0644 \\u0645\\u0631\\u062d\\u0644\\u0629\"}]', 'Detailed Visa Explanation', 'شرح التأشيرة بالتفصيل', 'The France tourist visa usually falls under the short-stay Schengen visa category and is suitable for travelers visiting for tourism, family visits, or certain short business trips.\r\n\r\nVisa approval depends on having a well-organized file that clearly explains your purpose of travel, financial ability, and the consistency of your bookings and supporting documents with your travel plan. Your application details should also be accurate, and all essential documents should be clear, recent, and ready for review.', 'غالبًا ما تندرج تأشيرة فرنسا السياحية ضمن فئة شنغن قصيرة الإقامة، وهي مناسبة للمسافرين بغرض السياحة أو الزيارة العائلية أو بعض رحلات الأعمال القصيرة.\r\n\r\nيعتمد قبول الطلب على وجود ملف منظم يوضح سبب السفر، والقدرة المالية، وتوافق الحجوزات والمستندات مع خطة الرحلة. كما يجب أن تكون بيانات الطلب دقيقة، وأن تكون جميع الوثائق الأساسية واضحة وحديثة وقابلة للمراجعة.', 'Best time', 'أفضل وقت 0', 'Best time to apply', 'أفضل وقت للتقديم 0', 'The processing time usually ranges between 15 and 30 working days, depending on the season, embassy pressure, and the completeness of the file.', 'تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف. 0', 'Key points', 'أبرز النقاط المهمة', 'Key points', 'أبرز النقاط المهمة', '[{\"title_en\":\"The more structured and\",\"title_ar\":\"\\u0643\\u0644\\u0645\\u0627 \\u0643\\u0627\\u0646\\u062a \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0645\\u0631\\u062a\\u0628\\u0629\",\"description_en\":\"The more structured and consistent your documents are, the easier your application becomes to review.\",\"description_ar\":\"\\u0643\\u0644\\u0645\\u0627 \\u0643\\u0627\\u0646\\u062a \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0645\\u0631\\u062a\\u0628\\u0629 \\u0648\\u0645\\u062a\\u0631\\u0627\\u0628\\u0637\\u0629 \\u0645\\u0639 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631\\u060c \\u0623\\u0635\\u0628\\u062d \\u0627\\u0644\\u0637\\u0644\\u0628 \\u0623\\u0643\\u062b\\u0631 \\u0648\\u0636\\u0648\\u062d\\u064b\\u0627 \\u0639\\u0646\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629.\",\"image\":\"visa-countries\\/highlights\\/kIGUFgWrDz8qmHK1O3eokREfQEf66r2XpyFTdDNm.jpg\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Matching travel dates, hotel\",\"title_ar\":\"\\u062a\\u0637\\u0627\\u0628\\u0642 \\u0645\\u0648\\u0627\\u0639\\u064a\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0627\\u0644\\u0641\\u0646\\u062f\\u0642\",\"description_en\":\"Matching travel dates, hotel reservations, insurance, and supporting documents strengthens your application.\",\"description_ar\":\"\\u062a\\u0637\\u0627\\u0628\\u0642 \\u0645\\u0648\\u0627\\u0639\\u064a\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0648\\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u064a\\u062f\\u0639\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0641\\u0636\\u0644.\",\"image\":\"visa-countries\\/highlights\\/ROPFjByXiumOxWhvgMvssXrhWOtaxgpfAFqdFsPx.png\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"test\",\"title_ar\":\"test\",\"description_en\":\"testtesttesttesttest\",\"description_ar\":\"testtesttesttesttest\",\"image\":\"media-library\\/DExTaYIB43hfIRCdpPiFH37AisJoMIUpfxuHYm3I.png\",\"sort_order\":3,\"is_active\":true}]', '[]', 'Required Documents', 'المستندات المطلوبة', 'The exact file may vary by profile, but these are the most common documents requested for France tourist visa preparation.', 'قد يختلف الملف بحسب حالة المتقدم، لكن هذه هي المستندات الأكثر شيوعا لتجهيز تأشيرة فرنسا السياحية.', '[{\"name_en\":\"Valid Passport\",\"name_ar\":\"\\u062c\\u0648\\u0627\\u0632 \\u0633\\u0641\\u0631 \\u0633\\u0627\\u0631\\u064a\",\"description_en\":\"Passport should cover the required validity period and include usable pages.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u064a\\u063a\\u0637\\u064a \\u062c\\u0648\\u0627\\u0632 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0645\\u062f\\u0629 \\u0627\\u0644\\u0635\\u0644\\u0627\\u062d\\u064a\\u0629 \\u0627\\u0644\\u0645\\u0637\\u0644\\u0648\\u0628\\u0629 \\u0648\\u0623\\u0646 \\u064a\\u062d\\u062a\\u0648\\u064a \\u0639\\u0644\\u0649 \\u0635\\u0641\\u062d\\u0627\\u062a \\u0645\\u062a\\u0627\\u062d\\u0629.\",\"sort_order\":1,\"is_active\":true},{\"name_en\":\"Recent Personal Photos\",\"name_ar\":\"\\u0635\\u0648\\u0631 \\u0634\\u062e\\u0635\\u064a\\u0629 \\u062d\\u062f\\u064a\\u062b\\u0629\",\"description_en\":\"Photos should match embassy size and background requirements.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u0637\\u0627\\u0628\\u0642 \\u0627\\u0644\\u0635\\u0648\\u0631 \\u0645\\u0642\\u0627\\u0633\\u0627\\u062a \\u0648\\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0627\\u0631\\u0629.\",\"sort_order\":2,\"is_active\":true},{\"name_en\":\"Bank Statement\",\"name_ar\":\"\\u0643\\u0634\\u0641 \\u062d\\u0633\\u0627\\u0628 \\u0628\\u0646\\u0643\\u064a\",\"description_en\":\"Financial movement should support the proposed trip timing and cost level.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u064a\\u062f\\u0639\\u0645 \\u0643\\u0634\\u0641 \\u0627\\u0644\\u062d\\u0633\\u0627\\u0628 \\u062a\\u0648\\u0642\\u064a\\u062a \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u062a\\u0643\\u0644\\u0641\\u0629 \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d.\",\"sort_order\":3,\"is_active\":true},{\"name_en\":\"Employment or Study Proof\",\"name_ar\":\"\\u0625\\u062b\\u0628\\u0627\\u062a \\u0639\\u0645\\u0644 \\u0623\\u0648 \\u062f\\u0631\\u0627\\u0633\\u0629\",\"description_en\":\"An employment letter or equivalent proof strengthens the purpose and return intention.\",\"description_ar\":\"\\u062e\\u0637\\u0627\\u0628 \\u0627\\u0644\\u0639\\u0645\\u0644 \\u0623\\u0648 \\u0645\\u0627 \\u064a\\u0639\\u0627\\u062f\\u0644\\u0647 \\u064a\\u062f\\u0639\\u0645 \\u0633\\u0628\\u0628 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0646\\u064a\\u0629 \\u0627\\u0644\\u0639\\u0648\\u062f\\u0629.\",\"sort_order\":4,\"is_active\":true},{\"name_en\":\"Hotel and Flight Reservations\",\"name_ar\":\"\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646\",\"description_en\":\"Reservation dates should match the travel plan and visa request window.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u062a\\u0648\\u0627\\u0641\\u0642 \\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u062d\\u062c\\u0632 \\u0645\\u0639 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0637\\u0644\\u0628.\",\"sort_order\":5,\"is_active\":true},{\"name_en\":\"Travel Insurance\",\"name_ar\":\"\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0627\\u0644\\u0633\\u0641\\u0631\",\"description_en\":\"Insurance should meet Schengen coverage requirements for the full stay period.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u064a\\u062d\\u0642\\u0642 \\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a \\u0634\\u0646\\u063a\\u0646 \\u0637\\u0648\\u0627\\u0644 \\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629.\",\"sort_order\":6,\"is_active\":true}]', '[]', 'Application Steps', 'خطوات التقديم', '[{\"title_en\":\"Submit Your Details\",\"title_ar\":\"\\u0623\\u0631\\u0633\\u0644 \\u0628\\u064a\\u0627\\u0646\\u0627\\u062a\\u0643\",\"description_en\":\"Share your travel purpose, timing, and basic profile so we can assess the file direction.\",\"description_ar\":\"\\u0634\\u0627\\u0631\\u0643 \\u0633\\u0628\\u0628 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u062a\\u0648\\u0642\\u064a\\u062a\\u0647 \\u0648\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a\\u0643 \\u0627\\u0644\\u0623\\u0633\\u0627\\u0633\\u064a\\u0629 \\u0644\\u062a\\u062d\\u062f\\u064a\\u062f \\u0627\\u062a\\u062c\\u0627\\u0647 \\u0627\\u0644\\u0645\\u0644\\u0641.\",\"sort_order\":1,\"step_number\":1,\"is_active\":true},{\"title_en\":\"Review the File\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"description_en\":\"Travel Wave reviews what is available and points out what still needs improvement.\",\"description_ar\":\"\\u062a\\u0631\\u0627\\u062c\\u0639 Travel Wave \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0627\\u0644\\u062d\\u0627\\u0644\\u064a \\u0648\\u062a\\u0648\\u0636\\u062d \\u0645\\u0627 \\u064a\\u062d\\u062a\\u0627\\u062c \\u0625\\u0644\\u0649 \\u0627\\u0633\\u062a\\u0643\\u0645\\u0627\\u0644 \\u0623\\u0648 \\u062a\\u062d\\u0633\\u064a\\u0646.\",\"sort_order\":2,\"step_number\":2,\"is_active\":true},{\"title_en\":\"Prepare Documents\",\"title_ar\":\"\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"Supporting papers are completed and aligned with the travel purpose and booking dates.\",\"description_ar\":\"\\u064a\\u062a\\u0645 \\u0627\\u0633\\u062a\\u0643\\u0645\\u0627\\u0644 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0648\\u0645\\u0637\\u0627\\u0628\\u0642\\u062a\\u0647\\u0627 \\u0645\\u0639 \\u063a\\u0631\\u0636 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a.\",\"sort_order\":3,\"step_number\":3,\"is_active\":true},{\"title_en\":\"Booking and Follow-Up\",\"title_ar\":\"\\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0648\\u0627\\u0644\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629\",\"description_en\":\"Hotel, flight, and insurance details are coordinated before the appointment stage.\",\"description_ar\":\"\\u064a\\u062a\\u0645 \\u062a\\u0646\\u0633\\u064a\\u0642 \\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0642\\u0628\\u0644 \\u0645\\u0631\\u062d\\u0644\\u0629 \\u0627\\u0644\\u0645\\u0648\\u0639\\u062f.\",\"sort_order\":4,\"step_number\":4,\"is_active\":true},{\"title_en\":\"Application Submission\",\"title_ar\":\"\\u0625\\u062a\\u0645\\u0627\\u0645 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\",\"description_en\":\"You attend the submission and biometric step, then continue tracking the request.\",\"description_ar\":\"\\u062a\\u0642\\u0648\\u0645 \\u0628\\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0648\\u0627\\u0644\\u0628\\u0635\\u0645\\u0629 \\u062b\\u0645 \\u062a\\u062a\\u0627\\u0628\\u0639 \\u062d\\u0627\\u0644\\u0629 \\u0627\\u0644\\u0637\\u0644\\u0628 \\u0628\\u0639\\u062f \\u0630\\u0644\\u0643.\",\"sort_order\":5,\"step_number\":5,\"is_active\":true}]', '[]', 'Why Choose Travel Wave', 'لماذا تختار Travel Wave', 'A premium support experience built to make the France visa process clearer, more organized, and easier to follow.', 'تجربة دعم احترافية تجعل مسار تأشيرة فرنسا أوضح وأكثر تنظيما وأسهل في المتابعة.', '[{\"title_en\":\"Professional Document Review\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u062d\\u062a\\u0631\\u0627\\u0641\\u064a\\u0629 \\u0644\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"We check the file structure and highlight missing items before the appointment date.\",\"description_ar\":\"\\u0646\\u0631\\u0627\\u062c\\u0639 \\u062a\\u0631\\u062a\\u064a\\u0628 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0646\\u0648\\u0636\\u062d \\u0627\\u0644\\u0646\\u0648\\u0627\\u0642\\u0635 \\u0642\\u0628\\u0644 \\u0645\\u0648\\u0639\\u062f \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"icon\":\"shield\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Organized File Preparation\",\"title_ar\":\"\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0628\\u0634\\u0643\\u0644 \\u0648\\u0627\\u0636\\u062d\",\"description_en\":\"Your supporting papers are arranged in a cleaner order that is easier to understand and present.\",\"description_ar\":\"\\u064a\\u062a\\u0645 \\u062a\\u0631\\u062a\\u064a\\u0628 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0648\\u0636\\u062d \\u0648\\u0623\\u0633\\u0647\\u0644 \\u0644\\u0644\\u0641\\u0647\\u0645 \\u0648\\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"icon\":\"file\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Booking Support\",\"title_ar\":\"\\u062f\\u0639\\u0645 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a\",\"description_en\":\"We help align hotel, flight, and insurance details with the intended travel plan.\",\"description_ar\":\"\\u0646\\u0633\\u0627\\u0639\\u062f \\u0641\\u064a \\u062a\\u0646\\u0633\\u064a\\u0642 \\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0645\\u0639 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631.\",\"icon\":\"calendar\",\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Step-by-Step Follow-Up\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u062e\\u0637\\u0648\\u0629 \\u0628\\u062e\\u0637\\u0648\\u0629\",\"description_en\":\"Applicants know what comes next at every stage instead of guessing the process.\",\"description_ar\":\"\\u064a\\u0639\\u0631\\u0641 \\u0627\\u0644\\u0645\\u062a\\u0642\\u062f\\u0645 \\u0645\\u0627 \\u0647\\u064a \\u0627\\u0644\\u062e\\u0637\\u0648\\u0629 \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629 \\u0641\\u064a \\u0643\\u0644 \\u0645\\u0631\\u062d\\u0644\\u0629 \\u062f\\u0648\\u0646 \\u062a\\u062e\\u0645\\u064a\\u0646.\",\"icon\":\"support\",\"sort_order\":4,\"is_active\":true}]', 'Estimated processing is usually around 15 to 30 working days, depending on seasonality, embassy load, and file completeness.', 'تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.', 'Approximate fees depend on embassy, visa center, and service support charges.', 'تعتمد الرسوم التقريبية على رسوم السفارة ومركز التأشيرات ورسوم الخدمة.', 'Fees and Processing Time', 'الرسوم ومدة المعالجة', '[{\"label_en\":\"Embassy Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u0633\\u0641\\u0627\\u0631\\u0629\",\"value_en\":\"Varies by traveler type\",\"value_ar\":\"\\u062a\\u062e\\u062a\\u0644\\u0641 \\u062d\\u0633\\u0628 \\u0646\\u0648\\u0639 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0641\\u0631\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Visa Center Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0645\\u0631\\u0643\\u0632 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0627\\u062a\",\"value_en\":\"Additional service charge\",\"value_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u062e\\u062f\\u0645\\u0629 \\u0625\\u0636\\u0627\\u0641\\u064a\\u0629\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Travel Wave Service Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u062e\\u062f\\u0645\\u0629 Travel Wave\",\"value_en\":\"Quoted after file review\",\"value_ar\":\"\\u064a\\u062a\\u0645 \\u062a\\u062d\\u062f\\u064a\\u062f\\u0647\\u0627 \\u0628\\u0639\\u062f \\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"Usually 15 to 30 working days\",\"value_ar\":\"\\u0639\\u0627\\u062f\\u0629 \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"sort_order\":4,\"is_active\":true}]', 'Final pricing may change according to embassy updates, traveler age, or additional service needs. Travel Wave confirms the latest estimate before submission.', 'قد تختلف التكلفة النهائية بحسب تحديثات السفارة أو عمر المسافر أو الخدمات الإضافية. تؤكد Travel Wave التقدير الأحدث قبل التقديم.', '[{\"question_en\":\"Is France visa considered a Schengen visa?\",\"question_ar\":\"\\u0647\\u0644 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627 \\u062a\\u0639\\u062a\\u0628\\u0631 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0634\\u0646\\u063a\\u0646\\u061f\",\"answer_en\":\"Yes. In most travel cases, the France short-stay visa is processed under Schengen rules.\",\"answer_ar\":\"\\u0646\\u0639\\u0645. \\u0641\\u064a \\u0623\\u063a\\u0644\\u0628 \\u062d\\u0627\\u0644\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0631 \\u062a\\u062a\\u0645 \\u0645\\u0639\\u0627\\u0644\\u062c\\u0629 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u0636\\u0645\\u0646 \\u0646\\u0638\\u0627\\u0645 \\u0634\\u0646\\u063a\\u0646.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"How long does processing usually take?\",\"question_ar\":\"\\u0643\\u0645 \\u062a\\u0633\\u062a\\u063a\\u0631\\u0642 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629 \\u0639\\u0627\\u062f\\u0629\\u061f\",\"answer_en\":\"It often ranges from 15 to 30 working days, but seasonal pressure can affect timelines.\",\"answer_ar\":\"\\u063a\\u0627\\u0644\\u0628\\u0627 \\u0645\\u0627 \\u062a\\u062a\\u0631\\u0627\\u0648\\u062d \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644 \\u0648\\u0642\\u062f \\u062a\\u062a\\u0623\\u062b\\u0631 \\u0628\\u0627\\u0644\\u0645\\u0648\\u0627\\u0633\\u0645 \\u0648\\u0636\\u063a\\u0637 \\u0627\\u0644\\u0637\\u0644\\u0628\\u0627\\u062a.\",\"sort_order\":2,\"is_active\":true},{\"question_en\":\"Is biometric attendance required?\",\"question_ar\":\"\\u0647\\u0644 \\u0627\\u0644\\u062d\\u0636\\u0648\\u0631 \\u0644\\u0644\\u0628\\u0635\\u0645\\u0629 \\u0645\\u0637\\u0644\\u0648\\u0628\\u061f\",\"answer_en\":\"In many cases yes, depending on prior Schengen biometric history and current requirements.\",\"answer_ar\":\"\\u0641\\u064a \\u0643\\u062b\\u064a\\u0631 \\u0645\\u0646 \\u0627\\u0644\\u062d\\u0627\\u0644\\u0627\\u062a \\u0646\\u0639\\u0645 \\u0628\\u062d\\u0633\\u0628 \\u0633\\u062c\\u0644 \\u0627\\u0644\\u0628\\u0635\\u0645\\u0629 \\u0627\\u0644\\u0633\\u0627\\u0628\\u0642 \\u0648\\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0627\\u0644\\u062d\\u0627\\u0644\\u064a\\u0629.\",\"sort_order\":3,\"is_active\":true},{\"question_en\":\"When should I apply before travel?\",\"question_ar\":\"\\u0645\\u062a\\u0649 \\u064a\\u062c\\u0628 \\u0623\\u0646 \\u0623\\u0628\\u062f\\u0623 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0633\\u0641\\u0631\\u061f\",\"answer_en\":\"Starting early is usually the safer option, especially before busy travel seasons.\",\"answer_ar\":\"\\u064a\\u0641\\u0636\\u0644 \\u0627\\u0644\\u0628\\u062f\\u0621 \\u0645\\u0628\\u0643\\u0631\\u0627 \\u062e\\u0627\\u0635\\u0629 \\u0642\\u0628\\u0644 \\u0645\\u0648\\u0627\\u0633\\u0645 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0632\\u062f\\u062d\\u0645\\u0629.\",\"sort_order\":4,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', 'Need Help Before You Apply?', 'تحتاج إلى مساعدة قبل التقديم؟', 'Talk to Travel Wave and get practical guidance on documents, bookings, and the best next step for your France visa file.', 'تحدث مع Travel Wave واحصل على إرشاد عملي بخصوص المستندات والحجوزات وأفضل خطوة تالية لملف تأشيرة فرنسا.', 'Speak to an Advisor', 'تحدث مع مستشار', '#visa-inquiry', 1, 'Office and Visa Support Location', 'موقع المكتب ودعم التأشيرات', 'Use the map section to display your office, embassy, or visa center reference point for applicants.', 'يمكن استخدام هذا القسم لعرض موقع المكتب أو السفارة أو مركز التأشيرات كمرجع للمتقدمين.', '<iframe src=\"https://www.google.com/maps?q=Paris%20France&output=embed\" width=\"100%\" height=\"420\" style=\"border:0;\" loading=\"lazy\"></iframe>', 1, 'Talk to Travel Wave About Your France Visa', 'تواصل مع Travel Wave بخصوص تأشيرة فرنسا', 'Send your details and our team will guide you on eligibility, documents, and the next practical step.', 'أرسل بياناتك وسيرشدك فريقنا بخصوص الأهلية والمستندات والخطوة العملية التالية.', 'Send France Visa Inquiry', 'أرسل استفسار تأشيرة فرنسا', 'Your France visa inquiry has been received. A Travel Wave advisor will contact you shortly.', 'تم استلام استفسارك الخاص بتأشيرة فرنسا وسيتواصل معك أحد مستشاري Travel Wave قريبا.', 'France Visa', '[\"full_name\",\"phone\",\"whatsapp_number\",\"email\",\"service_type\",\"destination\",\"travel_date\",\"message\"]', 1, 'Contact Us', 'تواصل معنا', 'Ready to Start Your France Visa File?', 'جاهز لبدء ملف تأشيرة فرنسا؟', 'Let Travel Wave turn a complex visa process into a more organized, readable, and confidence-building journey.', 'دع Travel Wave تحول خطوات التأشيرة المعقدة إلى رحلة أكثر تنظيما ووضوحا وثقة.', 'Apply with Travel Wave', 'قدّم مع Travel Wave', '#visa-inquiry', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 1, 'France Visa Services | Travel Wave', 'خدمات تأشيرة فرنسا | Travel Wave', 'Explore Travel Wave France visa support, required documents, steps, fees, FAQs, and inquiry options in a premium reusable visa template.', 'اكتشف خدمات Travel Wave لتأشيرة فرنسا والمستندات والخطوات والرسوم والأسئلة الشائعة ونموذج الاستفسار في قالب تأشيرات قابل لإعادة الاستخدام.', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 1, 1, 1, '2026-03-25 23:07:02', '2026-03-27 23:50:25', NULL, NULL),
(2, 1, 'Germany', 'ألمانيا', 'germany-visa', 'Germany visa is commonly requested for tourism, family visits, and selected business travel under the short-stay Schengen category.', 'تأشيرة ألمانيا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.', 'Schengen Visa Support', 'دعم تأشيرة شنغن', 'Germany Visa Support with Better Organization', 'دعم تأشيرة ألمانيا بملف أكثر تنظيمًا ووضوحًا', 'Travel Wave helps applicants prepare the file, align the bookings, and understand the process before submission.', 'تساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.', 'Start Germany Visa Request', 'ابدأ طلب تأشيرة ألمانيا', '#destination-form', 0.45, 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'visa-countries/germany-flag.svg', 'Germany remains one of the popular Schengen destinations and usually benefits from a clear, well-structured file before the appointment stage.', 'تعد ألمانيا من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.', 'Short-Stay Schengen Visa', 'تأشيرة شنغن قصيرة الإقامة', 'Up to 90 days within 180 days', 'حتى 90 يومًا خلال 180 يومًا', NULL, NULL, NULL, '[{\"title_en\":\"Visa Type\",\"title_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629\",\"value_en\":\"Short-Stay Schengen\",\"value_ar\":\"\\u0634\\u0646\\u063a\\u0646 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"icon\":\"VS\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Processing Time\",\"title_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"15 to 30 working days\",\"value_ar\":\"15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"icon\":\"PT\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Stay Duration\",\"title_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"value_en\":\"Up to 90 days\",\"value_ar\":\"\\u062d\\u062a\\u0649 90 \\u064a\\u0648\\u0645\\u064b\\u0627\",\"icon\":\"SD\",\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Approx. Fees\",\"title_ar\":\"\\u0627\\u0644\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u062a\\u0642\\u0631\\u064a\\u0628\\u064a\\u0629\",\"value_en\":\"Quoted after review\",\"value_ar\":\"\\u062a\\u062d\\u062f\\u062f \\u0628\\u0639\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629\",\"icon\":\"FE\",\"sort_order\":4,\"is_active\":true}]', NULL, 'Germany Visa Overview', 'نظرة عامة على تأشيرة ألمانيا', 'Travel Wave Support', 'دعم Travel Wave', '[{\"text_en\":\"Suitable for tourism, family visits, and selected business travel.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0633\\u064a\\u0627\\u062d\\u0629 \\u0648\\u0627\\u0644\\u0632\\u064a\\u0627\\u0631\\u0627\\u062a \\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u064a\\u0629 \\u0648\\u0628\\u0639\\u0636 \\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0623\\u0639\\u0645\\u0627\\u0644.\"},{\"text_en\":\"A better organized file usually creates a clearer submission path.\",\"text_ar\":\"\\u064a\\u0633\\u0627\\u0639\\u062f \\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0639\\u0644\\u0649 \\u062c\\u0639\\u0644 \\u0645\\u0633\\u0627\\u0631 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0623\\u0648\\u0636\\u062d.\"},{\"text_en\":\"Travel Wave clarifies the next step before each stage.\",\"text_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f\\u0643 Travel Wave \\u0641\\u064a \\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u062a\\u0646\\u0627\\u0633\\u0642 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0648\\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0648\\u0634\\u0631\\u062d \\u0645\\u0627 \\u064a\\u0644\\u0632\\u0645 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0645\\u0648\\u0639\\u062f.\"}]', 'Detailed Visa Explanation', 'شرح التأشيرة بالتفصيل', 'Short-stay Schengen files usually need consistency between the stated purpose of travel, the financial documents, and the booking evidence.\n\nTravel Wave helps applicants reduce confusion by organizing the file in a more readable and practical way.', 'تحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"text_en\":\"Suitable for tourism and family visits.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0633\\u064a\\u0627\\u062d\\u0629 \\u0648\\u0627\\u0644\\u0632\\u064a\\u0627\\u0631\\u0627\\u062a \\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u064a\\u0629.\"},{\"text_en\":\"Early preparation can help in busy seasons.\",\"text_ar\":\"\\u064a\\u0641\\u064a\\u062f \\u0627\\u0644\\u062a\\u062d\\u0636\\u064a\\u0631 \\u0627\\u0644\\u0645\\u0628\\u0643\\u0631 \\u0641\\u064a \\u0627\\u0644\\u0645\\u0648\\u0627\\u0633\\u0645 \\u0627\\u0644\\u0645\\u0632\\u062f\\u062d\\u0645\\u0629.\"}]', NULL, 'Required Documents', 'المستندات المطلوبة', 'The exact list may vary by profile, but these are common supporting items.', 'قد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.', '[{\"name_en\":\"Valid Passport\",\"name_ar\":\"\\u062c\\u0648\\u0627\\u0632 \\u0633\\u0641\\u0631 \\u0633\\u0627\\u0631\\u064a\",\"description_en\":\"Passport validity and blank pages should be suitable for the request.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u0643\\u0648\\u0646 \\u0645\\u062f\\u0629 \\u0627\\u0644\\u0635\\u0644\\u0627\\u062d\\u064a\\u0629 \\u0648\\u0627\\u0644\\u0635\\u0641\\u062d\\u0627\\u062a \\u0627\\u0644\\u0645\\u062a\\u0627\\u062d\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0637\\u0644\\u0628.\",\"sort_order\":1,\"is_active\":true},{\"name_en\":\"Bank Statement\",\"name_ar\":\"\\u0643\\u0634\\u0641 \\u062d\\u0633\\u0627\\u0628 \\u0628\\u0646\\u0643\\u064a\",\"description_en\":\"Financial movement should support the travel plan.\",\"description_ar\":\"\\u064a\\u0646\\u0628\\u063a\\u064a \\u0623\\u0646 \\u064a\\u062f\\u0639\\u0645 \\u0627\\u0644\\u062d\\u0631\\u0643\\u0629 \\u0627\\u0644\\u0645\\u0627\\u0644\\u064a\\u0629 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629.\",\"sort_order\":2,\"is_active\":true},{\"name_en\":\"Employment or Study Proof\",\"name_ar\":\"\\u0625\\u062b\\u0628\\u0627\\u062a \\u0639\\u0645\\u0644 \\u0623\\u0648 \\u062f\\u0631\\u0627\\u0633\\u0629\",\"description_en\":\"Supporting the declared profile and return intention.\",\"description_ar\":\"\\u0644\\u062f\\u0639\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0627\\u0644\\u0627\\u0631\\u062a\\u0628\\u0627\\u0637\\u0627\\u062a \\u0628\\u0639\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631.\",\"sort_order\":3,\"is_active\":true},{\"name_en\":\"Hotel, Flight, and Insurance\",\"name_ar\":\"\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646\",\"description_en\":\"Dates should stay aligned across the itinerary.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u0643\\u0648\\u0646 \\u0627\\u0644\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0645\\u062a\\u0633\\u0642\\u0629 \\u0641\\u064a \\u0643\\u0627\\u0645\\u0644 \\u062e\\u0637 \\u0627\\u0644\\u0633\\u064a\\u0631.\",\"sort_order\":4,\"is_active\":true}]', NULL, 'Application Steps', 'خطوات التقديم', '[{\"title_en\":\"Send the basic details\",\"title_ar\":\"\\u0623\\u0631\\u0633\\u0644 \\u0627\\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u0623\\u0633\\u0627\\u0633\\u064a\\u0629\",\"description_en\":\"Purpose, timing, and profile overview.\",\"description_ar\":\"\\u0627\\u0644\\u063a\\u0631\\u0636 \\u0645\\u0646 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0627\\u0644\\u062a\\u0648\\u0642\\u064a\\u062a \\u0648\\u0646\\u0628\\u0630\\u0629 \\u0639\\u0646 \\u0627\\u0644\\u0645\\u0644\\u0641.\",\"step_number\":1,\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Review the file\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"description_en\":\"Clarify missing points and organize priorities.\",\"description_ar\":\"\\u062a\\u0648\\u0636\\u064a\\u062d \\u0627\\u0644\\u0646\\u0648\\u0627\\u0642\\u0635 \\u0648\\u062a\\u0631\\u062a\\u064a\\u0628 \\u0627\\u0644\\u0623\\u0648\\u0644\\u0648\\u064a\\u0627\\u062a.\",\"step_number\":2,\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Prepare the documents\",\"title_ar\":\"\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"Organize the file and supporting bookings.\",\"description_ar\":\"\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0627\\u0644\\u062f\\u0627\\u0639\\u0645\\u0629.\",\"step_number\":3,\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Submit and follow up\",\"title_ar\":\"\\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0648\\u0627\\u0644\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629\",\"description_en\":\"Complete the submission and continue request tracking.\",\"description_ar\":\"\\u0625\\u062a\\u0645\\u0627\\u0645 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u062b\\u0645 \\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u062d\\u0627\\u0644\\u0629 \\u0627\\u0644\\u0637\\u0644\\u0628.\",\"step_number\":4,\"sort_order\":4,\"is_active\":true}]', NULL, 'Why Choose Travel Wave', 'لماذا تختار Travel Wave', 'We help applicants move through the process with more clarity and fewer last-minute surprises.', 'نساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.', '[{\"title_en\":\"Document Review\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"Checking the consistency of the file before submission.\",\"description_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u062a\\u0633\\u0627\\u0642 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"icon\":\"shield\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Booking Alignment\",\"title_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a\",\"description_en\":\"Aligning hotel, flight, and insurance timing.\",\"description_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u062a\\u0648\\u0642\\u064a\\u062a\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646.\",\"icon\":\"calendar\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Step Follow-Up\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a\",\"description_en\":\"Clarifying the next practical step after each stage.\",\"description_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f\\u0643 Travel Wave \\u0641\\u064a \\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u062a\\u0646\\u0627\\u0633\\u0642 \\u0628\\u064a\\u0646 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0648\\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0648\\u0634\\u0631\\u062d \\u0645\\u0627 \\u064a\\u0644\\u0632\\u0645 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0645\\u0648\\u0639\\u062f.\",\"icon\":\"support\",\"sort_order\":3,\"is_active\":true}]', NULL, NULL, NULL, NULL, 'Fees and Processing Time', 'الرسوم ومدة المعالجة', '[{\"label_en\":\"Embassy Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u0633\\u0641\\u0627\\u0631\\u0629\",\"value_en\":\"Varies by profile\",\"value_ar\":\"\\u062a\\u062e\\u062a\\u0644\\u0641 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Service Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u062e\\u062f\\u0645\\u0629\",\"value_en\":\"Quoted after review\",\"value_ar\":\"\\u062a\\u062d\\u062f\\u062f \\u0628\\u0639\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"Usually 15 to 30 working days\",\"value_ar\":\"\\u0639\\u0627\\u062f\\u0629 \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"sort_order\":3,\"is_active\":true}]', 'Final timing depends on seasonality and file readiness.', 'تعتمد المدة النهائية على الموسم ومدى جاهزية الملف.', '[{\"question_en\":\"Is this a Schengen visa?\",\"question_ar\":\"\\u0647\\u0644 \\u0647\\u0630\\u0647 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0634\\u0646\\u063a\\u0646\\u061f\",\"answer_en\":\"Yes, in most short-stay travel cases the request falls under Schengen rules.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u0641\\u064a \\u0623\\u063a\\u0644\\u0628 \\u062d\\u0627\\u0644\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631 \\u064a\\u0646\\u062f\\u0631\\u062c \\u0627\\u0644\\u0637\\u0644\\u0628 \\u0636\\u0645\\u0646 \\u0646\\u0638\\u0627\\u0645 \\u0634\\u0646\\u063a\\u0646.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"When should I start?\",\"question_ar\":\"\\u0645\\u062a\\u0649 \\u0623\\u0628\\u062f\\u0623\\u061f\",\"answer_en\":\"Early preparation is usually better, especially before high-demand travel periods.\",\"answer_ar\":\"\\u064a\\u0641\\u0636\\u0644 \\u0627\\u0644\\u0628\\u062f\\u0621 \\u0645\\u0628\\u0643\\u0631\\u064b\\u0627\\u060c \\u062e\\u0635\\u0648\\u0635\\u064b\\u0627 \\u0642\\u0628\\u0644 \\u0641\\u062a\\u0631\\u0627\\u062a \\u0627\\u0644\\u0636\\u063a\\u0637 \\u0627\\u0644\\u0645\\u0631\\u062a\\u0641\\u0639 \\u0639\\u0644\\u0649 \\u0627\\u0644\\u0645\\u0648\\u0627\\u0639\\u064a\\u062f.\",\"sort_order\":2,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, 'Ask About Germany Visa', 'استفسر عن تأشيرة ألمانيا', 'Send your details and Travel Wave will guide you on the next practical step.', 'أرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.', 'Send Inquiry', 'أرسل الاستفسار', NULL, NULL, 'Germany Visa', '[\"email\",\"travel_date\",\"message\"]', 1, NULL, NULL, 'Ready to Start Germany Visa Preparation?', 'جاهز لبدء تجهيز تأشيرة ألمانيا؟', 'Travel Wave helps make the request more organized and easier to follow.', 'تساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.', 'Start Now', 'ابدأ الآن', '#destination-form', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, '2026-03-25 23:07:02', '2026-03-28 00:21:22', NULL, NULL);
INSERT INTO `visa_countries` (`id`, `visa_category_id`, `name_en`, `name_ar`, `slug`, `excerpt_en`, `excerpt_ar`, `hero_badge_en`, `hero_badge_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_cta_text_en`, `hero_cta_text_ar`, `hero_cta_url`, `hero_overlay_opacity`, `hero_image`, `hero_mobile_image`, `flag_image`, `overview_en`, `overview_ar`, `visa_type_en`, `visa_type_ar`, `stay_duration_en`, `stay_duration_ar`, `quick_summary_destination_label_en`, `quick_summary_destination_label_ar`, `quick_summary_destination_icon`, `quick_summary_items`, `intro_image`, `introduction_title_en`, `introduction_title_ar`, `introduction_badge_en`, `introduction_badge_ar`, `introduction_points`, `detailed_title_en`, `detailed_title_ar`, `detailed_description_en`, `detailed_description_ar`, `best_time_badge_en`, `best_time_badge_ar`, `best_time_title_en`, `best_time_title_ar`, `best_time_description_en`, `best_time_description_ar`, `highlights_section_label_en`, `highlights_section_label_ar`, `highlights_section_title_en`, `highlights_section_title_ar`, `highlights`, `required_documents`, `documents_title_en`, `documents_title_ar`, `documents_subtitle_en`, `documents_subtitle_ar`, `document_items`, `application_steps`, `steps_title_en`, `steps_title_ar`, `step_items`, `services`, `why_choose_title_en`, `why_choose_title_ar`, `why_choose_intro_en`, `why_choose_intro_ar`, `why_choose_items`, `processing_time_en`, `processing_time_ar`, `fees_en`, `fees_ar`, `fees_title_en`, `fees_title_ar`, `fee_items`, `fees_notes_en`, `fees_notes_ar`, `faqs`, `faq_title_en`, `faq_title_ar`, `support_title_en`, `support_title_ar`, `support_subtitle_en`, `support_subtitle_ar`, `support_button_en`, `support_button_ar`, `support_button_link`, `support_is_active`, `map_title_en`, `map_title_ar`, `map_description_en`, `map_description_ar`, `map_embed_code`, `map_is_active`, `inquiry_form_title_en`, `inquiry_form_title_ar`, `inquiry_form_subtitle_en`, `inquiry_form_subtitle_ar`, `inquiry_form_button_en`, `inquiry_form_button_ar`, `inquiry_form_success_en`, `inquiry_form_success_ar`, `inquiry_form_default_service_type`, `inquiry_form_visible_fields`, `inquiry_form_is_active`, `inquiry_form_label_en`, `inquiry_form_label_ar`, `cta_title_en`, `cta_title_ar`, `cta_text_en`, `cta_text_ar`, `cta_button_en`, `cta_button_ar`, `cta_url`, `final_cta_background_image`, `final_cta_is_active`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `og_image`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(3, 1, 'Italy', 'إيطاليا', 'italy-visa', 'Italy visa is commonly requested for tourism, family visits, and selected business travel under the short-stay Schengen category.', 'تأشيرة إيطاليا من التأشيرات المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال ضمن فئة شنغن قصيرة الإقامة.', 'Schengen Visa Support', 'دعم تأشيرة شنغن', 'Italy Visa Support with Better Organization', 'دعم تأشيرة إيطاليا بملف أكثر تنظيمًا ووضوحًا', 'Travel Wave helps applicants prepare the file, align the bookings, and understand the process before submission.', 'تساعدك Travel Wave على تجهيز الملف وتنسيق الحجوزات وفهم خطوات التقديم قبل التسليم.', 'Start Italy Visa Request', 'ابدأ طلب تأشيرة إيطاليا', '#destination-form', 0.45, 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'visa-countries/italy-flag.svg', 'Italy remains one of the popular Schengen destinations and usually benefits from a clear, well-structured file before the appointment stage.', 'تعد إيطاليا من الوجهات الشائعة ضمن شنغن، ويستفيد طلبها عادة من ملف واضح ومنظم قبل مرحلة الموعد.', 'Short-Stay Schengen Visa', 'تأشيرة شنغن قصيرة الإقامة', 'Up to 90 days within 180 days', 'حتى 90 يومًا خلال 180 يومًا', NULL, NULL, NULL, '[{\"title_en\":\"Visa Type\",\"title_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629\",\"value_en\":\"Short-Stay Schengen\",\"value_ar\":\"\\u0634\\u0646\\u063a\\u0646 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"icon\":\"VS\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Processing Time\",\"title_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"15 to 30 working days\",\"value_ar\":\"15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"icon\":\"PT\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Stay Duration\",\"title_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"value_en\":\"Up to 90 days\",\"value_ar\":\"\\u062d\\u062a\\u0649 90 \\u064a\\u0648\\u0645\\u064b\\u0627\",\"icon\":\"SD\",\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Approx. Fees\",\"title_ar\":\"\\u0627\\u0644\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u062a\\u0642\\u0631\\u064a\\u0628\\u064a\\u0629\",\"value_en\":\"Quoted after review\",\"value_ar\":\"\\u062a\\u062d\\u062f\\u062f \\u0628\\u0639\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629\",\"icon\":\"FE\",\"sort_order\":4,\"is_active\":true}]', NULL, 'Italy Visa Overview', 'نظرة عامة على تأشيرة إيطاليا', 'Travel Wave Support', 'دعم Travel Wave', '[{\"text_en\":\"Suitable for tourism, family visits, and selected business travel.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0633\\u064a\\u0627\\u062d\\u0629 \\u0648\\u0627\\u0644\\u0632\\u064a\\u0627\\u0631\\u0627\\u062a \\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u064a\\u0629 \\u0648\\u0628\\u0639\\u0636 \\u0631\\u062d\\u0644\\u0627\\u062a \\u0627\\u0644\\u0623\\u0639\\u0645\\u0627\\u0644.\"},{\"text_en\":\"A better organized file usually creates a clearer submission path.\",\"text_ar\":\"\\u064a\\u0633\\u0627\\u0639\\u062f \\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0639\\u0644\\u0649 \\u062c\\u0639\\u0644 \\u0645\\u0633\\u0627\\u0631 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0623\\u0648\\u0636\\u062d.\"},{\"text_en\":\"Travel Wave clarifies the next step before each stage.\",\"text_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f\\u0643 Travel Wave \\u0641\\u064a \\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0628\\u0637\\u0631\\u064a\\u0642\\u0629 \\u0623\\u0648\\u0636\\u062d \\u0648\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0648\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0628\\u0645\\u0627 \\u064a\\u062f\\u0639\\u0645 \\u063a\\u0631\\u0636 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0644\\u0646.\"}]', 'Detailed Visa Explanation', 'شرح التأشيرة بالتفصيل', 'Short-stay Schengen files usually need consistency between the stated purpose of travel, the financial documents, and the booking evidence.\n\nTravel Wave helps applicants reduce confusion by organizing the file in a more readable and practical way.', 'تحتاج ملفات شنغن قصيرة الإقامة عادة إلى اتساق بين الغرض المعلن من السفر والمستندات المالية والحجوزات الداعمة.\n\nتساعدك Travel Wave على تقليل الارتباك من خلال تنظيم الملف بطريقة أكثر وضوحًا وعملية.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[{\"text_en\":\"Suitable for tourism and family visits.\",\"text_ar\":\"\\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0633\\u064a\\u0627\\u062d\\u0629 \\u0648\\u0627\\u0644\\u0632\\u064a\\u0627\\u0631\\u0627\\u062a \\u0627\\u0644\\u0639\\u0627\\u0626\\u0644\\u064a\\u0629.\"},{\"text_en\":\"Early preparation can help in busy seasons.\",\"text_ar\":\"\\u064a\\u0641\\u064a\\u062f \\u0627\\u0644\\u062a\\u062d\\u0636\\u064a\\u0631 \\u0627\\u0644\\u0645\\u0628\\u0643\\u0631 \\u0641\\u064a \\u0627\\u0644\\u0645\\u0648\\u0627\\u0633\\u0645 \\u0627\\u0644\\u0645\\u0632\\u062f\\u062d\\u0645\\u0629.\"}]', NULL, 'Required Documents', 'المستندات المطلوبة', 'The exact list may vary by profile, but these are common supporting items.', 'قد تختلف القائمة حسب الملف، لكن هذه من المستندات الأساسية الشائعة.', '[{\"name_en\":\"Valid Passport\",\"name_ar\":\"\\u062c\\u0648\\u0627\\u0632 \\u0633\\u0641\\u0631 \\u0633\\u0627\\u0631\\u064a\",\"description_en\":\"Passport validity and blank pages should be suitable for the request.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u0643\\u0648\\u0646 \\u0645\\u062f\\u0629 \\u0627\\u0644\\u0635\\u0644\\u0627\\u062d\\u064a\\u0629 \\u0648\\u0627\\u0644\\u0635\\u0641\\u062d\\u0627\\u062a \\u0627\\u0644\\u0645\\u062a\\u0627\\u062d\\u0629 \\u0645\\u0646\\u0627\\u0633\\u0628\\u0629 \\u0644\\u0644\\u0637\\u0644\\u0628.\",\"sort_order\":1,\"is_active\":true},{\"name_en\":\"Bank Statement\",\"name_ar\":\"\\u0643\\u0634\\u0641 \\u062d\\u0633\\u0627\\u0628 \\u0628\\u0646\\u0643\\u064a\",\"description_en\":\"Financial movement should support the travel plan.\",\"description_ar\":\"\\u064a\\u0646\\u0628\\u063a\\u064a \\u0623\\u0646 \\u064a\\u062f\\u0639\\u0645 \\u0627\\u0644\\u062d\\u0631\\u0643\\u0629 \\u0627\\u0644\\u0645\\u0627\\u0644\\u064a\\u0629 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629.\",\"sort_order\":2,\"is_active\":true},{\"name_en\":\"Employment or Study Proof\",\"name_ar\":\"\\u0625\\u062b\\u0628\\u0627\\u062a \\u0639\\u0645\\u0644 \\u0623\\u0648 \\u062f\\u0631\\u0627\\u0633\\u0629\",\"description_en\":\"Supporting the declared profile and return intention.\",\"description_ar\":\"\\u0644\\u062f\\u0639\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0627\\u0644\\u0627\\u0631\\u062a\\u0628\\u0627\\u0637\\u0627\\u062a \\u0628\\u0639\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631.\",\"sort_order\":3,\"is_active\":true},{\"name_en\":\"Hotel, Flight, and Insurance\",\"name_ar\":\"\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646\",\"description_en\":\"Dates should stay aligned across the itinerary.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u0643\\u0648\\u0646 \\u0627\\u0644\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0645\\u062a\\u0633\\u0642\\u0629 \\u0641\\u064a \\u0643\\u0627\\u0645\\u0644 \\u062e\\u0637 \\u0627\\u0644\\u0633\\u064a\\u0631.\",\"sort_order\":4,\"is_active\":true}]', NULL, 'Application Steps', 'خطوات التقديم', '[{\"title_en\":\"Send the basic details\",\"title_ar\":\"\\u0623\\u0631\\u0633\\u0644 \\u0627\\u0644\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a \\u0627\\u0644\\u0623\\u0633\\u0627\\u0633\\u064a\\u0629\",\"description_en\":\"Purpose, timing, and profile overview.\",\"description_ar\":\"\\u0627\\u0644\\u063a\\u0631\\u0636 \\u0645\\u0646 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0627\\u0644\\u062a\\u0648\\u0642\\u064a\\u062a \\u0648\\u0646\\u0628\\u0630\\u0629 \\u0639\\u0646 \\u0627\\u0644\\u0645\\u0644\\u0641.\",\"step_number\":1,\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Review the file\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"description_en\":\"Clarify missing points and organize priorities.\",\"description_ar\":\"\\u062a\\u0648\\u0636\\u064a\\u062d \\u0627\\u0644\\u0646\\u0648\\u0627\\u0642\\u0635 \\u0648\\u062a\\u0631\\u062a\\u064a\\u0628 \\u0627\\u0644\\u0623\\u0648\\u0644\\u0648\\u064a\\u0627\\u062a.\",\"step_number\":2,\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Prepare the documents\",\"title_ar\":\"\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"Organize the file and supporting bookings.\",\"description_ar\":\"\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0627\\u0644\\u062f\\u0627\\u0639\\u0645\\u0629.\",\"step_number\":3,\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Submit and follow up\",\"title_ar\":\"\\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0648\\u0627\\u0644\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629\",\"description_en\":\"Complete the submission and continue request tracking.\",\"description_ar\":\"\\u0625\\u062a\\u0645\\u0627\\u0645 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u062b\\u0645 \\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u062d\\u0627\\u0644\\u0629 \\u0627\\u0644\\u0637\\u0644\\u0628.\",\"step_number\":4,\"sort_order\":4,\"is_active\":true}]', NULL, 'Why Choose Travel Wave', 'لماذا تختار Travel Wave', 'We help applicants move through the process with more clarity and fewer last-minute surprises.', 'نساعد المتقدمين على المرور في الخطوات بوضوح أكبر ومفاجآت أقل في اللحظات الأخيرة.', '[{\"title_en\":\"Document Review\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"Checking the consistency of the file before submission.\",\"description_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u062a\\u0633\\u0627\\u0642 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"icon\":\"shield\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Booking Alignment\",\"title_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a\",\"description_en\":\"Aligning hotel, flight, and insurance timing.\",\"description_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u062a\\u0648\\u0642\\u064a\\u062a\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646.\",\"icon\":\"calendar\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Step Follow-Up\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u0627\\u0644\\u062e\\u0637\\u0648\\u0627\\u062a\",\"description_en\":\"Clarifying the next practical step after each stage.\",\"description_ar\":\"\\u062a\\u0633\\u0627\\u0639\\u062f\\u0643 Travel Wave \\u0641\\u064a \\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0628\\u0637\\u0631\\u064a\\u0642\\u0629 \\u0623\\u0648\\u0636\\u062d \\u0648\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0648\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0628\\u0645\\u0627 \\u064a\\u062f\\u0639\\u0645 \\u063a\\u0631\\u0636 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0639\\u0644\\u0646.\",\"icon\":\"support\",\"sort_order\":3,\"is_active\":true}]', NULL, NULL, NULL, NULL, 'Fees and Processing Time', 'الرسوم ومدة المعالجة', '[{\"label_en\":\"Embassy Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u0633\\u0641\\u0627\\u0631\\u0629\",\"value_en\":\"Varies by profile\",\"value_ar\":\"\\u062a\\u062e\\u062a\\u0644\\u0641 \\u062d\\u0633\\u0628 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Service Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u062e\\u062f\\u0645\\u0629\",\"value_en\":\"Quoted after review\",\"value_ar\":\"\\u062a\\u062d\\u062f\\u062f \\u0628\\u0639\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"Usually 15 to 30 working days\",\"value_ar\":\"\\u0639\\u0627\\u062f\\u0629 \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"sort_order\":3,\"is_active\":true}]', 'Final timing depends on seasonality and file readiness.', 'تعتمد المدة النهائية على الموسم ومدى جاهزية الملف.', '[{\"question_en\":\"Is this a Schengen visa?\",\"question_ar\":\"\\u0647\\u0644 \\u0647\\u0630\\u0647 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0634\\u0646\\u063a\\u0646\\u061f\",\"answer_en\":\"Yes, in most short-stay travel cases the request falls under Schengen rules.\",\"answer_ar\":\"\\u0646\\u0639\\u0645\\u060c \\u0641\\u064a \\u0623\\u063a\\u0644\\u0628 \\u062d\\u0627\\u0644\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0642\\u0635\\u064a\\u0631 \\u064a\\u0646\\u062f\\u0631\\u062c \\u0627\\u0644\\u0637\\u0644\\u0628 \\u0636\\u0645\\u0646 \\u0646\\u0638\\u0627\\u0645 \\u0634\\u0646\\u063a\\u0646.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"When should I start?\",\"question_ar\":\"\\u0645\\u062a\\u0649 \\u0623\\u0628\\u062f\\u0623\\u061f\",\"answer_en\":\"Early preparation is usually better, especially before high-demand travel periods.\",\"answer_ar\":\"\\u064a\\u0641\\u0636\\u0644 \\u0627\\u0644\\u0628\\u062f\\u0621 \\u0645\\u0628\\u0643\\u0631\\u064b\\u0627\\u060c \\u062e\\u0635\\u0648\\u0635\\u064b\\u0627 \\u0642\\u0628\\u0644 \\u0641\\u062a\\u0631\\u0627\\u062a \\u0627\\u0644\\u0636\\u063a\\u0637 \\u0627\\u0644\\u0645\\u0631\\u062a\\u0641\\u0639 \\u0639\\u0644\\u0649 \\u0627\\u0644\\u0645\\u0648\\u0627\\u0639\\u064a\\u062f.\",\"sort_order\":2,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, 'Ask About Italy Visa', 'استفسر عن تأشيرة إيطاليا', 'Send your details and Travel Wave will guide you on the next practical step.', 'أرسل بياناتك وستساعدك Travel Wave في تحديد الخطوة العملية التالية.', 'Send Inquiry', 'أرسل الاستفسار', NULL, NULL, 'Italy Visa', '[\"email\",\"travel_date\",\"message\"]', 1, NULL, NULL, 'Ready to Start Italy Visa Preparation?', 'جاهز لبدء تجهيز تأشيرة إيطاليا؟', 'Travel Wave helps make the request more organized and easier to follow.', 'تساعدك Travel Wave على جعل الطلب أكثر تنظيمًا وأسهل في المتابعة.', 'Start Now', 'ابدأ الآن', '#destination-form', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 1, NULL, NULL, NULL, NULL, NULL, 1, 1, 2, '2026-03-25 23:07:02', '2026-03-28 00:21:22', NULL, NULL),
(4, 4, 'Spain', 'إسبانيا', 'spain-visa', NULL, NULL, NULL, NULL, 'Spain Visa Services', 'خدمات تأشيرة إسبانيا', NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 5, '2026-03-25 23:07:02', '2026-03-27 23:59:21', '2026-03-27 23:59:21', 3),
(5, 4, 'Netherlands', 'هولندا', 'netherlands-visa', NULL, NULL, NULL, NULL, 'Netherlands Visa Services', 'خدمات تأشيرة هولندا', NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 6, '2026-03-25 23:07:02', '2026-03-27 23:59:23', '2026-03-27 23:59:23', 3),
(6, 4, 'Greece', 'اليونان', 'greece-visa', NULL, NULL, NULL, NULL, 'Greece Visa Services', 'خدمات تأشيرة اليونان', NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 7, '2026-03-25 23:07:02', '2026-03-27 23:59:25', '2026-03-27 23:59:25', 3),
(7, 2, 'UAE', 'الإمارات', 'uae-visa', 'UAE visa support with practical planning and document guidance.', 'دعم تأشيرة الإمارات مع تخطيط عملي وإرشاد للمستندات.', NULL, NULL, 'UAE Visa Services', 'خدمات تأشيرة الإمارات', NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, 'Travel Wave provides structured assistance for UAE visa applications with document review and travel advice.', 'توفر Travel Wave دعمًا منظمًا لطلبات تأشيرة الإمارات مع مراجعة الملف والنصائح قبل السفر.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 10, '2026-03-25 23:07:02', '2026-03-27 23:59:33', '2026-03-27 23:59:33', 3),
(8, 4, 'USA', 'أمريكا', 'usa-visa', NULL, NULL, NULL, NULL, 'USA Visa Services', 'خدمات تأشيرة أمريكا', NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 9, '2026-03-25 23:07:02', '2026-03-27 23:59:27', '2026-03-27 23:59:27', 3),
(9, 4, 'Canada', 'كندا', 'canada-visa', 'Canada visa support with practical planning and document guidance.', 'دعم تأشيرة كندا مع تخطيط عملي وإرشاد للمستندات.', NULL, NULL, 'Canada Visa Services', 'خدمات تأشيرة كندا', NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, 'Travel Wave provides structured assistance for Canada visa applications with document review and travel advice.', 'توفر Travel Wave دعمًا منظمًا لطلبات تأشيرة كندا مع مراجعة الملف والنصائح قبل السفر.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 10, '2026-03-25 23:07:02', '2026-03-27 23:59:36', '2026-03-27 23:59:36', 3),
(10, 3, 'Turkey', 'تركيا', 'turkey-visa', 'Turkey visa support with practical planning and document guidance.', 'دعم تأشيرة تركيا مع تخطيط عملي وإرشاد للمستندات.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.45, NULL, NULL, NULL, 'Travel Wave provides structured assistance for Turkey visa applications with document review and travel advice.', 'توفر Travel Wave دعمًا منظمًا لطلبات تأشيرة تركيا مع مراجعة الملف والنصائح قبل السفر.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, 0, 1, 10, '2026-03-25 23:07:02', '2026-03-27 23:59:38', '2026-03-27 23:59:38', 3);
INSERT INTO `visa_countries` (`id`, `visa_category_id`, `name_en`, `name_ar`, `slug`, `excerpt_en`, `excerpt_ar`, `hero_badge_en`, `hero_badge_ar`, `hero_title_en`, `hero_title_ar`, `hero_subtitle_en`, `hero_subtitle_ar`, `hero_cta_text_en`, `hero_cta_text_ar`, `hero_cta_url`, `hero_overlay_opacity`, `hero_image`, `hero_mobile_image`, `flag_image`, `overview_en`, `overview_ar`, `visa_type_en`, `visa_type_ar`, `stay_duration_en`, `stay_duration_ar`, `quick_summary_destination_label_en`, `quick_summary_destination_label_ar`, `quick_summary_destination_icon`, `quick_summary_items`, `intro_image`, `introduction_title_en`, `introduction_title_ar`, `introduction_badge_en`, `introduction_badge_ar`, `introduction_points`, `detailed_title_en`, `detailed_title_ar`, `detailed_description_en`, `detailed_description_ar`, `best_time_badge_en`, `best_time_badge_ar`, `best_time_title_en`, `best_time_title_ar`, `best_time_description_en`, `best_time_description_ar`, `highlights_section_label_en`, `highlights_section_label_ar`, `highlights_section_title_en`, `highlights_section_title_ar`, `highlights`, `required_documents`, `documents_title_en`, `documents_title_ar`, `documents_subtitle_en`, `documents_subtitle_ar`, `document_items`, `application_steps`, `steps_title_en`, `steps_title_ar`, `step_items`, `services`, `why_choose_title_en`, `why_choose_title_ar`, `why_choose_intro_en`, `why_choose_intro_ar`, `why_choose_items`, `processing_time_en`, `processing_time_ar`, `fees_en`, `fees_ar`, `fees_title_en`, `fees_title_ar`, `fee_items`, `fees_notes_en`, `fees_notes_ar`, `faqs`, `faq_title_en`, `faq_title_ar`, `support_title_en`, `support_title_ar`, `support_subtitle_en`, `support_subtitle_ar`, `support_button_en`, `support_button_ar`, `support_button_link`, `support_is_active`, `map_title_en`, `map_title_ar`, `map_description_en`, `map_description_ar`, `map_embed_code`, `map_is_active`, `inquiry_form_title_en`, `inquiry_form_title_ar`, `inquiry_form_subtitle_en`, `inquiry_form_subtitle_ar`, `inquiry_form_button_en`, `inquiry_form_button_ar`, `inquiry_form_success_en`, `inquiry_form_success_ar`, `inquiry_form_default_service_type`, `inquiry_form_visible_fields`, `inquiry_form_is_active`, `inquiry_form_label_en`, `inquiry_form_label_ar`, `cta_title_en`, `cta_title_ar`, `cta_text_en`, `cta_text_ar`, `cta_button_en`, `cta_button_ar`, `cta_url`, `final_cta_background_image`, `final_cta_is_active`, `meta_title_en`, `meta_title_ar`, `meta_description_en`, `meta_description_ar`, `og_image`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `deleted_by`) VALUES
(11, 1, 'Slovakia', 'سلوفاكيا', 'Slovakia-visa', 'Visa type: Short-Stay Schengen Visa. Typical stay allowance: Up to 90 days within 180 days. Expected processing time: Estimated processing is usually around 15 to 30 working days, depending on seasonality, embassy load, and file completeness.. Common required documents include Valid Passport, Recent Personal Photos, and Bank Statement. Travel Wave support includes Professional Document Review and Organized File Preparation.', 'تشمل الخدمة تأشيرة شنغن قصيرة الإقامة. مدة الإقامة المعتادة حتى 90 يوما خلال 180 يوما. المدة المتوقعة للمعالجة تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.. أبرز المستندات المطلوبة: جواز سفر ساري، صور شخصية حديثة، وكشف حساب بنكي. توفر Travel Wave دعماً يشمل مراجعة احترافية للمستندات وتنظيم الملف بشكل واضح.', 'Schengen Visa Support', 'دعم تأشيرة شنغن', 'Apply for a France Visa with Clarity and Confidence', 'استخراج فيزا فرنسا للمصريين بوضوح وثقة', 'With Travel Wave, we help you prepare your France tourist visa application, book your appointment, review your documents, and follow up on your application through a clear and professional process.', 'مع Travel Wave نساعدك في تجهيز ملف تأشيرة فرنسا السياحية، وحجز الموعد، ومراجعة المستندات، ومتابعة الطلب بخطوات واضحة وتنظيم احترافي.', 'Start Your France Visa Request', 'ابدأ طلب تأشيرة فرنسا', '#visa-inquiry', 0.50, 'visa-countries/highlights/ROPFjByXiumOxWhvgMvssXrhWOtaxgpfAFqdFsPx.png', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 'visa-countries/france-flag.svg', 'The France visa is one of the most requested Schengen visas for travelers planning tourism, visits, or certain short business activities.\r\nThis page is designed to give you a clear understanding of the requirements for a France visa application, the main steps involved, and how Travel Wave helps you prepare your file and reduce errors before submission.', 'تُعد فيزا فرنسا من أكثر تأشيرات شنغن طلبًا للمسافرين الراغبين في السياحة أو الزيارة أو حضور بعض الأنشطة التجارية القصيرة.\r\nتم تصميم هذه الصفحة لتمنحك فهمًا واضحًا لمتطلبات تأشيرة فرنسا للمصريين، والخطوات الأساسية للتقديم، وكيف تساعدك Travel Wave في تجهيز الملف وتقليل الأخطاء قبل التقديم.', 'Short-Stay Schengen Visa', 'تأشيرة شنغن قصيرة الإقامة', 'Up to 90 days within 180 days', 'حتى 90 يوما خلال 180 يوما', 'Country', 'الدولة', 'fxemoji:franceflag', '[{\"label_en\":\"Visa Type\",\"label_ar\":\"\\u0646\\u0648\\u0639 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0629\",\"value_en\":\"Short-Stay Schengen\",\"value_ar\":\"\\u0634\\u0646\\u063a\\u0646 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"icon\":\"material-symbols:travel-explore-rounded\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"15 to 30 working days\",\"value_ar\":\"15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"icon\":\"tdesign:time-filled\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Stay Duration\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629\",\"value_en\":\"Up to 90 days\",\"value_ar\":\"\\u062d\\u062a\\u0649 90 \\u064a\\u0648\\u0645\\u0627\",\"icon\":\"fluent-mdl2:date-time\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Approx. Fees\",\"label_ar\":\"\\u0627\\u0644\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u062a\\u0642\\u0631\\u064a\\u0628\\u064a\\u0629\",\"value_en\":\"Determined after file review\",\"value_ar\":\"\\u062a\\u062d\\u062f\\u062f \\u0628\\u0639\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"icon\":\"material-symbols:attach-money\",\"sort_order\":4,\"is_active\":true}]', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 'France Visa Overview', 'نظرة عامة على تأشيرة فرنسا', 'Travel Wave Guided Support', 'دعم موجه من Travel Wave', '[{\"text_en\":\"Clear file preparation before submission.\",\"text_ar\":\"\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0648\\u0627\\u0636\\u062d \\u0644\\u0644\\u0645\\u0644\\u0641 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\"},{\"text_en\":\"Practical support for bookings and application timing\",\"text_ar\":\"\\u062f\\u0639\\u0645 \\u0639\\u0645\\u0644\\u064a \\u0644\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0648\\u062a\\u0648\\u0642\\u064a\\u062a \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\"},{\"text_en\":\"Better document organization based on your travel purpose\",\"text_ar\":\"\\u062a\\u0646\\u0633\\u064a\\u0642 \\u0623\\u0641\\u0636\\u0644 \\u0644\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0628\\u0645\\u0627 \\u064a\\u0646\\u0627\\u0633\\u0628 \\u0633\\u0628\\u0628 \\u0627\\u0644\\u0633\\u0641\\u0631\"},{\"text_en\":\"A clearer understanding of the next step at every stage\",\"text_ar\":\"\\u0641\\u0647\\u0645 \\u0623\\u0648\\u0636\\u062d \\u0644\\u0644\\u062e\\u0637\\u0648\\u0629 \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629 \\u0628\\u0639\\u062f \\u0643\\u0644 \\u0645\\u0631\\u062d\\u0644\\u0629\"}]', 'Detailed Visa Explanation', 'شرح التأشيرة بالتفصيل', 'The France tourist visa usually falls under the short-stay Schengen visa category and is suitable for travelers visiting for tourism, family visits, or certain short business trips.\r\n\r\nVisa approval depends on having a well-organized file that clearly explains your purpose of travel, financial ability, and the consistency of your bookings and supporting documents with your travel plan. Your application details should also be accurate, and all essential documents should be clear, recent, and ready for review.', 'غالبًا ما تندرج تأشيرة فرنسا السياحية ضمن فئة شنغن قصيرة الإقامة، وهي مناسبة للمسافرين بغرض السياحة أو الزيارة العائلية أو بعض رحلات الأعمال القصيرة.\r\n\r\nيعتمد قبول الطلب على وجود ملف منظم يوضح سبب السفر، والقدرة المالية، وتوافق الحجوزات والمستندات مع خطة الرحلة. كما يجب أن تكون بيانات الطلب دقيقة، وأن تكون جميع الوثائق الأساسية واضحة وحديثة وقابلة للمراجعة.', 'Best time', 'أفضل وقت 0', 'Best time to apply', 'أفضل وقت للتقديم 0', 'The processing time usually ranges between 15 and 30 working days, depending on the season, embassy pressure, and the completeness of the file.', 'تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف. 0', 'Key points', 'أبرز النقاط المهمة', 'Key points', 'أبرز النقاط المهمة', '[{\"title_en\":\"The more structured and\",\"title_ar\":\"\\u0643\\u0644\\u0645\\u0627 \\u0643\\u0627\\u0646\\u062a \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0645\\u0631\\u062a\\u0628\\u0629\",\"description_en\":\"The more structured and consistent your documents are, the easier your application becomes to review.\",\"description_ar\":\"\\u0643\\u0644\\u0645\\u0627 \\u0643\\u0627\\u0646\\u062a \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0645\\u0631\\u062a\\u0628\\u0629 \\u0648\\u0645\\u062a\\u0631\\u0627\\u0628\\u0637\\u0629 \\u0645\\u0639 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631\\u060c \\u0623\\u0635\\u0628\\u062d \\u0627\\u0644\\u0637\\u0644\\u0628 \\u0623\\u0643\\u062b\\u0631 \\u0648\\u0636\\u0648\\u062d\\u064b\\u0627 \\u0639\\u0646\\u062f \\u0627\\u0644\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629.\",\"image\":\"visa-countries\\/highlights\\/kIGUFgWrDz8qmHK1O3eokREfQEf66r2XpyFTdDNm.jpg\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Matching travel dates, hotel\",\"title_ar\":\"\\u062a\\u0637\\u0627\\u0628\\u0642 \\u0645\\u0648\\u0627\\u0639\\u064a\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0627\\u0644\\u0641\\u0646\\u062f\\u0642\",\"description_en\":\"Matching travel dates, hotel reservations, insurance, and supporting documents strengthens your application.\",\"description_ar\":\"\\u062a\\u0637\\u0627\\u0628\\u0642 \\u0645\\u0648\\u0627\\u0639\\u064a\\u062f \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0648\\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u064a\\u062f\\u0639\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0641\\u0636\\u0644.\",\"image\":\"visa-countries\\/highlights\\/ROPFjByXiumOxWhvgMvssXrhWOtaxgpfAFqdFsPx.png\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"test\",\"title_ar\":\"test\",\"description_en\":\"testtesttesttesttest\",\"description_ar\":\"testtesttesttesttest\",\"image\":\"media-library\\/DExTaYIB43hfIRCdpPiFH37AisJoMIUpfxuHYm3I.png\",\"sort_order\":3,\"is_active\":true}]', '[]', 'Required Documents', 'المستندات المطلوبة', 'The exact file may vary by profile, but these are the most common documents requested for France tourist visa preparation.', 'قد يختلف الملف بحسب حالة المتقدم، لكن هذه هي المستندات الأكثر شيوعا لتجهيز تأشيرة فرنسا السياحية.', '[{\"name_en\":\"Valid Passport\",\"name_ar\":\"\\u062c\\u0648\\u0627\\u0632 \\u0633\\u0641\\u0631 \\u0633\\u0627\\u0631\\u064a\",\"description_en\":\"Passport should cover the required validity period and include usable pages.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u064a\\u063a\\u0637\\u064a \\u062c\\u0648\\u0627\\u0632 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0645\\u062f\\u0629 \\u0627\\u0644\\u0635\\u0644\\u0627\\u062d\\u064a\\u0629 \\u0627\\u0644\\u0645\\u0637\\u0644\\u0648\\u0628\\u0629 \\u0648\\u0623\\u0646 \\u064a\\u062d\\u062a\\u0648\\u064a \\u0639\\u0644\\u0649 \\u0635\\u0641\\u062d\\u0627\\u062a \\u0645\\u062a\\u0627\\u062d\\u0629.\",\"sort_order\":1,\"is_active\":true},{\"name_en\":\"Recent Personal Photos\",\"name_ar\":\"\\u0635\\u0648\\u0631 \\u0634\\u062e\\u0635\\u064a\\u0629 \\u062d\\u062f\\u064a\\u062b\\u0629\",\"description_en\":\"Photos should match embassy size and background requirements.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u0637\\u0627\\u0628\\u0642 \\u0627\\u0644\\u0635\\u0648\\u0631 \\u0645\\u0642\\u0627\\u0633\\u0627\\u062a \\u0648\\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0627\\u0631\\u0629.\",\"sort_order\":2,\"is_active\":true},{\"name_en\":\"Bank Statement\",\"name_ar\":\"\\u0643\\u0634\\u0641 \\u062d\\u0633\\u0627\\u0628 \\u0628\\u0646\\u0643\\u064a\",\"description_en\":\"Financial movement should support the proposed trip timing and cost level.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u064a\\u062f\\u0639\\u0645 \\u0643\\u0634\\u0641 \\u0627\\u0644\\u062d\\u0633\\u0627\\u0628 \\u062a\\u0648\\u0642\\u064a\\u062a \\u0627\\u0644\\u0631\\u062d\\u0644\\u0629 \\u0648\\u0645\\u0633\\u062a\\u0648\\u0649 \\u0627\\u0644\\u062a\\u0643\\u0644\\u0641\\u0629 \\u0627\\u0644\\u0645\\u0642\\u062a\\u0631\\u062d.\",\"sort_order\":3,\"is_active\":true},{\"name_en\":\"Employment or Study Proof\",\"name_ar\":\"\\u0625\\u062b\\u0628\\u0627\\u062a \\u0639\\u0645\\u0644 \\u0623\\u0648 \\u062f\\u0631\\u0627\\u0633\\u0629\",\"description_en\":\"An employment letter or equivalent proof strengthens the purpose and return intention.\",\"description_ar\":\"\\u062e\\u0637\\u0627\\u0628 \\u0627\\u0644\\u0639\\u0645\\u0644 \\u0623\\u0648 \\u0645\\u0627 \\u064a\\u0639\\u0627\\u062f\\u0644\\u0647 \\u064a\\u062f\\u0639\\u0645 \\u0633\\u0628\\u0628 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0646\\u064a\\u0629 \\u0627\\u0644\\u0639\\u0648\\u062f\\u0629.\",\"sort_order\":4,\"is_active\":true},{\"name_en\":\"Hotel and Flight Reservations\",\"name_ar\":\"\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646\",\"description_en\":\"Reservation dates should match the travel plan and visa request window.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u062a\\u062a\\u0648\\u0627\\u0641\\u0642 \\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u062d\\u062c\\u0632 \\u0645\\u0639 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0637\\u0644\\u0628.\",\"sort_order\":5,\"is_active\":true},{\"name_en\":\"Travel Insurance\",\"name_ar\":\"\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0627\\u0644\\u0633\\u0641\\u0631\",\"description_en\":\"Insurance should meet Schengen coverage requirements for the full stay period.\",\"description_ar\":\"\\u064a\\u062c\\u0628 \\u0623\\u0646 \\u064a\\u062d\\u0642\\u0642 \\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a \\u0634\\u0646\\u063a\\u0646 \\u0637\\u0648\\u0627\\u0644 \\u0641\\u062a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629.\",\"sort_order\":6,\"is_active\":true}]', '[]', 'Application Steps', 'خطوات التقديم', '[{\"title_en\":\"Submit Your Details\",\"title_ar\":\"\\u0623\\u0631\\u0633\\u0644 \\u0628\\u064a\\u0627\\u0646\\u0627\\u062a\\u0643\",\"description_en\":\"Share your travel purpose, timing, and basic profile so we can assess the file direction.\",\"description_ar\":\"\\u0634\\u0627\\u0631\\u0643 \\u0633\\u0628\\u0628 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u062a\\u0648\\u0642\\u064a\\u062a\\u0647 \\u0648\\u0628\\u064a\\u0627\\u0646\\u0627\\u062a\\u0643 \\u0627\\u0644\\u0623\\u0633\\u0627\\u0633\\u064a\\u0629 \\u0644\\u062a\\u062d\\u062f\\u064a\\u062f \\u0627\\u062a\\u062c\\u0627\\u0647 \\u0627\\u0644\\u0645\\u0644\\u0641.\",\"sort_order\":1,\"step_number\":1,\"is_active\":true},{\"title_en\":\"Review the File\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"description_en\":\"Travel Wave reviews what is available and points out what still needs improvement.\",\"description_ar\":\"\\u062a\\u0631\\u0627\\u062c\\u0639 Travel Wave \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0627\\u0644\\u062d\\u0627\\u0644\\u064a \\u0648\\u062a\\u0648\\u0636\\u062d \\u0645\\u0627 \\u064a\\u062d\\u062a\\u0627\\u062c \\u0625\\u0644\\u0649 \\u0627\\u0633\\u062a\\u0643\\u0645\\u0627\\u0644 \\u0623\\u0648 \\u062a\\u062d\\u0633\\u064a\\u0646.\",\"sort_order\":2,\"step_number\":2,\"is_active\":true},{\"title_en\":\"Prepare Documents\",\"title_ar\":\"\\u062a\\u062c\\u0647\\u064a\\u0632 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"Supporting papers are completed and aligned with the travel purpose and booking dates.\",\"description_ar\":\"\\u064a\\u062a\\u0645 \\u0627\\u0633\\u062a\\u0643\\u0645\\u0627\\u0644 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0648\\u0645\\u0637\\u0627\\u0628\\u0642\\u062a\\u0647\\u0627 \\u0645\\u0639 \\u063a\\u0631\\u0636 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0648\\u062a\\u0648\\u0627\\u0631\\u064a\\u062e \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a.\",\"sort_order\":3,\"step_number\":3,\"is_active\":true},{\"title_en\":\"Booking and Follow-Up\",\"title_ar\":\"\\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a \\u0648\\u0627\\u0644\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629\",\"description_en\":\"Hotel, flight, and insurance details are coordinated before the appointment stage.\",\"description_ar\":\"\\u064a\\u062a\\u0645 \\u062a\\u0646\\u0633\\u064a\\u0642 \\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0642\\u0628\\u0644 \\u0645\\u0631\\u062d\\u0644\\u0629 \\u0627\\u0644\\u0645\\u0648\\u0639\\u062f.\",\"sort_order\":4,\"step_number\":4,\"is_active\":true},{\"title_en\":\"Application Submission\",\"title_ar\":\"\\u0625\\u062a\\u0645\\u0627\\u0645 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645\",\"description_en\":\"You attend the submission and biometric step, then continue tracking the request.\",\"description_ar\":\"\\u062a\\u0642\\u0648\\u0645 \\u0628\\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0648\\u0627\\u0644\\u0628\\u0635\\u0645\\u0629 \\u062b\\u0645 \\u062a\\u062a\\u0627\\u0628\\u0639 \\u062d\\u0627\\u0644\\u0629 \\u0627\\u0644\\u0637\\u0644\\u0628 \\u0628\\u0639\\u062f \\u0630\\u0644\\u0643.\",\"sort_order\":5,\"step_number\":5,\"is_active\":true}]', '[]', 'Why Choose Travel Wave', 'لماذا تختار Travel Wave', 'A premium support experience built to make the France visa process clearer, more organized, and easier to follow.', 'تجربة دعم احترافية تجعل مسار تأشيرة فرنسا أوضح وأكثر تنظيما وأسهل في المتابعة.', '[{\"title_en\":\"Professional Document Review\",\"title_ar\":\"\\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u062d\\u062a\\u0631\\u0627\\u0641\\u064a\\u0629 \\u0644\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a\",\"description_en\":\"We check the file structure and highlight missing items before the appointment date.\",\"description_ar\":\"\\u0646\\u0631\\u0627\\u062c\\u0639 \\u062a\\u0631\\u062a\\u064a\\u0628 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0648\\u0646\\u0648\\u0636\\u062d \\u0627\\u0644\\u0646\\u0648\\u0627\\u0642\\u0635 \\u0642\\u0628\\u0644 \\u0645\\u0648\\u0639\\u062f \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"icon\":\"shield\",\"sort_order\":1,\"is_active\":true},{\"title_en\":\"Organized File Preparation\",\"title_ar\":\"\\u062a\\u0646\\u0638\\u064a\\u0645 \\u0627\\u0644\\u0645\\u0644\\u0641 \\u0628\\u0634\\u0643\\u0644 \\u0648\\u0627\\u0636\\u062d\",\"description_en\":\"Your supporting papers are arranged in a cleaner order that is easier to understand and present.\",\"description_ar\":\"\\u064a\\u062a\\u0645 \\u062a\\u0631\\u062a\\u064a\\u0628 \\u0627\\u0644\\u0645\\u0633\\u062a\\u0646\\u062f\\u0627\\u062a \\u0628\\u0634\\u0643\\u0644 \\u0623\\u0648\\u0636\\u062d \\u0648\\u0623\\u0633\\u0647\\u0644 \\u0644\\u0644\\u0641\\u0647\\u0645 \\u0648\\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645.\",\"icon\":\"file\",\"sort_order\":2,\"is_active\":true},{\"title_en\":\"Booking Support\",\"title_ar\":\"\\u062f\\u0639\\u0645 \\u0627\\u0644\\u062d\\u062c\\u0648\\u0632\\u0627\\u062a\",\"description_en\":\"We help align hotel, flight, and insurance details with the intended travel plan.\",\"description_ar\":\"\\u0646\\u0633\\u0627\\u0639\\u062f \\u0641\\u064a \\u062a\\u0646\\u0633\\u064a\\u0642 \\u062a\\u0641\\u0627\\u0635\\u064a\\u0644 \\u0627\\u0644\\u0641\\u0646\\u062f\\u0642 \\u0648\\u0627\\u0644\\u0637\\u064a\\u0631\\u0627\\u0646 \\u0648\\u0627\\u0644\\u062a\\u0623\\u0645\\u064a\\u0646 \\u0645\\u0639 \\u062e\\u0637\\u0629 \\u0627\\u0644\\u0633\\u0641\\u0631.\",\"icon\":\"calendar\",\"sort_order\":3,\"is_active\":true},{\"title_en\":\"Step-by-Step Follow-Up\",\"title_ar\":\"\\u0645\\u062a\\u0627\\u0628\\u0639\\u0629 \\u062e\\u0637\\u0648\\u0629 \\u0628\\u062e\\u0637\\u0648\\u0629\",\"description_en\":\"Applicants know what comes next at every stage instead of guessing the process.\",\"description_ar\":\"\\u064a\\u0639\\u0631\\u0641 \\u0627\\u0644\\u0645\\u062a\\u0642\\u062f\\u0645 \\u0645\\u0627 \\u0647\\u064a \\u0627\\u0644\\u062e\\u0637\\u0648\\u0629 \\u0627\\u0644\\u062a\\u0627\\u0644\\u064a\\u0629 \\u0641\\u064a \\u0643\\u0644 \\u0645\\u0631\\u062d\\u0644\\u0629 \\u062f\\u0648\\u0646 \\u062a\\u062e\\u0645\\u064a\\u0646.\",\"icon\":\"support\",\"sort_order\":4,\"is_active\":true}]', 'Estimated processing is usually around 15 to 30 working days, depending on seasonality, embassy load, and file completeness.', 'تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.', 'Approximate fees depend on embassy, visa center, and service support charges.', 'تعتمد الرسوم التقريبية على رسوم السفارة ومركز التأشيرات ورسوم الخدمة.', 'Fees and Processing Time', 'الرسوم ومدة المعالجة', '[{\"label_en\":\"Embassy Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0627\\u0644\\u0633\\u0641\\u0627\\u0631\\u0629\",\"value_en\":\"Varies by traveler type\",\"value_ar\":\"\\u062a\\u062e\\u062a\\u0644\\u0641 \\u062d\\u0633\\u0628 \\u0646\\u0648\\u0639 \\u0627\\u0644\\u0645\\u0633\\u0627\\u0641\\u0631\",\"sort_order\":1,\"is_active\":true},{\"label_en\":\"Visa Center Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u0645\\u0631\\u0643\\u0632 \\u0627\\u0644\\u062a\\u0623\\u0634\\u064a\\u0631\\u0627\\u062a\",\"value_en\":\"Additional service charge\",\"value_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u062e\\u062f\\u0645\\u0629 \\u0625\\u0636\\u0627\\u0641\\u064a\\u0629\",\"sort_order\":2,\"is_active\":true},{\"label_en\":\"Travel Wave Service Fee\",\"label_ar\":\"\\u0631\\u0633\\u0648\\u0645 \\u062e\\u062f\\u0645\\u0629 Travel Wave\",\"value_en\":\"Quoted after file review\",\"value_ar\":\"\\u064a\\u062a\\u0645 \\u062a\\u062d\\u062f\\u064a\\u062f\\u0647\\u0627 \\u0628\\u0639\\u062f \\u0645\\u0631\\u0627\\u062c\\u0639\\u0629 \\u0627\\u0644\\u0645\\u0644\\u0641\",\"sort_order\":3,\"is_active\":true},{\"label_en\":\"Processing Time\",\"label_ar\":\"\\u0645\\u062f\\u0629 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629\",\"value_en\":\"Usually 15 to 30 working days\",\"value_ar\":\"\\u0639\\u0627\\u062f\\u0629 \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644\",\"sort_order\":4,\"is_active\":true}]', 'Final pricing may change according to embassy updates, traveler age, or additional service needs. Travel Wave confirms the latest estimate before submission.', 'قد تختلف التكلفة النهائية بحسب تحديثات السفارة أو عمر المسافر أو الخدمات الإضافية. تؤكد Travel Wave التقدير الأحدث قبل التقديم.', '[{\"question_en\":\"Is France visa considered a Schengen visa?\",\"question_ar\":\"\\u0647\\u0644 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627 \\u062a\\u0639\\u062a\\u0628\\u0631 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0634\\u0646\\u063a\\u0646\\u061f\",\"answer_en\":\"Yes. In most travel cases, the France short-stay visa is processed under Schengen rules.\",\"answer_ar\":\"\\u0646\\u0639\\u0645. \\u0641\\u064a \\u0623\\u063a\\u0644\\u0628 \\u062d\\u0627\\u0644\\u0627\\u062a \\u0627\\u0644\\u0633\\u0641\\u0631 \\u062a\\u062a\\u0645 \\u0645\\u0639\\u0627\\u0644\\u062c\\u0629 \\u062a\\u0623\\u0634\\u064a\\u0631\\u0629 \\u0641\\u0631\\u0646\\u0633\\u0627 \\u0642\\u0635\\u064a\\u0631\\u0629 \\u0627\\u0644\\u0625\\u0642\\u0627\\u0645\\u0629 \\u0636\\u0645\\u0646 \\u0646\\u0638\\u0627\\u0645 \\u0634\\u0646\\u063a\\u0646.\",\"sort_order\":1,\"is_active\":true},{\"question_en\":\"How long does processing usually take?\",\"question_ar\":\"\\u0643\\u0645 \\u062a\\u0633\\u062a\\u063a\\u0631\\u0642 \\u0627\\u0644\\u0645\\u0639\\u0627\\u0644\\u062c\\u0629 \\u0639\\u0627\\u062f\\u0629\\u061f\",\"answer_en\":\"It often ranges from 15 to 30 working days, but seasonal pressure can affect timelines.\",\"answer_ar\":\"\\u063a\\u0627\\u0644\\u0628\\u0627 \\u0645\\u0627 \\u062a\\u062a\\u0631\\u0627\\u0648\\u062d \\u0645\\u0646 15 \\u0625\\u0644\\u0649 30 \\u064a\\u0648\\u0645 \\u0639\\u0645\\u0644 \\u0648\\u0642\\u062f \\u062a\\u062a\\u0623\\u062b\\u0631 \\u0628\\u0627\\u0644\\u0645\\u0648\\u0627\\u0633\\u0645 \\u0648\\u0636\\u063a\\u0637 \\u0627\\u0644\\u0637\\u0644\\u0628\\u0627\\u062a.\",\"sort_order\":2,\"is_active\":true},{\"question_en\":\"Is biometric attendance required?\",\"question_ar\":\"\\u0647\\u0644 \\u0627\\u0644\\u062d\\u0636\\u0648\\u0631 \\u0644\\u0644\\u0628\\u0635\\u0645\\u0629 \\u0645\\u0637\\u0644\\u0648\\u0628\\u061f\",\"answer_en\":\"In many cases yes, depending on prior Schengen biometric history and current requirements.\",\"answer_ar\":\"\\u0641\\u064a \\u0643\\u062b\\u064a\\u0631 \\u0645\\u0646 \\u0627\\u0644\\u062d\\u0627\\u0644\\u0627\\u062a \\u0646\\u0639\\u0645 \\u0628\\u062d\\u0633\\u0628 \\u0633\\u062c\\u0644 \\u0627\\u0644\\u0628\\u0635\\u0645\\u0629 \\u0627\\u0644\\u0633\\u0627\\u0628\\u0642 \\u0648\\u0645\\u062a\\u0637\\u0644\\u0628\\u0627\\u062a \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0627\\u0644\\u062d\\u0627\\u0644\\u064a\\u0629.\",\"sort_order\":3,\"is_active\":true},{\"question_en\":\"When should I apply before travel?\",\"question_ar\":\"\\u0645\\u062a\\u0649 \\u064a\\u062c\\u0628 \\u0623\\u0646 \\u0623\\u0628\\u062f\\u0623 \\u0627\\u0644\\u062a\\u0642\\u062f\\u064a\\u0645 \\u0642\\u0628\\u0644 \\u0627\\u0644\\u0633\\u0641\\u0631\\u061f\",\"answer_en\":\"Starting early is usually the safer option, especially before busy travel seasons.\",\"answer_ar\":\"\\u064a\\u0641\\u0636\\u0644 \\u0627\\u0644\\u0628\\u062f\\u0621 \\u0645\\u0628\\u0643\\u0631\\u0627 \\u062e\\u0627\\u0635\\u0629 \\u0642\\u0628\\u0644 \\u0645\\u0648\\u0627\\u0633\\u0645 \\u0627\\u0644\\u0633\\u0641\\u0631 \\u0627\\u0644\\u0645\\u0632\\u062f\\u062d\\u0645\\u0629.\",\"sort_order\":4,\"is_active\":true}]', 'Frequently Asked Questions', 'الأسئلة الشائعة', 'Need Help Before You Apply?', 'تحتاج إلى مساعدة قبل التقديم؟', 'Talk to Travel Wave and get practical guidance on documents, bookings, and the best next step for your France visa file.', 'تحدث مع Travel Wave واحصل على إرشاد عملي بخصوص المستندات والحجوزات وأفضل خطوة تالية لملف تأشيرة فرنسا.', 'Speak to an Advisor', 'تحدث مع مستشار', '#visa-inquiry', 1, 'Office and Visa Support Location', 'موقع المكتب ودعم التأشيرات', 'Use the map section to display your office, embassy, or visa center reference point for applicants.', 'يمكن استخدام هذا القسم لعرض موقع المكتب أو السفارة أو مركز التأشيرات كمرجع للمتقدمين.', '<iframe src=\"https://www.google.com/maps?q=Paris%20France&output=embed\" width=\"100%\" height=\"420\" style=\"border:0;\" loading=\"lazy\"></iframe>', 1, 'Talk to Travel Wave About Your France Visa', 'تواصل مع Travel Wave بخصوص تأشيرة فرنسا', 'Send your details and our team will guide you on eligibility, documents, and the next practical step.', 'أرسل بياناتك وسيرشدك فريقنا بخصوص الأهلية والمستندات والخطوة العملية التالية.', 'Send France Visa Inquiry', 'أرسل استفسار تأشيرة فرنسا', 'Your France visa inquiry has been received. A Travel Wave advisor will contact you shortly.', 'تم استلام استفسارك الخاص بتأشيرة فرنسا وسيتواصل معك أحد مستشاري Travel Wave قريبا.', 'France Visa', '[\"full_name\",\"phone\",\"whatsapp_number\",\"email\",\"service_type\",\"destination\",\"travel_date\",\"message\"]', 1, 'Contact Us', 'تواصل معنا', 'Ready to Start Your France Visa File?', 'جاهز لبدء ملف تأشيرة فرنسا؟', 'Let Travel Wave turn a complex visa process into a more organized, readable, and confidence-building journey.', 'دع Travel Wave تحول خطوات التأشيرة المعقدة إلى رحلة أكثر تنظيما ووضوحا وثقة.', 'Apply with Travel Wave', 'قدّم مع Travel Wave', '#visa-inquiry', 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png', 1, 'France Visa Services | Travel Wave', 'خدمات تأشيرة فرنسا | Travel Wave', 'Explore Travel Wave France visa support, required documents, steps, fees, FAQs, and inquiry options in a premium reusable visa template.', 'اكتشف خدمات Travel Wave لتأشيرة فرنسا والمستندات والخطوات والرسوم والأسئلة الشائعة ونموذج الاستفسار في قالب تأشيرات قابل لإعادة الاستخدام.', 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png', 1, 1, 1, '2026-03-27 23:51:36', '2026-03-27 23:59:15', '2026-03-27 23:59:15', 3);

-- --------------------------------------------------------

--
-- Table structure for table `workflow_automations`
--

CREATE TABLE `workflow_automations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `trigger_type` varchar(255) NOT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`conditions`)),
  `actions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`actions`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `priority` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `run_once` tinyint(1) NOT NULL DEFAULT 0,
  `cooldown_minutes` int(10) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `last_executed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_automations`
--

INSERT INTO `workflow_automations` (`id`, `name`, `description`, `trigger_type`, `entity_type`, `conditions`, `actions`, `is_active`, `priority`, `run_once`, `cooldown_minutes`, `created_by`, `updated_by`, `last_executed_at`, `created_at`, `updated_at`) VALUES
(1, 'Overdue task reminder', NULL, 'task_overdue', 'task', '{\"min_overdue_days\":1}', '{\"send_notification\":{\"recipient_mode\":\"linked_owner\",\"severity\":\"warning\",\"title_en\":\"Task is overdue\",\"message_en\":\"Please review {task_title}\"}}', 1, 100, 0, NULL, 1, NULL, NULL, '2026-03-25 20:41:09', '2026-03-25 20:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `workflow_execution_logs`
--

CREATE TABLE `workflow_execution_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workflow_automation_id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trigger_type` varchar(255) NOT NULL,
  `execution_status` varchar(255) NOT NULL,
  `target_label` varchar(255) DEFAULT NULL,
  `result_summary` text DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `executed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workflow_execution_logs`
--

INSERT INTO `workflow_execution_logs` (`id`, `workflow_automation_id`, `entity_type`, `entity_id`, `trigger_type`, `execution_status`, `target_label`, `result_summary`, `error_message`, `context`, `executed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'task', 1, 'task_overdue', 'failed', 'Collect missing document', 'Execution failed.', 'Undefined array key \"actor\"', '{\"overdue_days\":1}', '2026-03-25 20:41:09', '2026-03-25 20:41:09', '2026-03-25 20:41:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_customer_accounts`
--
ALTER TABLE `accounting_customer_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acct_customer_accounts_inquiry_unique` (`inquiry_id`),
  ADD UNIQUE KEY `acct_customer_accounts_customer_unique` (`crm_customer_id`),
  ADD KEY `acct_customer_accounts_seller_fk` (`assigned_user_id`),
  ADD KEY `acct_customer_accounts_creator_fk` (`created_by`);

--
-- Indexes for table `accounting_customer_expenses`
--
ALTER TABLE `accounting_customer_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acct_customer_expenses_account_fk` (`accounting_customer_account_id`),
  ADD KEY `acct_customer_expenses_cat_fk` (`accounting_expense_category_id`),
  ADD KEY `acct_customer_expenses_subcat_fk` (`accounting_expense_subcategory_id`),
  ADD KEY `acct_customer_expenses_creator_fk` (`created_by`),
  ADD KEY `acct_customer_expenses_treasury_fk` (`accounting_treasury_id`);

--
-- Indexes for table `accounting_customer_payments`
--
ALTER TABLE `accounting_customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acct_customer_payments_account_fk` (`accounting_customer_account_id`),
  ADD KEY `acct_customer_payments_creator_fk` (`created_by`),
  ADD KEY `acct_customer_payments_treasury_fk` (`accounting_treasury_id`);

--
-- Indexes for table `accounting_employee_transactions`
--
ALTER TABLE `accounting_employee_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acct_employee_transactions_user_fk` (`user_id`),
  ADD KEY `acct_employee_transactions_creator_fk` (`created_by`),
  ADD KEY `acct_employee_transactions_treasury_fk` (`accounting_treasury_id`);

--
-- Indexes for table `accounting_expense_categories`
--
ALTER TABLE `accounting_expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_expense_categories_slug_unique` (`slug`);

--
-- Indexes for table `accounting_expense_subcategories`
--
ALTER TABLE `accounting_expense_subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_expense_subcategories_slug_unique` (`slug`),
  ADD KEY `acct_expense_subcategories_cat_fk` (`accounting_expense_category_id`);

--
-- Indexes for table `accounting_general_expenses`
--
ALTER TABLE `accounting_general_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acct_general_expenses_cat_fk` (`accounting_general_expense_category_id`),
  ADD KEY `acct_general_expenses_creator_fk` (`created_by`),
  ADD KEY `acct_general_expenses_treasury_fk` (`accounting_treasury_id`);

--
-- Indexes for table `accounting_general_expense_categories`
--
ALTER TABLE `accounting_general_expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `accounting_general_expense_categories_slug_unique` (`slug`);

--
-- Indexes for table `accounting_treasuries`
--
ALTER TABLE `accounting_treasuries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acct_treasuries_creator_fk` (`created_by`);

--
-- Indexes for table `accounting_treasury_transactions`
--
ALTER TABLE `accounting_treasury_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounting_treasury_transactions_related_type_related_id_index` (`related_type`,`related_id`),
  ADD KEY `acct_treasury_transactions_creator_fk` (`created_by`),
  ADD KEY `acct_treasury_transactions_treasury_date_idx` (`accounting_treasury_id`,`transaction_date`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_logs_module_action_type_index` (`module`,`action_type`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_categories_slug_unique` (`slug`),
  ADD KEY `blog_categories_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blog_posts_slug_unique` (`slug`),
  ADD KEY `blog_posts_blog_category_id_foreign` (`blog_category_id`),
  ADD KEY `blog_posts_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `chatbot_interactions`
--
ALTER TABLE `chatbot_interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chatbot_interactions_was_answered_created_at_index` (`was_answered`,`created_at`),
  ADD KEY `chatbot_interactions_locale_created_at_index` (`locale`,`created_at`);

--
-- Indexes for table `chatbot_knowledge_entries`
--
ALTER TABLE `chatbot_knowledge_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chatbot_knowledge_entries_is_active_priority_index` (`is_active`,`priority`),
  ADD KEY `chatbot_knowledge_entries_category_en_is_active_index` (`category_en`,`is_active`),
  ADD KEY `chatbot_knowledge_entries_category_ar_is_active_index` (`category_ar`,`is_active`),
  ADD KEY `chatbot_knowledge_entries_created_by_foreign` (`created_by`),
  ADD KEY `chatbot_knowledge_entries_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `chatbot_knowledge_items`
--
ALTER TABLE `chatbot_knowledge_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chatbot_knowledge_items_source_type_locale_index` (`source_type`,`locale`),
  ADD KEY `chatbot_knowledge_items_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `chatbot_knowledge_items_source_key_locale_index` (`source_key`,`locale`);

--
-- Indexes for table `commission_statements`
--
ALTER TABLE `commission_statements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commission_statements_unique_period` (`user_id`,`basis_type`,`period_start`,`period_end`),
  ADD KEY `commission_statements_created_by_foreign` (`created_by`),
  ADD KEY `commission_statements_status_period_idx` (`payment_status`,`period_start`,`period_end`);

--
-- Indexes for table `crm_customers`
--
ALTER TABLE `crm_customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_customers_inquiry_unique` (`inquiry_id`),
  ADD UNIQUE KEY `crm_customers_customer_code_unique` (`customer_code`),
  ADD KEY `crm_customers_source_fk` (`crm_source_id`),
  ADD KEY `crm_customers_service_type_fk` (`crm_service_type_id`),
  ADD KEY `crm_customers_service_subtype_fk` (`crm_service_subtype_id`),
  ADD KEY `crm_customers_seller_fk` (`assigned_user_id`),
  ADD KEY `crm_customers_creator_fk` (`created_by`);

--
-- Indexes for table `crm_customer_activities`
--
ALTER TABLE `crm_customer_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_customer_activities_customer_fk` (`crm_customer_id`),
  ADD KEY `crm_customer_activities_user_fk` (`user_id`);

--
-- Indexes for table `crm_documents`
--
ALTER TABLE `crm_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  ADD KEY `crm_documents_category_uploaded_idx` (`crm_document_category_id`,`uploaded_at`),
  ADD KEY `crm_documents_uploader_uploaded_idx` (`uploaded_by`,`uploaded_at`),
  ADD KEY `crm_documents_status_expiry_idx` (`status`,`expiry_date`);

--
-- Indexes for table `crm_document_categories`
--
ALTER TABLE `crm_document_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_document_categories_slug_unique` (`slug`);

--
-- Indexes for table `crm_follow_ups`
--
ALTER TABLE `crm_follow_ups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_follow_ups_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `crm_follow_ups_crm_status_id_foreign` (`crm_status_id`),
  ADD KEY `crm_follow_ups_assigned_user_id_foreign` (`assigned_user_id`),
  ADD KEY `crm_follow_ups_created_by_foreign` (`created_by`),
  ADD KEY `crm_follow_ups_completed_by_foreign` (`completed_by`);

--
-- Indexes for table `crm_information`
--
ALTER TABLE `crm_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_information_created_by_foreign` (`created_by`);

--
-- Indexes for table `crm_information_recipients`
--
ALTER TABLE `crm_information_recipients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_information_recipients_crm_information_id_user_id_unique` (`crm_information_id`,`user_id`),
  ADD KEY `crm_information_recipients_user_id_foreign` (`user_id`);

--
-- Indexes for table `crm_lead_assignments`
--
ALTER TABLE `crm_lead_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_lead_assignments_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `crm_lead_assignments_old_assigned_user_id_foreign` (`old_assigned_user_id`),
  ADD KEY `crm_lead_assignments_new_assigned_user_id_foreign` (`new_assigned_user_id`),
  ADD KEY `crm_lead_assignments_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `crm_lead_notes`
--
ALTER TABLE `crm_lead_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_lead_notes_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `crm_lead_notes_user_id_foreign` (`user_id`);

--
-- Indexes for table `crm_lead_sources`
--
ALTER TABLE `crm_lead_sources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_lead_sources_slug_unique` (`slug`);

--
-- Indexes for table `crm_service_subtypes`
--
ALTER TABLE `crm_service_subtypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_service_subtypes_slug_unique` (`slug`),
  ADD KEY `crm_service_subtypes_crm_service_type_id_foreign` (`crm_service_type_id`);

--
-- Indexes for table `crm_service_types`
--
ALTER TABLE `crm_service_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_service_types_slug_unique` (`slug`);

--
-- Indexes for table `crm_statuses`
--
ALTER TABLE `crm_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `crm_statuses_slug_unique` (`slug`);

--
-- Indexes for table `crm_status_updates`
--
ALTER TABLE `crm_status_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_status_updates_inquiry_id_foreign` (`inquiry_id`),
  ADD KEY `crm_status_updates_old_status_id_foreign` (`old_status_id`),
  ADD KEY `crm_status_updates_new_status_id_foreign` (`new_status_id`),
  ADD KEY `crm_status_updates_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `crm_tasks`
--
ALTER TABLE `crm_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_tasks_assigned_user_id_foreign` (`assigned_user_id`),
  ADD KEY `crm_tasks_created_by_foreign` (`created_by`),
  ADD KEY `crm_tasks_inquiry_fk` (`inquiry_id`),
  ADD KEY `crm_tasks_closed_by_fk` (`closed_by`);

--
-- Indexes for table `crm_task_activities`
--
ALTER TABLE `crm_task_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crm_task_activities_task_fk` (`crm_task_id`),
  ADD KEY `crm_task_activities_user_fk` (`user_id`),
  ADD KEY `crm_task_activities_action_idx` (`action_type`,`created_at`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `destinations_slug_unique` (`slug`),
  ADD KEY `destinations_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `goal_targets`
--
ALTER TABLE `goal_targets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goal_targets_created_by_foreign` (`created_by`),
  ADD KEY `goal_targets_user_type_idx` (`user_id`,`target_type`),
  ADD KEY `goal_targets_period_idx` (`period_start`,`period_end`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `home_country_strip_items`
--
ALTER TABLE `home_country_strip_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `home_country_strip_items_visa_country_id_foreign` (`visa_country_id`),
  ADD KEY `home_country_strip_items_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inquiries_lead_form_id_foreign` (`lead_form_id`),
  ADD KEY `inquiries_lead_form_assignment_id_foreign` (`lead_form_assignment_id`),
  ADD KEY `inquiries_marketing_landing_page_id_foreign` (`marketing_landing_page_id`),
  ADD KEY `inquiries_crm_status_id_foreign` (`crm_status_id`),
  ADD KEY `inquiries_crm_status2_id_foreign` (`crm_status2_id`),
  ADD KEY `inquiries_assigned_user_id_foreign` (`assigned_user_id`),
  ADD KEY `inquiries_status_1_updated_by_foreign` (`status_1_updated_by`),
  ADD KEY `inquiries_status_2_updated_by_foreign` (`status_2_updated_by`),
  ADD KEY `inquiries_crm_source_id_foreign` (`crm_source_id`),
  ADD KEY `inquiries_crm_status_updated_by_foreign` (`crm_status_updated_by`),
  ADD KEY `inquiries_deleted_by_foreign` (`deleted_by`),
  ADD KEY `inquiries_crm_service_type_id_foreign` (`crm_service_type_id`),
  ADD KEY `inquiries_crm_service_subtype_id_foreign` (`crm_service_subtype_id`),
  ADD KEY `inquiries_utm_campaign_id_foreign` (`utm_campaign_id`);

--
-- Indexes for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_base_articles_slug_unique` (`slug`),
  ADD KEY `knowledge_base_articles_created_by_foreign` (`created_by`),
  ADD KEY `knowledge_base_articles_updated_by_foreign` (`updated_by`),
  ADD KEY `knowledge_base_articles_status_visibility_scope_index` (`status`,`visibility_scope`),
  ADD KEY `knowledge_base_articles_knowledge_base_category_id_status_index` (`knowledge_base_category_id`,`status`);

--
-- Indexes for table `knowledge_base_categories`
--
ALTER TABLE `knowledge_base_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_base_categories_slug_unique` (`slug`);

--
-- Indexes for table `lead_forms`
--
ALTER TABLE `lead_forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lead_forms_slug_unique` (`slug`);

--
-- Indexes for table `lead_form_assignments`
--
ALTER TABLE `lead_form_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_form_assignments_lead_form_id_foreign` (`lead_form_id`),
  ADD KEY `lead_form_assignments_assignment_type_target_id_index` (`assignment_type`,`target_id`),
  ADD KEY `lead_form_assignments_assignment_type_target_key_index` (`assignment_type`,`target_key`);

--
-- Indexes for table `lead_form_fields`
--
ALTER TABLE `lead_form_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lead_form_fields_lead_form_id_foreign` (`lead_form_id`);

--
-- Indexes for table `map_sections`
--
ALTER TABLE `map_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `map_sections_slug_unique` (`slug`);

--
-- Indexes for table `map_section_assignments`
--
ALTER TABLE `map_section_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `map_section_assignments_map_section_id_foreign` (`map_section_id`);

--
-- Indexes for table `marketing_landing_pages`
--
ALTER TABLE `marketing_landing_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `marketing_landing_pages_slug_unique` (`slug`),
  ADD KEY `marketing_landing_pages_assigned_lead_form_id_foreign` (`assigned_lead_form_id`);

--
-- Indexes for table `marketing_landing_page_events`
--
ALTER TABLE `marketing_landing_page_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketing_landing_page_events_marketing_landing_page_id_foreign` (`marketing_landing_page_id`);

--
-- Indexes for table `media_assets`
--
ALTER TABLE `media_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_assets_path_unique` (`path`),
  ADD KEY `media_assets_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_parent_id_foreign` (`parent_id`),
  ADD KEY `menu_items_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_key_unique` (`key`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`),
  ADD KEY `pages_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_role_permission_id_role_id_unique` (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_user_role_id_user_id_unique` (`role_id`,`user_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `seo_meta_entries`
--
ALTER TABLE `seo_meta_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seo_meta_entries_target_type_target_id_unique` (`target_type`,`target_id`);

--
-- Indexes for table `seo_redirects`
--
ALTER TABLE `seo_redirects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seo_redirects_source_path_unique` (`source_path`);

--
-- Indexes for table `seo_settings`
--
ALTER TABLE `seo_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `testimonials_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `tracking_integrations`
--
ALTER TABLE `tracking_integrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_integrations_slug_unique` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_permission_overrides`
--
ALTER TABLE `user_permission_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_permission_overrides_user_id_permission_id_unique` (`user_id`,`permission_id`),
  ADD KEY `user_permission_overrides_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `utm_campaigns`
--
ALTER TABLE `utm_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utm_campaigns_owner_user_id_foreign` (`owner_user_id`),
  ADD KEY `utm_campaigns_created_by_foreign` (`created_by`),
  ADD KEY `utm_campaigns_source_medium_campaign_idx` (`utm_source`,`utm_medium`,`utm_campaign`),
  ADD KEY `utm_campaigns_status_owner_idx` (`status`,`owner_user_id`);

--
-- Indexes for table `utm_visits`
--
ALTER TABLE `utm_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utm_visits_utm_campaign_id_foreign` (`utm_campaign_id`),
  ADD KEY `utm_visits_visited_at_index` (`visited_at`),
  ADD KEY `utm_visits_session_key_index` (`session_key`),
  ADD KEY `utm_visits_source_medium_idx` (`utm_source`,`utm_medium`),
  ADD KEY `utm_visits_utm_campaign_index` (`utm_campaign`);

--
-- Indexes for table `visa_categories`
--
ALTER TABLE `visa_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visa_categories_slug_unique` (`slug`),
  ADD KEY `visa_categories_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `visa_countries`
--
ALTER TABLE `visa_countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visa_countries_slug_unique` (`slug`),
  ADD KEY `visa_countries_visa_category_id_foreign` (`visa_category_id`),
  ADD KEY `visa_countries_deleted_by_foreign` (`deleted_by`);

--
-- Indexes for table `workflow_automations`
--
ALTER TABLE `workflow_automations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_automations_created_by_foreign` (`created_by`),
  ADD KEY `workflow_automations_updated_by_foreign` (`updated_by`),
  ADD KEY `workflow_automations_trigger_type_is_active_priority_index` (`trigger_type`,`is_active`,`priority`),
  ADD KEY `workflow_automations_entity_type_is_active_index` (`entity_type`,`is_active`);

--
-- Indexes for table `workflow_execution_logs`
--
ALTER TABLE `workflow_execution_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workflow_execution_logs_workflow_automation_id_executed_at_index` (`workflow_automation_id`,`executed_at`),
  ADD KEY `workflow_execution_logs_trigger_type_execution_status_index` (`trigger_type`,`execution_status`),
  ADD KEY `workflow_execution_logs_entity_type_entity_id_index` (`entity_type`,`entity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_customer_accounts`
--
ALTER TABLE `accounting_customer_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_customer_expenses`
--
ALTER TABLE `accounting_customer_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_customer_payments`
--
ALTER TABLE `accounting_customer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_employee_transactions`
--
ALTER TABLE `accounting_employee_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_expense_categories`
--
ALTER TABLE `accounting_expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `accounting_expense_subcategories`
--
ALTER TABLE `accounting_expense_subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `accounting_general_expenses`
--
ALTER TABLE `accounting_general_expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_general_expense_categories`
--
ALTER TABLE `accounting_general_expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `accounting_treasuries`
--
ALTER TABLE `accounting_treasuries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `accounting_treasury_transactions`
--
ALTER TABLE `accounting_treasury_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chatbot_interactions`
--
ALTER TABLE `chatbot_interactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chatbot_knowledge_entries`
--
ALTER TABLE `chatbot_knowledge_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chatbot_knowledge_items`
--
ALTER TABLE `chatbot_knowledge_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;

--
-- AUTO_INCREMENT for table `commission_statements`
--
ALTER TABLE `commission_statements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_customers`
--
ALTER TABLE `crm_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_customer_activities`
--
ALTER TABLE `crm_customer_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_documents`
--
ALTER TABLE `crm_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_document_categories`
--
ALTER TABLE `crm_document_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `crm_follow_ups`
--
ALTER TABLE `crm_follow_ups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_information`
--
ALTER TABLE `crm_information`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_information_recipients`
--
ALTER TABLE `crm_information_recipients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_lead_assignments`
--
ALTER TABLE `crm_lead_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_lead_notes`
--
ALTER TABLE `crm_lead_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_lead_sources`
--
ALTER TABLE `crm_lead_sources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `crm_service_subtypes`
--
ALTER TABLE `crm_service_subtypes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `crm_service_types`
--
ALTER TABLE `crm_service_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `crm_statuses`
--
ALTER TABLE `crm_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `crm_status_updates`
--
ALTER TABLE `crm_status_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crm_tasks`
--
ALTER TABLE `crm_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `crm_task_activities`
--
ALTER TABLE `crm_task_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goal_targets`
--
ALTER TABLE `goal_targets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `home_country_strip_items`
--
ALTER TABLE `home_country_strip_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_base_categories`
--
ALTER TABLE `knowledge_base_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lead_forms`
--
ALTER TABLE `lead_forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `lead_form_assignments`
--
ALTER TABLE `lead_form_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lead_form_fields`
--
ALTER TABLE `lead_form_fields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `map_sections`
--
ALTER TABLE `map_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `map_section_assignments`
--
ALTER TABLE `map_section_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `marketing_landing_pages`
--
ALTER TABLE `marketing_landing_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketing_landing_page_events`
--
ALTER TABLE `marketing_landing_page_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media_assets`
--
ALTER TABLE `media_assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `seo_meta_entries`
--
ALTER TABLE `seo_meta_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo_redirects`
--
ALTER TABLE `seo_redirects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo_settings`
--
ALTER TABLE `seo_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tracking_integrations`
--
ALTER TABLE `tracking_integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_permission_overrides`
--
ALTER TABLE `user_permission_overrides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utm_campaigns`
--
ALTER TABLE `utm_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utm_visits`
--
ALTER TABLE `utm_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visa_categories`
--
ALTER TABLE `visa_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visa_countries`
--
ALTER TABLE `visa_countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `workflow_automations`
--
ALTER TABLE `workflow_automations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `workflow_execution_logs`
--
ALTER TABLE `workflow_execution_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounting_customer_accounts`
--
ALTER TABLE `accounting_customer_accounts`
  ADD CONSTRAINT `acct_customer_accounts_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_customer_accounts_customer_fk` FOREIGN KEY (`crm_customer_id`) REFERENCES `crm_customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_customer_accounts_inquiry_fk` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acct_customer_accounts_seller_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_customer_expenses`
--
ALTER TABLE `accounting_customer_expenses`
  ADD CONSTRAINT `acct_customer_expenses_account_fk` FOREIGN KEY (`accounting_customer_account_id`) REFERENCES `accounting_customer_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acct_customer_expenses_cat_fk` FOREIGN KEY (`accounting_expense_category_id`) REFERENCES `accounting_expense_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acct_customer_expenses_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_customer_expenses_subcat_fk` FOREIGN KEY (`accounting_expense_subcategory_id`) REFERENCES `accounting_expense_subcategories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_customer_expenses_treasury_fk` FOREIGN KEY (`accounting_treasury_id`) REFERENCES `accounting_treasuries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_customer_payments`
--
ALTER TABLE `accounting_customer_payments`
  ADD CONSTRAINT `acct_customer_payments_account_fk` FOREIGN KEY (`accounting_customer_account_id`) REFERENCES `accounting_customer_accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acct_customer_payments_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_customer_payments_treasury_fk` FOREIGN KEY (`accounting_treasury_id`) REFERENCES `accounting_treasuries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_employee_transactions`
--
ALTER TABLE `accounting_employee_transactions`
  ADD CONSTRAINT `acct_employee_transactions_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_employee_transactions_treasury_fk` FOREIGN KEY (`accounting_treasury_id`) REFERENCES `accounting_treasuries` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_employee_transactions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `accounting_expense_subcategories`
--
ALTER TABLE `accounting_expense_subcategories`
  ADD CONSTRAINT `acct_expense_subcategories_cat_fk` FOREIGN KEY (`accounting_expense_category_id`) REFERENCES `accounting_expense_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `accounting_general_expenses`
--
ALTER TABLE `accounting_general_expenses`
  ADD CONSTRAINT `acct_general_expenses_cat_fk` FOREIGN KEY (`accounting_general_expense_category_id`) REFERENCES `accounting_general_expense_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `acct_general_expenses_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_general_expenses_treasury_fk` FOREIGN KEY (`accounting_treasury_id`) REFERENCES `accounting_treasuries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_treasuries`
--
ALTER TABLE `accounting_treasuries`
  ADD CONSTRAINT `acct_treasuries_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `accounting_treasury_transactions`
--
ALTER TABLE `accounting_treasury_transactions`
  ADD CONSTRAINT `acct_treasury_transactions_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `acct_treasury_transactions_treasury_fk` FOREIGN KEY (`accounting_treasury_id`) REFERENCES `accounting_treasuries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD CONSTRAINT `blog_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_blog_category_id_foreign` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `blog_posts_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chatbot_knowledge_entries`
--
ALTER TABLE `chatbot_knowledge_entries`
  ADD CONSTRAINT `chatbot_knowledge_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chatbot_knowledge_entries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `commission_statements`
--
ALTER TABLE `commission_statements`
  ADD CONSTRAINT `commission_statements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commission_statements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_customers`
--
ALTER TABLE `crm_customers`
  ADD CONSTRAINT `crm_customers_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_customers_inquiry_fk` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_customers_seller_fk` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_customers_service_subtype_fk` FOREIGN KEY (`crm_service_subtype_id`) REFERENCES `crm_service_subtypes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_customers_service_type_fk` FOREIGN KEY (`crm_service_type_id`) REFERENCES `crm_service_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_customers_source_fk` FOREIGN KEY (`crm_source_id`) REFERENCES `crm_lead_sources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_customer_activities`
--
ALTER TABLE `crm_customer_activities`
  ADD CONSTRAINT `crm_customer_activities_customer_fk` FOREIGN KEY (`crm_customer_id`) REFERENCES `crm_customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_customer_activities_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_documents`
--
ALTER TABLE `crm_documents`
  ADD CONSTRAINT `crm_documents_crm_document_category_id_foreign` FOREIGN KEY (`crm_document_category_id`) REFERENCES `crm_document_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_follow_ups`
--
ALTER TABLE `crm_follow_ups`
  ADD CONSTRAINT `crm_follow_ups_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_follow_ups_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_follow_ups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_follow_ups_crm_status_id_foreign` FOREIGN KEY (`crm_status_id`) REFERENCES `crm_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_follow_ups_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_information`
--
ALTER TABLE `crm_information`
  ADD CONSTRAINT `crm_information_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_information_recipients`
--
ALTER TABLE `crm_information_recipients`
  ADD CONSTRAINT `crm_information_recipients_crm_information_id_foreign` FOREIGN KEY (`crm_information_id`) REFERENCES `crm_information` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_information_recipients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_lead_assignments`
--
ALTER TABLE `crm_lead_assignments`
  ADD CONSTRAINT `crm_lead_assignments_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_lead_assignments_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_lead_assignments_new_assigned_user_id_foreign` FOREIGN KEY (`new_assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_lead_assignments_old_assigned_user_id_foreign` FOREIGN KEY (`old_assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_lead_notes`
--
ALTER TABLE `crm_lead_notes`
  ADD CONSTRAINT `crm_lead_notes_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_lead_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_service_subtypes`
--
ALTER TABLE `crm_service_subtypes`
  ADD CONSTRAINT `crm_service_subtypes_crm_service_type_id_foreign` FOREIGN KEY (`crm_service_type_id`) REFERENCES `crm_service_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crm_status_updates`
--
ALTER TABLE `crm_status_updates`
  ADD CONSTRAINT `crm_status_updates_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_status_updates_inquiry_id_foreign` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_status_updates_new_status_id_foreign` FOREIGN KEY (`new_status_id`) REFERENCES `crm_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_status_updates_old_status_id_foreign` FOREIGN KEY (`old_status_id`) REFERENCES `crm_statuses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_tasks`
--
ALTER TABLE `crm_tasks`
  ADD CONSTRAINT `crm_tasks_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_tasks_closed_by_fk` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `crm_tasks_inquiry_fk` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crm_task_activities`
--
ALTER TABLE `crm_task_activities`
  ADD CONSTRAINT `crm_task_activities_task_fk` FOREIGN KEY (`crm_task_id`) REFERENCES `crm_tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crm_task_activities_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `destinations`
--
ALTER TABLE `destinations`
  ADD CONSTRAINT `destinations_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `goal_targets`
--
ALTER TABLE `goal_targets`
  ADD CONSTRAINT `goal_targets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `goal_targets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `home_country_strip_items`
--
ALTER TABLE `home_country_strip_items`
  ADD CONSTRAINT `home_country_strip_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `home_country_strip_items_visa_country_id_foreign` FOREIGN KEY (`visa_country_id`) REFERENCES `visa_countries` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_assigned_user_id_foreign` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_crm_service_subtype_id_foreign` FOREIGN KEY (`crm_service_subtype_id`) REFERENCES `crm_service_subtypes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_crm_service_type_id_foreign` FOREIGN KEY (`crm_service_type_id`) REFERENCES `crm_service_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_crm_source_id_foreign` FOREIGN KEY (`crm_source_id`) REFERENCES `crm_lead_sources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_crm_status2_id_foreign` FOREIGN KEY (`crm_status2_id`) REFERENCES `crm_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_crm_status_id_foreign` FOREIGN KEY (`crm_status_id`) REFERENCES `crm_statuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_crm_status_updated_by_foreign` FOREIGN KEY (`crm_status_updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_lead_form_assignment_id_foreign` FOREIGN KEY (`lead_form_assignment_id`) REFERENCES `lead_form_assignments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_lead_form_id_foreign` FOREIGN KEY (`lead_form_id`) REFERENCES `lead_forms` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_marketing_landing_page_id_foreign` FOREIGN KEY (`marketing_landing_page_id`) REFERENCES `marketing_landing_pages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_status_1_updated_by_foreign` FOREIGN KEY (`status_1_updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_status_2_updated_by_foreign` FOREIGN KEY (`status_2_updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inquiries_utm_campaign_id_foreign` FOREIGN KEY (`utm_campaign_id`) REFERENCES `utm_campaigns` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `knowledge_base_articles`
--
ALTER TABLE `knowledge_base_articles`
  ADD CONSTRAINT `knowledge_base_articles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `knowledge_base_articles_knowledge_base_category_id_foreign` FOREIGN KEY (`knowledge_base_category_id`) REFERENCES `knowledge_base_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `knowledge_base_articles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lead_form_assignments`
--
ALTER TABLE `lead_form_assignments`
  ADD CONSTRAINT `lead_form_assignments_lead_form_id_foreign` FOREIGN KEY (`lead_form_id`) REFERENCES `lead_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lead_form_fields`
--
ALTER TABLE `lead_form_fields`
  ADD CONSTRAINT `lead_form_fields_lead_form_id_foreign` FOREIGN KEY (`lead_form_id`) REFERENCES `lead_forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `map_section_assignments`
--
ALTER TABLE `map_section_assignments`
  ADD CONSTRAINT `map_section_assignments_map_section_id_foreign` FOREIGN KEY (`map_section_id`) REFERENCES `map_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marketing_landing_pages`
--
ALTER TABLE `marketing_landing_pages`
  ADD CONSTRAINT `marketing_landing_pages_assigned_lead_form_id_foreign` FOREIGN KEY (`assigned_lead_form_id`) REFERENCES `lead_forms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `marketing_landing_page_events`
--
ALTER TABLE `marketing_landing_page_events`
  ADD CONSTRAINT `marketing_landing_page_events_marketing_landing_page_id_foreign` FOREIGN KEY (`marketing_landing_page_id`) REFERENCES `marketing_landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `media_assets`
--
ALTER TABLE `media_assets`
  ADD CONSTRAINT `media_assets_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `menu_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_permission_overrides`
--
ALTER TABLE `user_permission_overrides`
  ADD CONSTRAINT `user_permission_overrides_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permission_overrides_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `utm_campaigns`
--
ALTER TABLE `utm_campaigns`
  ADD CONSTRAINT `utm_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `utm_campaigns_owner_user_id_foreign` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `utm_visits`
--
ALTER TABLE `utm_visits`
  ADD CONSTRAINT `utm_visits_utm_campaign_id_foreign` FOREIGN KEY (`utm_campaign_id`) REFERENCES `utm_campaigns` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `visa_categories`
--
ALTER TABLE `visa_categories`
  ADD CONSTRAINT `visa_categories_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `visa_countries`
--
ALTER TABLE `visa_countries`
  ADD CONSTRAINT `visa_countries_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visa_countries_visa_category_id_foreign` FOREIGN KEY (`visa_category_id`) REFERENCES `visa_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workflow_automations`
--
ALTER TABLE `workflow_automations`
  ADD CONSTRAINT `workflow_automations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `workflow_automations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `workflow_execution_logs`
--
ALTER TABLE `workflow_execution_logs`
  ADD CONSTRAINT `workflow_execution_logs_workflow_automation_id_foreign` FOREIGN KEY (`workflow_automation_id`) REFERENCES `workflow_automations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
