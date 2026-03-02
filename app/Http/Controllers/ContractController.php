<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::latest()->paginate(15);

        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('contracts.form', [
            'contract' => new Contract(),
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contract_number'    => ['required', 'string', 'max:100', 'unique:contracts,contract_number'],
            'buyer_name'         => ['nullable', 'string', 'max:150'],
            'rfq_from_buyer'     => ['nullable', 'date'],
            'quotation_to_buyer' => ['nullable', 'date'],
            'contract_date'      => ['nullable', 'date'],
            'delivery_date'      => ['nullable', 'date'],
        ]);

        $contract = Contract::create($data);

        // Contract Payment Terms
        foreach ($request->input('contract_payment_terms', []) as $item) {
            $contract->contractPaymentTerms()->create([
                'term_code'      => $item['term_code'] ?? null,
                'percentage'     => ($item['percentage'] ?? '') !== '' ? $item['percentage'] : null,
                'invoice_number' => $item['invoice_number'] ?? null,
                'invoice_date'   => ($item['invoice_date'] ?? '') ?: null,
                'paid_date'      => ($item['paid_date'] ?? '') ?: null,
            ]);
        }

        // RFQs
        foreach ($request->input('rfqs', []) as $item) {
            if (empty($item['rfq_number'])) continue;
            $contract->rfqs()->create([
                'rfq_number' => $item['rfq_number'],
                'rfq_date'   => ($item['rfq_date'] ?? '') ?: null,
                'maker'      => $item['maker'] ?? null,
            ]);
        }

        // Quotations
        foreach ($request->input('quotations', []) as $item) {
            if (empty($item['quotation_number'])) continue;
            $contract->quotations()->create([
                'quotation_number' => $item['quotation_number'],
                'quotation_date'   => ($item['quotation_date'] ?? '') ?: null,
            ]);
        }

        // Purchase Orders + their Maker Payment Terms
        foreach ($request->input('purchase_orders', []) as $pi => $item) {
            if (empty($item['po_number'])) continue;
            $po = $contract->purchaseOrders()->create([
                'po_number'           => $item['po_number'],
                'po_date'             => ($item['po_date'] ?? '') ?: null,
                'po_payment_term'     => $item['po_payment_term'] ?? null,
                'wip_status'          => $item['wip_status'] ?? null,
                'exact_delivery_date' => ($item['exact_delivery_date'] ?? '') ?: null,
                'dimension'           => $item['dimension'] ?? null,
                'weight'              => $item['weight'] ?? null,
                'incoterm'            => $item['incoterm'] ?? null,
            ]);
            foreach ($item['maker_payment_terms'] ?? [] as $mpt) {
                $po->makerPaymentTerms()->create([
                    'term_code'      => $mpt['term_code'] ?? null,
                    'percentage'     => ($mpt['percentage'] ?? '') !== '' ? $mpt['percentage'] : null,
                    'invoice_number' => $mpt['invoice_number'] ?? null,
                    'invoice_date'   => ($mpt['invoice_date'] ?? '') ?: null,
                    'paid_date'      => ($mpt['paid_date'] ?? '') ?: null,
                ]);
            }
            // Shipping document uploads
            $uploadedDocs = $request->file("purchase_orders.$pi.new_shipping_docs") ?? [];
            foreach ($uploadedDocs as $di => $docFiles) {
                $file    = $docFiles['file'] ?? null;
                $docName = $item['new_shipping_docs'][$di]['name'] ?? ($file?->getClientOriginalName() ?? 'Document');
                if ($file && $file->isValid()) {
                    $path = $file->store('shipping-docs', 'public');
                    $po->shippingDocuments()->create(['name' => $docName, 'file_path' => $path]);
                }
            }
        }

        // BG Numbers
        foreach ($request->input('bg_numbers', []) as $item) {
            $contract->bgNumbers()->create([
                'number'     => $item['number'] ?? null,
                'periode'    => $item['periode'] ?? null,
                'start_date' => ($item['start_date'] ?? '') ?: null,
                'end_date'   => ($item['end_date'] ?? '') ?: null,
            ]);
        }

        // Surety Bonds
        foreach ($request->input('surety_bonds', []) as $item) {
            $contract->suretyBonds()->create([
                'number'     => $item['number'] ?? null,
                'periode'    => $item['periode'] ?? null,
                'start_date' => ($item['start_date'] ?? '') ?: null,
                'end_date'   => ($item['end_date'] ?? '') ?: null,
            ]);
        }

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract berhasil dibuat.');
    }

    public function show(Contract $contract)
    {
        $contract->load([
            'rfqs',
            'quotations',
            'purchaseOrders.makerPaymentTerms',
            'purchaseOrders.shippingDocuments',
            'contractPaymentTerms',
            'bgNumbers',
            'suretyBonds',
        ]);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $contract->load(['rfqs', 'quotations', 'purchaseOrders.makerPaymentTerms', 'purchaseOrders.shippingDocuments', 'contractPaymentTerms', 'bgNumbers', 'suretyBonds']);

        return view('contracts.form', [
            'contract' => $contract,
            'isEdit'   => true,
        ]);
    }

    public function update(Request $request, Contract $contract)
    {
        $data = $request->validate([
            'contract_number'    => ['required', 'string', 'max:100', Rule::unique('contracts', 'contract_number')->ignore($contract->id)],
            'buyer_name'         => ['nullable', 'string', 'max:150'],
            'rfq_from_buyer'     => ['nullable', 'date'],
            'quotation_to_buyer' => ['nullable', 'date'],
            'contract_date'      => ['nullable', 'date'],
            'delivery_date'      => ['nullable', 'date'],
        ]);

        $contract->update($data);

        // ── Contract Payment Terms (sync) ────────────────────────────────
        $submittedCptIds = [];
        foreach ($request->input('contract_payment_terms', []) as $item) {
            $id   = $item['id'] ?? null;
            $cptData = [
                'term_code'      => $item['term_code'] ?? null,
                'percentage'     => ($item['percentage'] ?? '') !== '' ? $item['percentage'] : null,
                'invoice_number' => $item['invoice_number'] ?? null,
                'invoice_date'   => ($item['invoice_date'] ?? '') ?: null,
                'paid_date'      => ($item['paid_date'] ?? '') ?: null,
            ];
            if ($id) {
                $contract->contractPaymentTerms()->where('id', $id)->update($cptData);
                $submittedCptIds[] = (int) $id;
            } else {
                $new = $contract->contractPaymentTerms()->create($cptData);
                $submittedCptIds[] = $new->id;
            }
        }
        $contract->contractPaymentTerms()->whereNotIn('id', $submittedCptIds ?: [0])->delete();

        // ── RFQs (sync) ──────────────────────────────────────────────────
        $submittedRfqIds = [];
        foreach ($request->input('rfqs', []) as $item) {
            $id = $item['id'] ?? null;
            if (!$id && empty($item['rfq_number'])) continue;
            $rfqData = [
                'rfq_number' => $item['rfq_number'],
                'rfq_date'   => ($item['rfq_date'] ?? '') ?: null,
                'maker'      => $item['maker'] ?? null,
            ];
            if ($id) {
                $contract->rfqs()->where('id', $id)->update($rfqData);
                $submittedRfqIds[] = (int) $id;
            } else {
                $new = $contract->rfqs()->create($rfqData);
                $submittedRfqIds[] = $new->id;
            }
        }
        $contract->rfqs()->whereNotIn('id', $submittedRfqIds ?: [0])->delete();

        // ── Quotations (sync) ────────────────────────────────────────────
        $submittedQuoIds = [];
        foreach ($request->input('quotations', []) as $item) {
            $id = $item['id'] ?? null;
            if (!$id && empty($item['quotation_number'])) continue;
            $quoData = [
                'quotation_number' => $item['quotation_number'],
                'quotation_date'   => ($item['quotation_date'] ?? '') ?: null,
            ];
            if ($id) {
                $contract->quotations()->where('id', $id)->update($quoData);
                $submittedQuoIds[] = (int) $id;
            } else {
                $new = $contract->quotations()->create($quoData);
                $submittedQuoIds[] = $new->id;
            }
        }
        $contract->quotations()->whereNotIn('id', $submittedQuoIds ?: [0])->delete();

        // ── Purchase Orders + Maker Payment Terms (sync) ─────────────────
        $submittedPoIds = [];
        foreach ($request->input('purchase_orders', []) as $pi => $item) {
            $id = $item['id'] ?? null;
            if (!$id && empty($item['po_number'])) continue;
            $poData = [
                'po_number'           => $item['po_number'],
                'po_date'             => ($item['po_date'] ?? '') ?: null,
                'po_payment_term'     => $item['po_payment_term'] ?? null,
                'wip_status'          => $item['wip_status'] ?? null,
                'exact_delivery_date' => ($item['exact_delivery_date'] ?? '') ?: null,
                'dimension'           => $item['dimension'] ?? null,
                'weight'              => $item['weight'] ?? null,
                'incoterm'            => $item['incoterm'] ?? null,
            ];
            if ($id) {
                $contract->purchaseOrders()->where('id', $id)->update($poData);
                $po = $contract->purchaseOrders()->where('id', $id)->first();
                $submittedPoIds[] = (int) $id;
            } else {
                $po = $contract->purchaseOrders()->create($poData);
                $submittedPoIds[] = $po->id;
            }
            // Upsert MPTs for this PO
            $submittedMptIds = [];
            foreach ($item['maker_payment_terms'] ?? [] as $mpt) {
                $mptId   = $mpt['id'] ?? null;
                $mptData = [
                    'term_code'      => $mpt['term_code'] ?? null,
                    'percentage'     => ($mpt['percentage'] ?? '') !== '' ? $mpt['percentage'] : null,
                    'invoice_number' => $mpt['invoice_number'] ?? null,
                    'invoice_date'   => ($mpt['invoice_date'] ?? '') ?: null,
                    'paid_date'      => ($mpt['paid_date'] ?? '') ?: null,
                ];
                if ($mptId) {
                    $po->makerPaymentTerms()->where('id', $mptId)->update($mptData);
                    $submittedMptIds[] = (int) $mptId;
                } else {
                    $new = $po->makerPaymentTerms()->create($mptData);
                    $submittedMptIds[] = $new->id;
                }
            }
            $po->makerPaymentTerms()->whereNotIn('id', $submittedMptIds ?: [0])->delete();
            // Delete shipping docs marked for removal
            foreach ($item['delete_shipping_docs'] ?? [] as $docId) {
                $doc = $po->shippingDocuments()->find($docId);
                if ($doc) { Storage::disk('public')->delete($doc->file_path); $doc->delete(); }
            }
            // Upload new shipping docs
            $uploadedDocs = $request->file("purchase_orders.$pi.new_shipping_docs") ?? [];
            foreach ($uploadedDocs as $di => $docFiles) {
                $file    = $docFiles['file'] ?? null;
                $docName = $item['new_shipping_docs'][$di]['name'] ?? ($file?->getClientOriginalName() ?? 'Document');
                if ($file && $file->isValid()) {
                    $path = $file->store('shipping-docs', 'public');
                    $po->shippingDocuments()->create(['name' => $docName, 'file_path' => $path]);
                }
            }
        }
        $contract->purchaseOrders()->whereNotIn('id', $submittedPoIds ?: [0])->delete();

        // ── BG Numbers (sync) ────────────────────────────────────────────
        $submittedBgIds = [];
        foreach ($request->input('bg_numbers', []) as $item) {
            $id = $item['id'] ?? null;
            $bgData = [
                'number'     => $item['number'] ?? null,
                'periode'    => $item['periode'] ?? null,
                'start_date' => ($item['start_date'] ?? '') ?: null,
                'end_date'   => ($item['end_date'] ?? '') ?: null,
            ];
            if ($id) {
                $contract->bgNumbers()->where('id', $id)->update($bgData);
                $submittedBgIds[] = (int) $id;
            } else {
                $new = $contract->bgNumbers()->create($bgData);
                $submittedBgIds[] = $new->id;
            }
        }
        $contract->bgNumbers()->whereNotIn('id', $submittedBgIds ?: [0])->delete();

        // ── Surety Bonds (sync) ──────────────────────────────────────────
        $submittedSbIds = [];
        foreach ($request->input('surety_bonds', []) as $item) {
            $id = $item['id'] ?? null;
            $sbData = [
                'number'     => $item['number'] ?? null,
                'periode'    => $item['periode'] ?? null,
                'start_date' => ($item['start_date'] ?? '') ?: null,
                'end_date'   => ($item['end_date'] ?? '') ?: null,
            ];
            if ($id) {
                $contract->suretyBonds()->where('id', $id)->update($sbData);
                $submittedSbIds[] = (int) $id;
            } else {
                $new = $contract->suretyBonds()->create($sbData);
                $submittedSbIds[] = $new->id;
            }
        }
        $contract->suretyBonds()->whereNotIn('id', $submittedSbIds ?: [0])->delete();

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract berhasil diperbarui.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contract berhasil dihapus.');
    }
}
