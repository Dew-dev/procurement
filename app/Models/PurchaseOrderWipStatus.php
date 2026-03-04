<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderWipStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'percentage',
        'status_date',
    ];

    protected function casts(): array
    {
        return [
            'status_date' => 'date',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
}
