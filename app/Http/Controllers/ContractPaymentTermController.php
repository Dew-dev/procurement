<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractPaymentTerm;
use Illuminate\Http\Request;

class ContractPaymentTermController extends Controller
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

    public function upsert(Request $request, Contract $contract)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) ContractPaymentTerm::where('id', $id)->where('contract_id', $contract->id)->delete();
                continue;
            }

            $data = $this->fill($item);

            if ($id) {
                ContractPaymentTerm::where('id', $id)->where('contract_id', $contract->id)->update($data);
            } else {
                $contract->contractPaymentTerms()->create($data);
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract payment terms berhasil disimpan.');
    }
}
