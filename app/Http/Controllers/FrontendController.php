<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\HomeCountryStripItem;
use App\Models\Inquiry;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function switchLocale(string $locale)
    {
        abort_unless(in_array($locale, ['en', 'ar'], true), 404);
        session(['locale' => $locale]);

        return back();
    }

    public function home()
    {
        $settings = Setting::query()->first();
        $homeSearchConfig = $this->homeSearchConfig();

        return view('frontend.home', [
            'page' => $this->page('home'),
            'heroSlides' => HeroSlide::where('is_active', true)->orderBy('sort_order')->limit(3)->get(),
            'heroSliderSettings' => $settings,
            'homeSearchConfig' => $homeSearchConfig,
            'homeCountryStripItems' => HomeCountryStripItem::where('is_active', true)
                ->where('show_on_homepage', true)
                ->with('visaCountry')
                ->orderBy('sort_order')
                ->get(),
            'featuredCountries' => VisaCountry::where('is_featured', true)->where('is_active', true)->with('category')->orderBy('sort_order')->limit(6)->get(),
            'featuredDestinations' => Destination::where('is_featured', true)->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'categories' => VisaCategory::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
            'posts' => BlogPost::where('is_published', true)->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function visaIndex()
    {
        return view('frontend.visas.index', [
            'page' => $this->page('visas'),
            'categories' => VisaCategory::where('is_active', true)->with(['countries' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])->orderBy('sort_order')->get(),
            'featuredCountries' => VisaCountry::where('is_featured', true)->where('is_active', true)->with('category')->orderBy('sort_order')->get(),
        ]);
    }

    public function visaCategory(VisaCategory $category)
    {
        return view('frontend.visas.category', [
            'category' => $category->load(['countries' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')]),
        ]);
    }

    public function visaCountry(VisaCountry $country)
    {
        abort_unless($country->is_active, 404);

        return view('frontend.visas.show', [
            'country' => $country->load('category'),
        ]);
    }

    public function domesticIndex()
    {
        return view('frontend.destinations.index', [
            'page' => $this->page('domestic'),
            'destinations' => Destination::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    public function destinationShow(Destination $destination)
    {
        return view('frontend.destinations.show', compact('destination'));
    }

    public function flights()
    {
        return view('frontend.pages.standard', ['page' => $this->page('flights')]);
    }

    public function hotels()
    {
        return view('frontend.pages.standard', ['page' => $this->page('hotels')]);
    }

    public function about()
    {
        return view('frontend.pages.standard', ['page' => $this->page('about')]);
    }

    public function contact()
    {
        return view('frontend.pages.standard', ['page' => $this->page('contact')]);
    }

    public function blogIndex()
    {
        return view('frontend.blog.index', [
            'page' => $this->page('blog'),
            'posts' => BlogPost::where('is_published', true)->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(BlogPost $post)
    {
        abort_unless($post->is_published, 404);

        return view('frontend.blog.show', [
            'post' => $post->load('category'),
            'relatedPosts' => BlogPost::where('is_published', true)
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }

    public function storeInquiry(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:general,visa,destination,flights,hotels,contact'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'service_type' => ['nullable', 'string', 'max:255'],
            'travel_date' => ['nullable', 'date'],
            'return_date' => ['nullable', 'date'],
            'travelers_count' => ['nullable', 'integer'],
            'nights_count' => ['nullable', 'integer'],
            'accommodation_type' => ['nullable', 'string', 'max:255'],
            'estimated_budget' => ['nullable', 'string', 'max:255'],
            'preferred_language' => ['nullable', 'in:en,ar'],
            'source_page' => ['nullable', 'string', 'max:255'],
            'success_message' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string'],
        ]);

        Inquiry::create($data + [
            'preferred_language' => $data['preferred_language'] ?? app()->getLocale(),
            'status' => 'new',
        ]);

        return back()->with('success', $data['success_message'] ?? __('ui.inquiry_success'));
    }

    protected function page(string $key): Page
    {
        return Page::where('key', $key)->where('is_active', true)->firstOrFail();
    }

    protected function homeSearchConfig(): array
    {
        $regionDefinitions = [
            [
                'key' => 'european-union',
                'label_en' => 'European Union',
                'label_ar' => 'الاتحاد الأوروبي',
                'category_slugs' => ['european-union'],
            ],
            [
                'key' => 'arab-countries',
                'label_en' => 'Arab Countries',
                'label_ar' => 'الدول العربية',
                'category_slugs' => ['arab-countries', 'gcc', 'gulf'],
            ],
            [
                'key' => 'asia',
                'label_en' => 'Asia',
                'label_ar' => 'آسيا',
                'category_slugs' => ['asia'],
            ],
            [
                'key' => 'usa-canada',
                'label_en' => 'USA & Canada',
                'label_ar' => 'أمريكا وكندا',
                'category_slugs' => ['usa-canada', 'north-america'],
            ],
        ];

        $categories = VisaCategory::query()
            ->where('is_active', true)
            ->with(['countries' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')])
            ->get()
            ->keyBy('slug');

        $visaRegions = collect($regionDefinitions)->map(function (array $region) use ($categories) {
            $countries = collect($region['category_slugs'])
                ->map(fn (string $slug) => $categories->get($slug))
                ->filter()
                ->flatMap(fn (VisaCategory $category) => $category->countries)
                ->unique('id')
                ->sortBy('sort_order')
                ->values()
                ->map(fn (VisaCountry $country) => [
                    'slug' => $country->slug,
                    'label_en' => $country->name_en,
                    'label_ar' => $country->name_ar,
                    'url' => route('visas.country', $country),
                ])
                ->all();

            return [
                'key' => $region['key'],
                'label_en' => $region['label_en'],
                'label_ar' => $region['label_ar'],
                'countries' => $countries,
            ];
        })->all();

        $domesticDestinations = Destination::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Destination $destination) => [
                'slug' => $destination->slug,
                'label_en' => $destination->title_en,
                'label_ar' => $destination->title_ar,
                'url' => route('destinations.show', $destination),
            ])
            ->all();

        return [
            'services' => [
                ['key' => 'visas', 'label_en' => 'External Visas', 'label_ar' => 'التأشيرات الخارجية'],
                ['key' => 'domestic', 'label_en' => 'Domestic Trips', 'label_ar' => 'الرحلات الداخلية'],
                ['key' => 'flights', 'label_en' => 'Flights', 'label_ar' => 'الطيران'],
                ['key' => 'hotels', 'label_en' => 'Hotels', 'label_ar' => 'الفنادق'],
            ],
            'visa_regions' => $visaRegions,
            'domestic_destinations' => $domesticDestinations,
            'direct_routes' => [
                'flights' => route('flights'),
                'hotels' => route('hotels'),
            ],
        ];
    }
}
