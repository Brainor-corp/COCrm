<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferGroup extends Model
{
    public function offers(){
        return $this->hasMany(Offer::class, 'group_id', 'id');
    }
}
