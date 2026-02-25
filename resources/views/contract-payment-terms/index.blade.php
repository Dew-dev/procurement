@extends('layouts.app')

@section('title', 'Contract Payment Terms')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Contract Payment Terms</h1>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('contract-payment-terms.create') }}" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700">Tambah Term</a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3">Contract</th>
                    <th class="text-left px-4 py-3">Term</th>
                    <th class="text-left px-4 py-3">Percentage</th>
                    <th class="text-left px-4 py-3">Invoice</th>
                    <th class="text-left px-4 py-3 w-52">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contractPaymentTerms as $term)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ $term->contract?->contract_number ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $term->term_code ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $term->percentage ?: 0 }}%</td>
                        <td class="px-4 py-3">{{ $term->invoice_number ?: '-' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('contract-payment-terms.show', $term) }}" class="px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Detail</a>
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('contract-payment-terms.edit', $term) }}" class="px-3 py-1.5 rounded-md border border-indigo-300 text-indigo-700 hover:bg-indigo-50">Edit</a>
                                <form method="POST" action="{{ route('contract-payment-terms.destroy', $term) }}" onsubmit="return confirm('Hapus data ini?')">
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

    <div class="mt-4">{{ $contractPaymentTerms->links() }}</div>
@endsection
