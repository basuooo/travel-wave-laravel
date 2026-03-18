<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\Inquiry;
use App\Models\Testimonial;
use App\Models\VisaCountry;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'stats' => [
                'inquiries' => Inquiry::count(),
                'visa_countries' => VisaCountry::count(),
                'destinations' => Destination::count(),
                'posts' => BlogPost::count(),
                'testimonials' => Testimonial::count(),
                'hero_slides' => HeroSlide::count(),
            ],
            'latestInquiries' => Inquiry::latest()->limit(6)->get(),
            'latestPosts' => BlogPost::latest()->limit(5)->get(),
            'latestCountries' => VisaCountry::latest()->limit(5)->get(),
        ]);
    }
}
