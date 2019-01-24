<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute(){
        return $this->middle . ' ' . $this->name . ' ' . $this->last;
    }

    public function contacts(){
        $this->hasMany(Contact::class, 'user_id', 'id');
    }

    public function roles(){
        $this->belongsToMany(Role::class, 'role_user', 'user_id', 'id');
    }
}
