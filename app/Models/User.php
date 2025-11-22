<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'identity_number',
        'user_type',
        'email',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'user_type' => 'string',
    ];

    /**
     * Get the identifier for the user based on their type.
     *
     * @return string
     */
    public function getIdentifierAttribute()
    {
        switch ($this->user_type) {
            case 'admin':
            case 'pegawai':
                return $this->username;
            case 'dosen':
                return $this->identity_number ?? $this->nim_nip_nup;
            case 'mahasiswa':
                return $this->identity_number ?? $this->nim_nip_nup;
            default:
                return $this->username;
        }
    }

    /**
     * Get the identifier label based on user type.
     *
     * @return string
     */
    public function getIdentifierLabelAttribute()
    {
        switch ($this->user_type) {
            case 'admin':
            case 'pegawai':
                return 'Username';
            case 'dosen':
                return 'NIP/NUP';
            case 'mahasiswa':
                return 'NIM';
            default:
                return 'Identifier';
        }
    }

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
        ];
    }
}
