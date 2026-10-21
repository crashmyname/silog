<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class Route extends BaseModel
{
    protected string $table = 'routes';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;
}
