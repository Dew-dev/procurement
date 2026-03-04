<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;


    protected $fillable = [
        'contract_id',
        'rfq_id',
        'po_number',
        'po_date',
        'po_payment_term',
        'wip_status',
        'exact_delivery_date',
        'dimension',
        'weight',
        'incoterm',
        'expedite',
    ];

    protected function casts(): array
    {
        return [
            'po_date' => 'date',
            'exact_delivery_date' => 'date',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    public function makerPaymentTerms(): HasMany
    {
        return $this->hasMany(MakerPaymentTerm::class, 'po_id');
    }

    public function shippingDocuments(): HasMany
    {
        return $this->hasMany(ShippingDocument::class);
    }

    public function wipStatuses(): HasMany
    {
        return $this->hasMany(PurchaseOrderWipStatus::class)->orderBy('percentage');
    }

    public function syncWipStatuses(?array $payload): void
    {
        if ($payload === null) {
            return;
        }

        // payload is a list of {percentage, status_date} items
        $seen = [];
        foreach ($payload as $row) {
            $percentage = (int) ($row['percentage'] ?? 0);
            $date       = ($row['status_date'] ?? '') ?: null;
            if ($percentage <= 0) continue;

            if ($date) {
                $this->wipStatuses()->updateOrCreate(
                    ['percentage' => $percentage],
                    ['status_date' => $date]
                );
            }
            $seen[] = $percentage;
        }

        // Remove rows that were deleted
        if (count($seen)) {
            $this->wipStatuses()->whereNotIn('percentage', $seen)->delete();
        } else {
            $this->wipStatuses()->delete();
        }
    }
}
