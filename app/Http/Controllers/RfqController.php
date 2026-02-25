<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Rfq;
use Illuminate\Http\Request;

class RfqController extends Controller
{
    public function upsert(Request $request, Contract $contract)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) Rfq::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            $data = [
                'rfq_number' => $item['rfq_number'] ?? null,
                'rfq_date'   => $item['rfq_date'] ?: null,
                'maker'      => $item['maker'] ?? null,
            ];

            if (empty($data['rfq_number'])) continue;

            if ($id) {
                Rfq::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->rfqs()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'RFQ berhasil disimpan.');
    }
}
