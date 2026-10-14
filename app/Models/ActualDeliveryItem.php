<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class ActualDeliveryItem extends BaseModel
{
    protected string $table = 'actual_delivery_item';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;

    public function items()
    {
        return $this->belongsTo(Item::class,'item_id','id');
    }

    public function delivery()
    {
        return $this->belongsTo(ActualDelivery::class,'delivery_id','id');
    }
}
