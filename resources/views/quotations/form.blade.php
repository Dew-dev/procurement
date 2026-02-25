@extends('layouts.app')

@section('title', $isEdit ? 'Edit Quotation' : 'Tambah Quotation')

@section('content')
    <h1 class="text-2xl font-semibold mb-5">{{ $isEdit ? 'Edit Quotation' : 'Tambah Quotation' }}</h1>

    <form method="POST" action="{{ $isEdit ? route('quotations.update', $quotation) : route('quotations.store') }}" class="bg-white border border-slate-200 rounded-xl p-6 space-y-4 max-w-3xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium mb-1">RFQ</label>
            <select name="rfq_id" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                <option value="">- Pilih RFQ -</option>
                @foreach ($rfqs as $rfq)
                    <option value="{{ $rfq->id }}" @selected(old('rfq_id', $quotation->rfq_id) == $rfq->id)>{{ $rfq->rfq_number }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Quotation Number</label>
                <input name="quotation_number" value="{{ old('quotation_number', $quotation->quotation_number) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Quotation Date</label>
                <input type="date" name="quotation_date" value="{{ old('quotation_date', optional($quotation->quotation_date)->format('Y-m-d')) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
            </div>
        </div>

        <div class="flex gap-2">
            <button class="px-4 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-700" type="submit">Simpan</button>
            <a href="{{ route('quotations.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-50">Batal</a>
        </div>
    </form>
@endsection
