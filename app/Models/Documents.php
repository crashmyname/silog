<?php

namespace App\Models;

use Bpjs\Framework\Helpers\BaseModel;

class Documents extends BaseModel
{
    protected string $table = 'documents';
    protected string $primaryKey = 'id';
    protected array $fillable = [];
     protected array $casts = [
        'category_id' => 'int',
        'file_size'   => 'int',
        'uploaded_by' => 'int',
    ];
    protected array $hidden = [];
    protected array $appends = ['file_size_human', 'download_url'];
    protected bool $softDelete = true;
    protected string $deletedAtColumn = 'deleted_at';
    protected bool $timestamps = true;

     public function category(): array
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /* =========================================================
       Accessor
       ========================================================= */

    public function getFileSizeHumanAttribute($value): string
    {
        $b = (int) ($this->attributes['file_size'] ?? 0);
        if ($b < 1024)    return $b . ' B';
        if ($b < 1048576) return round($b / 1024, 1) . ' KB';
        return round($b / 1048576, 2) . ' MB';
    }

    public function getDownloadUrlAttribute($value): string
    {
        $path = $this->attributes['file_path'] ?? '';
        if ($path === '') return '';
        return storage_secure($path, 3600);
    }
}
