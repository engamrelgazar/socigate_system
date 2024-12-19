<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;
    protected $fillable = [
        'user_name',
        'email',
        'password',
        'last_login',
        'role_id',
    ];

    
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
    
    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',  
    ];
}
