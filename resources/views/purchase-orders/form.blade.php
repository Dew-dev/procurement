@extends('layouts.app')

@section('title', $isEdit ? 'Edit Purchase Order' : 'Tambah Purchase Order')

@section('content')
    <h1 class="text-2xl font-semibold mb-5">{{ $isEdit ? 'Edit Purchase Order' : 'Tambah Purchase Order' }}</h1>

    <form method="POST" action="{{ $isEdit ? route('purchase-orders.update', $purchaseOrder) : route('purchase-orders.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Quotation</label>
            <select name="quotation_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">- Pilih Quotation -</option>
                @foreach ($quotations as $quotation)
                    <option value="{{ $quotation->id }}" @selected(old('quotation_id', $purchaseOrder->quotation_id) == $quotation->id)>{{ $quotation->quotation_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">PO Number</label><input name="po_number" value="{{ old('po_number', $purchaseOrder->po_number) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">PO Date</label><input type="date" name="po_date" value="{{ old('po_date', optional($purchaseOrder->po_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Payment Term</label><input name="po_payment_term" value="{{ old('po_payment_term', $purchaseOrder->po_payment_term) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-sm font-medium mb-1">WIP Status</label><input name="wip_status" value="{{ old('wip_status', $purchaseOrder->wip_status) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Exact Delivery Date</label><input type="date" name="exact_delivery_date" value="{{ old('exact_delivery_date', optional($purchaseOrder->exact_delivery_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Incoterm</label><input name="incoterm" value="{{ old('incoterm', $purchaseOrder->incoterm) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Dimension</label><input name="dimension" value="{{ old('dimension', $purchaseOrder->dimension) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
            <div><label class="block text-sm font-medium mb-1">Weight</label><input name="weight" value="{{ old('weight', $purchaseOrder->weight) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2"></div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Shipping Documents</label>
            <textarea name="shipping_documents" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2">{{ old('shipping_documents', $purchaseOrder->shipping_documents) }}</textarea>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700" type="submit">Simpan</button>
            <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection
