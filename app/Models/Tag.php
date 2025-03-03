<?php

namespace k1fl1k\joyart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string'; // ULID

    protected $fillable = [
        'id',
        'name',
        'description',
        'aliases',
        'icon',
        'image',
        'image_alt',
        'slug',
        'meta_title',
        'meta_description',
        'parent_id',
    ];

    protected $casts = [
        'aliases' => 'array',
    ];

    /**
     * Отримує батьківський тег
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'parent_id');
    }

    /**
     * Отримує всі дочірні теги (сабтеги)
     */
    public function subtags(): HasMany
    {
        return $this->hasMany(Tag::class, 'parent_id');
    }

    public function artworks()
    {
        return $this->belongsToMany(Artwork::class, 'artwork_tag');
    }
}
