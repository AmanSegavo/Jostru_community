<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Division extends Model {
    protected $guarded = [];
    public function users() { return $this->hasMany(User::class); }
    public function assignedUsers() { 
        return $this->belongsToMany(User::class, 'division_user')
                    ->withPivot('jabatan', 'is_admin')
                    ->withTimestamps(); 
    }
    public function finances() { return $this->hasMany(Finance::class); }
    public function budgets() { return $this->hasMany(Budget::class); }
    public function inventories() { return $this->hasMany(Inventory::class); }
    public function livestocks() { return $this->hasMany(Livestock::class); }
    public function productionBatches() { return $this->hasMany(ProductionBatch::class); }
}
