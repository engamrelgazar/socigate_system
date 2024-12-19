<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'id_card_file',
        'national_id',
        'phone_number',
        'address',
        'profile_picture',
        'hiring_date',
        'date_of_birth',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture ? Storage::url($this->profile_picture) : null;
    }

    public function getIdCardFileUrlAttribute()
    {
        return $this->id_card_file ? Storage::url($this->id_card_file) : null;
    }
}
