<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\MakerPaymentTerm;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class MakerPaymentTermController extends Controller
{
    private function fill(array $item): array
    {
        return [
            'term_code'      => $item['term_code'] ?? null,
            'percentage'     => $item['percentage'] !== '' ? $item['percentage'] : null,
            'invoice_number' => $item['invoice_number'] ?? null,
            'invoice_date'   => $item['invoice_date'] ?: null,
            'paid_date'      => $item['paid_date'] ?: null,
        ];
    }

    public function upsert(Request $request, Contract $contract, PurchaseOrder $purchaseOrder)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) MakerPaymentTerm::where('id', $id)->where('po_id', $purchaseOrder->id)->delete();
                continue;
            }

            $data = $this->fill($item);

            if ($id) {
                MakerPaymentTerm::where('id', $id)->where('po_id', $purchaseOrder->id)->update($data);
            } else {
                $purchaseOrder->makerPaymentTerms()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Maker payment terms berhasil disimpan.');
    }
}
