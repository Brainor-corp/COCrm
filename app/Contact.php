<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    public function type(){
        $this->belongsTo(Type::class);
    }

    public function user(){
        $this->belongsTo(User::class, 'id', 'user_id');
    }
}
