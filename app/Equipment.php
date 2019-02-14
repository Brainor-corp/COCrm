<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function type(){
        return $this->hasOne(Type::class, 'id', 'type_id');
    }

    public function sets(){
        return $this->belongsToMany(Set::class, 'equipment_set', 'equipment_id', 'set_id');
    }

    public function offers(){
        return $this->belongsToMany(Offer::class, 'equipment_offer', 'equipment_id', 'offer_id')->withPivot('quantity');
    }

    public function offer_group(){
        return $this->belongsToMany(Equipment::class, 'equipment_offer_groups', 'equipment_id', 'offer_group_id')->withPivot('quantity');
    }

    public function types(){
        return $this->belongsToMany(Type::class, 'equipment_type', 'equipment_id', 'type_id')->withPivot('quantity');
    }
}
