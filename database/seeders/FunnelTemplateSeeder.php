<?php

namespace Database\Seeders;

use App\Models\FunnelTemplate;
use Illuminate\Database\Seeder;

class FunnelTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── 1. Schengen Visa Eligibility ──────────────────────────────────────
            [
                'name' => 'Schengen Visa Eligibility (فحص أهلية الشنغن)',
                'slug' => 'schengen-visa-eligibility',
                'category' => 'Travel',
                'description' => 'فحص أهلية متقدم للحصول على تأشيرة الشنغن الأوروبية مع احتساب دقيق لنسبة القبول وربط واتساب فوري.',
                'thumbnail_url' => '/assets/images/templates/schengen.jpg',
                'sort_order' => 1,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#1e40af', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'اختبار الجاهزية للتأشيرة الشنغن',
                            'subtitle' => 'اكتشف نسبة قبول طلبك قبل التقديم خلال دقيقتين بدقة عالية',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'هل أنت جاهز للحصول على تأشيرة الشنغن؟', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'أجب على الأسئلة التالية لنحدد لك السفارة الأنسب ونسبة قبول ملفك وتجهيز الموعد المناسب.'],
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

            // ── 2. Real Estate Property Finder ────────────────────────────────────
            [
                'name' => 'Real Estate Property Finder (مستكشف العقارات والاستثمار)',
                'slug' => 'real-estate-property-finder',
                'category' => 'Real Estate',
                'description' => 'فانل تأهيل المشترين والمستثمرين العقاريين وتحديد نوع العقار والميزانية وخيارات التمويل.',
                'thumbnail_url' => '/assets/images/templates/real-estate.jpg',
                'sort_order' => 2,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#0f766e', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'ابحث عن عقارك المثالي أو استثمارك القادم',
                            'subtitle' => 'حدد مواصفات وميزانية العقار المناسب وسنرشح لك أفضل المشاريع المتاحة فوراً',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'عقارك القادم يبدأ من هنا 🏡'],
                                ['element_type' => 'text', 'label' => 'أجب على 3 أسئلة سريعة لاستلام كتالوج العقارات والأسعار وخطة السداد.'],
                            ],
                        ],
                        [
                            'title' => 'الهدف ونوع العقار',
                            'subtitle' => 'الغرض من الشراء ونوع الوحدة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو نوع العقار والغرض من الشراء؟',
                                    'question_key' => 'property_type',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🏠 فيلا سكنية فاخرة للسكن العائلي', 'value' => 'Residential Villa', 'score' => 30],
                                            ['label' => '🏢 شقة سكنية للتمليك أو الاستثمار', 'value' => 'Apartment', 'score' => 20],
                                            ['label' => '📈 عقار تجاري / مكاتب بعائد إيجاري استثماري', 'value' => 'Commercial Investment', 'score' => 30],
                                            ['label' => '🏖️ شاليه / عقار ساحلي لقضاء العطلات', 'value' => 'Holiday Chalet', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الميزانية وطريقة الدفع',
                            'subtitle' => 'القدرة الشرائية وخيارات التقسيط',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الميزانية التقديرية وطريقة السداد؟',
                                    'question_key' => 'budget_payment',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'دفع كاش بالكامل (خصم فوري خاص)', 'value' => 'Full Cash', 'score' => 30],
                                            ['label' => 'أقساط ميسرة مع المطور (دفعة أولى حتى 20%)', 'value' => 'Developer Installments', 'score' => 25],
                                            ['label' => 'تمويل عقاري بنكي عن طريق جهة العمل', 'value' => 'Bank Mortgage', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'المدينة والموقع المفضل',
                            'subtitle' => 'المنطقة الجغرافية المستهدفة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'أين تفضل أن يكون موقع العقار؟',
                                    'question_key' => 'target_city',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '📍 الرياض (شمال أو شرق الرياض)', 'value' => 'Riyadh', 'score' => 25],
                                            ['label' => '📍 جدة أو المنطقة الشرقية', 'value' => 'Jeddah / Eastern', 'score' => 20],
                                            ['label' => '📍 دبي / الإمارات (عقارات التملك الحر)', 'value' => 'Dubai UAE', 'score' => 25],
                                            ['label' => '📍 القاهرة / الساحل الشمالي / التجمع', 'value' => 'Cairo / North Coast', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام كتالوج المشاريع والأسعار',
                            'subtitle' => 'سيتواصل معك المستشار العقاري بملفات PDF والأسعار الحصرية',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات الاتصال',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكريم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '✨ تم تجهيز قائمة العقارات المطابقة لميزانيتك!',
                            'description' => 'لدينا مشاريع ممتازة بمواقع استراتيجية وأسعار حصرية مطابقة لاختياراتك. مستشارك العقاري جاهز لإرسال التفاصيل.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تحدث مع المستشار العقاري واستلم الكتالوج 📱',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 3. Study Abroad & University Matcher ──────────────────────────────
            [
                'name' => 'Study Abroad & University Matcher (القبول الجامعي والدراسة بالخارج)',
                'slug' => 'study-abroad-university-matcher',
                'category' => 'Education',
                'description' => 'فانل تأهيل الطلاب الراغبين بالدراسة في بريطانيا، كندا، أمريكا، وأوروبا وتحديد التخصص والمنحة.',
                'thumbnail_url' => '/assets/images/templates/education.jpg',
                'sort_order' => 3,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#8b5cf6', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'مستشارك للقبول الجامعي والدراسة بالخارج',
                            'subtitle' => 'احصل على قبول جامعي معتمد في أفضل جامعات بريطانيا، كندا، أمريكا، وماليزيا',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'ابدأ مستقبلك الأكاديمي العالمي 🎓'],
                            ],
                        ],
                        [
                            'title' => 'المرحلة والتخصص الدراسي',
                            'subtitle' => 'المستوى الأكاديمي المطلوب',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي المرحلة الدراسية والتخصص المرغوب؟',
                                    'question_key' => 'study_level',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'بكالوريوس (هندسة / طب / إدارة وأعمال / تقنية)', 'value' => 'Bachelors', 'score' => 25],
                                            ['label' => 'ماجستير أو دكتوراه (دراسات عليا وبحوث)', 'value' => 'Postgraduate', 'score' => 30],
                                            ['label' => 'دراسة لغة إنجليزية مكثفة في معهد معتمد', 'value' => 'English Course', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'البلد المستهدف للدراسة',
                            'subtitle' => 'الوجهة الدراسية المفضلة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'أين تفضل أن تدرس؟',
                                    'question_key' => 'study_country',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🇬🇧 بريطانيا (جامعات مرموقة وسرعة التخرج)', 'value' => 'UK', 'score' => 25],
                                            ['label' => '🇨🇦 كندا (فرص عمل وإقامة بعد التخرج)', 'value' => 'Canada', 'score' => 25],
                                            ['label' => '🇺🇸 أمريكا أو 🇦🇺 أستراليا', 'value' => 'USA_Australia', 'score' => 25],
                                            ['label' => '🇲🇾 ماليزيا أو 🇹🇷 تركيا (تكلفة دراسية مناسبة جداً)', 'value' => 'Malaysia_Turkey', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'مستوى اللغة الإنجليزية',
                            'subtitle' => 'هل لديك اختبار آيلتس أو توفل؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو مستواك في اللغة الإنجليزية حالياً؟',
                                    'question_key' => 'english_level',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'معي شهادة IELTS أو TOEFL جاهزة', 'value' => 'IELTS Ready', 'score' => 30],
                                            ['label' => 'مستواي جيد ولكن لم أختبر بعد', 'value' => 'Good No Test', 'score' => 20],
                                            ['label' => 'مبتدئ وأريد دراسة سنة تحضيرية / كورس لغة أولاً', 'value' => 'Foundation Needed', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام استشارة القبول الجامعي مجاناً',
                            'subtitle' => 'سيقوم المستشار الأكاديمي بتحديد الجامعات المناسبة وشروط القبول',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات الطالب',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'اسم الطالب', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🎓 تهانينا! مؤهل للتقديم والحصول على قبول جامعي',
                            'description' => 'لدينا اتفاقيات وشراكات مباشرة مع أكثر من 150 جامعة دولية لضمان قبولك واستخراج تأشيرة الطالب بسهولة.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تواصل مع المستشار الأكاديمي الآن',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 4. Business Setup & Company Formation ─────────────────────────────
            [
                'name' => 'Business Setup & Company Formation (تأسيس الشركات وحلول الأعمال)',
                'slug' => 'business-setup-company-formation',
                'category' => 'B2B Services',
                'description' => 'حاسبة وفانل تأسيس الشركات وإصدار الرخص التجارية والإقامة الاستثمارية في السعودية والإمارات.',
                'thumbnail_url' => '/assets/images/templates/business.jpg',
                'sort_order' => 4,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#0369a1', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'حاسبة تأسيس الشركات وإطلاق الأعمال',
                            'subtitle' => 'احسب تكلفة الرخصة التجارية، فتح الحساب البنكي للشركات، والإقامة الاستثمارية',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'أسس شركتك القانونية بأسرع وقت 💼'],
                            ],
                        ],
                        [
                            'title' => 'مقر تأسيس الشركة',
                            'subtitle' => 'أين تود تأسيس نشاطك التجاري؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'في أي دولة ترغب بتسجيل شركتك؟',
                                    'question_key' => 'company_location',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🇸🇦 المملكة العربية السعودية (سجل تجاري / ترخيص استثمار أجنبي MISA)', 'value' => 'KSA', 'score' => 30],
                                            ['label' => '🇦🇪 الإمارات (دبي / منطقة حرة Freezone وتملك 100%)', 'value' => 'UAE', 'score' => 30],
                                            ['label' => '🇬🇧 بريطانيا أو 🇺🇸 أمريكا (شركة رقمية أونلاين وبوابة دفع دولية)', 'value' => 'UK_USA_LLC', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'نوع النشاط والخدمات المرافقة',
                            'subtitle' => 'المتطلبات الإضافية',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الخدمات التي تحتاجها مع التأسيس؟',
                                    'question_key' => 'additional_services',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'رخصة تجارية + فتح حساب بنكي تجاري + إقامات للمؤسسين', 'value' => 'Full Setup Package', 'score' => 30],
                                            ['label' => 'رخصة تجارية وسجل تجاري فقط', 'value' => 'License Only', 'score' => 20],
                                            ['label' => 'استشارة قانونية وضريبية قبل التأسيس', 'value' => 'Consultation', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام عرض الأسعار الشامل',
                            'subtitle' => 'سيتواصل معك مستشار تأسيس الأعمال بخطة التكلفة والخطوات القانونية',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'اسم رائد الأعمال / المستثمر', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                            ['name' => 'email', 'label' => 'البريد الإلكتروني للعمل', 'type' => 'email', 'required' => false],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🏢 خطة تأسيس شركتك جاهزة للإطلاق!',
                            'description' => 'فريقنا القانوني يتولى كافة الإجراءات من إصدار السجل التجاري وحتى فتح الحساب البنكي وتسليمك الرخصة.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تحدث مع مستشار الأعمال الآن عبر الواتساب',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 5. Car Financing & Buying Calculator ──────────────────────────────
            [
                'name' => 'Car Financing & Buying Calculator (حاسبة تمويل وتقسيط السيارات)',
                'slug' => 'car-financing-calculator',
                'category' => 'Automotive',
                'description' => 'فانل احتساب قسط السيارة الشهري والدفعة الأولى واختيار السيارة وطلب تجربة القيادة.',
                'thumbnail_url' => '/assets/images/templates/auto.jpg',
                'sort_order' => 5,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#b91c1c', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'حاسبة أقساط وعروض السيارات التمويلية',
                            'subtitle' => 'احسب قسطك الشهري التقديري بدون دفعة أولى ومع أفضل جهات التمويل',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'سيارتك الجديدة بأفضل نسبة تمويل 🚗'],
                            ],
                        ],
                        [
                            'title' => 'فئة السيارة المفضلة',
                            'subtitle' => 'نوع وحجم السيارة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي فئة السيارة التي تبحث عنها؟',
                                    'question_key' => 'car_category',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🚙 سيارة عائلية SUV / جيب دفع رباعي', 'value' => 'SUV', 'score' => 30],
                                            ['label' => '🚗 سيارة سيدان مريحة واقتصادية', 'value' => 'Sedan', 'score' => 20],
                                            ['label' => '⚡ سيارة هايبرد / كهربائية ذكية موفرة للوقود', 'value' => 'Hybrid_Electric', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'القسط الشهري المناسب',
                            'subtitle' => 'مستوى الالتزام المالي المريح لك',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو متوسط القسط الشهري المناسب لراتبك؟',
                                    'question_key' => 'monthly_installment',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'أقل من 1,500 ريال شهرياً', 'value' => 'Under 1500', 'score' => 15],
                                            ['label' => 'من 1,500 إلى 2,500 ريال شهرياً', 'value' => '1500-2500', 'score' => 25],
                                            ['label' => 'أكثر من 2,500 ريال شهرياً (فئات فاخرة VIP)', 'value' => 'Over 2500', 'score' => 35],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'أدخل بياناتك لحساب الموافقة الفورية',
                            'subtitle' => 'سنرسل لك جدول الأقساط وقائمة السيارات المتاحة فوراً',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات العميل',
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
                            'title' => '🚘 عرض تمويل سيارتك جاهز للمعاينة والتجربة!',
                            'description' => 'تم احتساب أفضل نسبة تمويل بأقل فائدة سنوية وتجهيز خيارات السيارات المتوفرة للتسليم الفوري.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'استلم جدول الأقساط واحجز تجربة القيادة',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 6. Medical Tourism & Treatment Estimator ──────────────────────────
            [
                'name' => 'Medical Tourism & Treatment Estimator (السياحة العلاجية والتجميلية)',
                'slug' => 'medical-tourism-estimator',
                'category' => 'Healthcare',
                'description' => 'فانل تقدير تكلفة السياحة العلاجية: زراعة الشعر، علاج الأسنان، والعمليات في تركيا وأوروبا.',
                'thumbnail_url' => '/assets/images/templates/health.jpg',
                'sort_order' => 6,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#059669', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'حاسبة السياحة العلاجية والاستشارة الطبية',
                            'subtitle' => 'احصل على خطة علاجية وتكلفة تقديرية شاملة الفندق والمواصلات مع أفضل المستشفيات المعتمدة',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'رعايتك الصحية بأعلى معايير الجودة 🩺'],
                            ],
                        ],
                        [
                            'title' => 'نوع الإجراء الطبي أو التجميلي',
                            'subtitle' => 'اختر التخصص المطلوب',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو الإجراء الطبي الذي تبحث عنه؟',
                                    'question_key' => 'treatment_type',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '💆‍♂️ زراعة الشعر بأحدث تقنيات DHI و Sapphire', 'value' => 'Hair Transplant', 'score' => 30],
                                            ['label' => '🦷 تجميل وزراعة الأسنان وابتسامة هوليوود 3D', 'value' => 'Dental Care', 'score' => 30],
                                            ['label' => '👁️ تصحيح النظر الليزك وجراحة العيون', 'value' => 'Eye Lasik', 'score' => 25],
                                            ['label' => '🏥 فحوصات شاملة وعلاج متخصص في أوروبا أو تركيا', 'value' => 'Checkup / Specialized', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الموعد المفضل للسفر',
                            'subtitle' => 'التوقيت المفضل لبدء الخطة العلاجية',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'متى تود إجراء العملية أو الفحص؟',
                                    'question_key' => 'preferred_date',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'خلال هذا الشهر (حجز عاجل)', 'value' => 'This Month', 'score' => 30],
                                            ['label' => 'خلال شهرين إلى 3 أشهر', 'value' => '1-3 Months', 'score' => 20],
                                            ['label' => 'أريد استشارة وتقييم أولي للصور أولاً', 'value' => 'Consultation First', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام خطة العلاج والتكلفة',
                            'subtitle' => 'سيقوم الطبيب والمستشار الطبي بمراجعة حالتك وتقديم الخطة المجانية',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات المريض',
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
                            'title' => '🏥 تقريرك الطبي وعرض الباقة العلاجية جاهز!',
                            'description' => 'الباقة تشمل: الإجراء الطبي، الإقامة الفندقية 5 نجوم، الاستقبال والتنقلات بسيارة خاصة، والمتابعة الطبية.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تحدث مع المستشار الطبي واستلم خطة العلاج 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 7. Personal Loan & Finance Eligibility ────────────────────────────
            [
                'name' => 'Personal Loan & Finance Eligibility (حاسبة التمويل الشخصي)',
                'slug' => 'personal-loan-eligibility',
                'category' => 'Financial Services',
                'description' => 'فانل فحص أهلية القروض والتمويل الشخصي، حساب الالتزامات والحد الأقصى للتمويل المتاح.',
                'thumbnail_url' => '/assets/images/templates/finance.jpg',
                'sort_order' => 7,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#047857', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'حاسبة التمويل والملاءة المالية',
                            'subtitle' => 'احسب أقصى مبلغ تمويل تستحقه بأقل هامش ربح وأطول فترة سداد',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'تمويلك المالي بمرونة وسرعة 💳'],
                            ],
                        ],
                        [
                            'title' => 'الراتب الشهري الصافي',
                            'subtitle' => 'الراتب المحول للبنك',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو متوسط راتبك الشهري؟',
                                    'question_key' => 'salary_range',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'أكثر من 15,000 ريال شهرياً', 'value' => 'Over 15k', 'score' => 40],
                                            ['label' => 'من 8,000 إلى 15,000 ريال شهرياً', 'value' => '8k-15k', 'score' => 30],
                                            ['label' => 'من 4,000 إلى 8,000 ريال شهرياً', 'value' => '4k-8k', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'جهة العمل والالتزامات القائمة',
                            'subtitle' => 'قطاع العمل',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو قطاع عملك الحالي؟',
                                    'question_key' => 'employer_sector',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'قطاع حكومي / عسكري / شبه حكومي', 'value' => 'Government', 'score' => 30],
                                            ['label' => 'شركات القطاع الخاص الكبرى المعتمدة', 'value' => 'Corporate Private', 'score' => 25],
                                            ['label' => 'مؤسسات وشركات خاصة متوسطة / أعمال حرة', 'value' => 'Private / Self', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لحساب الحد الأقصى للتمويل',
                            'subtitle' => 'سنرسل لك تقرير الحسبة التقديرية بدقة',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات العميل',
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
                            'title' => '💰 مؤهل للحصول على تمويل فوري بأفضل نسبة!',
                            'description' => 'بناءً على بياناتك الأولية، يمكنك الحصول على تمويل ميسر بهامش ربح تنافسي وتأجيل أول قسط.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تواصل مع المستشار المالي واستلم الحسبة الآن',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 8. Golden Visa & Immigration by Investment ────────────────────────
            [
                'name' => 'Golden Visa & Immigration Checker (الإقامة الذهبية والهجرة الاستثمارية)',
                'slug' => 'golden-visa-immigration-checker',
                'category' => 'Legal & Immigration',
                'description' => 'فحص الأهلية للحصول على الإقامة الذهبية، الجواز الثاني، وبرامج الهجرة في أوروبا والكاريبي.',
                'thumbnail_url' => '/assets/images/templates/golden-visa.jpg',
                'sort_order' => 8,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#d97706', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'فحص أهلية الإقامة الذهبية والجواز الثاني',
                            'subtitle' => 'احصل على حرية السفر بدون تأشيرة لأكثر من 150 دولة مع عائلتك',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'جوازك الثاني وإقامتك الأوروبية الدائمة 🌐'],
                            ],
                        ],
                        [
                            'title' => 'البرنامج الاستثماري المستهدف',
                            'subtitle' => 'نوع البرنامج المفضل',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو البرنامج الذي تهتم به؟',
                                    'question_key' => 'immigration_program',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🇪🇸 🇬🇷 الإقامة الذهبية الأوروبية (إسبانيا، اليونان، البرتغال)', 'value' => 'European Golden Visa', 'score' => 35],
                                            ['label' => '🌴 جوازات دول الكاريبي (سانت كيتس، دومينيكا - سفر بدون تأشيرة)', 'value' => 'Caribbean Passport', 'score' => 35],
                                            ['label' => '🇦🇪 الإقامة الذهبية في دولة الإمارات (10 سنوات)', 'value' => 'UAE Golden Visa', 'score' => 30],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الملاءة الاستثمارية المخصصة',
                            'subtitle' => 'حجم الاستثمار التقديري',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو نطاق الاستثمار المتاح لديك؟',
                                    'question_key' => 'investment_capacity',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'أكثر من 250,000 دولار / يورو (مؤهل لكافة البرامج العقارية)', 'value' => 'Over 250k USD', 'score' => 40],
                                            ['label' => 'من 100,000 إلى 200,000 دولار (مؤهل لبرامج التبرع وجوازات الكاريبي)', 'value' => '100k-200k USD', 'score' => 30],
                                            ['label' => 'أقل من 100,000 دولار (أريد استشارة خيارات الإقامة المهنية)', 'value' => 'Under 100k USD', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لحجز جلسة استشارة سرية مع المحامي الدولي',
                            'subtitle' => 'سنقدم لك دليلاً كاملاً بالدول المعفاة والشروط القانونية',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات المستثمر',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكامل', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب المباشر', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🌐 مؤهل لبرامج الإقامة الذهبية والجواز الثاني!',
                            'description' => 'بياناتك تؤهلك للحصول على الإقامة أو الجنسية الثانية لك ولعائلتك بدون متطلبات إقامة فعلية.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'احجز استشارة الهجرة والاستثمار الآن',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 9. Fitness & Diet Custom Plan Quiz ────────────────────────────────
            [
                'name' => 'Fitness & Diet Custom Plan Quiz (اللياقة والبرنامج الغذائي المخصص)',
                'slug' => 'fitness-diet-custom-plan',
                'category' => 'Fitness & Health',
                'description' => 'فانل تحديد النظام الغذائي والتدريبي الأنسب حسب الهدف والوزن مع تقديم كود خصم للاشتراك.',
                'thumbnail_url' => '/assets/images/templates/fitness.jpg',
                'sort_order' => 9,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#15803d', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'صمم برنامجك الرياضي والغذائي المخصص',
                            'subtitle' => 'اكتشف خطتك المثالية لإنقاص الوزن أو بناء العضلات خلال 60 ثانية فقط',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'جسمك المثالي وصحتك تبدأ اليوم 💪'],
                            ],
                        ],
                        [
                            'title' => 'الهدف البدني الأساسي',
                            'subtitle' => 'ما الذي تسعى لتحقيقه؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو هدفك الأساسي خلال الـ 90 يوماً القادمة؟',
                                    'question_key' => 'fitness_goal',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🔥 خسارة الدهون ونحت القوام وحرق السعرات', 'value' => 'Fat Loss', 'score' => 30],
                                            ['label' => '🏋️ زيادة الكتلة العضلية والقوة البدنية', 'value' => 'Muscle Gain', 'score' => 30],
                                            ['label' => '🧘 زيادة اللياقة والنشاط وتحسين نمط الحياة والصحة', 'value' => 'General Fitness', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'المكان المفضل للتمرين',
                            'subtitle' => 'طريقة ممارسة الرياضة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'أين تفضل ممارسة تمارينك؟',
                                    'question_key' => 'workout_location',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'في الجيم / النادي الرياضي مع الأجهزة', 'value' => 'Gym', 'score' => 30],
                                            ['label' => 'في المنزل بدون أدوات أو بأدوات بسيطة', 'value' => 'Home', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام خطتك الغذائية وكوبون الخصم',
                            'subtitle' => 'سنرسل لك الخطة المحسوبة وجدول التمارين فوراً',
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
                            'title' => '🥗 خطتك التدريبية والغذائية جاهزة مع خصم 20%!',
                            'description' => 'تم تجهيز جدول السعرات اليومية وتوزيع المغذيات وتمارينك الأسبوعية لتحقيق هدفك بسرعة وأمان.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'استلم خطتك واشترك مع المدرب عبر الواتساب',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 10. Product & Gift Recommendation Quiz ────────────────────────────
            [
                'name' => 'Product & Gift Recommendation Quiz (مساعد اختيار الهدايا والمنتجات)',
                'slug' => 'product-gift-recommendation-quiz',
                'category' => 'E-commerce',
                'description' => 'فانل ترشيح المنتجات والهدايا الذكي للمتاجر الإلكترونية لزيادة المبيعات ومتوسط قيمة الطلب.',
                'thumbnail_url' => '/assets/images/templates/ecommerce.jpg',
                'sort_order' => 10,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#db2777', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'مساعد اختيار الهدية والمنتج المثالي 🎁',
                            'subtitle' => 'محتار في اختيار الهدية المناسبة؟ أجب على سؤالين وسنرشح لك الخيار الأروع',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'ابهر من تحب بأفضل هدية 💖'],
                            ],
                        ],
                        [
                            'title' => 'المناسبة والشخص المهدي إليه',
                            'subtitle' => 'لمن تشتري الهدية؟',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي المناسبة ولمن الهدية؟',
                                    'question_key' => 'gift_recipient',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '💍 ذكرى زواج / شريك الحياة', 'value' => 'Partner Anniversary', 'score' => 30],
                                            ['label' => '🎂 عيد ميلاد صديق أو قريب', 'value' => 'Birthday', 'score' => 20],
                                            ['label' => '🎓 مناسبة تخرج أو نجاح وترقية', 'value' => 'Graduation', 'score' => 25],
                                            ['label' => '💼 هدية راقية لزميل عمل أو مدير', 'value' => 'Corporate Gift', 'score' => 30],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الميزانية المخصصة للهدية',
                            'subtitle' => 'نطاق السعر المطلوب',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الميزانية التي ترغب بها؟',
                                    'question_key' => 'gift_budget',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'هدية مميزة واقتصادية (150 - 300 ريال)', 'value' => '150-300 SAR', 'score' => 15],
                                            ['label' => 'هدية فاخرة ومميزة (300 - 700 ريال)', 'value' => '300-700 SAR', 'score' => 25],
                                            ['label' => 'هدية VIP استثنائية (أكثر من 700 ريال)', 'value' => 'Over 700 SAR', 'score' => 35],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لاستلام كود الخصم وترشيح الهدية',
                            'subtitle' => 'مع إمكانية التغليف الفاخر وكتابة كارت الإهداء والتوصيل السريع',
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
                            'title' => '🎁 هديتك المثالية جاهزة مع تغليف مجاني وكود خصم!',
                            'description' => 'تم اختيار أفضل تشكيلة هدايا تناسب ذوقك مع كارت إهداء مخصص وتوصيل بنفس اليوم.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'اطلب الهدية واستلم كود الخصم عبر الواتساب',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 11. US & UK Visa Readiness Assessment ─────────────────────────────
            [
                'name' => 'US & UK Visa Readiness (تأشيرات أمريكا وبريطانيا وكندا)',
                'slug' => 'us-uk-visa-readiness',
                'category' => 'Travel',
                'description' => 'فحص الجاهزية للمقابلة الشخصية وحجز المواعيد وتعبئة استمارة DS-160 لتأشيرات أمريكا وبريطانيا.',
                'thumbnail_url' => '/assets/images/templates/us-uk.jpg',
                'sort_order' => 11,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#1e3a8a', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'فحص الجاهزية لتأشيرة أمريكا وبريطانيا',
                            'subtitle' => 'احصل على أقرب موعد مقابلة وتجهيز استمارة DS-160 بدقة تامة لتفادي الرفض',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'سافر إلى أمريكا أو بريطانيا بكل ثقة 🗽🇬🇧'],
                            ],
                        ],
                        [
                            'title' => 'الوجهة والغرض من الزيارة',
                            'subtitle' => 'حدد نوع التأشيرة المطلوبة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي التأشيرة التي ترغب بالتقديم عليها؟',
                                    'question_key' => 'visa_country_type',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🇺🇸 تأشيرة أمريكا B1/B2 السياحية والتجارية (صالحة لـ 5 أو 10 سنوات)', 'value' => 'US B1/B2', 'score' => 30],
                                            ['label' => '🇬🇧 تصريح بريطانيا الإلكتروني السريع ETA (خلال 24-48 ساعة)', 'value' => 'UK ETA', 'score' => 30],
                                            ['label' => '🇨🇦 تأشيرة كندا السياحية البيومترية', 'value' => 'Canada Tourist', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الوضع المالي وتاريخ السفر',
                            'subtitle' => 'شروط قبول الملف',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل لديك دخل شهري ثابت وسفر سابق خارج دولتك؟',
                                    'question_key' => 'us_financial_ties',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، موظف/صاحب عمل وسافرت لعدة دول سابقاً', 'value' => 'Strong Ties', 'score' => 35],
                                            ['label' => 'موظف ولكن لم أسافر خارجياً بعد (أول مرة)', 'value' => 'First Travel', 'score' => 20],
                                            ['label' => 'طالب / مكفول من أحد أفراد العائلة', 'value' => 'Sponsored', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'سجل بياناتك لحجز أسرع موعد وتعبئة الأبلكيشن',
                            'subtitle' => 'سيتولى خبير تأشيرات أمريكا تجهيز ملفك بالكامل',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم باللغة الإنجليزية كما في الجواز', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => '🗽 ملفك مؤهل للتقديم وحجز موعد السفارة فوراً!',
                            'description' => 'فريقنا المتخصص يتولى تعبئة الأبلكيشن وسداد الرسوم وحجز أقرب موعد متاح وتدريبك على المقابلة.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تواصل معنا الآن لبدء التقديم وحجز الموعد',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 12. Instant WhatsApp Lead Routing Funnel ───────────────────────────
            [
                'name' => 'Instant WhatsApp Lead Routing (فانل التوجيه الفوري للواتساب)',
                'slug' => 'instant-whatsapp-lead-routing',
                'category' => 'Qualification',
                'description' => 'فانل محادثة ذكي يوجه العميل مباشرة للمستشار المسؤول عبر الواتساب في ثوانٍ معدودة.',
                'thumbnail_url' => '/assets/images/templates/wa.jpg',
                'sort_order' => 12,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#16a34a', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg'],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'خدمة العملاء المباشرة عبر الواتساب ⚡',
                            'subtitle' => 'اختر خدمتك وسنوصلك مباشرة بالمسؤول المختص لتلبية طلبك فوراً',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'مستشارك بانتظارك للإجابة على جميع استفساراتك 💬'],
                            ],
                        ],
                        [
                            'title' => 'ما هي الخدمة التي تحتاجها اليوم؟',
                            'subtitle' => 'تحديد القسم المختص',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'اختر الخدمة المطلوبة:',
                                    'question_key' => 'required_service',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🛂 استخراج تأشيرة سياحية / عمل (شنغن، بريطانيا، أمريكا، آسيا)', 'value' => 'Visa Department', 'score' => 25],
                                            ['label' => '✈️ حجز طيران وفنادق وبكجات سياحية متكاملة', 'value' => 'Tourism Department', 'score' => 25],
                                            ['label' => '⚡ حجز موعد سفارة مستعجل وتأمين سفر طبي', 'value' => 'Appointments Department', 'score' => 25],
                                            ['label' => '💼 خدمات تأسيس شركات واستشارات أخرى', 'value' => 'Business Department', 'score' => 25],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'أدخل اسمك ورقم هاتفك لبدء المحادثة',
                            'subtitle' => 'سيتم فتح محادثة الواتساب فوراً مع الرسالة المجهزة',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'بيانات الاتصال',
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
                            'title' => 'تم تعيين مستشارك الخاص بنجاح! ✅',
                            'description' => 'اضغط على الزر أدناه لبدء المحادثة الفورية وتلقي كافة التفاصيل والمساعدة.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'ابدأ المحادثة على الواتساب الآن 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 13. Cruises & Luxury Resorts Booking ────────────────────────────────
            [
                'name' => 'Cruises & Luxury Resorts (رحلات الكروز والفنادق الفاخرة)',
                'slug' => 'cruises-luxury-resorts-booking',
                'category' => 'Travel',
                'description' => 'قالب فخم لحجز الكروز والمصايف والمنتجعات مع اختيار الفئات والخدمات البحرية المميزة.',
                'thumbnail_url' => '/assets/images/templates/cruises.jpg',
                'sort_order' => 13,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#0284c7', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-xl', 'thank_you_page' => ['enabled' => true, 'title' => 'شكرًا لك! تم استلام طلب حجز الكروز والمنتجع 🛥️', 'subtitle' => 'يتواصل معك مستشار السفر البحري عبر الواتساب فوراً لمشاركتك كتالوج الأسعار والعروض الحصرية.', 'button_text' => 'متابعة المحادثة على الواتساب 💬', 'button_action' => 'whatsapp', 'button_whatsapp' => '966500000000']],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'رحلات الكروز والفنادق الفاخرة autour العالم 🛥️',
                            'subtitle' => 'اكتشف أفضل الرحلات البحرية والمنتجعات العالمية مع عروض خاصة',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'حجز رحلات الكروز والمنتجعات الملكية 🛥️✨', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'أجب على الأسئلة التالية لنحدد لك أفضل مسارات البحر والكبائن الفاخرة والعروض المتاحة.'],
                            ],
                        ],
                        [
                            'title' => 'الوجهة والمسار البحري المفضّل',
                            'subtitle' => 'اختر المنطقة البحرية أو السياحية التي ترغب بزيارتها',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الوجهة المفضل لرحلتك القادمة؟',
                                    'question_key' => 'cruise_destination',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🌊 جزر الكاريبي والمالديف', 'value' => 'Caribbean & Maldives', 'score' => 25],
                                            ['label' => '🇪🇺 البحر الأبيض المتوسط وشواطئ أوروبا', 'value' => 'Mediterranean Europe', 'score' => 25],
                                            ['label' => '🇸🇦 البحر الأحمر والخليج العربي', 'value' => 'Red Sea & GCC', 'score' => 20],
                                            ['label' => '🌏 شرق آسيا واليابان', 'value' => 'East Asia', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'فئة الكبينة والإقامة',
                            'subtitle' => 'تحديد مستوى الراحة والخدمة المطلوبة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي فئة الكبينة أو الجناح المفضّل لديك؟',
                                    'question_key' => 'cabin_category',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '👑 جناح ملوكي ببلكونة خاصة (Royal Suite with Balcony)', 'value' => 'Royal Suite', 'score' => 30],
                                            ['label' => '🌅 كبينة إطلالة مباشرة على البحر (Ocean View Balcony)', 'value' => 'Ocean View', 'score' => 25],
                                            ['label' => '👨‍👩‍👧‍👦 جناح عائلي متصل (Connecting Family Suite)', 'value' => 'Family Suite', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات التواصل للحجز المباشر 📱',
                            'subtitle' => 'أدخل بياناتك لتصلك أسعار وتوافر الكبائن المتاحة فوراً',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'form_fields',
                                    'label' => 'معلومات المسافر الرئيسي',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكامل', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => 'تم استلام طلب حجز الكروز والمنتجع بنجاح! 🛥️',
                            'description' => 'يقوم مستشار السفر البحري بالتواصل معك عبر الواتساب لتزويدك بكتالوج الأسعار والخصومات.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تواصل معنا على الواتساب 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 14. Private Jet Charter & VIP Flight Booking ──────────────────────
            [
                'name' => 'Private Jet Charter (حجز الطيران الخاص والرحلات الملكية)',
                'slug' => 'private-jet-vip-charter',
                'category' => 'Travel',
                'description' => 'قالب ملكي فاخر باللون الذهبي والأسود لطلب واستئجار الطائرات الخاصة والرحلات VIP.',
                'thumbnail_url' => '/assets/images/templates/privatejet.jpg',
                'sort_order' => 14,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#d97706', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-md', 'thank_you_page' => ['enabled' => true, 'title' => 'تم تسجيل طلب الطيران الخاص بنجاح! 👑', 'subtitle' => 'يتواصل معك مدير قسم الطيران الخاص مباشرة لتجهيز مسار الطائرة وتفاصيل الإقلاع.', 'button_text' => 'التواصل المباشر مع مدير الرحلة 💬', 'button_action' => 'whatsapp', 'button_whatsapp' => '966500000000']],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'خدمة استئجار الطائرات الخاصة VIP ✈️👑',
                            'subtitle' => 'رحلات جوية مخصصة بأعلى معايير الخصوصية والأمان وحرية التوقيت',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'حجز الطيران الخاص والرحلات الملكية 👑✈️', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'حدد خط سير الرحلة وعدد المسافرين لنقدم لك أفضل أسطول طائرات متاح بالأسعار والخدمات.'],
                            ],
                        ],
                        [
                            'title' => 'مسار وقار الرحلة الجوية',
                            'subtitle' => 'اختر خط السير المطلوب للطائرة الخاصة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو نوع ومسار الرحلة الجوية؟',
                                    'question_key' => 'flight_route_type',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '✈️ رحلة ذهاب فقط (One-Way Charter)', 'value' => 'One Way', 'score' => 20],
                                            ['label' => '🔄 رحلة ذهاب وعودة (Round-Trip Charter)', 'value' => 'Round Trip', 'score' => 25],
                                            ['label' => '🌍 وجهات ومحطات متعددة (Multi-Leg Charter)', 'value' => 'Multi-Leg', 'score' => 30],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'عدد المسافرين وحجم الطائرة',
                            'subtitle' => 'تحديد سعة الطائرة المطلوبة للرحلة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي السعة وحجم الطائرة المناسب لرحلتك؟',
                                    'question_key' => 'jet_capacity',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🛩️ طائرة خفيفة (Light Jet - حتى 6 ركاب)', 'value' => 'Light Jet', 'score' => 20],
                                            ['label' => '✈️ طائرة متوسطة (Midsize Jet - حتى 9 ركاب)', 'value' => 'Midsize Jet', 'score' => 25],
                                            ['label' => '👑 طائرة ثقيلة فاخرة (Heavy Jet - حتى 14 راكب)', 'value' => 'Heavy Jet', 'score' => 30],
                                            ['label' => '🏛️ طائرة رئاسية فارهة (VIP Airliner - كبار الشخصيات)', 'value' => 'VIP Airliner', 'score' => 35],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات حجز الطيران الخاص 📱',
                            'subtitle' => 'أدخل بياناتك لتصلك الخيارات الفنية والتوافر المباشر',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'form_fields',
                                    'label' => 'بيانات راكب الطيران الخاص',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'اسم صاحب الطلب / الكفيل', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الاتصال / الواتساب المباشر', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => 'تم استلام طلب حجز الطائرة الخاصة بنجاح 👑',
                            'description' => 'يتواصل معك مدير قسم الطيران المباشر فوراً لتأكيد التوافر وتفاصيل الإقلاع.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'التواصل المباشر عبر الواتساب 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 15. Web & App Project Calculator ──────────────────────────────────
            [
                'name' => 'Web & App Project Cost Estimator (حاسبة تكلفة المواقع والتطبيقات)',
                'slug' => 'web-app-project-calculator',
                'category' => 'B2B Services',
                'description' => 'قالب تقني بنفسجي وأزرق متطور لاحتساب تكلفة ومواصفات التطبيقات والمواقع والأنظمة.',
                'thumbnail_url' => '/assets/images/templates/webapp.jpg',
                'sort_order' => 15,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#7c3aed', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg', 'thank_you_page' => ['enabled' => true, 'title' => 'تم احتساب تقدير مشروعك البرمجي بنجاح! 🚀', 'subtitle' => 'فريق المهندسين يقوم بمراجعة المتطلبات وإرسال العرض الفني والمالي التفصيلي عبر الواتساب.', 'button_text' => 'استلام العرض الفني على الواتساب 💬', 'button_action' => 'whatsapp', 'button_whatsapp' => '966500000000']],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'حاسبة مشروع التطبيق أو المنصة البرمجية 💻',
                            'subtitle' => 'احصل على تقدير تكلفة وخطة تنفيذ مشروعك خلال 60 ثانية',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'حاسبة تكلفة المواقع والتطبيقات والأنظمة 💻⚡', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'حدد نوع التطبيق والخدمات المطلوبة لنرسل لك التقدير الفني والزمني والمالي.'],
                            ],
                        ],
                        [
                            'title' => 'نوع النظام البرمجي المطلوبة',
                            'subtitle' => 'حدد طبيعة المنصة أو الخدمة التقنية',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو نوع المنصة أو المشروع المطلوب تنفيذه؟',
                                    'question_key' => 'project_type',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '📱 تطبيق جوال متكامل (iOS & Android App)', 'value' => 'Mobile App', 'score' => 30],
                                            ['label' => '🛒 متجر إلكتروني احترافي مع بوابات الدفع (E-Commerce Store)', 'value' => 'E-Commerce', 'score' => 25],
                                            ['label' => '⚙️ نظام إدارة وحلول ساس (SaaS Platform / Custom ERP)', 'value' => 'Custom System', 'score' => 35],
                                            ['label' => '🌐 موقع تعريفي احترافي للشركة (Corporate Website)', 'value' => 'Website', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الميزات والمواصفات الأساسية',
                            'subtitle' => 'حدد الميزات والربط التقني المطلوب',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي أهم الميزات والأنظمة المطلوب دمجها؟',
                                    'question_key' => 'required_features',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '💳 بوابات الدفع وشحن واستخراج الفواتير', 'value' => 'Payments & Invoicing', 'score' => 20],
                                            ['label' => '🤖 الذكاء الاصطناعي وربط الواتساب الآلي (AI & WhatsApp Auto)', 'value' => 'AI & Automation', 'score' => 25],
                                            ['label' => '🌐 لوحة تحكم متعددة اللغات والصلاحيات', 'value' => 'Multilingual & Roles', 'score' => 20],
                                            ['label' => '✨ جميع ما سبق باحترافية عالية', 'value' => 'Full Enterprise Suite', 'score' => 30],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات التواصل واستلام الدراسة الفنية 📱',
                            'subtitle' => 'أدخل بياناتك لتصلك دراسة التكلفة والعرض الفني المباشر',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'form_fields',
                                    'label' => 'معلومات صاحب المشروع',
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
                            'title' => 'تم إرسال متطلبات المشروع للمهندسين المختصين 🚀',
                            'description' => 'يتواصل معك المهندس المختص لتزويدك بدراسة النطاق والعرض المالي والزمني.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'متابعة العرض الفني عبر الواتساب 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 16. Legal Advisory & Law Firm Qualifier ────────────────────────────
            [
                'name' => 'Legal Advisory & Law Firm (استشارات المحاماة والدعم القانوني)',
                'slug' => 'legal-law-advisory-qualifier',
                'category' => 'Legal & Immigration',
                'description' => 'قالب رسمي بلون كحلي وذهبي مخصص لمكاتب المحاماة والاستشارات القانونية.',
                'thumbnail_url' => '/assets/images/templates/legal.jpg',
                'sort_order' => 16,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#1e3a8a', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-md', 'thank_you_page' => ['enabled' => true, 'title' => 'تم حجز موعد الاستشارة القانونية بنجاح ⚖️', 'subtitle' => 'تم توجيه طلبك للمستشار القانوني المختص وسيتم التواصل معك مباشرة لترتيب الجلسة.', 'button_text' => 'التواصل الفوري مع المستشار 💬', 'button_action' => 'whatsapp', 'button_whatsapp' => '966500000000']],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'استشارة قانونية وتطوير الأعمال ⚖️',
                            'subtitle' => 'حجز جلسة استشارة مع مستشار قانوني معتمد',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'طلب استشارة قانونية ودعم أعمال ⚖️', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'حدد نوع القضية أو الاستشارة المطلوبة لنحدد لك المستشار القانوني المتخصص.'],
                            ],
                        ],
                        [
                            'title' => 'نوع الاستشارة القانونية',
                            'subtitle' => 'تحديد مجال الاستفسار أو القضية',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو مجال الاستشارة القانونية المطلوبة؟',
                                    'question_key' => 'legal_topic',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🏢 قضايا وتأسيس العقود والشركات (Corporate Law)', 'value' => 'Corporate', 'score' => 25],
                                            ['label' => '⚖️ النزاعات والقضايا التجارية والمالية (Commercial Disputes)', 'value' => 'Commercial', 'score' => 25],
                                            ['label' => '🌍 الاستثمار الأجنبي والملكية الفكرية (Foreign Investment)', 'value' => 'Investment', 'score' => 20],
                                            ['label' => '📝 صياغة وتوثيق الاتفاقيات والعقود (Contracts Review)', 'value' => 'Contracts', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات التواصل وحجز الجلسة 📱',
                            'subtitle' => 'أدخل بياناتك لترتيب موعد الاستشارة',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'form_fields',
                                    'label' => 'معلومات العميل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكريم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب / الاتصال', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => 'تم تسجيل طلب الاستشارة القانونية بنجاح ⚖️',
                            'description' => 'يتواصل معك المكتب القانوني لترتيب الجلسة والمواعيد المتاحة.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'التواصل الفوري على الواتساب 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 17. VIP Spa & Wellness Reservation ────────────────────────────────
            [
                'name' => 'VIP Spa & Wellness Center (مراكز السبا والاسترخاء والعناية)',
                'slug' => 'vip-spa-wellness-reservation',
                'category' => 'Healthcare',
                'description' => 'قالب رقيق وناعم بلون وردي مخصص لمراكز التجميل والسبا والعناية بالبشرة.',
                'thumbnail_url' => '/assets/images/templates/spa.jpg',
                'sort_order' => 17,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#db2777', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-full', 'thank_you_page' => ['enabled' => true, 'title' => 'تم استلام طلب حجز جلسة السبا بنجاح! 🌸✨', 'subtitle' => 'يتواصل معك موظف الاستقبال فوراً لتأكيد التوقيت المناسب وتجهيز الجلسة.', 'button_text' => 'تأكيد الحجز المباشر على الواتساب 💬', 'button_action' => 'whatsapp', 'button_whatsapp' => '966500000000']],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'جلسة الاسترخاء والعناية بالبشرة والسبا الفاخر 💆✨',
                            'subtitle' => 'احجزي وقتك الخاص للاسترخاء والعناية المتكاملة مع أفضل الخبيرات',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'حجز جلسة سبا واسترخاء فاخرة 🌸💆', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'حددي نوع الجلسة والخدمات المفضلة لنجهز لك موعد الجلسة المثالي.'],
                            ],
                        ],
                        [
                            'title' => 'نوع الجلسة والخدمة المطلوبة',
                            'subtitle' => 'اختاري نوع المساج أو العناية بالبشرة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي الخدمة أو الجلسة التي ترغبين بحجزها؟',
                                    'question_key' => 'spa_service',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '💆 مساج واسترخاء ملكي بالزيوت العطرية (Royal Aromatherapy)', 'value' => 'Aromatherapy Massage', 'score' => 25],
                                            ['label' => '✨ جلسة عناية وتنظيف عميق للبشرة (Deep Hydramedi Facial)', 'value' => 'Facial Care', 'score' => 25],
                                            ['label' => '🛁 حمام مغربي ملكي مع المساج (Royal Moroccan Bath)', 'value' => 'Moroccan Bath', 'score' => 30],
                                            ['label' => '👰 بكج العروسة والاسترخاء الشامل (VIP Bridal Spa Package)', 'value' => 'Bridal Package', 'score' => 35],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات الحجز وتأكيد الموعد 📱',
                            'subtitle' => 'أدخلي بياناتك لتأكيد حجز الجلسة',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'form_fields',
                                    'label' => 'معلومات الحجز',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب للتأكيد', 'type' => 'phone', 'required' => true],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => 'تم استلام طلب حجز الجلسة بنجاح 🌸✨',
                            'description' => 'نتطلع لاستقبالك! يتواصل معك موظف الاستقبال لتأكيد الموعد وتأكيد التفاصيل.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تأكيد الحجز المباشر على الواتساب 💬',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                            'sort_order' => 1,
                        ],
                    ],
                ],
            ],

            // ── 18. B2B Lead Magnet Template ──────────────────────────────────────
            [
                'name' => 'B2B Lead Magnet (مغناطيس جذب عملاء الشركات والدليل المجاني)',
                'slug' => 'b2b-lead-magnet',
                'category' => 'B2B Services',
                'description' => 'قالب محاكٍ لقالب involve.me الشهير لجذب واستقطاب عملاء الشركات B2B عبر كتاب/دليل مجاني واحتساب مؤهلات العملاء.',
                'thumbnail_url' => '/assets/images/templates/b2b-lead-magnet.png',
                'sort_order' => 18,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#2563eb', 'font_family' => 'Tajawal, sans-serif', 'button_style' => 'rounded-lg', 'thank_you_page' => ['enabled' => true, 'title' => 'تم تجهيز دليلك التجاري المجاني بنجاح! 📚🎉', 'subtitle' => 'شكراً لاهتمامك! تم إرسال الدليل بصيغة PDF على بريدك الإلكتروني والواتساب، ويمكنك تحميله المباشر فوراً.', 'button_text' => 'تحميل الدليل المجاني الآن 📥', 'button_action' => 'url', 'button_url' => 'https://travel-wave.com']],
                    'crm_settings' => ['enabled' => true],
                    'steps' => [
                        [
                            'title' => 'دليل تنمية ومبيعات الشركات B2B المجاني 📚⚡',
                            'subtitle' => 'احصل على النسخة المجانية من دليل استراتيجيات جذب العملاء وإغلاق الصفقات',
                            'step_type' => 'welcome',
                            'elements' => [
                                ['element_type' => 'heading', 'label' => 'دليل نمو ومبيعات الشركات B2B المجاني 📚⚡', 'properties' => ['font_size' => '2xl']],
                                ['element_type' => 'text', 'label' => 'أدخل إجاباتك السريعة لتتلقى الدليل المخصص والحلول المناسبة لحجم شركتك.'],
                            ],
                        ],
                        [
                            'title' => 'المسمى الوظيفي وحجم الشركة',
                            'subtitle' => 'حدد دورك ودور شركتك في السوق',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو مسمى عملك وحجم شركتك الحالي؟',
                                    'question_key' => 'b2b_role',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '🏢 صاحب عمل / رئيس تنفيذي (CEO / Founder / Owner)', 'value' => 'CEO / Founder', 'score' => 30],
                                            ['label' => '🎯 مدير مبيعات أو تسويق (Sales / Marketing Director)', 'value' => 'Sales Director', 'score' => 25],
                                            ['label' => '💼 مستشار أو خبير مستقل (Independent Consultant)', 'value' => 'Consultant', 'score' => 20],
                                            ['label' => '🚀 فريق عمل مشروع ناشئ (Startup Team)', 'value' => 'Startup Team', 'score' => 15],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'هدف النمو الرئيسي لشركتك',
                            'subtitle' => 'حدد الأولوية الأساسية للشركة في الوقت الحالي',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو هدف النمو الرئيسي لشركتك هذا الربع؟',
                                    'question_key' => 'b2b_goal',
                                    'properties' => [
                                        'options' => [
                                            ['label' => '📈 جذب عملاء محتملين مؤهلين بجودة عالية (Qualified B2B Leads)', 'value' => 'Qualified Leads', 'score' => 25],
                                            ['label' => '⚡ أتمتة نظام المبيعات والمتابعة التلقائية (Sales Automation)', 'value' => 'Sales Automation', 'score' => 25],
                                            ['label' => '💰 زيادة نسبة إغلاق الصفقات والإيرادات (Close Rate & Revenue)', 'value' => 'Higher Revenue', 'score' => 30],
                                            ['label' => '🌐 التوسع والدخول في أسواق جديدة (International Expansion)', 'value' => 'Expansion', 'score' => 20],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'أين نرسل لك الدليل المجاني؟ 📩',
                            'subtitle' => 'أدخل بياناتك وسيتم إرسال الدليل فوراً',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'form_fields',
                                    'label' => 'معلومات استلام الدليل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكامل', 'type' => 'text', 'required' => true],
                                            ['name' => 'phone', 'label' => 'رقم الواتساب', 'type' => 'phone', 'required' => true],
                                            ['name' => 'email', 'label' => 'بريد العمل الإلكتروني', 'type' => 'email', 'required' => false],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'results' => [
                        [
                            'title' => 'تم تجهيز دليلك التجاري المجاني بنجاح! 📚🎉',
                            'description' => 'يمكنك تحميل الدليل المباشر بصيغة PDF فوراً أو استلامه على الواتساب.',
                            'min_score' => 0,
                            'max_score' => 100,
                            'cta_label' => 'تحميل الدليل المجاني الآن 📥',
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
                    'thumbnail_url' => $tpl['thumbnail_url'] ?? null,
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
