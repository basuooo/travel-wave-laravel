<?php

use App\Models\VisaCategory;
use App\Models\VisaCountry;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Remove empty "شنغن (Schengen)" category
        $schengenCat = VisaCategory::withTrashed()
            ->where(function ($q) {
                $q->where('slug', 'schengen')
                  ->orWhere('name_ar', 'شنغن')
                  ->orWhere('name_ar', 'like', '%شنغن (Schengen)%');
            })
            ->where('name_ar', 'not like', '%خارج%')
            ->first();

        if ($schengenCat) {
            $schengenCat->update(['is_active' => false]);
            $schengenCat->delete();
        }

        // 2. Rename "دول شنغن خارج الاتحاد الاوروبي" to "دول اوربية خارج الاتحاد الاوروبي"
        $nonEuCat = VisaCategory::withTrashed()
            ->where(function ($q) {
                $q->where('slug', 'schengen-non-eu')
                  ->orWhere('slug', 'non-eu-europe')
                  ->orWhere('name_ar', 'like', '%خارج الاتحاد%');
            })
            ->first();

        if (! $nonEuCat) {
            $nonEuCat = VisaCategory::create([
                'name_ar' => 'دول اوربية خارج الاتحاد الاوروبي',
                'name_en' => 'European Non-EU Countries',
                'slug' => 'non-eu-europe',
                'sort_order' => 2,
                'is_active' => true,
            ]);
        } else {
            $nonEuCat->update([
                'name_ar' => 'دول اوربية خارج الاتحاد الاوروبي',
                'name_en' => 'European Non-EU Countries',
                'is_active' => true,
            ]);
            if ($nonEuCat->trashed()) {
                $nonEuCat->restore();
            }
        }

        // 3. Move Norway (النرويج) & Switzerland (سويسرا) to "دول الاتحاد الاوروبي"
        $euCat = VisaCategory::withTrashed()
            ->where(function ($q) {
                $q->where('slug', 'eu')
                  ->orWhere('name_ar', 'like', '%الاتحاد%');
            })
            ->first();

        if (! $euCat) {
            $euCat = VisaCategory::create([
                'name_ar' => 'دول الاتحاد الاوروبي',
                'name_en' => 'European Union',
                'slug' => 'eu',
                'sort_order' => 1,
                'is_active' => true,
            ]);
        } elseif ($euCat->trashed()) {
            $euCat->restore();
        }

        $targets = [
            ['slug' => 'norway', 'name_en' => 'Norway', 'name_ar' => 'النرويج'],
            ['slug' => 'switzerland', 'name_en' => 'Switzerland', 'name_ar' => 'سويسرا'],
        ];

        foreach ($targets as $item) {
            $c = VisaCountry::withTrashed()
                ->where('slug', $item['slug'])
                ->orWhere('name_en', $item['name_en'])
                ->orWhere('name_ar', 'like', '%' . $item['name_ar'] . '%')
                ->first();

            if (! $c) {
                $c = VisaCountry::create([
                    'visa_category_id' => $euCat->id,
                    'name_ar' => $item['name_ar'],
                    'name_en' => $item['name_en'],
                    'slug' => $item['slug'],
                    'is_active' => true,
                ]);
            } else {
                $c->update([
                    'visa_category_id' => $euCat->id,
                    'is_active' => true,
                ]);
                if ($c->trashed()) {
                    $c->restore();
                }
            }

            try {
                $c->categories()->syncWithoutDetaching([$euCat->id]);
            } catch (\Throwable $ex) {}
        }

        // 4. Remove "امريكا الوسطى" (Central America)
        $centralAmerica = VisaCategory::withTrashed()
            ->where(function ($q) {
                $q->where('slug', 'central-america')
                  ->orWhere('name_ar', 'like', '%امريكا الوسطى%')
                  ->orWhere('name_ar', 'like', '%أمريكا الوسطى%');
            })
            ->first();

        if ($centralAmerica) {
            $centralAmerica->update(['is_active' => false]);
            $centralAmerica->delete();
        }

        // 5. Remove "الكاريبي" (Caribbean)
        $caribbean = VisaCategory::withTrashed()
            ->where(function ($q) {
                $q->where('slug', 'caribbean')
                  ->orWhere('name_ar', 'like', '%الكاريبي%');
            })
            ->first();

        if ($caribbean) {
            $caribbean->update(['is_active' => false]);
            $caribbean->delete();
        }
    }

    public function down(): void
    {
    }
};
