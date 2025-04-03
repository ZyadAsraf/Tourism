<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Admin extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'FirstName',
        'LastName',
        'PhoneNumber',
        'BirthDate',
        'Email',
        'Password',

    ];
    public function setPasswordAttribute($value)
    {
        $this->attributes['Password'] = Hash::make($value);
    }
    
}
