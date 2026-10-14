<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class ActualDelivery extends BaseModel
{
    protected string $table = 'actual_deliveries';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;

    public function cycle()
    {
        return $this->belongsTo(CustomerCycle::class,'customer_cycle_id','id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class,'vehicle_id','id');
    }

    public function items()
    {
        return $this->hasMany(ActualDelivery::class,'delivery_id','id');
    }
}
