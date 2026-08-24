<?php

namespace Database\Seeders;

use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Models\VisaRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VisaDatabaseSeeder extends Seeder
{
    private function normalizeArabic(string $text): string
    {
        $text = preg_replace('/[\x{064B}-\x{0652}\x{0670}]/u', '', $text);
        return preg_replace('/[أإآ]/u', 'ا', $text);
    }

    public function run(): void
    {
        VisaCountry::ensureTableSchema();

        $categoriesData = [
            ['name_ar' => 'الاتحاد الأوروبي (European Union)', 'name_en' => 'European Union', 'slug' => 'eu', 'sort_order' => 1],
            ['name_ar' => 'دول شنغن خارج الاتحاد الأوروبي', 'name_en' => 'Schengen Non-EU', 'slug' => 'schengen-non-eu', 'sort_order' => 2],
            ['name_ar' => 'أوروبا', 'name_en' => 'Europe', 'slug' => 'europe', 'sort_order' => 3],
            ['name_ar' => 'آسيا', 'name_en' => 'Asia', 'slug' => 'asia', 'sort_order' => 4],
            ['name_ar' => 'الشرق الأوسط', 'name_en' => 'Middle East', 'slug' => 'middle-east', 'sort_order' => 5],
            ['name_ar' => 'أفريقيا', 'name_en' => 'Africa', 'slug' => 'africa', 'sort_order' => 6],
            ['name_ar' => 'أمريكا الشمالية', 'name_en' => 'North America', 'slug' => 'north-america', 'sort_order' => 7],
            ['name_ar' => 'أمريكا الجنوبية', 'name_en' => 'South America', 'slug' => 'south-america', 'sort_order' => 8],
            ['name_ar' => 'أمريكا الوسطى', 'name_en' => 'Central America', 'slug' => 'central-america', 'sort_order' => 9],
            ['name_ar' => 'الكاريبي', 'name_en' => 'Caribbean', 'slug' => 'caribbean', 'sort_order' => 10],
            ['name_ar' => 'أوقيانوسيا', 'name_en' => 'Oceania', 'slug' => 'oceania', 'sort_order' => 11],
            ['name_ar' => 'دول الخليج', 'name_en' => 'GCC', 'slug' => 'gcc', 'sort_order' => 12],
            ['name_ar' => 'دول عربية', 'name_en' => 'Arab Countries', 'slug' => 'arab-countries', 'sort_order' => 13],
            ['name_ar' => 'دول رابطة الدول المستقلة (CIS)', 'name_en' => 'CIS Countries', 'slug' => 'cis', 'sort_order' => 14],
            ['name_ar' => 'دول أخرى', 'name_en' => 'Other Countries', 'slug' => 'other-countries', 'sort_order' => 15],
        ];

        $categoriesMap = [];
        foreach ($categoriesData as $cat) {
            $category = VisaCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name_ar' => $this->normalizeArabic($cat['name_ar']),
                    'name_en' => $cat['name_en'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => true,
                ]
            );
            $categoriesMap[$cat['slug']] = $category->id;
        }

        $standardEuDocs = "## الأوراق الأساسية\n"
            . "- كشف حساب بنكي لآخر 6 شهور، موضح به عمليات السحب والإيداع.\n"
            . "- إثبات الحالة الوظيفية:\n"
            . "  - السجل التجاري والبطاقة الضريبية لأصحاب الأعمال.\n"
            . "  - HR Letter للموظفين.\n"
            . "  - برنت معاشات في حالة المتقاعد.\n"
            . "- شهادة تحركات، خاصةً في حالة التقديم على الشنغن لأول مرة.\n"
            . "- قيد عائلي في حالة المتزوج.\n"
            . "- قيد فردي في حالة الأعزب.\n\n"
            . "## الأطفال والطلاب\n"
            . "- شهادة الميلاد.\n"
            . "- إثبات قيد من المدرسة أو الجامعة.\n"
            . "- في حالة بلوغ سن 18 سنة، يتم تقديم قيد فردي.\n"
            . "- شهادة تحركات.\n"
            . "- موافقة ولي الأمر، حسب الحالة.\n\n"
            . "## ملاحظة عامة\n"
            . "بنبدأ في تجهيز الأوراق بعد حجز موعد السفارة، وحضرتك في البداية مطلوب فقط إرسال صورة واضحة من الباسبور لبدء إجراءات حجز الموعد.";

        $franceBelgiumDocs = "## أوراق فرنسا وبلجيكا\n"
            . "- كشف حساب بنكي لآخر 6 شهور، موضح به عمليات السحب والإيداع.\n"
            . "- إثبات الحالة الوظيفية:\n"
            . "  - السجل التجاري والبطاقة الضريبية لأصحاب الأعمال.\n"
            . "  - HR Letter للموظفين.\n"
            . "  - برنت معاشات في حالة المتقاعد.\n\n"
            . "## الأطفال والطلاب\n"
            . "- شهادة الميلاد.\n"
            . "- إثبات قيد من المدرسة أو الجامعة.\n"
            . "- موافقة ولي الأمر، حسب الحالة.\n\n"
            . "## الملاحظة\n"
            . "بنبدأ في تجهيز الأوراق بعد حجز موعد السفارة، وحضرتك في البداية مطلوب فقط إرسال صورة واضحة من الباسبور لبدء إجراءات حجز الموعد.";

        $euCountries = [
            ['name_ar' => 'فرنسا', 'name_en' => 'France', 'slug' => 'france', 'center' => ['TLS'], 'docs' => $franceBelgiumDocs],
            ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'slug' => 'belgium', 'center' => ['VFS'], 'docs' => $franceBelgiumDocs],
            ['name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'slug' => 'italy', 'price' => 9500, 'center' => ['Almaviva / المافيفا'], 'notes' => 'يتم عمل Video Call مع العميل لأخذ / تأكيد الميعاد. المقابلة مطلوبة: أسئلة عن البرنامج السياحي.'],
            ['name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'slug' => 'germany', 'center' => ['VFS']],
            ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece', 'center' => ['VFS']],
            ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'slug' => 'netherlands', 'center' => ['VFS']],
            ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'slug' => 'austria', 'center' => ['VFS']],
            ['name_ar' => 'جمهورية التشيك', 'name_en' => 'Czech Republic', 'slug' => 'czech-republic', 'center' => ['VFS']],
            ['name_ar' => 'الدنمارك', 'name_en' => 'Denmark', 'slug' => 'denmark', 'center' => ['VFS']],
            ['name_ar' => 'المجر', 'name_en' => 'Hungary', 'slug' => 'hungary', 'center' => ['VFS']],
            ['name_ar' => 'بولندا', 'name_en' => 'Poland', 'slug' => 'poland', 'center' => ['VFS']],
            ['name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'slug' => 'portugal', 'center' => ['VFS']],
            ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'slug' => 'sweden', 'center' => ['VFS']],
            ['name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'slug' => 'croatia', 'center' => ['VFS']],
            ['name_ar' => 'قبرص', 'name_en' => 'Cyprus', 'slug' => 'cyprus', 'center' => ['VFS']],
            ['name_ar' => 'فنلندا', 'name_en' => 'Finland', 'slug' => 'finland', 'center' => ['VFS']],
            ['name_ar' => 'مالطا', 'name_en' => 'Malta', 'slug' => 'malta', 'center' => ['VFS']],
            ['name_ar' => 'رومانيا', 'name_en' => 'Romania', 'slug' => 'romania', 'center' => ['VFS']],
            ['name_ar' => 'بلغاريا', 'name_en' => 'Bulgaria', 'slug' => 'bulgaria', 'center' => ['VFS']],
            ['name_ar' => 'إستونيا', 'name_en' => 'Estonia', 'slug' => 'estonia', 'center' => ['VFS']],
            ['name_ar' => 'لاتفيا', 'name_en' => 'Latvia', 'slug' => 'latvia', 'center' => ['VFS']],
            ['name_ar' => 'ليتوانيا', 'name_en' => 'Lithuania', 'slug' => 'lithuania', 'center' => ['VFS']],
            ['name_ar' => 'لوكسمبورغ', 'name_en' => 'Luxembourg', 'slug' => 'luxembourg', 'center' => ['VFS']],
            ['name_ar' => 'سلوفاكيا', 'name_en' => 'Slovakia', 'slug' => 'slovakia', 'center' => ['VFS']],
            ['name_ar' => 'سلوفينيا', 'name_en' => 'Slovenia', 'slug' => 'slovenia', 'center' => ['VFS']],
            ['name_ar' => 'أيرلندا', 'name_en' => 'Ireland', 'slug' => 'ireland', 'center' => ['VFS']],
        ];

        foreach ($euCountries as $item) {
            $country = VisaCountry::where('slug', $item['slug'])->first()
                ?? VisaCountry::where('name_en', $item['name_en'])->first();

            if (! $country) {
                $country = VisaCountry::create([
                    'visa_category_id' => $categoriesMap['eu'],
                    'name_ar' => $this->normalizeArabic($item['name_ar']),
                    'name_en' => $item['name_en'],
                    'slug' => VisaCountry::makeUniqueSlug($item['slug']),
                    'is_active' => true,
                ]);
            } else {
                $country->update(['name_ar' => $this->normalizeArabic($item['name_ar'])]);
            }

            $country->categories()->syncWithoutDetaching([$categoriesMap['eu'], $categoriesMap['europe']]);

            VisaRecord::updateOrCreate(
                [
                    'visa_country_id' => $country->id,
                    'visa_type' => 'سياحة',
                ],
                [
                    'price' => $item['price'] ?? 6500,
                    'currency' => 'EGP',
                    'working_days' => '15–20 يوم عمل',
                    'proposed_duration' => 'حسب قرار السفارة من أسبوع إلى 15 يوم',
                    'stay_duration' => 'حسب قرار السفارة',
                    'entries_count' => 'مفردة / متعددة (حسب قرار السفارة)',
                    'required_documents' => $item['docs'] ?? $standardEuDocs,
                    'embassy_fee' => '90',
                    'embassy_fee_currency' => 'EUR',
                    'embassy_fee_payment_method' => 'بالمصري داخل السفارة — Visa أو Cash',
                    'application_center' => $item['center'] ?? ['VFS'],
                    'is_biometrics_required' => true,
                    'is_interview_required' => true,
                    'notes' => $item['notes'] ?? 'المقابلة مطلوبة: أسئلة عن البرنامج السياحي.',
                    'status' => 'active',
                ]
            );
        }

        // Spain Exception: 2 records (Regular 6500, VIP 8500, 126 USD Cash)
        $spainCountry = VisaCountry::where('slug', 'spain')->first()
            ?? VisaCountry::where('name_en', 'Spain')->first();

        if (! $spainCountry) {
            $spainCountry = VisaCountry::create([
                'visa_category_id' => $categoriesMap['eu'],
                'name_ar' => $this->normalizeArabic('إسبانيا'),
                'name_en' => 'Spain',
                'slug' => 'spain',
                'is_active' => true,
            ]);
        } else {
            $spainCountry->update(['name_ar' => $this->normalizeArabic('إسبانيا')]);
        }
        $spainCountry->categories()->syncWithoutDetaching([$categoriesMap['eu'], $categoriesMap['europe']]);

        VisaRecord::updateOrCreate(
            [
                'visa_country_id' => $spainCountry->id,
                'visa_type' => 'Spain — Regular Appointment — سياحة',
            ],
            [
                'visa_type_slug' => 'spain-regular',
                'price' => 6500,
                'currency' => 'EGP',
                'working_days' => '15–20 يوم عمل',
                'proposed_duration' => 'حسب قرار السفارة من أسبوع إلى 15 يوم',
                'stay_duration' => 'حسب قرار السفارة',
                'entries_count' => 'مفردة / متعددة',
                'required_documents' => $standardEuDocs,
                'embassy_fee' => '126',
                'embassy_fee_currency' => 'USD',
                'embassy_fee_payment_method' => 'Cash داخل السفارة',
                'application_center' => ['BLS'],
                'is_biometrics_required' => true,
                'is_interview_required' => true,
                'notes' => 'Spain — Regular Appointment (مواعيد عادية). المقابلة مطلوبة: أسئلة عن البرنامج السياحي.',
                'status' => 'active',
            ]
        );

        VisaRecord::updateOrCreate(
            [
                'visa_country_id' => $spainCountry->id,
                'visa_type' => 'Spain — VIP Appointment — سياحة',
            ],
            [
                'visa_type_slug' => 'spain-vip',
                'price' => 8500,
                'currency' => 'EGP',
                'working_days' => '15–20 يوم عمل',
                'proposed_duration' => 'حسب قرار السفارة من أسبوع إلى 15 يوم',
                'stay_duration' => 'حسب قرار السفارة',
                'entries_count' => 'مفردة / متعددة',
                'required_documents' => $standardEuDocs,
                'embassy_fee' => '126',
                'embassy_fee_currency' => 'USD',
                'embassy_fee_payment_method' => 'Cash داخل السفارة',
                'application_center' => ['BLS'],
                'is_biometrics_required' => true,
                'is_interview_required' => true,
                'notes' => 'Spain — VIP Appointment (مواعيد VIP). المقابلة مطلوبة: أسئلة عن البرنامج السياحي.',
                'status' => 'active',
            ]
        );

        // Non-EU Schengen Countries
        $schengenNonEu = [
            ['name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'slug' => 'switzerland', 'center' => ['VFS']],
            ['name_ar' => 'النرويج', 'name_en' => 'Norway', 'slug' => 'norway', 'center' => ['VFS']],
            ['name_ar' => 'أيسلندا', 'name_en' => 'Iceland', 'slug' => 'iceland', 'center' => ['VFS']],
            ['name_ar' => 'ليختنشتاين', 'name_en' => 'Liechtenstein', 'slug' => 'liechtenstein', 'center' => ['VFS']],
        ];

        foreach ($schengenNonEu as $item) {
            $country = VisaCountry::where('slug', $item['slug'])->first()
                ?? VisaCountry::where('name_en', $item['name_en'])->first();

            if (! $country) {
                $country = VisaCountry::create([
                    'visa_category_id' => $categoriesMap['schengen-non-eu'],
                    'name_ar' => $this->normalizeArabic($item['name_ar']),
                    'name_en' => $item['name_en'],
                    'slug' => VisaCountry::makeUniqueSlug($item['slug']),
                    'is_active' => true,
                ]);
            } else {
                $country->update(['name_ar' => $this->normalizeArabic($item['name_ar'])]);
            }

            $country->categories()->syncWithoutDetaching([$categoriesMap['schengen-non-eu'], $categoriesMap['europe']]);

            VisaRecord::updateOrCreate(
                [
                    'visa_country_id' => $country->id,
                    'visa_type' => 'سياحة',
                ],
                [
                    'price' => 6500,
                    'currency' => 'EGP',
                    'working_days' => '15–20 يوم عمل',
                    'proposed_duration' => 'حسب قرار السفارة من أسبوع إلى 15 يوم',
                    'stay_duration' => 'حسب قرار السفارة',
                    'entries_count' => 'مفردة / متعددة',
                    'required_documents' => $standardEuDocs,
                    'embassy_fee' => '90',
                    'embassy_fee_currency' => 'EUR',
                    'embassy_fee_payment_method' => 'بالمصري داخل السفارة — Visa أو Cash',
                    'application_center' => $item['center'],
                    'is_biometrics_required' => true,
                    'is_interview_required' => true,
                    'notes' => 'المقابلة مطلوبة: أسئلة عن البرنامج السياحي.',
                    'status' => 'active',
                ]
            );
        }

        // Complete list of remaining countries in the world
        $otherWorldCountries = [
            // GCC & Middle East & Arab
            ['name_ar' => 'المملكة العربية السعودية', 'name_en' => 'Saudi Arabia', 'slug' => 'saudi-arabia', 'cats' => ['gcc', 'middle-east', 'arab-countries', 'asia']],
            ['name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates', 'slug' => 'uae', 'cats' => ['gcc', 'middle-east', 'arab-countries', 'asia']],
            ['name_ar' => 'قطر', 'name_en' => 'Qatar', 'slug' => 'qatar', 'cats' => ['gcc', 'middle-east', 'arab-countries', 'asia']],
            ['name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'slug' => 'kuwait', 'cats' => ['gcc', 'middle-east', 'arab-countries', 'asia']],
            ['name_ar' => 'عُمان', 'name_en' => 'Oman', 'slug' => 'oman', 'cats' => ['gcc', 'middle-east', 'arab-countries', 'asia']],
            ['name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'slug' => 'bahrain', 'cats' => ['gcc', 'middle-east', 'arab-countries', 'asia']],
            ['name_ar' => 'مصر', 'name_en' => 'Egypt', 'slug' => 'egypt', 'cats' => ['arab-countries', 'middle-east', 'africa']],
            ['name_ar' => 'الأردن', 'name_en' => 'Jordan', 'slug' => 'jordan', 'cats' => ['arab-countries', 'middle-east', 'asia']],
            ['name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'slug' => 'lebanon', 'cats' => ['arab-countries', 'middle-east', 'asia']],
            ['name_ar' => 'العراق', 'name_en' => 'Iraq', 'slug' => 'iraq', 'cats' => ['arab-countries', 'middle-east', 'asia']],
            ['name_ar' => 'المغرب', 'name_en' => 'Morocco', 'slug' => 'morocco', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'تونس', 'name_en' => 'Tunisia', 'slug' => 'tunisia', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'slug' => 'algeria', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'ليبيا', 'name_en' => 'Libya', 'slug' => 'libya', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'السودان', 'name_en' => 'Sudan', 'slug' => 'sudan', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'slug' => 'palestine', 'cats' => ['arab-countries', 'middle-east', 'asia']],
            ['name_ar' => 'اليمن', 'name_en' => 'Yemen', 'slug' => 'yemen', 'cats' => ['arab-countries', 'middle-east', 'asia']],
            ['name_ar' => 'سوريا', 'name_en' => 'Syria', 'slug' => 'syria', 'cats' => ['arab-countries', 'middle-east', 'asia']],
            ['name_ar' => 'موريتانيا', 'name_en' => 'Mauritania', 'slug' => 'mauritania', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'الصومال', 'name_en' => 'Somalia', 'slug' => 'somalia', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'جيبوتي', 'name_en' => 'Djibouti', 'slug' => 'djibouti', 'cats' => ['arab-countries', 'africa']],
            ['name_ar' => 'جزر القمر', 'name_en' => 'Comoros', 'slug' => 'comoros', 'cats' => ['arab-countries', 'africa']],

            // Europe Non-Schengen
            ['name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'slug' => 'united-kingdom', 'cats' => ['europe'], 'center' => ['TLS'], 'price' => 7500],
            ['name_ar' => 'تركيا', 'name_en' => 'Turkey', 'slug' => 'turkey', 'cats' => ['europe', 'asia', 'middle-east'], 'center' => ['VFS'], 'price' => 5500],
            ['name_ar' => 'ألبانيا', 'name_en' => 'Albania', 'slug' => 'albania', 'cats' => ['europe']],
            ['name_ar' => 'أندورا', 'name_en' => 'Andorra', 'slug' => 'andorra', 'cats' => ['europe']],
            ['name_ar' => 'أرمينيا', 'name_en' => 'Armenia', 'slug' => 'armenia', 'cats' => ['europe', 'asia', 'cis']],
            ['name_ar' => 'أذربيجان', 'name_en' => 'Azerbaijan', 'slug' => 'azerbaijan', 'cats' => ['europe', 'asia', 'cis']],
            ['name_ar' => 'البوسنة والهرسك', 'name_en' => 'Bosnia and Herzegovina', 'slug' => 'bosnia-herzegovina', 'cats' => ['europe']],
            ['name_ar' => 'جورجيا', 'name_en' => 'Georgia', 'slug' => 'georgia', 'cats' => ['europe', 'asia']],
            ['name_ar' => 'كوسوفو', 'name_en' => 'Kosovo', 'slug' => 'kosovo', 'cats' => ['europe']],
            ['name_ar' => 'مقدونيا الشمالية', 'name_en' => 'North Macedonia', 'slug' => 'north-macedonia', 'cats' => ['europe']],
            ['name_ar' => 'مولدوفا', 'name_en' => 'Moldova', 'slug' => 'moldova', 'cats' => ['europe', 'cis']],
            ['name_ar' => 'الجبل الأسود', 'name_en' => 'Montenegro', 'slug' => 'montenegro', 'cats' => ['europe']],
            ['name_ar' => 'صربيا', 'name_en' => 'Serbia', 'slug' => 'serbia', 'cats' => ['europe']],
            ['name_ar' => 'أوكرانيا', 'name_en' => 'Ukraine', 'slug' => 'ukraine', 'cats' => ['europe']],
            ['name_ar' => 'بيلاروسيا', 'name_en' => 'Belarus', 'slug' => 'belarus', 'cats' => ['europe', 'cis']],
            ['name_ar' => 'روسيا', 'name_en' => 'Russia', 'slug' => 'russia', 'cats' => ['europe', 'asia', 'cis']],

            // North America
            ['name_ar' => 'الولايات المتحدة الأمريكية', 'name_en' => 'United States', 'slug' => 'united-states', 'cats' => ['north-america'], 'center' => ['السفارة مباشرة'], 'price' => 8500],
            ['name_ar' => 'كندا', 'name_en' => 'Canada', 'slug' => 'canada', 'cats' => ['north-america'], 'center' => ['VFS'], 'price' => 8000],
            ['name_ar' => 'المكسيك', 'name_en' => 'Mexico', 'slug' => 'mexico', 'cats' => ['north-america']],

            // Asia
            ['name_ar' => 'الصين', 'name_en' => 'China', 'slug' => 'china', 'cats' => ['asia'], 'center' => ['VFS']],
            ['name_ar' => 'اليابان', 'name_en' => 'Japan', 'slug' => 'japan', 'cats' => ['asia'], 'center' => ['VFS']],
            ['name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea', 'slug' => 'south-korea', 'cats' => ['asia']],
            ['name_ar' => 'الهند', 'name_en' => 'India', 'slug' => 'india', 'cats' => ['asia'], 'center' => ['BLS']],
            ['name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia', 'slug' => 'indonesia', 'cats' => ['asia']],
            ['name_ar' => 'ماليزيا', 'name_en' => 'Malaysia', 'slug' => 'malaysia', 'cats' => ['asia']],
            ['name_ar' => 'تايلاند', 'name_en' => 'Thailand', 'slug' => 'thailand', 'cats' => ['asia']],
            ['name_ar' => 'فيتنام', 'name_en' => 'Vietnam', 'slug' => 'vietnam', 'cats' => ['asia']],
            ['name_ar' => 'سنغافورة', 'name_en' => 'Singapore', 'slug' => 'singapore', 'cats' => ['asia']],
            ['name_ar' => 'الفلبين', 'name_en' => 'Philippines', 'slug' => 'philippines', 'cats' => ['asia']],
            ['name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'slug' => 'pakistan', 'cats' => ['asia']],
            ['name_ar' => 'بنغلاديش', 'name_en' => 'Bangladesh', 'slug' => 'bangladesh', 'cats' => ['asia']],
            ['name_ar' => 'سريلانكا', 'name_en' => 'Sri Lanka', 'slug' => 'sri-lanka', 'cats' => ['asia']],
            ['name_ar' => 'نيبال', 'name_en' => 'Nepal', 'slug' => 'nepal', 'cats' => ['asia']],
            ['name_ar' => 'كازاخستان', 'name_en' => 'Kazakhstan', 'slug' => 'kazakhstan', 'cats' => ['asia', 'cis']],
            ['name_ar' => 'أوزبكستان', 'name_en' => 'Uzbekistan', 'slug' => 'uzbekistan', 'cats' => ['asia', 'cis']],
            ['name_ar' => 'تركمانستان', 'name_en' => 'Turkmenistan', 'slug' => 'turkmenistan', 'cats' => ['asia', 'cis']],
            ['name_ar' => 'قيرغيزستان', 'name_en' => 'Kyrgyzstan', 'slug' => 'kyrgyzstan', 'cats' => ['asia', 'cis']],
            ['name_ar' => 'طاجيكستان', 'name_en' => 'Tajikistan', 'slug' => 'tajikistan', 'cats' => ['asia', 'cis']],

            // Oceania
            ['name_ar' => 'أستراليا', 'name_en' => 'Australia', 'slug' => 'australia', 'cats' => ['oceania'], 'price' => 8500],
            ['name_ar' => 'نيوزيلندا', 'name_en' => 'New Zealand', 'slug' => 'new-zealand', 'cats' => ['oceania']],

            // South America
            ['name_ar' => 'البرازيل', 'name_en' => 'Brazil', 'slug' => 'brazil', 'cats' => ['south-america']],
            ['name_ar' => 'الأرجنتين', 'name_en' => 'Argentina', 'slug' => 'argentina', 'cats' => ['south-america']],
            ['name_ar' => 'كولومبيا', 'name_en' => 'Colombia', 'slug' => 'colombia', 'cats' => ['south-america']],
            ['name_ar' => 'تشيلي', 'name_en' => 'Chile', 'slug' => 'chile', 'cats' => ['south-america']],

            // Africa
            ['name_ar' => 'جنوب أفريقيا', 'name_en' => 'South Africa', 'slug' => 'south-africa', 'cats' => ['africa'], 'center' => ['VFS']],
            ['name_ar' => 'كينيا', 'name_en' => 'Kenya', 'slug' => 'kenya', 'cats' => ['africa']],
            ['name_ar' => 'نيجيريا', 'name_en' => 'Nigeria', 'slug' => 'nigeria', 'cats' => ['africa']],
            ['name_ar' => 'إثيوبيا', 'name_en' => 'Ethiopia', 'slug' => 'ethiopia', 'cats' => ['africa']],
            ['name_ar' => 'تنزانيا', 'name_en' => 'Tanzania', 'slug' => 'tanzania', 'cats' => ['africa']],
        ];

        foreach ($otherWorldCountries as $item) {
            $country = VisaCountry::where('slug', $item['slug'])->first()
                ?? VisaCountry::where('name_en', $item['name_en'])->first();

            $catIds = array_filter(array_map(fn ($slug) => $categoriesMap[$slug] ?? null, $item['cats'] ?? ['other-countries']));
            $primaryCatId = reset($catIds) ?: $categoriesMap['other-countries'];

            if (! $country) {
                $country = VisaCountry::create([
                    'visa_category_id' => $primaryCatId,
                    'name_ar' => $this->normalizeArabic($item['name_ar']),
                    'name_en' => $item['name_en'],
                    'slug' => VisaCountry::makeUniqueSlug($item['slug']),
                    'is_active' => true,
                ]);
            } else {
                $country->update(['name_ar' => $this->normalizeArabic($item['name_ar'])]);
            }

            if (! empty($catIds)) {
                $country->categories()->syncWithoutDetaching($catIds);
            }

            VisaRecord::firstOrCreate(
                [
                    'visa_country_id' => $country->id,
                    'visa_type' => 'سياحة',
                ],
                [
                    'price' => $item['price'] ?? null,
                    'currency' => 'EGP',
                    'working_days' => 'حسب نوع الطلب',
                    'proposed_duration' => 'حسب قرار السفارة',
                    'stay_duration' => 'حسب القرار',
                    'entries_count' => 'حسب القرار',
                    'required_documents' => "الأوراق المطلوبة غير محدودة حالياً، يرجى التواصل مع خدمة العملاء لتدقيق الملف.",
                    'embassy_fee' => 'غير محدد',
                    'embassy_fee_currency' => 'EGP',
                    'embassy_fee_payment_method' => 'داخل السفارة / المركز المعني',
                    'application_center' => $item['center'] ?? ['السفارة مباشرة'],
                    'is_biometrics_required' => true,
                    'is_interview_required' => true,
                    'notes' => 'يرجى استكمال البيانات وتحديد الرسوم مع إدارة المبيعات.',
                    'status' => 'active',
                ]
            );
        }
    }
}
