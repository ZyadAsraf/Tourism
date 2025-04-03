<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
class Attraction_Staff extends Model
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
        'Email',
        'Password',
        'BirthDate',
        'PhoneNumber',
        'attraction_id',
     ];
     public function attraction(){
      return $this->belongsTo(Attraction::class);
     }
     public function setPasswordAttribute($value)
     {
         $this->attributes['password'] = Hash::make($value);
     }
}
