<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractPaymentTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
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

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
