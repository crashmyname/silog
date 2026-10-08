<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class Category extends BaseModel
{
    protected string $table = 'category';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;
     public function documents(): array
    {
        return $this->hasMany(Documents::class, 'category_id', 'id');
    }
}
