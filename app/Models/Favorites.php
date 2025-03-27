<?php

namespace k1fl1k\joyart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorites extends Model
{
    /** @use HasFactory<\Database\Factories\FavoritesFactory> */
    use HasFactory;

    protected $table = 'favorites';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'user_id', 'artwork_id'];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'artwork_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
