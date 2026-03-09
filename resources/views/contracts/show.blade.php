@extends('layouts.app')

@section('title', 'Contract  ' . $contract->contract_number)

@section('content')
@php
    $isAdmin = auth()->user()->role === 'admin';

    // Shared Tailwind class strings
    $inp = 'border border-slate-200 rounded px-2 py-1 text-sm w-full focus:outline-none focus:border-indigo-400 bg-white disabled:bg-slate-50 disabled:text-slate-400';
    $btnSave = 'px-4 py-1.5 text-sm rounded-md font-medium bg-indigo-600 text-white hover:bg-indigo-700';
    $btnAdd  = 'px-3 py-1.5 text-sm rounded-md font-medium bg-emerald-600 text-white hover:bg-emerald-700';
    $btnDel  = 'w-7 h-7 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 text-sm font-bold leading-none flex-shrink-0';
    $th      = 'px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wide whitespace-nowrap';
    $td      = 'px-3 py-1.5 align-top';
@endphp

{{-- â”€â”€ CONTRACT HEADER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
<div class="mb-5 flex items-center gap-3">
    <a href="{{ route('contracts.index') }}" class="text-slate-400 hover:text-slate-600 text-sm"> Contracts</a>
</div>
{{-- ── CONTRACT SECTION LABEL ── --}}
<div class="mb-3 flex items-center gap-3 ">
    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xl font-bold uppercase tracking-widest">Contract</span>
    <div class="flex-1 h-4 rounded-full bg-blue-200"></div>
</div>
<div class="p-6 mb-7 bg-blue-50 rounded-xl border border-blue-200 overflow-hidden">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $contract->contract_number }}</h1>
            <p class="text-slate-500 text-sm mt-0.5">{{ $contract->buyer_name ?: '' }} (<span class="font-semibold">Contract Signed: {{ $contract->contract_date?->format('d M Y') ?? '' }}</span> / <span class="font-semibold">Delivery Date: {{ $contract->delivery_date?->format('d M Y') ?? '' }}</span>)</p>
        </div>
        @if($isAdmin)
        <div class="flex gap-2">
            <a href="{{ route('contracts.edit', $contract) }}" class="px-3 py-1.5 text-sm rounded-md border border-slate-300 hover:bg-slate-50">Edit</a>
            <form method="POST" action="{{ route('contracts.destroy', $contract) }}" onsubmit="return confirm('Hapus contract ini beserta semua data terkait?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1.5 text-sm rounded-md bg-red-500 text-white hover:bg-red-600">Hapus Contract</button>
            </form>
        </div>
        @endif
    </div>
    <dl class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div><dt class="text-slate-400 text-xs">Inquiry Date (from buyer)</dt><dd class="font-medium">{{ $contract->rfq_from_buyer?->format('d M Y') ?? '-' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Inquiry Number (from buyer)</dt><dd class="font-medium">{{ $contract->rfq_number ?? '-' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Quotation Date (to buyer)</dt><dd class="font-medium">{{ $contract->quotation_to_buyer?->format('d M Y') ?? '-' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Quotation Number (to buyer)</dt><dd class="font-medium">{{ $contract->quotation_number ?? '-' }}</dd></div>
        <!-- <div><dt class="text-slate-400 text-xs">Contract Date</dt><dd class="font-medium">{{ $contract->contract_date?->format('d M Y') ?? '' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Delivery Date</dt><dd class="font-medium">{{ $contract->delivery_date?->format('d M Y') ?? '' }}</dd></div> -->
    </dl>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 1 CONTRACT PAYMENT TERMS
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}


<div class="bg-blue-50 rounded-xl border border-blue-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-blue-100 bg-blue-100 flex justify-between items-center">
        <h2 class="font-semibold text-blue-800">Contract Payment Terms <span class="text-xs font-normal text-blue-400 ml-1">(buyer side)</span></h2>
        <span class="text-xs text-blue-400">{{ $contract->contractPaymentTerms->count() }} baris</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.contract-payment-terms.upsert', $contract) }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Term Code</th>
                        <th class="{{ $th }}">% (persen)</th>
                        <th class="{{ $th }}">No. Invoice</th>
                        <th class="{{ $th }}">Tgl Invoice</th>
                        <th class="{{ $th }}">Tgl Bayar</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="cpt-tbody">
                @foreach($contract->contractPaymentTerms as $i => $cpt)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][term_code]" value="{{ $cpt->term_code }}" placeholder="DP, P1, P2"><input type="hidden" name="items[{{ $i }}][id]" value="{{ $cpt->id }}" data-row-id></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="items[{{ $i }}][percentage]" value="{{ $cpt->percentage }}" placeholder="0100"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][invoice_number]" value="{{ $cpt->invoice_number }}" placeholder="INV-xxx"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][invoice_date]" value="{{ $cpt->invoice_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][paid_date]" value="{{ $cpt->paid_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 flex gap-2 border-t border-slate-100">
            <button type="button" onclick="addRow('cpt-tbody','cpt-row-tpl',counters,'cpt')" class="{{ $btnAdd }}">+ Tambah Baris</button>
            <button type="submit" class="{{ $btnSave }}">Simpan</button>
        </div>
    </form>

    {{-- Template for new CPT row --}}
    <template id="cpt-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][term_code]" placeholder="DP, P1, P2"><input type="hidden" name="items[__IDX__][id]" value="" data-row-id></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="items[__IDX__][percentage]" placeholder="0100"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][invoice_number]" placeholder="INV-xxx"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][invoice_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][paid_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
        </tr>
    </template>

    @else
    {{-- Read-only view --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100"><tr>
                <th class="{{ $th }}">Term</th><th class="{{ $th }}">%</th><th class="{{ $th }}">Invoice No</th><th class="{{ $th }}">Tgl Invoice</th><th class="{{ $th }}">Tgl Bayar</th>
            </tr></thead>
            <tbody>
            @forelse($contract->contractPaymentTerms as $cpt)
            <tr class="border-b border-slate-50"><td class="{{ $td }}">{{ $cpt->term_code ?: '' }}</td><td class="{{ $td }}">{{ $cpt->percentage !== null ? $cpt->percentage.'%' : '' }}</td><td class="{{ $td }}">{{ $cpt->invoice_number ?: '' }}</td><td class="{{ $td }}">{{ $cpt->invoice_date?->format('d/m/Y') ?? '' }}</td><td class="{{ $td }}">{{ $cpt->paid_date?->format('d/m/Y') ?? '' }}</td></tr>
            @empty
            <tr><td colspan="5" class="px-4 py-4 text-center text-slate-400 text-sm">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
{{-- ═══════════════════════════════════════════════════════════════════════
     SECTION 6 – BG NUMBERS
═══════════════════════════════════════════════════════════════════════ --}}
<div class="bg-blue-50 rounded-xl border border-blue-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-blue-100 bg-blue-100 flex justify-between items-center">
        <h2 class="font-semibold text-blue-800">BG Number</h2>
        <span class="text-xs text-blue-400">{{ $contract->bgNumbers->count() }} baris</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.bg-numbers.upsert', $contract) }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Number</th>
                        <th class="{{ $th }}">Periode</th>
                        <th class="{{ $th }}">Start Date</th>
                        <th class="{{ $th }}">End Date</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="bg-tbody">
                @foreach($contract->bgNumbers as $i => $bg)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][number]" value="{{ $bg->number }}" placeholder="No. BG"><input type="hidden" name="items[{{ $i }}][id]" value="{{ $bg->id }}" data-row-id></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][periode]" value="{{ $bg->periode }}" placeholder="misal: 12 bulan"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][start_date]" value="{{ $bg->start_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][end_date]" value="{{ $bg->end_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 flex gap-2 border-t border-slate-100">
            <button type="button" onclick="addRow('bg-tbody','bg-row-tpl',counters,'bg')" class="{{ $btnAdd }}">+ Tambah Baris</button>
            <button type="submit" class="{{ $btnSave }}">Simpan</button>
        </div>
    </form>

    <template id="bg-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][number]" placeholder="No. BG"><input type="hidden" name="items[__IDX__][id]" value="" data-row-id></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][periode]" placeholder="misal: 12 bulan"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][start_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][end_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
        </tr>
    </template>

    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100"><tr>
                <th class="{{ $th }}">Number</th><th class="{{ $th }}">Periode</th><th class="{{ $th }}">Start Date</th><th class="{{ $th }}">End Date</th>
            </tr></thead>
            <tbody>
            @forelse($contract->bgNumbers as $bg)
            <tr class="border-b border-slate-50"><td class="{{ $td }}">{{ $bg->number ?: '' }}</td><td class="{{ $td }}">{{ $bg->periode ?: '' }}</td><td class="{{ $td }}">{{ $bg->start_date?->format('d/m/Y') ?? '' }}</td><td class="{{ $td }}">{{ $bg->end_date?->format('d/m/Y') ?? '' }}</td></tr>
            @empty
            <tr><td colspan="4" class="px-4 py-4 text-center text-slate-400 text-sm">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     SECTION 7 – SURETY BONDS
═══════════════════════════════════════════════════════════════════════ --}}
<div class="bg-blue-50 rounded-xl border border-blue-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-blue-100 bg-blue-100 flex justify-between items-center">
        <h2 class="font-semibold text-blue-800">Surety Bond</h2>
        <span class="text-xs text-blue-400">{{ $contract->suretyBonds->count() }} baris</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.surety-bonds.upsert', $contract) }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Number</th>
                        <th class="{{ $th }}">Periode</th>
                        <th class="{{ $th }}">Start Date</th>
                        <th class="{{ $th }}">End Date</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="sb-tbody">
                @foreach($contract->suretyBonds as $i => $sb)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][number]" value="{{ $sb->number }}" placeholder="No. Surety Bond"><input type="hidden" name="items[{{ $i }}][id]" value="{{ $sb->id }}" data-row-id></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][periode]" value="{{ $sb->periode }}" placeholder="misal: 12 bulan"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][start_date]" value="{{ $sb->start_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][end_date]" value="{{ $sb->end_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 flex gap-2 border-t border-slate-100">
            <button type="button" onclick="addRow('sb-tbody','sb-row-tpl',counters,'sb')" class="{{ $btnAdd }}">+ Tambah Baris</button>
            <button type="submit" class="{{ $btnSave }}">Simpan</button>
        </div>
    </form>

    <template id="sb-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][number]" placeholder="No. Surety Bond"><input type="hidden" name="items[__IDX__][id]" value="" data-row-id></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][periode]" placeholder="misal: 12 bulan"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][start_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][end_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
        </tr>
    </template>

    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100"><tr>
                <th class="{{ $th }}">Number</th><th class="{{ $th }}">Periode</th><th class="{{ $th }}">Start Date</th><th class="{{ $th }}">End Date</th>
            </tr></thead>
            <tbody>
            @forelse($contract->suretyBonds as $sb)
            <tr class="border-b border-slate-50"><td class="{{ $td }}">{{ $sb->number ?: '' }}</td><td class="{{ $td }}">{{ $sb->periode ?: '' }}</td><td class="{{ $td }}">{{ $sb->start_date?->format('d/m/Y') ?? '' }}</td><td class="{{ $td }}">{{ $sb->end_date?->format('d/m/Y') ?? '' }}</td></tr>
            @empty
            <tr><td colspan="4" class="px-4 py-4 text-center text-slate-400 text-sm">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

@php
    $rfqs = $contract->rfqs;
    $quotations = $contract->quotations;
    // Pair RFQs & Quotations by maker (case-insensitive)
    $rfqsByMaker  = $rfqs->groupBy(fn($r) => strtolower(trim($r->maker ?? '')));
    $quosByMaker  = $quotations->groupBy(fn($q) => strtolower(trim($q->maker_name ?? '')));
    $allMakerKeys = $rfqsByMaker->keys()->merge($quosByMaker->keys())->unique()->values();
    $iqRows = collect();
    foreach ($allMakerKeys as $mk) {
        $mRfqs = $rfqsByMaker->get($mk, collect());
        $mQuos = $quosByMaker->get($mk, collect());
        foreach (range(0, max($mRfqs->count(), $mQuos->count()) - 1) as $j) {
            $iqRows->push(['rfq' => $mRfqs->get($j), 'quo' => $mQuos->get($j)]);
        }
    }
    $iqRowCount = $iqRows->count();
    $posByRfq = $contract->purchaseOrders->groupBy('rfq_id');
@endphp

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 2 â€” Procurement & Expedite
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
{{-- ── PROCUREMENT & EXPEDITE SECTION LABEL ── --}}
<div class="mb-3 mt-6 flex items-center gap-3">
    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xl font-bold uppercase tracking-widest">Procurement &amp; Expedite</span>
    <div class="flex-1 h-4 rounded-full bg-yellow-300"></div>
</div>

<div class="bg-yellow-50 rounded-xl border border-yellow-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-yellow-100 bg-yellow-100 flex justify-between items-center">
        <h2 class="font-semibold text-yellow-800">Procurement &amp; Expedite</h2>
        <span class="text-xs text-yellow-500">{{ $iqRowCount }} baris</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.rfqs.upsert', $contract) }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Maker</th>
                        <th class="{{ $th }}">Inquiry Number</th>
                        <th class="{{ $th }}">Inquiry Date</th>
                        <th class="{{ $th }}">Quotation Number</th>
                        <th class="{{ $th }}">Quotation Date</th>
                        <th class="{{ $th }}">PO Number</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="iq-tbody">
                @foreach($iqRows as $i => $iqRow)
                    @php
                        $rfq = $iqRow['rfq'];
                        $quo = $iqRow['quo'];
                        $makerValue = optional($rfq)->maker ?? optional($quo)->maker_name ?? '';
                        $makerToken = 'iq-' . $i;
                        $rfqDate = optional(optional($rfq)->rfq_date)->format('Y-m-d');
                        $quoDate = optional(optional($quo)->quotation_date)->format('Y-m-d');
                        $linkedPos = $rfq ? ($posByRfq->get($rfq->id) ?? collect()) : collect();
                    @endphp
                    <tr class="border-b border-slate-50 hover:bg-slate-50" data-maker-row>
                        <td class="{{ $td }}">
                            <input class="{{ $inp }}" type="text" name="items[{{ $i }}][maker]" value="{{ $makerValue }}" placeholder="Nama maker" data-maker-sync="{{ $makerToken }}">
                            <input type="hidden" name="items[{{ $i }}][id]" value="{{ optional($rfq)->id }}" data-row-id>
                            <input type="hidden" name="quotations[{{ $i }}][id]" value="{{ optional($quo)->id }}" data-row-id>
                            <input type="hidden" name="quotations[{{ $i }}][maker_name]" value="{{ optional($quo)->maker_name ?? $makerValue }}" data-maker-sync-target="{{ $makerToken }}">
                        </td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][rfq_number]" value="{{ optional($rfq)->rfq_number }}" placeholder="INQ-xxx"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][rfq_date]" value="{{ $rfqDate }}"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="quotations[{{ $i }}][quotation_number]" value="{{ optional($quo)->quotation_number }}" placeholder="QUO-xxx"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="quotations[{{ $i }}][quotation_date]" value="{{ $quoDate }}"></td>
                        <td class="{{ $td }}">
                            @forelse($linkedPos as $lpo)
                            <button type="button" onclick="openPoModal({{ $lpo->id }})" class="inline-block px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 text-xs font-medium mr-1 mb-0.5 hover:bg-indigo-200 cursor-pointer">{{ $lpo->po_number }}</button>
                            @empty<span class="text-slate-300 text-xs">—</span>@endforelse
                        </td>
                        <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 flex gap-2 border-t border-slate-100">
            <button type="button" onclick="addRow('iq-tbody','iq-row-tpl',counters,'iq')" class="{{ $btnAdd }}">+ Tambah Baris</button>
            <button type="submit" class="{{ $btnSave }}">Simpan</button>
        </div>
    </form>

    <template id="iq-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50" data-maker-row>
            <td class="{{ $td }}">
                <input class="{{ $inp }}" type="text" name="items[__IDX__][maker]" placeholder="Nama maker" data-maker-sync="iq-__IDX__">
                <input type="hidden" name="items[__IDX__][id]" value="" data-row-id>
                <input type="hidden" name="quotations[__IDX__][id]" value="" data-row-id>
                <input type="hidden" name="quotations[__IDX__][maker_name]" data-maker-sync-target="iq-__IDX__">
            </td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][rfq_number]" placeholder="INQ-xxx"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][rfq_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="quotations[__IDX__][quotation_number]" placeholder="QUO-xxx"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="quotations[__IDX__][quotation_date]"></td>
            <td class="{{ $td }}"><span class="text-slate-300 text-xs">—</span></td>
            <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
        </tr>
    </template>

    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100"><tr>
                <th class="{{ $th }}">Maker</th>
                <th class="{{ $th }}">Inquiry Number</th>
                <th class="{{ $th }}">Inquiry Date</th>
                <th class="{{ $th }}">Quotation Number</th>
                <th class="{{ $th }}">Quotation Date</th>
                <th class="{{ $th }}">PO Number</th>
            </tr></thead>
            <tbody>
            @if($iqRowCount > 0)
                @foreach($iqRows as $i => $iqRow)
                    @php
                        $rfq = $iqRow['rfq'];
                        $quo = $iqRow['quo'];
                        $makerValue = optional($rfq)->maker ?? optional($quo)->maker_name ?? '';
                    @endphp
                    <tr class="border-b border-slate-50">
                        <td class="{{ $td }}">{{ $makerValue }}</td>
                        <td class="{{ $td }}">{{ $rfq?->rfq_number ?? '' }}</td>
                        <td class="{{ $td }}">{{ $rfq?->rfq_date?->format('d/m/Y') ?? '' }}</td>
                        <td class="{{ $td }}">{{ $quo?->quotation_number ?? '' }}</td>
                        <td class="{{ $td }}">{{ $quo?->quotation_date?->format('d/m/Y') ?? '' }}</td>
                        <td class="{{ $td }}">
                            @php $roPos = $rfq ? ($posByRfq->get($rfq->id) ?? collect()) : collect(); @endphp
                            @forelse($roPos as $lpo)
                            <button type="button" onclick="openPoModal({{ $lpo->id }})" class="inline-block px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 text-xs font-medium mr-1 mb-0.5 hover:bg-indigo-200 cursor-pointer">{{ $lpo->po_number }}</button>
                            @empty<span class="text-slate-300 text-xs">—</span>@endforelse
                        </td>
                    </tr>
                @endforeach
            @else
                <tr><td colspan="6" class="px-4 py-4 text-center text-slate-400 text-sm">Belum ada data</td></tr>
            @endif
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 4 â€” PURCHASE ORDERS (cards layout)
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-yellow-50 rounded-xl border border-yellow-200 mb-6 overflow-hidden hidden">
    <div class="px-6 py-3 border-b border-yellow-100 bg-yellow-100 flex justify-between items-center">
        <h2 class="font-semibold text-yellow-800">Purchase Orders</h2>
        <span class="text-xs text-yellow-500">{{ $contract->purchaseOrders->count() }} PO</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.purchase-orders.upsert', $contract) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div id="po-cards" class="divide-y divide-slate-100">
        @foreach($contract->purchaseOrders as $i => $po)
        <div class="p-5" data-po-card>
            {{-- Hidden ID --}}
            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $po->id }}" data-row-id>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">PO #{{ $i + 1 }}</span>
                <button type="button" onclick="removePoCard(this)" class="{{ $btnDel }}" title="Hapus PO ini">-</button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-3">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Number <span class="text-red-400">*</span></label>
                    <input class="{{ $inp }}" type="text" name="items[{{ $i }}][po_number]" value="{{ $po->po_number }}" placeholder="PO-xxx" required>
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Date</label>
                    <input class="{{ $inp }}" type="date" name="items[{{ $i }}][po_date]" value="{{ $po->po_date?->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Delivery Date</label>
                    <input class="{{ $inp }}" type="date" name="items[{{ $i }}][exact_delivery_date]" value="{{ $po->exact_delivery_date?->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Linked Inquiry</label>
                    <select class="{{ $inp }}" name="items[{{ $i }}][rfq_id]">
                        <option value="">— Tidak ada —</option>
                        @foreach($rfqs as $rfqOpt)
                        <option value="{{ $rfqOpt->id }}" {{ $po->rfq_id == $rfqOpt->id ? 'selected' : '' }}>{{ $rfqOpt->rfq_number ?: ('INQ #'.$rfqOpt->id) }}{{ $rfqOpt->maker ? ' — '.$rfqOpt->maker : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="rounded-lg border border-slate-100 overflow-hidden mb-4">
                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">WIP Progress</span>
                    <button type="button" onclick="addWipRow(this,'{{ $i }}')" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+ Tambah</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="border-b border-slate-100">
                            <tr>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Progress (%)</th>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Tanggal</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody data-wip-tbody="{{ $i }}">
                        @forelse($po->wipStatuses->sortBy('percentage') as $wi => $wip)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="px-3 py-1.5"><select class="{{ $inp }}" name="items[{{ $i }}][wip_statuses][{{ $wi }}][percentage]"><option value="">—</option><option value="25" {{ $wip->percentage == 25 ? 'selected' : '' }}>25%</option><option value="50" {{ $wip->percentage == 50 ? 'selected' : '' }}>50%</option><option value="75" {{ $wip->percentage == 75 ? 'selected' : '' }}>75%</option><option value="100" {{ $wip->percentage == 100 ? 'selected' : '' }}>100%</option></select></td>
                            <td class="px-3 py-1.5"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][wip_statuses][{{ $wi }}][status_date]" value="{{ $wip->status_date?->format('Y-m-d') }}"></td>
                            <td class="px-3 py-1.5"><button type="button" onclick="this.closest('tr').remove()" class="w-6 h-6 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 text-xs font-bold">×</button></td>
                        </tr>
                        @empty
                        <tr data-wip-empty><td colspan="3" class="px-3 py-2 text-center text-slate-400 italic">Belum ada progress</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Expedite sub-section --}}
            <div class="rounded-lg border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Expedite</span>
                </div>
                <div class="p-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Dimension</label>
                        <input class="{{ $inp }}" type="text" name="items[{{ $i }}][dimension]" value="{{ $po->dimension }}" placeholder="pxlxt cm">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Weight</label>
                        <input class="{{ $inp }}" type="text" name="items[{{ $i }}][weight]" value="{{ $po->weight }}" placeholder="kg">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Incoterm</label>
                        <input class="{{ $inp }}" type="text" name="items[{{ $i }}][incoterm]" value="{{ $po->incoterm }}" placeholder="FOB, CIF">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Delivered Date</label>
                        <input class="{{ $inp }}" type="date" name="items[{{ $i }}][delivered_date]" value="{{ $po->delivered_date?->format('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Expedite Note</label>
                        <input class="{{ $inp }}" type="text" name="items[{{ $i }}][expedite]" value="{{ $po->expedite }}" placeholder="Catatan expedite">
                    </div>
                    <div class="col-span-2 sm:col-span-3 lg:col-span-3">
                        <label class="text-xs text-slate-400 mb-0.5 block">Shipping Documents</label>
                        @foreach($po->shippingDocuments as $doc)
                        <div class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="items[{{ $i }}][delete_shipping_docs][]" value="{{ $doc->id }}" class="accent-red-500" id="del-doc-{{ $doc->id }}">
                            <label for="del-doc-{{ $doc->id }}" class="text-xs text-red-400 cursor-pointer">Hapus</label>
                            <a href="{{ $doc->url }}" target="_blank" class="text-xs text-sky-600 hover:underline truncate">{{ $doc->name }}</a>
                        </div>
                        @endforeach
                        <div class="space-y-1" data-sdoc-rows>
                            <div class="flex gap-1 items-center sdoc-row">
                                <input class="{{ $inp }} flex-1" type="text" name="items[{{ $i }}][new_shipping_docs][0][name]" placeholder="Nama dokumen (B/L, Packing List…)">
                                <input class="{{ $inp }} flex-1" type="file" name="items[{{ $i }}][new_shipping_docs][0][file]">
                                <button type="button" onclick="addSdocRow(this,'{{ $i }}')" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex gap-2">
            <button type="button" onclick="addPoCard()" class="{{ $btnAdd }}">+ Tambah PO</button>
            <button type="submit" class="{{ $btnSave }}">Simpan Semua PO</button>
        </div>
    </form>

    {{-- Template for a new PO card --}}
    <template id="po-card-tpl">
        <div class="p-5 border-t border-slate-100" data-po-card>
            <input type="hidden" name="items[__IDX__][id]" value="" data-row-id>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">PO baru</span>
                <button type="button" onclick="removePoCard(this)" class="{{ $btnDel }}" title="Hapus">-</button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-3">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Number <span class="text-red-400">*</span></label>
                    <input class="{{ $inp }}" type="text" name="items[__IDX__][po_number]" placeholder="PO-xxx" required>
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Date</label>
                    <input class="{{ $inp }}" type="date" name="items[__IDX__][po_date]">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Delivery Date</label>
                    <input class="{{ $inp }}" type="date" name="items[__IDX__][exact_delivery_date]">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Linked Inquiry</label>
                    <select class="{{ $inp }}" name="items[__IDX__][rfq_id]">
                        <option value="">— Tidak ada —</option>
                        @foreach($rfqs as $rfqOpt)
                        <option value="{{ $rfqOpt->id }}">{{ $rfqOpt->rfq_number ?: ('INQ #'.$rfqOpt->id) }}{{ $rfqOpt->maker ? ' — '.$rfqOpt->maker : '' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="rounded-lg border border-slate-100 overflow-hidden mb-4">
                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">WIP Progress</span>
                    <button type="button" onclick="addWipRow(this,'__IDX__')" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+ Tambah</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead class="border-b border-slate-100">
                            <tr>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Progress (%)</th>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Tanggal</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody data-wip-tbody="__IDX__">
                            <tr data-wip-empty><td colspan="3" class="px-3 py-2 text-center text-slate-400 italic">Belum ada progress</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Expedite sub-section --}}
            <div class="rounded-lg border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Expedite</span>
                </div>
                <div class="p-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Dimension</label>
                        <input class="{{ $inp }}" type="text" name="items[__IDX__][dimension]" placeholder="pxlxt cm">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Weight</label>
                        <input class="{{ $inp }}" type="text" name="items[__IDX__][weight]" placeholder="kg">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Incoterm</label>
                        <input class="{{ $inp }}" type="text" name="items[__IDX__][incoterm]" placeholder="FOB, CIF">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Delivered Date</label>
                        <input class="{{ $inp }}" type="date" name="items[__IDX__][delivered_date]">
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 mb-0.5 block">Expedite Note</label>
                        <input class="{{ $inp }}" type="text" name="items[__IDX__][expedite]" placeholder="Catatan expedite">
                    </div>
                    <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                        <label class="text-xs text-slate-400 mb-0.5 block">Shipping Documents</label>
                        <div class="space-y-1" data-sdoc-rows>
                            <div class="flex gap-1 items-center sdoc-row">
                                <input class="{{ $inp }} flex-1" type="text" name="items[__IDX__][new_shipping_docs][0][name]" placeholder="Nama dokumen (B/L, Packing List…)">
                                <input class="{{ $inp }} flex-1" type="file" name="items[__IDX__][new_shipping_docs][0][file]">
                                <button type="button" onclick="addSdocRow(this,'__IDX__')" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @else
    {{-- Read-only PO list --}}
    @forelse($contract->purchaseOrders as $po)
    <button type="button" onclick="openPoModal({{ $po->id }})" class="w-full text-left px-5 py-3 border-b border-slate-100 last:border-b-0 hover:bg-slate-50 transition-colors flex items-center justify-between group">
        <div>
            <p class="font-semibold text-slate-800 text-sm">{{ $po->po_number }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $po->rfq?->maker ?: '' }}{{ $po->rfq?->maker && $po->po_date ? ' · ' : '' }}{{ $po->po_date?->format('d/m/Y') ?? '' }}</p>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </button>
    @empty
    <div class="px-6 py-4 text-center text-slate-400 text-sm">Belum ada PO</div>
    @endforelse
    @endif
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 5 â€” MAKER PAYMENT TERMS (one form per existing PO)
     Note: only shown for existing POs. New POs need to be saved first.
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
@if($contract->purchaseOrders->count() > 0)
<div class="bg-yellow-50 rounded-xl border border-yellow-200 mb-6 overflow-hidden hidden">
    <div class="px-6 py-3 border-b border-yellow-100 bg-yellow-100">
        <h2 class="font-semibold text-yellow-800">Maker Payment Terms</h2>
        <p class="text-xs text-yellow-500 mt-0.5">Per PO  simpan masing-masing secara terpisah</p>
    </div>

    @foreach($contract->purchaseOrders as $po)
    <div class="border-b border-slate-100 last:border-b-0">
        <div class="px-6 py-2 bg-slate-50 border-b border-slate-100">
            <span class="text-sm font-medium text-slate-700">{{ $po->po_number }}</span>
            <span class="ml-2 text-xs text-slate-400">{{ $po->makerPaymentTerms->count() }} terms</span>
        </div>

        @if($isAdmin)
        <form method="POST" action="{{ route('contracts.purchase-orders.maker-payment-terms.upsert', [$contract, $po]) }}">
            @csrf @method('PUT')
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-50">
                        <tr>
                            <th class="{{ $th }}">Term Code</th>
                            <th class="{{ $th }}">%</th>
                            <th class="{{ $th }}">No. Invoice</th>
                            <th class="{{ $th }}">Tgl Invoice</th>
                            <th class="{{ $th }}">Tgl Bayar</th>
                            <th class="px-3 py-2 w-8"></th>
                        </tr>
                    </thead>
                    <tbody id="mpt-tbody-{{ $po->id }}">
                    @foreach($po->makerPaymentTerms as $j => $mpt)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $j }}][term_code]" value="{{ $mpt->term_code }}" placeholder="DP, P1"><input type="hidden" name="items[{{ $j }}][id]" value="{{ $mpt->id }}" data-row-id></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="items[{{ $j }}][percentage]" value="{{ $mpt->percentage }}" placeholder="0100"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $j }}][invoice_number]" value="{{ $mpt->invoice_number }}" placeholder="INV-xxx"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $j }}][invoice_date]" value="{{ $mpt->invoice_date?->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $j }}][paid_date]" value="{{ $mpt->paid_date?->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 flex gap-2 border-t border-slate-50">
                <button type="button"
                    onclick="addMptRow('mpt-tbody-{{ $po->id }}','mpt-row-tpl-{{ $po->id }}',mptCounters,{{ $po->id }})"
                    class="{{ $btnAdd }}">+ Tambah Baris</button>
                <button type="submit" class="{{ $btnSave }}">Simpan</button>
            </div>
        </form>

        <template id="mpt-row-tpl-{{ $po->id }}">
            <tr class="border-b border-slate-50 hover:bg-slate-50">
                <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][term_code]" placeholder="DP, P1"><input type="hidden" name="items[__IDX__][id]" value="" data-row-id></td>
                <td class="{{ $td }}"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="items[__IDX__][percentage]" placeholder="0100"></td>
                <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][invoice_number]" placeholder="INV-xxx"></td>
                <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][invoice_date]"></td>
                <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][paid_date]"></td>
                <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
            </tr>
        </template>

        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-50"><tr>
                    <th class="{{ $th }}">Term</th><th class="{{ $th }}">%</th><th class="{{ $th }}">Invoice No</th><th class="{{ $th }}">Tgl Invoice</th><th class="{{ $th }}">Tgl Bayar</th>
                </tr></thead>
                <tbody>
                @forelse($po->makerPaymentTerms as $mpt)
                <tr class="border-b border-slate-50"><td class="{{ $td }}">{{ $mpt->term_code ?: '' }}</td><td class="{{ $td }}">{{ $mpt->percentage !== null ? $mpt->percentage.'%' : '' }}</td><td class="{{ $td }}">{{ $mpt->invoice_number ?: '' }}</td><td class="{{ $td }}">{{ $mpt->invoice_date?->format('d/m/Y') ?? '' }}</td><td class="{{ $td }}">{{ $mpt->paid_date?->format('d/m/Y') ?? '' }}</td></tr>
                @empty
                <tr><td colspan="5" class="px-4 py-3 text-center text-slate-400 text-sm">Belum ada terms</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- ── PO Detail Modal ─────────────────────────────────────────────────── --}}
<div id="po-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <h3 class="font-semibold text-slate-800" id="po-modal-title">Detail Purchase Order</h3>
            <button onclick="document.getElementById('po-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
        </div>
        <div id="po-modal-body" class="p-6 overflow-y-auto text-sm text-slate-700"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// â”€â”€ Row counters (start after existing records) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const counters = {
    cpt: {{ $contract->contractPaymentTerms->count() }},
    iq: {{ $iqRowCount }},
    po:  {{ $contract->purchaseOrders->count() }},
    bg:  {{ $contract->bgNumbers->count() }},
    sb:  {{ $contract->suretyBonds->count() }},
};

const mptCounters = {
    @foreach($contract->purchaseOrders as $po)
    {{ $po->id }}: {{ $po->makerPaymentTerms->count() }},
    @endforeach
};

// â”€â”€ Add a new row to a simple <tbody> â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function addRow(tbodyId, templateId, countersObj, key) {
    const tbody = document.getElementById(tbodyId);
    const tpl   = document.getElementById(templateId);
    if (!tbody || !tpl) return;
    const idx  = countersObj[key]++;
    const html = tpl.innerHTML.replace(/__IDX__/g, idx);
    tbody.insertAdjacentHTML('beforeend', html);
    tbody.lastElementChild?.querySelector('input:not([type=hidden])')?.focus();
}

// â”€â”€ Add a maker-term row (uses mptCounters keyed by poId) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function addMptRow(tbodyId, templateId, mptCountersObj, poId) {
    const tbody = document.getElementById(tbodyId);
    const tpl   = document.getElementById(templateId);
    if (!tbody || !tpl) return;
    const idx  = mptCountersObj[poId]++;
    const html = tpl.innerHTML.replace(/__IDX__/g, idx);
    tbody.insertAdjacentHTML('beforeend', html);
    tbody.lastElementChild?.querySelector('input:not([type=hidden])')?.focus();
}

// â”€â”€ Remove / undo a simple table row â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function removeRow(btn) {
    const tr = btn.closest('tr');
    const idInputs = Array.from(tr.querySelectorAll('[data-row-id]')).filter(input => input.value);

    if (idInputs.length > 0) {
        const isMarked = tr.dataset.markedDelete === '1';
        if (isMarked) {
            delete tr.dataset.markedDelete;
            tr.classList.remove('opacity-40');
            tr.querySelectorAll('input[data-del-flag]').forEach(el => el.remove());
            tr.querySelectorAll('input:not([data-row-id]):not([type=hidden]), textarea, select')
                .forEach(el => el.disabled = false);
            btn.textContent = '-';
            btn.title = 'Hapus baris ini';
        } else {
            tr.dataset.markedDelete = '1';
            tr.classList.add('opacity-40');
            idInputs.forEach(idInput => {
                const del = document.createElement('input');
                del.type  = 'hidden';
                del.name  = idInput.name.replace('[id]', '[_delete]');
                del.value = '1';
                del.dataset.delFlag = '';
                tr.appendChild(del);
            });
            tr.querySelectorAll('input:not([data-row-id]):not([type=hidden]), textarea, select')
                .forEach(el => el.disabled = true);
            btn.textContent = 'Undo';
            btn.title = 'Batalkan hapus';
        }
    } else {
        tr.remove();
    }
}

// â”€â”€ Add a new PO card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// ── Add a shipping-doc row ─────────────────────────────────────────────────
function addSdocRow(btn, idx) {
    const wrapper = btn.closest('[data-sdoc-rows]');
    const rowCount = wrapper.querySelectorAll('.sdoc-row').length;
    const div = document.createElement('div');
    div.className = 'flex gap-1 items-center sdoc-row';
    div.innerHTML = `<input class="border border-slate-300 rounded px-2 py-1 text-sm w-full flex-1" type="text" name="items[${idx}][new_shipping_docs][${rowCount}][name]" placeholder="Nama dokumen">
        <input class="border border-slate-300 rounded px-2 py-1 text-sm flex-1" type="file" name="items[${idx}][new_shipping_docs][${rowCount}][file]">
        <button type="button" onclick="this.closest('.sdoc-row').remove()" class="text-xs px-2 py-1 rounded bg-red-100 text-red-500 hover:bg-red-200 font-medium">&times;</button>`;
    btn.closest('.sdoc-row').after(div);
}

function addPoCard() {
    const container = document.getElementById('po-cards');
    const tpl       = document.getElementById('po-card-tpl');
    if (!container || !tpl) return;
    const idx  = counters.po++;
    const html = tpl.innerHTML.replace(/__IDX__/g, idx);
    container.insertAdjacentHTML('beforeend', html);
    container.lastElementChild?.querySelector('input:not([type=hidden])')?.focus();
}

// â”€â”€ Remove / undo a PO card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function removePoCard(btn) {
    const card    = btn.closest('[data-po-card]');
    const idInput = card.querySelector('[data-row-id]');

    if (idInput?.value) {
        const isMarked = card.dataset.markedDelete === '1';
        if (isMarked) {
            delete card.dataset.markedDelete;
            card.classList.remove('opacity-40');
            card.querySelectorAll('input[data-del-flag]').forEach(el => el.remove());
            card.querySelectorAll('input:not([data-row-id]):not([type=hidden]), textarea, select')
                .forEach(el => el.disabled = false);
            btn.textContent = '-';
            btn.title = 'Hapus PO ini';
        } else {
            card.dataset.markedDelete = '1';
            card.classList.add('opacity-40');
            const del = document.createElement('input');
            del.type  = 'hidden';
            del.name  = idInput.name.replace('[id]', '[_delete]');
            del.value = '1';
            del.dataset.delFlag = '';
            card.appendChild(del);
            card.querySelectorAll('input:not([data-row-id]):not([type=hidden]), textarea, select')
                .forEach(el => el.disabled = true);
            btn.textContent = 'â†©';
            btn.title = 'Batalkan hapus';
        }
    } else {
        card.remove();
    }
}

function addWipRow(btn, poIdx) {
    const tbody = btn.closest('.rounded-lg').querySelector('[data-wip-tbody]');
    if (!tbody) return;
    const emptyRow = tbody.querySelector('[data-wip-empty]');
    if (emptyRow) emptyRow.style.display = 'none';
    const rowCount = tbody.querySelectorAll('tr:not([data-wip-empty])').length;
    const tr = document.createElement('tr');
    tr.className = 'border-b border-slate-50 hover:bg-slate-50';
    tr.innerHTML = `<td class="px-3 py-1.5"><select class="border border-slate-200 rounded px-2 py-1 text-sm w-full focus:outline-none focus:border-indigo-400 bg-white" name="items[${poIdx}][wip_statuses][${rowCount}][percentage]"><option value="">—</option><option value="25">25%</option><option value="50">50%</option><option value="75">75%</option><option value="100">100%</option></select></td>
        <td class="px-3 py-1.5"><input class="border border-slate-200 rounded px-2 py-1 text-sm w-full focus:outline-none focus:border-indigo-400 bg-white" type="date" name="items[${poIdx}][wip_statuses][${rowCount}][status_date]"></td>
        <td class="px-3 py-1.5"><button type="button" onclick="this.closest('tr').remove()" class="w-6 h-6 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 text-xs font-bold">&times;</button></td>`;
    tbody.appendChild(tr);
    tr.querySelector('input')?.focus();
}

function syncMakerTargets(input) {
    const token = input.dataset.makerSync;
    if (!token) return;
    const container = input.closest('[data-maker-row]');
    if (!container) return;
    container.querySelectorAll(`[data-maker-sync-target="${token}"]`).forEach(target => {
        target.value = input.value;
    });
}

document.addEventListener('input', (event) => {
    if (event.target.matches('[data-maker-sync]')) {
        syncMakerTargets(event.target);
    }
});

document.querySelectorAll('[data-maker-sync]').forEach(syncMakerTargets);

// ── PO Modal ──────────────────────────────────────────────────────────────
@php
$poJsonMap = [];
foreach ($contract->purchaseOrders as $poItem) {
    $poJsonMap[$poItem->id] = [
        'po_number'           => $poItem->po_number,
        'po_date'             => $poItem->po_date?->format('d/m/Y'),
        'maker'               => $poItem->rfq?->maker ?? '',
        'exact_delivery_date'   => $poItem->exact_delivery_date?->format('d/m/Y'),
        'delivered_date'         => $poItem->delivered_date?->format('d/m/Y'),
        'contract_delivery_date' => $contract->delivery_date?->format('d/m/Y'),
        'delivery_diff_days'     => ($poItem->delivered_date && $contract->delivery_date)
            ? $contract->delivery_date->diffInDays($poItem->delivered_date, false)
            : null,
        'incoterm'               => $poItem->incoterm,
        'dimension'              => $poItem->dimension,
        'weight'                 => $poItem->weight,
        'expedite'               => $poItem->expedite,
        'payment_terms'       => $poItem->makerPaymentTerms->sortBy('term_code')->map(fn($m) => [
            'term_code'      => $m->term_code,
            'percentage'     => $m->percentage !== null ? rtrim(rtrim(number_format((float)$m->percentage, 2), '0'), '.') : null,
            'invoice_number' => $m->invoice_number,
            'invoice_date'   => $m->invoice_date?->format('d/m/Y'),
            'paid_date'      => $m->paid_date?->format('d/m/Y'),
        ])->values()->toArray(),
        'wip_statuses'        => $poItem->wipStatuses->sortBy('percentage')->map(fn($w) => [
            'percentage'  => $w->percentage,
            'status_date' => $w->status_date?->format('d/m/Y'),
        ])->values()->toArray(),
        'shipping_docs' => $poItem->shippingDocuments->map(fn($d) => [
            'name' => $d->name,
            'url'  => asset('storage/'.$d->file_path),
        ])->values()->toArray(),
    ];
}
@endphp
const poData = @json($poJsonMap);

function openPoModal(poId) {
    const po = poData[poId];
    if (!po) return;
    document.getElementById('po-modal-title').textContent = po.po_number;

    // ── Header info row
    let html = `<div class="flex flex-wrap gap-x-6 gap-y-2 text-sm mb-4 pb-4 border-b border-slate-100">`;
    html += `<div><div class="text-xs text-slate-400 uppercase tracking-wide">PO Date</div><div class="font-medium text-slate-800">${po.po_date || '\u2014'}</div></div>`;
    if (po.maker) html += `<div><div class="text-xs text-slate-400 uppercase tracking-wide">Maker</div><div class="font-medium text-slate-800">${po.maker}</div></div>`;
    html += `<div><div class="text-xs text-slate-400 uppercase tracking-wide">D. Date</div><div class="font-medium text-slate-800">${po.exact_delivery_date || '\u2014'}</div></div>`;
    if (po.incoterm) html += `<div><div class="text-xs text-slate-400 uppercase tracking-wide">Incoterm</div><div class="font-medium text-slate-800">${po.incoterm}</div></div>`;
    if (po.delivered_date) {
           let diffHtml = '';
           if (po.delivery_diff_days !== null && po.delivery_diff_days !== undefined) {
               const d = Number(po.delivery_diff_days);
               const color = d > 0 ? 'text-red-500' : d < 0 ? 'text-emerald-500' : 'text-slate-400';
               const sign  = d > 0 ? '+' : '';
               const label = d > 0 ? 'late' : d < 0 ? 'early' : 'on time';
               diffHtml = ` <span class="${color} font-semibold">${sign}${d} Days ${label}</span>`;
           }
           html += `<div><dt class="text-xs text-slate-400 uppercase tracking-wide">DELIVERY DIFFERENCE</dt><dd class="font-medium">${diffHtml}</dd></div>`;
       }
    html += `</div>`;

    // ── Payment terms — table like contract payment terms
    if (po.payment_terms && po.payment_terms.length) {
        html += `<div class="rounded-lg border border-slate-100 overflow-hidden mb-4">
            <div class="px-4 py-2 bg-slate-50 border-b border-slate-100"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Payment Terms</span></div>
            <table class="w-full text-xs">
                <thead class="border-b border-slate-100 bg-slate-50">
                    <tr>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Term</th>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">%</th>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Invoice No.</th>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Invoice Date</th>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Paid Date</th>
                    </tr>
                </thead>
                <tbody>${po.payment_terms.map(m => `<tr class="border-b border-slate-50">
                    <td class="px-3 py-1.5 font-medium">${m.term_code || '\u2014'}</td>
                    <td class="px-3 py-1.5">${m.percentage != null ? m.percentage + '%' : '\u2014'}</td>
                    <td class="px-3 py-1.5">${m.invoice_number || '\u2014'}</td>
                    <td class="px-3 py-1.5">${m.invoice_date || '\u2014'}</td>
                    <td class="px-3 py-1.5">${m.paid_date ? '<span class="text-emerald-600">' + m.paid_date + '</span>' : '\u2014'}</td>
                </tr>`).join('')}</tbody>
            </table>
        </div>`;
    }

    // ── Expedite + WIP side by side
    const hasExpedite = po.dimension || po.weight || po.incoterm || po.expedite || po.delivered_date;
    const hasWip = po.wip_statuses && po.wip_statuses.length;
    if (hasExpedite || hasWip) {
        html += `<div class="rounded-lg border border-slate-100 overflow-hidden mb-4">
            <div class="px-4 py-2 bg-slate-50 border-b border-slate-100"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Expedite</span></div>
            <div class="flex gap-0 divide-x divide-slate-100">`;

        // Left: expedite fields
        html += `<dl class="flex-1 grid grid-cols-2 gap-3 p-3 text-xs content-start">`;
        if (po.dimension) html += `<div><dt class="text-slate-400">Dimension</dt><dd class="font-medium">${po.dimension}</dd></div>`;
        if (po.weight)    html += `<div><dt class="text-slate-400">Weight</dt><dd class="font-medium">${po.weight}</dd></div>`;
        if (po.incoterm)  html += `<div><dt class="text-slate-400">Incoterm</dt><dd class="font-medium">${po.incoterm}</dd></div>`;
       
        if (po.expedite)  html += `<div class="col-span-2"><dt class="text-slate-400">Note</dt><dd class="font-medium">${po.expedite}</dd></div>`;
        if (!hasExpedite) html += `<div class="col-span-2 text-slate-300 italic">—</div>`;
        html += `</dl>`;

        // Right: WIP table
        if (hasWip) {
            html += `<div class="flex-shrink-0 w-48">
                <table class="w-full text-xs">
                    <thead class="border-b border-slate-100 bg-slate-50">
                        <tr>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">WIP</th>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Date</th>
                        </tr>
                    </thead>
                    <tbody>${po.wip_statuses.map(w => `<tr class="border-b border-slate-50">
                        <td class="px-3 py-1.5 font-semibold text-indigo-700">${w.percentage}%</td>
                        <td class="px-3 py-1.5 text-slate-500">${w.status_date || '\u2014'}</td>
                    </tr>`).join('')}</tbody>
                </table>
            </div>`;
        }
        html += `</div></div>`;
    }

    // ── Shipping docs
    if (po.shipping_docs && po.shipping_docs.length) {
        html += `<div class="rounded-lg border border-slate-100 overflow-hidden">
            <div class="px-4 py-2 bg-slate-50 border-b border-slate-100"><span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Shipping Documents</span></div>
            <table class="w-full text-xs">
                <thead class="border-b border-slate-100 bg-slate-50">
                    <tr>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Nama Dokumen</th>
                        <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Link</th>
                    </tr>
                </thead>
                <tbody>${po.shipping_docs.map(d => `<tr class="border-b border-slate-50">
                    <td class="px-3 py-1.5">${d.name}</td>
                    <td class="px-3 py-1.5"><a href="${d.url}" target="_blank" class="text-sky-600 hover:underline">Buka &nearr;</a></td>
                </tr>`).join('')}</tbody>
            </table>
        </div>`;
    }

    document.getElementById('po-modal-body').innerHTML = html;
    document.getElementById('po-modal').classList.remove('hidden');
}
</script>
@endpush
