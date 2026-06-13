<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PosTransactionItem extends Model {
    protected $guarded = [];
    public function transaction() { return $this->belongsTo(PosTransaction::class, 'pos_transaction_id'); }
    public function product() { return $this->belongsTo(PosProduct::class); }
}
