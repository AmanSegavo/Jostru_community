<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'google_id', 'role', 'member_id', 'status', 'jabatan', 'tanggal_lahir', 'alamat', 'latitude', 'longitude', 
        'can_chat', 'can_post', 'can_comment', 'onesignal_player_id', 'can_input_waste', 'points',
        'can_view_finances', 'finance_view_scope', 'can_manage_division',
        'can_manage_members', 'can_manage_finances', 'can_manage_waste', 'can_manage_posts'
    ];
    protected $hidden = ['password', 'remember_token'];
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function membershipCard()
    {
        return $this->hasOne(MembershipCard::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function assignedDivisions()
    {
        return $this->belongsToMany(Division::class, 'division_user')
                    ->withPivot('jabatan', 'is_admin')
                    ->withTimestamps();
    }

    public function debts()
    {
        return $this->hasMany(Debt::class, 'member_id');
    }

    public function delegatedPermissions()
    {
        return $this->hasMany(PermissionDelegation::class, 'delegator_id');
    }

    public function receivedPermissions()
    {
        return $this->hasMany(PermissionDelegation::class, 'delegatee_id');
    }
}
