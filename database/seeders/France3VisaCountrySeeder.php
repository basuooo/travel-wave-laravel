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
                'hero_cta_url' => '#visa-inquiry',
                'hero_overlay_opacity' => 0.50,
                'hero_image' => $heroSlideTwo,
                'hero_mobile_image' => $heroSlideTwo,
                'flag_image' => 'visa-countries/france-flag.svg',
                'visa_type_en' => 'Short-Stay Schengen',
                'visa_type_ar' => 'شنغن قصيرة الإقامة',
                'stay_duration_en' => 'Per visa terms',
                'stay_duration_ar' => 'حسب شروط التأشيرة',
                'quick_summary_destination_label_en' => 'Country',
                'quick_summary_destination_label_ar' => 'الدولة',
                'quick_summary_destination_icon' => 'globe',
                'quick_summary_items' => [
                    ['title_en' => 'Country', 'title_ar' => 'الدولة', 'value_en' => 'France 🇫🇷', 'value_ar' => 'فرنسا 🇫🇷', 'icon' => 'globe', 'is_active' => true, 'sort_order' => 1],
                    ['title_en' => 'Visa Type', 'title_ar' => 'نوع التأشيرة', 'value_en' => 'Short-Stay Schengen', 'value_ar' => 'شنغن قصيرة الإقامة', 'icon' => 'file', 'is_active' => true, 'sort_order' => 2],
                    ['title_en' => 'Stay Duration', 'title_ar' => 'مدة الإقامة', 'value_en' => 'Per visa terms', 'value_ar' => 'حسب شروط التأشيرة', 'icon' => 'calendar', 'is_active' => true, 'sort_order' => 3],
                    ['title_en' => 'Processing Time', 'title_ar' => 'مدة الإجراءات', 'value_en' => 'Varies by case & slots', 'value_ar' => 'تختلف حسب الحالة والمواعيد', 'icon' => 'clock', 'is_active' => true, 'sort_order' => 4],
                    ['title_en' => 'Approx. Fees', 'title_ar' => 'الرسوم', 'value_en' => 'Per service request', 'value_ar' => 'تحدد حسب نوع الطلب والخدمات', 'icon' => 'money', 'is_active' => true, 'sort_order' => 5],
                ],
                'introduction_title_en' => 'About France Visa',
                'introduction_title_ar' => 'نبذة عن فيزا فرنسا',
                'overview_en' => "The France short-stay visa is a Schengen visa allowing travel to France and Schengen area states as permitted.\n\nPreparing a well-organized application file matching your trip purpose is key to presenting your request professionally.",
                'overview_ar' => "فيزا فرنسا قصيرة الإقامة هي تأشيرة شنغن تسمح بالسفر إلى فرنسا ودول منطقة الشنغن وفق مدة وشروط التأشيرة.\n\nتجهيز ملف منظم ومتناسق مع هدف السفر والمستندات المقدمة أمر أساسي لضمان تقديم طلبك بأفضل صورة ممكنة.",
                'best_time_title_en' => 'When to Start France Visa Steps?',
                'best_time_title_ar' => 'متى تبدأ إجراءات فيزا فرنسا؟',
                'best_time_description_en' => 'It is best to start your visa procedures early well ahead of travel, especially during peak travel seasons. The earlier you start preparing your file, the better buffer you have to finalize documents.',
                'best_time_description_ar' => 'يفضل البدء في إجراءات التأشيرة مبكرًا قبل موعد السفر بوقت كافٍ، خاصة خلال مواسم السفر والضغط على المواعيد. كلما بدأت تجهيز ملفك مبكرًا، كان لديك وقت أفضل لاستكمال أي مستندات مطلوبة وحجز الموعد المناسب.',
                'documents_title_en' => 'Key Requirements',
                'documents_title_ar' => 'إيه أهم متطلبات فيزا فرنسا؟',
                'documents_subtitle_en' => 'Requirements vary by case. Travel Wave team clarifies the precise document checklist for your situation before starting.',
                'documents_subtitle_ar' => 'المتطلبات بتختلف من حالة للتانية، وفريق Travel Wave بيوضح لك المستندات المناسبة لحالتك قبل البدء.',
                'document_items' => [
                    ['name_en' => 'Passport', 'name_ar' => 'جواز السفر', 'description_en' => 'Valid passport meeting travel terms with blank pages.', 'description_ar' => 'جواز سفر ساري ومستوفي شروط السفر وفيه صفحات فارغة.', 'sort_order' => 1, 'is_active' => true],
                    ['name_en' => 'Personal Photos', 'name_ar' => 'الصور الشخصية', 'description_en' => 'Recent white background passport photos.', 'description_ar' => 'صور حديثة بخلفية بيضاء بالمواصفات المطلوبة.', 'sort_order' => 2, 'is_active' => true],
                    ['name_en' => 'Bank Statement', 'name_ar' => 'كشف الحساب البنكي', 'description_en' => 'Recent bank statement showing financial stability.', 'description_ar' => 'إثبات مناسب للوضع المالي وحركة الحساب.', 'sort_order' => 3, 'is_active' => true],
                    ['name_en' => 'Employment Proof', 'name_ar' => 'إثبات الوظيفة أو الدخل', 'description_en' => 'Documents proving employment status or income source.', 'description_ar' => 'مستندات تثبت الحالة الوظيفية أو مصدر الدخل حسب الحالة.', 'sort_order' => 4, 'is_active' => true],
                    ['name_en' => 'Travel & Accommodation', 'name_ar' => 'إثبات السفر والإقامة', 'description_en' => 'Flight itineraries and hotel reservations.', 'description_ar' => 'مستندات مبدئية توضح خطة السفر والإقامة.', 'sort_order' => 5, 'is_active' => true],
                    ['name_en' => 'Travel Insurance', 'name_ar' => 'التأمين الطبي للسفر', 'description_en' => 'Travel insurance compliant with Schengen requirements.', 'description_ar' => 'تأمين طبي مناسب لمتطلبات منطقة الشنغن.', 'sort_order' => 6, 'is_active' => true],
                    ['name_en' => 'Additional Documents', 'name_ar' => 'مستندات إضافية', 'description_en' => 'Additional supporting documents depending on your case purpose.', 'description_ar' => 'قد تختلف المستندات حسب حالة كل متقدم والغرض من السفر.', 'sort_order' => 7, 'is_active' => true],
                ],
                'services' => [
                    ['title_en' => 'Visa File Preparation', 'title_ar' => 'تجهيز ملف التأشيرة', 'text_en' => 'Filling forms and structuring your file per guidelines.', 'text_ar' => 'تعبئة النماذج وتنظيم الملف وفقًا للمتطلبات.', 'icon' => 'file', 'sort_order' => 1, 'is_active' => true],
                    ['title_en' => 'Document Audit', 'title_ar' => 'مراجعة المستندات', 'text_en' => 'Reviewing all paperwork and addressing remarks beforehand.', 'text_ar' => 'مراجعة كافة الأوراق وتصحيح الملاحظات قبل التقديم.', 'icon' => 'shield', 'sort_order' => 2, 'is_active' => true],
                    ['title_en' => 'Pre-Submission Review', 'title_ar' => 'مراجعة الملف قبل التقديم', 'text_en' => 'Final review to ensure file completeness and consistency.', 'text_ar' => 'فحص شامل للتأكد من اكتمال وتطابق الملف.', 'icon' => 'check', 'sort_order' => 3, 'is_active' => true],
                    ['title_en' => 'Appointment Support', 'title_ar' => 'المساعدة في حجز الموعد', 'text_en' => 'Tracking and booking available appointment slots.', 'text_ar' => 'متابعة وتنسيق المواعيد المتاحة بمركز التأشيرات.', 'icon' => 'calendar', 'sort_order' => 4, 'is_active' => true],
                    ['title_en' => 'Certified Translation', 'title_ar' => 'الترجمة المعتمدة عند الحاجة', 'text_en' => 'Providing certified translation for required papers.', 'text_ar' => 'توفير الترجمة المعتمدة للمستندات المطلوبة.', 'icon' => 'globe', 'sort_order' => 5, 'is_active' => true],
                    ['title_en' => 'Flight & Hotel Bookings', 'title_ar' => 'حجوزات الطيران والفنادق', 'text_en' => 'Securing necessary itinerary flight and hotel bookings.', 'text_ar' => 'تأمين الحجوزات المبدئية المطلوبة لملف السفر.', 'icon' => 'plane', 'sort_order' => 6, 'is_active' => true],
                    ['title_en' => 'Step-by-Step Guidance', 'title_ar' => 'توجيه العميل خلال الخطوات', 'text_en' => 'Clear ongoing advice to prevent common submission errors.', 'text_ar' => 'إرشاد واضح ومستمر لتفادي أي أخطاء في التقديم.', 'icon' => 'support', 'sort_order' => 7, 'is_active' => true],
                    ['title_en' => 'Preparation Follow-Up', 'title_ar' => 'متابعة خلال مراحل التجهيز', 'text_en' => 'Regular follow-ups until all file requirements are met.', 'text_ar' => 'متابعة دورية معك حتى تكتمل جميع المتطلبات.', 'icon' => 'clock', 'sort_order' => 8, 'is_active' => true],
                ],
                'steps_title_en' => 'Application Steps',
                'steps_title_ar' => 'خطوات التقديم',
                'step_items' => [
                    ['step_number' => '01', 'title_en' => 'Case Assessment', 'title_ar' => 'تقييم حالتك', 'description_en' => 'We understand your case and trip goals.', 'description_ar' => 'نفهم حالتك وهدف السفر ونحدد الخطوات المناسبة.', 'sort_order' => 1, 'is_active' => true],
                    ['step_number' => '02', 'title_en' => 'File Review', 'title_ar' => 'مراجعة وتجهيز الملف', 'description_en' => 'We detail needed papers and organize your file.', 'description_ar' => 'نحدد المستندات المطلوبة ونراجع الملف قبل التقديم.', 'sort_order' => 2, 'is_active' => true],
                    ['step_number' => '03', 'title_en' => 'Docs & Bookings', 'title_ar' => 'استكمال المستندات والحجوزات', 'description_en' => 'We assist in gathering supporting papers and bookings.', 'description_ar' => 'نساعدك في تجهيز المستندات والحجوزات المطلوبة حسب حالتك.', 'sort_order' => 3, 'is_active' => true],
                    ['step_number' => '04', 'title_en' => 'Appointment Booking', 'title_ar' => 'حجز الموعد', 'description_en' => 'We support you in scheduling your appointment.', 'description_ar' => 'نساعدك في إجراءات حجز موعد التقديم.', 'sort_order' => 4, 'is_active' => true],
                    ['step_number' => '05', 'title_en' => 'Submission', 'title_ar' => 'التقديم', 'description_en' => 'You finalize submission and biometrics at the center.', 'description_ar' => 'تستكمل إجراءات التقديم والبصمة حسب حالتك.', 'sort_order' => 5, 'is_active' => true],
                ],
                'why_choose_title_en' => 'Why Choose Travel Wave?',
                'why_choose_title_ar' => 'ليه تختار Travel Wave؟',
                'why_choose_items' => [
                    ['title_en' => 'Pre-Start Evaluation', 'title_ar' => 'تقييم حالتك قبل البدء', 'description_en' => 'Evaluating your profile to clarify the best approach.', 'description_ar' => 'فهم موقفك وتوضيح أنسب خيار لك قبل البدء.', 'icon' => 'shield', 'sort_order' => 1, 'is_active' => true],
                    ['title_en' => 'Meticulous Audit', 'title_ar' => 'مراجعة دقيقة للملف', 'description_en' => 'Ensuring all documents are consistent and complete.', 'description_ar' => 'التأكد من اكتمال المستندات وتوافقها.', 'icon' => 'check', 'sort_order' => 2, 'is_active' => true],
                    ['title_en' => 'Clear Organization', 'title_ar' => 'تنظيم المستندات بشكل واضح', 'description_en' => 'Structuring your file to match official standards.', 'description_ar' => 'ترتيب الأوراق وفق معايير التقديم الرسمية.', 'icon' => 'file', 'sort_order' => 3, 'is_active' => true],
                    ['title_en' => 'Time & Effort Saving', 'title_ar' => 'توفير الوقت والمجهود', 'description_en' => 'Practical workflows saving you time and research effort.', 'description_ar' => 'خطوات عملية تختصر عليك الوقت والبحث.', 'icon' => 'clock', 'sort_order' => 4, 'is_active' => true],
                    ['title_en' => 'Schengen Expertise', 'title_ar' => 'خبرة في ملفات تأشيرات شنغن', 'description_en' => 'Deep technical understanding of Schengen file requirements.', 'description_ar' => 'فهم عميق لمتطلبات وتفاصيل التأشيرة.', 'icon' => 'globe', 'sort_order' => 5, 'is_active' => true],
                    ['title_en' => 'Guidance & Support', 'title_ar' => 'متابعة وإرشاد خلال الخطوات', 'description_en' => 'Continuous support and answers to all inquiries.', 'description_ar' => 'دعم متواصل وإجابة على كافة استفساراتك.', 'icon' => 'support', 'sort_order' => 6, 'is_active' => true],
                ],
                'support_title_en' => 'Not Sure If You Qualify?',
                'support_title_ar' => 'مش عارف إذا كانت حالتك مناسبة؟',
                'support_subtitle_en' => 'Visa decisions rely on various factors. Instead of guessing, reach out to Travel Wave and our team will review your case.',
                'support_subtitle_ar' => 'قبول التأشيرة بيعتمد على عدة عوامل، منها الحالة الوظيفية والوضع المالي وسبب السفر. بدل ما تحاول تحدد موقفك بنفسك، تواصل مع Travel Wave وفريقنا يراجع حالتك ويوضح لك الخطوات المناسبة.',
                'support_button_en' => 'Assess Your Case Now',
                'support_button_ar' => 'قيّم حالتك الآن',
                'support_button_link' => '#visa-inquiry',
                'processing_time_en' => 'Varies by applicant profile, appointment slots, and embassy authority.',
                'processing_time_ar' => 'تختلف مدة دراسة الطلب حسب الحالة، مواعيد التقديم، والجهة المختصة.',
                'fees_en' => 'Varies depending on visa category and requested services.',
                'fees_ar' => 'تختلف الرسوم حسب نوع التأشيرة والخدمات المطلوبة.',
                'faq_title_en' => 'France Visa FAQs',
                'faq_title_ar' => 'الأسئلة الشائعة عن فيزا فرنسا',
                'faqs' => [
                    ['question_en' => 'Is France Visa a Schengen Visa?', 'question_ar' => 'هل فيزا فرنسا فيزا شنغن؟', 'answer_en' => 'Yes, France short-stay visa is a Schengen visa.', 'answer_ar' => 'نعم، فيزا فرنسا قصيرة الإقامة تكون ضمن تأشيرات شنغن وفق شروط التأشيرة.', 'is_active' => true, 'sort_order' => 1],
                    ['question_en' => 'Must I Attend in Person?', 'question_ar' => 'هل لازم أحضر بنفسي؟', 'answer_en' => 'Personal attendance for biometrics is required.', 'answer_ar' => 'قد تكون الحضور الشخصي والبصمة مطلوبة حسب حالة المتقدم والمتطلبات الحالية بمركز التأشيرات.', 'is_active' => true, 'sort_order' => 2],
                    ['question_en' => 'Can First-Time Applicants Apply?', 'question_ar' => 'هل أقدر أقدم لأول مرة؟', 'answer_en' => 'Yes, first-time applicants can apply.', 'answer_ar' => 'نعم، يمكن للمتقدمين لأول مرة التقديم، بشرط تجهيز ملف مناسب ومتكامل لحالتهم.', 'is_active' => true, 'sort_order' => 3],
                    ['question_en' => 'Is a Bank Account Required?', 'question_ar' => 'هل لازم يكون عندي حساب بنكي؟', 'answer_en' => 'Demonstrating financial capacity is key.', 'answer_ar' => 'إثبات القدرة المالية جزء مهم من ملف التأشيرة، وتختلف المستندات المطلوبة حسب الحالة.', 'is_active' => true, 'sort_order' => 4],
                    ['question_en' => 'Does Travel Wave Guarantee Visa Approval?', 'question_ar' => 'هل Travel Wave تضمن قبول الفيزا؟', 'answer_en' => 'No. Final decisions rest solely with embassy authorities.', 'answer_ar' => 'لا. القرار النهائي يكون من الجهة المختصة بالسفارة، ودور Travel Wave هو مساعدتك في تجهيز ومراجعة ملفك بشكل منظم بأعلى دقة.', 'is_active' => true, 'sort_order' => 5],
                    ['question_en' => 'How Do I Know the Required Documents for My Case?', 'question_ar' => 'إزاي أعرف المستندات المطلوبة لحالتي؟', 'answer_en' => 'Reach out to us and our team will evaluate your status.', 'answer_ar' => 'تواصل معنا وفريق Travel Wave يراجع حالتك ويوضح لك المستندات والخطوات المناسبة.', 'is_active' => true, 'sort_order' => 6],
                ],
                'cta_title_en' => 'Ready to Start France Visa Steps?',
                'cta_title_ar' => 'جاهز تبدأ إجراءات فيزا فرنسا؟',
                'cta_text_en' => 'Leave your details and the Travel Wave team will contact you.',
                'cta_text_ar' => 'سيب بياناتك وفريق Travel Wave هيتواصل معاك، يفهم حالتك ويوضح لك الخطوات والمستندات المناسبة.',
                'cta_button_en' => 'Contact Us Now',
                'cta_button_ar' => 'تواصل معنا الآن',
                'cta_url' => '#visa-inquiry',
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
