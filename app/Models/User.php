<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table='user';
    protected $fillable = [
        'google_id',// id đăng nhập của google dành cho người đăng nhập bằng google
        'full_name',
        'email',
        'role',
        'password',
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
public function ptProfile() { return $this->hasOne(PtProfile::class); }
public function memberships() { return $this->hasMany(Membership::class); }
public function bookings() { return $this->hasMany(Booking::class); }
public function orders() { return $this->hasMany(Order::class); }
public function checkIns() { return $this->hasMany(CheckIn::class); }
public function bodyMetrics() { return $this->hasMany(BodyMetric::class); }

public function enrolledClasses()
{
    // Kết nối với GymClass thông qua bảng bookings
    // user_id và gym_id phải khớp với tên cột trong file migration của bạn
    return $this->belongsToMany(GymClass::class, 'bookings', 'user_id', 'gym_id')
                ->withPivot('status', 'booking_date')
                ->withTimestamps();
}
}
