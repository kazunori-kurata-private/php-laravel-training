<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'introduction',
        'role_id',
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
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    // 権限判定メソッド
    public function isStaff(): bool
    {
        return $this->role_id === RoleType::STAFF;
    }

    public function isManager(): bool
    {
        return $this->role_id === RoleType::MANAGER;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === RoleType::ADMIN;
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canManageCats(): bool
    {
        return $this->isManager() || $this->isAdmin();
    }

    // public function canManageAllBlogs(): bool
    // {
    //     return $this->isAdmin();
    // }

    public function canCreateBlogs(): bool
    {
        return $this->isStaff() || $this->isManager(); // adminは除外
    }

    public function canEditBlogs(): bool
    {
        return $this->isStaff() || $this->isManager(); // adminは除外
    }

    public function canManageBlogs(): bool
    {
        return $this->canCreateBlogs() || $this->canEditBlogs();
    }
}
