<?php

namespace k1fl1k\joyart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'artwork_id', 'user_id', 'reason', 'status', 'description'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_DISMISSED = 'dismissed';

    // Reason constants
    const REASON_INAPPROPRIATE = 'inappropriate_content';
    const REASON_COPYRIGHT = 'copyright_violation';
    const REASON_OFFENSIVE = 'offensive';
    const REASON_SPAM = 'spam';
    const REASON_OTHER = 'other';

    /**
     * Get the artwork that was reported
     */
    public function artwork()
    {
        return $this->belongsTo(Artwork::class, 'artwork_id');
    }

    /**
     * Get the user who reported the artwork
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
