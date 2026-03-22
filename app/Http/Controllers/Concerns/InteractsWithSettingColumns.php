<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\Schema;

trait InteractsWithSettingColumns
{
    protected function filterExistingSettingColumns(array $data): array
    {
        static $columns = null;

        $columns ??= array_flip(Schema::getColumnListing('settings'));

        return array_intersect_key($data, $columns);
    }
}
