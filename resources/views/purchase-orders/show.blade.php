@extends('layouts.app')

@section('title', 'Detail Purchase Order')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Detail Purchase Order</h1>
        <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Kembali</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div><span class="text-slate-500 text-sm">PO Number</span><p class="font-medium">{{ $purchaseOrder->po_number }}</p></div>
        <div><span class="text-slate-500 text-sm">Quotation</span><p class="font-medium">{{ $purchaseOrder->quotation?->quotation_number ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">PO Date</span><p class="font-medium">{{ optional($purchaseOrder->po_date)->format('Y-m-d') ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Payment Term</span><p class="font-medium">{{ $purchaseOrder->po_payment_term ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">WIP Status</span><p class="font-medium">{{ $purchaseOrder->wip_status ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Exact Delivery Date</span><p class="font-medium">{{ optional($purchaseOrder->exact_delivery_date)->format('Y-m-d') ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Dimension</span><p class="font-medium">{{ $purchaseOrder->dimension ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Weight</span><p class="font-medium">{{ $purchaseOrder->weight ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Incoterm</span><p class="font-medium">{{ $purchaseOrder->incoterm ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Shipping Documents</span><p class="font-medium">{{ $purchaseOrder->shipping_documents ?: '-' }}</p></div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h2 class="font-semibold mb-3">Maker Payment Terms</h2>
        <ul class="text-sm space-y-2">
            @forelse ($purchaseOrder->makerPaymentTerms as $term)
                <li>{{ $term->term_code ?: '-' }} - {{ $term->percentage ?: 0 }}%</li>
            @empty
                <li class="text-slate-500">Belum ada maker payment term.</li>
            @endforelse
        </ul>
    </div>
@endsection
