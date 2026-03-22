<?php

namespace App\Http\Controllers\Concerns;

use App\Support\MediaLibraryService;
use Illuminate\Http\Request;

trait HandlesCmsData
{
    protected function uploadFile(Request $request, string $field, string $directory, ?string $current = null): ?string
    {
        if ($request->hasFile($field)) {
            $path = $request->file($field)->store($directory, 'public');
            MediaLibraryService::registerUploadedFile($request->file($field), $path, $directory);

            return $path;
        }

        $selectedPath = MediaLibraryService::normalizePath((string) $request->input($field . '_existing_path', ''));
        if ($selectedPath !== '') {
            MediaLibraryService::syncExistingFile($selectedPath, $directory);

            return $selectedPath;
        }

        return $current;
    }

    protected function uploadMultipleFiles(Request $request, string $field, string $directory, array $current = []): array
    {
        $selectedExisting = collect((array) $request->input($field . '_existing_paths', []))
            ->map(fn ($path) => MediaLibraryService::normalizePath((string) $path))
            ->filter()
            ->values()
            ->all();

        if (! $request->hasFile($field) && empty($selectedExisting)) {
            return $current;
        }

        $paths = $selectedExisting;

        foreach ($selectedExisting as $selectedPath) {
            MediaLibraryService::syncExistingFile($selectedPath, $directory);
        }

        foreach ((array) $request->file($field, []) as $file) {
            $path = $file->store($directory, 'public');
            MediaLibraryService::registerUploadedFile($file, $path, $directory);
            $paths[] = $path;
        }

        return array_values(array_unique($paths));
    }

    protected function mapLocalizedTextItems(?array $en, ?array $ar, string $key = 'text'): array
    {
        $rows = max(count($en ?? []), count($ar ?? []));
        $items = [];

        for ($i = 0; $i < $rows; $i++) {
            $item = [
                "{$key}_en" => trim($en[$i] ?? ''),
                "{$key}_ar" => trim($ar[$i] ?? ''),
            ];

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }

    protected function mapLocalizedBlocks(
        ?array $titleEn,
        ?array $titleAr,
        ?array $textEn,
        ?array $textAr,
        ?array $icon = null
    ): array {
        $rows = max(count($titleEn ?? []), count($titleAr ?? []), count($textEn ?? []), count($textAr ?? []), count($icon ?? []));
        $items = [];

        for ($i = 0; $i < $rows; $i++) {
            $item = [
                'title_en' => trim($titleEn[$i] ?? ''),
                'title_ar' => trim($titleAr[$i] ?? ''),
                'text_en' => trim($textEn[$i] ?? ''),
                'text_ar' => trim($textAr[$i] ?? ''),
            ];

            if ($icon !== null) {
                $item['icon'] = trim($icon[$i] ?? '');
            }

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }

    protected function mapFaqs(?array $questionEn, ?array $answerEn, ?array $questionAr, ?array $answerAr): array
    {
        $rows = max(count($questionEn ?? []), count($answerEn ?? []), count($questionAr ?? []), count($answerAr ?? []));
        $items = [];

        for ($i = 0; $i < $rows; $i++) {
            $item = [
                'question_en' => trim($questionEn[$i] ?? ''),
                'answer_en' => trim($answerEn[$i] ?? ''),
                'question_ar' => trim($questionAr[$i] ?? ''),
                'answer_ar' => trim($answerAr[$i] ?? ''),
            ];

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $items[] = $item;
        }

        return $items;
    }
}
