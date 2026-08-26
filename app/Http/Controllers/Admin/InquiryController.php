<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\LeadForm;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::query()
            ->where(function ($q) {
                $q->whereNotNull('lead_form_id')
                  ->orWhereNotNull('form_name');
            })
            ->with(['form', 'crmStatus', 'assignedUser', 'crmServiceType'])
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('form')) {
            $query->where('lead_form_id', $request->integer('form'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($sq) use ($search) {
                $sq->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('form_name', 'like', "%{$search}%");
            });
        }

        $formLeadsBaseQuery = fn () => Inquiry::query()->where(function ($q) {
            $q->whereNotNull('lead_form_id')
              ->orWhereNotNull('form_name');
        });

        return view('admin.inquiries.index', [
            'items' => $query->paginate(25)->withQueryString(),
            'forms' => LeadForm::query()->orderBy('name')->get(),
            'stats' => [
                'إجمالي فورـم لـيـد' => $formLeadsBaseQuery()->count(),
                'جديد' => $formLeadsBaseQuery()->where(fn ($q) => $q->where('status', 'new')->orWhereNull('status'))->count(),
                'تم التواصل' => $formLeadsBaseQuery()->where('status', 'contacted')->count(),
                'مغلق / تم الاتفاق' => $formLeadsBaseQuery()->where('status', 'closed')->count(),
            ],
        ]);
    }

    public function show(Inquiry $inquiry)
    {
        $inquiry->load(['form', 'crmStatus', 'assignedUser', 'crmServiceType', 'crmServiceSubtype']);

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'string'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $inquiry->update($data);

        return back()->with('success', 'تم تحديث الليد بنجاح.');
    }
}
