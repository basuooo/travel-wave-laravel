<?php

namespace App\Support;

class WorldCountries
{
    /**
     * Get a comprehensive list of world countries with Arabic name, English name, flag emoji, and code.
     *
     * @return array<int, array{code: string, name_ar: string, name_en: string, flag: string}>
     */
    public static function all(): array
    {
        return [
            ['code' => 'EG', 'name_ar' => 'مصر', 'name_en' => 'Egypt', 'flag' => '🇪🇬'],
            ['code' => 'SA', 'name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'flag' => '🇸🇦'],
            ['code' => 'AE', 'name_ar' => 'الإمارات', 'name_en' => 'United Arab Emirates', 'flag' => '🇦🇪'],
            ['code' => 'KW', 'name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'flag' => '🇰🇼'],
            ['code' => 'QA', 'name_ar' => 'قطر', 'name_en' => 'Qatar', 'flag' => '🇶🇦'],
            ['code' => 'BH', 'name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'flag' => '🇧🇭'],
            ['code' => 'OM', 'name_ar' => 'عُمان', 'name_en' => 'Oman', 'flag' => '🇴🇲'],
            ['code' => 'JO', 'name_ar' => 'الأردن', 'name_en' => 'Jordan', 'flag' => '🇯🇴'],
            ['code' => 'LB', 'name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'flag' => '🇱🇧'],
            ['code' => 'IQ', 'name_ar' => 'العراق', 'name_en' => 'Iraq', 'flag' => '🇮🇶'],
            ['code' => 'SY', 'name_ar' => 'سوريا', 'name_en' => 'Syria', 'flag' => '🇸🇾'],
            ['code' => 'PS', 'name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'flag' => '🇵🇸'],
            ['code' => 'YE', 'name_ar' => 'اليمن', 'name_en' => 'Yemen', 'flag' => '🇾🇪'],
            ['code' => 'LY', 'name_ar' => 'ليبيا', 'name_en' => 'Libya', 'flag' => '🇱🇾'],
            ['code' => 'SD', 'name_ar' => 'السودان', 'name_en' => 'Sudan', 'flag' => '🇸🇩'],
            ['code' => 'TN', 'name_ar' => 'تونس', 'name_en' => 'Tunisia', 'flag' => '🇹🇳'],
            ['code' => 'DZ', 'name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'flag' => '🇩🇿'],
            ['code' => 'MA', 'name_ar' => 'المغرب', 'name_en' => 'Morocco', 'flag' => '🇲🇦'],
            ['code' => 'MR', 'name_ar' => 'موريتانيا', 'name_en' => 'Mauritania', 'flag' => '🇲🇷'],
            ['code' => 'SO', 'name_ar' => 'الصومال', 'name_en' => 'Somalia', 'flag' => '🇸🇴'],
            ['code' => 'DJ', 'name_ar' => 'جيبوتي', 'name_en' => 'Djibouti', 'flag' => '🇩🇯'],
            ['code' => 'KM', 'name_ar' => 'جزر القمر', 'name_en' => 'Comoros', 'flag' => '🇰🇲'],

            ['code' => 'EU', 'name_ar' => 'دول الشنغن (الاتحاد الأوروبي)', 'name_en' => 'Schengen Area (EU)', 'flag' => '🇪🇺'],
            ['code' => 'FR', 'name_ar' => 'فرنسا', 'name_en' => 'France', 'flag' => '🇫🇷'],
            ['code' => 'GB', 'name_ar' => 'المملكة المتحدة (بريطانيا)', 'name_en' => 'United Kingdom', 'flag' => '🇬🇧'],
            ['code' => 'US', 'name_ar' => 'الولايات المتحدة الأمريكية', 'name_en' => 'United States', 'flag' => '🇺🇸'],
            ['code' => 'CA', 'name_ar' => 'كندا', 'name_en' => 'Canada', 'flag' => '🇨🇦'],
            ['code' => 'DE', 'name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'flag' => '🇩🇪'],
            ['code' => 'IT', 'name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'flag' => '🇮🇹'],
            ['code' => 'ES', 'name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'flag' => '🇪🇸'],
            ['code' => 'TR', 'name_ar' => 'تركيا', 'name_en' => 'Turkey', 'flag' => '🇹🇷'],
            ['code' => 'RU', 'name_ar' => 'روسيا', 'name_en' => 'Russia', 'flag' => '🇷🇺'],
            ['code' => 'CN', 'name_ar' => 'الصين', 'name_en' => 'China', 'flag' => '🇨🇳'],
            ['code' => 'JP', 'name_ar' => 'اليابان', 'name_en' => 'Japan', 'flag' => '🇯🇵'],
            ['code' => 'KR', 'name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea', 'flag' => '🇰🇷'],
            ['code' => 'IN', 'name_ar' => 'الهند', 'name_en' => 'India', 'flag' => '🇮🇳'],
            ['code' => 'PK', 'name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'flag' => '🇵🇰'],
            ['code' => 'BD', 'name_ar' => 'بنغلاديش', 'name_en' => 'Bangladesh', 'flag' => '🇧🇩'],
            ['code' => 'ID', 'name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia', 'flag' => '🇮🇩'],
            ['code' => 'MY', 'name_ar' => 'ماليزيا', 'name_en' => 'Malaysia', 'flag' => '🇲🇾'],
            ['code' => 'TH', 'name_ar' => 'تايلاند', 'name_en' => 'Thailand', 'flag' => '🇹🇭'],
            ['code' => 'SG', 'name_ar' => 'سنغافورة', 'name_en' => 'Singapore', 'flag' => '🇸🇬'],
            ['code' => 'PH', 'name_ar' => 'الفلبين', 'name_en' => 'Philippines', 'flag' => '🇵🇭'],
            ['code' => 'VN', 'name_ar' => 'فيتنام', 'name_en' => 'Vietnam', 'flag' => '🇻🇳'],
            ['code' => 'AU', 'name_ar' => 'أستراليا', 'name_en' => 'Australia', 'flag' => '🇦🇺'],
            ['code' => 'NZ', 'name_ar' => 'نيوزيلندا', 'name_en' => 'New Zealand', 'flag' => '🇳🇿'],

            ['code' => 'CH', 'name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'flag' => '🇨🇭'],
            ['code' => 'NL', 'name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'flag' => '🇳🇱'],
            ['code' => 'BE', 'name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'flag' => '🇧🇪'],
            ['code' => 'AT', 'name_ar' => 'النمسا', 'name_en' => 'Austria', 'flag' => '🇦🇹'],
            ['code' => 'GR', 'name_ar' => 'اليونان', 'name_en' => 'Greece', 'flag' => '🇬🇷'],
            ['code' => 'PT', 'name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'flag' => '🇵🇹'],
            ['code' => 'SE', 'name_ar' => 'السويد', 'name_en' => 'Sweden', 'flag' => '🇸🇪'],
            ['code' => 'NO', 'name_ar' => 'النرويج', 'name_en' => 'Norway', 'flag' => '🇳🇴'],
            ['code' => 'DK', 'name_ar' => 'الدنمارك', 'name_en' => 'Denmark', 'flag' => '🇩🇰'],
            ['code' => 'FI', 'name_ar' => 'فنلندا', 'name_en' => 'Finland', 'flag' => '🇫🇮'],
            ['code' => 'IE', 'name_ar' => 'أيرلندا', 'name_en' => 'Ireland', 'flag' => '🇮🇪'],
            ['code' => 'CZ', 'name_ar' => 'التشيك', 'name_en' => 'Czech Republic', 'flag' => '🇨🇿'],
            ['code' => 'HU', 'name_ar' => 'المجر (هنغاريا)', 'name_en' => 'Hungary', 'flag' => '🇭🇺'],
            ['code' => 'PL', 'name_ar' => 'بولندا', 'name_en' => 'Poland', 'flag' => '🇵🇱'],
            ['code' => 'RO', 'name_ar' => 'رومانيا', 'name_en' => 'Romania', 'flag' => '🇷🇴'],
            ['code' => 'BG', 'name_ar' => 'بلغاريا', 'name_en' => 'Bulgaria', 'flag' => '🇧🇬'],
            ['code' => 'HR', 'name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'flag' => '🇭🇷'],
            ['code' => 'SK', 'name_ar' => 'سلوفاكيا', 'name_en' => 'Slovakia', 'flag' => '🇸🇰'],
            ['code' => 'SI', 'name_ar' => 'سلوفينيا', 'name_en' => 'Slovenia', 'flag' => '🇸🇮'],
            ['code' => 'CY', 'name_ar' => 'قبرص', 'name_en' => 'Cyprus', 'flag' => '🇨🇾'],
            ['code' => 'MT', 'name_ar' => 'مالطا', 'name_en' => 'Malta', 'flag' => '🇲🇹'],
            ['code' => 'IS', 'name_ar' => 'أيسلندا', 'name_en' => 'Iceland', 'flag' => '🇮🇸'],
            ['code' => 'LU', 'name_ar' => 'لوكسمبورغ', 'name_en' => 'Luxembourg', 'flag' => '🇱🇺'],
            ['code' => 'EE', 'name_ar' => 'إستونيا', 'name_en' => 'Estonia', 'flag' => '🇪🇪'],
            ['code' => 'LV', 'name_ar' => 'لاتفيا', 'name_en' => 'Latvia', 'flag' => '🇱🇻'],
            ['code' => 'LT', 'name_ar' => 'ليتوانيا', 'name_en' => 'Lithuania', 'flag' => '🇱🇹'],

            ['code' => 'GE', 'name_ar' => 'جورجيا', 'name_en' => 'Georgia', 'flag' => '🇬🇪'],
            ['code' => 'AZ', 'name_ar' => 'أذربيجان', 'name_en' => 'Azerbaijan', 'flag' => '🇦🇿'],
            ['code' => 'AM', 'name_ar' => 'أرمينيا', 'name_en' => 'Armenia', 'flag' => '🇦🇲'],
            ['code' => 'AL', 'name_ar' => 'ألبانيا', 'name_en' => 'Albania', 'flag' => '🇦🇱'],
            ['code' => 'BA', 'name_ar' => 'البوسنة والهرسك', 'name_en' => 'Bosnia and Herzegovina', 'flag' => '🇧🇦'],
            ['code' => 'RS', 'name_ar' => 'صربيا', 'name_en' => 'Serbia', 'flag' => '🇷🇸'],
            ['code' => 'ME', 'name_ar' => 'الجبل الأسود (مونتينيغرو)', 'name_en' => 'Montenegro', 'flag' => '🇲🇪'],
            ['code' => 'MK', 'name_ar' => 'مقدونيا الشمالية', 'name_en' => 'North Macedonia', 'flag' => '🇲🇰'],
            ['code' => 'BY', 'name_ar' => 'بيلاروسيا', 'name_en' => 'Belarus', 'flag' => '🇧🇾'],
            ['code' => 'UA', 'name_ar' => 'أوكرانيا', 'name_en' => 'Ukraine', 'flag' => '🇺🇦'],
            ['code' => 'MD', 'name_ar' => 'مولدوفا', 'name_en' => 'Moldova', 'flag' => '🇲🇩'],

            ['code' => 'ZA', 'name_ar' => 'جنوب إفريقيا', 'name_en' => 'South Africa', 'flag' => '🇿🇦'],
            ['code' => 'KE', 'name_ar' => 'كينيا', 'name_en' => 'Kenya', 'flag' => '🇰🇪'],
            ['code' => 'TZ', 'name_ar' => 'تنزانيا', 'name_en' => 'Tanzania', 'flag' => '🇹🇿'],
            ['code' => 'UG', 'name_ar' => 'أوغندا', 'name_en' => 'Uganda', 'flag' => '🇺🇬'],
            ['code' => 'ET', 'name_ar' => 'إثيوبيا', 'name_en' => 'Ethiopia', 'flag' => '🇪🇹'],
            ['code' => 'GH', 'name_ar' => 'غانا', 'name_en' => 'Ghana', 'flag' => '🇬🇭'],
            ['code' => 'NG', 'name_ar' => 'نيجيريا', 'name_en' => 'Nigeria', 'flag' => '🇳🇬'],
            ['code' => 'CI', 'name_ar' => 'ساحل العاج', 'name_en' => 'Ivory Coast', 'flag' => '🇨🇮'],
            ['code' => 'SN', 'name_ar' => 'السنغال', 'name_en' => 'Senegal', 'flag' => '🇸🇳'],
            ['code' => 'MU', 'name_ar' => 'موريشيوس', 'name_en' => 'Mauritius', 'flag' => '🇲🇺'],
            ['code' => 'SC', 'name_ar' => 'سيشل', 'name_en' => 'Seychelles', 'flag' => '🇸🇨'],
            ['code' => 'MV', 'name_ar' => 'جزر المالديف', 'name_en' => 'Maldives', 'flag' => '🇲🇻'],
            ['code' => 'LK', 'name_ar' => 'سريلانكا', 'name_en' => 'Sri Lanka', 'flag' => '🇱🇰'],
            ['code' => 'NP', 'name_ar' => 'نيبال', 'name_en' => 'Nepal', 'flag' => '🇳🇵'],
            ['code' => 'KZ', 'name_ar' => 'كازاخستان', 'name_en' => 'Kazakhstan', 'flag' => '🇰🇿'],
            ['code' => 'UZ', 'name_ar' => 'أوزبكستان', 'name_en' => 'Uzbekistan', 'flag' => '🇺🇿'],
            ['code' => 'KG', 'name_ar' => 'قيرغيزستان', 'name_en' => 'Kyrgyzstan', 'flag' => '🇰🇬'],
            ['code' => 'TJ', 'name_ar' => 'طاجيكستان', 'name_en' => 'Tajikistan', 'flag' => '🇹🇯'],
            ['code' => 'TM', 'name_ar' => 'تركمانستان', 'name_en' => 'Turkmenistan', 'flag' => '🇹🇲'],

            ['code' => 'BR', 'name_ar' => 'البرازيل', 'name_en' => 'Brazil', 'flag' => '🇧🇷'],
            ['code' => 'AR', 'name_ar' => 'الأرجنتين', 'name_en' => 'Argentina', 'flag' => '🇦🇷'],
            ['code' => 'MX', 'name_ar' => 'المكسيك', 'name_en' => 'Mexico', 'flag' => '🇲🇽'],
            ['code' => 'CL', 'name_ar' => 'تشيلي', 'name_en' => 'Chile', 'flag' => '🇨🇱'],
            ['code' => 'CO', 'name_ar' => 'كولومبيا', 'name_en' => 'Colombia', 'flag' => '🇨🇴'],
            ['code' => 'PE', 'name_ar' => 'بيرو', 'name_en' => 'Peru', 'flag' => '🇵🇪'],
            ['code' => 'VE', 'name_ar' => 'فنزويلا', 'name_en' => 'Venezuela', 'flag' => '🇻🇪'],
            ['code' => 'EC', 'name_ar' => 'إكوادور', 'name_en' => 'Ecuador', 'flag' => '🇪🇨'],
            ['code' => 'UY', 'name_ar' => 'أوروغواي', 'name_en' => 'Uruguay', 'flag' => '🇺🇾'],
            ['code' => 'PY', 'name_ar' => 'باراغواي', 'name_en' => 'Paraguay', 'flag' => '🇵🇾'],
            ['code' => 'BO', 'name_ar' => 'بوليفيا', 'name_en' => 'Bolivia', 'flag' => '🇧🇴'],
            ['code' => 'CR', 'name_ar' => 'كوستاريكا', 'name_en' => 'Costa Rica', 'flag' => '🇨🇷'],
            ['code' => 'PA', 'name_ar' => 'بنما', 'name_en' => 'Panama', 'flag' => '🇵🇦'],
            ['code' => 'DO', 'name_ar' => 'جمهورية الدومينيكان', 'name_en' => 'Dominican Republic', 'flag' => '🇩🇴'],
            ['code' => 'CU', 'name_ar' => 'كوبا', 'name_en' => 'Cuba', 'flag' => '🇨🇺'],
            ['code' => 'JM', 'name_ar' => 'جاميكا', 'name_en' => 'Jamaica', 'flag' => '🇯🇲'],

            ['code' => 'AF', 'name_ar' => 'أفغانستان', 'name_en' => 'Afghanistan', 'flag' => '🇦🇫'],
            ['code' => 'AO', 'name_ar' => 'أنغولا', 'name_en' => 'Angola', 'flag' => '🇦🇴'],
            ['code' => 'BN', 'name_ar' => 'بروناي', 'name_en' => 'Brunei', 'flag' => '🇧🇳'],
            ['code' => 'KH', 'name_ar' => 'كمبوديا', 'name_en' => 'Cambodia', 'flag' => '🇰🇭'],
            ['code' => 'CM', 'name_ar' => 'الكاميرون', 'name_en' => 'Cameroon', 'flag' => '🇨🇲'],
            ['code' => 'CG', 'name_ar' => 'الكونغو', 'name_en' => 'Congo', 'flag' => '🇨🇬'],
            ['code' => 'GN', 'name_ar' => 'غينيا', 'name_en' => 'Guinea', 'flag' => '🇬🇳'],
            ['code' => 'HT', 'name_ar' => 'هايتي', 'name_en' => 'Haiti', 'flag' => '🇭🇹'],
            ['code' => 'HN', 'name_ar' => 'هندوراس', 'name_en' => 'Honduras', 'flag' => '🇭🇳'],
            ['code' => 'IR', 'name_ar' => 'إيران', 'name_en' => 'Iran', 'flag' => '🇮🇷'],
            ['code' => 'LA', 'name_ar' => 'لاوس', 'name_en' => 'Laos', 'flag' => '🇱🇦'],
            ['code' => 'MM', 'name_ar' => 'ميانمار', 'name_en' => 'Myanmar', 'flag' => '🇲🇲'],
            ['code' => 'NA', 'name_ar' => 'ناميبيا', 'name_en' => 'Namibia', 'flag' => '🇳🇦'],
            ['code' => 'NI', 'name_ar' => 'نيكاراغوا', 'name_en' => 'Nicaragua', 'flag' => '🇳🇮'],
            ['code' => 'RW', 'name_ar' => 'رواندا', 'name_en' => 'Rwanda', 'flag' => '🇷🇼'],
            ['code' => 'SV', 'name_ar' => 'السلفادور', 'name_en' => 'El Salvador', 'flag' => '🇸🇻'],
            ['code' => 'TW', 'name_ar' => 'تايوان', 'name_en' => 'Taiwan', 'flag' => '🇹🇼'],
            ['code' => 'ZW', 'name_ar' => 'زمبابوي', 'name_en' => 'Zimbabwe', 'flag' => '🇿🇼'],
        ];
    }

    /**
     * Check if a given string matches any country in our list.
     * Normalizes spaces, hamzas, and lowercases for comparison.
     */
    public static function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = str_replace(['أ', 'إ', 'آ'], 'ا', $text);
        $text = str_replace('ة', 'ه', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return $text;
    }

    /**
     * Finds matching country array from standard list or returns null if custom text.
     */
    public static function findMatch(?string $val): ?array
    {
        if ($val === null || trim($val) === '') {
            return null;
        }

        $normVal = static::normalize($val);

        foreach (static::all() as $country) {
            if (static::normalize($country['name_ar']) === $normVal ||
                static::normalize($country['name_en']) === $normVal ||
                static::normalize($country['code']) === $normVal) {
                return $country;
            }
        }

        foreach (static::all() as $country) {
            $normAr = static::normalize($country['name_ar']);
            $normEn = static::normalize($country['name_en']);
            if (!empty($normVal) && (str_contains($normAr, $normVal) || str_contains($normVal, $normAr) ||
                                     str_contains($normEn, $normVal) || str_contains($normVal, $normEn))) {
                return $country;
            }
        }

        return null;
    }
}
