<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Model;

class OfferGroup extends Model
{
    use Uuids;
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function offers(){
        return $this->hasMany(Offer::class, 'group_id', 'id');
    }

    public function equipment(){
        return $this->belongsToMany(Equipment::class, 'equipment_offer_groups', 'offer_group_id', 'equipment_id')->withPivot('quantity');
    }
}