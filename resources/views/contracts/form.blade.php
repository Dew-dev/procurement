@extends('layouts.app')

@section('title', $isEdit ? 'Edit Contract' : 'Tambah Contract')

@section('content')
@php
$inp = 'border border-slate-200 rounded px-2 py-1.5 text-sm w-full focus:outline-none focus:border-indigo-400 bg-white';
$btnAdd = 'px-3 py-1.5 text-sm rounded-md font-medium bg-emerald-600 text-white hover:bg-emerald-700';
$btnDel = 'w-7 h-7 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 text-sm font-bold leading-none flex-shrink-0 mt-1';
$th = 'px-3 py-2 text-left text-xs font-medium text-slate-500 uppercase tracking-wide whitespace-nowrap';
$td = 'px-3 py-2 align-top';
$section = 'bg-white border border-slate-200 rounded-xl overflow-hidden mb-6';
$secHead = 'px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between';
$existingCpts = $isEdit ? $contract->contractPaymentTerms : collect();
$existingRfqs = $isEdit ? $contract->rfqs : collect();
$existingQuos = $isEdit ? $contract->quotations : collect();
$existingPos = $isEdit ? $contract->purchaseOrders : collect();
$existingBgs = $isEdit ? $contract->bgNumbers : collect();
$existingSbs = $isEdit ? $contract->suretyBonds : collect();
@endphp

<h1 class="text-2xl font-semibold mb-6">{{ $isEdit ? 'Edit Contract' : 'Tambah Contract' }}</h1>

<form method="POST"
    action="{{ $isEdit ? route('contracts.update', $contract) : route('contracts.store') }}"
    enctype="multipart/form-data"
    class="space-y-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- CONTRACT INFO --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">Informasi Contract</h2>
        </div>
        <div class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Contract Number <span class="text-red-400">*</span></label>
                    <input class="{{ $inp }}" name="contract_number" value="{{ old('contract_number', $contract->contract_number) }}" required placeholder="CTR-001">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Buyer Name</label>
                    <input class="{{ $inp }}" name="buyer_name" value="{{ old('buyer_name', $contract->buyer_name) }}" placeholder="PT. Example">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">RFQ From Buyer</label>
                    <input class="{{ $inp }}" type="date" name="rfq_from_buyer" value="{{ old('rfq_from_buyer', optional($contract->rfq_from_buyer)->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Quotation To Buyer</label>
                    <input class="{{ $inp }}" type="date" name="quotation_to_buyer" value="{{ old('quotation_to_buyer', optional($contract->quotation_to_buyer)->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Contract Date</label>
                    <input class="{{ $inp }}" type="date" name="contract_date" value="{{ old('contract_date', optional($contract->contract_date)->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Delivery Date</label>
                    <input class="{{ $inp }}" type="date" name="delivery_date" value="{{ old('delivery_date', optional($contract->delivery_date)->format('Y-m-d')) }}">
                </div>
            </div>
        </div>
    </div>

    {{-- CONTRACT PAYMENT TERMS --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">Contract Payment Terms <span class="text-xs font-normal text-slate-400 ml-1">(buyer side)</span></h2>
            <button type="button" onclick="addRow('cpt-tbody','cpt-tpl',ctrs,'cpt')" class="{{ $btnAdd }}">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Term Code</th>
                        <th class="{{ $th }}">% (persen)</th>
                        <th class="{{ $th }}">No. Invoice</th>
                        <th class="{{ $th }}">Tgl Invoice</th>
                        <th class="{{ $th }}">Tgl Bayar</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody id="cpt-tbody">
                    @foreach($existingCpts as $i => $cpt)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="{{ $td }}">
                            <input type="hidden" name="contract_payment_terms[{{ $i }}][id]" value="{{ $cpt->id }}">
                            <input class="{{ $inp }}" type="text" name="contract_payment_terms[{{ $i }}][term_code]" value="{{ $cpt->term_code }}" placeholder="DP, P1 ">
                        </td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="contract_payment_terms[{{ $i }}][percentage]" value="{{ $cpt->percentage }}" placeholder="0–100"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="contract_payment_terms[{{ $i }}][invoice_number]" value="{{ $cpt->invoice_number }}" placeholder="INV-xxx"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="contract_payment_terms[{{ $i }}][invoice_date]" value="{{ optional($cpt->invoice_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="contract_payment_terms[{{ $i }}][paid_date]" value="{{ optional($cpt->paid_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'cpt-tbody','cpt-empty-msg')" class="{{ $btnDel }}">×</button></td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-slate-50" id="cpt-empty-msg" {{ $existingCpts->isNotEmpty() ? ' style="display:none"' : '' }}>
                        <td colspan="6" class="px-3 py-3 text-center text-slate-400 text-sm italic">Klik "+ Tambah" untuk menambah baris</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <template id="cpt-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="contract_payment_terms[__IDX__][term_code]" placeholder="DP, P1 "></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="contract_payment_terms[__IDX__][percentage]" placeholder="0–100"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="contract_payment_terms[__IDX__][invoice_number]" placeholder="INV-xxx"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="contract_payment_terms[__IDX__][invoice_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="contract_payment_terms[__IDX__][paid_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'cpt-tbody','cpt-empty-msg')" class="{{ $btnDel }}">×</button></td>
        </tr>
    </template>

    {{-- BG NUMBERS --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">BG Number</h2>
            <button type="button" onclick="addRow('bg-tbody','bg-tpl',ctrs,'bg')" class="{{ $btnAdd }}">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Number</th>
                        <th class="{{ $th }}">Periode</th>
                        <th class="{{ $th }}">Start Date</th>
                        <th class="{{ $th }}">End Date</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody id="bg-tbody">
                    @foreach($existingBgs as $i => $bg)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="{{ $td }}">
                            <input type="hidden" name="bg_numbers[{{ $i }}][id]" value="{{ $bg->id }}">
                            <input class="{{ $inp }}" type="text" name="bg_numbers[{{ $i }}][number]" value="{{ $bg->number }}" placeholder="No. BG">
                        </td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="bg_numbers[{{ $i }}][periode]" value="{{ $bg->periode }}" placeholder="misal: 12 bulan"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="bg_numbers[{{ $i }}][start_date]" value="{{ optional($bg->start_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="bg_numbers[{{ $i }}][end_date]" value="{{ optional($bg->end_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'bg-tbody','bg-empty-msg')" class="{{ $btnDel }}">×</button></td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-slate-50" id="bg-empty-msg" {{ $existingBgs->isNotEmpty() ? ' style="display:none"' : '' }}>
                        <td colspan="5" class="px-3 py-3 text-center text-slate-400 text-sm italic">Klik "+ Tambah" untuk menambah baris</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <template id="bg-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="bg_numbers[__IDX__][number]" placeholder="No. BG"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="bg_numbers[__IDX__][periode]" placeholder="misal: 12 bulan"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="bg_numbers[__IDX__][start_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="bg_numbers[__IDX__][end_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'bg-tbody','bg-empty-msg')" class="{{ $btnDel }}">×</button></td>
        </tr>
    </template>

    {{-- SURETY BONDS --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">Surety Bond</h2>
            <button type="button" onclick="addRow('sb-tbody','sb-tpl',ctrs,'sb')" class="{{ $btnAdd }}">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Number</th>
                        <th class="{{ $th }}">Periode</th>
                        <th class="{{ $th }}">Start Date</th>
                        <th class="{{ $th }}">End Date</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody id="sb-tbody">
                    @foreach($existingSbs as $i => $sb)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="{{ $td }}">
                            <input type="hidden" name="surety_bonds[{{ $i }}][id]" value="{{ $sb->id }}">
                            <input class="{{ $inp }}" type="text" name="surety_bonds[{{ $i }}][number]" value="{{ $sb->number }}" placeholder="No. Surety Bond">
                        </td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="surety_bonds[{{ $i }}][periode]" value="{{ $sb->periode }}" placeholder="misal: 12 bulan"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="surety_bonds[{{ $i }}][start_date]" value="{{ optional($sb->start_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="surety_bonds[{{ $i }}][end_date]" value="{{ optional($sb->end_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'sb-tbody','sb-empty-msg')" class="{{ $btnDel }}">×</button></td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-slate-50" id="sb-empty-msg" {{ $existingSbs->isNotEmpty() ? ' style="display:none"' : '' }}>
                        <td colspan="5" class="px-3 py-3 text-center text-slate-400 text-sm italic">Klik "+ Tambah" untuk menambah baris</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <template id="sb-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="surety_bonds[__IDX__][number]" placeholder="No. Surety Bond"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="surety_bonds[__IDX__][periode]" placeholder="misal: 12 bulan"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="surety_bonds[__IDX__][start_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="surety_bonds[__IDX__][end_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'sb-tbody','sb-empty-msg')" class="{{ $btnDel }}">×</button></td>
        </tr>
    </template>

    {{-- RFQs --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">RFQs</h2>
            <button type="button" onclick="addRow('rfq-tbody','rfq-tpl',ctrs,'rfq')" class="{{ $btnAdd }}">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">RFQ Number</th>
                        <th class="{{ $th }}">RFQ Date</th>
                        <th class="{{ $th }}">Maker</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody id="rfq-tbody">
                    @foreach($existingRfqs as $i => $rfq)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="{{ $td }}">
                            <input type="hidden" name="rfqs[{{ $i }}][id]" value="{{ $rfq->id }}">
                            <input class="{{ $inp }}" type="text" name="rfqs[{{ $i }}][rfq_number]" value="{{ $rfq->rfq_number }}" placeholder="RFQ-xxx">
                        </td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="rfqs[{{ $i }}][rfq_date]" value="{{ optional($rfq->rfq_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="rfqs[{{ $i }}][maker]" value="{{ $rfq->maker }}" placeholder="Nama maker"></td>
                        <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'rfq-tbody','rfq-empty-msg')" class="{{ $btnDel }}">×</button></td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-slate-50" id="rfq-empty-msg" {{ $existingRfqs->isNotEmpty() ? ' style="display:none"' : '' }}>
                        <td colspan="4" class="px-3 py-3 text-center text-slate-400 text-sm italic">Klik "+ Tambah" untuk menambah baris</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <template id="rfq-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="rfqs[__IDX__][rfq_number]" placeholder="RFQ-xxx"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="rfqs[__IDX__][rfq_date]"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="rfqs[__IDX__][maker]" placeholder="Nama maker"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'rfq-tbody','rfq-empty-msg')" class="{{ $btnDel }}">×</button></td>
        </tr>
    </template>

    {{-- QUOTATIONS --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">Quotations</h2>
            <button type="button" onclick="addRow('quo-tbody','quo-tpl',ctrs,'quo')" class="{{ $btnAdd }}">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="{{ $th }}">Quotation Number</th>
                        <th class="{{ $th }}">Quotation Date</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody id="quo-tbody">
                    @foreach($existingQuos as $i => $quo)
                    <tr class="border-b border-slate-50 hover:bg-slate-50">
                        <td class="{{ $td }}">
                            <input type="hidden" name="quotations[{{ $i }}][id]" value="{{ $quo->id }}">
                            <input class="{{ $inp }}" type="text" name="quotations[{{ $i }}][quotation_number]" value="{{ $quo->quotation_number }}" placeholder="QUO-xxx">
                        </td>
                        <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="quotations[{{ $i }}][quotation_date]" value="{{ optional($quo->quotation_date)->format('Y-m-d') }}"></td>
                        <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'quo-tbody','quo-empty-msg')" class="{{ $btnDel }}">×</button></td>
                    </tr>
                    @endforeach
                    <tr class="border-b border-slate-50" id="quo-empty-msg" {{ $existingQuos->isNotEmpty() ? ' style="display:none"' : '' }}>
                        <td colspan="3" class="px-3 py-3 text-center text-slate-400 text-sm italic">Klik "+ Tambah" untuk menambah baris</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <template id="quo-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="{{ $td }}"><input class="{{ $inp }}" type="text" name="quotations[__IDX__][quotation_number]" placeholder="QUO-xxx"></td>
            <td class="{{ $td }}"><input class="{{ $inp }}" type="date" name="quotations[__IDX__][quotation_date]"></td>
            <td class="{{ $td }}"><button type="button" onclick="removeSimpleRow(this,'quo-tbody','quo-empty-msg')" class="{{ $btnDel }}">×</button></td>
        </tr>
    </template>

    {{-- PURCHASE ORDERS --}}
    <div class="{{ $section }}">
        <div class="{{ $secHead }}">
            <h2 class="font-semibold text-slate-800">Purchase Orders <span class="text-xs font-normal text-slate-400 ml-1">(beserta Maker Payment Terms)</span></h2>
            <button type="button" onclick="addPoCard()" class="{{ $btnAdd }}">+ Tambah PO</button>
        </div>
        <div id="po-cards">
            @foreach($existingPos as $pi => $po)
            <div class="border-t border-slate-100 p-5" data-po-card data-po-idx="{{ $pi }}">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $po->po_number }}</span>
                    <button type="button" onclick="removePoCard(this,'po-cards','po-empty-msg')" class="{{ $btnDel }}">×</button>
                </div>
                <input type="hidden" name="purchase_orders[{{ $pi }}][id]" value="{{ $po->id }}">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="text-xs text-slate-400 mb-0.5 block">PO Number <span class="text-red-400">*</span></label>
                        <input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][po_number]" value="{{ $po->po_number }}" placeholder="PO-xxx" required>
                    </div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">PO Date</label><input class="{{ $inp }}" type="date" name="purchase_orders[{{ $pi }}][po_date]" value="{{ optional($po->po_date)->format('Y-m-d') }}"></div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">Payment Term</label><input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][po_payment_term]" value="{{ $po->po_payment_term }}" placeholder="30 hari"></div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">WIP Status</label><input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][wip_status]" value="{{ $po->wip_status }}" placeholder="On Track "></div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">Delivery Date</label><input class="{{ $inp }}" type="date" name="purchase_orders[{{ $pi }}][exact_delivery_date]" value="{{ optional($po->exact_delivery_date)->format('Y-m-d') }}"></div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">Dimension</label><input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][dimension]" value="{{ $po->dimension }}" placeholder="pxlxt cm"></div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">Weight</label><input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][weight]" value="{{ $po->weight }}" placeholder="kg"></div>
                    <div><label class="text-xs text-slate-400 mb-0.5 block">Incoterm</label><input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][incoterm]" value="{{ $po->incoterm }}" placeholder="FOB, CIF "></div>
                    <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                        <label class="text-xs text-slate-400 mb-0.5 block">Shipping Documents</label>
                        @foreach($po->shippingDocuments as $doc)
                        <div class="flex items-center gap-2 mb-1">
                            <input type="checkbox" name="purchase_orders[{{ $pi }}][delete_shipping_docs][]" value="{{ $doc->id }}" class="accent-red-500" id="fdel-doc-{{ $doc->id }}">
                            <label for="fdel-doc-{{ $doc->id }}" class="text-xs text-red-400 cursor-pointer">Hapus</label>
                            <a href="{{ $doc->url }}" target="_blank" class="text-xs text-sky-600 hover:underline truncate">{{ $doc->name }}</a>
                        </div>
                        @endforeach
                        <div class="space-y-1" data-sdoc-rows>
                            <div class="flex gap-1 items-center sdoc-row">
                                <input class="{{ $inp }} flex-1" type="text" name="purchase_orders[{{ $pi }}][new_shipping_docs][0][name]" placeholder="Nama dokumen (B/L, Packing List…)">
                                <input class="{{ $inp }} flex-1" type="file" name="purchase_orders[{{ $pi }}][new_shipping_docs][0][file]">
                                <button type="button" onclick="addSdocRowForm(this,'{{ $pi }}')" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Nested MPTs for existing PO --}}
                <div class="rounded-lg border border-slate-100 overflow-hidden">
                    <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Maker Payment Terms</span>
                        <button type="button" onclick="addMptRow(this)" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+ Tambah Term</button>
                    </div>
                    <table class="w-full text-xs">
                        <thead class="border-b border-slate-100">
                            <tr>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Term Code</th>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">%</th>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">No. Invoice</th>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Tgl Invoice</th>
                                <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Tgl Bayar</th>
                                <th class="w-8"></th>
                            </tr>
                        </thead>
                        <tbody data-mpt-tbody data-mpt-ctr="{{ $po->makerPaymentTerms->count() }}">
                            @foreach($po->makerPaymentTerms as $mi => $mpt)
                            <tr class="border-b border-slate-50 hover:bg-slate-50">
                                <td class="px-3 py-1.5">
                                    <input type="hidden" name="purchase_orders[{{ $pi }}][maker_payment_terms][{{ $mi }}][id]" value="{{ $mpt->id }}">
                                    <input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][maker_payment_terms][{{ $mi }}][term_code]" value="{{ $mpt->term_code }}" placeholder="DP, P1 ">
                                </td>
                                <td class="px-3 py-1.5"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="purchase_orders[{{ $pi }}][maker_payment_terms][{{ $mi }}][percentage]" value="{{ $mpt->percentage }}" placeholder="0–100"></td>
                                <td class="px-3 py-1.5"><input class="{{ $inp }}" type="text" name="purchase_orders[{{ $pi }}][maker_payment_terms][{{ $mi }}][invoice_number]" value="{{ $mpt->invoice_number }}" placeholder="INV-xxx"></td>
                                <td class="px-3 py-1.5"><input class="{{ $inp }}" type="date" name="purchase_orders[{{ $pi }}][maker_payment_terms][{{ $mi }}][invoice_date]" value="{{ optional($mpt->invoice_date)->format('Y-m-d') }}"></td>
                                <td class="px-3 py-1.5"><input class="{{ $inp }}" type="date" name="purchase_orders[{{ $pi }}][maker_payment_terms][{{ $mi }}][paid_date]" value="{{ optional($mpt->paid_date)->format('Y-m-d') }}"></td>
                                <td class="px-3 py-1.5"><button type="button" onclick="removeMptRow(this)" class="w-6 h-6 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 text-xs font-bold">×</button></td>
                            </tr>
                            @endforeach
                            <tr data-mpt-empty class="border-b border-slate-50" {{ $po->makerPaymentTerms->isNotEmpty() ? ' style="display:none"' : '' }}>
                                <td colspan="6" class="px-3 py-2 text-center text-slate-400 italic">Belum ada terms</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            <p id="po-empty-msg" class="px-5 py-3 text-center text-slate-400 text-sm italic" {{ $existingPos->isNotEmpty() ? ' style="display:none"' : '' }}>Klik "+ Tambah PO" untuk menambah Purchase Order</p>
        </div>
    </div>

    {{-- PO card template (for new POs) --}}
    <template id="po-card-tpl">
        <div class="border-t border-slate-100 p-5" data-po-card>
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">PO Baru</span>
                <button type="button" onclick="removePoCard(this,'po-cards','po-empty-msg')" class="{{ $btnDel }}">×</button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-xs text-slate-400 mb-0.5 block">PO Number <span class="text-red-400">*</span></label>
                    <input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][po_number]" placeholder="PO-xxx" required>
                </div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">PO Date</label><input class="{{ $inp }}" type="date" name="purchase_orders[__PO__][po_date]"></div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">Payment Term</label><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][po_payment_term]" placeholder="30 hari"></div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">WIP Status</label><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][wip_status]" placeholder="On Track"></div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">Delivery Date</label><input class="{{ $inp }}" type="date" name="purchase_orders[__PO__][exact_delivery_date]"></div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">Dimension</label><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][dimension]" placeholder="pxlxt cm"></div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">Weight</label><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][weight]" placeholder="kg"></div>
                <div><label class="text-xs text-slate-400 mb-0.5 block">Incoterm</label><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][incoterm]" placeholder="FOB, CIF "></div>
                <div class="col-span-2 sm:col-span-3 lg:col-span-4">
                    <label class="text-xs text-slate-400 mb-0.5 block">Shipping Documents</label>
                    <div class="space-y-1" data-sdoc-rows>
                        <div class="flex gap-1 items-center sdoc-row">
                            <input class="{{ $inp }} flex-1" type="text" name="purchase_orders[__PO__][new_shipping_docs][0][name]" placeholder="Nama dokumen (B/L, Packing List…)">
                            <input class="{{ $inp }} flex-1" type="file" name="purchase_orders[__PO__][new_shipping_docs][0][file]">
                            <button type="button" onclick="addSdocRowForm(this,'__PO__')" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-slate-100 overflow-hidden">
                <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Maker Payment Terms</span>
                    <button type="button" onclick="addMptRow(this)" class="text-xs px-2 py-1 rounded bg-sky-100 text-sky-700 hover:bg-sky-200 font-medium">+ Tambah Term</button>
                </div>
                <table class="w-full text-xs">
                    <thead class="border-b border-slate-100">
                        <tr>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Term Code</th>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">%</th>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">No. Invoice</th>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Tgl Invoice</th>
                            <th class="px-3 py-1.5 text-left text-slate-500 font-medium">Tgl Bayar</th>
                            <th class="w-8"></th>
                        </tr>
                    </thead>
                    <tbody data-mpt-tbody data-mpt-ctr="0">
                        <tr data-mpt-empty class="border-b border-slate-50">
                            <td colspan="6" class="px-3 py-2 text-center text-slate-400 italic">Belum ada terms</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    {{-- MPT row template (for new MPTs, __PO__ and __MPT__ replaced at runtime) --}}
    <template id="mpt-row-tpl">
        <tr class="border-b border-slate-50 hover:bg-slate-50">
            <td class="px-3 py-1.5"><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][maker_payment_terms][__MPT__][term_code]" placeholder="DP, P1 "></td>
            <td class="px-3 py-1.5"><input class="{{ $inp }}" type="number" step="0.01" min="0" max="100" name="purchase_orders[__PO__][maker_payment_terms][__MPT__][percentage]" placeholder="0–100"></td>
            <td class="px-3 py-1.5"><input class="{{ $inp }}" type="text" name="purchase_orders[__PO__][maker_payment_terms][__MPT__][invoice_number]" placeholder="INV-xxx"></td>
            <td class="px-3 py-1.5"><input class="{{ $inp }}" type="date" name="purchase_orders[__PO__][maker_payment_terms][__MPT__][invoice_date]"></td>
            <td class="px-3 py-1.5"><input class="{{ $inp }}" type="date" name="purchase_orders[__PO__][maker_payment_terms][__MPT__][paid_date]"></td>
            <td class="px-3 py-1.5"><button type="button" onclick="removeMptRow(this)" class="w-6 h-6 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 text-xs font-bold">×</button></td>
        </tr>
    </template>



    {{-- SUBMIT --}}
    <div class="flex gap-3">
        <button class="px-5 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700 font-medium" type="submit">
            {{ $isEdit ? 'Simpan Perubahan' : 'Buat Contract' }}
        </button>
        <a href="{{ $isEdit ? route('contracts.show', $contract) : route('contracts.index') }}"
            class="px-5 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">
            Batal
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const ctrs = {
        cpt: {{$existingCpts->count()}},
        rfq: {{$existingRfqs->count()}},
        quo: {{$existingQuos->count()}},
        po: {{$existingPos->count()}},
        bg: {{$existingBgs->count()}},
        sb: {{$existingSbs->count()}}
    };

    function addRow(tbodyId, tplId, countersObj, key) {
        const tbody = document.getElementById(tbodyId);
        const tpl = document.getElementById(tplId);
        // Hide empty message
        const emptyId = tbodyId.replace('-tbody', '-empty-msg');
        const emptyEl = document.getElementById(emptyId);
        if (emptyEl) emptyEl.style.display = 'none';
        // Clone and insert
        const idx = countersObj[key]++;
        const html = tpl.innerHTML.replace(/__IDX__/g, idx);
        tbody.insertAdjacentHTML('beforeend', html);
        tbody.lastElementChild?.querySelector('input')?.focus();
    }

    function removeSimpleRow(btn, tbodyId, emptyMsgId) {
        const tr = btn.closest('tr');
        const tbody = document.getElementById(tbodyId);
        tr.remove();
        if (tbody.querySelectorAll('tr:not(#' + emptyMsgId + ')').length === 0) {
            const emptyEl = document.getElementById(emptyMsgId);
            if (emptyEl) emptyEl.style.display = '';
        }
    }

    function addSdocRowForm(btn, pi) {
        const wrapper = btn.closest('[data-sdoc-rows]');
        const rowCount = wrapper.querySelectorAll('.sdoc-row').length;
        const div = document.createElement('div');
        div.className = 'flex gap-1 items-center sdoc-row';
        div.innerHTML = `<input class="border border-slate-300 rounded px-2 py-1 text-sm w-full flex-1" type="text" name="purchase_orders[${pi}][new_shipping_docs][${rowCount}][name]" placeholder="Nama dokumen">
            <input class="border border-slate-300 rounded px-2 py-1 text-sm flex-1" type="file" name="purchase_orders[${pi}][new_shipping_docs][${rowCount}][file]">
            <button type="button" onclick="this.closest('.sdoc-row').remove()" class="text-xs px-2 py-1 rounded bg-red-100 text-red-500 hover:bg-red-200 font-medium">&times;</button>`;
        btn.closest('.sdoc-row').after(div);
    }

    function addPoCard() {
        const container = document.getElementById('po-cards');
        const tpl = document.getElementById('po-card-tpl');
        const emptyMsg = document.getElementById('po-empty-msg');
        if (emptyMsg) emptyMsg.style.display = 'none';
        const poIdx = ctrs.po++;
        // Replace PO index placeholder
        let html = tpl.innerHTML.replace(/__PO__/g, poIdx);
        container.insertAdjacentHTML('beforeend', html);
        const newCard = container.lastElementChild;
        // Store po index on this card
        newCard.dataset.poIdx = poIdx;
        newCard.querySelector('input:not([type=hidden])')?.focus();
    }

    function removePoCard(btn, containerId, emptyMsgId) {
        const card = btn.closest('[data-po-card]');
        const container = document.getElementById(containerId);
        card.remove();
        if (container.querySelectorAll('[data-po-card]').length === 0) {
            const emptyEl = document.getElementById(emptyMsgId);
            if (emptyEl) emptyEl.style.display = '';
        }
    }

    function addMptRow(btn) {
        const card = btn.closest('[data-po-card]');
        const poIdx = card.dataset.poIdx;
        const tbody = card.querySelector('[data-mpt-tbody]');
        const emptyTr = tbody.querySelector('[data-mpt-empty]');
        const mptTpl = document.getElementById('mpt-row-tpl');
        const mptIdx = parseInt(tbody.dataset.mptCtr || '0');
        tbody.dataset.mptCtr = mptIdx + 1;
        if (emptyTr) emptyTr.style.display = 'none';
        let html = mptTpl.innerHTML.replace(/__PO__/g, poIdx).replace(/__MPT__/g, mptIdx);
        tbody.insertAdjacentHTML('beforeend', html);
        tbody.lastElementChild?.querySelector('input')?.focus();
    }

    function removeMptRow(btn) {
        const tr = btn.closest('tr');
        const tbody = tr.closest('tbody');
        tr.remove();
        if (tbody.querySelectorAll('tr:not([data-mpt-empty])').length === 0) {
            const emptyTr = tbody.querySelector('[data-mpt-empty]');
            if (emptyTr) emptyTr.style.display = '';
        }
    }
</script>
@endpush