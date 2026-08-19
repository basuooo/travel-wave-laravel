<?php

namespace Database\Seeders;

use App\Models\FunnelTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FunnelTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── 1. Schengen Visa Eligibility ──────────────────────────────────────
            [
                'name' => 'Schengen Visa Eligibility',
                'slug' => 'schengen-visa-eligibility',
                'category' => 'Travel',
                'description' => 'فحص أهلية متقدم للحصول على تأشيرة الشنغن الأوروبية مع احتساب دقيق لنسبة القبول وربط واتساب فوري.',
                'thumbnail' => '/assets/images/templates/schengen.jpg',
                'sort_order' => 1,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#1e40af',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => [
                        'enabled' => true,
                    ],
                    'steps' => [
                        [
                            'title' => 'اختبار الجاهزية للتأشيرة الشنغن',
                            'subtitle' => 'اكتشف نسبة قبول طلبك قبل التقديم خلال دقيقتين بدقة عالية',
                            'step_type' => 'welcome',
                            'elements' => [
                                [
                                    'element_type' => 'heading',
                                    'label' => 'هل أنت جاهز للحصول على تأشيرة الشنغن؟',
                                    'properties' => ['font_size' => '2xl'],
                                ],
                                [
                                    'element_type' => 'text',
                                    'label' => 'أجب على الأسئلة التالية لنحدد لك السفارة الأنسب ونسبة قبول ملفك وتجهيز الموعد المناسب.',
                                ],
                            ],
                        ],
                        [
                            'title' => 'الجنسية والإقامة',
                            'subtitle' => 'تحديد نطاق التقديم ومقر الإقامة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي جنسيتك ومكان إقامتك الحالي؟',
                                    'question_key' => 'nationality_residence',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🇸🇦 مواطن سعودي / مقيم في السعودية', 'value' => 'Saudi / Resident KSA', 'score' => 20],
                                            ['label' => '🇦🇪 مقيم / مواطن في الإمارات أو دول الخليج', 'value' => 'GCC Resident', 'score' => 20],
                                            ['label' => '🇪🇬 مقيم في مصر', 'value' => 'Egypt Resident', 'score' => 15],
                                            ['label' => '🌍 جنسية أو إقامة في دولة أخرى', 'value' => 'Other Country', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الحساب البنكي والرصيد',
                            'subtitle' => 'تقييم الملاءة المالية المطلوبة للسفارة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو متوسط الرصيد المتوفر في حسابك البنكي لآخر 3-6 أشهر؟',
                                    'question_key' => 'bank_balance',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'أكثر من 30,000 ريال / 250,000 جنيه (ممتاز)', 'value' => 'Over 30k SAR', 'score' => 30],
                                            ['label' => 'من 15,000 إلى 30,000 ريال / 120,000 جنيه (جيد جداً)', 'value' => '15k-30k SAR', 'score' => 20],
                                            ['label' => 'أقل من 15,000 ريال / يوجد حساب بنكي بحركات نشطة', 'value' => 'Under 15k SAR', 'score' => 10],
                                            ['label' => 'لا يتوفر حساب بنكي حالياً (أحتاج وجود ضامن)', 'value' => 'No Bank Account', 'score' => 0],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الوضع الوظيفي والمهنة',
                            'subtitle' => 'إثبات مصدر الدخل وتعريف العمل',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو وضعك الوظيفي الحالي؟',
                                    'question_key' => 'employment_status',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'موظف قطاع حكومي أو شركة خاصة مع تعريف بالراتب', 'value' => 'Employed', 'score' => 25],
                                            ['label' => 'صاحب عمل / شريك في سجل تجاري أو شركة', 'value' => 'Business Owner', 'score' => 25],
                                            ['label' => 'طالب / ربة منزل (وجود كفيل أو ضامن عائلي)', 'value' => 'Sponsored', 'score' => 15],
                                            ['label' => 'أعمال حرة بدون إثبات رسمي حالياً', 'value' => 'Freelance / Unofficial', 'score' => 5],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'تاريخ السفر والتأشيرات السابقة',
                            'subtitle' => 'قوة جواز السفر والسجل السابق',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل سبق لك الحصول على تأشيرة شنغن أو السفر لدول أوروبية / أمريكا؟',
                                    'question_key' => 'travel_history',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، حصلت على تأشيرة شنغن سابقة أو تأشيرة أمريكا / بريطانيا', 'value' => 'Previous Strong Visa', 'score' => 25],
                                            ['label' => 'سافرت لدول آسيوية / سياحية بدون تأشيرة معقدة', 'value' => 'Other Travels', 'score' => 15],
                                            ['label' => 'جواز جديد ولم أسافر خارجياً من قبل', 'value' => 'First Time Travel', 'score' => 5],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات التواصل لاستلام التقرير وخطة التقديم',
                            'subtitle' => 'أدخل بياناتك لاستلام النتيجة التفصيلية وتحديد السفارة الأسهل',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'نموذج التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكريم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                            ['name' => 'email', 'label' => 'البريد الإلكتروني', 'type' => 'email', 'required' => false],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🎉 مؤهل بنسبة عالية جداً للشنغن (أكثر من 80%)',
                            'description' => 'ملفك قوي جداً ومستوفي لكافة شروط سفارات الشنغن. فرصتك ممتازة للحصول على التأشيرة بسهولة وسرعة.',
                            'min_score' => 80,
                            'max_score' => 100,
                            'cta_label' => 'تواصل معنا الآن عبر الواتساب لحجز موعد السفارة',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                        [
                            'title' => '👍 مؤهل وتستطيع التقديم بنجاح (60% - 79%)',
                            'description' => 'ملفك جيد ومقبول، وننصح بتقديم أوراق إضافية لضمان قبول الطلب من أول مرة وبدون تأخير.',
                            'min_score' => 60,
                            'max_score' => 79,
                            'cta_label' => 'تحدث مع مستشار التأشيرات لتجهيز ملفك',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 2,
                        ],
                        [
                            'title' => '⚠️ ملفك يحتاج تجهيز ومراجعة قبل التقديم',
                            'description' => 'نسبة القبول الحالية تحتاج تقوية (الضامن أو كشف الحساب). خبيرنا سيساعدك على سد النواقص لتفادي الرفض.',
                            'min_score' => 0,
                            'max_score' => 59,
                            'cta_label' => 'احصل على استشارة تقوية الملف مجاناً',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 3,
                        ],
                    ],
                ],
            ],

            // ── 2. Visa Qualification Quiz ─────────────────────────────────────────
            [
                'name' => 'Visa Qualification Quiz',
                'slug' => 'visa-qualification-quiz',
                'category' => 'Qualification',
                'description' => 'اختبار تأهيل شامل لتأشيرات السفر حول العالم (أمريكا 🇺🇸، بريطانيا 🇬🇧، كندا 🇨🇦، الشنغن 🇪🇺).',
                'thumbnail' => '/assets/images/templates/visa-qual.jpg',
                'sort_order' => 2,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#0d9488',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'اختبار التأهيل الشامل للتأشيرات الدولية',
                            'subtitle' => 'حدد وجهتك المطلوبة واعرف شروطها وفرصتك الفعلية خلال دقيقة واحدة',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'إلى أين تخطط للسفر في رحلتك القادمة؟ ✈️'],
                                ['element_type' => 'text', 'label' => 'فريق Travel Wave يساعدك في معرفة متطلبات كل سفارة وحجز أسرع المواعيد المتاحة.'],
                            ],
                        ],
                        [
                            'title' => 'الوجهة المستهدفة',
                            'subtitle' => 'اختر الدولة التي ترغب في استخراج تأشيرتها',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي التأشيرة التي ترغب بالتقديم عليها؟',
                                    'question_key' => 'target_destination',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🇪🇺 دول الشنغن الأوروبية (فرنسا، إيطاليا، إسبانيا، ألمانيا...)', 'value' => 'Schengen', 'score' => 25],
                                            ['label' => '🇬🇧 بريطانيا (تأشيرة سياحية / تصريح ETA الإلكتروني)', 'value' => 'UK', 'score' => 25],
                                            ['label' => '🇺🇸 الولايات المتحدة الأمريكية (تأشيرة B1/B2 لمذة 5-10 سنوات)', 'value' => 'USA', 'score' => 20],
                                            ['label' => '🇨🇦 كندا أو 🇦🇺 أستراليا', 'value' => 'Canada_Australia', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'صلاحية جواز السفر',
                            'subtitle' => 'الشرط الأساسي لجميع السفارات',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل جواز سفرك ساري المفعول لأكثر من 6 أشهر؟',
                                    'question_key' => 'passport_validity',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، صالح لأكثر من 6 أشهر ويوجد صفحات فارغة', 'value' => 'Valid > 6m', 'score' => 25],
                                            ['label' => 'ينتهي قريباً (أقل من 6 أشهر - أحتاج تجديده)', 'value' => 'Expiring Soon', 'score' => 10],
                                            ['label' => 'لا أملك جواز سفر حالياً (سأقوم بإصداره)', 'value' => 'No Passport', 'score' => 0],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل الرفض السابق',
                            'subtitle' => 'الشفافية في الملف لضمان المعالجة القانونية الصحيحة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل سبق أن رُفض لك طلب تأشيرة من قبل في أي سفارة؟',
                                    'question_key' => 'previous_refusal',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'لا، لم أتعرض لأي رفض تأشيرة سابقاً', 'value' => 'No Refusal', 'score' => 25],
                                            ['label' => 'نعم، رُفض طلب سابق وأريد إعادة التقديم بشكل سليم', 'value' => 'Refused Before', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك للتواصل المباشر',
                            'subtitle' => 'سيقوم مستشار التأشيرات المتخصص بالوجهة بمراجعة ملفك',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات الاتصال',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم بالكامل', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                            ['name' => 'email', 'label' => 'البريد الإلكتروني', 'type' => 'email', 'required' => false],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🎯 مؤهل للتقديم الفوري وحجز الموعد',
                            'description' => 'بياناتك الأولية ممتازة ومطابقة لمعايير السفارة المطلوبة. مستشارك جاهز لحجز أسرع موعد وتجهيز الأبلكيشن.',
                            'min_score' => 70,
                            'max_score' => 100,
                            'cta_label' => 'ابدأ التقديم الآن عبر الواتساب',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                        [
                            'title' => '📋 ملفك يحتاج استشارة لتجاوز النواقص أو الرفض السابق',
                            'description' => 'يمكن التقديم بنجاح بعد معالجة سبب الرفض السابق وتحديث الأوراق اللازمة لضمان قبول الطلب.',
                            'min_score' => 0,
                            'max_score' => 69,
                            'cta_label' => 'تحدث مع خبير معالجة الرفض السابق',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 2,
                        ],
                    ],
                ],
            ],

            // ── 3. Which Visa Is Right For You? ────────────────────────────────────
            [
                'name' => 'Which Visa Is Right For You?',
                'slug' => 'which-visa-is-right-for-you',
                'category' => 'Recommendation',
                'description' => 'مساعد تفاعلي ذكي يرشح لك الوجهة والتأشيرة الأسهل والأسرع بناءً على ظروفك وميزانيتك.',
                'thumbnail' => '/assets/images/templates/rec.jpg',
                'sort_order' => 3,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#7c3aed',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'مساعد اختيار التأشيرة الأنسب لك',
                            'subtitle' => 'محتار أين تسافر أو أي تأشيرة أسهل لك؟ أجب على 3 أسئلة وسنرشح لك أفضل خيار',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'اعرف التأشيرة التي تناسبك دون تعقيد 🎯'],
                            ],
                        ],
                        [
                            'title' => 'الهدف الأساسي من الرحلة',
                            'subtitle' => 'ما نوع السفر المفضل لديك؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو نوع الرحلة التي تخطط لها؟',
                                    'question_key' => 'trip_purpose',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🏖️ سياحة واستجمام وتسوق عائلي', 'value' => 'Tourism', 'score' => 25],
                                            ['label' => '💼 رحلة عمل / حضور مؤتمرات وتجارة', 'value' => 'Business', 'score' => 25],
                                            ['label' => '🎓 دراسة لغة / تدريب قصير أو علاج', 'value' => 'Study_Medical', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'شروط الحساب والأوراق',
                            'subtitle' => 'مستوى سهولة الإجراءات التي تفضلها',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل تفضل دولة بتأشيرة إلكترونية سريعة وبدون متطلبات بنكية صعبة؟',
                                    'question_key' => 'visa_difficulty',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، أريد تأشيرة إلكترونية سريعة ومضمونة (مثل بريطانيا ETA أو أذربيجان وجورجيا)', 'value' => 'Easy eVisa', 'score' => 30],
                                            ['label' => 'لا مانع من تقديم كشف حساب وحجز موعد سفارة للشنغن أو أمريكا', 'value' => 'Embassy Visa', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الموعد المخطط للسفر',
                            'subtitle' => 'الوقت المتاح لاستخراج التأشيرة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'متى موعد سفرك المتوقع؟',
                                    'question_key' => 'travel_time',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'عاجل (خلال أسبوع إلى 10 أيام)', 'value' => 'Urgent', 'score' => 25],
                                            ['label' => 'خلال شهر إلى 3 أشهر (وقت كافٍ ومثالي)', 'value' => '1-3 Months', 'score' => 25],
                                            ['label' => 'تخطيط للمستقبل (بعد 3 أشهر)', 'value' => 'Future', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام التوصية الكاملة',
                            'subtitle' => 'سنرسل لك قائمة الدول المتاحة وأسهل سفارة لك الآن',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🌟 ترشيحنا لك: تأشيرة إلكترونية سريعة (ETA أو آسيا)',
                            'description' => 'بناءً على إجاباتك، ننصحك بالتقديم على تأشيرة بريطانيا الإلكترونية ETA أو وجهات جورجيا وأذربيجان لسرعة الإنجاز بدون مقابلة.',
                            'min_score' => 60,
                            'max_score' => 100,
                            'cta_label' => 'تواصل معنا لإصدار التأشيرة الإلكترونية فوراً',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                        [
                            'title' => '🇪🇺 ترشيحنا لك: تأشيرة الشنغن الأوروبية (فرنسا أو إيطاليا)',
                            'description' => 'لديك الوقت الكافي والقدرة على التقديم لسفارات الشنغن السريعة، وسنقوم بحجز أقرب موعد وتجهيز حجوزاتك.',
                            'min_score' => 0,
                            'max_score' => 59,
                            'cta_label' => 'استشر خبير الشنغن لتحديد السفارة الأنسب',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 2,
                        ],
                    ],
                ],
            ],

            // ── 4. Travel Destination Finder ───────────────────────────────────────
            [
                'name' => 'Travel Destination Finder',
                'slug' => 'travel-destination-finder',
                'category' => 'Travel',
                'description' => 'مستكشف الوجهات السياحية الذكي لاقتراح أفضل برنامج سياحي وبكج متكامل حسب رغبتك وميزانيتك.',
                'thumbnail' => '/assets/images/templates/dest.jpg',
                'sort_order' => 4,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#ea580c',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'مستكشف الوجهات السياحية المثالية',
                            'subtitle' => 'اختر طبيعة رحلتك وسنصمم لك البكج السياحي الأفضل بأفضل سعر',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'أين ستقضي إجازتك القادمة؟ 🌍'],
                            ],
                        ],
                        [
                            'title' => 'طبيعة المسافرين',
                            'subtitle' => 'مع من ستسافر في هذه الرحلة؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'من هم شركاؤك في السفر؟',
                                    'question_key' => 'travel_companions',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '👨‍👩‍👧‍👦 رحلة عائلية مع أطفال', 'value' => 'Family', 'score' => 25],
                                            ['label' => '💍 شهر عسل / زوجين (كابلز)', 'value' => 'Honeymoon', 'score' => 25],
                                            ['label' => '🎒 مع أصدقاء أو بمفردي (شبابي)', 'value' => 'Solo_Friends', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الجو والطبيعة المفضلة',
                            'subtitle' => 'نوع الأجواء التي تفضلها في رحلتك',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الطبيعة التي تسعى للاستمتاع بها؟',
                                    'question_key' => 'vibe_preference',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🏔️ جبال خضراء، بحيرات وأرياف باردة (مثل النمسا وسويسرا وجورجيا)', 'value' => 'Nature_Lakes', 'score' => 30],
                                            ['label' => '🏖️ شواطئ استوائية، منتجعات وجزر بحرية (مثل تايلاند وبالي والمالديف)', 'value' => 'Tropical_Beaches', 'score' => 25],
                                            ['label' => '🏛️ مدن تاريخية، تسوق، متاحف ومطاعم عالمية (مثل لندن وإيطاليا وباريس)', 'value' => 'City_Shopping', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الميزانية التقديرية للشخص',
                            'subtitle' => 'الميزانية التقريبية للرحلة الشاملة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو سقف الميزانية التقديرية للشخص الواحد؟',
                                    'question_key' => 'budget_per_person',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'ميزانية اقتصادية وممتازة (حتى 3,500 ريال)', 'value' => 'Economy', 'score' => 15],
                                            ['label' => 'ميزانية متوسطة مميزة (من 4,000 إلى 8,000 ريال)', 'value' => 'Standard', 'score' => 25],
                                            ['label' => 'ميزانية فاخرة VIP (أكثر من 8,000 ريال)', 'value' => 'Luxury', 'score' => 35],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام خطة الرحلة والأسعار',
                            'subtitle' => 'سنرسل لك جدولاً سياحياً مقترحاً مع أسعار الطيران والفنادق',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🏔️ الوجهة المرشحة: أرياف أوروبا الساحرة (النمسا وسويسرا)',
                            'description' => 'تم تصميم بكج عائلي سياحي متكامل يشمل الطيران، الفنادق ذات الإطلالة الساحرة، والجولات اليومية.',
                            'min_score' => 70,
                            'max_score' => 100,
                            'cta_label' => 'تواصل لاستلام برنامج أوروبا السياحي بالأسعار',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                        [
                            'title' => '🏖️ الوجهة المرشحة: آسيا الاستوائية أو القوقاز (جورجيا وتايلاند)',
                            'description' => 'وجهات مثالية تناسب ميزانيتك مع خدمات راقية وسهولة دخول بدون تأشيرة معقدة.',
                            'min_score' => 0,
                            'max_score' => 69,
                            'cta_label' => 'استلم عروض بكجات تايلاند وجورجيا الحصرية',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 2,
                        ],
                    ],
                ],
            ],

            // ── 5. Visa Readiness Assessment ───────────────────────────────────────
            [
                'name' => 'Visa Readiness Assessment',
                'slug' => 'visa-readiness-assessment',
                'category' => 'Assessment',
                'description' => 'فحص دقيق لقائمة وثائق التأشيرة المطلوبة قبل التوجه لموعد السفارة لضمان عدم وجود أخطاء.',
                'thumbnail' => '/assets/images/templates/readiness.jpg',
                'sort_order' => 5,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#0284c7',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'فحص جاهزية مستندات التأشيرة',
                            'subtitle' => 'تأكد من اكتمال أوراقك ومطابقتها لشروط السفارة وتجنب رفض الملف أو ضياع الموعد',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'راجع أوراقك خطوة بخطوة مجاناً ✅'],
                            ],
                        ],
                        [
                            'title' => 'حجوزات الطيران والفندق',
                            'subtitle' => 'شرط أساسي لجميع ملفات التأشيرات',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل تملك حجز طيران وفندق مؤكد مطابق لتواريخ الرحلة؟',
                                    'question_key' => 'flight_hotel_status',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، لدي حجوزات طيران وفنادق رسمية ومؤكدة', 'value' => 'Confirmed Bookings', 'score' => 25],
                                            ['label' => 'لا، أحتاج حجز طيران وفندق مبدئي معتمد للسفارة', 'value' => 'Need Bookings', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'التأمين الطبي الدولي للسفر',
                            'subtitle' => 'تأمين يغطي 30,000 يورو لدول الشنغن والعالم',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل أصدرت وثيقة تأمين السفر الدولي المعتمدة؟',
                                    'question_key' => 'insurance_status',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، التأمين الطبي صادر ومطابق لمدة الرحلة', 'value' => 'Insurance Ready', 'score' => 25],
                                            ['label' => 'لا، أرغب في إصدار تأمين سفر فوري معتمد', 'value' => 'Need Insurance', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'خطاب العمل والترجمة الرسمية',
                            'subtitle' => 'إثبات الراتب والوظيفة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل تعريف الراتب مترجم بالإنجليزية ومختوم من جهة العمل؟',
                                    'question_key' => 'employment_letter_status',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، الخطاب بالإنجليزية ومختوم ومحدث', 'value' => 'Letter Ready', 'score' => 25],
                                            ['label' => 'الخطاب بالعربية وأحتاج ترجمة معتمدة', 'value' => 'Need Translation', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لتدقيق الملف مع المستشار',
                            'subtitle' => 'أدخل بياناتك وسيقوم فريقنا بمراجعة الأوراق قبل موعدك مجاناً',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '✅ ملفك مكتمل وجاهز للموعد!',
                            'description' => 'أوراقك الأساسية ممتازة ومطابقة للمطلوب. نوصي بطباعتها وترتيبها حسب المعايير قبل موعد السفارة.',
                            'min_score' => 70,
                            'max_score' => 100,
                            'cta_label' => 'تواصل معنا للتدقيق النهائي المجاني',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                        [
                            'title' => '⚠️ ينقصك بعض الوثائق الأساسية (حجوزات أو تأمين)',
                            'description' => 'لتفادي إلغاء الموعد أو رفض الطلب، يمكننا تزويدك بحجوزات الطيران والتأمين المعتمد فوراً.',
                            'min_score' => 0,
                            'max_score' => 69,
                            'cta_label' => 'اطلب إكمال حجوزاتك والتأمين الطبي الآن',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 2,
                        ],
                    ],
                ],
            ],

            // ── 6. Travel Budget Calculator ────────────────────────────────────────
            [
                'name' => 'Travel Budget Calculator',
                'slug' => 'travel-budget-calculator',
                'category' => 'Calculator',
                'description' => 'حاسبة تقديرية لميزانية الرحلة وتكاليف التذاكر والفنادق ورسوم التأشيرات بمختلف الوجهات.',
                'thumbnail' => '/assets/images/templates/calc.jpg',
                'sort_order' => 6,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#0891b2',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'حاسبة ميزانية وتكاليف السفر التقديرية',
                            'subtitle' => 'احسب تكلفة رحلتك التقديرية شاملة التأشيرة، التذاكر، والإقامة في 3 خطوات',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'خطط ميزانية سفرك بذكاء ووفر حتى 25% 💰'],
                            ],
                        ],
                        [
                            'title' => 'الوجهة المرغوبة',
                            'subtitle' => 'اختر المنطقة لحساب متوسط الأسعار',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'إلى أي منطقة ترغب بالسفر؟',
                                    'question_key' => 'destination_region',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'دول أوروبا (شنغن أو بريطانيا)', 'value' => 'Europe', 'score' => 30],
                                            ['label' => 'دول شرق آسيا (تايلاند، ماليزيا، إندونيسيا)', 'value' => 'East_Asia', 'score' => 20],
                                            ['label' => 'دول القوقاز (جورجيا، أذربيجان، البوسنة)', 'value' => 'Caucasus', 'score' => 15],
                                            ['label' => 'أمريكا أو كندا', 'value' => 'North_America', 'score' => 35],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'مدة الإقامة وعدد الأشخاص',
                            'subtitle' => 'عدد الأيام وعدد المسافرين',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'كم عدد المسافرين ومدة الرحلة المتوقعة؟',
                                    'question_key' => 'duration_people',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'شخص أو شخصين (من 7 إلى 10 أيام)', 'value' => '1-2 People / 10 Days', 'score' => 20],
                                            ['label' => 'عائلة 3 إلى 5 أفراد (من 10 إلى 15 يوماً)', 'value' => 'Family / 10-15 Days', 'score' => 30],
                                            ['label' => 'رحلة طويلة (أكثر من 15 يوماً)', 'value' => 'Long Trip > 15 Days', 'score' => 40],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك للحصول على عرض السعر المخصص',
                            'subtitle' => 'سنرسل لك تقرير التكلفة الكاملة وعروض خصم على رسوم التأشيرة والخدمات',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '📊 ميزانيتك المقدرة وخطة السفر جاهزة!',
                            'description' => 'فريقنا أعد لك خطة ميزانية متوازنة تضمن لك أفضل أسعار الطيران والفنادق ورسوم التأشيرة مع كوبون خصم.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'استلم تفاصيل الميزانية وكوبون الخصم عبر الواتساب',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 7. Lead Generation Quiz ────────────────────────────────────────────
            [
                'name' => 'Lead Generation Quiz',
                'slug' => 'lead-generation-quiz',
                'category' => 'Lead Generation',
                'description' => 'فانل تفاعلي سريع عالي التحويل لتوليد العملاء المهتمين بالسفر واستخراج التأشيرات.',
                'thumbnail' => '/assets/images/templates/lead.jpg',
                'sort_order' => 7,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#4f46e5',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'احصل على استشارة تأشيرة مجانية وخصم 15% 🎁',
                            'subtitle' => 'أجب على سؤالين سريعين وسيتواصل معك خبير التأشيرات فوراً مع هدية خصم خاصة',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'خطط لسفرك براحة بال مع Travel Wave ✨'],
                            ],
                        ],
                        [
                            'title' => 'أكبر تحدٍ يواجهك في التأشيرة',
                            'subtitle' => 'ما الذي تبحث عن مساعدة فيه تحديداً؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي العقبة الأساسية في تقديمك الحالي؟',
                                    'question_key' => 'main_challenge',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'صعوبة العثور على موعد قريب في السفارة', 'value' => 'Appointment Booking', 'score' => 25],
                                            ['label' => 'تجهيز الأبلكيشن والترجمة والحجوزات والتأمين', 'value' => 'Application Documents', 'score' => 25],
                                            ['label' => 'الخوف من رفض الطلب وأريد ضمان أعلى نسبة قبول', 'value' => 'Approval Guarantee', 'score' => 25],
                                            ['label' => 'أريد باقة سياحية متكاملة (تأشيرة + طيران وفنادق)', 'value' => 'Full Package', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'متى ترغب بالبدء في المعاملة؟',
                            'subtitle' => 'سرعة الخدمة المطلوبة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'متى تود البدء في إجراءات ملفك؟',
                                    'question_key' => 'start_timeline',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'فوراً خلال هذا الأسبوع 🚀', 'value' => 'Immediately', 'score' => 50],
                                            ['label' => 'خلال الأسبوعين القادمين', 'value' => 'Next 2 Weeks', 'score' => 30],
                                            ['label' => 'استفسار وتخطيط للشهر القادم', 'value' => 'Planning', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'أدخل بياناتك لاستلام كود الخصم والاستشارة',
                            'subtitle' => 'سيتواصل معك المستشار المخصص فوراً عبر الواتساب',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🎉 تم تفعيل كود الخصم (WAVE2026) واستشارتك جاهزة!',
                            'description' => 'شكراً لتواصلك! مستشارك في Travel Wave بانتظارك لمساعدتك في استخراج التأشيرة بأسرع وقت.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تحدث مع مستشارك واستفد من الخصم الآن',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 8. WhatsApp Qualification Funnel ───────────────────────────────────
            [
                'name' => 'WhatsApp Qualification Funnel',
                'slug' => 'whatsapp-qualification-funnel',
                'category' => 'Qualification',
                'description' => 'فانل محادثة وتأهيل سريع ينقل العميل المؤهل مباشرة لمحادثة الواتساب المباشرة مع خدمة العملاء.',
                'thumbnail' => '/assets/images/templates/wa-funnel.jpg',
                'sort_order' => 8,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#16a34a',
                        'font_family' => 'Tajawal, sans-serif',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'تحدث مع مستشار التأشيرات مباشرة عبر الواتساب 💬',
                            'subtitle' => 'حدد نوع طلبك وسنوجهك فوراً للقسم المختص لحجز موعدك ومتابعة ملفك',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'خدمة عملاء سريعة ومباشرة على مدار الساعة ⚡'],
                            ],
                        ],
                        [
                            'title' => 'نوع الخدمة المطلوبة',
                            'subtitle' => 'اختر الخدمة لتوجيهك للمختص المناسب',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الخدمة التي تحتاجها الآن؟',
                                    'question_key' => 'service_type',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'استخراج تأشيرة سياحية أو عمل (شنغن / بريطانيا / أمريكا)', 'value' => 'Visa Service', 'score' => 25],
                                            ['label' => 'حجز موعد سفارة عاجل ومؤكد', 'value' => 'Urgent Appointment', 'score' => 25],
                                            ['label' => 'بكج سياحي كامل (طيران + فنادق + جولات)', 'value' => 'Tourism Package', 'score' => 25],
                                            ['label' => 'تأمين سفر طبي دولي أو ترجمة معتمدة', 'value' => 'Insurance_Translation', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بلد الإقامة أو السفر',
                            'subtitle' => 'تحديد موقع التقديم',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'أين تقيم حالياً؟',
                                    'question_key' => 'current_location',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'المملكة العربية السعودية 🇸🇦', 'value' => 'KSA', 'score' => 25],
                                            ['label' => 'الإمارات أو دولة خليجية 🇦🇪 🇰🇼 🇶🇦', 'value' => 'GCC', 'score' => 25],
                                            ['label' => 'جمهورية مصر العربية 🇪🇬', 'value' => 'Egypt', 'score' => 25],
                                            ['label' => 'دولة أخرى 🌍', 'value' => 'Other', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'أدخل اسمك ورقم الواتساب لبدء المحادثة',
                            'subtitle' => 'سيتم فتح تطبيق الواتساب مباشرة مع المستشار المسؤول',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => 'مستشارك بانتظارك الآن على الواتساب ✅',
                            'description' => 'اضغط على الزر أدناه لبدء المحادثة الفورية وتلقي كافة التفاصيل والمواعيد.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'ابدأ محادثة الواتساب الآن 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            FunnelTemplate::updateOrCreate(
                ['slug' => $tpl['slug']],
                [
                    'name' => $tpl['name'],
                    'category' => $tpl['category'],
                    'description' => $tpl['description'],
                    'thumbnail_url' => $tpl['thumbnail'] ?? null,
                    'schema_data' => $tpl['schema_data'],
                    'is_active' => true,
                    'sort_order' => $tpl['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
