<?php

namespace k1fl1k\joyart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Likes extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id', 'artwork_id', 'user_id', 'state'];
    protected $casts = [
        'state' => 'boolean',
    ];

    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'artwork_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
