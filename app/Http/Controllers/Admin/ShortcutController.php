<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ShortcutController extends Controller
{
    public static function getAvailableShortcuts(): array
    {
        return [
            'crm_dashboard' => [
                'id' => 'crm_dashboard',
                'title_ar' => '📊 لوحة تحكم الـ CRM',
                'title_en' => 'CRM Dashboard',
                'description_ar' => 'عرض إحصائيات المبيعات، معدل التحويل، ونظرة عامة على العملاء.',
                'description_en' => 'View sales statistics, conversion rates, and leads overview.',
                'icon' => '📊',
                'route' => 'admin.crm.dashboard',
                'permission' => 'leads.view',
                'category' => 'العملاء والمبيعات (CRM)',
                'badge_color' => 'primary',
            ],
            'crm_lead_create' => [
                'id' => 'crm_lead_create',
                'title_ar' => '➕ إضافة ليد جديد',
                'title_en' => 'Add New Lead',
                'description_ar' => 'إدخال عميل محتمل جديد مباشرة إلى النظام وتحديد بياناته.',
                'description_en' => 'Insert a new lead directly into the system.',
                'icon' => '➕',
                'route' => 'admin.crm.leads.create',
                'permission' => 'leads.create',
                'category' => 'العملاء والمبيعات (CRM)',
                'badge_color' => 'success',
            ],
            'crm_lead_delayed' => [
                'id' => 'crm_lead_delayed',
                'title_ar' => '⚠️ الليد المتأخرة',
                'title_en' => 'Delayed Leads',
                'description_ar' => 'متابعة العملاء المحتملين الذين مرت عليهم 48 ساعة دون اتخاذ إجراء.',
                'description_en' => 'Track leads with no action for > 48 hours.',
                'icon' => '⚠️',
                'route' => 'admin.crm.leads.delayed',
                'permission' => 'leads.view',
                'category' => 'العملاء والمبيعات (CRM)',
                'badge_color' => 'danger',
            ],
            'forms_submissions' => [
                'id' => 'forms_submissions',
                'title_ar' => '📥 فورـم لـيـد (Form Leads)',
                'title_en' => 'Form Leads',
                'description_ar' => 'قائمة العملاء الجدد المسجلين عبر استمارات ونماذج الموقع.',
                'description_en' => 'View website form submission leads.',
                'icon' => '📥',
                'route' => 'admin.forms.submissions',
                'permission' => 'forms.submissions.view',
                'category' => 'النماذج والاستمارات',
                'badge_color' => 'info',
            ],
            'embassy_appointments' => [
                'id' => 'embassy_appointments',
                'title_ar' => '📅 مواعيد السفارات',
                'title_en' => 'Embassy Appointments',
                'description_ar' => 'إدارة حشوف ومواعيد السفارات والـ Visa Appointments.',
                'description_en' => 'Manage embassy visa appointments.',
                'icon' => '📅',
                'route' => 'admin.embassy-appointments.index',
                'permission' => 'embassy_appointments.view',
                'category' => 'الخدمات والمواعيد',
                'badge_color' => 'warning',
            ],
            'forms_manager' => [
                'id' => 'forms_manager',
                'title_ar' => '📑 إدارة النماذج (Forms)',
                'title_en' => 'Forms Manager',
                'description_ar' => 'إنشاء وتخصيص استمارات النماذج التفاعلية وحقولها.',
                'description_en' => 'Create and customize website dynamic forms.',
                'icon' => '📑',
                'route' => 'admin.forms.index',
                'permission' => 'forms.manage',
                'category' => 'النماذج والاستمارات',
                'badge_color' => 'secondary',
            ],
            'accounting_dashboard' => [
                'id' => 'accounting_dashboard',
                'title_ar' => '💰 لوحة الحسابات والمالية',
                'title_en' => 'Accounting Dashboard',
                'description_ar' => 'متابعة المقبوضات، المصروفات، الخزائن، والسندات المالية.',
                'description_en' => 'Financial overview, revenues, and expenses.',
                'icon' => '💰',
                'route' => 'admin.accounting.dashboard',
                'permission' => 'accounting.view',
                'category' => 'الحسابات والمالية',
                'badge_color' => 'success',
            ],
            'accounting_accounts' => [
                'id' => 'accounting_accounts',
                'title_ar' => '🌳 شجرة الحسابات',
                'title_en' => 'Chart of Accounts',
                'description_ar' => 'عرض الهيكل المالي للأصول، الخصوم، الإيرادات، والمصروفات.',
                'description_en' => 'View chart of accounts structure.',
                'icon' => '🌳',
                'route' => 'admin.accounting.accounts.index',
                'permission' => 'accounting.view',
                'category' => 'الحسابات والمالية',
                'badge_color' => 'dark',
            ],
            'audit_logs' => [
                'id' => 'audit_logs',
                'title_ar' => '📜 سجل التغييرات والتدقيق',
                'title_en' => 'Audit Logs',
                'description_ar' => 'متابعة كافة أنشطة وحركات وتعديلات المستخدمين بالنظام.',
                'description_en' => 'Track system user audit log activities.',
                'icon' => '📜',
                'route' => 'admin.audit-logs.index',
                'permission' => 'audit_logs.view',
                'category' => 'النظام والرقابة',
                'badge_color' => 'secondary',
            ],
            'users_management' => [
                'id' => 'users_management',
                'title_ar' => '👥 إدارة المستخدمين والموظفين',
                'title_en' => 'Users Management',
                'description_ar' => 'إضافة موظفين جدد وتحديد الصلاحيات والأدوار.',
                'description_en' => 'Manage system users, staff, and roles.',
                'icon' => '👥',
                'route' => 'admin.users.index',
                'permission' => 'users.view',
                'category' => 'المستخدمين والصلاحيات',
                'badge_color' => 'primary',
            ],
            'system_modules' => [
                'id' => 'system_modules',
                'title_ar' => '⚙️ إدارة موديولات النظام',
                'title_en' => 'Modules Control',
                'description_ar' => 'التحكم في تفعيل أو إيقاف الموديولات الرئيسية.',
                'description_en' => 'Enable or disable core system modules.',
                'icon' => '⚙️',
                'route' => 'admin.modules-control.edit',
                'permission' => 'settings.manage',
                'category' => 'إعدادات النظام',
                'badge_color' => 'danger',
            ],
            'website_settings' => [
                'id' => 'website_settings',
                'title_ar' => '🌐 إعدادات الموقع والبراند',
                'title_en' => 'Website Settings',
                'description_ar' => 'تعديل اسم الشركة، اللوجو، وسائل التواصل، وبيانات الموقع.',
                'description_en' => 'Manage site branding, logos, and contacts.',
                'icon' => '🌐',
                'route' => 'admin.website-settings.edit',
                'permission' => 'settings.manage',
                'category' => 'إعدادات النظام',
                'badge_color' => 'info',
            ],
        ];
    }

    public static function getSavedEnabledShortcuts(): array
    {
        $allShortcuts = self::getAvailableShortcuts();
        $defaultKeys = array_keys($allShortcuts);

        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->where('key', 'system_shortcuts')->first();
            if ($setting && !empty($setting->value)) {
                $decoded = json_decode($setting->value, true);
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
        $allRegistry = self::getAvailableShortcuts();
        $enabledKeys = self::getSavedEnabledShortcuts();

        // Filter shortcuts that are both enabled system-wide AND accessible by the user's permissions
        $userShortcuts = [];
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
                $userShortcuts[] = $item;
            }
        }

        $canManageShortcuts = $user && method_exists($user, 'hasPermission') && ($user->hasPermission('settings.manage') || $user->hasPermission('roles.manage'));

        return view('admin.shortcuts.index', [
            'userShortcuts' => $userShortcuts,
            'allRegistry' => $allRegistry,
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

        if (Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'system_shortcuts'],
                ['value' => json_encode($validKeys), 'updated_at' => now()]
            );
        }

        return redirect()->route('admin.shortcuts.index')->with('success', 'تم حفظ وتحديث قائمة الاختصارات بنجاح.');
    }
}
