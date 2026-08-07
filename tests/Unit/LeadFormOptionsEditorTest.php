<?php

namespace Tests\Unit;

use Tests\TestCase;

class LeadFormOptionsEditorTest extends TestCase
{
    public function test_dual_tags_editor_preserves_empty_translation_values(): void
    {
        $view = view('admin.forms.partials.templates', [
            'fieldTypeOptions' => [],
            'assignmentTargets' => [],
            'positionOptions' => [],
        ])->render();

        $this->assertStringContainsString("if (!en) en = '';", $view);
        $this->assertStringContainsString("if (!ar) ar = '';", $view);
    }
}
