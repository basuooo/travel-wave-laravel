<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingTreasury;
use App\Models\AccountingTreasuryTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountingTreasuryController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountingTreasury::query()
            ->with('creator')
            ->withSum(['transactions as incoming_total' => fn ($q) => $q->where('direction', AccountingTreasuryTransaction::DIRECTION_IN)], 'amount')
            ->withSum(['transactions as outgoing_total' => fn ($q) => $q->where('direction', AccountingTreasuryTransaction::DIRECTION_OUT)], 'amount')
            ->latest();

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('q')) {
            $search = trim($request->string('q')->toString());
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('identifier', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        return view('admin.accounting.treasuries.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'typeOptions' => AccountingTreasury::typeOptions(),
        ]);
    }

    public function create()
    {
        return view('admin.accounting.treasuries.create', [
            'item' => new AccountingTreasury(['is_active' => true, 'opening_balance' => 0]),
            'typeOptions' => AccountingTreasury::typeOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $treasury = AccountingTreasury::query()->create($data + [
            'created_by' => $request->user()->id,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.accounting.treasuries.show', $treasury)
            ->with('success', __('admin.accounting_treasury_added'));
    }

    public function show(Request $request, AccountingTreasury $treasury)
    {
        $transactions = $treasury->transactions()
            ->with(['creator', 'related'])
            ->when($request->filled('from'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->date('to')))
            ->paginate(20)
            ->withQueryString();

        $summaryQuery = $treasury->transactions()
            ->when($request->filled('from'), fn ($query) => $query->whereDate('transaction_date', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('transaction_date', '<=', $request->date('to')));

        $incoming = (float) (clone $summaryQuery)->where('direction', AccountingTreasuryTransaction::DIRECTION_IN)->sum('amount');
        $outgoing = (float) (clone $summaryQuery)->where('direction', AccountingTreasuryTransaction::DIRECTION_OUT)->sum('amount');

        return view('admin.accounting.treasuries.show', [
            'item' => $treasury->load('creator'),
            'transactions' => $transactions,
            'summary' => [
                'incoming' => round($incoming, 2),
                'outgoing' => round($outgoing, 2),
                'net' => round($incoming - $outgoing, 2),
                'current_balance' => $treasury->currentBalance(),
            ],
        ]);
    }

    public function edit(AccountingTreasury $treasury)
    {
        return view('admin.accounting.treasuries.edit', [
            'item' => $treasury,
            'typeOptions' => AccountingTreasury::typeOptions(),
        ]);
    }

    public function update(Request $request, AccountingTreasury $treasury)
    {
        $data = $this->validated($request);

        $treasury->update($data + [
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.accounting.treasuries.show', $treasury)
            ->with('success', __('admin.accounting_treasury_updated'));
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(AccountingTreasury::typeOptions()))],
            'identifier' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
