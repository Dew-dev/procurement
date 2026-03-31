<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractItem;
use Illuminate\Http\Request;

class ContractItemController extends Controller
{
    public function upsert(Request $request, Contract $contract)
    {
        foreach ($request->input('items', []) as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) ContractItem::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            if (empty($item['item_name'])) continue;

            $data = [
                'item_name'   => $item['item_name'] ?? null,
                'description' => $item['description'] ?? null,
                'qty'         => ($item['qty'] ?? '') !== '' ? $item['qty'] : null,
                'unit'        => $item['unit'] ?? null,
                'unit_price'  => ($item['unit_price'] ?? '') !== '' ? $item['unit_price'] : null,
                'currency'    => $item['currency'] ?? null,
            ];

            if ($id) {
                ContractItem::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->contractItems()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract Items berhasil disimpan.');
    }
}
