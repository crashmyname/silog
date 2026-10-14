<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class Customer extends BaseModel
{
    protected string $table = 'customers';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;

    public function cycles()
    {
        return $this->hasMany(CustomerCycle::class,'customer_id','id');
    }
}
