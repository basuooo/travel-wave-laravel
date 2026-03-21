<?php

namespace Database\Seeders;

use App\Models\LeadForm;
use App\Models\MarketingLandingPage;
use App\Models\TrackingIntegration;
use Illuminate\Database\Seeder;

class MarketingLandingPageDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tracking = TrackingIntegration::query()->updateOrCreate(
            ['slug' => 'france-visa-meta-demo'],
            [
                'name' => 'France Visa Meta Demo',
                'integration_type' => TrackingIntegration::TYPE_META_PIXEL,
                'platform' => 'Meta Ads',
                'tracking_code' => '123456789012345',
                'placement' => 'standard',
                'visibility_mode' => 'all',
                'notes' => 'Demo Meta Pixel for the France visa campaign landing page.',
                'sort_order' => 20,
                'is_active' => false,
            ]
        );

        $form = LeadForm::query()->firstOrCreate(
            ['slug' => 'marketing-france-visa-demo-form'],
            [
                'name' => 'Marketing France Visa Demo Form',
                'form_category' => 'visa',
                'title_en' => 'Talk to Travel Wave About Your France Visa',
                'title_ar' => 'تواصل مع Travel Wave بخصوص تأشيرة فرنسا',
                'subtitle_en' => 'Send your details and our team will help with the next visa step.',
                'subtitle_ar' => 'أرسل بياناتك وسيقوم فريقنا بمساعدتك في الخطوة التالية للتأشيرة.',
                'submit_text_en' => 'Send Request',
                'submit_text_ar' => 'أرسل الطلب',
                'success_message_en' => 'Your request has been received successfully.',
                'success_message_ar' => 'تم استلام طلبك بنجاح.',
                'is_active' => true,
            ]
        );

        if ($form->fields()->count() === 0) {
            $form->fields()->createMany([
                ['field_key' => 'full_name', 'type' => 'text', 'label_en' => 'Full Name', 'label_ar' => 'الاسم الكامل', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 1],
                ['field_key' => 'phone', 'type' => 'phone', 'label_en' => 'Phone Number', 'label_ar' => 'رقم الهاتف', 'is_required' => true, 'is_enabled' => true, 'sort_order' => 2],
                ['field_key' => 'whatsapp_number', 'type' => 'text', 'label_en' => 'WhatsApp Number', 'label_ar' => 'رقم واتساب', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 3],
                ['field_key' => 'email', 'type' => 'email', 'label_en' => 'Email Address', 'label_ar' => 'البريد الإلكتروني', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 4],
                ['field_key' => 'service_type', 'type' => 'text', 'label_en' => 'Visa Type', 'label_ar' => 'نوع التأشيرة', 'default_value' => 'شنغن قصيرة الإقامة', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 5],
                ['field_key' => 'destination', 'type' => 'text', 'label_en' => 'Country', 'label_ar' => 'الدولة', 'default_value' => 'فرنسا', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 6],
                ['field_key' => 'travel_date', 'type' => 'date', 'label_en' => 'Travel Date', 'label_ar' => 'تاريخ السفر', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 7],
                ['field_key' => 'message', 'type' => 'textarea', 'label_en' => 'Your Message', 'label_ar' => 'رسالتك', 'is_required' => false, 'is_enabled' => true, 'sort_order' => 8],
            ]);
        }

        MarketingLandingPage::query()->updateOrCreate(
            ['slug' => 'france-visa-campaign-demo'],
            [
                'internal_name' => 'France Visa Campaign Demo',
                'title_en' => 'France Visa 2026',
                'title_ar' => 'تأشيرة فرنسا 2026',
                'campaign_name' => 'France Visa Lead Campaign',
                'ad_platform' => 'Meta Ads',
                'campaign_type' => 'Lead Generation',
                'traffic_source' => 'meta',
                'target_audience_note' => 'Arabic-speaking travelers interested in short-stay France visa support.',
                'status' => MarketingLandingPage::STATUS_PUBLISHED,
                'assigned_lead_form_id' => $form->id,
                'tracking_integration_ids' => [$tracking->id],
                'seo_title_en' => 'France Visa 2026 | Travel Wave Campaign',
                'seo_title_ar' => 'تأشيرة فرنسا 2026 | حملة Travel Wave',
                'seo_description_en' => 'France visa campaign landing page with a lead form and quick visa highlights.',
                'seo_description_ar' => 'صفحة هبوط لحملة تأشيرة فرنسا مع نموذج تواصل ومعلومات سريعة عن التأشيرة.',
                'utm_source' => 'meta',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'france_visa_2026',
                'utm_content' => 'creative_a',
                'utm_term' => null,
                'final_url' => route('marketing.landing-pages.show', 'france-visa-campaign-demo'),
                'notes' => 'Demo marketing landing page for testing the marketing workflow.',
                'sections' => [
                    'hero' => [
                        'enabled' => true,
                        'eyebrow_en' => 'Travel Wave Campaign',
                        'eyebrow_ar' => 'حملة Travel Wave',
                        'title_en' => 'France Visa 2026',
                        'title_ar' => 'تأشيرة فرنسا 2026',
                        'subtitle_en' => 'Get your France visa with a clearer process and practical support from Travel Wave.',
                        'subtitle_ar' => 'استخرج تأشيرتك بسهولة مع متابعة احترافية من Travel Wave',
                        'primary_button_text_en' => 'Start Now',
                        'primary_button_text_ar' => 'ابدأ الآن',
                        'primary_button_url' => '#marketing-form',
                        'secondary_button_text_en' => 'View Details',
                        'secondary_button_text_ar' => 'اعرض التفاصيل',
                        'secondary_button_url' => '#marketing-benefits',
                    ],
                    'benefits' => [
                        'enabled' => true,
                        'title_en' => 'Why Choose Travel Wave',
                        'title_ar' => 'لماذا تختار Travel Wave',
                        'subtitle_en' => 'A clearer service path for one of the most requested Schengen visa destinations.',
                        'subtitle_ar' => 'مسار أوضح للخدمة في واحدة من أكثر وجهات شنغن طلبًا.',
                        'items' => [
                            ['title_en' => 'Document Review', 'title_ar' => 'مراجعة المستندات', 'text_en' => 'We review the file before submission.', 'text_ar' => 'نراجع الملف قبل التقديم.', 'meta_en' => 'File readiness', 'meta_ar' => 'جاهزية الملف', 'sort_order' => 1, 'is_active' => true],
                            ['title_en' => 'File Follow-up', 'title_ar' => 'متابعة الملف', 'text_en' => 'We keep the process organized and clear.', 'text_ar' => 'نتابع الخطوات بشكل منظم وواضح.', 'meta_en' => 'Practical coordination', 'meta_ar' => 'تنسيق عملي', 'sort_order' => 2, 'is_active' => true],
                            ['title_en' => 'Full Support', 'title_ar' => 'دعم كامل حتى التقديم', 'text_en' => 'From the first inquiry to the next action.', 'text_ar' => 'من أول استفسار وحتى الخطوة التالية.', 'meta_en' => 'Step-by-step support', 'meta_ar' => 'دعم خطوة بخطوة', 'sort_order' => 3, 'is_active' => true],
                            ['title_en' => 'Fast Execution', 'title_ar' => 'سرعة في التنفيذ', 'text_en' => 'A faster workflow when the documents are ready.', 'text_ar' => 'مسار أسرع عند اكتمال المستندات.', 'meta_en' => 'Clear timeline', 'meta_ar' => 'جدول زمني أوضح', 'sort_order' => 4, 'is_active' => true],
                        ],
                    ],
                    'quick_info' => [
                        'enabled' => true,
                        'title_en' => 'Quick France Visa Highlights',
                        'title_ar' => 'معلومات سريعة عن تأشيرة فرنسا',
                        'subtitle_en' => 'Three points the campaign team can surface immediately for ad traffic.',
                        'subtitle_ar' => 'ثلاث نقاط أساسية تظهر مباشرة لزوار الحملة الإعلانية.',
                        'items' => [
                            ['label_en' => 'Visa Type', 'label_ar' => 'نوع التأشيرة', 'value_en' => 'Short-Stay Schengen', 'value_ar' => 'شنغن قصيرة الإقامة', 'sort_order' => 1, 'is_active' => true],
                            ['label_en' => 'Processing Time', 'label_ar' => 'مدة المعالجة', 'value_en' => '15 to 30 working days', 'value_ar' => '15 إلى 30 يوم عمل', 'sort_order' => 2, 'is_active' => true],
                            ['label_en' => 'Stay Duration', 'label_ar' => 'مدة الإقامة', 'value_en' => 'Up to 90 days', 'value_ar' => 'حتى 90 يومًا', 'sort_order' => 3, 'is_active' => true],
                        ],
                    ],
                    'testimonials' => [
                        'enabled' => false,
                        'items' => [],
                    ],
                    'faq' => [
                        'enabled' => true,
                        'title_en' => 'Common Questions',
                        'title_ar' => 'الأسئلة الشائعة',
                        'subtitle_en' => 'Useful answers for paid campaign visitors.',
                        'subtitle_ar' => 'إجابات سريعة ومهمة لزوار الحملات الإعلانية.',
                        'items' => [
                            ['question_en' => 'How long does the visa take?', 'question_ar' => 'ما مدة استخراج التأشيرة؟', 'answer_en' => 'Usually around 15 to 30 working days depending on season and file completeness.', 'answer_ar' => 'غالبًا من 15 إلى 30 يوم عمل حسب الموسم واكتمال الملف.', 'sort_order' => 1, 'is_active' => true],
                            ['question_en' => 'What documents are required?', 'question_ar' => 'ما الأوراق المطلوبة؟', 'answer_en' => 'Passport, photos, financial proof, booking details, and supporting travel documents.', 'answer_ar' => 'جواز السفر، الصور، الإثباتات المالية، تفاصيل الحجوزات، والمستندات الداعمة.', 'sort_order' => 2, 'is_active' => true],
                            ['question_en' => 'Do you provide support until submission?', 'question_ar' => 'هل يوجد دعم حتى التقديم؟', 'answer_en' => 'Yes, Travel Wave supports the file review and next practical steps until submission readiness.', 'answer_ar' => 'نعم، تقدم Travel Wave الدعم في مراجعة الملف والخطوات العملية حتى الاستعداد للتقديم.', 'sort_order' => 3, 'is_active' => true],
                        ],
                    ],
                    'cta' => [
                        'enabled' => true,
                        'title_en' => 'Start Your France Visa Request Today',
                        'title_ar' => 'ابدأ طلب تأشيرة فرنسا اليوم',
                        'description_en' => 'Use this page as a live marketing example for ad traffic, forms, and performance tracking.',
                        'description_ar' => 'استخدم هذه الصفحة كنموذج حي لفريق التسويق لتجربة الإعلانات والنماذج وتتبع الأداء.',
                        'primary_button_text_en' => 'Start Now',
                        'primary_button_text_ar' => 'ابدأ الآن',
                        'primary_button_url' => '#marketing-form',
                        'secondary_button_text_en' => 'Chat on WhatsApp',
                        'secondary_button_text_ar' => 'تواصل واتساب',
                        'secondary_button_url' => 'https://wa.me/201060500236?text=' . rawurlencode('مرحبًا، أريد الاستفسار عن خدمات Travel Wave'),
                    ],
                    'form' => [
                        'enabled' => true,
                        'title_en' => 'Request Your France Visa Callback',
                        'title_ar' => 'اطلب التواصل بخصوص تأشيرة فرنسا',
                        'subtitle_en' => 'A demo lead form connected to the new Marketing module.',
                        'subtitle_ar' => 'نموذج تجريبي مرتبط مباشرة بوحدة التسويق الجديدة.',
                    ],
                ],
            ]
        );
    }
}
