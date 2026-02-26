<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\SuretyBond;
use Illuminate\Http\Request;

class SuretyBondController extends Controller
{
    public function upsert(Request $request, Contract $contract)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) SuretyBond::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            $data = [
                'number'     => $item['number'] ?? null,
                'periode'    => $item['periode'] ?? null,
                'start_date' => ($item['start_date'] ?? '') ?: null,
                'end_date'   => ($item['end_date'] ?? '') ?: null,
            ];

            if ($id) {
                SuretyBond::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->suretyBonds()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Surety Bond berhasil disimpan.');
    }
}
