@extends('layouts.app')

@section('title', 'Detail RFQ')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Detail RFQ</h1>
        <a href="{{ route('rfqs.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Kembali</a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-6 grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div><span class="text-slate-500 text-sm">RFQ Number</span><p class="font-medium">{{ $rfq->rfq_number }}</p></div>
        <div><span class="text-slate-500 text-sm">Contract</span><p class="font-medium">{{ $rfq->contract?->contract_number ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">Maker</span><p class="font-medium">{{ $rfq->maker ?: '-' }}</p></div>
        <div><span class="text-slate-500 text-sm">RFQ Date</span><p class="font-medium">{{ optional($rfq->rfq_date)->format('Y-m-d') ?: '-' }}</p></div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h2 class="font-semibold mb-3">Quotation Terkait</h2>
        <ul class="text-sm space-y-2">
            @forelse ($rfq->quotations as $quotation)
                <li>{{ $quotation->quotation_number }}</li>
            @empty
                <li class="text-slate-500">Belum ada quotation.</li>
            @endforelse
        </ul>
    </div>
@endsection
