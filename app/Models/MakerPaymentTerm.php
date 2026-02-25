<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MakerPaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'term_code',
        'percentage',
        'invoice_number',
        'invoice_date',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:2',
            'invoice_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }
}
