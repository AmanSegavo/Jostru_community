<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_sku',
        'quantity_produced',
        'price',
        'source_waste_id',
        'produced_at',
    ];

    protected $casts = [
        'produced_at' => 'datetime',
    ];

    public function sourceWaste()
    {
        return $this->belongsTo(WasteDeposit::class, 'source_waste_id');
    }
}
