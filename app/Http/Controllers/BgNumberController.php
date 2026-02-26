<?php

namespace App\Http\Controllers;

use App\Models\BgNumber;
use App\Models\Contract;
use Illuminate\Http\Request;

class BgNumberController extends Controller
{
    public function upsert(Request $request, Contract $contract)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) BgNumber::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            $data = [
                'number'     => $item['number'] ?? null,
                'periode'    => $item['periode'] ?? null,
                'start_date' => ($item['start_date'] ?? '') ?: null,
                'end_date'   => ($item['end_date'] ?? '') ?: null,
            ];

            if ($id) {
                BgNumber::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->bgNumbers()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'BG Number berhasil disimpan.');
    }
}
