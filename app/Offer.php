<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
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

    protected $fillable = [
        'name', 'slug', 'uuid', 'tax', 'group_id'
    ];

    public function equipments(){
        return $this->belongsToMany(Equipment::class, 'equipment_offer', 'offer_id', 'equipment_id')->withPivot('quantity', 'price');
    }

    public function offer_groups(){
        return $this->belongsTo(OfferGroup::class, 'group_id', 'id');
    }
}