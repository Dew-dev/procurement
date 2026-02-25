@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Purchase Orders</h1>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('purchase-orders.create') }}" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700">Tambah PO</a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3">PO Number</th>
                    <th class="text-left px-4 py-3">Quotation</th>
                    <th class="text-left px-4 py-3">WIP Status</th>
                    <th class="text-left px-4 py-3">Delivery</th>
                    <th class="text-left px-4 py-3 w-52">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchaseOrders as $purchaseOrder)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ $purchaseOrder->po_number }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->quotation?->quotation_number ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $purchaseOrder->wip_status ?: '-' }}</td>
                        <td class="px-4 py-3">{{ optional($purchaseOrder->exact_delivery_date)->format('Y-m-d') ?: '-' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Detail</a>
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="px-3 py-1.5 rounded-md border border-indigo-300 text-indigo-700 hover:bg-indigo-50">Edit</a>
                                <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-md border border-red-300 text-red-700 hover:bg-red-50" type="submit">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-5 text-slate-500" colspan="5">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $purchaseOrders->links() }}</div>
@endsection
