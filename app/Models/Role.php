<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 
        'role_desc', 
        'permissions'
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
