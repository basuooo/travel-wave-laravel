<?php

namespace App\Services\LandingPageNew;

use Illuminate\Support\Str;

class HtmlAnalyzer
{
    public function analyze(string $htmlContent): array
    {
        $doc = new \DOMDocument();
        // Suppress warnings for HTML5 elements
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $dependencies = $this->detectDependencies($htmlContent);
        $forms = $this->detectForms($doc);
        $headStyles = $this->extractHeadStyles($doc);
        $headScripts = $this->extractHeadScripts($doc);
        $sections = $this->detectSections($doc, $htmlContent);

        return [
            'dependencies' => $dependencies,
            'forms' => $forms,
            'head_styles' => $headStyles,
            'head_scripts' => $headScripts,
            'sections' => $sections,
            'total_sections' => count($sections),
            'total_forms' => count($forms),
        ];
    }

    public function detectDependencies(string $html): array
    {
        $deps = [];

        if (Str::contains($html, ['bootstrap.css', 'bootstrap.min.css', 'bootstrap.bundle'])) {
            $deps['bootstrap'] = true;
        }
        if (Str::contains($html, ['swiper', 'swiper-bundle'])) {
            $deps['swiper'] = true;
        }
        if (Str::contains($html, ['jquery', 'jquery.min.js'])) {
            $deps['jquery'] = true;
        }
        if (Str::contains($html, ['fontawesome', 'fa-', 'font-awesome'])) {
            $deps['fontawesome'] = true;
        }
        if (Str::contains($html, ['bootstrap-icons', 'bi-'])) {
            $deps['bootstrap_icons'] = true;
        }
        if (Str::contains($html, ['boxicons', 'bx-'])) {
            $deps['boxicons'] = true;
        }
        if (Str::contains($html, ['remixicon', 'ri-'])) {
            $deps['remixicon'] = true;
        }
        if (Str::contains($html, ['fonts.googleapis.com', 'fonts.gstatic.com'])) {
            $deps['google_fonts'] = true;
        }
        if (Str::contains($html, ['countdown', 'timer', 'data-countdown'])) {
            $deps['countdown'] = true;
        }

        return $deps;
    }

    public function detectForms(\DOMDocument $doc): array
    {
        $forms = [];
        $formElements = $doc->getElementsByTagName('form');

        foreach ($formElements as $index => $formEl) {
            $inputs = [];
            $inputNodes = $formEl->getElementsByTagName('input');
            foreach ($inputNodes as $input) {
                $inputs[] = [
                    'name' => $input->getAttribute('name') ?: $input->getAttribute('id'),
                    'type' => $input->getAttribute('type') ?: 'text',
                    'placeholder' => $input->getAttribute('placeholder'),
                    'required' => $input->hasAttribute('required'),
                ];
            }

            $selectNodes = $formEl->getElementsByTagName('select');
            foreach ($selectNodes as $select) {
                $inputs[] = [
                    'name' => $select->getAttribute('name') ?: $select->getAttribute('id'),
                    'type' => 'select',
                    'required' => $select->hasAttribute('required'),
                ];
            }

            $textareaNodes = $formEl->getElementsByTagName('textarea');
            foreach ($textareaNodes as $ta) {
                $inputs[] = [
                    'name' => $ta->getAttribute('name') ?: $ta->getAttribute('id'),
                    'type' => 'textarea',
                    'placeholder' => $ta->getAttribute('placeholder'),
                    'required' => $ta->hasAttribute('required'),
                ];
            }

            $forms[] = [
                'id' => $formEl->getAttribute('id') ?: 'form_' . ($index + 1),
                'action' => $formEl->getAttribute('action'),
                'method' => $formEl->getAttribute('method') ?: 'POST',
                'inputs' => $inputs,
                'html' => $doc->saveHTML($formEl),
            ];
        }

        return $forms;
    }

    public function extractHeadStyles(\DOMDocument $doc): string
    {
        $styles = '';
        $styleNodes = $doc->getElementsByTagName('style');
        foreach ($styleNodes as $node) {
            $styles .= $node->nodeValue . "\n";
        }

        return $styles;
    }

    public function extractHeadScripts(\DOMDocument $doc): string
    {
        $scripts = '';
        $scriptNodes = $doc->getElementsByTagName('script');
        foreach ($scriptNodes as $node) {
            if (! $node->hasAttribute('src') && filled($node->nodeValue)) {
                $scripts .= $node->nodeValue . "\n";
            }
        }

        return $scripts;
    }

    public function detectSections(\DOMDocument $doc, string $rawHtml): array
    {
        $sections = [];
        $xpath = new \DOMXPath($doc);

        // Query <section> elements or major <div> containers
        $nodes = $xpath->query('//section | //div[contains(@class, "section") or contains(@class, "hero") or contains(@class, "cta") or contains(@class, "faq") or contains(@class, "pricing") or contains(@class, "features") or contains(@class, "footer")]');

        if ($nodes->length === 0) {
            // Fallback: Query top-level body children
            $nodes = $xpath->query('//body/*');
        }

        $index = 1;
        foreach ($nodes as $node) {
            $classAttr = $node->getAttribute('class') ?: '';
            $idAttr = $node->getAttribute('id') ?: 'sec_' . $index;
            $nodeName = strtolower($node->nodeName);
            $innerHtml = $doc->saveHTML($node);

            if (empty(trim(strip_tags($innerHtml))) && ! Str::contains($innerHtml, ['<img', '<video', '<svg', '<iframe'])) {
                continue;
            }

            $type = $this->classifySectionType($nodeName, $classAttr, $idAttr, $innerHtml);

            $sections[] = [
                'id' => $idAttr,
                'type' => $type['type'],
                'name_ar' => $type['name_ar'],
                'name_en' => $type['name_en'],
                'is_custom_html' => $type['is_custom_html'],
                'html' => $innerHtml,
                'class' => $classAttr,
                'order' => $index,
            ];

            $index++;
        }

        if (empty($sections)) {
            $sections[] = [
                'id' => 'sec_full_custom',
                'type' => 'custom_html',
                'name_ar' => 'قسم هجين مخصص',
                'name_en' => 'Custom Hybrid Section',
                'is_custom_html' => true,
                'html' => $rawHtml,
                'class' => 'custom-landing-section',
                'order' => 1,
            ];
        }

        return $sections;
    }

    protected function classifySectionType(string $tag, string $class, string $id, string $html): array
    {
        $combined = strtolower($tag . ' ' . $class . ' ' . $id);

        if (Str::contains($combined, ['hero', 'banner', 'header-slide', 'top-section'])) {
            return ['type' => 'hero', 'name_ar' => 'قسم البانر (Hero)', 'name_en' => 'Hero Banner', 'is_custom_html' => false];
        }

        if (Str::contains($combined, ['feature', 'benefit', 'service', 'why-choose', 'advantage'])) {
            return ['type' => 'features', 'name_ar' => 'قسم المميزات والخدمات', 'name_en' => 'Features & Services', 'is_custom_html' => false];
        }

        if (Str::contains($combined, ['product', 'offer', 'deal', 'package', 'pricing', 'price'])) {
            return ['type' => 'product_pricing', 'name_ar' => 'قسم العرض والأسعار', 'name_en' => 'Product & Pricing', 'is_custom_html' => false];
        }

        if (Str::contains($combined, ['faq', 'question', 'accordion'])) {
            return ['type' => 'faq', 'name_ar' => 'قسم الأسئلة الشائعة', 'name_en' => 'FAQ Section', 'is_custom_html' => false];
        }

        if (Str::contains($combined, ['testimonial', 'review', 'rating', 'feedback', 'comment'])) {
            return ['type' => 'reviews', 'name_ar' => 'قسم آراء العملاء', 'name_en' => 'Testimonials & Reviews', 'is_custom_html' => false];
        }

        if (Str::contains($combined, ['cta', 'call-to-action', 'contact', 'form-section', 'action'])) {
            return ['type' => 'cta_form', 'name_ar' => 'قسم التواصل والطلب (CTA)', 'name_en' => 'CTA & Form Section', 'is_custom_html' => false];
        }

        if (Str::contains($combined, ['footer', 'bottom'])) {
            return ['type' => 'footer', 'name_ar' => 'قسم الفوتر (Footer)', 'name_en' => 'Footer Section', 'is_custom_html' => false];
        }

        return ['type' => 'custom_html', 'name_ar' => 'قسم مخصص (Custom Section)', 'name_en' => 'Custom Section', 'is_custom_html' => true];
    }
}
