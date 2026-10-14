<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class CustomerCycle extends BaseModel
{
    protected string $table = 'customer_cycles';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','id');
    }
}
