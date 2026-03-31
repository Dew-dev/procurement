@extends('layouts.app')

@section('title', 'Contracts')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <h1 class="text-2xl font-semibold">Contracts</h1>
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('contracts.create') }}" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-700">Tambah Contract</a>
        @endif
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('contracts.index') }}" class="mb-4 flex gap-2">
        <input type="hidden" name="sort" value="{{ $sortBy }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
        <input type="text" name="search" value="{{ $search }}"
               placeholder="Cari no. contract atau buyer..."
               class="flex-1 px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400">
        <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-700">Cari</button>
        @if ($search)
            <a href="{{ route('contracts.index', ['sort' => $sortBy, 'direction' => $direction]) }}"
               class="px-4 py-2 text-sm rounded-lg border border-slate-300 hover:bg-slate-50">Reset</a>
        @endif
    </form>

    @php
        $nextDir = fn($col) => ($sortBy === $col && $direction === 'asc') ? 'desc' : 'asc';
        $sortIcon = fn($col) => $sortBy === $col ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
        $rowStart = ($contracts->currentPage() - 1) * $contracts->perPage() + 1;
    @endphp

    <div class="bg-white border border-slate-200 rounded-xl overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-left px-4 py-3 w-10">#</th>
                    <th class="text-left px-4 py-3">
                        <a href="{{ route('contracts.index', ['search' => $search, 'sort' => 'contract_number', 'direction' => $nextDir('contract_number')]) }}"
                           class="hover:text-slate-900">No Contract{!! $sortIcon('contract_number') !!}</a>
                    </th>
                    <th class="text-left px-4 py-3">
                        <a href="{{ route('contracts.index', ['search' => $search, 'sort' => 'buyer_name', 'direction' => $nextDir('buyer_name')]) }}"
                           class="hover:text-slate-900">Buyer{!! $sortIcon('buyer_name') !!}</a>
                    </th>
                    <th class="text-left px-4 py-3">
                        <a href="{{ route('contracts.index', ['search' => $search, 'sort' => 'company_name', 'direction' => $nextDir('company_name')]) }}"
                           class="hover:text-slate-900">Company Name{!! $sortIcon('company_name') !!}</a>
                    </th>
                    <th class="text-left px-4 py-3">
                        <a href="{{ route('contracts.index', ['search' => $search, 'sort' => 'contract_date', 'direction' => $nextDir('contract_date')]) }}"
                           class="hover:text-slate-900">Tanggal Contract{!! $sortIcon('contract_date') !!}</a>
                    </th>
                    <th class="text-left px-4 py-3">
                        <a href="{{ route('contracts.index', ['search' => $search, 'sort' => 'delivery_date', 'direction' => $nextDir('delivery_date')]) }}"
                           class="hover:text-slate-900">Tanggal Delivery{!! $sortIcon('delivery_date') !!}</a>
                    </th>
                    <th class="text-left px-4 py-3">
                        <a href="{{ route('contracts.index', ['search' => $search, 'sort' => 'created_at', 'direction' => $nextDir('created_at')]) }}"
                           class="hover:text-slate-900">Ditambahkan{!! $sortIcon('created_at') !!}</a>
                    </th>
                    <th class="text-left px-4 py-3 w-52">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contracts as $i => $contract)
                    <tr class="border-t border-slate-100 hover:bg-blue-100 transition-colors odd:bg-white even:bg-slate-100">
                        <td class="px-4 py-3 text-slate-400">{{ $rowStart + $i }}</td>
                        <td class="px-4 py-3 font-medium">{{ $contract->contract_number }}</td>
                        <td class="px-4 py-3">{{ $contract->buyer_name ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $contract->company_name ?: '-' }}</td>
                        <td class="px-4 py-3">{{ optional($contract->contract_date)->format('Y-m-d') ?: '-' }}</td>
                        <td class="px-4 py-3">{{ optional($contract->delivery_date)->format('Y-m-d') ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $contract->created_at->format('Y-m-d') }}</td>
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
                    <tr><td class="px-4 py-5 text-slate-500" colspan="8">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $contracts->links() }}</div>
@endsection
