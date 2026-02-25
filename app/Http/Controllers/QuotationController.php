<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Quotation;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function upsert(Request $request, Contract $contract)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) Quotation::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            $data = [
                'quotation_number' => $item['quotation_number'] ?? null,
                'quotation_date'   => $item['quotation_date'] ?: null,
            ];

            if (empty($data['quotation_number'])) continue;

            if ($id) {
                Quotation::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->quotations()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Quotation berhasil disimpan.');
    }
}
