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
    <a href="{{ route('contracts.index') }}" class="text-slate-400 hover:text-slate-600 text-sm">â† Contracts</a>
</div>

<div class="bg-white rounded-xl border border-slate-200 p-6 mb-7">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">{{ $contract->contract_number }}</h1>
            <p class="text-slate-500 text-sm mt-0.5">{{ $contract->buyer_name ?: '' }}</p>
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
        <div><dt class="text-slate-400 text-xs">RFQ from Buyer</dt><dd class="font-medium">{{ $contract->rfq_from_buyer?->format('d M Y') ?? '' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Quotation to Buyer</dt><dd class="font-medium">{{ $contract->quotation_to_buyer?->format('d M Y') ?? '' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Contract Date</dt><dd class="font-medium">{{ $contract->contract_date?->format('d M Y') ?? '' }}</dd></div>
        <div><dt class="text-slate-400 text-xs">Delivery Date</dt><dd class="font-medium">{{ $contract->delivery_date?->format('d M Y') ?? '' }}</dd></div>
    </dl>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 1 CONTRACT PAYMENT TERMS
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800">Contract Payment Terms <span class="text-xs font-normal text-slate-400 ml-1">(buyer side)</span></h2>
        <span class="text-xs text-slate-400">{{ $contract->contractPaymentTerms->count() }} baris</span>
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

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 2 â€” RFQs
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800">RFQs</h2>
        <span class="text-xs text-slate-400">{{ $contract->rfqs->count() }} baris</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.rfqs.upsert', $contract) }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">RFQ Number</th>
                        <th class="{{ $th }}">RFQ Date</th>
                        <th class="{{ $th }}">Maker</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="rfq-tbody">
                @foreach($contract->rfqs as $i => $rfq)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][rfq_number]" value="{{ $rfq->rfq_number }}" placeholder="RFQ-xxx" required><input type="hidden" name="items[{{ $i }}][id]" value="{{ $rfq->id }}" data-row-id></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][rfq_date]" value="{{ $rfq->rfq_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][maker]" value="{{ $rfq->maker }}" placeholder="Nama maker"></td>
                    <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 flex gap-2 border-t border-slate-100">
            <button type="button" onclick="addRow('rfq-tbody','rfq-row-tpl',counters,'rfq')" class="{{ $btnAdd }}">+ Tambah Baris</button>
            <button type="submit" class="{{ $btnSave }}">Simpan</button>
        </div>
    </form>

    <template id="rfq-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][rfq_number]" placeholder="RFQ-xxx" required><input type="hidden" name="items[__IDX__][id]" value="" data-row-id></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][rfq_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][maker]" placeholder="Nama maker"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
        </tr>
    </template>

    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100"><tr><th class="{{ $th }}">RFQ Number</th><th class="{{ $th }}">RFQ Date</th><th class="{{ $th }}">Maker</th></tr></thead>
            <tbody>
            @forelse($contract->rfqs as $rfq)
            <tr class="border-b border-slate-50"><td class="{{ $td }}">{{ $rfq->rfq_number }}</td><td class="{{ $td }}">{{ $rfq->rfq_date?->format('d/m/Y') ?? '' }}</td><td class="{{ $td }}">{{ $rfq->maker ?: '' }}</td></tr>
            @empty
            <tr><td colspan="3" class="px-4 py-4 text-center text-slate-400 text-sm">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 3 â€” QUOTATIONS
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800">Quotations</h2>
        <span class="text-xs text-slate-400">{{ $contract->quotations->count() }} baris</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.quotations.upsert', $contract) }}">
        @csrf @method('PUT')
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Quotation Number</th>
                        <th class="{{ $th }}">Quotation Date</th>
                        <th class="px-3 py-2 w-8"></th>
                    </tr>
                </thead>
                <tbody id="quo-tbody">
                @foreach($contract->quotations as $i => $q)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[{{ $i }}][quotation_number]" value="{{ $q->quotation_number }}" placeholder="QUO-xxx" required><input type="hidden" name="items[{{ $i }}][id]" value="{{ $q->id }}" data-row-id></td>
                    <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[{{ $i }}][quotation_date]" value="{{ $q->quotation_date?->format('Y-m-d') }}"></td>
                    <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 flex gap-2 border-t border-slate-100">
            <button type="button" onclick="addRow('quo-tbody','quo-row-tpl',counters,'quo')" class="{{ $btnAdd }}">+ Tambah Baris</button>
            <button type="submit" class="{{ $btnSave }}">Simpan</button>
        </div>
    </form>

    <template id="quo-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="items[__IDX__][quotation_number]" placeholder="QUO-xxx" required><input type="hidden" name="items[__IDX__][id]" value="" data-row-id></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="items[__IDX__][quotation_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeRow(this)" class="{{ $btnDel }}" title="Hapus">-</button></td>
        </tr>
    </template>

    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-100"><tr><th class="{{ $th }}">Quotation Number</th><th class="{{ $th }}">Date</th></tr></thead>
            <tbody>
            @forelse($contract->quotations as $q)
            <tr class="border-b border-slate-50"><td class="{{ $td }}">{{ $q->quotation_number }}</td><td class="{{ $td }}">{{ $q->quotation_date?->format('d/m/Y') ?? '' }}</td></tr>
            @empty
            <tr><td colspan="2" class="px-4 py-4 text-center text-slate-400 text-sm">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     SECTION 4 â€” PURCHASE ORDERS (cards layout)
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800">Purchase Orders</h2>
        <span class="text-xs text-slate-400">{{ $contract->purchaseOrders->count() }} PO</span>
    </div>

    @if($isAdmin)
    <form method="POST" action="{{ route('contracts.purchase-orders.upsert', $contract) }}">
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
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Number <span class="text-red-400">*</span></label>
                    <input class="{{ $inp }}" type="text" name="items[{{ $i }}][po_number]" value="{{ $po->po_number }}" placeholder="PO-xxx" required>
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Date</label>
                    <input class="{{ $inp }}" type="date" name="items[{{ $i }}][po_date]" value="{{ $po->po_date?->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Payment Term</label>
                    <input class="{{ $inp }}" type="text" name="items[{{ $i }}][po_payment_term]" value="{{ $po->po_payment_term }}" placeholder="misal: 30 hari">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">WIP Status</label>
                    <input class="{{ $inp }}" type="text" name="items[{{ $i }}][wip_status]" value="{{ $po->wip_status }}" placeholder="On Track / Late">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Delivery Date</label>
                    <input class="{{ $inp }}" type="date" name="items[{{ $i }}][exact_delivery_date]" value="{{ $po->exact_delivery_date?->format('Y-m-d') }}">
                </div>
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
                <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                    <label class="text-xs text-slate-400 mb-0.5 block">Shipping Documents</label>
                    <textarea class="{{ $inp }}" name="items[{{ $i }}][shipping_documents]" rows="2" placeholder="B/L, Packing List">{{ $po->shipping_documents }}</textarea>
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
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Number <span class="text-red-400">*</span></label>
                    <input class="{{ $inp }}" type="text" name="items[__IDX__][po_number]" placeholder="PO-xxx" required>
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Date</label>
                    <input class="{{ $inp }}" type="date" name="items[__IDX__][po_date]">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Payment Term</label>
                    <input class="{{ $inp }}" type="text" name="items[__IDX__][po_payment_term]" placeholder="misal: 30 hari">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">WIP Status</label>
                    <input class="{{ $inp }}" type="text" name="items[__IDX__][wip_status]" placeholder="On Track / Late">
                </div>
                <div>
                    <label class="text-xs text-slate-400 mb-0.5 block">Delivery Date</label>
                    <input class="{{ $inp }}" type="date" name="items[__IDX__][exact_delivery_date]">
                </div>
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
                <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                    <label class="text-xs text-slate-400 mb-0.5 block">Shipping Documents</label>
                    <textarea class="{{ $inp }}" name="items[__IDX__][shipping_documents]" rows="2" placeholder="B/L, Packing List"></textarea>
                </div>
            </div>
        </div>
    </template>

    @else
    {{-- Read-only PO list --}}
    @forelse($contract->purchaseOrders as $po)
    <div class="p-5 border-b border-slate-100">
        <p class="font-semibold text-slate-800 mb-2">{{ $po->po_number }}</p>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div><dt class="text-slate-400 text-xs">PO Date</dt><dd>{{ $po->po_date?->format('d/m/Y') ?? '' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">Payment Term</dt><dd>{{ $po->po_payment_term ?: '' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">WIP Status</dt><dd>{{ $po->wip_status ?: '' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">Delivery Date</dt><dd>{{ $po->exact_delivery_date?->format('d/m/Y') ?? '' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">Dimension</dt><dd>{{ $po->dimension ?: '' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">Weight</dt><dd>{{ $po->weight ?: '' }}</dd></div>
            <div><dt class="text-slate-400 text-xs">Incoterm</dt><dd>{{ $po->incoterm ?: '' }}</dd></div>
            <div class="col-span-2"><dt class="text-slate-400 text-xs">Shipping Docs</dt><dd>{{ $po->shipping_documents ?: '' }}</dd></div>
        </dl>
    </div>
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
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50">
        <h2 class="font-semibold text-slate-800">Maker Payment Terms</h2>
        <p class="text-xs text-slate-400 mt-0.5">Per PO  simpan masing-masing secara terpisah</p>
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

{{-- ═══════════════════════════════════════════════════════════════════════
     SECTION 6 – BG NUMBERS
═══════════════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800">BG Number</h2>
        <span class="text-xs text-slate-400">{{ $contract->bgNumbers->count() }} baris</span>
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
<div class="bg-white rounded-xl border border-slate-200 mb-6 overflow-hidden">
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
        <h2 class="font-semibold text-slate-800">Surety Bond</h2>
        <span class="text-xs text-slate-400">{{ $contract->suretyBonds->count() }} baris</span>
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

@endsection

@push('scripts')
<script>
// â”€â”€ Row counters (start after existing records) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const counters = {
    cpt: {{ $contract->contractPaymentTerms->count() }},
    rfq: {{ $contract->rfqs->count() }},
    quo: {{ $contract->quotations->count() }},
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
    const tr      = btn.closest('tr');
    const idInput = tr.querySelector('[data-row-id]');

    if (idInput?.value) {
        // Existing record â€” toggle mark-for-delete
        const isMarked = tr.dataset.markedDelete === '1';
        if (isMarked) {
            // Undo
            delete tr.dataset.markedDelete;
            tr.classList.remove('opacity-40');
            tr.querySelectorAll('input[data-del-flag]').forEach(el => el.remove());
            tr.querySelectorAll('input:not([data-row-id]):not([type=hidden]), textarea, select')
              .forEach(el => el.disabled = false);
            btn.textContent = '-';
            btn.title = 'Hapus baris ini';
        } else {
            // Mark for deletion
            tr.dataset.markedDelete = '1';
            tr.classList.add('opacity-40');
            const del = document.createElement('input');
            del.type  = 'hidden';
            del.name  = idInput.name.replace('[id]', '[_delete]');
            del.value = '1';
            del.dataset.delFlag = '';
            tr.appendChild(del);
            tr.querySelectorAll('input:not([data-row-id]):not([type=hidden]), textarea, select')
              .forEach(el => el.disabled = true);
            btn.textContent = 'â†©';
            btn.title = 'Batalkan hapus';
        }
    } else {
        // New row â€” just remove it
        tr.remove();
    }
}

// â”€â”€ Add a new PO card â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
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
</script>
@endpush
