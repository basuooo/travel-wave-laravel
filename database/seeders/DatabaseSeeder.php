<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\HeroSlide;
use App\Models\Inquiry;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@travelwave.test'],
            [
                'name' => 'Travel Wave Admin',
                'password' => Hash::make('password123'),
                'is_admin' => true,
            ]
        );

        Setting::query()->updateOrCreate(
            ['id' => 1],
            [
                'site_name_en' => 'Travel Wave',
                'site_name_ar' => 'ترافل ويف',
                'site_tagline_en' => 'Visas, flights, hotels, and travel planning in one place.',
                'site_tagline_ar' => 'التأشيرات والطيران والفنادق وتخطيط الرحلات في مكان واحد.',
                'contact_email' => 'info@travelwave.com',
                'phone' => '+20 100 123 4567',
                'secondary_phone' => '+20 122 555 7788',
                'whatsapp_number' => '+20 100 123 4567',
                'address_en' => 'Nasr City, Cairo, Egypt',
                'address_ar' => 'مدينة نصر، القاهرة، مصر',
                'working_hours_en' => 'Daily from 10:00 AM to 8:00 PM',
                'working_hours_ar' => 'يوميًا من 10 صباحًا حتى 8 مساءً',
                'footer_text_en' => 'Travel Wave organizes your trip with clear planning, responsive support, and practical travel solutions.',
                'footer_text_ar' => 'تنظم Travel Wave رحلتك بخطة واضحة ومتابعة سريعة وحلول سفر عملية.',
                'copyright_text_en' => 'Copyright © Travel Wave. All rights reserved.',
                'copyright_text_ar' => 'جميع الحقوق محفوظة لشركة Travel Wave.',
                'default_meta_title_en' => 'Travel Wave Travel & Tourism',
                'default_meta_title_ar' => 'Travel Wave للسفر والسياحة',
                'default_meta_description_en' => 'Travel Wave provides visas, domestic tourism, outbound trips, flights, hotels, and travel consultation services.',
                'default_meta_description_ar' => 'تقدم Travel Wave خدمات التأشيرات والسياحة الداخلية والخارجية والطيران والفنادق والاستشارات السياحية.',
                'primary_color' => '#12395b',
                'secondary_color' => '#ff8c32',
                'global_cta_title_en' => 'Plan your next move with Travel Wave',
                'global_cta_title_ar' => 'خطط خطوتك القادمة مع Travel Wave',
                'global_cta_text_en' => 'Tell us about your trip and we will match you with the right service, timeline, and budget-friendly plan.',
                'global_cta_text_ar' => 'أخبرنا عن رحلتك وسنساعدك في اختيار الخدمة المناسبة والجدول الزمني وخطة السفر الملائمة لميزانيتك.',
                'global_cta_button_en' => 'Start Your Request',
                'global_cta_button_ar' => 'ابدأ طلبك الآن',
                'global_cta_url' => '/contact',
                'map_iframe' => '<iframe src="https://www.google.com/maps?q=Cairo%20Egypt&output=embed" width="100%" height="320" style="border:0;" loading="lazy"></iframe>',
                'hero_slider_autoplay' => true,
                'hero_slider_interval' => 5000,
                'hero_slider_overlay_opacity' => 0.48,
                'hero_slider_show_dots' => true,
                'hero_slider_show_arrows' => true,
                'hero_slider_content_alignment' => 'start',
            ]
        );

        foreach ([
            [
                'sort_order' => 1,
                'image_path' => 'hero-slides/slide-1.svg',
                'headline_en' => 'Luxury journeys shaped around your next visa, flight, and stay',
                'headline_ar' => 'رحلات راقية نصممها حول تأشيرتك ورحلتك الجوية وإقامتك',
                'subtitle_en' => 'Travel Wave combines visa support, premium bookings, and responsive trip planning in one smooth customer journey.',
                'subtitle_ar' => 'تجمع Travel Wave بين دعم التأشيرات والحجوزات الراقية وتخطيط الرحلات السريع ضمن تجربة واحدة متكاملة.',
                'cta_text_en' => 'Start Planning',
                'cta_text_ar' => 'ابدأ التخطيط',
                'cta_link' => '/contact',
                'is_active' => true,
            ],
            [
                'sort_order' => 2,
                'image_path' => 'hero-slides/slide-2.svg',
                'headline_en' => 'Europe, Gulf, and Asia visa services with a clearer path',
                'headline_ar' => 'خدمات تأشيرات أوروبا والخليج وآسيا بمسار أوضح',
                'subtitle_en' => 'From file preparation to booking coordination, we help you move with confidence and less last-minute pressure.',
                'subtitle_ar' => 'من تجهيز الملف إلى تنسيق الحجوزات نساعدك على التحرك بثقة وبضغط أقل في اللحظات الأخيرة.',
                'cta_text_en' => 'Explore Visa Services',
                'cta_text_ar' => 'استكشف التأشيرات',
                'cta_link' => '/visas',
                'is_active' => true,
            ],
            [
                'sort_order' => 3,
                'image_path' => 'hero-slides/slide-3.svg',
                'headline_en' => 'Discover Egypt and beyond with polished travel packages',
                'headline_ar' => 'اكتشف مصر وخارجها عبر باقات سفر مصممة باحتراف',
                'subtitle_en' => 'Domestic escapes, hotel bookings, flights, and custom itineraries built around your timing and budget.',
                'subtitle_ar' => 'رحلات داخلية وحجوزات فنادق وطيران وبرامج مخصصة مبنية حول توقيتك وميزانيتك.',
                'cta_text_en' => 'Browse Trips',
                'cta_text_ar' => 'تصفح الرحلات',
                'cta_link' => '/domestic-tourism',
                'is_active' => true,
            ],
        ] as $slide) {
            HeroSlide::query()->updateOrCreate(
                ['sort_order' => $slide['sort_order']],
                $slide
            );
        }

        $eu = VisaCategory::query()->updateOrCreate(
            ['slug' => 'european-union'],
            [
                'name_en' => 'European Union',
                'name_ar' => 'الاتحاد الأوروبي',
                'short_description_en' => 'Europe visa support with clear document preparation and appointment coordination.',
                'short_description_ar' => 'دعم تأشيرات أوروبا مع تجهيز واضح للملف وتنظيم المواعيد.',
                'icon' => 'EU',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $arab = VisaCategory::query()->updateOrCreate(
            ['slug' => 'arab-countries'],
            [
                'name_en' => 'Arab Countries',
                'name_ar' => 'الدول العربية',
                'short_description_en' => 'Fast guidance for tourism, visit, and business destinations in the region.',
                'short_description_ar' => 'إرشاد سريع لوجهات السياحة والزيارة والأعمال في المنطقة.',
                'icon' => 'AR',
                'sort_order' => 2,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $asia = VisaCategory::query()->updateOrCreate(
            ['slug' => 'asia'],
            [
                'name_en' => 'Asia',
                'name_ar' => 'آسيا',
                'short_description_en' => 'Popular Asian destinations with travel-ready guidance and planning support.',
                'short_description_ar' => 'وجهات آسيوية مطلوبة مع إرشاد عملي قبل السفر ودعم للتخطيط.',
                'icon' => 'AS',
                'sort_order' => 3,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $other = VisaCategory::query()->updateOrCreate(
            ['slug' => 'other-countries'],
            [
                'name_en' => 'Other Countries',
                'name_ar' => 'دول أخرى',
                'short_description_en' => 'Dedicated support for standalone destinations like USA, Canada, Georgia, and Armenia.',
                'short_description_ar' => 'دعم مخصص للوجهات المنفردة مثل أمريكا وكندا وجورجيا وأرمينيا.',
                'icon' => 'OT',
                'sort_order' => 4,
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        VisaCountry::query()->updateOrCreate(
            ['slug' => 'france-visa'],
            [
                'visa_category_id' => $eu->id,
                'name_en' => 'France',
                'name_ar' => 'فرنسا',
                'excerpt_en' => 'Tourist visa support for France with structured file preparation, appointment guidance, and follow-up.',
                'excerpt_ar' => 'دعم تأشيرة فرنسا السياحية مع تجهيز منظم للملف وإرشاد للمواعيد والمتابعة.',
                'hero_badge_en' => 'Schengen Visa',
                'hero_badge_ar' => 'تأشيرة شنغن',
                'hero_title_en' => 'France Visa Services with Travel Wave',
                'hero_title_ar' => 'خدمات تأشيرة فرنسا مع Travel Wave',
                'hero_subtitle_en' => 'We help you organize your France visa request clearly, from the required documents to final submission follow-up.',
                'hero_subtitle_ar' => 'نساعدك على تنظيم طلب تأشيرة فرنسا بشكل واضح من الأوراق المطلوبة وحتى متابعة التقديم النهائي.',
                'overview_en' => 'France remains one of the most requested Schengen destinations for tourism, family visits, and business trips. Travel Wave supports applicants with practical file review, document preparation guidance, and booking coordination.',
                'overview_ar' => 'تعد فرنسا من أكثر وجهات شنغن طلبًا للسياحة والزيارة العائلية ورحلات الأعمال. وتقدم Travel Wave دعمًا عمليًا لمراجعة الملف وتجهيز المستندات وتنسيق الحجوزات اللازمة.',
                'processing_time_en' => 'Typical processing time ranges between 15 and 30 working days depending on season and embassy load.',
                'processing_time_ar' => 'تتراوح مدة المعالجة عادة بين 15 و30 يوم عمل حسب الموسم وضغط السفارة.',
                'fees_en' => 'Fees vary between embassy charges, visa center fees, and service support fees. Contact us for the latest quote before submission.',
                'fees_ar' => 'تختلف الرسوم بين رسوم السفارة ورسوم مركز التأشيرات ورسوم الخدمة. تواصل معنا للحصول على عرض سعر محدث قبل التقديم.',
                'highlights' => [
                    ['text_en' => 'Suitable for tourism, family visits, and selected business purposes.', 'text_ar' => 'مناسبة للسياحة والزيارة العائلية وبعض أغراض الأعمال.'],
                    ['text_en' => 'Access to Schengen-area travel once the visa is issued.', 'text_ar' => 'إمكانية السفر داخل منطقة شنغن بعد إصدار التأشيرة.'],
                    ['text_en' => 'Clear follow-up on missing items and appointment readiness.', 'text_ar' => 'متابعة واضحة للعناصر الناقصة والاستعداد للموعد.'],
                ],
                'required_documents' => [
                    ['text_en' => 'Valid passport with sufficient validity.', 'text_ar' => 'جواز سفر ساري بمدة صلاحية مناسبة.'],
                    ['text_en' => 'Recent personal photos matching embassy requirements.', 'text_ar' => 'صور شخصية حديثة مطابقة لاشتراطات السفارة.'],
                    ['text_en' => 'Bank statement, employment proof, and travel booking details.', 'text_ar' => 'كشف حساب بنكي وإثبات وظيفة وتفاصيل الحجز والسفر.'],
                ],
                'application_steps' => [
                    ['text_en' => 'Share your travel purpose and basic profile.', 'text_ar' => 'أرسل هدف السفر وبياناتك الأساسية.'],
                    ['text_en' => 'Receive a tailored document checklist.', 'text_ar' => 'استلم قائمة مستندات مناسبة لحالتك.'],
                    ['text_en' => 'Review the file with Travel Wave before submission.', 'text_ar' => 'راجع الملف مع Travel Wave قبل التقديم.'],
                    ['text_en' => 'Attend the appointment and continue follow-up after submission.', 'text_ar' => 'احضر الموعد واستمر في المتابعة بعد التقديم.'],
                ],
                'services' => [
                    ['text_en' => 'Document checklist and file structure support.', 'text_ar' => 'قائمة مستندات ودعم في ترتيب الملف.'],
                    ['text_en' => 'Appointment preparation and submission guidance.', 'text_ar' => 'إرشاد قبل الموعد وأثناء التحضير للتقديم.'],
                    ['text_en' => 'Continuous follow-up and practical travel planning tips.', 'text_ar' => 'متابعة مستمرة ونصائح عملية لتخطيط الرحلة.'],
                ],
                'faqs' => [
                    ['question_en' => 'Can Travel Wave guarantee visa approval?', 'answer_en' => 'No. Approval remains solely with the embassy, but we help improve clarity, organization, and readiness of the application.', 'question_ar' => 'هل تضمن Travel Wave الموافقة على التأشيرة؟', 'answer_ar' => 'لا، فالموافقة قرار خاص بالسفارة، لكننا نساعد في تحسين وضوح الملف وتجهيزه بشكل أفضل.'],
                    ['question_en' => 'When should I apply?', 'answer_en' => 'It is usually better to start early, especially in busy seasons and before holidays.', 'question_ar' => 'متى يجب أن أبدأ التقديم؟', 'answer_ar' => 'يفضل البدء مبكرًا خاصة في المواسم المزدحمة وقبل العطلات.' ],
                ],
                'cta_title_en' => 'Ask about the right file for your France visa',
                'cta_title_ar' => 'اسأل عن الملف المناسب لتأشيرة فرنسا',
                'cta_text_en' => 'Send your details and our team will guide you through the first step clearly.',
                'cta_text_ar' => 'أرسل بياناتك وسيقوم فريقنا بإرشادك إلى أول خطوة بشكل واضح.',
                'cta_button_en' => 'Start Your Request',
                'cta_button_ar' => 'ابدأ طلبك',
                'cta_url' => '/contact',
                'meta_title_en' => 'France Visa Services | Travel Wave',
                'meta_title_ar' => 'خدمات تأشيرة فرنسا | Travel Wave',
                'meta_description_en' => 'France visa support with document preparation, appointment guidance, and travel follow-up.',
                'meta_description_ar' => 'دعم تأشيرة فرنسا مع تجهيز المستندات والإرشاد للموعد والمتابعة.',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        foreach ([
            [$eu->id, 'Germany', 'ألمانيا', 'germany-visa'],
            [$eu->id, 'Italy', 'إيطاليا', 'italy-visa'],
            [$arab->id, 'UAE', 'الإمارات', 'uae-visa'],
            [$asia->id, 'Turkey', 'تركيا', 'turkey-visa'],
            [$other->id, 'Canada', 'كندا', 'canada-visa'],
        ] as [$categoryId, $nameEn, $nameAr, $slug]) {
            VisaCountry::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'visa_category_id' => $categoryId,
                    'name_en' => $nameEn,
                    'name_ar' => $nameAr,
                    'excerpt_en' => "{$nameEn} visa support with practical planning and document guidance.",
                    'excerpt_ar' => "دعم تأشيرة {$nameAr} مع تخطيط عملي وإرشاد للمستندات.",
                    'overview_en' => "Travel Wave provides structured assistance for {$nameEn} visa applications with document review and travel advice.",
                    'overview_ar' => "توفر Travel Wave دعمًا منظمًا لطلبات تأشيرة {$nameAr} مع مراجعة الملف والنصائح قبل السفر.",
                    'is_featured' => false,
                    'is_active' => true,
                    'sort_order' => 10,
                ]
            );
        }

        Destination::query()->updateOrCreate(
            ['slug' => 'sharm-el-sheikh'],
            [
                'title_en' => 'Sharm El Sheikh',
                'title_ar' => 'شرم الشيخ',
                'excerpt_en' => 'Carefully selected Sharm El Sheikh programs for couples, families, and groups.',
                'excerpt_ar' => 'برامج مختارة بعناية إلى شرم الشيخ تناسب الأزواج والعائلات والمجموعات.',
                'hero_title_en' => 'Sharm El Sheikh Trips with Travel Wave',
                'hero_title_ar' => 'رحلات شرم الشيخ مع Travel Wave',
                'overview_en' => 'Enjoy a memorable experience in Sharm El Sheikh with flexible packages, trusted hotels, and support before and after booking.',
                'overview_ar' => 'استمتع بتجربة مميزة في شرم الشيخ مع باقات مرنة وفنادق موثوقة ودعم قبل الحجز وبعده.',
                'highlights' => [
                    ['text_en' => 'Resorts and hotels selected by category and budget.', 'text_ar' => 'منتجعات وفنادق مختارة حسب الفئة والميزانية.'],
                    ['text_en' => 'Programs suitable for families, couples, and groups.', 'text_ar' => 'برامج مناسبة للعائلات والأزواج والمجموعات.'],
                    ['text_en' => 'Transport and optional activities when requested.', 'text_ar' => 'إمكانية إضافة التنقلات والأنشطة عند الطلب.'],
                ],
                'packages' => [
                    ['text_en' => 'Weekend getaway packages.', 'text_ar' => 'باقات عطلات نهاية الأسبوع.'],
                    ['text_en' => '4-night family offers.', 'text_ar' => 'عروض عائلية لمدة 4 ليالٍ.'],
                    ['text_en' => 'Seasonal high-demand offers.', 'text_ar' => 'عروض موسمية للطلبات المرتفعة.'],
                ],
                'included_items' => [
                    ['text_en' => 'Accommodation', 'text_ar' => 'الإقامة'],
                    ['text_en' => 'Meals depending on package type', 'text_ar' => 'الوجبات حسب نوع الباقة'],
                    ['text_en' => 'Support and follow-up', 'text_ar' => 'الدعم والمتابعة'],
                ],
                'excluded_items' => [
                    ['text_en' => 'Personal spending', 'text_ar' => 'المصروفات الشخصية'],
                    ['text_en' => 'Optional tours unless stated', 'text_ar' => 'الرحلات الاختيارية ما لم يتم ذكرها'],
                ],
                'itinerary' => [
                    ['text_en' => 'Arrival, hotel check-in, and free evening.', 'text_ar' => 'الوصول وتسجيل الدخول بالفندق وأمسية حرة.'],
                    ['text_en' => 'Beach or leisure day with optional activities.', 'text_ar' => 'يوم شاطئي أو ترفيهي مع أنشطة اختيارية.'],
                    ['text_en' => 'Final day follow-up and departure support.', 'text_ar' => 'متابعة في اليوم الأخير ودعم حتى المغادرة.'],
                ],
                'faqs' => [
                    ['question_en' => 'Can the number of nights change?', 'answer_en' => 'Yes, packages can be adjusted based on your dates and budget.', 'question_ar' => 'هل يمكن تغيير عدد الليالي؟', 'answer_ar' => 'نعم، يمكن تعديل الباقة حسب المواعيد والميزانية.'],
                    ['question_en' => 'Are there family offers?', 'answer_en' => 'Yes, we prepare family-friendly options across several hotel levels.', 'question_ar' => 'هل توجد عروض للعائلات؟', 'answer_ar' => 'نعم، نوفر خيارات مناسبة للعائلات عبر مستويات فندقية مختلفة.'],
                ],
                'cta_title_en' => 'Request the right Sharm package for your dates',
                'cta_title_ar' => 'اطلب باقة شرم الشيخ المناسبة لمواعيدك',
                'cta_text_en' => 'Share your travel dates and group size to receive the best-fit recommendation.',
                'cta_text_ar' => 'أرسل مواعيد السفر وعدد الأفراد لتحصل على ترشيح مناسب.',
                'cta_button_en' => 'Request a Quote',
                'cta_button_ar' => 'اطلب عرض سعر',
                'cta_url' => '/contact',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        foreach ([
            ['hurghada', 'Hurghada', 'الغردقة'],
            ['marsa-alam', 'Marsa Alam', 'مرسى علم'],
            ['dahab', 'Dahab', 'دهب'],
            ['north-coast', 'North Coast', 'الساحل الشمالي'],
            ['luxor-aswan', 'Luxor & Aswan', 'الأقصر وأسوان'],
        ] as [$slug, $en, $ar]) {
            Destination::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title_en' => $en,
                    'title_ar' => $ar,
                    'excerpt_en' => "{$en} travel programs with flexible accommodation and support.",
                    'excerpt_ar' => "برامج {$ar} مع إقامة مرنة ودعم قبل الحجز.",
                    'overview_en' => "Travel Wave offers practical {$en} packages for short breaks and seasonal travel.",
                    'overview_ar' => "تقدم Travel Wave باقات عملية إلى {$ar} تناسب العطلات القصيرة والمواسم المختلفة.",
                    'is_featured' => false,
                    'is_active' => true,
                    'sort_order' => 10,
                ]
            );
        }

        $insights = BlogCategory::query()->updateOrCreate(
            ['slug' => 'travel-insights'],
            [
                'name_en' => 'Travel Insights',
                'name_ar' => 'محتوى السفر',
                'description_en' => 'Practical guidance for visas, hotels, flights, and smart travel planning.',
                'description_ar' => 'محتوى عملي عن التأشيرات والفنادق والطيران وتخطيط السفر الذكي.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        foreach ([
            ['top-reasons-visas-get-rejected', 'Top reasons visas get rejected and how to avoid them', 'أهم أسباب رفض التأشيرات وكيف تتجنبها'],
            ['best-time-to-apply-for-europe-visas', 'Best time to apply for Europe visas', 'أفضل وقت للتقديم على تأشيرات أوروبا'],
            ['how-to-choose-the-right-hotel', 'How to choose the right hotel for your trip', 'كيف تختار الفندق المناسب لرحلتك'],
        ] as [$slug, $titleEn, $titleAr]) {
            BlogPost::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'blog_category_id' => $insights->id,
                    'title_en' => $titleEn,
                    'title_ar' => $titleAr,
                    'excerpt_en' => 'A practical Travel Wave article designed to help travelers make better booking and planning decisions.',
                    'excerpt_ar' => 'مقال عملي من Travel Wave لمساعدة المسافر على اتخاذ قرارات أفضل في الحجز والتخطيط.',
                    'content_en' => 'Travel Wave recommends starting with clear objectives, matching your documents to the service required, and planning early enough to avoid last-minute pressure. Organized preparation usually creates a smoother customer journey and better booking confidence.',
                    'content_ar' => 'تنصح Travel Wave بالبدء بهدف واضح، ومطابقة المستندات مع الخدمة المطلوبة، والتخطيط المبكر لتجنب ضغط اللحظات الأخيرة. فكلما كان التحضير منظمًا كانت الرحلة أوضح وأكثر سهولة في التنفيذ.',
                    'is_published' => true,
                    'is_featured' => true,
                    'published_at' => now()->subDays(3),
                ]
            );
        }

        foreach ([
            ['client_name' => 'Ahmed Samir', 'client_role_en' => 'Family Traveler', 'client_role_ar' => 'مسافر مع العائلة'],
            ['client_name' => 'Mona Adel', 'client_role_en' => 'Visa Client', 'client_role_ar' => 'عميلة تأشيرات'],
            ['client_name' => 'Youssef Nabil', 'client_role_en' => 'Corporate Traveler', 'client_role_ar' => 'مسافر أعمال'],
        ] as $index => $testimonial) {
            Testimonial::query()->updateOrCreate(
                ['client_name' => $testimonial['client_name']],
                $testimonial + [
                    'testimonial_en' => 'The team was responsive, organized, and helped us move forward with a much clearer travel plan.',
                    'testimonial_ar' => 'كان الفريق سريع الاستجابة ومنظمًا وساعدنا على التحرك بخطة سفر أوضح بكثير.',
                    'rating' => 5,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $pages = [
            'home' => [
                'title_en' => 'Home',
                'title_ar' => 'الرئيسية',
                'hero_badge_en' => 'Integrated Travel Services',
                'hero_badge_ar' => 'خدمات سفر متكاملة',
                'hero_title_en' => 'We organize your journey from the first step to the final detail',
                'hero_title_ar' => 'ننظم رحلتك من أول خطوة حتى آخر تفصيلة',
                'hero_subtitle_en' => 'With Travel Wave, you get an integrated service that makes travel planning easier, from file preparation and bookings to destination selection and trip planning.',
                'hero_subtitle_ar' => 'مع Travel Wave تحصل على خدمة متكاملة تجعل تخطيط السفر أسهل، من تجهيز الملف والحجوزات إلى اختيار الوجهة وتنظيم الرحلة.',
                'hero_primary_cta_text_en' => 'Browse Visas',
                'hero_primary_cta_text_ar' => 'تصفح التأشيرات',
                'hero_primary_cta_url' => '/visas',
                'hero_secondary_cta_text_en' => 'Browse Trips',
                'hero_secondary_cta_text_ar' => 'تصفح الرحلات',
                'hero_secondary_cta_url' => '/domestic-tourism',
                'intro_title_en' => 'One team for visas, trips, flights, and hotels',
                'intro_title_ar' => 'فريق واحد للتأشيرات والرحلات والطيران والفنادق',
                'intro_body_en' => 'Travel Wave combines outbound travel, domestic tourism, visa services, hotels, and flights in one organized customer journey.',
                'intro_body_ar' => 'تجمع Travel Wave بين السفر الخارجي والسياحة الداخلية وخدمات التأشيرات والفنادق والطيران ضمن رحلة عميل منظمة وواضحة.',
                'sections' => [
                    'services' => [
                        ['title_en' => 'Visa Services', 'title_ar' => 'خدمات التأشيرات', 'text_en' => 'Guided support for preparing files and understanding requirements.', 'text_ar' => 'دعم منظم لتجهيز الملفات وفهم المتطلبات.', 'icon' => 'VS'],
                        ['title_en' => 'International Travel', 'title_ar' => 'السياحة الخارجية', 'text_en' => 'Travel planning for outbound tourism and flexible offers.', 'text_ar' => 'تخطيط للسفر الخارجي وعروض مرنة للرحلات.', 'icon' => 'IT'],
                        ['title_en' => 'Domestic Tourism', 'title_ar' => 'السياحة الداخلية', 'text_en' => 'Egypt destination packages with hotel and activity options.', 'text_ar' => 'باقات داخل مصر مع خيارات فنادق وأنشطة.', 'icon' => 'DT'],
                    ],
                    'why_choose_us' => [
                        ['title_en' => 'Organization', 'title_ar' => 'التنظيم', 'text_en' => 'We make the travel process easy to follow.', 'text_ar' => 'نجعل خطوات السفر سهلة وواضحة.'],
                        ['title_en' => 'Responsiveness', 'title_ar' => 'سرعة الاستجابة', 'text_en' => 'Practical answers and follow-up when you need them.', 'text_ar' => 'ردود عملية ومتابعة وقت الحاجة.'],
                    ],
                    'how_it_works' => [
                        ['title_en' => 'Share your need', 'title_ar' => 'أخبرنا بطلبك', 'text_en' => 'Tell us the destination, purpose, and timing.', 'text_ar' => 'أخبرنا بالوجهة والهدف والموعد.'],
                        ['title_en' => 'Receive a clear plan', 'title_ar' => 'استلم خطة واضحة', 'text_en' => 'We outline the suitable service and next steps.', 'text_ar' => 'نوضح لك الخدمة المناسبة والخطوات التالية.'],
                    ],
                    'promo' => [
                        'title_en' => 'Flights and hotels, planned together',
                        'title_ar' => 'الطيران والفنادق ضمن خطة واحدة',
                        'text_en' => 'Bundle your trip with coordinated flight and hotel support for smoother planning.',
                        'text_ar' => 'نسق رحلتك مع دعم متكامل للطيران والفنادق لتخطيط أكثر سهولة.',
                        'button_en' => 'Explore Services',
                        'button_ar' => 'اكتشف الخدمات',
                        'url' => '/flights',
                    ],
                    'inquiry' => [
                        'title_en' => 'Tell us where you want to go',
                        'title_ar' => 'أخبرنا إلى أين تريد السفر',
                        'text_en' => 'Our team can recommend the right visa, destination, or booking path.',
                        'text_ar' => 'يمكن لفريقنا ترشيح التأشيرة أو الوجهة أو مسار الحجز الأنسب لك.',
                    ],
                    'final_cta' => [
                        'title_en' => 'Start your request with confidence',
                        'title_ar' => 'ابدأ طلبك بثقة',
                        'text_en' => 'Travel planning becomes easier when every detail has a clear next step.',
                        'text_ar' => 'يصبح تخطيط السفر أسهل عندما تكون كل تفصيلة مرتبطة بخطوة واضحة.',
                        'button_en' => 'Contact Travel Wave',
                        'button_ar' => 'تواصل مع Travel Wave',
                        'url' => '/contact',
                    ],
                ],
            ],
            'visas' => ['title_en' => 'Visas', 'title_ar' => 'التأشيرات', 'hero_title_en' => 'Overseas visa services', 'hero_title_ar' => 'خدمات التأشيرات الخارجية', 'hero_subtitle_en' => 'Explore categories, compare countries, and request support.', 'hero_subtitle_ar' => 'استعرض الفئات وقارن بين الدول واطلب الدعم المناسب.'],
            'domestic' => ['title_en' => 'Domestic Tourism', 'title_ar' => 'السياحة الداخلية', 'hero_title_en' => 'Domestic tourism in Egypt', 'hero_title_ar' => 'السياحة الداخلية داخل مصر', 'hero_subtitle_en' => 'Trips to the most requested destinations with practical packages.', 'hero_subtitle_ar' => 'رحلات إلى أكثر الوجهات طلبًا مع باقات عملية.'],
            'flights' => ['title_en' => 'Flights', 'title_ar' => 'الطيران', 'hero_title_en' => 'Flight booking support', 'hero_title_ar' => 'دعم حجز الطيران', 'intro_title_en' => 'Choose the right route and timing', 'intro_title_ar' => 'اختر المسار والتوقيت المناسبين', 'intro_body_en' => 'Travel Wave supports customers with flight planning, route comparison, and booking coordination.', 'intro_body_ar' => 'تدعم Travel Wave العملاء في تخطيط الرحلات الجوية ومقارنة المسارات وتنسيق الحجز.'],
            'hotels' => ['title_en' => 'Hotels', 'title_ar' => 'الفنادق', 'hero_title_en' => 'Hotel booking support', 'hero_title_ar' => 'دعم حجز الفنادق', 'intro_title_en' => 'Stay options that match your budget', 'intro_title_ar' => 'خيارات إقامة تناسب ميزانيتك', 'intro_body_en' => 'We help travelers compare hotel categories and select the right stay for their trip.', 'intro_body_ar' => 'نساعد المسافرين على مقارنة الفئات الفندقية واختيار الإقامة المناسبة لرحلتهم.'],
            'about' => ['title_en' => 'About Us', 'title_ar' => 'من نحن', 'hero_title_en' => 'About Travel Wave', 'hero_title_ar' => 'عن Travel Wave', 'intro_title_en' => 'A travel company built around clarity and follow-up', 'intro_title_ar' => 'شركة سفر مبنية على الوضوح والمتابعة', 'intro_body_en' => 'Travel Wave was created to organize the customer journey across visas, travel planning, flights, hotels, and domestic tourism with one reliable team.', 'intro_body_ar' => 'تم إنشاء Travel Wave لتنظيم رحلة العميل في التأشيرات وتخطيط السفر والطيران والفنادق والسياحة الداخلية من خلال فريق واحد موثوق.'],
            'contact' => ['title_en' => 'Contact', 'title_ar' => 'تواصل معنا', 'hero_title_en' => 'Speak with Travel Wave', 'hero_title_ar' => 'تحدث مع Travel Wave', 'intro_title_en' => 'We are ready to help with the next step', 'intro_title_ar' => 'نحن جاهزون لمساعدتك في الخطوة التالية', 'intro_body_en' => 'Share your inquiry and our team will guide you toward the right service.', 'intro_body_ar' => 'أرسل استفسارك وسيقوم فريقنا بتوجيهك إلى الخدمة المناسبة.'],
            'blog' => ['title_en' => 'Blog', 'title_ar' => 'المقالات', 'hero_title_en' => 'Travel insights and practical tips', 'hero_title_ar' => 'محتوى السفر والنصائح العملية', 'hero_subtitle_en' => 'Articles that help travelers prepare before booking or applying.', 'hero_subtitle_ar' => 'مقالات تساعد المسافرين على الاستعداد قبل الحجز أو التقديم.'],
        ];

        foreach ($pages as $key => $page) {
            Page::query()->updateOrCreate(['key' => $key], $page + ['slug' => $key, 'is_active' => true]);
        }

        foreach ([
            ['header', null, 'Home', 'الرئيسية', null, 'home', 1],
            ['header', null, 'Overseas Visas', 'التأشيرات الخارجية', null, 'visas.index', 2],
            ['header', null, 'Domestic Tourism', 'السياحة الداخلية', null, 'destinations.index', 3],
            ['header', null, 'Flights', 'الطيران', null, 'flights', 4],
            ['header', null, 'Hotels', 'الفنادق', null, 'hotels', 5],
            ['header', null, 'About Us', 'من نحن', null, 'about', 6],
            ['header', null, 'Blog', 'المقالات', null, 'blog.index', 7],
            ['header', null, 'Contact Us', 'تواصل معنا', null, 'contact', 8],
            ['footer', null, 'About Us', 'من نحن', null, 'about', 1],
            ['footer', null, 'Blog', 'المقالات', null, 'blog.index', 2],
            ['footer', null, 'Contact Us', 'تواصل معنا', null, 'contact', 3],
        ] as [$location, $parentId, $titleEn, $titleAr, $url, $routeName, $sortOrder]) {
            MenuItem::query()->updateOrCreate(
                ['location' => $location, 'title_en' => $titleEn],
                [
                    'parent_id' => $parentId,
                    'title_ar' => $titleAr,
                    'url' => $url,
                    'route_name' => $routeName,
                    'target' => '_self',
                    'sort_order' => $sortOrder,
                    'is_active' => true,
                ]
            );
        }

        Inquiry::query()->updateOrCreate(
            ['full_name' => 'Sample Travel Lead'],
            [
                'type' => 'general',
                'phone' => '+20 111 222 3333',
                'email' => 'lead@example.com',
                'destination' => 'France',
                'service_type' => 'Visa',
                'message' => 'I need support preparing my file for a Europe visa this summer.',
                'status' => 'new',
                'preferred_language' => 'en',
            ]
        );

    }
}
