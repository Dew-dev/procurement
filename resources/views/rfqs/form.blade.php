@extends('layouts.app')

@section('title', $isEdit ? 'Edit RFQ' : 'Tambah RFQ')

@section('content')
    <h1 class="text-2xl font-semibold mb-5">{{ $isEdit ? 'Edit RFQ' : 'Tambah RFQ' }}</h1>

    <form method="POST" action="{{ $isEdit ? route('rfqs.update', $rfq) : route('rfqs.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4 max-w-3xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">Contract</label>
            <select name="contract_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">- Pilih Contract -</option>
                @foreach ($contracts as $contract)
                    <option value="{{ $contract->id }}" @selected(old('contract_id', $rfq->contract_id) == $contract->id)>{{ $contract->contract_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">RFQ Number</label>
                <input name="rfq_number" value="{{ old('rfq_number', $rfq->rfq_number) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">RFQ Date</label>
                <input type="date" name="rfq_date" value="{{ old('rfq_date', optional($rfq->rfq_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Maker</label>
                <input name="maker" value="{{ old('maker', $rfq->maker) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700" type="submit">Simpan</button>
            <a href="{{ route('rfqs.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection
