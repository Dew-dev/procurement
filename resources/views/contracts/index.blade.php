@extends('layouts.app')

@section('title', 'Contracts')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Contracts</h1>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('contracts.create') }}" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700">Tambah Contract</a>
        @endif
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3">No Contract</th>
                    <th class="text-left px-4 py-3">Buyer</th>
                    <th class="text-left px-4 py-3">Tanggal Contract</th>
                    <th class="text-left px-4 py-3 w-52">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contracts as $contract)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ $contract->contract_number }}</td>
                        <td class="px-4 py-3">{{ $contract->buyer_name ?: '-' }}</td>
                        <td class="px-4 py-3">{{ optional($contract->contract_date)->format('Y-m-d') ?: '-' }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('contracts.show', $contract) }}" class="px-3 py-1.5 rounded-md border border-slate-300 hover:bg-slate-50">Detail</a>
                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('contracts.edit', $contract) }}" class="px-3 py-1.5 rounded-md border border-indigo-300 text-indigo-700 hover:bg-indigo-50">Edit</a>
                                <form method="POST" action="{{ route('contracts.destroy', $contract) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-md border border-red-300 text-red-700 hover:bg-red-50" type="submit">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-4 py-5 text-slate-500" colspan="4">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $contracts->links() }}</div>
@endsection
