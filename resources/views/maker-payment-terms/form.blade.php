@extends('layouts.app')

@section('title', $isEdit ? 'Edit Maker Payment Term' : 'Tambah Maker Payment Term')

@section('content')
    <h1 class="text-2xl font-semibold mb-5">{{ $isEdit ? 'Edit Maker Payment Term' : 'Tambah Maker Payment Term' }}</h1>

    <form method="POST" action="{{ $isEdit ? route('maker-payment-terms.update', $makerPaymentTerm) : route('maker-payment-terms.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4 max-w-3xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Purchase Order</label>
            <select name="po_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">- Pilih PO -</option>
                @foreach ($purchaseOrders as $po)
                    <option value="{{ $po->id }}" @selected(old('po_id', $makerPaymentTerm->po_id) == $po->id)>{{ $po->po_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">Term Code</label><input name="term_code" value="{{ old('term_code', $makerPaymentTerm->term_code) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="DP / P1 / P2"></div>
            <div><label class="block text-sm font-medium mb-1">Percentage</label><input type="number" step="0.01" min="0" max="100" name="percentage" value="{{ old('percentage', $makerPaymentTerm->percentage) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Invoice Number</label><input name="invoice_number" value="{{ old('invoice_number', $makerPaymentTerm->invoice_number) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Invoice Date</label><input type="date" name="invoice_date" value="{{ old('invoice_date', optional($makerPaymentTerm->invoice_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Paid Date</label><input type="date" name="paid_date" value="{{ old('paid_date', optional($makerPaymentTerm->paid_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700" type="submit">Simpan</button>
            <a href="{{ route('maker-payment-terms.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection
