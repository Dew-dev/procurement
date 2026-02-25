@extends('layouts.app')

@section('title', $isEdit ? 'Edit Contract Payment Term' : 'Tambah Contract Payment Term')

@section('content')
    <h1 class="text-2xl font-semibold mb-5">{{ $isEdit ? 'Edit Contract Payment Term' : 'Tambah Contract Payment Term' }}</h1>

    <form method="POST" action="{{ $isEdit ? route('contract-payment-terms.update', $contractPaymentTerm) : route('contract-payment-terms.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4 max-w-3xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Contract</label>
            <select name="contract_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">- Pilih Contract -</option>
                @foreach ($contracts as $contract)
                    <option value="{{ $contract->id }}" @selected(old('contract_id', $contractPaymentTerm->contract_id) == $contract->id)>{{ $contract->contract_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">Term Code</label><input name="term_code" value="{{ old('term_code', $contractPaymentTerm->term_code) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2" placeholder="DP / P1 / P2"></div>
            <div><label class="block text-sm font-medium mb-1">Percentage</label><input type="number" step="0.01" min="0" max="100" name="percentage" value="{{ old('percentage', $contractPaymentTerm->percentage) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Invoice Number</label><input name="invoice_number" value="{{ old('invoice_number', $contractPaymentTerm->invoice_number) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Invoice Date</label><input type="date" name="invoice_date" value="{{ old('invoice_date', optional($contractPaymentTerm->invoice_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Paid Date</label><input type="date" name="paid_date" value="{{ old('paid_date', optional($contractPaymentTerm->paid_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700" type="submit">Simpan</button>
            <a href="{{ route('contract-payment-terms.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection
