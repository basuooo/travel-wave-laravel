<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inquiry::query()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date('date'));
        }

        return view('admin.inquiries.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'stats' => [
                'all' => Inquiry::count(),
                'new' => Inquiry::where('status', 'new')->count(),
                'contacted' => Inquiry::where('status', 'contacted')->count(),
                'closed' => Inquiry::where('status', 'closed')->count(),
            ],
        ]);
    }

    public function show(Inquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,closed'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $inquiry->update($data);

        return back()->with('success', 'Inquiry updated.');
    }
}
