<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Emadadly\LaravelUuid\Uuids;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use Sluggable;
    use Uuids;

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

    public function equipment(){
        return $this->belongsToMany(Offer::class, 'equipment_offer', 'offer_id', 'equipment_id');
    }

    public function offer_group(){
        return $this->belongsTo(OfferGroup::class, 'group_id', 'id');
    }
}
