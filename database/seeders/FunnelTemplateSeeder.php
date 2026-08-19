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
            [
                'name' => 'Schengen Visa Eligibility',
                'slug' => 'schengen-visa-eligibility',
                'category' => 'Travel',
                'description' => 'Interactive step-by-step qualification checker for Schengen visa applicants.',
                'thumbnail' => '/assets/images/templates/schengen.jpg',
                'sort_order' => 1,
                'schema_data' => [
                    'design_settings' => [
                        'primary_color' => '#1e40af',
                        'font_family' => 'System',
                        'button_style' => 'rounded-lg',
                    ],
                    'crm_settings' => [
                        'enabled' => true,
                    ],
                    'steps' => [
                        [
                            'title' => 'اختبار الجاهزية للتأشيرة الشنغن',
                            'subtitle' => 'اكتشف نسبة قبول طلبك قبل التقديم خلال دقيقتين',
                            'step_type' => 'welcome',
                            'elements' => [
                                [
                                    'element_type' => 'heading',
                                    'label' => 'هل أنت جاهز للحصول على تأشيرة الشنغن؟',
                                    'properties' => ['font_size' => '2xl'],
                                ],
                                [
                                    'element_type' => 'text',
                                    'label' => 'أجب عن بضعة أسئلة سريعة لمعرفة نسبة قبول ملفك والتوصيات المناسبة.',
                                ],
                                [
                                    'element_type' => 'button',
                                    'label' => 'ابدأ الفحص الآن 🚀',
                                ],
                            ],
                        ],
                        [
                            'title' => 'الوجهة المطلوبة',
                            'subtitle' => 'اختر الدولة التي ترغب في السفر إليها',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هي وجهتك الأوروبية الأساسية؟',
                                    'question_key' => 'destination_country',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'إسبانيا 🇪🇸', 'value' => 'Spain', 'score' => 15],
                                            ['label' => 'فرنسا 🇫🇷', 'value' => 'France', 'score' => 15],
                                            ['label' => 'إيطاليا 🇮🇹', 'value' => 'Italy', 'score' => 15],
                                            ['label' => 'ألمانيا 🇩🇪', 'value' => 'Germany', 'score' => 10],
                                            ['label' => 'دولة أخرى في الشنغن', 'value' => 'Other', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الحساب البنكي',
                            'subtitle' => 'شروط الحساب البنكي للتأشيرة',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل لديك حساب بنكي باسمك وتتحرك فيه كشوفات حساب؟',
                                    'question_key' => 'bank_account',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، كشف حساب فعال', 'value' => 'Yes', 'score' => 25],
                                            ['label' => 'لا يوجد حساب بنكي حاليًا', 'value' => 'No', 'score' => 0],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'عمر الحساب ورصيد الحساب',
                            'subtitle' => 'الرصيد التقديري والحركة المالية',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو متوسط الرصيد المالي بالحساب خلال آخر 6 أشهر؟',
                                    'question_key' => 'bank_balance',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'أكثر من 150,000 ريال / جنيه', 'value' => 'Over 150k', 'score' => 30],
                                            ['label' => 'من 70,000 إلى 150,000', 'value' => '70k to 150k', 'score' => 20],
                                            ['label' => 'أقل من 70,000', 'value' => 'Under 70k', 'score' => 10],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'الوضع الوظيفي',
                            'subtitle' => 'إثبات المهنة أو العمل',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'ما هو وضعك الوظيفي الحالي؟',
                                    'question_key' => 'employment_status',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'موظف قطاع حكومي أو خاص مع تعريف راتب', 'value' => 'Employed', 'score' => 20],
                                            ['label' => 'صاحب عمل / سجل تجاري', 'value' => 'Business Owner', 'score' => 20],
                                            ['label' => 'طالب / ربة منزل (وجود ضامن)', 'value' => 'Sponsored', 'score' => 10],
                                            ['label' => 'غير عمل حاليًا', 'value' => 'Unemployed', 'score' => 0],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'السجل التراكمي للسفر',
                            'subtitle' => 'تاريخ السفر السابق',
                            'step_type' => 'question',
                            'elements' => [
                                [
                                    'element_type' => 'single_choice',
                                    'label' => 'هل سافرت سابقًا إلى دول الشنغن أو أمريكا / بريطانيا؟',
                                    'question_key' => 'travel_history',
                                    'properties' => [
                                        'options' => [
                                            ['label' => 'نعم، حصلت على شنغن أو تأشيرات قوية سابقًا', 'value' => 'Previous Schengen', 'score' => 10],
                                            ['label' => 'سافرت دول بدون تأشيرة / دول عربية فقط', 'value' => 'Arab Countries Only', 'score' => 5],
                                            ['label' => 'جواز جديد لم يسافر بعد', 'value' => 'First Time', 'score' => 0],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        [
                            'title' => 'بيانات التواصل للحصول على التقرير التفصيلي',
                            'subtitle' => 'ادخل بياناتك لاستلام نتيجة تقييم الملف مجانًا',
                            'step_type' => 'lead_form',
                            'elements' => [
                                [
                                    'element_type' => 'contact_form',
                                    'label' => 'نموذج التواصل',
                                    'properties' => [
                                        'fields' => [
                                            ['name' => 'full_name', 'label' => 'الاسم الكامل', 'type' => 'text', 'required' => true],
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
                            'title' => '🎉 مؤهل بنسبة عالية جداً (أكثر من 80%)',
                            'description' => 'ملفك قوي جداً ومستوفي لشروط الشنغن الأساسية. فرصتك ممتازة للحصول على التأشيرة بسهولة.',
                            'min_score' => 80,
                            'max_score' => 100,
                            'cta_label' => 'تواصل معنا الآن عبر الواتساب لحجز الموعد',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                        ],
                        [
                            'title' => '👍 مؤهل وتستطيع التقديم (60% - 79%)',
                            'description' => 'ملفك جيد ولكن ينقصه بعض التجهيزات البسيطة لتقوية نسبة القبول ولتفادي أي رفض.',
                            'min_score' => 60,
                            'max_score' => 79,
                            'cta_label' => 'تحدث مع مستشار التأشيرات الآن',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                        ],
                        [
                            'title' => '⚠️ ملفك يحتاج دراسة وتعديل قبل التقديم',
                            'description' => 'نسبة القبول الحالية متوسطة. ننصح باستشارة خبيرنا لتجهيز الضامن أو الحساب البنكي بالشكل الصحيح.',
                            'min_score' => 0,
                            'max_score' => 59,
                            'cta_label' => 'احصل على استشارة تقوية الملف',
                            'cta_type' => 'whatsapp',
                            'cta_whatsapp_number' => '966500000000',
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Visa Qualification Quiz',
                'slug' => 'visa-qualification-quiz',
                'category' => 'Qualification',
                'description' => 'Quick general travel visa qualification questionnaire.',
                'thumbnail' => '/assets/images/templates/visa-qual.jpg',
                'sort_order' => 2,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#0d9488'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
            [
                'name' => 'Which Visa Is Right For You?',
                'slug' => 'which-visa-is-right-for-you',
                'category' => 'Recommendation',
                'description' => 'Personalized recommendation quiz helping travelers pick the right country & visa type.',
                'thumbnail' => '/assets/images/templates/rec.jpg',
                'sort_order' => 3,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#7c3aed'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
            [
                'name' => 'Travel Destination Finder',
                'slug' => 'travel-destination-finder',
                'category' => 'Travel',
                'description' => 'Discover the best tourism destinations matching your budget & trip style.',
                'thumbnail' => '/assets/images/templates/dest.jpg',
                'sort_order' => 4,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#ea580c'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
            [
                'name' => 'Visa Readiness Assessment',
                'slug' => 'visa-readiness-assessment',
                'category' => 'Assessment',
                'description' => 'Complete document checklist assessment before booking your Embassy appointment.',
                'thumbnail' => '/assets/images/templates/readiness.jpg',
                'sort_order' => 5,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#0284c7'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
            [
                'name' => 'Travel Budget Calculator',
                'slug' => 'travel-budget-calculator',
                'category' => 'Calculator',
                'description' => 'Estimate overall flight, hotel, visa, and daily expenses for your upcoming trip.',
                'thumbnail' => '/assets/images/templates/calc.jpg',
                'sort_order' => 6,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#16a34a'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
            [
                'name' => 'Lead Generation Quiz',
                'slug' => 'lead-generation-quiz',
                'category' => 'Lead Generation',
                'description' => 'High-converting interactive lead generator quiz template.',
                'thumbnail' => '/assets/images/templates/lead-gen.jpg',
                'sort_order' => 7,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#db2777'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
            [
                'name' => 'WhatsApp Qualification Funnel',
                'slug' => 'whatsapp-qualification-funnel',
                'category' => 'Qualification',
                'description' => 'Ultra-fast qualifying funnel driving high-intent leads straight to WhatsApp.',
                'thumbnail' => '/assets/images/templates/wa.jpg',
                'sort_order' => 8,
                'schema_data' => [
                    'design_settings' => ['primary_color' => '#22c55e'],
                    'steps' => [],
                    'results' => [],
                ],
            ],
        ];

        foreach ($templates as $tpl) {
            FunnelTemplate::updateOrInsert(
                ['slug' => $tpl['slug']],
                [
                    'name' => $tpl['name'],
                    'category' => $tpl['category'],
                    'description' => $tpl['description'],
                    'thumbnail' => $tpl['thumbnail'],
                    'schema_data' => json_encode($tpl['schema_data']),
                    'is_active' => true,
                    'sort_order' => $tpl['sort_order'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
