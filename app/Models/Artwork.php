<?php

namespace k1fl1k\joyart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    /** @use HasFactory<\Database\Factories\ArtworkFactory> */
    use HasFactory;
    protected $connection = 'pgsql';
    protected $table = 'artworks'; // Вказуємо явно таблицю
    protected $primaryKey = 'id';  // Вказуємо первинний ключ
    public $incrementing = false;  // Вимикаємо автоінкремент
    protected $keyType = 'string'; // `ULID` - це рядок

    protected $fillable = [
        'id', 'md5', 'rating', 'width', 'height', 'file_ext',
        'file_size', 'thumbnail', 'original', 'is_vip', 'colors',
        'source', 'is_published', 'slug', 'meta_title',
        'meta_description', 'image', 'image_alt', 'user_id', 'type',
        'tag_id'
    ];


    protected $casts = [
        'colors' => 'array',
        'is_published' => 'boolean',
        'is_vip' => 'boolean',
    ];

    protected $guarded = [];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'artwork_tag', 'artwork_id', 'tag_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorites::class, 'artwork_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
