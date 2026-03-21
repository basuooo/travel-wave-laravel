<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'sitemap_include_pages',
        'sitemap_include_visa_destinations',
        'sitemap_include_destinations',
        'sitemap_include_blog_posts',
        'sitemap_include_marketing_pages',
        'sitemap_include_images',
        'sitemap_last_generated_at',
        'robots_txt_content',
        'search_console_property',
        'search_console_notes',
        'schema_organization_enabled',
        'schema_organization_name',
        'schema_organization_logo',
        'schema_local_business_enabled',
        'schema_breadcrumb_enabled',
        'schema_faq_enabled',
        'schema_article_enabled',
        'schema_destination_enabled',
        'merchant_center_enabled',
        'merchant_center_verification_code',
        'merchant_center_notes',
        'default_robots_meta',
    ];

    protected $casts = [
        'sitemap_include_pages' => 'boolean',
        'sitemap_include_visa_destinations' => 'boolean',
        'sitemap_include_destinations' => 'boolean',
        'sitemap_include_blog_posts' => 'boolean',
        'sitemap_include_marketing_pages' => 'boolean',
        'sitemap_include_images' => 'boolean',
        'sitemap_last_generated_at' => 'datetime',
        'schema_organization_enabled' => 'boolean',
        'schema_local_business_enabled' => 'boolean',
        'schema_breadcrumb_enabled' => 'boolean',
        'schema_faq_enabled' => 'boolean',
        'schema_article_enabled' => 'boolean',
        'schema_destination_enabled' => 'boolean',
        'merchant_center_enabled' => 'boolean',
    ];
}
