@extends('layouts.app')

@section('title', 'Detail Contract Payment Term')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Detail Contract Payment Term</h1>
        <a href="{{ route('contract-payment-terms.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Kembali</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div><span class="text-slate-500 text-sm">Contract</span><p class="font-medium">{{ $contractPaymentTerm->contract?->contract_number ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Term Code</span><p class="font-medium">{{ $contractPaymentTerm->term_code ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Percentage</span><p class="font-medium">{{ $contractPaymentTerm->percentage ?: 0 }}%</p></div>
        <div><span class="text-slate-500 text-sm">Invoice Number</span><p class="font-medium">{{ $contractPaymentTerm->invoice_number ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Invoice Date</span><p class="font-medium">{{ optional($contractPaymentTerm->invoice_date)->format('Y-m-d') ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Paid Date</span><p class="font-medium">{{ optional($contractPaymentTerm->paid_date)->format('Y-m-d') ?: '-' }}</p></div>
    </div>
@endsection
