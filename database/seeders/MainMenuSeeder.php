<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MainMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $headerItems = [
            [
                'title_ar' => 'الرئيسية',
                'title_en' => 'Home',
                'page_key' => 'home',
                'type' => 'page',
                'sort_order' => 1,
            ],
            [
                'title_ar' => 'التأشيرات الخارجية',
                'title_en' => 'Visas',
                'page_key' => 'visas',
                'type' => 'page',
                'sort_order' => 2,
            ],
            [
                'title_ar' => 'السياحة الداخلية',
                'title_en' => 'Domestic Tourism',
                'page_key' => 'domestic',
                'type' => 'page',
                'sort_order' => 3,
            ],
            [
                'title_ar' => 'الطيران',
                'title_en' => 'Flights',
                'page_key' => 'flights',
                'type' => 'page',
                'sort_order' => 4,
            ],
            [
                'title_ar' => 'الفنادق',
                'title_en' => 'Hotels',
                'page_key' => 'hotels',
                'type' => 'page',
                'sort_order' => 5,
            ],
            [
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'page_key' => 'about',
                'type' => 'page',
                'sort_order' => 6,
            ],
            [
                'title_ar' => 'المقالات',
                'title_en' => 'Blog',
                'type' => 'custom',
                'route_name' => 'blog.index',
                'sort_order' => 7,
            ],
            [
                'title_ar' => 'تواصل معنا',
                'title_en' => 'Contact Us',
                'page_key' => 'contact',
                'type' => 'page',
                'sort_order' => 8,
            ],
        ];

        foreach ($headerItems as $itemData) {
            $pageId = null;
            if (! empty($itemData['page_key'])) {
                $page = Page::where('key', $itemData['page_key'])->first();
                if ($page) {
                    $pageId = $page->id;
                }
            }

            MenuItem::updateOrCreate(
                [
                    'location' => 'header',
                    'title_ar' => $itemData['title_ar'],
                ],
                [
                    'location' => 'header',
                    'parent_id' => null,
                    'type' => $itemData['type'] ?? 'custom',
                    'page_id' => $pageId,
                    'title_en' => $itemData['title_en'],
                    'title_ar' => $itemData['title_ar'],
                    'route_name' => $itemData['route_name'] ?? null,
                    'target' => '_self',
                    'sort_order' => $itemData['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
