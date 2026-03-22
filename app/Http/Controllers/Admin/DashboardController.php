<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\Inquiry;
use App\Models\LeadForm;
use App\Models\MarketingLandingPage;
use App\Models\Testimonial;
use App\Models\VisaCountry;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            [
                'label' => __('admin.dashboard_stat_inquiries'),
                'value' => Inquiry::count(),
                'tone' => 'primary',
            ],
            [
                'label' => __('admin.dashboard_stat_form_submissions'),
                'value' => Inquiry::query()->whereNotNull('lead_form_id')->count(),
                'tone' => 'success',
            ],
            [
                'label' => __('admin.dashboard_stat_landing_pages'),
                'value' => MarketingLandingPage::count(),
                'tone' => 'accent',
            ],
            [
                'label' => __('admin.dashboard_stat_campaigns'),
                'value' => MarketingLandingPage::query()->where('status', 'published')->count(),
                'tone' => 'warning',
            ],
            [
                'label' => __('admin.dashboard_stat_destinations'),
                'value' => VisaCountry::count() + Destination::count(),
                'tone' => 'slate',
            ],
            [
                'label' => __('admin.dashboard_stat_posts'),
                'value' => BlogPost::count(),
                'tone' => 'danger',
            ],
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'quickAccess' => [
                [
                    'title' => __('admin.nav_content'),
                    'text' => __('admin.dashboard_quick_content'),
                    'route' => route('admin.pages.index'),
                    'button' => __('admin.pages'),
                ],
                [
                    'title' => __('admin.nav_forms_leads'),
                    'text' => __('admin.dashboard_quick_forms'),
                    'route' => route('admin.forms.index'),
                    'button' => __('admin.forms_manager'),
                ],
                [
                    'title' => __('admin.nav_marketing'),
                    'text' => __('admin.dashboard_quick_marketing'),
                    'route' => route('admin.marketing-landing-pages.index'),
                    'button' => __('admin.marketing_manager'),
                ],
                [
                    'title' => 'SEO',
                    'text' => __('admin.dashboard_quick_seo'),
                    'route' => route('admin.seo.dashboard'),
                    'button' => __('admin.seo_manager'),
                ],
                [
                    'title' => __('admin.nav_site_settings'),
                    'text' => __('admin.dashboard_quick_settings'),
                    'route' => route('admin.settings.edit'),
                    'button' => __('admin.brand_settings'),
                ],
                [
                    'title' => __('admin.nav_users_permissions'),
                    'text' => __('admin.dashboard_quick_users'),
                    'route' => route('admin.users.index'),
                    'button' => __('admin.users_management'),
                ],
            ],
            'summary' => [
                'visa_countries' => VisaCountry::count(),
                'destinations' => Destination::count(),
                'posts' => BlogPost::count(),
                'testimonials' => Testimonial::count(),
                'hero_slides' => HeroSlide::count(),
                'forms' => LeadForm::count(),
                'landing_pages' => MarketingLandingPage::count(),
            ],
            'latestInquiries' => Inquiry::latest()->limit(6)->get(),
            'latestPosts' => BlogPost::latest()->limit(5)->get(),
            'latestCountries' => VisaCountry::latest()->limit(5)->get(),
        ]);
    }
}
