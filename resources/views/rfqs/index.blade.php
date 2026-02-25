@extends('layouts.app')

@section('title', 'RFQs')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">RFQs</h1>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('rfqs.create') }}" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700">Tambah RFQ</a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3">RFQ Number</th>
                    <th class="text-left px-4 py-3">Contract</th>
                    <th class="text-left px-4 py-3">Maker</th>
                    <th class="text-left px-4 py-3">Tanggal</th>
                    <th class="text-left px-4 py-3 w-52">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rfqs as $rfq)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ $rfq->rfq_number }}</td>
                        <td class="px-4 py-3">{{ $rfq->contract?->contract_number ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $rfq->maker ?: '-' }}</td>
                        <td class="px-4 py-3">{{ optional($rfq->rfq_date)->format('Y-m-d') ?: '-' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('rfqs.show', $rfq) }}" class="px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Detail</a>
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('rfqs.edit', $rfq) }}" class="px-3 py-1.5 rounded-md border border-indigo-300 text-indigo-700 hover:bg-indigo-50">Edit</a>
                                <form method="POST" action="{{ route('rfqs.destroy', $rfq) }}" onsubmit="return confirm('Hapus data ini?')">
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

    <div class="mt-4">{{ $rfqs->links() }}</div>
@endsection
