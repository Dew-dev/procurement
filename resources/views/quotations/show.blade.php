@extends('layouts.app')

@section('title', 'Detail Quotation')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Detail Quotation</h1>
        <a href="{{ route('quotations.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Kembali</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div><span class="text-slate-500 text-sm">Quotation Number</span><p class="font-medium">{{ $quotation->quotation_number }}</p></div>
        <div><span class="text-slate-500 text-sm">RFQ</span><p class="font-medium">{{ $quotation->rfq?->rfq_number ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Quotation Date</span><p class="font-medium">{{ optional($quotation->quotation_date)->format('Y-m-d') ?: '-' }}</p></div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h2 class="font-semibold mb-3">Purchase Order Terkait</h2>
        <ul class="text-sm space-y-2">
            @forelse ($quotation->purchaseOrders as $purchaseOrder)
                <li>{{ $purchaseOrder->po_number }}</li>
            @empty
                <li class="text-slate-500">Belum ada purchase order.</li>
            @endforelse
        </ul>
    </div>
@endsection
