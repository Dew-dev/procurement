<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ShippingDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PurchaseOrderController extends Controller
{
    private function fill(array $item): array
    {
        return [
            'rfq_id'              => ($item['rfq_id'] ?? '') ?: null,
            'po_number'           => $item['po_number'] ?? null,
            'po_date'             => ($item['po_date'] ?? '') ?: null,
            'po_payment_term'     => $item['po_payment_term'] ?? null,
            'wip_status'          => $item['wip_status'] ?? null,
            'exact_delivery_date' => ($item['exact_delivery_date'] ?? '') ?: null,
            'delivered_date'      => ($item['delivered_date'] ?? '') ?: null,
            'dimension'           => $item['dimension'] ?? null,
            'weight'              => $item['weight'] ?? null,
            'incoterm'            => $item['incoterm'] ?? null,
            'expedite'            => $item['expedite'] ?? null,
        ];
    }

    public function upsert(Request $request, Contract $contract)
    {
        $items       = $request->input('items', []);
        $uploadedFiles = $request->file('items', []);

        foreach ($items as $idx => $item) {
            $id = $item['id'] ?? null;

            if (!empty($item['_delete'])) {
                if ($id) {
                    $po = PurchaseOrder::where('id', $id)->where('contract_id', $contract->id)->first();
                    if ($po) {
                        foreach ($po->shippingDocuments as $doc) {
                            Storage::disk('public')->delete($doc->file_path);
                        }
                        $po->delete();
                    }
                }
                continue;
            }

            $data = $this->fill($item);
            if (empty($data['po_number'])) continue;

            if ($id) {
                PurchaseOrder::where('id', $id)->where('contract_id', $contract->id)->update($data);
                $po = PurchaseOrder::find($id);
            } else {
                $po = $contract->purchaseOrders()->create($data);
            }

            $po?->syncWipStatuses($item['wip_statuses'] ?? null);

            // Delete shipping docs marked for removal
            foreach ($item['delete_shipping_docs'] ?? [] as $docId) {
                $doc = ShippingDocument::where('id', $docId)->where('purchase_order_id', $po->id)->first();
                if ($doc) {
                    Storage::disk('public')->delete($doc->file_path);
                    $doc->delete();
                }
            }

            // Upload new shipping docs
            $newDocs = $uploadedFiles[$idx]['new_shipping_docs'] ?? [];
            foreach ($newDocs as $di => $docFiles) {
                $file     = $docFiles['file'] ?? null;
                $docName  = $item['new_shipping_docs'][$di]['name'] ?? ($file?->getClientOriginalName() ?? 'Document');
                if ($file && $file->isValid()) {
                    $path = $file->store('shipping-docs', 'public');
                    $po->shippingDocuments()->create(['name' => $docName, 'file_path' => $path]);
                }
            }

            // Sync PO items
            if (isset($item['po_items']) && $po) {
                foreach ($item['po_items'] as $poItem) {
                    $poItemId = $poItem['id'] ?? null;
                    if (!empty($poItem['_delete'])) {
                        if ($poItemId) PurchaseOrderItem::where('id', $poItemId)->where('purchase_order_id', $po->id)->delete();
                        continue;
                    }
                    if (empty($poItem['contract_item_id'])) continue;
                    $poItemData = [
                        'contract_item_id' => $poItem['contract_item_id'],
                        'qty'   => ($poItem['qty'] ?? '') !== '' ? $poItem['qty'] : null,
                        'notes' => $poItem['notes'] ?? null,
                    ];
                    if ($poItemId) {
                        PurchaseOrderItem::where('id', $poItemId)->where('purchase_order_id', $po->id)->update($poItemData);
                    } else {
                        $po->purchaseOrderItems()->create($poItemData);
                    }
                }
            }
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Purchase Order berhasil disimpan.');
    }
}
