<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmTaskActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'crm_task_id',
        'user_id',
        'action_type',
        'old_value',
        'new_value',
        'note',
    ];

    protected $casts = [
        'crm_task_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(CrmTask::class, 'crm_task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function localizedAction(): string
    {
        $labels = [
            'created' => ['ar' => 'تم إنشاء المهمة', 'en' => 'Task created'],
            'updated_status' => ['ar' => 'تم تغيير الحالة', 'en' => 'Status changed'],
            'updated_assigned_user_id' => ['ar' => 'تم تغيير المسؤول', 'en' => 'Assignee changed'],
            'updated_due_at' => ['ar' => 'تم تغيير تاريخ الاستحقاق', 'en' => 'Due date changed'],
            'updated_priority' => ['ar' => 'تم تغيير الأولوية', 'en' => 'Priority changed'],
            'updated_title' => ['ar' => 'تم تعديل العنوان', 'en' => 'Title updated'],
            'updated_description' => ['ar' => 'تم تعديل الوصف', 'en' => 'Description updated'],
            'updated_notes' => ['ar' => 'تم تحديث الملاحظات', 'en' => 'Notes updated'],
            'updated_category' => ['ar' => 'تم تغيير التصنيف', 'en' => 'Category changed'],
            'updated_task_type' => ['ar' => 'تم تغيير النوع', 'en' => 'Task type changed'],
            'quick_note' => ['ar' => 'تمت إضافة ملاحظة', 'en' => 'Note added'],
        ];

        $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';

        return $labels[$this->action_type][$locale]
            ?? str($this->action_type)->replace('_', ' ')->headline()->toString();
    }
}
