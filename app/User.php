<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'middle', 'last', 'company', 'position', 'verified', 'email', 'password', 'tel', 'contact_email', 'phone_number', 'mobile_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function offerGroups()
    {
        return $this->hasMany(OfferGroup::class,'user_id', 'id');
    }
    public function getFullNameAttribute(){
        return $this->middle . ' ' . $this->name . ' ' . $this->last;
    }

    public function contacts(){
        return $this->hasMany(Contact::class, 'user_id', 'id');
    }

    public function roles(){
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }
}
