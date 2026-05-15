<?php

namespace Database\Seeders;

use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Database\Seeder;

class PolandVisaTemplateSeeder extends Seeder
{
    public function run()
    {
        $heroSlideOne = 'hero-slides/XDOtmN6qPyfvyZMihVB7ZmNHaMRwt0JImWpqFmdj.png';
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

        $country = VisaCountry::query()->updateOrCreate(
            ['slug' => 'poland-visa'],
            [
                'visa_category_id' => $category->id,
                'name_en' => 'Poland',
                'name_ar' => 'بولندا',
                'excerpt_en' => 'Poland visa is one of the requested Schengen visas for tourism, family visits, and selected business travel, usually allowing stays of up to 90 days within 180 days. Travel Wave helps applicants organize the file, review documents, align bookings, and understand the process, fees, and expected timeline more clearly.',
                'excerpt_ar' => 'دعم احترافي من Travel Wave لتجهيز تأشيرة بولندا شنغن ومراجعة الملف والحجوزات والمتابعة.',
                'hero_badge_en' => 'Schengen Visa Support',
                'hero_badge_ar' => 'دعم تأشيرة شنغن',
                'hero_title_en' => 'Poland Visa Services Built for Clarity and Confidence',
                'hero_title_ar' => 'خدمات تأشيرة بولندا المصممة بوضوح وثقة',
                'hero_subtitle_en' => 'From the first checklist to the final submission, Travel Wave helps travelers prepare a stronger Poland visa file with premium guidance.',
                'hero_subtitle_ar' => 'من أول قائمة مستندات حتى التقديم النهائي تساعدك Travel Wave على تجهيز ملف بولندا بشكل أوضح وأكثر احترافية.',
                'hero_cta_text_en' => 'Start Your Poland Visa Request',
                'hero_cta_text_ar' => 'ابدأ طلب تأشيرة بولندا',
                'hero_cta_url' => '#visa-inquiry',
                'hero_overlay_opacity' => 0.50,
                'hero_image' => $heroSlideTwo,
                'hero_mobile_image' => $heroSlideTwo,
                'flag_image' => 'visa-countries/poland-flag.svg',
                'overview_en' => 'Poland remains a requested Schengen destination for tourism, family visits, and business travel. This page is structured to give applicants a fast understanding of the visa type, the expected process, and how Travel Wave helps reduce confusion before submission.',
                'overview_ar' => 'تظل بولندا وجهة شنغن مطلوبة للسياحة والزيارات العائلية ورحلات الأعمال. تم تصميم هذه الصفحة لتمنح المتقدم فهما سريعا لنوع التأشيرة والخطوات المتوقعة وكيف تساعد Travel Wave في تقليل أي ارتباك قبل التقديم.',
                'visa_type_en' => 'Short-Stay Schengen Visa',
                'visa_type_ar' => 'تأشيرة شنغن قصيرة الإقامة',
                'stay_duration_en' => 'Up to 90 days within 180 days',
                'stay_duration_ar' => 'حتى 90 يوما خلال 180 يوما',
                'quick_summary_items' => [
                    ['title_en' => 'Visa Type', 'title_ar' => 'نوع التأشيرة', 'value_en' => 'Short-Stay Schengen', 'value_ar' => 'شنغن قصيرة الإقامة', 'icon' => 'VS', 'sort_order' => 1, 'is_active' => true],
                    ['title_en' => 'Processing Time', 'title_ar' => 'مدة المعالجة', 'value_en' => '15 to 30 working days', 'value_ar' => '15 إلى 30 يوم عمل', 'icon' => 'PT', 'sort_order' => 2, 'is_active' => true],
                    ['title_en' => 'Stay Duration', 'title_ar' => 'مدة الإقامة', 'value_en' => 'Up to 90 days', 'value_ar' => 'حتى 90 يوما', 'icon' => 'SD', 'sort_order' => 3, 'is_active' => true],
                    ['title_en' => 'Approx. Fees', 'title_ar' => 'الرسوم التقريبية', 'value_en' => 'Quoted after review', 'value_ar' => 'تحدد بعد المراجعة', 'icon' => 'FE', 'sort_order' => 4, 'is_active' => true],
                ],
                'intro_image' => $heroSlideOne,
                'introduction_title_en' => 'Poland Visa Overview',
                'introduction_title_ar' => 'نظرة عامة على تأشيرة بولندا',
                'introduction_badge_en' => 'Travel Wave Guided Support',
                'introduction_badge_ar' => 'دعم موجه من Travel Wave',
                'introduction_points' => [
                    ['text_en' => 'Clear file preparation before submission.', 'text_ar' => 'تجهيز واضح للملف قبل التقديم.'],
                    ['text_en' => 'Practical support for bookings and timing.', 'text_ar' => 'دعم عملي للحجوزات وتوقيت التقديم.'],
                    ['text_en' => 'Stronger document consistency for the declared travel purpose.', 'text_ar' => 'اتساق أفضل للمستندات مع غرض السفر المعلن.'],
                    ['text_en' => 'Better visibility on next steps after every stage.', 'text_ar' => 'وضوح أكبر للخطوة التالية بعد كل مرحلة.'],
                ],
                'detailed_title_en' => 'Detailed Visa Explanation',
                'detailed_title_ar' => 'شرح التأشيرة بالتفصيل',
                'detailed_description_en' => "The Poland visa usually falls under the Schengen short-stay category for travelers planning tourism, family visits, or selected business trips.\n\nApplicants should prepare a clear travel purpose, financially consistent documents, and booking evidence that supports the itinerary.\n\nBefore applying, it is important to check passport validity, make sure the file matches the declared purpose of travel, and avoid inconsistent dates across hotel bookings, flight reservations, and insurance coverage.",
                'detailed_description_ar' => "غالبا ما تندرج تأشيرة بولندا ضمن تأشيرات شنغن قصيرة الإقامة للمسافرين بغرض السياحة أو الزيارة العائلية أو بعض رحلات الأعمال.\n\nيجب على المتقدم تجهيز غرض سفر واضح ومستندات مالية متناسقة وحجوزات تدعم خط السير المقدم.\n\nقبل التقديم من المهم التأكد من صلاحية جواز السفر وأن الملف متوافق مع سبب السفر المعلن وأن تواريخ الحجز والطيران والتأمين متطابقة.",
                'highlights' => [
                    ['text_en' => 'Suitable for tourism, family visits, and selected business travel.', 'text_ar' => 'مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.'],
                    ['text_en' => 'Organized files usually make embassy review smoother and clearer.', 'text_ar' => 'الملفات المنظمة تجعل مراجعة الطلب أكثر سلاسة ووضوحا.'],
                    ['text_en' => 'Early preparation helps with appointments and peak-season pressure.', 'text_ar' => 'التحضير المبكر يساعد في المواعيد وتجنب ضغط المواسم.'],
                ],
                'why_choose_title_en' => 'Why Choose Travel Wave',
                'why_choose_title_ar' => 'لماذا تختار Travel Wave',
                'why_choose_intro_en' => 'A premium support experience built to make the Poland visa process clearer, more organized, and easier to follow.',
                'why_choose_intro_ar' => 'تجربة دعم احترافية تجعل مسار تأشيرة بولندا أوضح وأكثر تنظيما وأسهل في المتابعة.',
                'why_choose_items' => [
                    ['title_en' => 'Professional Document Review', 'title_ar' => 'مراجعة احترافية للمستندات', 'description_en' => 'We check the file structure and highlight missing items before the appointment date.', 'description_ar' => 'نراجع ترتيب الملف ونوضح النواقص قبل موعد التقديم.', 'icon' => 'shield', 'sort_order' => 1, 'is_active' => true],
                    ['title_en' => 'Organized File Preparation', 'title_ar' => 'تنظيم الملف بشكل واضح', 'description_en' => 'Your supporting papers are arranged in a cleaner order that is easier to understand and present.', 'description_ar' => 'يتم ترتيب المستندات بشكل أوضح وأسهل للفهم والتقديم.', 'icon' => 'file', 'sort_order' => 2, 'is_active' => true],
                    ['title_en' => 'Booking Support', 'title_ar' => 'دعم الحجوزات', 'description_en' => 'We help align hotel, flight, and insurance details with the intended travel plan.', 'description_ar' => 'نساعد في تنسيق تفاصيل الفندق والطيران والتأمين مع خطة السفر.', 'icon' => 'calendar', 'sort_order' => 3, 'is_active' => true],
                    ['title_en' => 'Step-by-Step Follow-Up', 'title_ar' => 'متابعة خطوة بخطوة', 'description_en' => 'Applicants know what comes next at every stage instead of guessing the process.', 'description_ar' => 'يعرف المتقدم ما هي الخطوة التالية في كل مرحلة دون تخمين.', 'icon' => 'support', 'sort_order' => 4, 'is_active' => true],
                ],
                'documents_title_en' => 'Required Documents',
                'documents_title_ar' => 'المستندات المطلوبة',
                'documents_subtitle_en' => 'The exact file may vary by profile, but these are the most common documents requested for Poland tourist visa preparation.',
                'documents_subtitle_ar' => 'قد يختلف الملف بحسب حالة المتقدم، لكن هذه هي المستندات الأكثر شيوعا لتجهيز تأشيرة بولندا السياحية.',
                'document_items' => [
                    ['name_en' => 'Valid Passport', 'name_ar' => 'جواز سفر ساري', 'description_en' => 'Passport should cover the required validity period and include usable pages.', 'description_ar' => 'يجب أن يغطي جواز السفر مدة الصلاحية المطلوبة وأن يحتوي على صفحات متاحة.', 'sort_order' => 1, 'is_active' => true],
                    ['name_en' => 'Recent Personal Photos', 'name_ar' => 'صور شخصية حديثة', 'description_en' => 'Photos should match embassy size and background requirements.', 'description_ar' => 'يجب أن تطابق الصور مقاسات ومتطلبات السفارة.', 'sort_order' => 2, 'is_active' => true],
                    ['name_en' => 'Bank Statement', 'name_ar' => 'كشف حساب بنكي', 'description_en' => 'Financial movement should support the proposed trip timing and cost level.', 'description_ar' => 'يجب أن يدعم كشف الحساب توقيت الرحلة ومستوى التكلفة المقترح.', 'sort_order' => 3, 'is_active' => true],
                    ['name_en' => 'Employment or Study Proof', 'name_ar' => 'إثبات عمل أو دراسة', 'description_en' => 'An employment letter or equivalent proof strengthens the purpose and return intention.', 'description_ar' => 'خطاب العمل أو ما يعادله يدعم سبب السفر ونية العودة.', 'sort_order' => 4, 'is_active' => true],
                    ['name_en' => 'Hotel and Flight Reservations', 'name_ar' => 'حجوزات الفندق والطيران', 'description_en' => 'Reservation dates should match the travel plan and visa request window.', 'description_ar' => 'يجب أن تتوافق تواريخ الحجز مع خطة السفر وفترة الطلب.', 'sort_order' => 5, 'is_active' => true],
                    ['name_en' => 'Travel Insurance', 'name_ar' => 'تأمين السفر', 'description_en' => 'Insurance should meet Schengen coverage requirements for the full stay period.', 'description_ar' => 'يجب أن يحقق التأمين متطلبات شنغن طوال فترة الإقامة.', 'sort_order' => 6, 'is_active' => true],
                ],
                'steps_title_en' => 'Application Steps',
                'steps_title_ar' => 'خطوات التقديم',
                'step_items' => [
                    ['title_en' => 'Submit Your Details', 'title_ar' => 'أرسل بياناتك', 'description_en' => 'Share your travel purpose, timing, and basic profile so we can assess the file direction.', 'description_ar' => 'شارك سبب السفر وتوقيته وبياناتك الأساسية لتحديد اتجاه الملف.', 'step_number' => 1, 'sort_order' => 1, 'is_active' => true],
                    ['title_en' => 'Review the File', 'title_ar' => 'مراجعة الملف', 'description_en' => 'Travel Wave reviews what is available and points out what still needs improvement.', 'description_ar' => 'تراجع Travel Wave الملف الحالي وتوضح ما يحتاج إلى استكمال أو تحسين.', 'step_number' => 2, 'sort_order' => 2, 'is_active' => true],
                    ['title_en' => 'Prepare Documents', 'title_ar' => 'تجهيز المستندات', 'description_en' => 'Supporting papers are completed and aligned with the travel purpose and booking dates.', 'description_ar' => 'يتم استكمال المستندات ومطابقتها مع غرض السفر وتواريخ الحجوزات.', 'step_number' => 3, 'sort_order' => 3, 'is_active' => true],
                    ['title_en' => 'Booking and Follow-Up', 'title_ar' => 'الحجوزات والمتابعة', 'description_en' => 'Hotel, flight, and insurance details are coordinated before the appointment stage.', 'description_ar' => 'يتم تنسيق تفاصيل الفندق والطيران والتأمين قبل مرحلة الموعد.', 'step_number' => 4, 'sort_order' => 4, 'is_active' => true],
                    ['title_en' => 'Application Submission', 'title_ar' => 'إتمام التقديم', 'description_en' => 'You attend the submission and biometric step, then continue tracking the request.', 'description_ar' => 'تقوم بالتقديم والبصمة ثم تتابع حالة الطلب بعد ذلك.', 'step_number' => 5, 'sort_order' => 5, 'is_active' => true],
                ],
                'fees_title_en' => 'Fees and Processing Time',
                'fees_title_ar' => 'الرسوم ومدة المعالجة',
                'fee_items' => [
                    ['label_en' => 'Embassy Fee', 'label_ar' => 'رسوم السفارة', 'value_en' => 'Varies by traveler type', 'value_ar' => 'تختلف حسب نوع المسافر', 'sort_order' => 1, 'is_active' => true],
                    ['label_en' => 'Visa Center Fee', 'label_ar' => 'رسوم مركز التأشيرات', 'value_en' => 'Additional service charge', 'value_ar' => 'رسوم خدمة إضافية', 'sort_order' => 2, 'is_active' => true],
                    ['label_en' => 'Travel Wave Service Fee', 'label_ar' => 'رسوم خدمة Travel Wave', 'value_en' => 'Quoted after file review', 'value_ar' => 'يتم تحديدها بعد مراجعة الملف', 'sort_order' => 3, 'is_active' => true],
                    ['label_en' => 'Processing Time', 'label_ar' => 'مدة المعالجة', 'value_en' => 'Usually 15 to 30 working days', 'value_ar' => 'عادة من 15 إلى 30 يوم عمل', 'sort_order' => 4, 'is_active' => true],
                ],
                'processing_time_en' => 'Estimated processing is usually around 15 to 30 working days, depending on seasonality, embassy load, and file completeness.',
                'processing_time_ar' => 'تتراوح مدة المعالجة غالبا بين 15 و30 يوم عمل بحسب الموسم وضغط السفارة ومدى اكتمال الملف.',
                'fees_en' => 'Approximate fees depend on embassy, visa center, and service support charges.',
                'fees_ar' => 'تعتمد الرسوم التقريبية على رسوم السفارة ومركز التأشيرات ورسوم الخدمة.',
                'fees_notes_en' => 'Final pricing may change according to embassy updates, traveler age, or additional service needs. Travel Wave confirms the latest estimate before submission.',
                'fees_notes_ar' => 'قد تختلف التكلفة النهائية بحسب تحديثات السفارة أو عمر المسافر أو الخدمات الإضافية. تؤكد Travel Wave التقدير الأحدث قبل التقديم.',
                'faq_title_en' => 'Frequently Asked Questions',
                'faq_title_ar' => 'الأسئلة الشائعة',
                'support_title_en' => 'Need Help Before You Apply?',
                'support_title_ar' => 'تحتاج إلى مساعدة قبل التقديم؟',
                'support_subtitle_en' => 'Talk to Travel Wave and get practical guidance on documents, bookings, and the best next step for your Poland visa file.',
                'support_subtitle_ar' => 'تحدث مع Travel Wave واحصل على إرشاد عملي بخصوص المستندات والحجوزات وأفضل خطوة تالية لملف تأشيرة بولندا.',
                'support_button_en' => 'Speak to an Advisor',
                'support_button_ar' => 'تحدث مع مستشار',
                'support_button_link' => '#visa-inquiry',
                'support_is_active' => true,
                'faqs' => [
                    ['question_en' => 'Is Poland visa considered a Schengen visa?', 'question_ar' => 'هل تأشيرة بولندا تعتبر تأشيرة شنغن؟', 'answer_en' => 'Yes. In most travel cases, the Poland short-stay visa is processed under Schengen rules.', 'answer_ar' => 'نعم. في أغلب حالات السفر تتم معالجة تأشيرة بولندا قصيرة الإقامة ضمن نظام شنغن.', 'sort_order' => 1, 'is_active' => true],
                    ['question_en' => 'How long does processing usually take?', 'question_ar' => 'كم تستغرق المعالجة عادة؟', 'answer_en' => 'It often ranges from 15 to 30 working days, but seasonal pressure can affect timelines.', 'answer_ar' => 'غالبا ما تتراوح من 15 إلى 30 يوم عمل وقد تتأثر بالمواسم وضغط الطلبات.', 'sort_order' => 2, 'is_active' => true],
                    ['question_en' => 'Is biometric attendance required?', 'question_ar' => 'هل الحضور للبصمة مطلوب؟', 'answer_en' => 'In many cases yes, depending on prior Schengen biometric history and current requirements.', 'answer_ar' => 'في كثير من الحالات نعم بحسب سجل البصمة السابق ومتطلبات التقديم الحالية.', 'sort_order' => 3, 'is_active' => true],
                    ['question_en' => 'When should I apply before travel?', 'question_ar' => 'متى يجب أن أبدأ التقديم قبل السفر؟', 'answer_en' => 'Starting early is usually the safer option, especially before busy travel seasons.', 'answer_ar' => 'يفضل البدء مبكرا خاصة قبل مواسم السفر المزدحمة.', 'sort_order' => 4, 'is_active' => true],
                ],
                'map_title_en' => 'Office and Visa Support Location',
                'map_title_ar' => 'موقع المكتب ودعم التأشيرات',
                'map_description_en' => 'Use the map section to display your office, embassy, or visa center reference point for applicants.',
                'map_description_ar' => 'يمكن استخدام هذا القسم لعرض موقع المكتب أو السفارة أو مركز التأشيرات كمرجع للمتقدمين.',
                'map_embed_code' => '<iframe src="https://www.google.com/maps?q=Warsaw%20Poland&output=embed" width="100%" height="420" style="border:0;" loading="lazy"></iframe>',
                'map_is_active' => true,
                'inquiry_form_title_en' => 'Talk to Travel Wave About Your Poland Visa',
                'inquiry_form_label_en' => 'Contact Us',
                'inquiry_form_label_ar' => 'تواصل معنا',
                'inquiry_form_title_ar' => 'تواصل مع Travel Wave بخصوص تأشيرة بولندا',
                'inquiry_form_subtitle_en' => 'Send your details and our team will guide you on eligibility, documents, and the next practical step.',
                'inquiry_form_subtitle_ar' => 'أرسل بياناتك وسيرشدك فريقنا بخصوص الأهلية والمستندات والخطوة العملية التالية.',
                'inquiry_form_button_en' => 'Send Poland Visa Inquiry',
                'inquiry_form_button_ar' => 'أرسل استفسار تأشيرة بولندا',
                'inquiry_form_success_en' => 'Your Poland visa inquiry has been received. A Travel Wave advisor will contact you shortly.',
                'inquiry_form_success_ar' => 'تم استلام استفسارك الخاص بتأشيرة بولندا وسيتواصل معك أحد مستشاري Travel Wave قريبا.',
                'inquiry_form_default_service_type' => 'Poland Visa',
                'inquiry_form_visible_fields' => ['full_name', 'phone', 'whatsapp_number', 'email', 'service_type', 'destination', 'travel_date', 'message'],
                'inquiry_form_is_active' => true,
                'cta_title_en' => 'Ready to Start Your Poland Visa File?',
                'cta_title_ar' => 'جاهز لبدء ملف تأشيرة بولندا؟',
                'cta_text_en' => 'Let Travel Wave turn a complex visa process into a more organized, readable, and confidence-building journey.',
                'cta_text_ar' => 'دع Travel Wave تحول خطوات التأشيرة المعقدة إلى رحلة أكثر تنظيما ووضوحا وثقة.',
                'cta_button_en' => 'Apply with Travel Wave',
                'cta_button_ar' => 'قدّم مع Travel Wave',
                'cta_url' => '#visa-inquiry',
                'final_cta_background_image' => $heroSlideOne,
                'final_cta_is_active' => true,
                'meta_title_en' => 'Poland Visa Services | Travel Wave',
                'meta_title_ar' => 'خدمات تأشيرة بولندا | Travel Wave',
                'meta_description_en' => 'Explore Travel Wave Poland visa support, required documents, steps, fees, FAQs, and inquiry options in a premium reusable visa template.',
                'meta_description_ar' => 'اكتشف خدمات Travel Wave لتأشيرة بولندا والمستندات والخطوات والرسوم والأسئلة الشائعة ونموذج الاستفسار في قالب تأشيرات قابل لإعادة الاستخدام.',
                'og_image' => $heroSlideTwo,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        $country->update([
            'excerpt_en' => "- Poland visa usually falls under the short-stay Schengen category.\n- Suitable for tourism, family visits, and selected business travel.\n- It usually allows stays of up to 90 days within 180 days.\n- Processing often takes around 15 to 30 working days depending on season and file completeness.\n- Travel Wave helps review documents, align bookings, and organize the file more clearly.",
            'excerpt_ar' => 'تأشيرة بولندا من تأشيرات شنغن المطلوبة للسياحة والزيارات العائلية وبعض رحلات الأعمال، وتسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا. تساعدك Travel Wave في تجهيز الملف بشكل منظم، ومراجعة المستندات، وتنسيق الحجوزات، وشرح خطوات التقديم والرسوم والمدة المتوقعة للمعالجة بطريقة أوضح وأسهل.',
        ]);
        $country->update([
            'excerpt_ar' => "- تأشيرة بولندا تندرج غالبًا ضمن شنغن قصيرة الإقامة.\n- مناسبة للسياحة والزيارات العائلية وبعض رحلات الأعمال.\n- تسمح عادة بإقامة تصل إلى 90 يومًا خلال 180 يومًا.\n- مدة المعالجة غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.\n- تساعدك Travel Wave في مراجعة المستندات وتنظيم الحجوزات وتجهيز الملف بشكل أوضح وأكثر احترافية.",
        ]);

        \App\Models\HomeCountryStripItem::query()->updateOrCreate(
            ['name_en' => 'Poland'],
            [
                'visa_country_id' => $country->id,
                'name_ar' => 'بولندا',
                'image_path' => $country->flag_image ?: 'visa-countries/poland-flag.svg',
                'sort_order' => 2,
                'is_active' => true,
                'show_on_homepage' => true,
            ]
        );
    }
}
