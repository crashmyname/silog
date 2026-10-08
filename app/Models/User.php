<?php

namespace App\Models;
use Bpjs\Framework\Helpers\BaseModel;

class User extends BaseModel {
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    protected bool $timestamps = true;
    protected bool $softDelete = false;
}