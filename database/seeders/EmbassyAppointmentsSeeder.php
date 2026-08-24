<?php

namespace Database\Seeders;

use App\Models\EmbassyAppointment;
use App\Models\User;
use App\Models\VisaCategory;
use App\Models\VisaCountry;
use App\Support\EmbassyAppointmentService;
use Illuminate\Database\Seeder;

class EmbassyAppointmentsSeeder extends Seeder
{
    public function run(): void
    {
        EmbassyAppointment::ensureTableSchema();

        $category = VisaCategory::firstOrCreate(
            ['slug' => 'schengen'],
            ['name_ar' => 'شنغن (Schengen)', 'name_en' => 'Schengen']
        );

        $admin = User::where('is_admin', true)->first() ?? User::first();

        $appointmentsData = [
            ['name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'slug' => 'germany', 'dates' => 'شهر 12', 'center' => 'VFS'],
            ['name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'slug' => 'spain', 'dates' => 'شهر 9 و 10', 'center' => 'BLS'],
            ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece', 'dates' => 'شهر 9 و 10', 'center' => 'VFS'],
            ['name_ar' => 'المجر', 'name_en' => 'Hungary', 'slug' => 'hungary', 'dates' => 'شهر 9 و 10', 'center' => 'iOM'],
            ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'slug' => 'netherlands', 'dates' => 'شهر 11 و 12', 'center' => 'VFS'],
            ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'slug' => 'greece', 'dates' => 'شهر 9 (إسكندرية)', 'center' => 'VFS (إسكندرية)'],
            ['name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'slug' => 'portugal', 'dates' => 'شهر 9 و 10', 'center' => 'VFS'],
            ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'slug' => 'sweden', 'dates' => 'شهر 9', 'center' => 'VFS'],
            ['name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'slug' => 'italy', 'dates' => 'شهر 9', 'center' => 'VFS'],
            ['name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'slug' => 'switzerland', 'dates' => 'شهر 9', 'center' => 'VFS'],
            ['name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'slug' => 'croatia', 'dates' => 'شهر 10', 'center' => 'VFS'],
            ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'slug' => 'belgium', 'dates' => 'شهر 11 و 12', 'center' => 'TLS'],
            ['name_ar' => 'فرنسا', 'name_en' => 'France', 'slug' => 'france', 'dates' => 'شهر 1', 'center' => 'TLS'],
            ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'slug' => 'austria', 'dates' => 'شهر 10 و 11', 'center' => 'VFS'],
            ['name_ar' => 'النرويج', 'name_en' => 'Norway', 'slug' => 'norway', 'dates' => 'شهر 9', 'center' => 'VFS'],
        ];

        $service = app(EmbassyAppointmentService::class);

        foreach ($appointmentsData as $item) {
            $country = VisaCountry::where('slug', $item['slug'])
                ->orWhere('name_ar', $item['name_ar'])
                ->first();

            if (! $country) {
                $country = VisaCountry::create([
                    'visa_category_id' => $category->id,
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'],
                    'slug' => $item['slug'],
                    'status' => 'active',
                ]);
            }

            $appt = EmbassyAppointment::firstOrCreate([
                'visa_country_id' => $country->id,
                'visa_type' => 'سياحة',
                'appointment_center' => $item['center'],
                'appointment_type' => 'Regular',
            ], [
                'status' => EmbassyAppointment::STATUS_UNKNOWN,
            ]);

            $service->updateStatus(
                $appt,
                EmbassyAppointment::STATUS_AVAILABLE_NOW,
                $item['dates'],
                'تمت إضافة الموعد تلقائيًا من قائمة المواعيد المتاحة الآن 🟢',
                $admin
            );
        }
    }
}
