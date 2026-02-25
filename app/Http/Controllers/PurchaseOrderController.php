<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    private function fill(array $item): array
    {
        return [
            'po_number'           => $item['po_number'] ?? null,
            'po_date'             => $item['po_date'] ?: null,
            'po_payment_term'     => $item['po_payment_term'] ?? null,
            'wip_status'          => $item['wip_status'] ?? null,
            'exact_delivery_date' => $item['exact_delivery_date'] ?: null,
            'dimension'           => $item['dimension'] ?? null,
            'weight'              => $item['weight'] ?? null,
            'shipping_documents'  => $item['shipping_documents'] ?? null,
            'incoterm'            => $item['incoterm'] ?? null,
        ];
    }

    public function upsert(Request $request, Contract $contract)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) PurchaseOrder::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            $data = $this->fill($item);

            if (empty($data['po_number'])) continue;

            if ($id) {
                PurchaseOrder::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->purchaseOrders()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Purchase Order berhasil disimpan.');
    }
}
