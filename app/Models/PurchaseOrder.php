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
        'po_number',
        'po_date',
        'po_payment_term',
        'wip_status',
        'exact_delivery_date',
        'dimension',
        'weight',
        'shipping_documents',
        'incoterm',
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

    public function makerPaymentTerms(): HasMany
    {
        return $this->hasMany(MakerPaymentTerm::class, 'po_id');
    }
}
