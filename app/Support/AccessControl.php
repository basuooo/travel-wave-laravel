<?php

namespace App\Support;

class AccessControl
{
    public static function permissionGroups(): array
    {
        return [
            'dashboard' => [
                ['slug' => 'dashboard.access', 'name' => 'Dashboard Access', 'description' => 'Access the admin dashboard and authenticated admin area.'],
                ['slug' => 'reports.view', 'name' => 'View Reports', 'description' => 'View dashboard reports and high-level analytics.'],
            ],
            'audit' => [
                ['slug' => 'audit_logs.view', 'name' => 'View Audit Logs', 'description' => 'View immutable audit records for sensitive operational actions across the system.'],
            ],
            'workflow_automations' => [
                ['slug' => 'workflow_automations.view', 'name' => 'View Workflow Automations', 'description' => 'View workflow automation rules and execution traces.'],
                ['slug' => 'workflow_automations.manage', 'name' => 'Manage Workflow Automations', 'description' => 'Create, edit, enable, and disable controlled workflow rules.'],
            ],
            'goals_commissions' => [
                ['slug' => 'goals_commissions.view', 'name' => 'View Goals & Commissions', 'description' => 'View seller targets, commission statements, and performance ranking.'],
                ['slug' => 'goals_commissions.manage', 'name' => 'Manage Goals & Commissions', 'description' => 'Create targets, generate commission statements, and manage performance setup.'],
            ],
            'knowledge_base' => [
                ['slug' => 'knowledge_base.view', 'name' => 'View Knowledge Base', 'description' => 'View published internal knowledge base articles and operational reference content.'],
                ['slug' => 'knowledge_base.manage', 'name' => 'Manage Knowledge Base', 'description' => 'Create, edit, publish, and archive knowledge base articles.'],
                ['slug' => 'knowledge_base.categories.manage', 'name' => 'Manage Knowledge Base Categories', 'description' => 'Manage reusable categories used by the knowledge base module.'],
            ],
            'users' => [
                ['slug' => 'users.view', 'name' => 'View Users', 'description' => 'View the users list and user profile details.'],
                ['slug' => 'users.create', 'name' => 'Create Users', 'description' => 'Create new dashboard users.'],
                ['slug' => 'users.edit', 'name' => 'Edit Users', 'description' => 'Edit existing dashboard users.'],
                ['slug' => 'users.delete', 'name' => 'Delete Users', 'description' => 'Delete dashboard users when allowed.'],
                ['slug' => 'users.reset_password', 'name' => 'Reset User Passwords', 'description' => 'Reset dashboard user passwords.'],
            ],
            'roles' => [
                ['slug' => 'roles.manage', 'name' => 'Manage Roles', 'description' => 'Create, edit, and assign role permissions.'],
                ['slug' => 'permissions.manage', 'name' => 'Manage Permissions', 'description' => 'Create, edit, and delete permissions.'],
            ],
            'settings' => [
                ['slug' => 'settings.manage', 'name' => 'Manage Core Settings', 'description' => 'Manage brand, header, footer, and core dashboard settings.'],
                ['slug' => 'security.manage', 'name' => 'Manage Security Settings', 'description' => 'Manage sensitive system-level and security settings.'],
                ['slug' => 'translations.manage', 'name' => 'Manage Translations', 'description' => 'Manage localization and translation-related settings.'],
            ],
            'pages' => [
                ['slug' => 'pages.view', 'name' => 'View Pages', 'description' => 'View pages and service content.'],
                ['slug' => 'pages.create', 'name' => 'Create Pages', 'description' => 'Create new page content where supported.'],
                ['slug' => 'pages.edit', 'name' => 'Edit Pages', 'description' => 'Edit page and service content.'],
                ['slug' => 'pages.delete', 'name' => 'Delete Pages', 'description' => 'Delete or archive page content.'],
                ['slug' => 'pages.publish', 'name' => 'Publish Pages', 'description' => 'Publish or unpublish page content.'],
                ['slug' => 'destinations.manage', 'name' => 'Manage Destinations', 'description' => 'Manage domestic destinations, visa destinations, and categories.'],
                ['slug' => 'blog.manage', 'name' => 'Manage Blog', 'description' => 'Manage blog categories and posts.'],
                ['slug' => 'media.manage', 'name' => 'Manage Media', 'description' => 'Upload or manage media assets used in content.'],
                ['slug' => 'menu.manage', 'name' => 'Manage Navigation', 'description' => 'Manage site menus and navigation items.'],
                ['slug' => 'testimonials.manage', 'name' => 'Manage Testimonials', 'description' => 'Manage testimonials and social proof sections.'],

            ],
            'forms' => [
                ['slug' => 'forms.manage', 'name' => 'Manage Forms', 'description' => 'Manage reusable lead and inquiry forms.'],
                ['slug' => 'forms.submissions.view', 'name' => 'View Form Submissions', 'description' => 'View form submissions across the site.'],
                ['slug' => 'forms.submissions.edit', 'name' => 'Manage Form Submissions', 'description' => 'Edit submission status and follow-up metadata.'],
            ],
            'maps' => [
                ['slug' => 'maps.manage', 'name' => 'Manage Maps', 'description' => 'Manage reusable map sections and assignments.'],
            ],
            'tracking' => [
                ['slug' => 'tracking.manage', 'name' => 'Manage Tracking', 'description' => 'Manage GTM, GA4, Meta Pixel, and custom tracking integrations.'],
                ['slug' => 'utm.manage', 'name' => 'Manage UTM Builder', 'description' => 'Manage UTM builder and related campaign tracking tools.'],
            ],
            'seo' => [
                ['slug' => 'seo.manage', 'name' => 'Manage SEO', 'description' => 'Access core SEO dashboard and settings.'],
                ['slug' => 'seo.meta.manage', 'name' => 'Manage SEO Meta', 'description' => 'Manage page-level SEO fields and overrides.'],
                ['slug' => 'seo.redirects.manage', 'name' => 'Manage SEO Redirects', 'description' => 'Manage SEO redirects.'],
                ['slug' => 'seo.sitemap.manage', 'name' => 'Manage SEO Sitemap', 'description' => 'Regenerate and configure sitemap output.'],
            ],
            'marketing' => [
                ['slug' => 'marketing.manage', 'name' => 'Manage Marketing', 'description' => 'Access marketing dashboards and campaign tools.'],
                ['slug' => 'landing_pages.manage', 'name' => 'Manage Landing Pages', 'description' => 'Create and manage marketing landing pages.'],
                ['slug' => 'chatbot.manage', 'name' => 'Manage AI Chatbot', 'description' => 'Manage the website AI assistant, knowledge base, and chatbot logs.'],
            ],
            'leads' => [
                ['slug' => 'leads.view', 'name' => 'View Leads', 'description' => 'View inquiries and leads.'],
                ['slug' => 'leads.edit', 'name' => 'Manage Leads', 'description' => 'Update lead status and internal notes.'],
                ['slug' => 'leads.delete', 'name' => 'Delete Leads', 'description' => 'Move leads to trash, restore them, or delete them permanently when allowed.'],
                ['slug' => 'leads.export', 'name' => 'Export Leads', 'description' => 'Export leads for sales and reporting.'],
                ['slug' => 'information.manage', 'name' => 'Manage CRM Information', 'description' => 'Create and review targeted CRM information notices and acknowledgements.'],
                ['slug' => 'leads.change_assigned_to', 'name' => 'Change Assigned To', 'description' => 'Allow reassigning leads to other salespeople.'],
            ],
            'customers' => [
                ['slug' => 'customers.view', 'name' => 'View Customers', 'description' => 'View converted customers and active customer cases.'],
                ['slug' => 'customers.manage', 'name' => 'Manage Customers', 'description' => 'Convert leads to customers and update customer case data.'],
            ],
            'documents' => [
                ['slug' => 'documents.view', 'name' => 'View Documents', 'description' => 'View operational documents linked to CRM records.'],
                ['slug' => 'documents.manage', 'name' => 'Manage Documents', 'description' => 'Upload, download, and delete documents for allowed CRM records.'],
                ['slug' => 'documents.categories.manage', 'name' => 'Manage Document Categories', 'description' => 'Manage reusable document categories used by the documents module.'],
            ],
            'accounting' => [
                ['slug' => 'accounting.view', 'name' => 'View Accounting', 'description' => 'View accounting dashboard, customer accounting, expenses, and payroll records.'],
                ['slug' => 'accounting.manage', 'name' => 'Manage Accounting', 'description' => 'Manage collections, expenses, payroll entries, and accounting settings.'],
                ['slug' => 'accounting.reports.view', 'name' => 'View Accounting Reports', 'description' => 'View detailed accounting and finance reports.'],
            ],
        ];
    }

    public static function flatPermissions(): array
    {
        return collect(static::permissionGroups())
            ->flatMap(fn (array $permissions, string $module) => collect($permissions)->map(function (array $permission) use ($module) {
                return $permission + ['module' => $module];
            }))
            ->values()
            ->all();
    }

    public static function defaultRoles(): array
    {
        return [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system owner access across all dashboard modules and security controls.',
                'permissions' => ['*'],
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Broad operational access for day-to-day management without full system-owner privileges.',
                'permissions' => [
                    'dashboard.access', 'reports.view', 'settings.manage', 'pages.view', 'pages.create', 'pages.edit', 'pages.delete', 'pages.publish',
                    'destinations.manage', 'blog.manage', 'media.manage', 'menu.manage', 'testimonials.manage', 'forms.manage',
                    'forms.submissions.view', 'forms.submissions.edit', 'maps.manage', 'tracking.manage', 'utm.manage',
                    'seo.manage', 'seo.meta.manage', 'seo.redirects.manage', 'seo.sitemap.manage', 'marketing.manage', 'landing_pages.manage', 'chatbot.manage',
                    'audit_logs.view',
                    'workflow_automations.view', 'workflow_automations.manage',
                    'goals_commissions.view', 'goals_commissions.manage',
                    'knowledge_base.view', 'knowledge_base.manage', 'knowledge_base.categories.manage',
                    'leads.view', 'leads.edit', 'leads.delete', 'leads.export', 'information.manage', 'customers.view', 'customers.manage',
                    'documents.view', 'documents.manage', 'documents.categories.manage',
                    'accounting.view', 'accounting.manage', 'accounting.reports.view',
                ],
            ],
            [
                'name' => 'Marketing Manager',
                'slug' => 'marketing-manager',
                'description' => 'Manage landing pages, campaigns, UTM links, tracking, and marketing performance.',
                'permissions' => [
                    'dashboard.access', 'reports.view', 'marketing.manage', 'landing_pages.manage', 'tracking.manage', 'chatbot.manage',
                    'utm.manage', 'forms.manage', 'forms.submissions.view', 'leads.view', 'knowledge_base.view',
                ],
            ],
            [
                'name' => 'SEO Manager',
                'slug' => 'seo-manager',
                'description' => 'Manage technical SEO, metadata, redirects, sitemap, schema, and SEO reporting.',
                'permissions' => [
                    'dashboard.access', 'reports.view', 'seo.manage', 'seo.meta.manage', 'seo.redirects.manage', 'seo.sitemap.manage', 'pages.view',
                ],
            ],
            [
                'name' => 'Content Manager / Editor',
                'slug' => 'content-manager',
                'description' => 'Manage content pages, destinations, blog content, testimonials, and menus.',
                'permissions' => [
                    'dashboard.access', 'pages.view', 'pages.create', 'pages.edit', 'pages.publish', 'destinations.manage',
                    'blog.manage', 'media.manage', 'menu.manage', 'testimonials.manage', 'knowledge_base.view', 'knowledge_base.manage',
                ],
            ],
            [
                'name' => 'Sales / Leads Manager',
                'slug' => 'sales-leads-manager',
                'description' => 'Monitor and update leads, submissions, and sales-related follow-up records.',
                'permissions' => [
                    'dashboard.access', 'reports.view', 'leads.view', 'leads.edit', 'leads.export', 'forms.submissions.view', 'forms.submissions.edit', 'information.manage',
                    'customers.view', 'customers.manage', 'documents.view', 'documents.manage', 'knowledge_base.view', 'knowledge_base.manage',
                    'workflow_automations.view', 'goals_commissions.view', 'leads.change_assigned_to',
                ],
            ],
            [
                'name' => 'Viewer / Analyst',
                'slug' => 'viewer-analyst',
                'description' => 'Read-only access to dashboards, reports, and approved admin areas.',
                'permissions' => [
                    'dashboard.access', 'reports.view', 'pages.view', 'leads.view', 'forms.submissions.view', 'knowledge_base.view',
                    'workflow_automations.view', 'goals_commissions.view',
                ],
            ],
        ];
    }
}
