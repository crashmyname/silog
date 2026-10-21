<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class Vendor extends BaseModel
{
    protected string $table = 'vendors';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;
}
