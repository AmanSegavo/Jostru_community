<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PosTransaction extends Model {
    protected $guarded = [];
    public function division() { return $this->belongsTo(Division::class); }
    public function items() { return $this->hasMany(PosTransactionItem::class); }
    public function cashier() { return $this->belongsTo(User::class, 'cashier_id'); }
}
