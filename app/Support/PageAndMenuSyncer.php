<?php

namespace App\Support;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PageAndMenuSyncer
{
    public static function sync(): bool
    {
        try {
            // 1. Ensure columns on menu_items table
            if (Schema::hasTable('menu_items')) {
                Schema::table('menu_items', function ($table) {
                    if (! Schema::hasColumn('menu_items', 'type')) {
                        $table->string('type')->default('custom')->nullable();
                    }
                    if (! Schema::hasColumn('menu_items', 'page_id')) {
                        $table->unsignedBigInteger('page_id')->nullable();
                    }
                    if (! Schema::hasColumn('menu_items', 'icon')) {
                        $table->string('icon')->nullable();
                    }
                });
            }

            // 2. Create / Update Umrah Page
            if (Schema::hasTable('pages')) {
                $umrahSections = [
                    'section_order' => [
                        'hero' => ['sort_order' => 1, 'enabled' => true],
                        'intro' => ['sort_order' => 2, 'enabled' => true],
                        'feature_blocks' => ['sort_order' => 3, 'enabled' => true],
                        'faqs' => ['sort_order' => 4, 'enabled' => true],
                        'cta' => ['sort_order' => 5, 'enabled' => true],
                    ],
                    'feature_blocks' => [
                        [
                            'icon' => 'fa-kaaba',
                            'title_ar' => 'تأشيرات العمرة السريعة',
                            'title_en' => 'Fast Umrah Visas',
                            'text_ar' => 'استخراج وتنسيق تأشيرات العمرة الإلكترونية والزيارة بسرعة وسهولة وبدون تعقيدات.',
                            'text_en' => 'Quick and hassle-free processing of Umrah e-visas and visit permits.',
                            'is_active' => true,
                            'sort_order' => 1,
                        ],
                        [
                            'icon' => 'fa-hotel',
                            'title_ar' => 'فنادق مطلة على الحرمين',
                            'title_en' => 'Hotels Near Haram',
                            'text_ar' => 'إقامات فندقية في أفضل الفنادق 5 نجوم و 4 نجوم في مكة والمدينة القريبة جداً من أبواب الحرم.',
                            'text_en' => 'Luxury stays at 4-star and 5-star hotels just steps away from the Holy Mosques in Makkah & Madinah.',
                            'is_active' => true,
                            'sort_order' => 2,
                        ],
                        [
                            'icon' => 'fa-bus',
                            'title_ar' => 'تنقلات فاخرة وخاصة',
                            'title_en' => 'VIP Transport Services',
                            'text_ar' => 'سيارات وحافلات حديثة مكيفة للتنقل بين المطارات والفنادق والأماكن المقدسة بأعلى درجات الراحة.',
                            'text_en' => 'Modern air-conditioned buses and private VIP cars for seamless airport and holy site transfers.',
                            'is_active' => true,
                            'sort_order' => 3,
                        ],
                        [
                            'icon' => 'fa-mosque',
                            'title_ar' => 'مزارات وإرشاد ديني',
                            'title_en' => 'Holy Site Tours',
                            'text_ar' => 'رحلات مزارات إلى جبل أحد، مسجد قباء، غار حراء، والمشاعر المقدسة برفقة مرشدين متخصصين.',
                            'text_en' => 'Guided visits to historical Islamic landmarks including Mount Uhud, Quba Mosque, and Cave Hira.',
                            'is_active' => true,
                            'sort_order' => 4,
                        ],
                        [
                            'icon' => 'fa-headset',
                            'title_ar' => 'دعم ومتابعة 24/7',
                            'title_en' => '24/7 On-Ground Support',
                            'text_ar' => 'فريق عمل متخصص متواجد على مدار الساعة لمساعدتك وتلبية كافة احتياجاتك طوال فترة الرحلة.',
                            'text_en' => 'Dedicated multi-lingual staff available around the clock to assist you in Saudi Arabia.',
                            'is_active' => true,
                            'sort_order' => 5,
                        ],
                    ],
                    'faqs' => [
                        [
                            'question_ar' => 'ما هي الأوراق المطلوبة لتقديم تأشيرة العمرة؟',
                            'question_en' => 'What documents are required for an Umrah visa?',
                            'answer_ar' => 'يتطلب تقديم العمرة صورة جواز سفر ساري لمدة لا تقل عن 6 أشهر وصورة شخصية حديثة بخلفية بيضاء.',
                            'answer_en' => 'A valid passport with at least 6 months validity and a recent passport photo with a white background.',
                            'is_active' => true,
                            'sort_order' => 1,
                        ],
                        [
                            'question_ar' => 'هل تشمل باقات العمرة تذاكر الطيران والفنادق؟',
                            'question_en' => 'Do Umrah packages include flights and hotels?',
                            'answer_ar' => 'نعم، نوفر باقات شاملة تشمل التأشيرة، تذاكر الطيران، الإقامة الفندقية، والتنقلات، كما نوفر باقات مخصصة حسب طلبك.',
                            'answer_en' => 'Yes, our packages cover visas, flights, hotel stays, and ground transport. Customized options are also available.',
                            'is_active' => true,
                            'sort_order' => 2,
                        ],
                        [
                            'question_ar' => 'هل يمكن تصميم برنامج عمرة خاص بالمسافرين؟',
                            'question_en' => 'Can I customize a private Umrah program?',
                            'answer_ar' => 'نعم، يمكننا تصميم برنامج مخصص يطابق مواعيدك المفضلة وميزانيتك وفندقك المفضّل بخدمات VIP.',
                            'answer_en' => 'Absolutely! We can design a tailored itinerary matching your exact dates, preferred hotel, and budget.',
                            'is_active' => true,
                            'sort_order' => 3,
                        ],
                    ],
                    'cta' => [
                        'title_ar' => 'جاهز لرحلة العمرة؟ تواصل معنا اليوم واحجز مكانك',
                        'title_en' => 'Ready for Umrah? Contact us today to reserve your place',
                        'text_ar' => 'فريقنا المتخصص في رحلات العمرة جاهز للرد على كافة استفساراتك واختيار الباقة الأنسب لك ولعائلتك.',
                        'text_en' => 'Our Umrah specialists are ready to answer your questions and assist you in picking the ideal package.',
                        'button_ar' => 'تواصل معنا الآن',
                        'button_en' => 'Contact Us Now',
                        'url' => '/contact',
                    ],
                ];

                $umrahPage = Page::updateOrCreate(
                    ['key' => 'umrah'],
                    [
                        'key' => 'umrah',
                        'title_ar' => 'رحلات العمرة المباركة',
                        'title_en' => 'Umrah Packages & Spiritual Journeys',
                        'slug' => 'umrah',
                        'hero_badge_ar' => 'رحلات العمرة المباركة 🕌',
                        'hero_badge_en' => 'Blessed Umrah Journeys 🕌',
                        'hero_title_ar' => 'رحلات العمرة خطط سفر ميسرة وإقامة جوار الحرمين',
                        'hero_title_en' => 'Umrah Trips Comfort, Guidance & Premium Stays',
                        'hero_subtitle_ar' => 'نقدم لك باقات عمرة متكاملة تشمل تأشيرات العمرة، حجز الفنادق في مكة والمدينة، تذاكر الطيران، والتنقلات المريحة لضمان رحلة إيمانية مباركة ومريحة.',
                        'hero_subtitle_en' => 'Complete Umrah packages including Umrah visas, top Makkah and Madinah hotel bookings, flights, and luxury transport for a peaceful spiritual trip.',
                        'hero_primary_cta_text_ar' => 'احجز رحلتك الآن',
                        'hero_primary_cta_text_en' => 'Book Your Trip',
                        'hero_primary_cta_url' => '/contact',
                        'hero_secondary_cta_text_ar' => 'استعرض المميزات',
                        'hero_secondary_cta_text_en' => 'View Features',
                        'hero_secondary_cta_url' => '#features',
                        'intro_title_ar' => 'تجربة عمرة فريدة تجمع بين الراحة والروحانية',
                        'intro_title_en' => 'A Seamless Umrah Experience Combining Comfort & Guidance',
                        'intro_body_ar' => 'في ترافل ويف نسعى لجعل رحلتك إلى بيت الله الحرام تجربة استثنائية مليئة بالراحة والطمأنينة. نوفر لك برامج عمرة متنوعة تناسب الأفراد والعائلات والمجموعات مع إشراف كامل وتأشيرات سريعة وحجوزات فندقية في أفضل المواقع القريبة من الحرم المكي والمسجد النبوي.',
                        'intro_body_en' => 'Travel Wave is dedicated to providing an extraordinary Umrah experience. We offer versatile programs tailored for individuals, families, and group travel with full guidance, quick visa processing, and premium hotel bookings near the Holy Mosques.',
                        'sections' => $umrahSections,
                        'meta_title_ar' => 'رحلات العمرة | ترافل ويف للسياحة',
                        'meta_title_en' => 'Umrah Packages | Travel Wave Tourism',
                        'meta_description_ar' => 'احجز رحلة العمرة مع ترافل ويف. باقات متميزة تشمل تأشيرة العمرة، فنادق الحرمين، الطيران والتنقلات وإشراف كامل.',
                        'meta_description_en' => 'Book your Umrah journey with Travel Wave. Premium packages covering Umrah visas, top Haram hotels, flights & transport.',
                        'is_active' => true,
                    ]
                );

                // 3. Create / Update Hajj Page
                $hajjSections = [
                    'section_order' => [
                        'hero' => ['sort_order' => 1, 'enabled' => true],
                        'intro' => ['sort_order' => 2, 'enabled' => true],
                        'feature_blocks' => ['sort_order' => 3, 'enabled' => true],
                        'faqs' => ['sort_order' => 4, 'enabled' => true],
                        'cta' => ['sort_order' => 5, 'enabled' => true],
                    ],
                    'feature_blocks' => [
                        [
                            'icon' => 'fa-campground',
                            'title_ar' => 'مخيمات فاخرة في المشاعر',
                            'title_en' => 'VIP Camps in Mina & Arafat',
                            'text_ar' => 'مخيمات مكيفة ومجهزة بالكامل بوجبات فندقية وخدمات ضيافة ممتازة في منى وعرفات.',
                            'text_en' => 'Fully air-conditioned luxury tents with open-buffet catering and high-end hospitality in Mina and Arafat.',
                            'is_active' => true,
                            'sort_order' => 1,
                        ],
                        [
                            'icon' => 'fa-user-nurse',
                            'title_ar' => 'مرشدون دينيون وأطباء مرافقون',
                            'title_en' => 'Spiritual & Medical Team',
                            'text_ar' => 'نخبة من الدعاة للإرشاد الديني وفريق طبي مرافق لتقديم الرعاية الصحية طوال أيام الحج.',
                            'text_en' => 'Experienced Islamic scholars for religious guidance and dedicated medical doctors accompanying the group.',
                            'is_active' => true,
                            'sort_order' => 2,
                        ],
                        [
                            'icon' => 'fa-building-columns',
                            'title_ar' => 'فنادق قريبة جداً من الحرم',
                            'title_en' => 'Near-Haram Luxury Hotels',
                            'text_ar' => 'إقامات ممتازة في أبراج مكة والفنادق المطلة لتسهيل أداء الصلوات والطواف بدون عناء.',
                            'text_en' => 'Prime hotel stays in Makkah towers within walking distance to the Holy Mosque.',
                            'is_active' => true,
                            'sort_order' => 3,
                        ],
                        [
                            'icon' => 'fa-train-subway',
                            'title_ar' => 'قطار المشاعر وتنقلات خاصة',
                            'title_en' => 'Sacred Sites Train & Shuttles',
                            'text_ar' => 'التنقل بين المشاعر المقدسة عبر قطار المشاعر والحافلات الحديثة السريعة.',
                            'text_en' => 'Fast movement between holy sites via the Al Mashaaer Al Mugaddassah Metro and private buses.',
                            'is_active' => true,
                            'sort_order' => 4,
                        ],
                        [
                            'icon' => 'fa-passport',
                            'title_ar' => 'تصاريح وتأشيرات الحج الرسمية',
                            'title_en' => 'Official Hajj Permits',
                            'text_ar' => 'استخراج وتنسيق جميع التصاريح والأوراق الرسمية المعتمدة للحجاج بكل دقة.',
                            'text_en' => 'Complete handling and authorization of official Hajj permits and visas.',
                            'is_active' => true,
                            'sort_order' => 5,
                        ],
                    ],
                    'faqs' => [
                        [
                            'question_ar' => 'متى يبدأ التسجيل لرحلات الحج؟',
                            'question_en' => 'When does Hajj registration open?',
                            'answer_ar' => 'يبدأ التسجيل المبكر قبل موسم الحج بأشهر، ونوصي دائماً بالتسجيل المبكر لضمان الأماكن والمخيمات المتميزة.',
                            'answer_en' => 'Early registration opens several months prior to the Hajj season. Early booking is strongly recommended for premium camps.',
                            'is_active' => true,
                            'sort_order' => 1,
                        ],
                        [
                            'question_ar' => 'ما هي الخدمات المقدمة داخل مخيمات منى وعرفات؟',
                            'question_en' => 'What services are provided in Mina & Arafat tents?',
                            'answer_ar' => 'تشمل الإقامة المكيفة، الوجبات الفندقية (بوفيه مفتوح)، المشروبات المستمرة، والخدمات الطبية والإرشادية على مدار الساعة.',
                            'answer_en' => 'Services include air-conditioned lodging, open buffet dining, 24/7 beverages, medical care, and constant scholar guidance.',
                            'is_active' => true,
                            'sort_order' => 2,
                        ],
                        [
                            'question_ar' => 'هل يتوفر إرشاد ورعاية خاصة لكبار السن والنساء؟',
                            'question_en' => 'Are there specialized services for seniors and women?',
                            'answer_ar' => 'نعم، نوفر مرافقين وخدمات الكراسي المتحركة وإرشاد خاص لضمان أداء مناسك الحج بيسر وسهولة للجميع.',
                            'answer_en' => 'Yes, we provide wheelchair assistance, dedicated handlers, and tailored guidance for seniors and families.',
                            'is_active' => true,
                            'sort_order' => 3,
                        ],
                    ],
                    'cta' => [
                        'title_ar' => 'احجز رحلة الحج المباركة معنا واستعد لرحلة العمر',
                        'title_en' => 'Reserve Your Hajj Journey with Us Today',
                        'text_ar' => 'مقاعدنا محدودة لضمان تقديم أفضل مستوى من الخدمة والعناية لكل حاج. اتصل بنا للحصول على كافة التفاصيل.',
                        'text_en' => 'Seats are limited to ensure top-quality personalized service for every pilgrim. Call us now to secure your spot.',
                        'button_ar' => 'سجل اهتمامك الآن',
                        'button_en' => 'Register Your Interest',
                        'url' => '/contact',
                    ],
                ];

                $hajjPage = Page::updateOrCreate(
                    ['key' => 'hajj'],
                    [
                        'key' => 'hajj',
                        'title_ar' => 'رحلات الحج المباركة',
                        'title_en' => 'Hajj Pilgrimage Services & Luxury Programs',
                        'slug' => 'hajj',
                        'hero_badge_ar' => 'رحلات الحج الميمونة 🕋',
                        'hero_badge_en' => 'Blessed Hajj Pilgrimage 🕋',
                        'hero_title_ar' => 'أداء فريضة الحج بطمأنينة ودعم متكامل في المشاعر المقدسة',
                        'hero_title_en' => 'Perform Hajj with Peace of Mind & Dedicated On-Site Care',
                        'hero_subtitle_ar' => 'نصحبك في رحلة العمر لأداء فريضة الحج مع أفضل برامج الحج الفاخر والميسر، مخيمات مجهزة بأعلى مستويات الراحة في منى وعرفات، وإشراف ديني وطبي كامل.',
                        'hero_subtitle_en' => 'Accompanying you on the journey of a lifetime. Premium and luxury Hajj packages with fully equipped VIP camps in Mina and Arafat and round-the-clock medical & spiritual assistance.',
                        'hero_primary_cta_text_ar' => 'استفسر عن رحلات الحج',
                        'hero_primary_cta_text_en' => 'Inquire About Hajj',
                        'hero_primary_cta_url' => '/contact',
                        'hero_secondary_cta_text_ar' => 'تفاصيل البرامج',
                        'hero_secondary_cta_text_en' => 'Program Details',
                        'hero_secondary_cta_url' => '#features',
                        'intro_title_ar' => 'رحلة العمر لأداء الفريضة برعاية واهتمام كامل',
                        'intro_title_en' => 'The Journey of a Lifetime Supported by Expertise & Care',
                        'intro_body_ar' => 'حج بيت الله الحرام هو أمنية كل مسلم، وفي ترافل ويف نعمل بشغف وخبرة ممتدة لتوفير رحلة حج منظمة بأعلى معايير الجودة والراحة. نوفر لك الإشراف الإداري والديني، السكن القريب من الحرم، والمخيمات المجهزة بالكامل في المشاعر المقدسة ليتفرغ الحاج للعبادة والدعاء.',
                        'intro_body_en' => 'Performing Hajj is the dream of every Muslim. At Travel Wave, we utilize our experience to deliver meticulously organized Hajj programs. From proximity to the Holy Haram to VIP air-conditioned tents in Mina and Arafat, we take care of all logistics so you can focus purely on your worship.',
                        'sections' => $hajjSections,
                        'meta_title_ar' => 'رحلات الحج | ترافل ويف للسياحة',
                        'meta_title_en' => 'Hajj Pilgrimage Services | Travel Wave Tourism',
                        'meta_description_ar' => 'احجز رحلة الحج مع ترافل ويف. برامج حج فاخرة وميسرة تشمل التصاريح الرسمية، فنادق الحرم، مخيمات منى وعرفات وإشراف ديني وطبي.',
                        'meta_description_en' => 'Book your Hajj pilgrimage with Travel Wave. Luxury Hajj programs featuring official permits, Makkah hotels, Mina & Arafat VIP camps.',
                        'is_active' => true,
                    ]
                );

                require_once database_path('seeders/AboutPageSeeder.php');
                (new \Database\Seeders\AboutPageSeeder())->run();

                // 4. Populate Main Header Menu Items with Dropdown for Religious Tourism
                if (Schema::hasTable('menu_items')) {
                    $headerItems = [
                        ['title_ar' => 'الرئيسية', 'title_en' => 'Home', 'page_key' => 'home', 'type' => 'page', 'sort_order' => 1],
                        ['title_ar' => 'التأشيرات الخارجية', 'title_en' => 'Visas', 'page_key' => 'visas', 'type' => 'page', 'sort_order' => 2],
                        ['title_ar' => 'السياحة الداخلية', 'title_en' => 'Domestic Tourism', 'page_key' => 'domestic', 'type' => 'page', 'sort_order' => 3],
                        ['title_ar' => 'الطيران', 'title_en' => 'Flights', 'page_key' => 'flights', 'type' => 'page', 'sort_order' => 4],
                        ['title_ar' => 'الفنادق', 'title_en' => 'Hotels', 'page_key' => 'hotels', 'type' => 'page', 'sort_order' => 5],
                        ['title_ar' => 'السياحة الدينية', 'title_en' => 'Religious Tourism', 'type' => 'submenu', 'sort_order' => 6],
                        ['title_ar' => 'من نحن', 'title_en' => 'About Us', 'page_key' => 'about', 'type' => 'page', 'sort_order' => 7],
                        ['title_ar' => 'المقالات', 'title_en' => 'Blog', 'type' => 'custom', 'route_name' => 'blog.index', 'sort_order' => 8],
                        ['title_ar' => 'تواصل معنا', 'title_en' => 'Contact Us', 'page_key' => 'contact', 'type' => 'page', 'sort_order' => 9],
                    ];

                    $createdParents = [];

                    foreach ($headerItems as $itemData) {
                        $pageId = null;
                        if (! empty($itemData['page_key'])) {
                            $page = Page::where('key', $itemData['page_key'])->first();
                            if ($page) {
                                $pageId = $page->id;
                            }
                        }

                        $existing = MenuItem::where('location', 'header')->where('title_ar', $itemData['title_ar'])->first();
                        if ($existing) {
                            $existing->update([
                                'page_id' => $pageId ?: $existing->page_id,
                                'title_en' => $existing->title_en ?: $itemData['title_en'],
                                'type' => $existing->type ?: ($itemData['type'] ?? 'custom'),
                            ]);
                            $createdParents[$itemData['title_ar']] = $existing;
                        } else {
                            $item = MenuItem::create([
                                'location' => 'header',
                                'parent_id' => null,
                                'type' => $itemData['type'] ?? 'custom',
                                'page_id' => $pageId,
                                'title_en' => $itemData['title_en'],
                                'title_ar' => $itemData['title_ar'],
                                'route_name' => $itemData['route_name'] ?? null,
                                'target' => '_self',
                                'sort_order' => $itemData['sort_order'],
                                'is_active' => true,
                            ]);
                            $createdParents[$itemData['title_ar']] = $item;
                        }
                    }

                    // Attach Umrah & Hajj under "السياحة الدينية" if they are currently unparented
                    if (isset($createdParents['السياحة الدينية'])) {
                        $religiousParent = $createdParents['السياحة الدينية'];

                        $umrahPg = Page::where('key', 'umrah')->first();
                        $umrahItem = MenuItem::where('location', 'header')->where('title_ar', 'رحلات العمرة')->first();
                        if ($umrahItem) {
                            if (! $umrahItem->parent_id) {
                                $umrahItem->update(['parent_id' => $religiousParent->id]);
                            }
                        } else {
                            MenuItem::create([
                                'location' => 'header',
                                'parent_id' => $religiousParent->id,
                                'type' => 'page',
                                'page_id' => $umrahPg?->id,
                                'title_en' => 'Umrah Trips',
                                'title_ar' => 'رحلات العمرة',
                                'target' => '_self',
                                'sort_order' => 1,
                                'is_active' => true,
                            ]);
                        }

                        $hajjPg = Page::where('key', 'hajj')->first();
                        $hajjItem = MenuItem::where('location', 'header')->where('title_ar', 'رحلات الحج')->first();
                        if ($hajjItem) {
                            if (! $hajjItem->parent_id) {
                                $hajjItem->update(['parent_id' => $religiousParent->id]);
                            }
                        } else {
                            MenuItem::create([
                                'location' => 'header',
                                'parent_id' => $religiousParent->id,
                                'type' => 'page',
                                'page_id' => $hajjPg?->id,
                                'title_en' => 'Hajj Pilgrimage',
                                'title_ar' => 'رحلات الحج',
                                'target' => '_self',
                                'sort_order' => 2,
                                'is_active' => true,
                            ]);
                        }
                    }
                }
            }

            return true;
        } catch (Throwable $e) {
            report($e);
            return false;
        }
    }
}
