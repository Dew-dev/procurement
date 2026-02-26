<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'buyer_name',
        'rfq_from_buyer',
        'quotation_to_buyer',
        'contract_date',
        'delivery_date',
    ];

    protected function casts(): array
    {
        return [
            'rfq_from_buyer' => 'date',
            'quotation_to_buyer' => 'date',
            'contract_date' => 'date',
            'delivery_date' => 'date',
        ];
    }

    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function contractPaymentTerms(): HasMany
    {
        return $this->hasMany(ContractPaymentTerm::class);
    }

    public function bgNumbers(): HasMany
    {
        return $this->hasMany(BgNumber::class);
    }

    public function suretyBonds(): HasMany
    {
        return $this->hasMany(SuretyBond::class);
    }
}
