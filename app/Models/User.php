<?php

namespace k1fl1k\joyart\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use k1fl1k\joyart\Enums\Gender;
use k1fl1k\joyart\Enums\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasUlids;

    protected $keyType = 'string'; // ULID - це рядок
    public $incrementing = false; // Забороняємо автоінкремент

    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'birthday',
        'gender',
        'role',
        'avatar',
        'backdrop',
        'description',
        'allow_adult',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
            'gender' => Gender::class,
            'role' => Role::class,
            'allow_adult' => 'boolean',
        ];
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ? asset('storage/avatars/' . $this->avatar) : asset('storage/avatars/default.png');
    }
}
