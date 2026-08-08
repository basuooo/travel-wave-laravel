<?php

namespace Database\Seeders;

use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Database\Seeder;

class France3VisaCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $heroSlideTwo = 'hero-slides/1TunK6YuKgLHdHi2aBuDZeVe9NJXS23rNCFgFqi0.png';

        $category = VisaCategory::query()->firstOrCreate(
            ['slug' => 'european-union'],
            [
                'name_en' => 'European Union',
                'name_ar' => 'الاتحاد الأوروبي',
                'short_description_en' => 'Europe visa support with practical file preparation.',
                'short_description_ar' => 'دعم تأشيرات أوروبا مع تجهيز عملي للملف.',
                'icon' => 'EU',
                'sort_order' => 1,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        $france3Sections = [
            'section_order' => [
                'hero' => ['sort_order' => 1, 'enabled' => true],
                'quick_summary' => ['sort_order' => 2, 'enabled' => true],
                'intro' => ['sort_order' => 3, 'enabled' => true],
                'best_time' => ['sort_order' => 4, 'enabled' => true],
                'requirements' => ['sort_order' => 5, 'enabled' => true],
                'services' => ['sort_order' => 6, 'enabled' => true],
                'steps' => ['sort_order' => 7, 'enabled' => true],
                'why_choose' => ['sort_order' => 8, 'enabled' => true],
                'suitability' => ['sort_order' => 9, 'enabled' => true],
                'pricing_duration' => ['sort_order' => 10, 'enabled' => true],
                'faq' => ['sort_order' => 11, 'enabled' => true],
                'notice' => ['sort_order' => 12, 'enabled' => true],
                'cta' => ['sort_order' => 13, 'enabled' => true],
            ],

            // 1. HERO SECTION
            'hero' => [
                'enabled' => true,
                'eyebrow_ar' => 'فيزا فرنسا 🇫🇷 | شنغن',
                'eyebrow_en' => 'France Visa 🇫🇷 | Schengen',
                'title_ar' => 'فيزا فرنسا 🇫🇷',
                'title_en' => 'France Visa 🇫🇷',
                'subtitle_ar' => 'في Travel Wave بنساعدك في تجهيز ملف فيزا فرنسا ومراجعة المستندات والحجوزات وإجراءات التقديم خطوة بخطوة بثقة واحترافية.',
                'subtitle_en' => 'At Travel Wave, we help you prepare your France visa application file, review documents, and navigate submission steps with confidence.',
                'primary_cta_text_ar' => 'قيّم حالتك الآن',
                'primary_cta_text_en' => 'Assess Your Case Now',
                'primary_cta_url' => '/contact#lead-form',
            ],

            // 2. QUICK SUMMARY SECTION
            'quick_summary' => [
                'enabled' => true,
                'title_ar' => 'ملخص سريع عن فيزا فرنسا',
                'title_en' => 'Quick Summary',
                'items' => [
                    ['label_ar' => 'الدولة', 'label_en' => 'Country', 'value_ar' => 'فرنسا 🇫🇷', 'value_en' => 'France 🇫🇷', 'icon' => '🌍', 'is_active' => true, 'sort_order' => 1],
                    ['label_ar' => 'نوع التأشيرة', 'label_en' => 'Visa Type', 'value_ar' => 'شنغن قصيرة الإقامة', 'value_en' => 'Short-Stay Schengen', 'icon' => '📄', 'is_active' => true, 'sort_order' => 2],
                    ['label_ar' => 'مدة الإقامة', 'label_en' => 'Stay Duration', 'value_ar' => 'حسب شروط التأشيرة', 'value_en' => 'Per visa terms', 'icon' => '📅', 'is_active' => true, 'sort_order' => 3],
                    ['label_ar' => 'مدة الإجراءات', 'label_en' => 'Processing Time', 'value_ar' => 'تختلف حسب الحالة والمواعيد', 'value_en' => 'Varies by case & slots', 'icon' => '⏱️', 'is_active' => true, 'sort_order' => 4],
                    ['label_ar' => 'الرسوم', 'label_en' => 'Fees', 'value_ar' => 'تحدد حسب نوع الطلب والخدمات', 'value_en' => 'Varies by service', 'icon' => '💳', 'is_active' => true, 'sort_order' => 5],
                ],
            ],

            // 3. INTRO SECTION
            'intro' => [
                'enabled' => true,
                'title_ar' => 'نبذة عن فيزا فرنسا',
                'title_en' => 'About France Visa',
                'description_ar' => "فيزا فرنسا قصيرة الإقامة هي تأشيرة شنغن تسمح بالسفر إلى فرنسا ودول منطقة الشنغن وفق مدة وشروط التأشيرة.\n\nتجهيز ملف منظم ومتناسق مع هدف السفر والمستندات المقدمة أمر أساسي لضمان تقديم طلبك بأفضل صورة ممكنة.",
                'description_en' => "The France short-stay visa is a Schengen visa allowing travel to France and Schengen area states as permitted.\n\nPreparing a well-organized application file matching your trip purpose is key to presenting your request professionally.",
            ],

            // 4. BEST TIME TO APPLY SECTION
            'best_time' => [
                'enabled' => true,
                'title_ar' => 'متى تبدأ إجراءات فيزا فرنسا؟',
                'title_en' => 'When to Start France Visa Steps?',
                'text_ar' => 'يفضل البدء في إجراءات التأشيرة مبكرًا قبل موعد السفر بوقت كافٍ، خاصة خلال مواسم السفر والضغط على المواعيد.',
                'note_ar' => 'كلما بدأت تجهيز ملفك مبكرًا، كان لديك وقت أفضل لاستكمال أي مستندات مطلوبة وحجز الموعد المناسب.',
                'text_en' => 'It is best to start your visa procedures early well ahead of travel, especially during peak travel seasons.',
                'note_en' => 'The earlier you start preparing your file, the better buffer you have to finalize documents and secure prime appointment slots.',
            ],

            // 5. REQUIREMENTS SECTION
            'requirements' => [
                'enabled' => true,
                'title_ar' => 'إيه أهم متطلبات فيزا فرنسا؟',
                'title_en' => 'Key Requirements',
                'subtitle_ar' => 'أبرز المستندات والأوراق المطلوبة لتجهيز ملف التقديم.',
                'subtitle_en' => 'Core documents required for visa file preparation.',
                'note_ar' => 'المتطلبات بتختلف من حالة للتانية، وفريق Travel Wave بيوضح لك المستندات المناسبة لحالتك قبل البدء.',
                'note_en' => 'Requirements vary by case. Travel Wave team clarifies the precise document checklist for your situation before starting.',
                'items' => [
                    ['icon' => '🛂', 'title_ar' => 'جواز السفر', 'title_en' => 'Passport', 'text_ar' => 'جواز سفر ساري ومستوفي شروط السفر وفيه صفحات فارغة.', 'text_en' => 'Valid passport meeting travel terms with blank pages.', 'is_active' => true, 'sort_order' => 1],
                    ['icon' => '📸', 'title_ar' => 'الصور الشخصية', 'title_en' => 'Personal Photos', 'text_ar' => 'صور حديثة بخلفية بيضاء بالمواصفات المطلوبة.', 'text_en' => 'Recent white background passport photos.', 'is_active' => true, 'sort_order' => 2],
                    ['icon' => '🏦', 'title_ar' => 'كشف الحساب البنكي', 'title_en' => 'Bank Statement', 'text_ar' => 'إثبات مناسب للوضع المالي وحركة الحساب.', 'text_en' => 'Recent bank statement showing financial stability.', 'is_active' => true, 'sort_order' => 3],
                    ['icon' => '💼', 'title_ar' => 'إثبات الوظيفة أو الدخل', 'title_en' => 'Employment Proof', 'text_ar' => 'مستندات تثبت الحالة الوظيفية أو مصدر الدخل حسب الحالة.', 'text_en' => 'Documents proving employment status or income source.', 'is_active' => true, 'sort_order' => 4],
                    ['icon' => '🏨', 'title_ar' => 'إثبات السفر والإقامة', 'title_en' => 'Travel & Accommodation', 'text_ar' => 'مستندات مبدئية توضح خطة السفر والإقامة.', 'text_en' => 'Flight itineraries and hotel reservations.', 'is_active' => true, 'sort_order' => 5],
                    ['icon' => '🛡️', 'title_ar' => 'التأمين الطبي للسفر', 'title_en' => 'Travel Insurance', 'text_ar' => 'تأمين طبي مناسب لمتطلبات منطقة الشنغن.', 'text_en' => 'Travel insurance compliant with Schengen requirements.', 'is_active' => true, 'sort_order' => 6],
                    ['icon' => '📑', 'title_ar' => 'مستندات إضافية', 'title_en' => 'Additional Documents', 'text_ar' => 'قد تختلف المستندات حسب حالة كل متقدم والغرض من السفر.', 'text_en' => 'Additional supporting documents depending on your case purpose.', 'is_active' => true, 'sort_order' => 7],
                ],
            ],

            // 6. SERVICES SECTION
            'services' => [
                'enabled' => true,
                'title_ar' => 'إحنا بنساعدك في إيه؟',
                'title_en' => 'How We Help You',
                'subtitle_ar' => 'خدمات متكاملة تجعل تجربة التقديم أسهل وأكثر تنظيمًا.',
                'subtitle_en' => 'Comprehensive services making your application easier and structured.',
                'items' => [
                    ['icon' => '📝', 'title_ar' => 'تجهيز ملف التأشيرة', 'title_en' => 'Visa File Preparation', 'text_ar' => 'تعبئة النماذج وتنظيم الملف وفقًا للمتطلبات.', 'text_en' => 'Filling forms and structuring your file per guidelines.', 'is_active' => true, 'sort_order' => 1],
                    ['icon' => '🔎', 'title_ar' => 'مراجعة المستندات', 'title_en' => 'Document Audit', 'text_ar' => 'مراجعة كافة الأوراق وتصحيح الملاحظات قبل التقديم.', 'text_en' => 'Reviewing all paperwork and addressing remarks beforehand.', 'is_active' => true, 'sort_order' => 2],
                    ['icon' => '✅', 'title_ar' => 'مراجعة الملف قبل التقديم', 'title_en' => 'Pre-Submission Review', 'text_ar' => 'فحص شامل للتأكد من اكتمال وتطابق الملف.', 'text_en' => 'Final review to ensure file completeness and consistency.', 'is_active' => true, 'sort_order' => 3],
                    ['icon' => '📅', 'title_ar' => 'المساعدة في حجز الموعد', 'title_en' => 'Appointment Support', 'text_ar' => 'متابعة وتنسيق المواعيد المتاحة بمركز التأشيرات.', 'text_en' => 'Tracking and booking available appointment slots.', 'is_active' => true, 'sort_order' => 4],
                    ['icon' => '🌐', 'title_ar' => 'الترجمة المعتمدة عند الحاجة', 'title_en' => 'Certified Translation', 'text_ar' => 'توفير الترجمة المعتمدة للمستندات المطلوبة.', 'text_en' => 'Providing certified translation for required papers.', 'is_active' => true, 'sort_order' => 5],
                    ['icon' => '✈️', 'title_ar' => 'حجوزات الطيران والفنادق', 'title_en' => 'Flight & Hotel Bookings', 'text_ar' => 'تأمين الحجوزات المبدئية المطلوبة لملف السفر.', 'text_en' => 'Securing necessary itinerary flight and hotel bookings.', 'is_active' => true, 'sort_order' => 6],
                    ['icon' => '🤝', 'title_ar' => 'توجيه العميل خلال الخطوات', 'title_en' => 'Step-by-Step Guidance', 'text_ar' => 'إرشاد واضح ومستمر لتفادي أي أخطاء في التقديم.', 'text_en' => 'Clear ongoing advice to prevent common submission errors.', 'is_active' => true, 'sort_order' => 7],
                    ['icon' => '💬', 'title_ar' => 'متابعة خلال مراحل التجهيز', 'title_en' => 'Preparation Follow-Up', 'text_ar' => 'متابعة دورية معك حتى تكتمل جميع المتطلبات.', 'text_en' => 'Regular follow-ups until all file requirements are met.', 'is_active' => true, 'sort_order' => 8],
                ],
            ],

            // 7. APPLICATION STEPS SECTION
            'steps' => [
                'enabled' => true,
                'title_ar' => 'خطوات التقديم',
                'title_en' => 'Application Steps',
                'subtitle_ar' => 'إجراءات التقديم في 5 خطوات بسيطة.',
                'subtitle_en' => 'Submission procedures in 5 simple steps.',
                'items' => [
                    [
                        'step_number' => '01',
                        'title_ar' => 'تقييم حالتك',
                        'title_en' => 'Case Assessment',
                        'text_ar' => 'نفهم حالتك وهدف السفر ونحدد الخطوات المناسبة.',
                        'text_en' => 'We understand your case and trip goals to outline the proper path.',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'step_number' => '02',
                        'title_ar' => 'مراجعة وتجهيز الملف',
                        'title_en' => 'File Review & Setup',
                        'text_ar' => 'نحدد المستندات المطلوبة ونراجع الملف قبل التقديم.',
                        'text_en' => 'We detail needed papers and organize your application file.',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'step_number' => '03',
                        'title_ar' => 'استكمال المستندات والحجوزات',
                        'title_en' => 'Document & Booking Completion',
                        'text_ar' => 'نساعدك في تجهيز المستندات والحجوزات المطلوبة حسب حالتك.',
                        'text_en' => 'We assist in gathering supporting papers and flight/hotel bookings.',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'step_number' => '04',
                        'title_ar' => 'حجز الموعد',
                        'title_en' => 'Appointment Scheduling',
                        'text_ar' => 'نساعدك في إجراءات حجز موعد التقديم.',
                        'text_en' => 'We support you in scheduling your submission appointment.',
                        'is_active' => true,
                        'sort_order' => 4,
                    ],
                    [
                        'step_number' => '05',
                        'title_ar' => 'التقديم',
                        'title_en' => 'Submission',
                        'text_ar' => 'تستكمل إجراءات التقديم والبصمة حسب حالتك.',
                        'text_en' => 'You finalize submission and biometrics at the center.',
                        'is_active' => true,
                        'sort_order' => 5,
                    ],
                ],
            ],

            // 8. WHY TRAVEL WAVE SECTION
            'why_choose' => [
                'enabled' => true,
                'title_ar' => 'ليه تختار Travel Wave؟',
                'title_en' => 'Why Choose Travel Wave?',
                'subtitle_ar' => 'مزايا الخدمة التي تضمن لك تجربة تقديم مريحة واحترافية.',
                'subtitle_en' => 'Key advantages ensuring a seamless, professional experience.',
                'items' => [
                    ['icon' => '🔍', 'title_ar' => 'تقييم حالتك قبل البدء', 'title_en' => 'Pre-Start Evaluation', 'text_ar' => 'فهم موقفك وتوضيح أنسب خيار لك قبل البدء.', 'text_en' => 'Evaluating your profile to clarify the best approach beforehand.', 'is_active' => true, 'sort_order' => 1],
                    ['icon' => '🎯', 'title_ar' => 'مراجعة دقيقة للملف', 'title_en' => 'Meticulous Audit', 'text_ar' => 'التأكد من اكتمال المستندات وتوافقها.', 'text_en' => 'Ensuring all documents are consistent and complete.', 'is_active' => true, 'sort_order' => 2],
                    ['icon' => '📁', 'title_ar' => 'تنظيم المستندات بشكل واضح', 'title_en' => 'Clear Organization', 'text_ar' => 'ترتيب الأوراق وفق معايير التقديم الرسمية.', 'text_en' => 'Structuring your file to match official submission standards.', 'is_active' => true, 'sort_order' => 3],
                    ['icon' => '⚡', 'title_ar' => 'توفير الوقت والمجهود', 'title_en' => 'Time & Effort Saving', 'text_ar' => 'خطوات عملية تختصر عليك الوقت والبحث.', 'text_en' => 'Practical workflows saving you time and research effort.', 'is_active' => true, 'sort_order' => 4],
                    ['icon' => '🇪🇺', 'title_ar' => 'خبرة في ملفات تأشيرات شنغن', 'title_en' => 'Schengen Expertise', 'text_ar' => 'فهم عميق لمتطلبات وتفاصيل التأشيرة.', 'text_en' => 'Deep technical understanding of Schengen file requirements.', 'is_active' => true, 'sort_order' => 5],
                    ['icon' => '🤝', 'title_ar' => 'متابعة وإرشاد خلال الخطوات', 'title_en' => 'Guidance & Support', 'text_ar' => 'دعم متواصل وإجابة على كافة استفساراتك.', 'text_en' => 'Continuous support and answers to all your inquiries.', 'is_active' => true, 'sort_order' => 6],
                ],
            ],

            // 9. SUITABILITY ASSESSMENT SECTION
            'suitability' => [
                'enabled' => true,
                'title_ar' => 'مش عارف إذا كانت حالتك مناسبة؟',
                'title_en' => 'Not Sure If You Qualify?',
                'description_ar' => 'قبول التأشيرة بيعتمد على عدة عوامل، منها الحالة الوظيفية، الوضع المالي، سبب السفر، المستندات المقدمة، وخطة الرحلة.',
                'description_en' => 'Visa decisions rely on various factors including employment, finances, trip purpose, documents, and itinerary.',
                'note_ar' => 'بدل ما تحاول تحدد موقفك بنفسك، تواصل مع Travel Wave وفريقنا يراجع حالتك ويوضح لك الخطوات المناسبة.',
                'note_en' => 'Instead of guessing your readiness, reach out to Travel Wave and our team will review your case and outline the right steps.',
                'button_text_ar' => 'قيّم حالتك الآن',
                'button_text_en' => 'Assess Your Case Now',
                'button_url' => '/contact#lead-form',
            ],

            // 10. PRICING & DURATION SECTION
            'pricing_duration' => [
                'enabled' => true,
                'title_ar' => 'الرسوم ومدة الإجراءات',
                'title_en' => 'Duration & Fees',
                'duration_title_ar' => 'مدة الإجراءات',
                'duration_title_en' => 'Processing Time',
                'duration_text_ar' => 'تختلف مدة دراسة الطلب حسب الحالة، مواعيد التقديم، والجهة المختصة.',
                'duration_text_en' => 'Processing duration varies based on applicant profile, appointment slots, and embassy authority.',
                'fees_title_ar' => 'الرسوم',
                'fees_title_en' => 'Fees',
                'fees_text_ar' => 'تختلف الرسوم حسب نوع التأشيرة والخدمات المطلوبة.',
                'fees_text_en' => 'Fees differ depending on visa category and requested services.',
            ],

            // 11. FAQ SECTION
            'faq' => [
                'enabled' => true,
                'title_ar' => 'الأسئلة الشائعة عن فيزا فرنسا',
                'title_en' => 'France Visa FAQs',
                'items' => [
                    [
                        'question_ar' => 'هل فيزا فرنسا فيزا شنغن؟',
                        'question_en' => 'Is France Visa a Schengen Visa?',
                        'answer_ar' => 'نعم، فيزا فرنسا قصيرة الإقامة تكون ضمن تأشيرات شنغن وفق شروط التأشيرة.',
                        'answer_en' => 'Yes, France short-stay visa is a Schengen visa according to visa terms.',
                        'is_active' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'question_ar' => 'هل لازم أحضر بنفسي؟',
                        'question_en' => 'Must I Attend in Person?',
                        'answer_ar' => 'قد تكون الحضور الشخصي والبصمة مطلوبة حسب حالة المتقدم والمتطلبات الحالية بمركز التأشيرات.',
                        'answer_en' => 'Personal attendance for biometrics is required based on center requirements.',
                        'is_active' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'question_ar' => 'هل أقدر أقدم لأول مرة؟',
                        'question_en' => 'Can First-Time Applicants Apply?',
                        'answer_ar' => 'نعم، يمكن للمتقدمين لأول مرة التقديم، بشرط تجهيز ملف مناسب ومتكامل لحالتهم.',
                        'answer_en' => 'Yes, first-time applicants can apply with a complete, well-prepared application file.',
                        'is_active' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'question_ar' => 'هل لازم يكون عندي حساب بنكي؟',
                        'question_en' => 'Is a Bank Account Required?',
                        'answer_ar' => 'إثبات القدرة المالية جزء مهم من ملف التأشيرة، وتختلف المستندات المطلوبة حسب الحالة.',
                        'answer_en' => 'Demonstrating financial capacity is key, and specific documents depend on your case.',
                        'is_active' => true,
                        'sort_order' => 4,
                    ],
                    [
                        'question_ar' => 'هل Travel Wave تضمن قبول الفيزا؟',
                        'question_en' => 'Does Travel Wave Guarantee Visa Approval?',
                        'answer_ar' => 'لا. القرار النهائي يكون من الجهة المختصة بالسفارة، ودور Travel Wave هو مساعدتك في تجهيز ومراجعة ملفك بشكل منظم بأعلى دقة.',
                        'answer_en' => 'No. Final decisions rest solely with embassy authorities. Travel Wave helps prepare and audit your file professionally.',
                        'is_active' => true,
                        'sort_order' => 5,
                    ],
                    [
                        'question_ar' => 'إزاي أعرف المستندات المطلوبة لحالتي؟',
                        'question_en' => 'How Do I Know the Required Documents for My Case?',
                        'answer_ar' => 'تواصل معنا وفريق Travel Wave يراجع حالتك ويوضح لك المستندات والخطوات المناسبة.',
                        'answer_en' => 'Reach out to us and the Travel Wave team will evaluate your status and provide your tailored checklist.',
                        'is_active' => true,
                        'sort_order' => 6,
                    ],
                ],
            ],

            // 12. IMPORTANT NOTICE SECTION
            'notice' => [
                'enabled' => true,
                'title_ar' => 'مهم تعرف',
                'title_en' => 'Important Notice',
                'text_ar' => 'Travel Wave تساعدك في تجهيز ومراجعة ملف التأشيرة والإجراءات المتعلقة بالتقديم، لكن القرار النهائي بقبول أو رفض طلب التأشيرة يكون من الجهة المختصة.',
                'text_en' => 'Travel Wave assists with visa file preparation and guidance, but final approval or rejection decisions rest exclusively with the relevant authority.',
            ],

            // 13. FINAL CTA SECTION
            'cta' => [
                'enabled' => true,
                'title_ar' => 'جاهز تبدأ إجراءات فيزا فرنسا؟',
                'title_en' => 'Ready to Start France Visa Steps?',
                'description_ar' => 'سيب بياناتك وفريق Travel Wave هيتواصل معاك، يفهم حالتك ويوضح لك الخطوات والمستندات المناسبة.',
                'description_en' => 'Leave your details and the Travel Wave team will contact you, evaluate your situation, and outline the proper steps.',
                'button_text_ar' => 'تواصل معنا الآن',
                'button_text_en' => 'Contact Us Now',
                'button_url' => '/contact#lead-form',
            ],
        ];

        VisaCountry::query()->updateOrCreate(
            ['slug' => 'france-3-visa'],
            [
                'visa_category_id' => $category->id,
                'name_en' => 'France 3',
                'name_ar' => 'فرنسا 3',
                'excerpt_en' => 'France 3 visa destination - Schengen visa support with high conversion landing layout.',
                'excerpt_ar' => 'فرنسا 3 في قسم وجهات التأشيرات — تجمع بين محتوى فيزا فرنسا الشنغن القوي وتنظيم وتصميم الهبوط العالي.',
                'hero_badge_en' => 'France Visa 🇫🇷 | Schengen',
                'hero_badge_ar' => 'فيزا فرنسا 🇫🇷 | شنغن',
                'hero_title_en' => 'France Visa 🇫🇷',
                'hero_title_ar' => 'فيزا فرنسا 🇫🇷',
                'hero_subtitle_en' => 'At Travel Wave, we help you prepare your France visa application file, review documents, and navigate submission steps with confidence.',
                'hero_subtitle_ar' => 'في Travel Wave بنساعدك في تجهيز ملف فيزا فرنسا ومراجعة المستندات والحجوزات وإجراءات التقديم خطوة بخطوة بثقة واحترافية.',
                'hero_cta_text_en' => 'Assess Your Case Now',
                'hero_cta_text_ar' => 'قيّم حالتك الآن',
                'hero_cta_url' => '/contact#lead-form',
                'hero_overlay_opacity' => 0.50,
                'hero_image' => $heroSlideTwo,
                'hero_mobile_image' => $heroSlideTwo,
                'flag_image' => 'visa-countries/france-flag.svg',
                'visa_type_en' => 'Short-Stay Schengen',
                'visa_type_ar' => 'شنغن قصيرة الإقامة',
                'stay_duration_en' => 'Per visa terms',
                'stay_duration_ar' => 'حسب شروط التأشيرة',
                'sections' => $france3Sections,
                'meta_title_ar' => 'فرنسا 3 | فيزا شنغن فرنسا - قسم وجهات التأشيرات - ترافل ويف',
                'meta_title_en' => 'France 3 | France Visa Destinations - Travel Wave',
                'meta_description_ar' => 'تأشيرة فرنسا 3 في قسم وجهات التأشيرات. نساعدك في تقييم حالتك وتجهيز ومراجعة الملف بحرفية عالية.',
                'meta_description_en' => 'France 3 Schengen visa under Visa Destinations. Profile evaluation, file review, and appointment support.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );
    }
}
