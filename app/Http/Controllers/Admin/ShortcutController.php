<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ShortcutController extends Controller
{
    public static function getCategories(): array
    {
        return [
            'crm' => [
                'title_ar' => 'إدارة العملاء (CRM)',
                'icon' => '📋',
                'description' => 'جميع أدوات المبيعات والعملاء والمتابعات والحالات والتقارير الخاص بالـ CRM',
            ],
            'forms' => [
                'title_ar' => 'النماذج والعملاء (Forms & Submissions)',
                'icon' => '📥',
                'description' => 'إدارة النماذج التفاعلية وليدز استمارات الموقع الإلكتروني',
            ],
            'accounting' => [
                'title_ar' => 'الحسابات والمالية (Accounting)',
                'icon' => '💰',
                'description' => 'الخزائن، المصروفات، حسابات العملاء والموظفين، والشجرة المالية',
            ],
            'content' => [
                'title_ar' => 'المحتوى والخدمات والمواعيد',
                'icon' => '🌐',
                'description' => 'صفحات الموقع، قاعدة بيانات التأشيرات، مواعيد السفارات، والوسائط',
            ],
            'marketing' => [
                'title_ar' => 'التسويق والحملات والـ Funnels',
                'icon' => '🎯',
                'description' => 'منشئ لاندنج بيج، تحليلات UTM، مدير البوب اب، والربط مع Zapier',
            ],
            'users' => [
                'title_ar' => 'المستخدمين والصلاحيات',
                'icon' => '👥',
                'description' => 'إدارة الموظفين والأدوار وجدول الصلاحيات بالنظام',
            ],
            'system' => [
                'title_ar' => 'إعدادات النظام والرقابة',
                'icon' => '⚙️',
                'description' => 'إدارة موديولات النظام، إعدادات البراند، وسجل التغييرات والتدقيق',
            ],
        ];
    }

    public static function getAvailableShortcuts(): array
    {
        return [
            // CRM Group (إدارة العملاء)
            'crm_dashboard' => [
                'id' => 'crm_dashboard',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'إدارة العملاء (CRM Dashboard)',
                'description_ar' => 'عرض إحصائيات المبيعات، معدل التحويل، ونظرة عامة على العملاء.',
                'icon' => '📊',
                'route' => 'admin.crm.dashboard',
                'permission' => 'leads.view',
                'badge_color' => 'primary',
            ],
            'crm_leads' => [
                'id' => 'crm_leads',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'العملاء المحتملون (Inquiries / Leads)',
                'description_ar' => 'جدول وعرض كافة العملاء المحتملين والبحث والتصفية.',
                'icon' => '👥',
                'route' => 'admin.crm.leads.index',
                'permission' => 'leads.view',
                'badge_color' => 'primary',
            ],
            'crm_lead_create' => [
                'id' => 'crm_lead_create',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'إضافة ليد جديد',
                'description_ar' => 'إدخال عميل محتمل جديد مباشرة إلى النظام وتحديد بياناته.',
                'icon' => '➕',
                'route' => 'admin.crm.leads.create',
                'permission' => 'leads.create',
                'badge_color' => 'success',
            ],
            'crm_customers' => [
                'id' => 'crm_customers',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'عملاء CRM (CRM Customers)',
                'description_ar' => 'قائمة العملاء المؤكدين والمفعلين بالنظام.',
                'icon' => '🏅',
                'route' => 'admin.crm.customers.index',
                'permission' => 'customers.view',
                'badge_color' => 'success',
            ],
            'crm_documents' => [
                'id' => 'crm_documents',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'المستندات (Documents)',
                'description_ar' => 'مكتبة مستندات وأوراق التأشيرات والعملاء.',
                'icon' => '📁',
                'route' => 'admin.documents.index',
                'permission' => 'documents.view',
                'badge_color' => 'info',
            ],
            'crm_lead_delayed' => [
                'id' => 'crm_lead_delayed',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'الليد المتأخر (Delayed Leads)',
                'description_ar' => 'متابعة العملاء المحتملين الذين مرت عليهم 48 ساعة دون اتخاذ إجراء.',
                'icon' => '⚠️',
                'route' => 'admin.crm.leads.delayed',
                'permission' => 'leads.view',
                'badge_color' => 'danger',
            ],
            'crm_pipeline' => [
                'id' => 'crm_pipeline',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'مسار المبيعات (Sales Pipeline)',
                'description_ar' => 'عرض مسار وعربات المبيعات وسحب وإسقاط الليدز.',
                'icon' => '🔄',
                'route' => 'admin.crm.pipeline',
                'permission' => 'leads.view',
                'badge_color' => 'warning',
            ],
            'crm_followups' => [
                'id' => 'crm_followups',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'المتابعات (Follow-ups)',
                'description_ar' => 'جدول المتابعات والمواعيد المحددة للتواصل مع العملاء.',
                'icon' => '📅',
                'route' => 'admin.crm.follow-ups',
                'permission' => 'leads.view',
                'badge_color' => 'primary',
            ],
            'crm_tasks' => [
                'id' => 'crm_tasks',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'المهام (Tasks)',
                'description_ar' => 'إدارة قائمة مهام المبيعات والتكليفات الموجهة.',
                'icon' => '✅',
                'route' => 'admin.crm.tasks.index',
                'permission' => 'leads.view',
                'badge_color' => 'dark',
            ],
            'crm_information' => [
                'id' => 'crm_information',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'معلومات (CRM Information)',
                'description_ar' => 'التعميمات والتوجيهات الإدارية الصادرة لفريق المبيعات.',
                'icon' => 'ℹ️',
                'route' => 'admin.crm.information.index',
                'permission' => 'leads.view',
                'badge_color' => 'info',
            ],
            'crm_statuses' => [
                'id' => 'crm_statuses',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'الحالات (Status Config)',
                'description_ar' => 'تعديل وتخصيص حالات العملاء وألوانها في الـ CRM.',
                'icon' => '🏷️',
                'route' => 'admin.crm.statuses',
                'permission' => 'leads.edit',
                'badge_color' => 'secondary',
            ],
            'crm_sources' => [
                'id' => 'crm_sources',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'المصادر (Sources Config)',
                'description_ar' => 'إدارة مصادر العملاء (فيسبوك، جوجل، توصية، الخ).',
                'icon' => '📢',
                'route' => 'admin.crm.sources',
                'permission' => 'leads.view',
                'badge_color' => 'secondary',
            ],
            'crm_service_types' => [
                'id' => 'crm_service_types',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'الأنواع (Service Types Config)',
                'description_ar' => 'إدارة أنواع الخدمات السياحية والخدمات الفرعية.',
                'icon' => '✈️',
                'route' => 'admin.crm.service-types',
                'permission' => 'leads.view',
                'badge_color' => 'secondary',
            ],
            'crm_deleted_leads' => [
                'id' => 'crm_deleted_leads',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'العملاء المحذوفون (Trash Leads)',
                'description_ar' => 'سلة مهملات الليدز واسترجاع أو حذف العملاء.',
                'icon' => '🗑️',
                'route' => 'admin.crm.leads.trash',
                'permission' => 'leads.delete',
                'badge_color' => 'danger',
            ],
            'reports_center' => [
                'id' => 'reports_center',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => '📊 مركز التقارير والتحليل الإداري (Reports Control Center)',
                'description_ar' => 'مركز التحليل الشامل لأداء الشركة، المبيعات، التسويق والماليات.',
                'icon' => '📊',
                'route' => 'admin.crm.reports-center.index',
                'permission' => 'reports.view',
                'badge_color' => 'dark',
            ],
            'crm_reports' => [
                'id' => 'crm_reports',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'التقارير (CRM Reports)',
                'description_ar' => 'تقارير أداء المبيعات ومعدلات الإغلاق.',
                'icon' => '📈',
                'route' => 'admin.crm.reports',
                'permission' => 'reports.view',
                'badge_color' => 'primary',
            ],
            'crm_reports2' => [
                'id' => 'crm_reports2',
                'category_key' => 'crm',
                'category_name' => 'إدارة العملاء (CRM)',
                'title_ar' => 'تقارير 2 (KPI Dashboard)',
                'description_ar' => 'تقارير ومؤشرات الأداء المتقدمة للبائعين.',
                'icon' => '📊',
                'route' => 'admin.crm.reports2',
                'permission' => 'reports.view',
                'badge_color' => 'primary',
            ],

            // Forms Group (النماذج والعملاء)
            'forms_submissions' => [
                'id' => 'forms_submissions',
                'category_key' => 'forms',
                'category_name' => 'النماذج والعملاء',
                'title_ar' => 'فورـم لـيـد (Form Leads)',
                'description_ar' => 'قائمة العملاء الجدد المسجلين عبر استمارات ونماذج الموقع.',
                'icon' => '📥',
                'route' => 'admin.forms.submissions',
                'permission' => 'forms.submissions.view',
                'badge_color' => 'info',
            ],
            'forms_manager' => [
                'id' => 'forms_manager',
                'category_key' => 'forms',
                'category_name' => 'النماذج والعملاء',
                'title_ar' => 'إدارة النماذج (Forms Manager)',
                'description_ar' => 'إنشاء وتخصيص استمارات النماذج التفاعلية وحقولها.',
                'icon' => '📑',
                'route' => 'admin.forms.index',
                'permission' => 'forms.manage',
                'badge_color' => 'secondary',
            ],

            // Accounting Group (الحسابات والمالية)
            'accounting_dashboard' => [
                'id' => 'accounting_dashboard',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'لوحة الحسابات والمالية',
                'description_ar' => 'متابعة المقبوضات، المصروفات، الخزائن، والسندات المالية.',
                'icon' => '💰',
                'route' => 'admin.accounting.dashboard',
                'permission' => 'accounting.view',
                'badge_color' => 'success',
            ],
            'accounting_customers' => [
                'id' => 'accounting_customers',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'حسابات العملاء والذمم',
                'description_ar' => 'متابعة كشوفات حسابات العملاء والمدفوعات والمستحقات.',
                'icon' => '👤',
                'route' => 'admin.accounting.customers.index',
                'permission' => 'accounting.view',
                'badge_color' => 'success',
            ],
            'accounting_treasuries' => [
                'id' => 'accounting_treasuries',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'الخزائن والصناديق',
                'description_ar' => 'إدارة الخزائن النقدية والحسابات البنكية وحركات الإيداع والصرف.',
                'icon' => '🏦',
                'route' => 'admin.accounting.treasuries.index',
                'permission' => 'accounting.view',
                'badge_color' => 'success',
            ],
            'accounting_expenses' => [
                'id' => 'accounting_expenses',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'المصروفات العامة',
                'description_ar' => 'تسجيل وإدارة مصروفات التشغيل والإيجارات والمشتريات.',
                'icon' => '💸',
                'route' => 'admin.accounting.general-expenses.index',
                'permission' => 'accounting.view',
                'badge_color' => 'warning',
            ],
            'accounting_employees' => [
                'id' => 'accounting_employees',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'حسابات الموظفين والرواتب',
                'description_ar' => 'متابعة سلف الموظفين والرواتب والمستحقات المالية.',
                'icon' => '👷',
                'route' => 'admin.accounting.employees.index',
                'permission' => 'accounting.view',
                'badge_color' => 'info',
            ],
            'accounting_accounts' => [
                'id' => 'accounting_accounts',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'شجرة الحسابات (Chart of Accounts)',
                'description_ar' => 'عرض الهيكل المالي للأصول، الخصوم، الإيرادات، والمصروفات.',
                'icon' => '🌳',
                'route' => 'admin.accounting.accounts.index',
                'permission' => 'accounting.view',
                'badge_color' => 'dark',
            ],
            'accounting_reports' => [
                'id' => 'accounting_reports',
                'category_key' => 'accounting',
                'category_name' => 'الحسابات والمالية',
                'title_ar' => 'التقارير المالية والربحية',
                'description_ar' => 'قائمة الدخل، ميزان المراجعة، وتقارير أرباح الخزائن.',
                'icon' => '📊',
                'route' => 'admin.accounting.reports',
                'permission' => 'accounting.reports.view',
                'badge_color' => 'primary',
            ],

            // Content & Services Group (المحتوى والخدمات والمواعيد)
            'pages_manager' => [
                'id' => 'pages_manager',
                'category_key' => 'content',
                'category_name' => 'المحتوى والخدمات والمواعيد',
                'title_ar' => 'إدارة صفحات الموقع (Pages)',
                'description_ar' => 'تعديل وإنشاء صفحات الموقع الإلكتروني والمحتوى.',
                'icon' => '📄',
                'route' => 'admin.pages.index',
                'permission' => 'pages.view',
                'badge_color' => 'info',
            ],
            'media_library' => [
                'id' => 'media_library',
                'category_key' => 'content',
                'category_name' => 'المحتوى والخدمات والمواعيد',
                'title_ar' => 'مكتبة الوسائط والصور',
                'description_ar' => 'رفع وتصفح وإدارة الصور والملفات في الموقع.',
                'icon' => '🖼️',
                'route' => 'admin.media-library.index',
                'permission' => 'media.manage',
                'badge_color' => 'info',
            ],
            'visa_database' => [
                'id' => 'visa_database',
                'category_key' => 'content',
                'category_name' => 'المحتوى والخدمات والمواعيد',
                'title_ar' => 'قاعدة بيانات التأشيرات (Visa Database)',
                'description_ar' => 'متطلبات وأسعار وشروط التأشيرات والدول.',
                'icon' => '🌐',
                'route' => 'admin.visa-database.index',
                'permission' => 'visa_database.view',
                'badge_color' => 'primary',
            ],
            'embassy_appointments' => [
                'id' => 'embassy_appointments',
                'category_key' => 'content',
                'category_name' => 'المحتوى والخدمات والمواعيد',
                'title_ar' => 'مواعيد السفارات (Embassy Appointments)',
                'description_ar' => 'إدارة كشوف ومواعيد السفارات والـ Visa Appointments.',
                'icon' => '🏛️',
                'route' => 'admin.embassy-appointments.index',
                'permission' => 'embassy_appointments.view',
                'badge_color' => 'warning',
            ],
            'destinations_manager' => [
                'id' => 'destinations_manager',
                'category_key' => 'content',
                'category_name' => 'المحتوى والخدمات والمواعيد',
                'title_ar' => 'الوجهات السياحية (Destinations)',
                'description_ar' => 'إدارة برامج ودول ومناطق السفر والسياحة.',
                'icon' => '🗺️',
                'route' => 'admin.destinations.index',
                'permission' => 'destinations.manage',
                'badge_color' => 'success',
            ],

            // Marketing Group (التسويق والحملات)
            'landing_pages_new' => [
                'id' => 'landing_pages_new',
                'category_key' => 'marketing',
                'category_name' => 'التسويق والحملات والـ Funnels',
                'title_ar' => 'Landing Page Builder New',
                'description_ar' => 'بناء وتخصيص صفحات الهبوط التسويقية الحديثة.',
                'icon' => '🚀',
                'route' => 'admin.landing-pages-new.dashboard',
                'permission' => 'marketing.manage',
                'badge_color' => 'warning',
            ],
            'popup_manager' => [
                'id' => 'popup_manager',
                'category_key' => 'marketing',
                'category_name' => 'التسويق والحملات والـ Funnels',
                'title_ar' => 'Popup Manager (مدير التنبيهات)',
                'description_ar' => 'إنشاء وإدارة النوافذ المنبثقة والعروض الترويجية.',
                'icon' => '🎯',
                'route' => 'admin.popups.dashboard',
                'permission' => 'marketing.manage',
                'badge_color' => 'danger',
            ],
            'marketing_campaigns' => [
                'id' => 'marketing_campaigns',
                'category_key' => 'marketing',
                'category_name' => 'التسويق والحملات والـ Funnels',
                'title_ar' => 'الحملات التسويقية (Campaigns)',
                'description_ar' => 'متابعة الحملات الإعلانية ومصادر الزيارات.',
                'icon' => '📊',
                'route' => 'admin.marketing-campaigns.index',
                'permission' => 'marketing.manage',
                'badge_color' => 'primary',
            ],
            'utm_analytics' => [
                'id' => 'utm_analytics',
                'category_key' => 'marketing',
                'category_name' => 'التسويق والحملات والـ Funnels',
                'title_ar' => 'تحليلات UTM Analytics',
                'description_ar' => 'تتبع روابط الإعلانات ومعدل استجابة الحملات.',
                'icon' => '🔗',
                'route' => 'admin.utm.dashboard',
                'permission' => 'utm.manage',
                'badge_color' => 'info',
            ],
            'zapier_integration' => [
                'id' => 'zapier_integration',
                'category_key' => 'marketing',
                'category_name' => 'التسويق والحملات والـ Funnels',
                'title_ar' => 'ربط Zapier والتكامل البرمجي',
                'description_ar' => 'ربط الإعلانات والنماذج تلقائياً عبر Zapier و Webhooks.',
                'icon' => '⚡',
                'route' => 'admin.zapier.index',
                'permission' => 'settings.manage',
                'badge_color' => 'warning',
            ],

            // Users Group (المستخدمين والصلاحيات)
            'users_management' => [
                'id' => 'users_management',
                'category_key' => 'users',
                'category_name' => 'المستخدمين والصلاحيات',
                'title_ar' => 'إدارة المستخدمين والموظفين',
                'description_ar' => 'إضافة موظفين جدد وتعديل الحسابات وكلمات المرور.',
                'icon' => '👥',
                'route' => 'admin.users.index',
                'permission' => 'users.view',
                'badge_color' => 'primary',
            ],
            'roles_management' => [
                'id' => 'roles_management',
                'category_key' => 'users',
                'category_name' => 'المستخدمين والصلاحيات',
                'title_ar' => 'إدارة الأدوار والسلطات (Roles)',
                'description_ar' => 'تحديد وتسمية الأدوار الوظيفية (مدير، بائع، حسابات).',
                'icon' => '🛡️',
                'route' => 'admin.roles.index',
                'permission' => 'roles.manage',
                'badge_color' => 'dark',
            ],
            'permissions_management' => [
                'id' => 'permissions_management',
                'category_key' => 'users',
                'category_name' => 'المستخدمين والصلاحيات',
                'title_ar' => 'جدول الصلاحيات (Permissions)',
                'description_ar' => 'التحكم الدقيق في صلاحيات كل زر وشاشة بالنظام.',
                'icon' => '🔑',
                'route' => 'admin.permissions.index',
                'permission' => 'permissions.manage',
                'badge_color' => 'danger',
            ],

            // System Group (إعدادات النظام والرقابة)
            'modules_control' => [
                'id' => 'modules_control',
                'category_key' => 'system',
                'category_name' => 'إعدادات النظام والرقابة',
                'title_ar' => 'إدارة موديولات النظام (Modules Control)',
                'description_ar' => 'التحكم في تفعيل أو تشغيل الموديولات الرئيسية.',
                'icon' => '⚙️',
                'route' => 'admin.modules-control.edit',
                'permission' => 'settings.manage',
                'badge_color' => 'danger',
            ],
            'website_settings' => [
                'id' => 'website_settings',
                'category_key' => 'system',
                'category_name' => 'إعدادات النظام والرقابة',
                'title_ar' => 'إعدادات الموقع والبراند',
                'description_ar' => 'تعديل اسم الشركة، اللوجو، وسائل التواصل، وبيانات الموقع.',
                'icon' => '🌐',
                'route' => 'admin.website-settings.edit',
                'permission' => 'settings.manage',
                'badge_color' => 'info',
            ],
            'audit_logs' => [
                'id' => 'audit_logs',
                'category_key' => 'system',
                'category_name' => 'إعدادات النظام والرقابة',
                'title_ar' => 'سجل التدقيق والحركات (Audit Logs)',
                'description_ar' => 'متابعة كافة أنشطة وحركات وتعديلات المستخدمين بالنظام.',
                'icon' => '📜',
                'route' => 'admin.audit-logs.index',
                'permission' => 'audit_logs.view',
                'badge_color' => 'secondary',
            ],
            'kpi_dashboard' => [
                'id' => 'kpi_dashboard',
                'category_key' => 'system',
                'category_name' => 'إعدادات النظام والرقابة',
                'title_ar' => 'لوحة مؤشرات الأداء (KPI Dashboard)',
                'description_ar' => 'متابعة أداء الشركة الكلي وتحليلات الفترات.',
                'icon' => '📊',
                'route' => 'admin.kpi.dashboard',
                'permission' => 'reports.view',
                'badge_color' => 'primary',
            ],
        ];
    }

    public static function getSavedEnabledShortcuts(): array
    {
        $allShortcuts = self::getAvailableShortcuts();
        $defaultKeys = array_keys($allShortcuts);

        $filePath = storage_path('app/shortcuts_config.json');
        if (file_exists($filePath)) {
            $content = @file_get_contents($filePath);
            if ($content) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        return $defaultKeys;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $categories = self::getCategories();
        $allRegistry = self::getAvailableShortcuts();
        $enabledKeys = self::getSavedEnabledShortcuts();

        // Group active & accessible shortcuts by category
        $groupedShortcuts = [];
        foreach ($categories as $catKey => $catMeta) {
            $groupedShortcuts[$catKey] = [
                'meta' => $catMeta,
                'items' => [],
            ];
        }

        foreach ($enabledKeys as $key) {
            if (!isset($allRegistry[$key])) {
                continue;
            }
            $item = $allRegistry[$key];

            // Permission check: If shortcut requires a permission and user does NOT have it, skip
            if (!empty($item['permission']) && $user && method_exists($user, 'hasPermission')) {
                if (!$user->hasPermission($item['permission'])) {
                    continue;
                }
            }

            // Check if route exists
            if (Route::has($item['route'])) {
                $item['url'] = route($item['route']);
                $catKey = $item['category_key'] ?? 'system';
                if (!isset($groupedShortcuts[$catKey])) {
                    $groupedShortcuts[$catKey] = [
                        'meta' => ['title_ar' => $item['category_name'] ?? 'قسم عام', 'icon' => '📌', 'description' => ''],
                        'items' => [],
                    ];
                }
                $groupedShortcuts[$catKey]['items'][] = $item;
            }
        }

        // Filter out categories with no items for this user
        $groupedShortcuts = array_filter($groupedShortcuts, fn($cat) => !empty($cat['items']));

        // Group registry for Admin Modal selection by category
        $registryByCat = [];
        foreach ($categories as $catKey => $catMeta) {
            $registryByCat[$catKey] = [
                'meta' => $catMeta,
                'items' => [],
            ];
        }
        foreach ($allRegistry as $key => $item) {
            $catKey = $item['category_key'] ?? 'system';
            if (isset($registryByCat[$catKey])) {
                $registryByCat[$catKey]['items'][$key] = $item;
            }
        }

        $canManageShortcuts = $user && method_exists($user, 'hasPermission') && ($user->hasPermission('settings.manage') || $user->hasPermission('roles.manage'));

        return view('admin.shortcuts.index', [
            'groupedShortcuts' => $groupedShortcuts,
            'registryByCat' => $registryByCat,
            'enabledKeys' => $enabledKeys,
            'canManageShortcuts' => $canManageShortcuts,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user || !method_exists($user, 'hasPermission') || (!$user->hasPermission('settings.manage') && !$user->hasPermission('roles.manage'))) {
            abort(403, 'غير مصرح لك بتحديد وإدارة الاختصارات.');
        }

        $request->validate([
            'shortcuts' => ['nullable', 'array'],
            'shortcuts.*' => ['string'],
        ]);

        $selected = $request->input('shortcuts', []);
        $allRegistry = self::getAvailableShortcuts();
        $validKeys = array_values(array_intersect($selected, array_keys($allRegistry)));

        $filePath = storage_path('app/shortcuts_config.json');
        @file_put_contents($filePath, json_encode($validKeys, JSON_PRETTY_PRINT));

        return redirect()->route('admin.shortcuts.index')->with('success', 'تم حفظ وتحديث كافة خيارات الأقسام والاختصارات المحددة بنجاح.');
    }
}
